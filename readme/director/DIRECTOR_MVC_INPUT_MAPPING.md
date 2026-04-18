# Director MVC Input Mapping (View → Controller → Model → DB)

Use this after editing a view field to wire it correctly to backend and database.

---

## 1) Dispatcher and feature controllers

Route entry class:

- `app/controllers/director.php`

Feature controllers:

- Role: `DirectorRoleController`
- Schedule: `DirectorScheduleController`
- PM: `DirectorManagerController`
- Drama: `DirectorDramaController`

Shared trait:

- `DirectorFeatureControllerTrait`
  - `authorizeDrama()`
  - `renderDramaView()`
  - `consumeFlash()`

---

## 2) Role CRUD mapping

### Create Role

- **View/Form**: `create_role.view.php`
- **Action**: `POST /director/create_role?drama_id=...`
- **Controller**: `DirectorRoleController::create_role()`
- **Input keys used**:
  - `role_name`, `role_description`, `role_type`, `salary`, `positions_available`, `requirements`
- **Model call**:
  - `M_role::createRole($createData)`
  - delegates to `M_director_role_core::createRole()`
- **Primary DB table**:
  - `drama_roles`

### Update Role

- **View/Form**: `role_details.view.php`
- **Action**: `POST /director/update_role?drama_id=...&role_id=...`
- **Controller**: `DirectorRoleController::update_role()`
- **Input keys used**:
  - `role_name`, `role_description`, `role_type`, `salary`, `positions_available`, `requirements`, `status`
- **Model call**:
  - `M_role::updateRole($roleId, $updateData)`
  - delegates to `M_director_role_core::updateRole()`
- **Primary DB table**:
  - `drama_roles`

### Delete Role

- **View/Form**: `manage_roles_overview.view.php`
- **Action**: `POST /director/delete_role?drama_id=...&role_id=...`
- **Controller**: `DirectorRoleController::delete_role()`
- **Model call**:
  - `M_role::deleteRole($roleId)`
  - delegates to `M_director_role_core::deleteRole()`
- **DB tables touched**:
  - `role_assignments` (check count)
  - `drama_roles` (delete or mark `status='closed'`)

---

## 3) Role request / assignment / application mapping

### Send Role Request

- **View/Form**: `search_artists.view.php`
- **Action**: `POST /director/send_role_request?drama_id=...`
- **Controller**: `DirectorRoleController::send_role_request()`
- **Input keys used**:
  - `role_id`, `artist_id`, optional `note`, optional `interview_at`
- **Model call**:
  - `M_role::createRoleRequest(...)`
  - delegates to `M_director_role_requests::createRoleRequest()`
- **Primary DB table**:
  - `role_requests`

### Remove Role Request

- **Action**: `POST /director/remove_role_request?drama_id=...`
- **Controller**: `DirectorRoleController::remove_role_request()`
- **Inputs**:
  - `request_id`
- **Model call**:
  - `M_role::cancelRoleRequestByDirector(...)`
- **DB table**:
  - `role_requests` (status update to cancelled)

### Accept / Reject Application

- **Action**: `POST /director/accept_application` or `POST /director/reject_application`
- **Controller**: `DirectorRoleController::accept_application()` / `reject_application()`
- **Inputs**:
  - `application_id`
- **Model calls**:
  - `M_role::acceptApplication()` / `M_role::rejectApplication()`
  - delegates to `M_director_role_applications`
- **DB tables**:
  - `role_applications`
  - `role_assignments` (on accept)
  - `drama_roles` (`positions_filled`, status updates)

### Remove Assignment

- **View/Form**: `role_details.view.php`
- **Action**: `POST /director/remove_assignment?drama_id=...`
- **Controller**: `DirectorRoleController::remove_assignment()`
- **Inputs**:
  - `assignment_id`, `role_id`, `remove_reason`, `return_to`
- **Model call**:
  - `M_role::removeAssignment($assignmentId)`
  - delegates to `M_director_role_assignments::removeAssignment()`
- **DB tables**:
  - `role_assignments` (delete)
  - `drama_roles` (decrement `positions_filled`, reopen role if needed)

---

## 4) Schedule CRUD/status mapping

### Create Schedule Event

