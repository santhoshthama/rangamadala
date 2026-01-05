# Rangamadala Project - Complete File Dependencies & Includes Analysis

## Executive Summary

**Total Files Analyzed:** 30  
**PHP Files:** 15 (View files only - no controllers/models yet)  
**JavaScript Files:** 15  
**CSS Files:** 1  

---

## Directory Structure with File Dependencies

```
Rangamadala/
├── app/
│   ├── views/
│   │   ├── director/
│   │   │   ├── dashboard.php
│   │   │   ├── create_drama.php
│   │   │   ├── drama_details.php
│   │   │   ├── manage_dramas.php
│   │   │   ├── manage_roles.php
│   │   │   ├── role_management.php
│   │   │   ├── assign_managers.php
│   │   │   ├── schedule_management.php
│   │   │   ├── search_artists.php
│   │   │   └── view_services_budget.php
│   │   ├── production_manager/
│   │   │   ├── dashboard.php
│   │   │   ├── manage_budget.php
│   │   │   ├── manage_services.php
│   │   │   ├── book_theater.php
│   │   │   └── manage_schedule.php
│   │   └── includes/
│   │       ├── header.php (MISSING - TODO)
│   │       └── footer.php (MISSING - TODO)
│   ├── controllers/ (EMPTY - NEEDS IMPLEMENTATION)
│   ├── models/ (EMPTY - NEEDS IMPLEMENTATION)
│   ├── core/ (EMPTY - NEEDS IMPLEMENTATION)
│   └── uploads/
│       └── profile_images/
├── public/
│   └── assets/
│       ├── CSS/
│       │   └── ui-theme.css
│       └── JS/
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
└── readme/
    ├── CRUD_OPERATIONS_ANALYSIS.md (NEW)
    ├── CRUD_QUICK_REFERENCE.md (NEW)
    ├── CRUD_DETAILED_MAPPING.md (NEW)
    ├── README.md
    ├── readme.md
    ├── readme2.md
    ├── QUICK_START_GUIDE.md
    ├── RESTRUCTURE_COMPLETE.md
    ├── VERIFICATION_CHECKLIST.md
    ├── VISUAL_GUIDE.md
    ├── STRUCTURE_MIGRATION.md
    └── TABBED_INTERFACE_IMPLEMENTATION.md
```

---

## PHP View Files - Dependency Map

### Director Views

#### 1. dashboard.php
**Purpose:** Drama overview and quick stats  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/director-dashboard.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
<!-- Authentication check -->
<!-- Database connection -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs user authentication
- Needs drama data from database

**Functions Called:**
- `loadDramaData()` - from director-dashboard.js
- `acceptApplication()` - from director-dashboard.js
- `rejectApplication()` - from director-dashboard.js

---

#### 2. create_drama.php
**Purpose:** Create new drama production  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/create-drama.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Needs user authentication
- Needs access to upload directory for certificates/images

**Form Submission:**
- POST to BudgetController (backend needed)
- File upload handling needed

---

#### 3. drama_details.php
**Purpose:** Edit drama information  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/drama-details.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs drama data from database

**Functions Called:**
- `loadDramaDetails()` - from drama-details.js
- `enableEdit()` - from drama-details.js
- `saveDramaDetails()` - from drama-details.js

---

#### 4. manage_dramas.php
**Purpose:** List and manage dramas  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/manage-dramas.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Needs list of dramas from database
- Needs user authentication

---

#### 5. manage_roles.php
**Purpose:** Role management and applications  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/manage-roles.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs roles data from database
- Needs applications data from database

**Functions Called:**
- `loadRolesData()` - from manage-roles.js
- `submitCreateRole()` - from manage-roles.js
- `acceptApplication()` - from manage-roles.js
- `rejectApplication()` - from manage-roles.js

---

#### 6. role_management.php
**Purpose:** Alternative role management interface  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/role-management.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Note:** Mostly placeholder, could be merged with manage_roles.php

---

#### 7. assign_managers.php
**Purpose:** Assign production managers to dramas  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/assign-managers.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs current manager info from database
- Needs artist search capability

**Functions Called:**
- `loadManagerData()` - from assign-managers.js
- `searchArtists()` - from assign-managers.js
- `submitAssignManager()` - from assign-managers.js

---

#### 8. schedule_management.php
**Purpose:** Director's schedule management  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/schedule-management.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs schedule data from database

**Functions Called:**
- `loadScheduleData()` - from schedule-management.js
- `submitSchedule()` - from schedule-management.js

---

#### 9. search_artists.php
**Purpose:** Search and request artists for roles  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/search-artists.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs artist database access
- Needs role list for drama

**Functions Called:**
- `searchArtists()` - from search-artists.js
- `applyFilters()` - from search-artists.js
- `submitRoleRequest()` - from search-artists.js

---

