# BUG FIX SUMMARY - Production Manager Database Schema Mismatch

## The Problem

**Error Message:**
```
Fatal error: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'drama_name' 
in 'IN/ALL/ANY subquery' in Database.php:60
```

**Root Cause:**
The `M_service_request.php` model had the **wrong database schema**. It was using an old structure that didn't match the migration files.

---

## What Was Wrong

### Old M_service_request.php (BROKEN)
```php
// ❌ WRONG - Uses drama_name as a text column
public function getServicesByDrama($drama_id)
{
    $this->db->query("SELECT * FROM service_requests WHERE drama_name IN (
                        SELECT drama_name FROM dramas WHERE id = :drama_id
                      ) ORDER BY created_at DESC");
    // ...
}

// ❌ WRONG - Old column names
public function createRequest($data)
{
    $this->db->query("INSERT INTO service_requests (
        provider_id, requester_name, requester_email, requester_phone,
        drama_name, service_required, start_date, end_date, ...
    ) VALUES (...)");
    // ...
}
```

**Problems:**
1. Used `drama_name` in service_requests table (doesn't exist - should be `drama_id`)
2. Used subquery with `SELECT drama_name FROM dramas` (unnecessary complexity)
3. Used old column names: `provider_id`, `requester_name`, `service_required`
4. Missing many required methods

---

## What Was Fixed

### New M_service_request.php (CORRECT) ✅

```php
// ✅ CORRECT - Uses drama_id foreign key
public function getServicesByDrama($drama_id)
{
    $this->db->query("SELECT * FROM service_requests 
                     WHERE drama_id = :drama_id 
                     ORDER BY created_at DESC");
    $this->db->bind(':drama_id', $drama_id);
    $result = $this->db->resultSet();
    return $result ? $result : [];
}

// ✅ CORRECT - New column names matching migration
public function createRequest($data)
{
    $this->db->query("INSERT INTO service_requests (
        drama_id, service_provider_id, service_type, status, 
        request_date, required_date, budget_range, description, 
        special_requirements, created_by
    ) VALUES (
        :drama_id, :service_provider_id, :service_type, :status,
        :request_date, :required_date, :budget_range, :description,
        :special_requirements, :created_by
    )");
    // ... proper bindings
}
```

---

## Schema Comparison

### service_requests Table Schema

#### OLD (Wrong)
```sql
- provider_id
- requester_name
- requester_email  
- requester_phone
- drama_name          ❌ TEXT column (wrong!)
- service_required
- start_date
- end_date
- notes
```

#### NEW (Correct) ✅
```sql
- id
- drama_id            ✅ FOREIGN KEY → dramas.id
- service_provider_id ✅ FOREIGN KEY → users.id
- service_type
- status (enum)
- request_date
- required_date
- budget_range
- description
- special_requirements
- created_by          ✅ FOREIGN KEY → users.id
- created_at
- updated_at
```

---

## Methods Added to M_service_request.php

The model was completely rewritten with proper methods:

### Query Methods (8 total)
1. ✅ `getServicesByDrama($drama_id)` - Get all services for a drama
2. ✅ `getServicesByStatus($drama_id, $status)` - Filter by status
3. ✅ `countServicesByStatus($drama_id, $status)` - Count by status
4. ✅ `getRequestsByProvider($provider_id)` - Provider's requests (with JOIN)
5. ✅ `getRequestById($request_id)` - Single request (with JOIN)
6. ✅ `getTotalCount($drama_id)` - Total count

### Mutation Methods (3 total)
7. ✅ `createRequest($data)` - Insert new request
8. ✅ `updateRequestStatus($request_id, $status)` - Update status
9. ✅ `deleteRequest($request_id)` - Delete request

---

## Database Migration Required

To use the new code, you MUST run the migration:

### Option 1: Complete Migration (Recommended)
```sql
-- Run this file in PhpMyAdmin:
PM_COMPLETE_MIGRATION.sql
```

This creates:
- ✅ service_requests (with drama_id FK)
- ✅ theater_bookings  
- ✅ service_schedules
- ✅ drama_budgets

### Option 2: Just Fix service_requests Table

```sql
-- Drop old table (if exists with wrong schema)
DROP TABLE IF EXISTS service_requests;

-- Create correct table
CREATE TABLE `service_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `drama_id` int NOT NULL,
  `service_provider_id` int NOT NULL,
  `service_type` varchar(100) NOT NULL,
  `status` enum('pending','accepted','completed','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `request_date` date NOT NULL,
  `required_date` date,
  `budget_range` varchar(100),
  `description` text,
  `special_requirements` text,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_drama_id` (`drama_id`),
  CONSTRAINT `service_requests_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## How Data Flows Now

### Before (BROKEN)
```
Controller → Model
  ↓
SELECT * FROM service_requests WHERE drama_name IN (
  SELECT drama_name FROM dramas WHERE id = 8
)
  ↓
❌ Error: Column 'drama_name' doesn't exist in service_requests
```

### After (WORKING) ✅
```
Controller → Model
  ↓
SELECT * FROM service_requests WHERE drama_id = 8
  ↓
✅ Returns array of service requests (or empty array)
  ↓
Controller → View
  ↓
View loops and displays data
```

---

## Files Changed

| File | Status | Changes |
|------|--------|---------|
| M_service_request.php | ✅ FIXED | Complete rewrite with 9 methods |
| PM_COMPLETE_MIGRATION.sql | ✅ CREATED | All tables with correct schema |
| PM_DATABASE_DEBUG_GUIDE.md | ✅ CREATED | Debugging instructions |
| pm_system_tables.sql | ✅ UPDATED | Added service_requests table |

---

## Testing Checklist

After running migration:

```sql
-- 1. Verify table structure
DESCRIBE service_requests;
-- Should show: drama_id (not drama_name)

-- 2. Test the query
SELECT * FROM service_requests WHERE drama_id = 1;
-- Should work (even if empty)

-- 3. Test with JOIN (what views will use)
SELECT sr.*, d.drama_name 
FROM service_requests sr
JOIN dramas d ON sr.drama_id = d.id
WHERE sr.drama_id = 1;
-- Should work without errors
```

Then in browser:
```
http://localhost/production_manager?drama_id=1
http://localhost/production_manager/manage_services?drama_id=1
```

**Expected Result:** Pages load without errors (may show empty state if no data)

---

## Why This Happened

The original `M_service_request.php` was created with a different schema design (using text-based `drama_name` instead of FK `drama_id`). When the migration files were created with the proper relational schema, the model wasn't updated to match.

This is a common issue when:
- Models are created before finalizing database design
- Database schema changes but models aren't updated
- Old code references removed/renamed columns

---

## Prevention for Future

✅ **Always verify:**
1. Model queries match migration table structure
2. Column names are correct (drama_id vs drama_name)
3. Foreign keys are used instead of text fields
4. Migration is run before testing code

✅ **Use this command to check schema:**
```sql
DESCRIBE service_requests;
DESCRIBE dramas;
```

✅ **Match model to migration:**
- Migration has `drama_id`? Model must use `drama_id`
- Migration has `service_type`? Model must use `service_type`
- Never assume column names!

---

## Summary

| Issue | Solution |
|-------|----------|
| ❌ Wrong query using drama_name | ✅ Fixed to use drama_id |
| ❌ Old column names | ✅ Updated to match migration |
| ❌ Missing methods | ✅ Added 9 comprehensive methods |
| ❌ No foreign keys in queries | ✅ Proper FK relationships |
| ❌ Complex subqueries | ✅ Simple, efficient queries |

**Status:** ✅ **FIXED**  
**Action Required:** Run PM_COMPLETE_MIGRATION.sql  
**Expected Result:** All PM pages work correctly

---

*Generated: January 23, 2026*  
*Bug: Schema mismatch in M_service_request.php*  
*Resolution: Complete model rewrite + migration*
