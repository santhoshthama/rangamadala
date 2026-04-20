# Drama Module Overview

This document captures the current state of the drama-related MVC stack within the Rangamadala codebase. It is meant to help contributors understand how the existing flows operate, what data each layer expects, and which improvements remain outstanding.

---

## 🧭 High-level flow

1. **Artist creates a drama** via `CreateDrama::index()`.
   - GET `/createDrama` renders the form (`create_drama.view.php`).
   - POST `/createDrama` validates certificate data and persists a row through `M_drama::createDrama()`.
   - On success the artist is redirected to `artistdashboard`; failures re-render the form with the captured input.
2. **Director experience** is currently tied to the same artist account (the creator becomes the director).
   - Dashboard-style screens live in `director/*.view.php` but rely on `drama_id` supplied in the query string.
   - The controller (`director.php`) guards access with `authorizeDrama()` before rendering the requested view.

---

## 🏗️ MVC responsibilities

| Layer | Files | Responsibilities |
| --- | --- | --- |
| **Model** | `app/models/M_drama.php` | Wraps CRUD around the `dramas` table, exposes list/search helpers, and returns the new record ID after insert. Expects schema created via `database_setup.sql` (requires `drama_name`, `certificate_number`, `owner_name`, `certificate_image`, `created_by`, `creator_artist_id`). |
| **Controller (creation)** | `app/controllers/CreateDrama.php` | Enforces `artist` role, orchestrates form display vs. submission, validates certificate payload, and uploads certificate images into `public/uploads/certificates/`. |
| **Controller (director)** | `app/controllers/director.php` | Lazily loads `M_drama`, authorizes access to a specific drama (`drama_id` query parameter must resolve to a drama owned by the logged-in user), then renders the relevant director view. |
| **Views** | `app/views/create_drama.view.php`, `app/views/director/*.php` | Render HTML/CSS for the create form and director dashboard/auxiliary pages. Current director views are mostly static placeholders with sample data. |

---

## 🔒 Authentication & authorization

- All drama creation endpoints require an authenticated session where `$_SESSION['user_role'] === 'artist'`.
- Director endpoints fail closed: missing session, missing `drama_id`, or unauthorized ownership all redirect back to `/artistdashboard`.
- Because the same account acts as both creator and director, `M_drama::createDrama()` sets both `created_by` and `creator_artist_id` to the current artist's user ID.

---

## ✅ Validation & storage

- **Form validation**: `CreateDrama::createDrama()` ensures `drama_name`, `certificate_number`, `owner_name`, and a file upload are present before attempting persistence. Errors are concatenated and surfaced via `$_SESSION['message']`.
- **Uploads**: `handleImageUpload()` accepts JPG/PNG/GIF/WebP/PDF up to 5 MB, generates a unique filename, and stores the asset under `public/uploads/certificates/` (created on demand).
- **Database**: Inserts rely on the `dramas` table. The schema must include `creator_artist_id`; foreign keys in `database_setup.sql` expect `users.id` to exist for both `created_by` and `creator_artist_id`.

---

## 🎭 Director UX expectations

- Every director route (`/director/dashboard`, `/director/manage_roles`, etc.) must be called with `?drama_id={id}`; the view fallbacks (`$_GET['drama_id'] ?? 1`) are only placeholders and should not be relied upon.
- The dashboard (`app/views/director/dashboard.php`) currently displays hard-coded statistics, schedules, and role assignments. No live aggregation occurs yet.
- Additional director views (details, manage roles, assign managers, schedule, services, search) follow the same pattern—rendering static scaffolding while awaiting future data binding.

---

## 🧪 Manual testing checklist

1. Log in as an `artist` user.
2. Navigate to `/createDrama` and submit:
   - Valid certificate payload → Expect success banner and redirect back to `/artistdashboard`.
   - Missing certificate image or fields → Expect error banner, populated fields preserved.
   - Duplicate certificate number → Database unique constraint triggers failure; controller relays generic error message.
3. Copy the `id` of the newly inserted drama (from DB) and browse to `/director/dashboard?drama_id={id}` while still logged in → view should render (with static data) instead of redirecting.
4. Alter `drama_id` to a drama owned by another user → you should be redirected to `/artistdashboard`.

---

## ⚠️ Known limitations & recommendations

- Director pages do not yet consume live data; all statistics, schedules, and role assignments are mock content.
- The director navigation auto-fallback (`?? 1`) risks leaking incorrect IDs—replace with `$drama->id` once dynamic data arrives.
- No edit/update/delete path exists for dramas; only creation is handled.
- Server-side validation does not currently inspect MIME types via `finfo` or tighten file extensions—consider enhancing to mitigate spoofed uploads.
- Error feedback combines all validation errors into a single comma-separated string; consider switching to structured display.
- There is no automated test coverage for these flows; regression testing is fully manual.

---

## 📌 Next steps (suggested)

- Bind real metrics into the director dashboard (roles, schedules, services) once the relevant tables/controllers are ready.
- Extract shared layout elements in the director views to avoid repetition and unlock reusable components.
- Add audit logging or notifications after a drama is created to inform admins or collaborators.
- Introduce update workflows so directors can revise drama details without manual database edits.
