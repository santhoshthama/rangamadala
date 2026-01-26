# PRODUCTION MANAGER DEBUGGING GUIDE
## Quick Steps to Fix Database Issues

### Step 1: Verify Database Tables Exist

Run this in PhpMyAdmin SQL tab:

```sql
-- Check if tables exist
SHOW TABLES LIKE 'service_requests';
SHOW TABLES LIKE 'theater_bookings';
SHOW TABLES LIKE 'service_schedules';
SHOW TABLES LIKE 'drama_budgets';
SHOW TABLES LIKE 'dramas';

-- Check service_requests table structure
DESCRIBE service_requests;

-- Check dramas table structure
DESCRIBE dramas;
```

**Expected Results:**

#### service_requests table should have:
```
id
drama_id (NOT drama_name!)
service_provider_id  
service_type
status
request_date
required_date
budget_range
description
special_requirements
created_by
created_at
updated_at
```

#### dramas table should have:
```
id
drama_name (NOT title!)
certificate_number
owner_name
description
certificate_image
created_by
creator_artist_id
created_at
updated_at
```

---

### Step 2: If service_requests Table is Missing

**Run this SQL:**

```sql
-- Drop the old table if it exists with wrong schema
DROP TABLE IF EXISTS service_requests;

-- Create the correct table
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
  KEY `idx_service_provider_id` (`service_provider_id`),
  KEY `idx_status` (`status`),
  KEY `idx_drama_status` (`drama_id`, `status`),
  CONSTRAINT `service_requests_fk_drama` FOREIGN KEY (`drama_id`) REFERENCES `dramas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_provider` FOREIGN KEY (`service_provider_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_requests_fk_creator` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

### Step 3: Insert Test Data

**IMPORTANT:** Replace the IDs with actual values from your database!

```sql
-- Find your drama IDs
SELECT id, drama_name FROM dramas LIMIT 5;

-- Find user IDs who can be service providers
SELECT id, name, email FROM users WHERE role = 'service_provider' LIMIT 5;

-- Find user ID who created dramas (for created_by)
SELECT id, name FROM users WHERE role IN ('artist', 'director') LIMIT 5;

-- Insert test service request
-- Replace: drama_id=1, service_provider_id=5, created_by=2 with actual IDs
INSERT INTO service_requests (
  drama_id, 
  service_provider_id, 
  service_type, 
  status, 
  request_date, 
  description, 
  created_by
) VALUES (
  1,                     -- Replace with actual drama_id
  5,                     -- Replace with actual service_provider user_id
  'sound_system',        -- Service type
  'accepted',            -- Status
  '2026-01-20',         -- Request date
  'Professional sound system for main stage',
  2                      -- Replace with actual user_id who created this
);

-- Verify insertion
SELECT * FROM service_requests;
```

---

### Step 4: Test Production Manager Access

Try accessing these URLs in your browser:

```
http://localhost/production_manager?drama_id=1
http://localhost/production_manager/manage_services?drama_id=1
```

**Replace drama_id=1 with an actual drama ID from your dramas table!**

---

### Step 5: Common Errors and Fixes

#### Error: "Unknown column 'drama_name' in service_requests"
**Cause:** Old service_requests table with wrong schema  
**Fix:** Drop and recreate table using Step 2

#### Error: "Table 'service_requests' doesn't exist"
**Cause:** Migration wasn't run  
**Fix:** Run PM_COMPLETE_MIGRATION.sql

#### Error: "Cannot add foreign key constraint"
**Cause:** Referenced tables (dramas, users) don't exist or have wrong structure  
**Fix:** 
```sql
-- Check if dramas table exists
SELECT * FROM dramas LIMIT 1;

-- Check if users table exists
SELECT * FROM users LIMIT 1;

-- If they don't exist, you need to run the main database_setup.sql first
```

#### Error: "Access denied for user"
**Cause:** Insufficient permissions  
**Fix:** Use root user or grant permissions:
```sql
GRANT ALL PRIVILEGES ON rangamadala_db.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
```

---

### Step 6: Verification Checklist

Run these queries to verify everything is working:

```sql
-- 1. Check all PM tables exist
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'rangamadala_db' 
  AND TABLE_NAME IN ('service_requests', 'theater_bookings', 'service_schedules', 'drama_budgets');
-- Should return 4 rows

-- 2. Verify service_requests has correct columns
SELECT COLUMN_NAME 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'rangamadala_db' 
  AND TABLE_NAME = 'service_requests' 
ORDER BY ORDINAL_POSITION;
-- Should show: id, drama_id (NOT drama_name), service_provider_id, etc.

-- 3. Test the exact query used by M_service_request
SELECT * FROM service_requests WHERE drama_id = 1;
-- Replace 1 with your drama_id
-- Should return rows or empty set (not an error)

-- 4. Test JOIN query (what will be used in views)
SELECT sr.*, d.drama_name 
FROM service_requests sr
JOIN dramas d ON sr.drama_id = d.id
WHERE sr.drama_id = 1;
-- Should work without errors
```

---

### Step 7: Database State Summary

After completing all steps, your database should have:

✅ **service_requests table** with drama_id column (FK to dramas.id)  
✅ **theater_bookings table** with drama_id column  
✅ **service_schedules table** with drama_id column  
✅ **drama_budgets table** with drama_id column  
✅ **dramas table** with drama_name column  
✅ All foreign key constraints working  
✅ At least one test record in service_requests  

---

## Quick Diagnosis Script

Run this single query to see your database state:

```sql
SELECT 
  'service_requests' as tbl,
  COUNT(*) as row_count,
  (SELECT COUNT(*) FROM information_schema.COLUMNS 
   WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'service_requests' 
     AND COLUMN_NAME = 'drama_id') as has_drama_id,
  (SELECT COUNT(*) FROM information_schema.COLUMNS 
   WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'service_requests' 
     AND COLUMN_NAME = 'drama_name') as has_drama_name
FROM service_requests
UNION ALL
SELECT 
  'dramas',
  COUNT(*),
  (SELECT COUNT(*) FROM information_schema.COLUMNS 
   WHERE TABLE_SCHEMA = DATABASE() 
     AND TABLE_NAME = 'dramas' 
     AND COLUMN_NAME = 'drama_name'),
  0
FROM dramas;
```

**Expected Result:**
```
tbl              | row_count | has_drama_id | has_drama_name
-----------------|-----------|--------------|---------------
service_requests |     3     |      1       |      0
dramas           |     2     |      0       |      1
```

**Meaning:**
- service_requests should have drama_id column (has_drama_id=1)
- service_requests should NOT have drama_name column (has_drama_name=0)
- dramas should have drama_name column

---

## Files to Check After Fix

1. **M_service_request.php** - ✅ Updated (uses drama_id)
2. **Production_manager.php** - ✅ Should work now
3. **All PM views** - ✅ Use $drama->drama_name correctly

---

## Still Having Issues?

### Get Full Error Details

Add this to the top of `public/index.php`:

```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

Then check the exact error message and line number.

---

**Last Updated:** January 23, 2026  
**Purpose:** Debug PM database schema issues
