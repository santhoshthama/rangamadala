# Production Manager Database Migration Setup
## Quick Start Guide

### Problem Summary
The `service_requests` table was missing from your database, causing the error:
```
Table 'rangamadala_db.service_requests' doesn't exist
```

This table is **required** by all Production Manager pages.

---

## Solution: Run the Migration

### Option 1: Using PhpMyAdmin (Easiest)

1. **Open PhpMyAdmin**
   - Go to: `http://localhost/phpmyadmin`
   - Log in with your credentials

2. **Select Your Database**
   - Click on `rangamadala_db` (or your database name)

3. **Go to SQL Tab**
   - Click the "SQL" tab at the top

4. **Copy & Paste the Migration Script**
   - Open: `PM_COMPLETE_MIGRATION.sql` (new comprehensive file)
   - Copy all the SQL code
   - Paste into PhpMyAdmin SQL editor
   - Click "Go" to execute

5. **Verify**
   - You should see: "Queries executed successfully"
   - Check the Tables list - you should see all 4 tables:
     - service_requests (NEW - this was missing!)
     - theater_bookings
     - service_schedules
     - drama_budgets

---

### Option 2: Using MySQL Command Line

```powershell
# Navigate to your database directory
cd C:\xampp\htdocs\rangamadala

# Log in to MySQL
mysql -u root -p rangamadala_db

# Then paste or run:
SOURCE PM_COMPLETE_MIGRATION.sql;

# Or run the original file (which now includes service_requests):
SOURCE pm_system_tables.sql;

# Verify tables were created:
SHOW TABLES;
```

---

### Option 3: Using XAMPP Control Panel

1. Open XAMPP Control Panel
2. Start MySQL
3. Click "Admin" button next to MySQL
4. PhpMyAdmin will open
5. Follow Option 1 above

---

## Files Provided

| File | Purpose | Status |
|------|---------|--------|
| `PM_COMPLETE_MIGRATION.sql` | ✅ **NEW** - Complete migration with all tables + sample data | **USE THIS** |
| `pm_system_tables.sql` | Updated - Now includes service_requests table | Updated |

---

## What Gets Created

### 1. service_requests (CRITICAL - was missing)
```sql
Fields: id, drama_id, service_provider_id, service_type, status, 
        request_date, description, created_by, timestamps
```
✅ Links all services to dramas
✅ Used by manage_services page

### 2. theater_bookings
```sql
Fields: id, drama_id, theater_name, booking_date, time, capacity, cost, status
```
✅ Used by book_theater page

### 3. service_schedules
```sql
Fields: id, drama_id, service_name, scheduled_date, time, venue, status
```
✅ Used by manage_schedule page

### 4. drama_budgets
```sql
Fields: id, drama_id, item_name, category, allocated_amount, spent_amount, status
```
✅ Used by manage_budget page

---

## After Running Migration

### 1. Insert Test Data (Optional)
Uncomment the sample data section in `PM_COMPLETE_MIGRATION.sql` to add test records:
```sql
-- Replace drama_id=1 with an actual drama ID from your database
INSERT INTO service_requests (drama_id, service_provider_id, service_type, status, request_date, description, created_by)
VALUES (1, 5, 'sound_system', 'accepted', '2026-01-20', 'Professional sound system', 2);
```

### 2. Test Production Manager Pages
Try accessing these URLs:
- http://localhost/production_manager?drama_id=1 (Dashboard)
- http://localhost/production_manager/manage_services?drama_id=1
- http://localhost/production_manager/manage_budget?drama_id=1
- http://localhost/production_manager/book_theater?drama_id=1
- http://localhost/production_manager/manage_schedule?drama_id=1

### 3. Verify No Errors
- Should see dynamic data (not static demo data)
- No PHP errors or database errors
- Empty states should show if no data exists

---

## Troubleshooting

### Error: "Table already exists"
✅ **Safe** - The `CREATE TABLE IF NOT EXISTS` clause prevents this
- Just means table was already created
- No data loss

### Error: "Unknown column in 'on clause'"
❌ **Problem** - Foreign key references non-existent table
- Make sure `dramas` table exists
- Make sure `users` table exists
- Run: `SELECT * FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE();`

### Error: "Access denied"
❌ **Problem** - User permissions
- Use PhpMyAdmin (Option 1) if command line fails
- Check MySQL user has CREATE TABLE privileges

### Pages Still Show Empty
✅ **Normal** - Just means no data exists
- Insert test data using sample data section
- Or create real data through the UI

---

## Verification Checklist

After running the migration:

```sql
-- Run these to verify:

-- 1. Check tables exist
SHOW TABLES;
-- You should see: service_requests, theater_bookings, service_schedules, drama_budgets

-- 2. Check service_requests structure
DESCRIBE service_requests;
-- Should show: id, drama_id, service_provider_id, service_type, status, etc.

-- 3. Check row counts
SELECT COUNT(*) FROM service_requests;
SELECT COUNT(*) FROM theater_bookings;
SELECT COUNT(*) FROM service_schedules;
SELECT COUNT(*) FROM drama_budgets;

-- 4. Check foreign keys
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'rangamadala_db';
```

---

## Expected Result

✅ **After successful migration:**
1. All 4 tables created
2. All foreign keys configured
3. All indexes created for performance
4. Production Manager pages load without errors
5. Data displays dynamically (or empty state if no records)

---

## Still Having Issues?

**Error Message in Terminal:**
```
Fatal error: Uncaught PDOException: SQLSTATE[42S02]: Base table or view not found: 1146 
Table 'rangamadala_db.service_requests' doesn't exist
```

**Solution:**
1. Run `PM_COMPLETE_MIGRATION.sql` (provides all tables)
2. Verify with: `SHOW TABLES;` in MySQL
3. Refresh browser page
4. Check browser console for remaining errors

---

## Database Structure Diagram

```
Production Manager System
├── service_requests (Core)
│   ├── drama_id → dramas.id
│   ├── service_provider_id → users.id
│   └── created_by → users.id
│
├── theater_bookings
│   ├── drama_id → dramas.id
│   └── created_by → users.id
│
├── service_schedules
│   ├── drama_id → dramas.id
│   ├── service_request_id → service_requests.id
│   ├── assigned_to → users.id
│   └── created_by → users.id
│
└── drama_budgets
    ├── drama_id → dramas.id
    └── created_by → users.id
```

---

## Files to Reference

- [PM_COMPLETE_MIGRATION.sql](PM_COMPLETE_MIGRATION.sql) - Complete migration script
- [pm_system_tables.sql](pm_system_tables.sql) - Updated with service_requests
- [PM_TESTING_GUIDE.md](PM_TESTING_GUIDE.md) - Test data and verification steps
- [SERVICE_PROVIDER_README.md](SERVICE_PROVIDER_README.md) - Database patterns reference

---

**Status**: ✅ Ready to Deploy  
**Next Step**: Run the migration scripts above  
**Expected Time**: 2-5 minutes

---

*Last Updated: January 23, 2026*
*Purpose: Fix missing service_requests table error*
