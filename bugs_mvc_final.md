# MVC Audit Report (Artist, Director, Production Manager)

**Date:** 2026-04-16  
**Project:** Rangamadala  
**Scope:** `app/views/artist*`, `app/views/director/*`, `app/views/production_manager/*` and related controllers

---

## 1) Executive Summary

This audit identified multiple **MVC boundary violations** in Artist, Director, and Production Manager modules.  
Main issues are:

- **Model usage inside views**
- **Business/data transformation logic inside views**
- **Direct use of superglobals (`$_GET`, `$_SESSION`) in views**
- **Large inline CSS/JS in views**
- **Mixed/duplicate view naming (`.php` and `.view.php`) causing maintenance risk**

These issues reduce code clarity, increase bugs during changes, and make future code reviews difficult.

---

## 2) Confirmed MVC Violations

## 2.1 Model calls inside View files (High Severity)

### Observed in:
- `app/views/production_manager/dashboard.php`
  - `new M_universal_profile()` and `getUserById(...)`
- `app/views/artist_drama_view.view.php`
  - `new M_universal_profile()` and `getUserById(...)`
- `app/views/artist_event_detail.view.php`
  - `new M_universal_profile()` and `getUserById(...)`
- `app/views/director/_profile_image_helper.php`
  - helper itself creates model and fetches user

### Why this is a bug (architecture bug):
Views should render data only. DB/model access belongs to model/service/controller layers.

### Recommended fix:
- Fetch profile image source in controllers (or service class), pass as `$data['profileImageSrc']`.
- Keep view files free of `new M_*` and any model calls.

---

## 2.2 Business logic in Views (High Severity)

### Major example:
- `app/views/production_manager/manage_services.php`
  - Reads request flags from `$_GET`
  - Groups service data by type
  - `json_decode(...)` on payload fields
  - Date calculations with `DateTime` for overdue logic
  - Dynamic status derivation in template

### Additional examples:
- `app/views/artist_event_detail.view.php`
  - event type/status maps
  - countdown and duration calculations
- `app/views/artist_drama_view.view.php`
  - repeated formatting and transformation logic

### Why this is a bug:
Business rules in views make templates hard to test and easy to break.

### Recommended fix:
- Controller/service should prepare view-ready structures (view models).
- Views should only loop and display precomputed values.

---

## 2.3 Superglobals in Views (Medium-High Severity)

### Common patterns found:
- `$_GET['drama_id']` fallbacks in multiple view files
- Flash state read/unset directly in views (`$_SESSION['message']`, `unset(...)`)
- Active tab logic tied to `$_GET` and URL in templates

### Why this is a bug:
View behavior becomes request-dependent and harder to reason about.

### Recommended fix:
- Resolve request/session state in controller.
- Pass clean values to views:
  - `$dramaId`, `$activeTab`, `$flash` (message/type)

---

## 2.4 Inline CSS in Views (Medium Severity)

### Significant inline style usage found in:
- `app/views/artistdashboard.view.php`
- `app/views/artistprofile.view.php`
- many files in `app/views/director/*` (e.g., `drama_details.php`, `search_artists.view.php`, `assign_managers.view.php`, `schedule_management.view.php`, etc.)
- `app/views/production_manager/browse_providers.php`
- `app/views/production_manager/manage_schedule.php`

### Why this is a bug:
Large embedded styling makes views hard to read, reuse, and maintain.

### Recommended fix:
- Move styles into external CSS by module:
  - `public/assets/CSS/artist/...`
  - `public/assets/CSS/director/...`
  - `public/assets/CSS/production_manager/...`

---

## 2.5 Duplicate view naming and legacy files (Medium Severity)

Core loader behavior (`app/core/Controller.php`):
- tries `*.view.php` first
- falls back to `*.php`

### Risk:
If both exist for same logical page, reviewers and developers may edit wrong file.

### Example area:
- Director module has both legacy/static-like and active MVC views (`.php` + `.view.php`).

### Recommended fix:
- Standardize on one convention (prefer `*.view.php`).
- Archive/remove legacy static/demo pages not used by active routes.

---

## 3) Module-wise Refactor Targets

## 3.1 Artist module

### Priority files:
1. `app/views/artistdashboard.view.php`
2. `app/views/artist_drama_view.view.php`
3. `app/views/artist_event_detail.view.php`

### Controller updates (`app/controllers/Artistdashboard.php`):
- Build and pass:
  - `profileImageSrc`
  - `activeTab`
  - formatted display dates
  - transformed event/showing request objects
- Remove need for model access and superglobal parsing in views.

---

## 3.2 Director module

### Priority files:
1. `app/views/director/_profile_image_helper.php` (remove model call)
2. `app/views/director/manage_roles_overview.view.php`
3. `app/views/director/role_details.view.php`
4. `app/views/director/search_artists.view.php`

### Controller updates (`app/controllers/director.php`):
- Extend shared payload in `renderDramaView(...)`:
  - `profileImageSrc`, `dramaId`, `flash`
- Keep request/state resolution and transformations in controller/service.

---

## 3.3 Production Manager module

### Priority files:
1. `app/views/production_manager/manage_services.php` (highest logic density)
2. `app/views/production_manager/dashboard.php`
3. `app/views/production_manager/manage_schedule.php`

### Controller updates (`app/controllers/Production_manager.php`):
- Build service request cards/status in controller.
- Decode JSON and compute payment/overdue state before rendering.
- Pass render-ready arrays to views.

---

## 4) Suggested Clear MVC Structure

- **Model**: DB operations + domain data retrieval only
- **Controller**: orchestration, request/session handling, view payload preparation
- **Service/Presenter (recommended)**: format/derive UI-friendly data
- **View**: render-only (no model instantiation, no request parsing, no heavy data transforms)

Recommended helper/services:
- `ProfileImageResolver`
- `ServiceRequestPresenter`
- `DramaEventPresenter`

---

## 5) Priority Roadmap (for clean code check readiness)

### Phase 1 (Critical)
1. Remove all `new M_*` from views
2. Move all profile image resolution to controller/service

### Phase 2 (Critical)
3. Refactor `production_manager/manage_services.php` logic to controller/service
4. Refactor artist event/countdown/status maps to controller/service

### Phase 3 (Important)
5. Move all `$_GET`/`$_SESSION` handling to controllers
6. Standardize flash message handling via controller payload

### Phase 4 (Maintainability)
7. Extract inline CSS to external role-based stylesheets
8. Standardize to `.view.php`, remove/retire legacy duplicates

---

## 6) Expected Outcome After Refactor

- Cleaner MVC separation
- Easier code review and future maintenance
- Lower risk of regressions when changing UI
- Better testability and readability
- Faster onboarding for new contributors

---

## 7) Notes

- Current system is functional, but architectural debt is concentrated in view templates.
- Refactoring can be done incrementally without breaking routes.
- Start with highest-risk files listed above for immediate quality improvement.
