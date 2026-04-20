# Rangamadala – Full Project & UI Structure (Updated)

This document summarizes the current project layout, with emphasis on the UI files you worked on, their locations, and asset paths.

## 1) High-level structure
```
Rangamadala/
├── app/
│   ├── controllers/             # (empty placeholder)
│   ├── core/                    # (empty placeholder)
│   ├── models/                  # (empty placeholder)
│   ├── uploads/
│   │   └── profile_images/
│   └── views/
│       ├── includes/            # (empty placeholder for shared header/footer)
│       ├── director/            # UI screens for Director role
│       └── production_manager/  # UI screens for Production Manager role
│
├── public/
│   ├── assets/
│   │   ├── CSS/
│   │   │   └── ui-theme.css     # Shared theme stylesheet (moved here)
│   │   └── JS/                  # All role-specific JS bundles
│   └── (entry files would live here; index.php not present currently)
│
├── dev/                         # Placeholder per README
├── readme/                      # Original documentation set
├── readme.md                    # Repo root readme
└── view/                        # Now empty (original views were moved into app/views)
```

## 2) UI view files (your part)
Located under `app/views/` after restructuring:

### Director views
- `app/views/director/dashboard.php`
- `app/views/director/manage_dramas.php`
- `app/views/director/create_drama.php`
- `app/views/director/drama_details.php`
- `app/views/director/manage_roles.php`
- `app/views/director/role_management.php`
- `app/views/director/schedule_management.php`
- `app/views/director/search_artists.php`
- `app/views/director/assign_managers.php`
- `app/views/director/view_services_budget.php`

### Production Manager views
- `app/views/production_manager/dashboard.php`
- `app/views/production_manager/manage_budget.php`
- `app/views/production_manager/manage_services.php`
- `app/views/production_manager/manage_schedule.php`
- `app/views/production_manager/book_theater.php`
- `app/views/production_manager/README.md`

## 3) Assets (CSS & JS)
- CSS (shared): `public/assets/CSS/ui-theme.css`
- JS (role-specific): `public/assets/JS/`
  - Director-related: `director-dashboard.js`, `manage-dramas.js`, `create-drama.js`, `drama-details.js`, `manage-roles.js`, `role-management.js`, `schedule-management.js`, `search-artists.js`, `assign-managers.js`, `view-services-budget.js`
  - PM-related: `production-manager-dashboard.js`, `manage-budget.js`, `manage-services.js`, `manage-schedule.js`, `manage-theater.js`

## 4) Correct asset links (already fixed)
Use absolute paths so they work from any view depth:
```
CSS: <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
JS : <script src="/Rangamadala/public/assets/JS/<file>.js"></script>
```
If your local base URL changes, replace `/Rangamadala` with your actual project root.

## 5) What was moved/organized
- All director and production manager view PHP files were relocated from `view/` into `app/views/<role>/`.
- Shared theme CSS was moved to `public/assets/CSS/ui-theme.css`.
- Existing JS bundles remain in `public/assets/JS/` (paths updated in the views).

## 6) Next steps (optional)
- Add real controller/model/core files into `app/controllers`, `app/models`, and `app/core` when ready.
- If you want, remove the now-empty `view/` folder after confirming everything loads.
- Add a front controller (e.g., `public/index.php`) and routing if/when you hook up backend logic.

---
_Last updated: 2025-12-27_
