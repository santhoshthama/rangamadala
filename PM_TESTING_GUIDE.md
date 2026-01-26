# Production Manager Database Integration - Quick Testing Guide

## Overview
All Production Manager UI pages have been converted from static hardcoded data to dynamic database-driven content. This guide shows how to test each page.

## Setup Steps

### 1. Create Database Tables
Run the SQL migration file to create the three new tables:
```bash
# In MySQL client
mysql -u root -p rangamadala_db < pm_system_tables.sql
```

Or execute in PhpMyAdmin:
```sql
-- Copy and paste contents of pm_system_tables.sql
```

### 2. Add Test Data

Insert sample budget items:
```sql
-- For drama_id = 1 (Sinhabahu) and user_id = 1 (director/PM)
INSERT INTO drama_budgets (drama_id, item_name, category, allocated_amount, spent_amount, status, created_by) VALUES
(1, 'Elphinstone Theatre Rental', 'venue', 300000, 300000, 'paid', 1),
(1, 'Sound System Setup', 'technical', 120000, 85000, 'pending', 1),
(1, 'Professional Lighting', 'technical', 80000, 80000, 'paid', 1),
(1, 'Makeup & Costume Design', 'costume', 160000, 0, 'pending', 1);
```

Insert sample theater bookings:
```sql
INSERT INTO theater_bookings (drama_id, theater_name, booking_date, start_time, end_time, capacity, rental_cost, status, created_by) VALUES
(1, 'Elphinstone Theatre', '2025-02-15', '19:00:00', '22:00:00', 500, 300000, 'confirmed', 1),
(1, 'Colombo Auditorium', '2025-02-22', '18:30:00', '21:30:00', 1000, 400000, 'confirmed', 1),
(1, 'Galle Face Hotel Theatre', '2025-03-01', '19:30:00', '22:30:00', 300, 500000, 'pending', 1);
```

Insert sample service schedules:
```sql
INSERT INTO service_schedules (drama_id, service_name, scheduled_date, start_time, end_time, venue, status, created_by) VALUES
(1, 'Sound System Testing', '2025-02-10', '10:00:00', '12:00:00', 'Elphinstone Theatre', 'scheduled', 1),
(1, 'Lighting Design Consultation', '2025-02-12', '14:00:00', '16:00:00', 'Studio', 'scheduled', 1),
(1, 'Makeup & Costume Fitting', '2025-02-13', '09:00:00', '17:00:00', 'Rehearsal Space', 'scheduled', 1);
```

### 3. Test Each Page

#### Test: Manage Services
**URL**: `/production_manager/manage_services?drama_id=1`

**Expected Results**:
- Header shows "Sinhabahu"
- Stats show:
  - Total Services Requested: (count from service_requests table)
  - Confirmed Services: (count where status='accepted')
  - Pending Responses: (count where status='pending')
- Service list shows dynamic cards from database
- Empty state if no services exist

**Data Used**: 
- Table: `service_requests`
- Method: `M_service_request->getServicesByDrama(1)`

---

#### Test: Manage Budget
**URL**: `/production_manager/manage_budget?drama_id=1`

**Expected Results**:
- Header shows "Sinhabahu"
- Stats show:
  - Total Allocated: LKR 660,000 (sum of allocated_amount)
  - Total Spent: LKR 465,000 with 70% (sum of spent_amount)
  - Remaining Balance: LKR 195,000
  - Total Budget Items: 4
- Budget items table shows 4 rows:
  1. Elphinstone Theatre Rental | venue | LKR 300,000 | LKR 300,000 | Paid
  2. Sound System Setup | technical | LKR 120,000 | LKR 85,000 | Pending
  3. Professional Lighting | technical | LKR 80,000 | LKR 80,000 | Paid
  4. Makeup & Costume Design | costume | LKR 160,000 | LKR 0 | Pending
- Empty state shown if no items

**Data Used**:
- Table: `drama_budgets`
- Methods: 
  - `M_budget->getBudgetByDrama(1)` - Get all items
  - `M_budget->getTotalBudget(1)` - Sum allocated
  - `M_budget->getTotalSpent(1)` - Sum spent
  - `M_budget->getRemainingBudget(1)` - Calculate remaining

---

#### Test: Book Theater
**URL**: `/production_manager/book_theater?drama_id=1`

**Expected Results**:
- Header shows "Sinhabahu"
- Stats show:
  - Total Bookings: 3
  - Confirmed: 2
  - Pending: 1
  - Total Theater Cost: LKR 700,000 (confirmed bookings only)
- Theater bookings list shows 3 cards:
  1. Elphinstone Theatre | Feb 15, 2025 | 7:00 PM - 10:00 PM | 500 Seats | LKR 300,000 | ✓ Confirmed
  2. Colombo Auditorium | Feb 22, 2025 | 6:30 PM - 9:30 PM | 1000 Seats | LKR 400,000 | ✓ Confirmed
  3. Galle Face Hotel Theatre | Mar 1, 2025 | 7:30 PM - 10:30 PM | 300 Seats | LKR 500,000 | ⏳ Pending