#### 10. view_services_budget.php
**Purpose:** View-only services and budget (read-only)  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/view-services-budget.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs service bookings from database
- Needs budget items from database
- Needs theater bookings from database

**Functions Called:**
- `loadServices()` - from view-services-budget.js
- `loadBudget()` - from view-services-budget.js
- `loadTheaters()` - from view-services-budget.js

---

### Production Manager Views

#### 1. dashboard.php (Production Manager)
**Purpose:** Production manager overview  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/production-manager-dashboard.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs budget statistics
- Needs service request status
- Needs theater booking info

**Functions Called:**
- `loadDashboardData()` - from production-manager-dashboard.js

---

#### 2. manage_budget.php
**Purpose:** Budget management and tracking  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/manage-budget.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs budget items from database

**Functions Called:**
- `loadBudgetItems()` - from manage-budget.js
- `saveBudgetItem()` - from manage-budget.js
- `editBudgetItem()` - from manage-budget.js
- `deleteBudgetItem()` - from manage-budget.js

---

#### 3. manage_services.php
**Purpose:** Service booking and management  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/manage-services.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs service provider database
- Needs service booking data

**Functions Called:**
- `loadServices()` - from manage-services.js
- `submitServiceRequest()` - from manage-services.js
- `updateServiceProviders()` - from manage-services.js
- `processCardPayment()` - from manage-services.js

---

#### 4. book_theater.php
**Purpose:** Theater booking management  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/manage-theater.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs theater database
- Needs booking data

**Functions Called:**
- `loadTheaterBookings()` - from manage-theater.js
- `submitTheaterBooking()` - from manage-theater.js
- `updateTheaterDetails()` - from manage-theater.js

---

#### 5. manage_schedule.php (Production Manager)
**Purpose:** Service schedule management  
**Includes/Links:**
```html
<!-- CSS -->
<link rel="stylesheet" href="../../public/assets/CSS/ui-theme.css">

<!-- JavaScript -->
<script src="../../public/assets/JS/manage-schedule.js"></script>

<!-- Missing Includes -->
<!-- header.php -->
<!-- footer.php -->
```
**Data Dependencies:**
- Requires drama_id URL parameter
- Needs schedule data

**Functions Called:**
- `loadScheduleData()` - from manage-schedule.js
- `submitSchedule()` - from manage-schedule.js

---

## JavaScript Files - Dependencies

### 1. assign-managers.js
**Requires:**
- DOM Elements with specific IDs
- URL parameter parsing
- Modal functionality (vanilla JS)

**Functions Exported:**
- `loadManagerData()`
- `openAssignManagerModal()`
- `searchArtists()`
- `selectArtist()`
- `submitAssignManager()`
- `viewManagerDetails()`
- `removeManager()`

**Dependencies:**
- FontAwesome icons (fa-search, fa-plus, etc.)
- ui-theme.css

---

### 2. create-drama.js
**Status:** Placeholder  
**Requires:**
- HTML form element
- UI theme CSS

---

### 3. director-dashboard.js
**Requires:**
- URL parameter parsing
- DOM elements

**Functions Exported:**
- `loadDramaData()`
- `viewApplication()`
- `acceptApplication()`
- `rejectApplication()`
- `viewManagerDetails()`
- `refreshDashboard()`

---

### 4. drama-details.js
**Requires:**
- URL parameter parsing
- Form fields
- Edit mode toggle

**Functions Exported:**
- `loadDramaDetails()`
- `enableEdit()`
- `cancelEdit()`
- `saveDramaDetails()`

---

### 5. manage-budget.js
**Requires:**
- Modal functionality
- Form validation
- Table elements

**Functions Exported:**
- `openAddBudgetModal()`
- `closeBudgetModal()`
- `saveBudgetItem()`
- `editBudgetItem()`
- `deleteBudgetItem()`
- `loadBudgetItems()`
- `exportBudgetReport()`
- `printBudgetItems()`

---

### 6. manage-dramas.js
**Status:** Placeholder

---

### 7. manage-roles.js
**Requires:**
- Tab switching
- Modal functionality
- Form validation

**Functions Exported:**
- `showTab()`
- `loadRolesData()`
- `openCreateRoleModal()`
- `submitCreateRole()`
- `acceptApplication()`
- `rejectApplication()`
- `removeAssignedRole()`

---

### 8. manage-schedule.js
**Requires:**
- Calendar generation
- Modal functionality
- Date/time handling

**Functions Exported:**
- `showScheduleTab()`
- `loadScheduleData()`
- `openScheduleModal()`
- `submitSchedule()`
- `editScheduleEvent()`
- `deleteScheduleEvent()`
- `confirmAttendance()`
- `exportToCalendar()`
- `generateCalendar()`

---

### 9. manage-services.js
**Requires:**
- Service provider data
- Modal functionality
- Form validation
- Card payment form

