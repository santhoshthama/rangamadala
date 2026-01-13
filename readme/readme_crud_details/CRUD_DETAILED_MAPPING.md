# Complete CRUD Operations Mapping - All Files Included

## Table of Contents
1. [Budget Management](#budget-management)
2. [Service Management](#service-management)
3. [Role Management](#role-management)
4. [Schedule Management](#schedule-management)
5. [Theater Booking](#theater-booking)
6. [Manager Assignment](#manager-assignment)
7. [Drama Operations](#drama-operations)
8. [Search & Filter](#search--filter)
9. [Dashboard Operations](#dashboard-operations)
10. [View Operations](#view-operations)

---

## Budget Management

**File:** `public/assets/JS/manage-budget.js`  
**Controller Needed:** `app/controllers/BudgetController.php`  
**Model Needed:** `app/models/Budget.php`

### Operations

#### 1. CREATE - Add Budget Item
```javascript
Function: saveBudgetItem()
Input Fields:
  - itemName (string)
  - itemCategory (string)
  - itemAmount (decimal)
  - paymentStatus (enum: pending, paid, partial)
  - notes (text)
  - drama_id (int - from URL param)

Expected Backend Call:
  POST /controllers/BudgetController.php?action=create
  
Database: INSERT INTO budgets (drama_id, item_name, category, amount, paid_status, notes, added_by_manager_id)
```

#### 2. READ - Load Budget Items
```javascript
Function: loadBudgetItems()
Parameters:
  - drama_id (from URL)
  
Expected Backend Call:
  GET /controllers/BudgetController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM budgets WHERE drama_id = ?
```

#### 3. UPDATE - Edit Budget Item
```javascript
Function: editBudgetItem(itemId)
Parameters:
  - itemId (int)
  - drama_id (int)
  
Expected Backend Call:
  POST /controllers/BudgetController.php?action=update&id={itemId}
  
Database: UPDATE budgets SET ... WHERE id = ?
```

#### 4. DELETE - Remove Budget Item
```javascript
Function: deleteBudgetItem(itemId)
Parameters:
  - itemId (int)
  - drama_id (int)
  
Expected Backend Call:
  POST /controllers/BudgetController.php?action=delete&id={itemId}
  
Database: DELETE FROM budgets WHERE id = ? AND drama_id = ?
```

#### 5. READ - Export Report
```javascript
Function: exportBudgetReport()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/BudgetController.php?action=export&drama_id={id}&format=csv
  
Action: Generate PDF/CSV file
```

---

## Service Management

**File:** `public/assets/JS/manage-services.js`  
**Controller Needed:** `app/controllers/ServiceController.php`  
**Model Needed:** `app/models/Service.php`

### Operations

#### 1. CREATE - Request Service
```javascript
Function: submitServiceRequest()
Input Fields:
  - serviceType (select: sound, lighting, makeup, transport, catering)
  - serviceProviderId (int)
  - serviceDate (date)
  - serviceDescription (text)
  - estimatedBudget (decimal)
  - specialRequirements (text)
  - drama_id (int)

Expected Backend Call:
  POST /controllers/ServiceController.php?action=create
  
Database: INSERT INTO service_bookings (drama_id, service_type, provider_id, service_date, status, amount)
```

#### 2. READ - Load Services
```javascript
Function: loadServices()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/ServiceController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM service_bookings WHERE drama_id = ?
```

#### 3. READ - View Service Details
```javascript
Function: viewServiceDetails(serviceId)
Parameters:
  - serviceId (int)
  
Expected Backend Call:
  GET /controllers/ServiceController.php?action=get&id={serviceId}
  
Database: SELECT * FROM service_bookings WHERE id = ?
```

#### 4. READ - Filter Services
```javascript
Function: filterServices(status)
Parameters:
  - status (filter: all, pending, confirmed, rejected, cancelled)
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/ServiceController.php?action=filter&status={status}&drama_id={id}
  
Database: SELECT * FROM service_bookings WHERE drama_id = ? AND status = ?
```

#### 5. DELETE - Cancel Service
```javascript
Function: cancelService(serviceId)
Parameters:
  - serviceId (int)
  
Expected Backend Call:
  POST /controllers/ServiceController.php?action=cancel&id={serviceId}
  
Database: UPDATE service_bookings SET status = 'cancelled' WHERE id = ?
```

#### 6. UPDATE - Process Payment
```javascript
Function: processCardPayment()
Input Fields:
  - cardHolderName (string)
  - cardNumber (string)
  - expiryDate (string)
  - cvv (string)
  - amount (decimal)
  - serviceId (int)

Expected Backend Call:
  POST /controllers/ServiceController.php?action=processPayment
  
Database: INSERT INTO payments (service_id, amount, status, payment_method)
```

---

## Role Management

**File:** `public/assets/JS/manage-roles.js`  
**Controller Needed:** `app/controllers/RoleController.php`  
**Model Needed:** `app/models/Role.php`

### Operations

#### 1. CREATE - Create Role
```javascript
Function: submitCreateRole()
Input Fields:
  - roleName (string)
  - salary (decimal)
  - drama_id (int)

Expected Backend Call:
  POST /controllers/RoleController.php?action=create
  
Database: INSERT INTO roles (drama_id, role_name, salary, status)
```

#### 2. READ - Load Roles
```javascript
Function: loadRolesData()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/RoleController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM roles WHERE drama_id = ?
```

#### 3. UPDATE - Accept Application
```javascript
Function: acceptApplication(applicationId)
Parameters:
  - applicationId (int)
  
Expected Backend Call:
  POST /controllers/RoleController.php?action=acceptApp&id={applicationId}
  
Database: UPDATE applications SET status = 'accepted' WHERE id = ?
           UPDATE roles SET status = 'filled' WHERE id = ?
```

#### 4. UPDATE - Reject Application
```javascript
Function: rejectApplication(applicationId)
Parameters:
  - applicationId (int)
  
Expected Backend Call:
  POST /controllers/RoleController.php?action=rejectApp&id={applicationId}
  
Database: UPDATE applications SET status = 'rejected' WHERE id = ?
```

#### 5. DELETE - Remove Assignment
```javascript
Function: removeAssignedRole(roleId)
Parameters:
  - roleId (int)
  
Expected Backend Call:
  POST /controllers/RoleController.php?action=remove&id={roleId}
  
Database: UPDATE roles SET status = 'vacant' WHERE id = ?
           DELETE FROM applications WHERE role_id = ? AND status = 'accepted'
```

---

## Schedule Management

**File:** `public/assets/JS/schedule-management.js`  
**Controller Needed:** `app/controllers/ScheduleController.php`  
**Model Needed:** `app/models/Schedule.php`

### Operations

#### 1. CREATE - Create Schedule Event
```javascript
Function: submitSchedule()
Input Fields:
  - eventName (string)
  - eventDate (date)
  - eventTime (time)
  - venue (string)
  - eventType (string)
  - drama_id (int)

Expected Backend Call:
  POST /controllers/ScheduleController.php?action=create
  
Database: INSERT INTO drama_schedules (drama_id, event_name, event_date, event_time, venue, event_type)
```

#### 2. READ - Load Schedule Data
```javascript
Function: loadScheduleData()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/ScheduleController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM drama_schedules WHERE drama_id = ? ORDER BY event_date
```

#### 3. UPDATE - Edit Schedule Event
```javascript
Function: editScheduleEvent(eventId)
Parameters:
  - eventId (int)
  
Expected Backend Call:
  POST /controllers/ScheduleController.php?action=update&id={eventId}
  
Database: UPDATE drama_schedules SET ... WHERE id = ?
```

#### 4. DELETE - Delete Schedule Event
```javascript
Function: deleteScheduleEvent(eventId)
Parameters:
  - eventId (int)
  
Expected Backend Call:
  POST /controllers/ScheduleController.php?action=delete&id={eventId}
  
Database: DELETE FROM drama_schedules WHERE id = ?
```

#### 5. UPDATE - Confirm Attendance
```javascript
Function: confirmAttendance(eventId)
Parameters:
  - eventId (int)
  - userId (int)
  
Expected Backend Call:
  POST /controllers/ScheduleController.php?action=confirmAttendance&id={eventId}
  
Database: UPDATE attendance_tracking SET status = 'confirmed' WHERE event_id = ? AND user_id = ?
```

#### 6. DELETE - Cancel Schedule
```javascript
Function: cancelSchedule(eventId)
Parameters:
  - eventId (int)
  
Expected Backend Call:
  POST /controllers/ScheduleController.php?action=cancel&id={eventId}
  
Database: UPDATE drama_schedules SET status = 'cancelled' WHERE id = ?
```

---

## Theater Booking

**File:** `public/assets/JS/manage-theater.js`  
**Controller Needed:** `app/controllers/TheaterController.php`  
**Model Needed:** `app/models/Theater.php`

### Operations

#### 1. CREATE - Book Theater
```javascript
Function: submitTheaterBooking()
Input Fields:
  - theaterKey (string - theater identifier)
  - bookingDate (date)
  - bookingTime (time)
  - endTime (time)
  - estimatedAttendance (int)
  - specialRequests (text)
  - drama_id (int)

Expected Backend Call:
  POST /controllers/TheaterController.php?action=create
  
Database: INSERT INTO theater_bookings (drama_id, theater_id, booking_date, start_time, end_time, estimated_attendance, cost)
```

#### 2. READ - Load Theater Bookings
```javascript
Function: loadTheaterBookings()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/TheaterController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM theater_bookings WHERE drama_id = ? ORDER BY booking_date
```

#### 3. READ - View Booking Details
```javascript
Function: viewBookingDetails(bookingId)
Parameters:
  - bookingId (int)
  
Expected Backend Call:
  GET /controllers/TheaterController.php?action=get&id={bookingId}
  
Database: SELECT * FROM theater_bookings WHERE id = ?
```

#### 4. UPDATE - Edit Booking
```javascript
Function: editBooking(bookingId)
Parameters:
  - bookingId (int)
  
Expected Backend Call:
  POST /controllers/TheaterController.php?action=update&id={bookingId}
  
Database: UPDATE theater_bookings SET ... WHERE id = ?
```

#### 5. DELETE - Cancel Booking
```javascript
Function: cancelBooking(bookingId)
Parameters:
  - bookingId (int)
  
Expected Backend Call:
  POST /controllers/TheaterController.php?action=cancel&id={bookingId}
  
Database: UPDATE theater_bookings SET status = 'cancelled' WHERE id = ?
```

---

## Manager Assignment

**File:** `public/assets/JS/assign-managers.js`  
**Controller Needed:** `app/controllers/ManagerController.php`  
**Model Needed:** `app/models/DramaManager.php`

### Operations

#### 1. CREATE - Assign Manager
```javascript
Function: submitAssignManager()
Input Fields:
  - artistId (int)
  - notes (text)
  - drama_id (int)

Expected Backend Call:
  POST /controllers/ManagerController.php?action=assign
  
Database: INSERT INTO drama_managers (drama_id, artist_id, responsibilities, assigned_date, status)
           OR UPDATE drama_managers SET artist_id = ? WHERE drama_id = ?
```

#### 2. READ - Load Manager Data
```javascript
Function: loadManagerData()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/ManagerController.php?action=get&drama_id={id}
  
Database: SELECT * FROM drama_managers WHERE drama_id = ? AND status = 'active'
```

#### 3. READ - Search Artists
```javascript
Function: searchArtists()
Input:
  - searchTerm (string)
  
Expected Backend Call:
  GET /controllers/ArtistController.php?action=search&term={term}
  
Database: SELECT * FROM users WHERE role = 'artist' AND name LIKE ? LIMIT 20
```

#### 4. READ - View Manager Details
```javascript
Function: viewManagerDetails()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/ManagerController.php?action=getDetails&drama_id={id}
  
Database: SELECT u.*, dm.responsibilities FROM drama_managers dm 
          JOIN users u ON dm.artist_id = u.id WHERE dm.drama_id = ?
```

#### 5. DELETE - Remove Manager
```javascript
Function: removeManager()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  POST /controllers/ManagerController.php?action=remove&drama_id={id}
  
Database: UPDATE drama_managers SET status = 'removed' WHERE drama_id = ?
```

---

## Drama Operations

**Files:** `public/assets/JS/drama-details.js`, `public/assets/JS/director-dashboard.js`  
**Controller Needed:** `app/controllers/DramaController.php`  
**Model Needed:** `app/models/Drama.php`

### Operations (drama-details.js)

#### 1. READ - Load Drama Details
```javascript
Function: loadDramaDetails()
Parameters:
  - drama_id (int - from URL)
  
Expected Backend Call:
  GET /controllers/DramaController.php?action=get&id={dramaId}
  
Database: SELECT * FROM dramas WHERE id = ?
```

#### 2. UPDATE - Save Drama Details
```javascript
Function: saveDramaDetails()
Input Fields:
  - title (string)
  - description (text)
  - genre (string)
  - language (string)
  - certificate (string)
  - drama_id (int)

Expected Backend Call:
  POST /controllers/DramaController.php?action=update&id={dramaId}
  
Database: UPDATE dramas SET title = ?, description = ?, genre = ? ... WHERE id = ?
```

### Operations (director-dashboard.js)

#### 3. READ - Load Drama Data
```javascript
Function: loadDramaData()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/DramaController.php?action=getDashboard&id={dramaId}
  
Database: SELECT d.*, COUNT(r.id) as total_roles FROM dramas d 
          LEFT JOIN roles r ON d.id = r.drama_id WHERE d.id = ?
```

#### 4. UPDATE - Accept Application
```javascript
Function: acceptApplication(applicationId)
Parameters:
  - applicationId (int)
  
Expected Backend Call:
  POST /controllers/DramaController.php?action=acceptApp&id={applicationId}
  
Database: UPDATE applications SET status = 'accepted' WHERE id = ?
```

#### 5. UPDATE - Reject Application
```javascript
Function: rejectApplication(applicationId)
Parameters:
  - applicationId (int)
  
Expected Backend Call:
  POST /controllers/DramaController.php?action=rejectApp&id={applicationId}
  
Database: UPDATE applications SET status = 'rejected' WHERE id = ?
```

---

## Search & Filter

**File:** `public/assets/JS/search-artists.js`  
**Controller Needed:** `app/controllers/ArtistController.php`  
**Model Needed:** `app/models/User.php`

### Operations

#### 1. READ - Search Artists
```javascript
Function: searchArtists()
Input:
  - searchTerm (string)
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/ArtistController.php?action=search&term={term}
  
Database: SELECT * FROM users WHERE role = 'artist' AND 
          (name LIKE ? OR artist_code LIKE ?) LIMIT 20
```

#### 2. READ - Apply Filters
```javascript
Function: applyFilters()
Input:
  - experience (string)
  - specialization (string)
  - availability (string)
  - rating (string)
  
Expected Backend Call:
  GET /controllers/ArtistController.php?action=filter&exp={exp}&spec={spec}
  
Database: SELECT * FROM users WHERE role = 'artist' AND 
          experience_years >= ? AND specialization = ? ...
```

#### 3. READ - View Artist Profile
```javascript
Function: viewArtistProfile(artistId)
Parameters:
  - artistId (int)
  
Expected Backend Call:
  GET /controllers/ArtistController.php?action=getProfile&id={artistId}
  
Database: SELECT * FROM users WHERE id = ? AND role = 'artist'
```

#### 4. CREATE - Submit Role Request
```javascript
Function: submitRoleRequest()
Input:
  - artistId (int)
  - roleId (int)
  - message (text)
  - drama_id (int)

Expected Backend Call:
  POST /controllers/ArtistController.php?action=requestRole
  
Database: INSERT INTO applications (role_id, artist_id, message, status)
```

---

## Dashboard Operations

**File:** `public/assets/JS/production-manager-dashboard.js`  
**Controller Needed:** `app/controllers/DashboardController.php`

### Operations

#### 1. READ - Load Dashboard Data
```javascript
Function: loadDashboardData()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/DashboardController.php?action=getPMDash&drama_id={id}
  
Database: Query multiple tables for statistics:
  - SELECT SUM(amount) FROM budgets WHERE drama_id = ?
  - SELECT COUNT(*) FROM service_bookings WHERE drama_id = ? AND status = 'pending'
  - SELECT * FROM theater_bookings WHERE drama_id = ? ORDER BY booking_date LIMIT 5
```

---

## View Operations (Read-Only)

**File:** `public/assets/JS/view-services-budget.js`  
**Controller Needed:** `app/controllers/ViewController.php`

These are read-only operations for Director to view services and budget managed by Production Manager.

### Operations

#### 1. READ - Load Services
```javascript
Function: loadServices()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/ServiceController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM service_bookings WHERE drama_id = ?
```

#### 2. READ - Load Budget
```javascript
Function: loadBudget()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/BudgetController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM budgets WHERE drama_id = ?
```

#### 3. READ - Load Theaters
```javascript
Function: loadTheaters()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/TheaterController.php?action=getAll&drama_id={id}
  
Database: SELECT * FROM theater_bookings WHERE drama_id = ?
```

#### 4. READ - View Service Details
```javascript
Function: viewServiceDetails(serviceId)
Parameters:
  - serviceId (int)
  
Expected Backend Call:
  GET /controllers/ServiceController.php?action=get&id={serviceId}
  
Database: SELECT * FROM service_bookings WHERE id = ?
```

#### 5. READ - Export Budget Report
```javascript
Function: exportBudgetReport()
Parameters:
  - drama_id (int)
  
Expected Backend Call:
  GET /controllers/BudgetController.php?action=export&drama_id={id}&format=pdf
  
Action: Generate and download PDF/CSV report
```

---

## Summary Table

| Operation | File | Function | Type | Status |
|-----------|------|----------|------|--------|
| Add Budget Item | manage-budget.js | saveBudgetItem() | CREATE | ⏳ |
| Load Budget Items | manage-budget.js | loadBudgetItems() | READ | ⏳ |
| Edit Budget Item | manage-budget.js | editBudgetItem() | UPDATE | ⏳ |
| Delete Budget Item | manage-budget.js | deleteBudgetItem() | DELETE | ⏳ |
| Export Budget | manage-budget.js | exportBudgetReport() | READ | ⏳ |
| Request Service | manage-services.js | submitServiceRequest() | CREATE | ⏳ |
| Load Services | manage-services.js | loadServices() | READ | ⏳ |
| View Service Details | manage-services.js | viewServiceDetails() | READ | ⏳ |
| Filter Services | manage-services.js | filterServices() | READ | ⏳ |
| Cancel Service | manage-services.js | cancelService() | DELETE | ⏳ |
| Process Payment | manage-services.js | processCardPayment() | UPDATE | ⏳ |
| Create Role | manage-roles.js | submitCreateRole() | CREATE | ⏳ |
| Load Roles | manage-roles.js | loadRolesData() | READ | ⏳ |
| Accept Application | manage-roles.js | acceptApplication() | UPDATE | ⏳ |
| Reject Application | manage-roles.js | rejectApplication() | UPDATE | ⏳ |
| Remove Role | manage-roles.js | removeAssignedRole() | DELETE | ⏳ |
| Create Schedule | schedule-management.js | submitSchedule() | CREATE | ⏳ |
| Load Schedule | schedule-management.js | loadScheduleData() | READ | ⏳ |
| Edit Schedule Event | schedule-management.js | editScheduleEvent() | UPDATE | ⏳ |
| Delete Schedule Event | schedule-management.js | deleteScheduleEvent() | DELETE | ⏳ |
| Confirm Attendance | schedule-management.js | confirmAttendance() | UPDATE | ⏳ |
| Cancel Schedule | schedule-management.js | cancelSchedule() | DELETE | ⏳ |
| Book Theater | manage-theater.js | submitTheaterBooking() | CREATE | ⏳ |
| Load Bookings | manage-theater.js | loadTheaterBookings() | READ | ⏳ |
| View Booking Details | manage-theater.js | viewBookingDetails() | READ | ⏳ |
| Edit Booking | manage-theater.js | editBooking() | UPDATE | ⏳ |
| Cancel Booking | manage-theater.js | cancelBooking() | DELETE | ⏳ |
| Assign Manager | assign-managers.js | submitAssignManager() | CREATE | ⏳ |
| Load Manager Data | assign-managers.js | loadManagerData() | READ | ⏳ |
| Search Artists | assign-managers.js | searchArtists() | READ | ⏳ |
| View Manager Details | assign-managers.js | viewManagerDetails() | READ | ⏳ |
| Remove Manager | assign-managers.js | removeManager() | DELETE | ⏳ |
| Load Drama Details | drama-details.js | loadDramaDetails() | READ | ⏳ |
| Save Drama Details | drama-details.js | saveDramaDetails() | UPDATE | ⏳ |
| Load Drama Data | director-dashboard.js | loadDramaData() | READ | ⏳ |
| Accept App (Dashboard) | director-dashboard.js | acceptApplication() | UPDATE | ⏳ |
| Reject App (Dashboard) | director-dashboard.js | rejectApplication() | UPDATE | ⏳ |
| Search Artists | search-artists.js | searchArtists() | READ | ⏳ |
| Apply Filters | search-artists.js | applyFilters() | READ | ⏳ |
| View Artist Profile | search-artists.js | viewArtistProfile() | READ | ⏳ |
| Submit Role Request | search-artists.js | submitRoleRequest() | CREATE | ⏳ |
| Load Dashboard | production-manager-dashboard.js | loadDashboardData() | READ | ⏳ |
| Load Services (View) | view-services-budget.js | loadServices() | READ | ⏳ |
| Load Budget (View) | view-services-budget.js | loadBudget() | READ | ⏳ |
| Load Theaters (View) | view-services-budget.js | loadTheaters() | READ | ⏳ |
| View Service (View) | view-services-budget.js | viewServiceDetails() | READ | ⏳ |
| Export Report (View) | view-services-budget.js | exportBudgetReport() | READ | ⏳ |

---

**Total Operations: 52**
- CREATE: 9 ✏️
- READ: 21 👁️
- UPDATE: 11 📝
- DELETE: 9 🗑️

---

**Generated:** January 1, 2026
**Status:** Frontend Complete, Backend Pending