- **View/Form**: `schedule_management.view.php` (modal/toolbar)
- **Action**: `POST /director/create_schedule?drama_id=...`
- **Controller**: `DirectorScheduleController::create_schedule()`
- **Input keys used**:
  - `event_type`, `event_title`, `event_description`, `scheduled_date`, `start_time`, `end_time`, `venue`, `role_id`, `notes`
- **Model calls**:
  - `M_schedule::isTimeSlotAvailable(...)`
  - `M_schedule::createEvent($data)`
  - optional conflict check: `M_artist_calendar::findConflictsForArtists(...)`
- **Primary DB table**:
  - `drama_schedules`

### Update Schedule Event

- **Action**: `POST /director/update_schedule?drama_id=...`
- **Controller**: `DirectorScheduleController::update_schedule()`
- **Inputs**:
  - includes hidden `event_id` + same event fields as create
- **Model calls**:
  - `M_schedule::getEventById($eventId)`
  - `M_schedule::isTimeSlotAvailable(..., $eventId)`
  - `M_schedule::updateEvent($eventId, $data)`
- **Primary DB table**:
  - `drama_schedules`

### Update Event Status

- **Action**: `POST /director/update_schedule_status?drama_id=...`
- **Controller**: `DirectorScheduleController::update_schedule_status()`
- **Inputs**:
  - `event_id`, `status`
- **Model call**:
  - `M_schedule::updateEventStatus($eventId, $status)`
- **DB table**:
  - `drama_schedules`

### Delete Event

- **Action**: `POST /director/delete_schedule?drama_id=...`
- **Controller**: `DirectorScheduleController::delete_schedule()`
- **Inputs**:
  - `event_id`
- **Model call**:
  - `M_schedule::deleteEvent($eventId)`
- **DB table**:
  - `drama_schedules`

---

## 5) Production Manager mapping

### Send Manager Request

- **View/Form**: `search_managers.view.php`
- **Action**: `POST /director/send_manager_request?drama_id=...`
- **Controller**: `DirectorManagerController::send_manager_request()`
- **Inputs**:
  - `artist_id`, optional `message`
- **Model call**:
  - `M_production_manager::createRequest($dramaId, $artistId, $directorId, $message)`
- **Primary DB table**:
  - `drama_manager_requests`

### Remove Assigned Manager

- **View/Form**: `assign_managers.view.php`
- **Action**: `POST /director/remove_manager?drama_id=...`
- **Controller**: `DirectorManagerController::remove_manager()`
- **Model call**:
  - `M_production_manager::removeManager($dramaId, $directorId)`
- **Primary DB table**:
  - `drama_manager_assignments` (status removal flow)

---

## 6) Drama details/publish mapping

### Update Drama (details)

- **Action**: `POST /director/update_drama?drama_id=...`
- **Controller**: `DirectorDramaController::update_drama()`
- **Inputs**:
  - `drama_name`, `certificate_number`, `owner_name`, `description`, optional file `certificate_image`
- **Model call**:
  - `M_drama::updateDrama($dramaId, $updateData)`
- **Primary DB table**:
  - `dramas`

### Publish Drama

- **View/Form**: `drama_details.php` publish section
- **Action**: `POST /director/publish_drama?drama_id=...`
- **Controller**: `DirectorDramaController::publish_drama()`
- **Inputs** (typical):
  - `category_id`, `public_description`, `genre`, `language`, `duration_minutes`, `venue`, `event_date`, `ticket_price`, `showing_prices`, optional publish media fields
- **Model calls**:
  - handled in drama model publish/update path used by `publish_drama()` implementation
- **Primary DB table**:
  - `dramas` (+ related publish metadata fields)

---

## 7) Flash + redirect pattern (must preserve)

Throughout Director controllers:

- Success/failure feedback:
  - `$_SESSION['message']`
  - `$_SESSION['message_type']` (`success`, `error`, `info`, `warning`)
- View rendering:
  - `include APPROOT . '/views/_partials/flash.php'`
- POST handlers follow redirect-after-post pattern to avoid duplicate submissions.

---

## 8) If you add a new input field tomorrow

For any new field `new_field`:

1. Add in view form (`name="new_field"`).
2. Read in controller:
   - `$newField = trim($_POST['new_field'] ?? '');`
3. Validate and normalize.
4. Add to `$createData`/`$updateData`.
5. Ensure model method accepts and binds it.
6. Ensure DB column exists (migration if needed).
7. Keep flash + redirect behavior unchanged.

That is the full field path: **View → Controller → Model → Database**.