- Empty state if no bookings

**Data Used**:
- Table: `theater_bookings`
- Methods:
  - `M_theater_booking->getBookingsByDrama(1)` - Get all bookings
  - `M_theater_booking->getTotalCost(1)` - Sum confirmed costs

---

#### Test: Service Schedule
**URL**: `/production_manager/manage_schedule?drama_id=1`

**Expected Results**:
- Header shows "Sinhabahu"
- JavaScript initializes with 3 schedules:
  ```javascript
  scheduleData = [
    {id: 1, service_name: "Sound System Testing", ...},
    {id: 2, service_name: "Lighting Design Consultation", ...},
    {id: 3, service_name: "Makeup & Costume Fitting", ...}
  ]
  ```
- Console logs show: "Loaded 3 schedules for drama 1"
- Calendar and timeline views ready to display data

**Data Used**:
- Table: `service_schedules`
- Method: `M_service_schedule->getSchedulesByDrama(1)`

---

## Verify Installation

### Check Models Exist
```bash
ls -la app/models/M_*
# Should show: M_theater_booking.php, M_service_schedule.php, M_budget.php
```

### Check Views Updated
```bash
grep -l "isset(\$budgetItems)" app/views/production_manager/*.php
# Should show manage_budget.php and dashboard.php

grep -l "isset(\$theaterBookings)" app/views/production_manager/*.php
# Should show book_theater.php and dashboard.php
```

### Check Controller Updated
```bash
grep -n "M_budget\|M_theater_booking\|M_service_schedule" app/controllers/Production_manager.php
# Should show multiple matches in manage_budget, book_theater, manage_schedule methods
```

---

## Troubleshooting

### Problem: "Unknown database table 'drama_budgets'"
**Solution**: Run the SQL migration file to create tables
```sql
SOURCE pm_system_tables.sql;
```

### Problem: "Call to undefined method getServicesByDrama()"
**Solution**: Check that M_service_request model exists and has the method
```bash
grep -n "getServicesByDrama" app/models/M_service_request.php
```

### Problem: "Undefined variable '$budgetItems' in manage_budget.php"
**Solution**: Controller method may not have been updated. Check:
```php
// In Production_manager.php manage_budget() method
$budgetItems = $budgetModel ? $budgetModel->getBudgetByDrama($drama->id) : [];
```

### Problem: Empty state showing even with test data
**Solution**: Verify:
1. drama_id in URL matches inserted test data
2. created_by user is logged in
3. Data is actually in database:
   ```sql
   SELECT * FROM drama_budgets WHERE drama_id = 1;
   ```

---

## Verify Data Flow

### From Browser Console:
```javascript
// Check if drama ID is correct
console.log('Drama ID:', dramaId);

// Check if schedule data loaded (on manage_schedule page)
console.log('Schedules:', schedules);

// Verify JSON parsing worked
console.log('Schedule count:', schedules.length);
```

### From Server Logs:
```bash
tail -f /var/www/html/error.log
# Should show NO PHP errors related to undefined methods/variables
```

### From Database Direct Query:
```sql
-- Verify test data exists
SELECT COUNT(*) FROM drama_budgets WHERE drama_id = 1;
SELECT COUNT(*) FROM theater_bookings WHERE drama_id = 1;
SELECT COUNT(*) FROM service_schedules WHERE drama_id = 1;
```

---

## Expected Console Output (manage_schedule page)

```
Loaded 3 schedules for drama 1
Schedules: (3) [{…}, {…}, {…}]
  0: {id: 1, serviceName: "Sound System Testing", scheduledDate: "2025-02-10", ...}
  1: {id: 2, serviceName: "Lighting Design Consultation", scheduledDate: "2025-02-12", ...}
  2: {id: 3, serviceName: "Makeup & Costume Fitting", scheduledDate: "2025-02-13", ...}
```

---

## Next Steps After Verification

1. **Test Form Submissions**: Add button handlers for:
   - Add/Edit budget items
   - Create theater bookings
   - Schedule services

2. **Implement Modals**: Wire up modal forms to save data to database

3. **Add Pagination**: If many records, implement pagination

4. **Add Sorting/Filtering**: Sort by date, category, status

5. **Real-time Updates**: Use AJAX to refresh without page reload

---

**Status**: Ready for testing
**Database Tables**: Created ✅
**Models**: Created ✅
**Controllers**: Updated ✅
**Views**: Updated ✅
**Test Data Script**: See above ✅

For questions or issues, check [PRODUCTION_MANAGER_DATABASE_INTEGRATION.md](PRODUCTION_MANAGER_DATABASE_INTEGRATION.md)
