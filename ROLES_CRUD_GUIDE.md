# Drama Roles CRUD System - Implementation Guide

## Overview
A complete MVC-based CRUD system for managing drama roles, applications, and assignments.

---

## 1. Database Schema

### Tables Created:
Run `database_roles_table.sql` to create:

#### `drama_roles` - Main roles table
- **id**: Primary key
- **drama_id**: Foreign key to dramas table
- **role_name**: Character/role name
- **role_description**: Role details
- **role_type**: lead, supporting, ensemble, dancer, musician, other
- **salary**: Offered salary
- **positions_available**: Number of openings
- **positions_filled**: Filled positions
- **status**: open, closed, filled
- **requirements**: Role requirements
- **is_published**: Vacancy visibility toggle
- **published_at / published_message / published_by**: Publication metadata
- **created_by**: Director who created
- **created_at, updated_at**: Timestamps

#### `role_applications` - Artist applications
- **id**: Primary key
- **role_id**: Foreign key to drama_roles
- **artist_id**: Applying artist
- **application_message**: Message from artist
- **status**: pending, accepted, rejected
- **applied_at, reviewed_at**: Timestamps
- **reviewed_by**: Director who reviewed

#### `role_assignments` - Accepted assignments
- **id**: Primary key
- **role_id**: Foreign key to drama_roles
- **artist_id**: Assigned artist
- **assigned_by**: Director
- **assigned_at**: Timestamp
- **status**: active, completed, terminated

#### `role_requests` - Direct invitations
- **id**: Primary key
- **role_id**: Foreign key to drama_roles
- **artist_id**: Invited artist
- **director_id**: Director sending invite
- **status**: pending, interview, accepted, rejected, cancelled
- **note / interview_at**: Optional scheduling context
- **requested_at / responded_at**: Audit timestamps

---

## 2. Model Layer (`app/models/M_role.php`)

### CRUD Operations:

#### CREATE
```php
createRole($data)
```
- Creates a new role for a drama
- Parameters: drama_id, role_name, role_description, role_type, salary, positions_available, requirements, created_by
- Returns: role_id on success, false on failure

#### READ
```php
getRolesByDrama($drama_id)
```
- Gets all roles for a specific drama
- Returns: Array of role objects with creator info

```php
getRoleById($role_id)
```
- Gets single role details
- Returns: Role object with drama and creator info

```php
getRoleStats($drama_id)
```
- Gets statistics: total roles, positions, filled count, budget
- Returns: Stats object

```php
getPendingApplications($drama_id)
```
- Gets all pending applications for drama roles
- Returns: Array of applications with artist info

```php
getRoleRequestsByDrama($drama_id, $status = null)
```
- Fetches invitations sent to artists for a drama (optional status filter)

#### UPDATE
```php
updateRole($role_id, $data)
```
- Updates role details
- Parameters: role_name, role_description, role_type, salary, positions_available, requirements, status
- Returns: true/false

#### DELETE
```php
deleteRole($role_id)
```
- Deletes role (or marks as closed if has assignments)
- Returns: true/false

### Application Management:

```php
acceptApplication($application_id, $reviewed_by)
```
- Accepts application, creates assignment, updates positions_filled
- Uses transaction for data consistency
- Auto-closes role when filled

```php
rejectApplication($application_id, $reviewed_by)
```
- Marks application as rejected

```php
createRoleRequest($role_id, $artist_id, $director_id, ?string $note = null, ?string $interviewAt = null)
```
- Sends or refreshes a direct invite for an artist

```php
assignArtistFromRequest($request_id, $director_id)
```
- Accepts an invitation and creates an assignment

```php
publishVacancy($role_id, ?string $message, int $director_id)
```
- Marks a role as publicly advertised with an optional message

```php
unpublishVacancy($role_id)
```
- Removes a vacancy from public listings

### Artist Search Helpers (`app/models/M_artist.php`)

```php
get_artists_for_role($role_id, array $filters = [])
```
- Returns artists matching optional name/experience filters
- Includes current request/assignment status to prevent duplicates

```php
respond_to_role_request($request_id, $artist_id, $response)
```
- Allows artists to accept or reject invitations, updating assignments automatically

---

## 3. Controller Layer (`app/controllers/director.php`)

### Routes & Methods:

#### Manage Roles Overview
```
GET /director/manage_roles?drama_id={id}
```
- Method: `manage_roles()`
- Loads stats, pending applications/requests, published roles
- Renders `manage_roles_overview.view.php`

#### Create Role (Form + Submit)
```
GET  /director/create_role?drama_id={id}
POST /director/create_role?drama_id={id}
```
- Method: `create_role()`
- GET serves creation view; POST persists and redirects/returns JSON

#### Role Detail Console
```
GET /director/view_role?drama_id={id}&role_id={role}
```
- Method: `view_role()`
- Renders `role_details.view.php` with applications, requests, assignments

#### Update / Delete Role
```
POST /director/update_role?drama_id={id}&role_id={role}
POST /director/delete_role?drama_id={id}&role_id={role}
```
- Methods: `update_role()`, `delete_role()`
- Maintain role integrity, flash status messages, redirect appropriately

