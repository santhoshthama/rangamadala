# Production Manager Module - Rangamadala

## Overview

The Production Manager module is a comprehensive drama production management system within Rangamadala that enables production managers to oversee all aspects of drama production. Production managers can manage services, budgets, theater bookings, schedules, and coordinate with various stakeholders to ensure smooth execution of drama performances.

---

## Table of Contents

1. [Production Manager Role](#production-manager-role)
2. [Key Features](#key-features)
3. [Dashboard Overview](#dashboard-overview)
4. [Service Management](#service-management)
5. [Budget Management](#budget-management)
6. [Theater Booking Management](#theater-booking-management)
7. [Schedule Management](#schedule-management)
8. [Database Schema](#database-schema)
9. [CRUD Operations](#crud-operations)
10. [User Workflows](#user-workflows)
11. [Technical Implementation](#technical-implementation)

---

## Production Manager Role

A Production Manager is assigned by the Drama Director to handle the operational and logistical aspects of drama production. Their responsibilities include:

- **Service Coordination** - Request and manage services from external providers (sound, lighting, costumes, etc.)
- **Budget Management** - Track allocated budgets, spending, and financial status
- **Theater Operations** - Book theaters, manage venue logistics
- **Schedule Planning** - Coordinate rehearsals, service delivery, and performance schedules
- **Team Communication** - Coordinate with directors, artists, and service providers

---

## Key Features

### 1. **Comprehensive Dashboard**
- Real-time overview of all drama production metrics
- Budget tracking with visual progress indicators
- Service request status summary
- Theater booking overview
- Quick access to all management pages

### 2. **Service Management System**
- View all service requests for the drama
- Filter services by status (pending, accepted, completed, rejected)
- Count services by category
- Track service provider assignments
- Monitor service delivery status

### 3. **Budget Tracking & Management**
- Track budget allocations by category
- Monitor spending against allocated budgets
- Calculate remaining budgets
- View spending percentages
- Categorize expenses (venue, technical, costume, marketing, transport, other)
- Track payment status (pending, approved, paid, partial)

### 4. **Theater Booking System**
- Manage theater bookings for drama performances
- Track booking dates and times
- Monitor venue capacity
- Calculate total rental costs
- Track booking status (pending, confirmed, cancelled)
- Manage special requests for venues

### 5. **Schedule Management**
- Create and manage service schedules
- Track scheduled activities by date
- Assign services to team members
- Monitor schedule completion status
- View upcoming scheduled items
- Manage venue assignments for activities

---

## Dashboard Overview

### URL
```
/production_manager?drama_id={drama_id}
/production_manager/dashboard?drama_id={drama_id}
```

### Dashboard Metrics

#### Overview Cards
- **Total Budget Allocated** - Sum of all allocated budget amounts
- **Budget Used** - Total amount spent across all categories
- **Service Requests** - Total number of service requests
- **Theater Bookings** - Total number of theater bookings

#### Visual Analytics
- **Budget Overview** - Progress bar showing budget utilization percentage
- **Budget Status** - Color-coded indicator (green: under budget, yellow: at budget, red: over budget)
- **Service Status** - Count of services by status
- **Recent Activities** - Quick view of latest updates

#### Quick Actions
- **View All Services** - Navigate to manage_services page
- **Manage Budget** - Navigate to manage_budget page
- **Book Theater** - Navigate to book_theater page
- **View Schedule** - Navigate to manage_schedule page

---

## Service Management

### URL
```
/production_manager/manage_services?drama_id={drama_id}
```

### Features

#### Service Overview
- **Total Services** - Count of all service requests
- **Confirmed Services** - Count of accepted service requests
- **Pending Requests** - Count of pending service requests
- **Service List** - Dynamic list of all services

#### Service Display
Each service card shows:
- Service type (sound_system, lighting, costume, etc.)
- Service provider information
- Request date
- Required delivery date
- Budget range
- Description and special requirements
- Status badge (color-coded by status)
- Created by information

#### Status Types
- **Pending** - Yellow badge - Awaiting provider response
- **Accepted** - Green badge - Provider has accepted the request
- **Completed** - Blue badge - Service has been delivered
- **Rejected** - Red badge - Provider declined the request
- **Cancelled** - Grey badge - Request was cancelled

#### Empty State
When no services exist, displays:
- Informative message
- "Request Service" button to add new services

---

## Budget Management

### URL
```
/production_manager/manage_budget?drama_id={drama_id}
```

### Features

#### Budget Overview Statistics
- **Total Budget** - Sum of all allocated amounts (LKR format)
- **Total Spent** - Sum of all spent amounts
- **Remaining Budget** - Calculated as (Total Budget - Total Spent)
- **Spending Percentage** - Visual indicator of budget usage

#### Budget Categories
- **Venue** - Theater rental and venue-related costs
- **Technical** - Sound, lighting, and technical equipment
- **Costume** - Costume design, creation, and rental
- **Marketing** - Promotional and advertising expenses
- **Transport** - Transportation and logistics
- **Other** - Miscellaneous expenses

#### Budget Items Table
Each budget item displays:
- Item name
- Category (color-coded badge)
- Allocated amount (LKR)
- Spent amount (LKR)
- Remaining amount (calculated)
- Payment status
- Progress bar (visual spending indicator)

#### Payment Status Types
- **Pending** - Payment not yet made
- **Approved** - Payment approved but not disbursed
- **Paid** - Payment completed
- **Partial** - Partial payment made

#### Budget Summary by Category
Displays breakdown showing:
- Total allocated per category
- Total spent per category
- Percentage of total budget per category

#### Empty State
When no budget items exist, displays:
- Informative message
- "Add Budget Item" button

---

## Theater Booking Management

### URL
```
/production_manager/book_theater?drama_id={drama_id}
```

### Features

#### Booking Overview Statistics
- **Total Bookings** - Count of all theater bookings
- **Confirmed Bookings** - Count of confirmed bookings
- **Pending Bookings** - Count of pending bookings
- **Total Cost** - Sum of rental costs for confirmed bookings (LKR)

#### Theater Booking Cards
Each booking card displays:
- Theater name
- Booking date
- Start time and end time
- Venue capacity
- Rental cost (LKR)
- Booking status (color-coded badge)
- Special requests
- Booking reference number

#### Booking Status Types
- **Pending** - Yellow badge - Awaiting confirmation
- **Confirmed** - Green badge - Booking confirmed
- **Cancelled** - Red badge - Booking cancelled

#### Booking Details
- **Date & Time** - Full date with start and end times
- **Duration** - Calculated from start and end times
- **Capacity** - Venue seating/standing capacity
- **Cost Analysis** - Per-booking and total costs

#### Empty State
When no bookings exist, displays:
- Informative message
- "Book Theater" button to add new booking

---

## Schedule Management

### URL
```
/production_manager/manage_schedule?drama_id={drama_id}
```

### Features

#### Schedule Overview
- **Total Schedules** - Count of all scheduled items
- **Upcoming Schedules** - Count of items scheduled for future dates
- **Interactive Calendar** - Visual calendar display (if implemented)

#### Schedule Items
Each schedule item contains:
- Service name/activity
- Scheduled date
- Start time and end time
- Venue/location
- Assigned team member (optional)
- Status (scheduled, in_progress, completed, cancelled)
- Notes and special instructions

#### Schedule Status Types
- **Scheduled** - Planned activity
- **In Progress** - Currently happening
- **Completed** - Successfully finished
- **Cancelled** - Cancelled activity

#### Schedule Display
- **JavaScript Integration** - Data loaded via JSON for dynamic rendering
- **Date Filtering** - Filter schedules by date range
- **Status Filtering** - Filter by completion status
- **Upcoming View** - Highlight upcoming scheduled items

#### Empty State
When no schedules exist, displays:
- Informative message
- "Add Schedule" button

---

## Database Schema

### Tables Structure

#### 1. service_requests
```sql
id (Primary Key)
drama_id (Foreign Key → dramas.id)
service_provider_id (Foreign Key → users.id)
service_type (varchar)
status (enum: pending, accepted, completed, rejected, cancelled)
request_date (date)
required_date (date)
budget_range (varchar)
description (text)
special_requirements (text)
created_by (Foreign Key → users.id)
created_at (timestamp)
updated_at (timestamp)
```

**Indexes:**
- `idx_drama_id` on drama_id
- `idx_service_provider_id` on service_provider_id
- `idx_status` on status
- `idx_drama_status` on (drama_id, status)

#### 2. drama_budgets
```sql
id (Primary Key)
drama_id (Foreign Key → dramas.id)
item_name (varchar)
category (enum: venue, technical, costume, marketing, transport, other)
allocated_amount (decimal 12,2)
spent_amount (decimal 12,2)
status (enum: pending, approved, paid, partial)
notes (text)
created_by (Foreign Key → users.id)
created_at (timestamp)
updated_at (timestamp)
```

**Indexes:**
- `idx_drama_id` on drama_id
- `idx_category` on category
- `idx_status` on status
- `idx_drama_category` on (drama_id, category)

#### 3. theater_bookings
```sql
id (Primary Key)
drama_id (Foreign Key → dramas.id)
theater_name (varchar)
booking_date (date)
start_time (time)
end_time (time)
capacity (int)
rental_cost (decimal 10,2)
status (enum: pending, confirmed, cancelled)
special_requests (text)
booking_reference (varchar, unique)
created_by (Foreign Key → users.id)
created_at (timestamp)
updated_at (timestamp)
```

**Indexes:**
- `idx_drama_id` on drama_id
- `idx_booking_date` on booking_date
- `idx_status` on status
- `idx_drama_date` on (drama_id, booking_date)

#### 4. service_schedules
```sql
id (Primary Key)
drama_id (Foreign Key → dramas.id)
service_request_id (Foreign Key → service_requests.id, nullable)
service_name (varchar)
scheduled_date (date)
start_time (time)
end_time (time)
venue (varchar)
assigned_to (Foreign Key → users.id, nullable)
status (enum: scheduled, in_progress, completed, cancelled)
notes (text)
created_by (Foreign Key → users.id)
created_at (timestamp)
updated_at (timestamp)
```

**Indexes:**
- `idx_drama_id` on drama_id
- `idx_service_request_id` on service_request_id
- `idx_assigned_to` on assigned_to
- `idx_status` on status
- `idx_drama_date` on (drama_id, scheduled_date)

---

## CRUD Operations

### Service Requests (M_service_request.php)

#### Read Operations

**1. getServicesByDrama($drama_id)**
```php
// Get all service requests for a specific drama
$services = $serviceModel->getServicesByDrama($drama_id);
// Returns: Array of service request objects
```

**2. getServicesByStatus($drama_id, $status)**
```php
// Get services filtered by status
$acceptedServices = $serviceModel->getServicesByStatus($drama_id, 'accepted');
// Returns: Array of service requests with specified status
```

**3. countServicesByStatus($drama_id, $status)**
```php
// Count services by status
$pendingCount = $serviceModel->countServicesByStatus($drama_id, 'pending');
// Returns: Integer count
```

**4. getTotalCount($drama_id)**
```php
// Get total count of all services
$totalServices = $serviceModel->getTotalCount($drama_id);
// Returns: Integer count
```

**5. getRequestsByProvider($provider_id)**
```php
// Get all requests for a service provider (with JOIN)
$requests = $serviceModel->getRequestsByProvider($provider_id);
// Returns: Array with drama_name, requester_name, requester_email
```

**6. getRequestById($request_id)**
```php
// Get single service request with related data (with JOIN)
$request = $serviceModel->getRequestById($request_id);
// Returns: Object with drama_name, requester_name
```

#### Create Operations

**7. createRequest($data)**
```php
// Create new service request
$data = [
    'drama_id' => 1,
    'service_provider_id' => 5,
    'service_type' => 'sound_system',
    'status' => 'pending',
    'request_date' => '2026-01-20',
    'description' => 'Professional sound system',
    'created_by' => 2
];
$result = $serviceModel->createRequest($data);
// Returns: Boolean (true on success)
```

#### Update Operations

**8. updateRequestStatus($request_id, $status)**
```php
// Update service request status
$result = $serviceModel->updateRequestStatus(1, 'accepted');
// Returns: Boolean (true on success)
```

#### Delete Operations

**9. deleteRequest($request_id)**
```php
// Delete a service request
$result = $serviceModel->deleteRequest($request_id);
// Returns: Boolean (true on success)
```

---

### Budget Management (M_budget.php)

#### Read Operations

**1. getBudgetByDrama($drama_id)**
```php
// Get all budget items for a drama
$budgetItems = $budgetModel->getBudgetByDrama($drama_id);
// Returns: Array of budget item objects
```

**2. getBudgetById($budget_id)**
```php
// Get single budget item
$item = $budgetModel->getBudgetById($budget_id);
// Returns: Single budget object
```

**3. getBudgetByCategory($drama_id, $category)**
```php
// Get budget items filtered by category
$venueItems = $budgetModel->getBudgetByCategory($drama_id, 'venue');
// Returns: Array of budget items in specified category
```

**4. getBudgetByStatus($drama_id, $status)**
```php
// Get budget items filtered by payment status
$pendingItems = $budgetModel->getBudgetByStatus($drama_id, 'pending');
// Returns: Array of budget items with specified status
```

**5. getTotalBudget($drama_id)**
```php
// Get sum of all allocated amounts
$total = $budgetModel->getTotalBudget($drama_id);
// Returns: Float (total allocated amount)
```

**6. getTotalSpent($drama_id)**
```php
// Get sum of all spent amounts
$spent = $budgetModel->getTotalSpent($drama_id);
// Returns: Float (total spent amount)
```

**7. getRemainingBudget($drama_id)**
```php
// Get remaining budget (allocated - spent)
$remaining = $budgetModel->getRemainingBudget($drama_id);
// Returns: Float (remaining amount)
```

**8. getSpendingPercentage($drama_id)**
```php
// Get percentage of budget spent
$percentage = $budgetModel->getSpendingPercentage($drama_id);
// Returns: Float (percentage)
```

**9. getBudgetSummaryByCategory($drama_id)**
```php
// Get budget breakdown by category
$summary = $budgetModel->getBudgetSummaryByCategory($drama_id);
// Returns: Array grouped by category with totals
```

**10. getBudgetItemCount($drama_id)**
```php
// Get count of budget items
$count = $budgetModel->getBudgetItemCount($drama_id);
// Returns: Integer count
```

#### Create Operations

**11. createBudgetItem($data)**
```php
// Create new budget item
$data = [
    'drama_id' => 1,
    'item_name' => 'Theater Rental',
    'category' => 'venue',
    'allocated_amount' => 250000.00,
    'spent_amount' => 0.00,
    'status' => 'pending',
    'created_by' => 2
];
$result = $budgetModel->createBudgetItem($data);
// Returns: Boolean (true on success)
```

#### Update Operations

**12. updateBudgetItem($budget_id, $data)**
```php
// Update budget item
$data = [
    'spent_amount' => 150000.00,
    'status' => 'partial'
];
$result = $budgetModel->updateBudgetItem($budget_id, $data);
// Returns: Boolean (true on success)
```

**13. updateSpentAmount($budget_id, $amount)**
```php
// Update only the spent amount
$result = $budgetModel->updateSpentAmount($budget_id, 100000.00);
// Returns: Boolean (true on success)
```

#### Delete Operations

**14. deleteBudgetItem($budget_id)**
```php
// Delete a budget item
$result = $budgetModel->deleteBudgetItem($budget_id);
// Returns: Boolean (true on success)
```

---

### Theater Bookings (M_theater_booking.php)

#### Read Operations

**1. getBookingsByDrama($drama_id)**
```php
// Get all theater bookings for a drama
$bookings = $theaterModel->getBookingsByDrama($drama_id);
// Returns: Array of booking objects
```

**2. getBookingById($booking_id)**
```php
// Get single theater booking
$booking = $theaterModel->getBookingById($booking_id);
// Returns: Single booking object
```

**3. getBookingsByStatus($drama_id, $status)**
```php
// Get bookings filtered by status
$confirmed = $theaterModel->getBookingsByStatus($drama_id, 'confirmed');
// Returns: Array of bookings with specified status
```

**4. getConfirmedBookings($drama_id)**
```php
// Get only confirmed bookings
$confirmed = $theaterModel->getConfirmedBookings($drama_id);
// Returns: Array of confirmed bookings
```

**5. getTotalCost($drama_id)**
```php
// Get sum of rental costs for confirmed bookings
$totalCost = $theaterModel->getTotalCost($drama_id);
// Returns: Float (total rental cost)
```

**6. getBookingCount($drama_id)**
```php
// Get count of all bookings
$count = $theaterModel->getBookingCount($drama_id);
// Returns: Integer count
```

**7. getUpcomingBookings($drama_id)**
```php
// Get bookings scheduled for future dates
$upcoming = $theaterModel->getUpcomingBookings($drama_id);
// Returns: Array of future bookings
```

#### Create Operations

**8. createBooking($data)**
```php
// Create new theater booking
$data = [
    'drama_id' => 1,
    'theater_name' => 'Elphinstone Theatre',
    'booking_date' => '2026-02-15',
    'start_time' => '18:00:00',
    'end_time' => '22:00:00',
    'capacity' => 500,
    'rental_cost' => 250000.00,
    'status' => 'pending',
    'created_by' => 2
];
$result = $theaterModel->createBooking($data);
// Returns: Boolean (true on success)
```

#### Update Operations

**9. updateBooking($booking_id, $data)**
```php
// Update theater booking
$data = [
    'status' => 'confirmed',
    'booking_reference' => 'BK-2026-001'
];
$result = $theaterModel->updateBooking($booking_id, $data);
// Returns: Boolean (true on success)
```

**10. updateStatus($booking_id, $status)**
```php
// Update only booking status
$result = $theaterModel->updateStatus($booking_id, 'confirmed');
// Returns: Boolean (true on success)
```

#### Delete Operations

**11. deleteBooking($booking_id)**
```php
// Delete a theater booking
$result = $theaterModel->deleteBooking($booking_id);
// Returns: Boolean (true on success)
```

---

### Service Schedules (M_service_schedule.php)

#### Read Operations

**1. getSchedulesByDrama($drama_id)**
```php
// Get all schedules for a drama
$schedules = $scheduleModel->getSchedulesByDrama($drama_id);
// Returns: Array of schedule objects
```

**2. getScheduleById($schedule_id)**
```php
// Get single schedule item
$schedule = $scheduleModel->getScheduleById($schedule_id);
// Returns: Single schedule object
```

**3. getSchedulesByStatus($drama_id, $status)**
```php
// Get schedules filtered by status
$scheduled = $scheduleModel->getSchedulesByStatus($drama_id, 'scheduled');
// Returns: Array of schedules with specified status
```

**4. getSchedulesByDateRange($drama_id, $start_date, $end_date)**
```php
// Get schedules within date range
$schedules = $scheduleModel->getSchedulesByDateRange($drama_id, '2026-02-01', '2026-02-28');
// Returns: Array of schedules in date range
```

**5. getUpcomingSchedules($drama_id)**
```php
// Get schedules for future dates
$upcoming = $scheduleModel->getUpcomingSchedules($drama_id);
// Returns: Array of future schedules
```

**6. getScheduleCount($drama_id)**
```php
// Get count of all schedules
$count = $scheduleModel->getScheduleCount($drama_id);
// Returns: Integer count
```

**7. getSchedulesByAssignee($drama_id, $user_id)**
```php
// Get schedules assigned to specific user
$mySchedules = $scheduleModel->getSchedulesByAssignee($drama_id, $user_id);
// Returns: Array of assigned schedules
```

#### Create Operations

**8. createSchedule($data)**
```php
// Create new schedule item
$data = [
    'drama_id' => 1,
    'service_name' => 'Sound System Setup',
    'scheduled_date' => '2026-02-14',
    'start_time' => '10:00:00',
    'end_time' => '14:00:00',
    'venue' => 'Elphinstone Theatre',
    'status' => 'scheduled',
    'created_by' => 2
];
$result = $scheduleModel->createSchedule($data);
// Returns: Boolean (true on success)
```

#### Update Operations

**9. updateSchedule($schedule_id, $data)**
```php
// Update schedule item
$data = [
    'status' => 'completed',
    'notes' => 'Setup completed successfully'
];
$result = $scheduleModel->updateSchedule($schedule_id, $data);
// Returns: Boolean (true on success)
```

**10. updateStatus($schedule_id, $status)**
```php
// Update only schedule status
$result = $scheduleModel->updateStatus($schedule_id, 'in_progress');
// Returns: Boolean (true on success)
```

#### Delete Operations

**11. deleteSchedule($schedule_id)**
```php
// Delete a schedule item
$result = $scheduleModel->deleteSchedule($schedule_id);
// Returns: Boolean (true on success)
```

---

## User Workflows

### For Production Managers

#### Initial Setup (Done by Director)
1. Director creates drama
2. Director assigns artist as Production Manager via roles
3. PM receives access to production management features

#### Daily Operations

**Morning Routine:**
1. Access dashboard to view overall status
2. Check pending service requests
3. Review budget spending
4. Verify theater bookings
5. Check today's schedule

**Service Management:**
1. Navigate to Manage Services page
2. Review pending service requests
3. Contact service providers if needed
4. Track service delivery status
5. Mark services as completed

**Budget Monitoring:**
1. Navigate to Manage Budget page
2. Review allocated budgets by category
3. Check spending against allocations
4. Monitor payment status
5. Calculate remaining budgets
6. Review category summaries

**Theater Coordination:**
1. Navigate to Book Theater page
2. Review confirmed bookings
3. Check booking dates and times
4. Calculate total rental costs
5. Coordinate with venue managers
6. Update booking status

**Schedule Planning:**
1. Navigate to Manage Schedule page
2. View all scheduled activities
3. Check upcoming items
4. Assign team members to tasks
5. Update completion status
6. Adjust schedules as needed

#### Weekly Reviews
1. Generate budget reports
2. Review service completion rates
3. Check theater booking timeline
4. Plan upcoming week's schedule
5. Coordinate with director on progress

---

## Technical Implementation

### File Structure

```
app/
  controllers/
    Production_manager.php            # Main PM controller
  
  models/
    M_service_request.php             # Service requests model (9 methods)
    M_budget.php                      # Budget management model (14 methods)
    M_theater_booking.php             # Theater bookings model (11 methods)
    M_service_schedule.php            # Schedule management model (11 methods)
    M_drama.php                       # Drama model (for authorization)
  
  views/
    production_manager/
      dashboard.php                   # PM dashboard
      manage_services.php             # Service management page
      manage_budget.php               # Budget management page
      book_theater.php                # Theater booking page
      manage_schedule.php             # Schedule management page
```

### Controller Methods (Production_manager.php)

#### 1. dashboard()
```php
// Display PM dashboard with overview metrics
// Authorization: Checks if user is PM for the drama
// Data: Services, bookings, schedules, budget stats
```

#### 2. manage_services()
```php
// Service management page
// Fetches all services, counts by status
// Displays service list with filtering
```

#### 3. manage_budget()
```php
// Budget management page
// Fetches budget items, calculates totals
// Shows spending analysis and category breakdown
```

#### 4. book_theater()
```php
// Theater booking management page
// Fetches bookings, calculates costs
// Displays booking cards with status
```

#### 5. manage_schedule()
```php
// Schedule management page
// Fetches schedules, counts upcoming items
// Displays schedule with JavaScript integration
```

#### 6. authorizeDrama()
```php
// Protected method - verifies user is PM for drama
// Redirects if not authorized
// Returns drama object if authorized
```

### Database Relationships

```
dramas (1) ─────── (many) service_requests
       (1) ─────── (many) drama_budgets
       (1) ─────── (many) theater_bookings
       (1) ─────── (many) service_schedules

service_requests (1) ─── (many) service_schedules (optional link)

users (1) ─────── (many) service_requests (as service_provider_id)
      (1) ─────── (many) service_requests (as created_by)
      (1) ─────── (many) drama_budgets (as created_by)
      (1) ─────── (many) theater_bookings (as created_by)
      (1) ─────── (many) service_schedules (as assigned_to)
      (1) ─────── (many) service_schedules (as created_by)
```

### Key Technologies

- **Backend**: PHP (MVC architecture)
- **Database**: MySQL with InnoDB engine
- **Security**: Prepared statements, output escaping, authorization
- **Frontend**: HTML5, CSS3, JavaScript
- **Data Flow**: Controller → Model → Database → View
- **Session Management**: PHP sessions for authentication

### Security Features

- **Authorization** - `authorizeDrama()` checks PM role before all operations
- **SQL Injection Prevention** - Prepared statements with parameter binding
- **XSS Protection** - Output escaping with `esc()` function
- **CSRF Protection** - Session-based verification (recommended to add tokens)
- **Data Validation** - Type checking, null coalescing, default values
- **Foreign Key Constraints** - Referential integrity at database level
- **Cascading Deletes** - Automatic cleanup of related records

### Performance Optimizations

- **Database Indexes** - Strategic indexes on frequently queried columns
- **Single Query Loading** - One main query per page load
- **Efficient JOINs** - Used only when displaying related data
- **Result Caching** - Variables stored to avoid repeated queries
- **Conditional Loading** - Models loaded only when needed

---

## Model Method Summary

### Total Methods Across All Models: 45

| Model | Methods | CRUD Operations |
|-------|---------|-----------------|
| M_service_request.php | 9 | Read (6), Create (1), Update (1), Delete (1) |
| M_budget.php | 14 | Read (10), Create (1), Update (2), Delete (1) |
| M_theater_booking.php | 11 | Read (7), Create (1), Update (2), Delete (1) |
| M_service_schedule.php | 11 | Read (7), Create (1), Update (2), Delete (1) |

### Method Categories

**Read Operations:** 30 methods
- Basic queries (getBy*: 12 methods)
- Filtered queries (getByStatus, getByCategory: 8 methods)
- Calculations (getTotalCost, getTotalBudget, getSpendingPercentage: 6 methods)
- Counts (getCount, countBy*: 4 methods)

**Create Operations:** 4 methods
- createRequest, createBudgetItem, createBooking, createSchedule

**Update Operations:** 7 methods
- updateRequestStatus, updateBudgetItem, updateSpentAmount
- updateBooking, updateStatus (2x), updateSchedule

**Delete Operations:** 4 methods
- deleteRequest, deleteBudgetItem, deleteBooking, deleteSchedule

---

## Migration Files

### Required SQL Migrations

**File: PM_COMPLETE_MIGRATION.sql**
- Creates all 4 PM system tables
- Sets up foreign key constraints
- Creates performance indexes
- Includes sample data (commented)

### Migration Command

```sql
-- Run in PhpMyAdmin or MySQL CLI
SOURCE PM_COMPLETE_MIGRATION.sql;
```

---

## Future Enhancements

Potential features for future development:

- [ ] **Service Request Forms** - UI to create/edit service requests
- [ ] **Budget Item Forms** - UI to add/edit budget items
- [ ] **Theater Booking Forms** - UI to book/edit theaters
- [ ] **Schedule Forms** - UI to create/edit schedules
- [ ] **Real-time Notifications** - Alert PM of service updates
- [ ] **Email Notifications** - Service confirmations, reminders
- [ ] **Report Generation** - PDF/Excel exports of budgets and schedules
- [ ] **File Attachments** - Upload contracts, invoices
- [ ] **Advanced Charts** - Budget trends, spending analysis
- [ ] **Mobile Responsive** - Optimize for mobile devices
- [ ] **Calendar Integration** - Sync with external calendars
- [ ] **Multi-language Support** - Sinhala/Tamil translations
- [ ] **Payment Gateway Integration** - Online payment processing
- [ ] **Automated Reminders** - Schedule and deadline notifications
- [ ] **Collaborative Features** - Comments, notes, team chat

---

## Support & Documentation

For additional information:
- **Main Documentation**: [README.md](README.md)
- **Database Setup**: [database_setup.sql](database_setup.sql)
- **Migration Guide**: [QUICK_MIGRATION_GUIDE.md](QUICK_MIGRATION_GUIDE.md)
- **Testing Guide**: [PM_TESTING_GUIDE.md](PM_TESTING_GUIDE.md)
- **Compliance Check**: [PM_COMPLIANCE_WITH_SERVICE_PROVIDER_PATTERNS.md](PM_COMPLIANCE_WITH_SERVICE_PROVIDER_PATTERNS.md)
- **Bug Fixes**: [PM_BUG_FIX_SCHEMA_MISMATCH.md](PM_BUG_FIX_SCHEMA_MISMATCH.md)

---

## License

Part of the Rangamadala platform. All rights reserved.

---

**Last Updated**: January 23, 2026  
**Version**: 1.0.0  
**Total Lines of Code**: 1,754+  
**Total Database Tables**: 4  
**Total Model Methods**: 45  
**Total CRUD Operations**: 45  
**Maintained By**: Rangamadala Development Team

---

## Quick Reference

### URL Routes
```
/production_manager                              → Dashboard
/production_manager/dashboard?drama_id={id}      → Dashboard
/production_manager/manage_services?drama_id={id} → Service Management
/production_manager/manage_budget?drama_id={id}   → Budget Management
/production_manager/book_theater?drama_id={id}    → Theater Bookings
/production_manager/manage_schedule?drama_id={id} → Schedule Management
```

### Database Tables
```
service_requests    → Service provider requests
drama_budgets       → Budget tracking
theater_bookings    → Theater/venue bookings
service_schedules   → Activity schedules
```

### Model Files
```
M_service_request.php   → 9 methods (Service CRUD)
M_budget.php            → 14 methods (Budget CRUD)
M_theater_booking.php   → 11 methods (Booking CRUD)
M_service_schedule.php  → 11 methods (Schedule CRUD)
```

### Status Enums
```
service_requests.status → pending, accepted, completed, rejected, cancelled
drama_budgets.status    → pending, approved, paid, partial
theater_bookings.status → pending, confirmed, cancelled
service_schedules.status → scheduled, in_progress, completed, cancelled
```

### Budget Categories
```
venue, technical, costume, marketing, transport, other
```

---

**End of Documentation**
