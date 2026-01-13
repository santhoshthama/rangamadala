# Rangamadala MVC - File Structure Migration

## Overview
This document outlines the migration of the Rangamadala project to follow the proper MVC (Model-View-Controller) architecture as defined in README_file_structure.md.

## New Directory Structure

```
rangamadala/
├── public/                          # Web entry point
│   ├── index.php                   # Main application entry
│   ├── robots.txt
│   └── assets/
│       ├── CSS/
│       │   └── ui-theme.css        # Unified theme stylesheet
│       ├── images/
│       │   └── default-avatar.jpg  # Default profile image
│       └── JS/                     # JavaScript files
│           ├── assign-managers.js
│           ├── create-drama.js
│           ├── director-dashboard.js
│           ├── drama-details.js
│           ├── manage-budget.js
│           ├── manage-dramas.js
│           ├── manage-roles.js
│           ├── manage-schedule.js
│           ├── manage-services.js
│           ├── manage-theater.js
│           ├── production-manager-dashboard.js
│           ├── role-management.js
│           ├── schedule-management.js
│           ├── search-artists.js
│           └── view-services-budget.js
│
├── app/                            # Application core (MVC)
│   ├── controllers/                # Request handlers
│   │   └── (add your PHP controllers here)
│   ├── models/                     # Data & business logic
│   │   └── (add your model files here)
│   ├── views/                      # User interface templates
│   │   ├── includes/               # Shared components
│   │   │   ├── header.php
│   │   │   └── footer.php
│   │   ├── director/               # Director-specific views
│   │   │   ├── assign_managers.php
│   │   │   ├── create_drama.php
│   │   │   ├── dashboard.php
│   │   │   ├── drama_details.php
│   │   │   ├── manage_dramas.php
│   │   │   ├── manage_roles.php
│   │   │   ├── role_management.php
│   │   │   ├── schedule_management.php
│   │   │   ├── search_artists.php
│   │   │   └── view_services_budget.php
│   │   ├── production_manager/     # Production Manager views
│   │   │   ├── book_theater.php
│   │   │   ├── dashboard.php
│   │   │   ├── manage_budget.php
│   │   │   ├── manage_schedule.php
│   │   │   └── manage_services.php
│   │   ├── artist/                 # Artist views
│   │   │   └── profile.php
│   │   ├── admin/                  # Admin views
│   │   ├── audience/               # Audience views
│   │   └── service_provider/       # Service Provider views
│   ├── core/                       # Framework files
│   │   ├── App.php                # Router
│   │   ├── Controller.php         # Base controller
│   │   ├── Database.php           # Database connection
│   │   ├── config.php             # Configuration
│   │   ├── functions.php          # Helper functions
│   │   ├── init.php               # Initialization
│   │   └── Media.php              # File upload handling
│   └── uploads/                    # User-generated content
│       ├── profile_images/
│       ├── certificates/
│       ├── portfolios/
│       └── class_materials/
│
├── dev/                            # Development files
│   └── dbbackup.sql               # Database backup
│
├── database_setup.sql              # Initial database schema
├── README.md                       # Copilot-friendly documentation
├── README_file_structure.md        # This file structure guide
└── .htaccess                       # URL rewrite rules
```

## CSS Paths Updated

All view files have been updated to reference the new CSS location:
- **OLD:** `href="../../ui-theme.css"`
- **NEW:** `href="../../public/assets/CSS/ui-theme.css"`

## JavaScript Paths Maintained

JavaScript files reference path:
- `src="../../public/assets/JS/[filename].js"`

All JavaScript files should be located in: `public/assets/JS/`

## View Organization

### Director Views (`app/views/director/`)
- `dashboard.php` - Drama-specific dashboard (drama_id parameter)
- `drama_details.php` - Edit drama information
- `manage_roles.php` - Manage roles for a drama
- `assign_managers.php` - Assign/change production manager
- `manage_dramas.php` - List of all dramas created
- `create_drama.php` - Create new drama form
- `search_artists.php` - Search for artists to assign roles
- `schedule_management.php` - Manage drama schedule
- `role_management.php` - Global role management (all dramas)
- `view_services_budget.php` - View-only services and budget

### Production Manager Views (`app/views/production_manager/`)
- `dashboard.php` - Drama-specific PM dashboard (drama_id parameter)
- `manage_services.php` - Book and manage services
- `manage_budget.php` - Full budget management
- `manage_schedule.php` - View-only schedule (Director controls)
- `book_theater.php` - Theater booking interface

## Implementation Notes

1. **Drama ID Parameter**: Director and PM views use `drama_id` URL parameter for drama context
2. **View-Only Access**: Production Manager has view-only access to schedule and director views have view-only budget access
3. **Shared Assets**: All views reference central CSS and JS locations in `public/assets/`
4. **Backward Compatibility**: Old `view/` folder can be kept during transition

## Next Steps

1. Create PHP controller files in `app/controllers/`
2. Create model files in `app/models/`
3. Implement core framework files in `app/core/`
4. Update `.htaccess` for proper routing
5. Update all view files with correct controller references
6. Test all drama-specific routes with drama_id parameter
7. Verify CSS and JS paths work correctly

## URL Structure Example

```
/app/views/director/dashboard.php?drama_id=1
/app/views/director/manage_roles.php?drama_id=1
/app/views/production_manager/manage_budget.php?drama_id=1
```

## Git Merge Notes

This restructuring should be committed as:
- Branch: `feature/mvc-restructure`
- All view files updated with correct paths
- CSS centralized in `public/assets/CSS/`
- Ready to merge with main after implementation of controllers and models
