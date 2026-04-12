# Final System Summary: Director, Artist, and Production Manager Modules

## 1) System Overview

This project is a PHP web application built using a custom **Model-View-Controller (MVC)** architecture.

At a high level:
- **Controllers** receive requests, validate permissions, call models, and choose views.
- **Models** handle database operations and business rules.
- **Views** render UI pages using data passed from controllers.

Core files:
- Routing entry: `app/core/App.php`
- Base controller utilities: `app/core/Controller.php`
- Authentication controller: `app/controllers/Login.php`
- Session termination: `app/controllers/Logout.php`

---

## 2) How MVC Is Connected in This System

### 2.1 Request Routing (Controller Selection)

`app/core/App.php`:
- Reads URL segments from `$_GET['url']`
- Resolves controller file under `app/controllers/`
- Instantiates the controller class
- Resolves method (default: `index`)
- Passes remaining URL segments as parameters

So, a URL like `/director/manage_roles?drama_id=5` resolves to:
- Controller: `Director`
- Method: `manage_roles()`
- Query input: `drama_id=5` (handled inside controller using `getQueryParam`)

### 2.2 Controller ↔ Model Connection

`app/core/Controller.php` provides:
- `getModel($name)` to instantiate model classes from `app/models/`
- `view($name, $data)` to render `.view.php` first, then `.php` fallback

This keeps business logic in models and request flow in controllers.

### 2.3 Controller ↔ View Connection

Controllers pass arrays into views (`$this->view('path', $data)`), and views render UI from those values.
Example:
- `Director::manage_roles()` prepares roles, stats, applications, requests
- Renders `app/views/director/manage_roles_overview.view.php`

---

## 3) Director Module (Detailed)

Main controller: `app/controllers/director.php`

### 3.1 Purpose
The Director module manages the full creative and casting lifecycle for a drama:
- Drama details
- Role creation and updates
- Artist search/invitations
- Vacancy publishing
- Application review and assignment
- Interview scheduling
- Production manager assignment
- Schedule management

### 3.2 Key Model Dependencies
In controller constructor:
- `M_drama`
- `M_role`
- `M_artist`
- `M_production_manager`
- `M_schedule`
- `M_notification`

### 3.3 Authorization Pattern
Director methods call `authorizeDrama()` which verifies:
1. Session user exists
2. `drama_id` exists
3. Drama exists
4. Current user is drama owner (`creator_artist_id` / `created_by`)

If validation fails, user is redirected safely (usually to `artistdashboard` or login).

### 3.4 Main Workflows
- **Dashboard**: `dashboard()` loads assigned PM and assigned artists by role.
- **Role Management**: `manage_roles()`, `create_role()`, `update_role()`, `delete_role()`, `view_role()`.
- **Artist Casting Pipeline**:
  - `search_artists()`
  - `send_role_request()`
  - `publish_vacancy()` / `unpublish_vacancy()`
  - `accept_application()` / `reject_application()`
  - `application_profile()`
  - `schedule_application_interview()`
- **Production Manager Assignment**:
  - `assign_managers()`
  - `search_managers()`
  - `send_manager_request()`
  - `remove_manager()`
- **Schedule Management**:
  - `schedule_management()`
  - `create_schedule()`, `update_schedule()`, `delete_schedule()`
  - `update_schedule_status()`
  - `check_date_availability()` (JSON endpoint)

### 3.5 Important Views
`app/views/director/` (examples):
- `dashboard.php`
- `drama_details.php`
- `manage_roles_overview.view.php`
- `role_details.view.php`
- `search_artists.view.php`
- `assign_managers.view.php`
- `schedule_management.view.php`
- `view_services_budget.php`

---

## 4) Artist Module (Detailed)

Main controller: `app/controllers/Artistdashboard.php`

### 4.1 Purpose
The Artist module is a multi-role workspace where the same artist can:
- Act as **Director** for their own dramas
- Act as **Production Manager** when assigned
- Act as **Actor** in assigned roles

It also handles applications, role requests, PM requests, interview confirmations, and notifications.

### 4.2 Key Model Dependencies
Used across methods:
- `M_artist`
- `M_drama`
- `M_production_manager`
- `M_role`
- `M_schedule`
- `M_notification`

### 4.3 Authentication Guard
Most methods begin with strict checks:
- `$_SESSION['user_id']` exists
- `$_SESSION['user_role'] === 'artist'`

Unauthorized users are redirected to login.

### 4.4 Main Workflows
- **Dashboard aggregation**: `index()` combines dramas as director/manager, acting assignments, requests, and stats.
- **Vacancy flow**:
  - `browse_vacancies()`
  - `apply_for_role()`
  - `submit_application()`
  - `my_applications()`