**Functions Exported:**
- `openRequestServiceModal()`
- `submitServiceRequest()`
- `updateServiceProviders()`
- `openCardPaymentForService()`
- `processCardPayment()`
- `filterServices()`
- `viewServiceDetails()`
- `cancelService()`
- `loadServices()`

---

### 10. manage-theater.js
**Requires:**
- Theater database
- Cost calculation
- Modal functionality

**Functions Exported:**
- `openBookTheaterModal()`
- `updateTheaterDetails()`
- `calculateEstimatedCost()`
- `submitTheaterBooking()`
- `viewBookingDetails()`
- `editBooking()`
- `cancelBooking()`
- `loadTheaterBookings()`

---

### 11. production-manager-dashboard.js
**Requires:**
- Navigation logic
- URL parameter handling

**Functions Exported:**
- `navigateTo()`
- `loadDashboardData()`
- `manageServices()`
- `manageBudget()`
- `bookTheater()`

---

### 12. role-management.js
**Status:** Placeholder

---

### 13. schedule-management.js
**Same as manage-schedule.js** (duplicate)

---

### 14. search-artists.js
**Requires:**
- Filter functionality
- Modal for role requests

**Functions Exported:**
- `searchArtists()`
- `applyFilters()`
- `clearFilters()`
- `viewArtistProfile()`
- `openRoleRequestModal()`
- `submitRoleRequest()`

---

### 15. view-services-budget.js
**Requires:**
- Tab switching (read-only)
- Display logic

**Functions Exported:**
- `showServiceTab()`
- `loadTabData()`
- `loadServices()`
- `loadBudget()`
- `loadTheaters()`
- `viewServiceDetails()`
- `exportBudgetReport()`
- `printServiceSummary()`

---

## CSS File

### ui-theme.css
**Location:** `public/assets/CSS/ui-theme.css`  
**Dependencies:** FontAwesome icons  
**Used By:** All 15 PHP view files  
**Contains:**
- Root CSS variables (colors, spacing, fonts)
- Component styles (buttons, modals, forms, tabs)
- Layout styles
- Responsive design

---

## Missing Files That Need to Be Created

### Core Framework Files
```
app/core/
├── App.php              - Router and application bootstrap
├── Controller.php       - Base controller class
├── Database.php         - PDO database connection
├── config.php           - Database credentials and configuration
├── functions.php        - Helper functions
├── init.php             - Autoloading and initialization
└── Media.php            - File upload handling
```

### View Includes
```
app/views/includes/
├── header.php           - Navigation and meta tags
└── footer.php           - Footer content
```

### Controllers (Required for 52 CRUD Operations)
```
app/controllers/
├── AuthController.php           - Authentication
├── DramaController.php          - Drama CRUD
├── BudgetController.php         - Budget CRUD (5 operations)
├── ServiceController.php        - Service CRUD (6 operations)
├── RoleController.php           - Role CRUD (5 operations)
├── ScheduleController.php       - Schedule CRUD (6 operations)
├── TheaterController.php        - Theater CRUD (5 operations)
├── ManagerController.php        - Manager assignment (5 operations)
├── ArtistController.php         - Artist search (4 operations)
├── DashboardController.php      - Dashboard data (1 operation)
└── ViewController.php           - Read-only views (5 operations)
```

### Models
```
app/models/
├── Budget.php
├── Service.php
├── Role.php
├── Schedule.php
├── Theater.php
├── Drama.php
├── DramaManager.php
├── User.php
├── Application.php
└── Database.php (base model class)
```

### Database Setup
```
database_setup.sql - Contains all required table definitions
```

---

## External Dependencies

### CSS Framework/Libraries
- **FontAwesome** (Icons) - Referenced in PHP files

### JavaScript
- **Vanilla JavaScript** (No frameworks required)
- No jQuery, React, Vue, Angular, etc.

### PHP
- **PDO** for database
- **PHP 7.4+** recommended

### Database
- **MySQL 5.7+** or **MariaDB 10.3+**

---

## Dependency Graph Summary

```
View Files (PHP)
    ↓
JavaScript Files
    ↓
CSS (ui-theme.css)
    ↓
DOM Elements
    
Plus Missing:
View Files
    ↓
Header/Footer Includes
    ↓
Controllers
    ↓
Models
    ↓
Database

Frontend Complete ✅
Backend Missing ⏳
```

---

## Implementation Priority

1. **High Priority** (Needed for basic functionality)
   - Database tables
   - BudgetController & Budget model
   - ServiceController & Service model
   - TheaterController & Theater model

2. **Medium Priority** (Core features)
   - RoleController & Role model
   - ScheduleController & Schedule model
   - ManagerController & DramaManager model
   - DramaController & Drama model

3. **Low Priority** (Supporting features)
   - ArtistController & User model
   - DashboardController
   - AuthController
   - Core framework files

---

**Analysis Complete**  
**Total Tracked Dependencies:** 52 CRUD Operations + 15 View Files + 15 JS Files + 1 CSS File = 83 Files/Operations
