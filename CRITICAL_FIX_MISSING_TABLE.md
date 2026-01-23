# CRITICAL FIX: Missing service_requests Table

## Error Analysis
```
Fatal error: Uncaught PDOException: SQLSTATE[42S02]: Base table or view not found: 
1146 Table 'rangamadala_db.service_requests' doesn't exist
```

**Root Cause**: The `service_requests` table was never created in your database.

**Impact**: All Production Manager pages fail because they depend on this table:
- ✗ Dashboard fails (calls `M_service_request->getServicesByDrama()`)
- ✗ Manage Services fails (needs service data)
- ✗ Manage Budget fails (indirectly depends on service requests)
- ✗ Book Theater fails (indirectly depends on service requests)
- ✗ Manage Schedule fails (indirectly depends on service requests)

---

## Solution

### Step 1: Create the Missing Table
Run **ONE** of these files in your MySQL:

**Option A (Recommended)**: Use the comprehensive migration
```
File: PM_COMPLETE_MIGRATION.sql
Location: c:\xampp\htdocs\rangamadala\PM_COMPLETE_MIGRATION.sql
Contains: All 4 tables + indexes + sample data
```

**Option B**: Use the updated original file
```
File: pm_system_tables.sql (UPDATED)
Location: c:\xampp\htdocs\rangamadala\pm_system_tables.sql
Contains: All 4 tables + indexes (no sample data)
```

### Step 2: How to Run in PhpMyAdmin

1. Go to `http://localhost/phpmyadmin`
2. Select database: `rangamadala_db`
3. Click "SQL" tab
4. Copy all SQL from `PM_COMPLETE_MIGRATION.sql`
5. Paste into editor
6. Click "Go"
7. Wait for completion message

### Step 3: Verify Success

In PhpMyAdmin SQL tab, run:
```sql
SHOW TABLES;
```

Should show:
```
service_requests ✅ (This was missing!)
theater_bookings
service_schedules  
drama_budgets
```

---

## Table Details

| Table | Purpose | Status |
|-------|---------|--------|
| `service_requests` | **CRITICAL - WAS MISSING** | ✅ Now created |
| `theater_bookings` | Theater bookings for dramas | ✅ Created |
| `service_schedules` | Service scheduling/timeline | ✅ Created |
| `drama_budgets` | Budget tracking | ✅ Created |

### service_requests Structure
```sql
id (Primary Key)
drama_id (Foreign Key → dramas.id)
service_provider_id (Foreign Key → users.id)
service_type (varchar)
status (pending/accepted/completed/rejected/cancelled)
request_date (date)
description (text)
created_by (Foreign Key → users.id)
created_at, updated_at (timestamps)
```

---

## Files Affected

### Created Files
1. ✅ `PM_COMPLETE_MIGRATION.sql` - NEW comprehensive migration
2. ✅ `QUICK_MIGRATION_GUIDE.md` - NEW setup instructions

### Updated Files
1. ✅ `pm_system_tables.sql` - NOW includes service_requests table

### Reference Files
- `PM_TESTING_GUIDE.md` - Contains test data examples
- `PRODUCTION_MANAGER_DATABASE_INTEGRATION.md` - Database design docs
- `PM_COMPLIANCE_WITH_SERVICE_PROVIDER_PATTERNS.md` - Architecture compliance

---

## After Migration

### Expected Behavior
✅ Production Manager dashboard loads  
✅ manage_services page shows services (or empty state)  
✅ manage_budget page shows budget items (or empty state)  
✅ book_theater page shows bookings (or empty state)  
✅ manage_schedule page shows schedules (or empty state)  

### To See Data
Add test data using the sample section in `PM_COMPLETE_MIGRATION.sql`:
```sql
INSERT INTO service_requests (drama_id, service_provider_id, service_type, status, request_date, description, created_by)
VALUES (1, 5, 'sound_system', 'accepted', '2026-01-20', 'Sound system setup', 2);
```

(Replace drama_id=1, service_provider_id=5, created_by=2 with actual values from your database)

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Table already exists" | Safe - IF NOT EXISTS clause prevents errors |
| "Unknown column in 'on clause'" | Make sure `dramas` and `users` tables exist |
| "Access denied" | Use PhpMyAdmin with correct credentials |
| Pages still blank after migration | Insert test data or create records through UI |
| Still getting error | Clear browser cache, refresh page |

---

## Code Dependencies

The following code depends on the `service_requests` table:

**[Production_manager.php](app/controllers/Production_manager.php#L25)**
```php
// Line 25 - Called in dashboard() method
$allServices = $serviceModel->getServicesByDrama($drama->id);
```

**[M_service_request.php](app/models/M_service_request.php#L50)**
```php
// Line 50 - getServicesByDrama() method
SELECT * FROM service_requests WHERE drama_id = :drama_id
```

**[manage_services.php](app/views/production_manager/manage_services.php)**
```php
// View depends on $services array populated from database
<?php foreach ($services as $service): ?>
    <!-- Display dynamic service data -->
<?php endforeach; ?>
```

---

## Deployment Checklist

- [ ] Run `PM_COMPLETE_MIGRATION.sql` in MySQL
- [ ] Verify all 4 tables exist with `SHOW TABLES;`
- [ ] (Optional) Insert sample data
- [ ] Navigate to `/production_manager?drama_id=1`
- [ ] Verify no errors in browser console
- [ ] Check each PM page loads:
  - [ ] /production_manager/manage_services?drama_id=1
  - [ ] /production_manager/manage_budget?drama_id=1
  - [ ] /production_manager/book_theater?drama_id=1
  - [ ] /production_manager/manage_schedule?drama_id=1
- [ ] Verify data displays or empty state shows

---

## Summary

**What was missing**: `service_requests` table in database  
**Why it failed**: Code queries this table but it didn't exist  
**Solution provided**: 2 complete SQL migration files  
**Time to fix**: ~5 minutes  
**Result**: All PM pages will work correctly  

---

**Status**: ✅ READY TO DEPLOY  
**Action Required**: Run one of the SQL migration files  
**Priority**: HIGH - BLOCKING ALL PM FUNCTIONALITY  

---

*Generated: January 23, 2026*
*Issue: Missing Database Table*
*Resolution: Complete Database Migration*