- **Request responses**:
  - `respond_to_request()` (role requests)
  - `respond_to_manager_request()` (PM requests)
- **Interview participation**:
  - `confirm_interview()`
- **Drama/event visibility**:
  - `view_drama()`
  - `event_detail()`
- **Notifications**:
  - `notifications()`
  - `mark_notification_read()`
  - `mark_all_notifications_read()`

### 4.5 Important Views
- `app/views/artistdashboard.view.php`
- `app/views/artist/browse_vacancies.php`
- `app/views/artist/apply_for_role_form.view.php`
- `app/views/artist/notifications.view.php`

---

## 5) Production Manager Module (Detailed)

Main controller: `app/controllers/Production_manager.php`

### 5.1 Purpose
This module manages operational execution for a drama:
- Service provider workflows
- Budget monitoring
- Theater bookings
- Service schedules
- Service request confirmation/rejection and payment-linked actions

### 5.2 Key Model Dependencies
- `M_drama`
- `M_service_request`
- `M_service_provider`
- `M_budget`
- `M_theater_booking`
- `M_service_schedule`
- `M_drama_services`
- `M_production_manager` (authorization checks)

### 5.3 Authorization Pattern
`authorizeDrama()` validates:
1. User is logged in
2. `drama_id` is present
3. Drama exists
4. Current user is an active manager for drama (`M_production_manager::isManagerForDrama`)

Unauthorized access sets flash message and redirects to artist dashboard.

### 5.4 Main Workflows
- `dashboard()` overview of services, budget snapshot, and related summary data
- `manage_services()` service requests list and provider details
- `browse_services()` provider browsing with filters
- `manage_budget()` budget totals and category summaries
- `book_theater()` theater booking data
- `manage_schedule()` service schedules
- `save_required_services()` add/remove required service categories
- JSON/API-style handlers:
  - `cancelServiceRequest()`
  - `confirmProviderResponse()`
  - `rejectProviderResponse()`

### 5.5 Important Views
`app/views/production_manager/`:
- `dashboard.php`
- `manage_services.php`
- `manage_budget.php`
- `book_theater.php`
- `manage_schedule.php`
- `browse_providers.php`

---

## 6) Authentication and Verification

### 6.1 Login Implementation
Controller: `app/controllers/Login.php`  
Model: `app/models/M_login.php`

Flow:
1. User submits email/password.
2. `M_login::authenticate()` fetches user by email.
3. Password is verified with `password_verify()`.
4. On success, session values are set:
   - `user_id`, `user_name`, `full_name`, `email`, `phone`, `user_role`
5. User is redirected by role:
   - admin → `/Admindashboard`
   - artist → `/ArtistDashboard`
   - service_provider → `/ServiceProviderDashboard`
   - audience → `/Audiencedashboard`

### 6.2 Session-Based Authorization
After login, every role module validates access at controller level:
- Artist controller checks `user_role === 'artist'`
- Director and PM flows use drama-specific ownership/assignment checks
- Invalid access attempts are redirected safely and often flash an error

### 6.3 Logout Implementation
Controller: `app/controllers/Logout.php`
- Clears `$_SESSION`
- Expires session cookie
- Calls `session_destroy()`
- Redirects to `/Home`

### 6.4 Verification Quality in This Project
Authorization is verified in **two layers**:
1. **Identity layer**: session-based login state (`user_id`, `user_role`)
2. **Resource layer**: ownership/assignment checks (`authorizeDrama`, `isManagerForDrama`)

This prevents users from accessing modules by URL manipulation only.

---

## 7) Role-to-Role Interaction Through MVC

### Director ↔ Artist
- Director publishes vacancies / sends requests (`Director` + `M_role`)
- Artist sees and applies/responds (`Artistdashboard` + `M_role`/`M_artist`)
- Director accepts/rejects and assigns artists (`M_role` assignment methods)

### Director ↔ Production Manager
- Director sends PM invitation (`Director` + `M_production_manager::createRequest`)
- Artist accepts PM request (`Artistdashboard::respond_to_manager_request`)
- PM assignment becomes active and grants PM module access (`isManagerForDrama`)

### Production Manager ↔ Service Providers
- PM creates/manages service requests (`Production_manager` + `M_service_request`)
- Provider responds; PM confirms/rejects proposal
- Payment and status transitions flow back through service request state

---

## 8) Summary

This system is a strongly role-driven MVC application where:
- `App.php` routes requests to the correct controller/method.
- Controllers orchestrate business logic and authorization.
- Models enforce data operations and key workflow rules.
- Views render role-specific dashboards and operations.

All three modules (Director, Artist, Production Manager) are connected through shared drama, role, request, and notification models, with session-based authentication plus drama-level authorization checks to protect access.
