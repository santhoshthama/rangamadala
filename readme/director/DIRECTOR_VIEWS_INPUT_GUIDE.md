# Director Views Input Guide (View-First)

Use this guide when your task is to add/edit input fields in Director pages.

---

## 1) Director request flow in this project

Director routing is split into feature controllers:

- Entry dispatcher: `app/controllers/director.php`
- Role feature: `app/controllers/director/DirectorRoleController.php`
- Schedule feature: `app/controllers/director/DirectorScheduleController.php`
- PM feature: `app/controllers/director/DirectorManagerController.php`
- Drama feature: `app/controllers/director/DirectorDramaController.php`

Shared helpers (authorization, flash, payload merge):

- `app/controllers/director/DirectorFeatureControllerTrait.php`

Standard flow:

1. View form submits to `ROOT/director/...` with `drama_id`.
2. Controller validates + normalizes input.
3. Controller calls model method.
4. Controller sets flash (`$_SESSION['message']`, `$_SESSION['message_type']`) and redirects.
5. View shows flash via `app/views/_partials/flash.php`.

---

## 2) View files you will most likely edit

### A) Create role form

**File**: `app/views/director/create_role.view.php`

**Form action**:

- `POST <?= ROOT ?>/director/create_role?drama_id=<?= esc($dramaId) ?>`

**Inputs**:

- `role_name` (text, required)
- `role_type` (select, required)
- `role_description` (textarea, required)
- `salary` (number, optional)
- `positions_available` (number, required)
- `requirements` (textarea, optional)

**Error rendering**:

- `$formErrors['field_name']` shown below each input.

---

### B) Edit role + vacancy actions + assignment removal

**File**: `app/views/director/role_details.view.php`

#### Edit role form

**Form action**:

- `POST <?= ROOT ?>/director/update_role?drama_id=...&role_id=...`

**Inputs**:

- `role_name` (required)
- `role_type` (required)
- `role_description` (required)
- `salary` (optional, locked when role is full)
- `positions_available` (required)
- `requirements` (optional)
- `status` (required, locked when role is full)

#### Publish / unpublish vacancy actions

- Publish form:
  - `POST /director/publish_vacancy?drama_id=...`
  - hidden: `role_id`, `message`
- Unpublish form:
  - `POST /director/unpublish_vacancy?drama_id=...`
  - hidden: `role_id`

#### Remove assignment form

- `POST /director/remove_assignment?drama_id=...`
- Hidden fields: `assignment_id`, `role_id`, `return_to`
- Required textarea: `remove_reason`

---

### C) Role overview quick actions

**File**: `app/views/director/manage_roles_overview.view.php`

Important form/button actions:

- Create role: link to `/director/create_role?drama_id=...`
- View role: link to `/director/view_role?drama_id=...&role_id=...`
- Assign artist: link to `/director/search_artists?drama_id=...&role_id=...`
- Delete role form:
  - `POST /director/delete_role?drama_id=...&role_id=...`

---

### D) Search artists + send role request

**File**: `app/views/director/search_artists.view.php`

#### Search form (GET)

- Action: `GET /director/search_artists`
- Inputs:
  - hidden `drama_id`
  - hidden `role_id`
  - `search` text

#### Request artist form (POST)

- Action: `POST /director/send_role_request?drama_id=...`
- Hidden inputs:
  - `role_id`
  - `artist_id`

---

### E) Schedule management (event CRUD)

**File**: `app/views/director/schedule_management.view.php`

Key actions rendered inside page/partials:

- Create event: toolbar/modal submits to
  - `POST /director/create_schedule?drama_id=...`
- Update event:
  - `POST /director/update_schedule?drama_id=...`
- Update status (confirm/cancel/complete):
  - `POST /director/update_schedule_status?drama_id=...`
- Delete event:
  - `POST /director/delete_schedule?drama_id=...`

Event card partial used by this page:

- `app/views/_partials/_schedule_event_card.php`

Common event fields:

- `event_type`
- `event_title`
- `event_description`
- `scheduled_date`
- `start_time`
- `end_time`
- `venue`
- `role_id` (optional)
- `notes`
- `event_id` (hidden for update/status/delete)

---

### F) Production Manager assignment views

**File**: `app/views/director/assign_managers.view.php`

- Remove PM form:
  - `POST /director/remove_manager?drama_id=...`

**File**: `app/views/director/search_managers.view.php` (if used in your flow)

- Send PM request form:
  - `POST /director/send_manager_request?drama_id=...`
  - Inputs usually include: `artist_id`, optional `message`

---

### G) Drama details & publish block

**File**: `app/views/director/drama_details.php`

This page has a major publish form:

- `POST /director/publish_drama?drama_id=...` with `enctype=multipart/form-data`

Common publish inputs:

- `category_id` (required)
- `public_description` (required)
- `language` (required)
- `duration_minutes` (required)
- `showing_prices` (required)
- `venue` (required)
- `event_date` (required)
- `ticket_price` (required)
- poster/image inputs (if present in this form section)

Note: this page also renders readonly drama info fields for viewing context.

---

## 3) Input Field Addition Checklist (View-only task)

When adding a new input field in Director views, follow this exact order:

1. Add UI field to the correct form in the target view.
2. Use a stable `name` attribute (snake_case preferred).
3. Preserve context parameters:
   - keep `drama_id` in query/form routing
   - keep hidden IDs like `role_id`, `event_id`, `assignment_id` where needed
4. Add required/constraints in HTML if needed:
   - `required`, `min`, `max`, `step`, etc.
5. Add/extend error display block near field:
   - e.g., `$formErrors['your_field']`
6. Keep `esc(...)` for all output values.
7. Keep flash rendering in page:
   - `include APPROOT . '/views/_partials/flash.php'`
8. Verify form action endpoint exists in `director.php` route dispatcher.
9. Then update controller/model (see MVC mapping doc).

---

## 4) Do / Don’t (important for review)

### Do

- Keep business validation in controller/model, not in view.
- Keep view values escaped with `esc(...)`.
- Keep POST redirect pattern untouched.
- Keep hidden IDs and `drama_id` in place.

### Don’t

- Don’t write SQL or business rules inside views.
- Don’t remove existing hidden inputs casually.
- Don’t bypass controller validation by relying only on HTML attributes.
- Don’t hardcode IDs/user values in view.

---

## 5) Fast edit cheat sheet

- Add role field → `create_role.view.php` + `role_details.view.php`
- Add schedule field → `schedule_management.view.php` (+ relevant modal/JS/partial)
- Add PM request field → `search_managers.view.php`
- Add publish field → `drama_details.php`

Then map backend in: `readme/director/DIRECTOR_MVC_INPUT_MAPPING.md`