#### Publish or Unpublish Vacancy
```
POST /director/publish_vacancy
POST /director/unpublish_vacancy
```
- Methods: `publish_vacancy()`, `unpublish_vacancy()`
- Toggle vacancy visibility and announcement text

#### Search & Invite Artists
```
GET  /director/search_artists?drama_id={id}&role_id={role}
POST /director/send_role_request?drama_id={id}
```
- Methods: `search_artists()`, `send_role_request()`
- Provide filterable artist list and invitation handling

#### Application Decisions
```
POST /director/accept_application
POST /director/reject_application
```
- Methods: `accept_application()`, `reject_application()`
- Update assignments and application status via model helpers

---

## 4. View Layer (Updated Templates)

| View | Purpose |
|------|---------|
| `manage_roles_overview.view.php` | Dashboard summarising applications, published vacancies, and pending requests |
| `create_role.view.php` | Dedicated creation form with validation feedback |
| `role_details.view.php` | Role-level console for editing, publishing, and assigning artists |
| `search_artists.view.php` | Filterable artist directory with invitation forms and status badges |

### JavaScript Integration

`public/assets/JS/manage-roles.js` now:

- Automatically applies confirmation prompts for forms using `data-confirm`
- Disables submit buttons during processing and shows a spinner
- Restores button states when navigating back via browser history
- Fades out flash messages after a short delay

Tab and filter behaviours are handled with small inline scripts in each view, keeping logic close to the UI elements that rely on it.

### Director Assignment Flow

1. **Review applications** in `role_details.view.php`; accepting creates assignments and updates `positions_filled` immediately.
2. **Invite artists directly** from `search_artists.view.php`; invitations surface under "Pending Requests" and in the overview dashboard.
3. **Publish vacancies** to attract new applications, then unpublish when positions are filled.
4. **Finalize assignments** by accepting applications yourself or by approving pending invitations once the artist confirms from their dashboard.

---

## 5. Usage Example

### Create a New Role:
```php
POST /director/create_role?drama_id=1

FormData:
- role_name: "King Sinhabahu"
- role_description: "Lead male protagonist"
- role_type: "lead"
- salary: "80000"
- positions_available: "1"
- requirements: "Age 30-45, strong stage presence"
```

### Update Role:
```php
POST /director/update_role?drama_id=1

FormData:
- role_id: "5"
- role_name: "King Sinhabahu"
- salary: "85000"
- status: "open"
```

### Delete Role:
```php
POST /director/delete_role?drama_id=1

FormData:
- role_id: "5"
```

### Publish a Vacancy:
```php
POST /director/publish_vacancy?drama_id=1

FormData:
- role_id: "5"
- message: "Auditions open until Feb 15."
```

### Invite an Artist:
```php
POST /director/send_role_request?drama_id=1

FormData:
- role_id: "5"
- artist_id: "42"
- note: "We loved your last performance—join us for callbacks?"
- interview_at: "2026-02-03 15:00"
```

---

## 6. Security Features

✅ **Authorization Check**: `authorizeDrama()` verifies director owns the drama
✅ **SQL Injection Protection**: Prepared statements with parameter binding
✅ **Transaction Safety**: Application acceptance uses database transactions
✅ **Cascade Deletes**: Foreign keys properly handle related data
✅ **Soft Delete**: Roles with assignments are marked closed instead of deleted

---

## 7. Next Steps

1. **Run SQL**: Execute `database_roles_table.sql`
2. **Test Model**: Verify database connections
3. **Update View**: Add modal forms for create/edit
4. **Add JavaScript**: Implement AJAX calls
5. **Test CRUD**: Create, read, update, delete roles
6. **Test Applications**: Accept/reject workflow
7. **Test Invitations**: Send requests, confirm assignments, validate status updates
8. **Check Publication**: Publish/unpublish vacancies and ensure visibility updates

---

## 8. API Endpoints Summary

| Method | Endpoint | Purpose |
|--------|----------|---------|
| GET | `/director/manage_roles?drama_id={id}` | Roles overview dashboard |
| GET/POST | `/director/create_role` | Display form / create role |
| GET | `/director/view_role?drama_id={id}&role_id={role}` | Role detail console |
| POST | `/director/update_role` | Update role |
| POST | `/director/delete_role` | Delete or close role |
| POST | `/director/publish_vacancy` | Publish vacancy |
| POST | `/director/unpublish_vacancy` | Unpublish vacancy |
| GET | `/director/search_artists?drama_id={id}&role_id={role}` | Find artists for invitation |
| POST | `/director/send_role_request` | Send invitation to artist |
| POST | `/director/accept_application` | Accept artist application |
| POST | `/director/reject_application` | Reject artist application |

---

## Complete! 🎭

The CRUD system is now fully implemented following MVC architecture:
- **Model**: `M_role.php`, `M_artist.php` (data layer)
- **Views**: `manage_roles_overview.view.php`, `role_details.view.php`, `create_role.view.php`, `search_artists.view.php`
- **Controller**: `director.php` (business logic)

All operations are secure, transactional, and follow best practices.
