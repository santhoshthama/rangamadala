# Artist Role Management Overhaul (January 2026)

## Overview
The Artist/Actor role management flow has been refactored to provide clearer navigation for directors, improved visibility into applications and invitations, and a streamlined experience for assigning talent to dramas. Updates span the database schema, PHP models and controllers, view templates, and supporting JavaScript.

## Feature Highlights
- Dedicated overview dashboard that surfaces key metrics, pending applications, published vacancies, and direct requests.
- Guided create → detail workflow for managing roles, including inline editing and vacancy publishing controls.
- Artist search experience with filters for name and experience, plus one-click invitation forms.
- Automatic handling of accept/reject flows for applications and invitations with updated status tracking.
- Consistent feedback via loading states, flash messaging, and graceful fallbacks when no data is available.

## Backend Changes
- `app/models/M_role.php` now supports vacancy publishing, role request creation, application acceptance/rejection, and additional lookup helpers.
- `app/models/M_artist.php` exposes refined artist search and request-response logic aligned with the director experience.
- `app/controllers/director.php` orchestrates new views (`manage_roles_overview`, `create_role`, `role_details`, `search_artists`) and provides shared redirect/JSON utilities for AJAX submissions.
- `app/controllers/Artistdashboard.php` signature updates ensure artists can respond to invitations with accurate context.

## Frontend Updates
- New view templates in `app/views/director/`:
  - `manage_roles_overview.view.php` – centralized dashboard with tabs for applications, vacancies, and requests.
  - `create_role.view.php` – dedicated creation form with validation messaging.
  - `role_details.view.php` – role-level console for editing, publishing, reviewing applications, and tracking assignments.
  - `search_artists.view.php` – dynamic artist directory with invitation actions.
- `public/assets/JS/manage-roles.js` enhanced to handle confirmation prompts, loading states, and history-aware button resets.
- Supporting styles leverage `ui-theme.css` to maintain the refreshed design language.

## Database Schema Adjustments
- `drama_roles` gains publication metadata (`is_published`, `published_at`, `published_message`, `published_by`) and indexed status fields.
- `role_requests` table introduced to store director-to-artist invitations with scheduling notes and audit timestamps.
- Index naming conventions standardized across `role_applications` and `role_assignments` for clarity.
- Run `update_rolecrud.sql` followed by the existing migration scripts to align your local MySQL instance.

## Deployment Checklist
1. Back up the current database (recommended before running structural updates).
2. Execute `update_rolecrud.sql` in phpMyAdmin to apply schema changes.
3. Deploy updated PHP files and clear any opcode/application caches.
4. Verify director workflows: overview, create role, role detail, publish vacancy, search artists, accept/reject application, send invitation.
5. Validate artist dashboard pending requests to ensure invitations surface correctly.

## Known Considerations
- Legacy `manage_roles.view.php` can be retired once routing confirms adoption of the new views.
- Existing vacancy records default to an unpublished state after migration; directors can republish manually.
- Ensure cron or notification systems (if any) hook into the new `role_requests` data to maintain communication parity.

For additional implementation notes, review `database_roles_table.sql`, `update_rolecrud.sql`, and the updated controller/model files listed above.
