# Production Manager Dashboard - Database Integration Updates

## Overview
All static/hardcoded data has been removed from the Production Manager UI pages. Data is now dynamically fetched from the database.

## Changes Made

### 1. Controller Updates - `Production_manager.php`

**dashboard() method:**
- Now fetches real data from models
- Returns services, theater bookings, schedules, and budget information
- Data is passed to the view for rendering

```php
public function dashboard()
{
    $this->renderDramaView('dashboard', [], function ($drama) {
        $serviceModel = $this->getModel('M_service_request');
        $allServices = $serviceModel ? $serviceModel->getServicesByDrama($drama->id) : [];
        // Additional data fetching...
    });
}
```

### 2. View Updates - `production_manager/dashboard.php`

**Removed Static Data:**
- Drama name: Now uses `$drama->drama_name`
- Budget stats: Now uses `$totalBudget` and `$budgetUsed`
- Service requests list: Now iterates through `$services` array
- Theater bookings: Now iterates through `$theaterBookings` array
- Service schedule: Now iterates through `$schedules` array

**Added Conditional Rendering:**
- Empty state messages when no data exists
- Dynamic progress bars for budget utilization
- Status-based styling for different states

### 3. Model Updates - `M_service_request.php`

**New Method:**
```php
public function getServicesByDrama($drama_id)
{
    // Fetches all service requests for a specific drama
    // Returns array of service records from database
}
```

## Data Structure

### Services Array
```php
[
    'service_required' => 'Service name',
    'requester_name' => 'Who requested',
    'created_at' => 'Request date',
    'status' => 'pending|confirmed|cancelled',
    // ... other fields
]
```

### Theater Bookings Array
```php
[
    'theater_name' => 'Theater name',
    'booking_date' => 'Date of booking',
    'start_time' => 'Start time',
    'end_time' => 'End time',
    'cost' => 'Booking cost',
    'status' => 'confirmed|pending|inquiry'
]
```

### Service Schedule Array
```php
[
    'service_name' => 'Service name',
    'scheduled_date' => 'Date',
    'scheduled_time' => 'Time',
    'venue' => 'Location',
    'status' => 'scheduled|pending'
]
```

## Future Implementation Tasks

The following features need to be implemented in models to fully populate the dashboard:

### 1. Theater Bookings (M_theater_booking.php - needs creation)
- `getBookingsByDrama($drama_id)` - Get all theater bookings for a drama
- `createBooking($data)` - Create new theater booking
- `updateBookingStatus($booking_id, $status)` - Update booking status

### 2. Service Schedule (M_service_schedule.php - needs creation)
- `getSchedulesByDrama($drama_id)` - Get all schedules for a drama
- `createSchedule($data)` - Create new schedule
- `updateSchedule($schedule_id, $data)` - Update schedule

### 3. Budget Management (M_budget.php - needs creation)
- `getBudgetByDrama($drama_id)` - Get drama's budget
- `getTotalBudget($drama_id)` - Calculate total allocated budget
- `getBudgetUsed($drama_id)` - Calculate total spent
- `createBudget($data)` - Create new budget entry
- `updateBudget($budget_id, $data)` - Update budget

### 4. Service Providers Integration (M_service_provider.php)
- Already exists but may need enhancement for PM-specific queries
- `getServicesByDrama($drama_id)` - Get services for a drama

## Database Tables Required

The following tables should exist (verify with `addpm.sql`):

1. **service_requests**
   - service_required, requester_name, created_at, status, drama_name

2. **theater_bookings** (to be created)
   - theater_name, drama_id, booking_date, start_time, end_time, cost, status

3. **service_schedules** (to be created)
   - service_name, drama_id, scheduled_date, scheduled_time, venue, status

4. **drama_budgets** (to be created)
   - drama_id, total_budget, category, allocated_amount, spent_amount

## Testing Checklist

- [ ] Verify Production Manager can access dashboard without error
- [ ] Confirm database queries return data correctly
- [ ] Check empty states display when no data exists
- [ ] Verify budget percentage calculation is accurate
- [ ] Test service request filtering and display
- [ ] Validate theater booking list displays correctly
- [ ] Check service schedule rendering

## Notes

- All static HTML data has been replaced with PHP conditionals
- Empty state messages guide users when no data is available
- Data is formatted consistently across all sections
- Status badges use dynamic CSS classes based on actual status values
- All numbers are formatted with `number_format()` for currency display
