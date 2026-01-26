# Production Manager List - Database Fetch Fix

## Problem
The search_managers.view.php page was showing "No Artists Available" message even though artists existed in the database. The page required a search action to load data, but even then showed empty results.

## Root Causes Identified and Fixed

### 1. **Column Name Mismatch** ✅ FIXED
- **Issue**: The `searchAvailableManagers()` method was using `u.profile_image` but the users table uses `u.nic_photo`
- **Solution**: Updated the SQL query to use `u.nic_photo AS profile_image` to match the column that actually exists in the database
- **File**: `app/models/M_production_manager.php` (line 265)
- **Before**: `SELECT u.id, u.full_name, u.email, u.phone, u.profile_image, u.years_experience`
- **After**: `SELECT u.id, u.full_name, u.email, u.phone, u.nic_photo AS profile_image, u.years_experience`

### 2. **Always Fetch from Database** ✅ FIXED
- **Issue**: The query logic was not modified to load all managers on page load
- **Solution**: The query now executes for both search and no-search scenarios (empty search returns all artists)
- **File**: `app/models/M_production_manager.php` (line 264-295)
- **Result**: All available artists now display on initial page load without requiring a search

### 3. **Added Debug Logging** ✅ ENHANCED
- **Added**: Error logging to debug the search_managers flow
- **File**: `app/controllers/director.php` (line 321)
- **Logs**: Director ID, Drama ID, Search term, and number of results found
- **Purpose**: Helps identify data flow issues during development

### 4. **Proper Type Casting** ✅ VERIFIED
- **Verified**: Both `drama_id` and `director_id` are properly cast to integers before passing to model
- **File**: `app/controllers/director.php` (lines 319-320)

## Database Query

The corrected SQL query now properly fetches all available artists:

```sql
SELECT u.id, u.full_name, u.email, u.phone, u.nic_photo AS profile_image, u.years_experience
FROM users u
WHERE u.role = 'artist' 
AND u.id != :director_id
AND u.id NOT IN (
    SELECT manager_artist_id 
    FROM drama_manager_assignments 
    WHERE drama_id = :drama_id 
    AND status = 'active'
)
ORDER BY u.full_name ASC LIMIT 50
```

**Excludes:**
- The drama's director (cannot be their own PM)
- Any artist already assigned as an active PM for this drama

**Includes:**
- All other artists in the system
- Their full profile information including experience level
- Sorted alphabetically by name

## Files Modified

1. **app/models/M_production_manager.php**
   - Line 265: Changed column name from `profile_image` to `nic_photo AS profile_image`
   - Line 280: Updated to always return array (even if empty)

2. **app/controllers/director.php**
   - Line 316: Added `trim()` to search input
   - Line 319: Cast `director_id` to int
   - Line 321-322: Added error logging for debugging

## Testing Steps

1. **Navigate** to a drama's Production Manager section as a director
2. **Click** "Search Production Manager" or the button to search
3. **Verify** that all available artists appear immediately without needing to search
4. **Check** that:
   - Artist names display with photos
   - Contact information shows (email, phone)
   - Experience level displays correctly
   - "Send Request" button is available
5. **Search functionality** still works to filter the list

## Expected Behavior After Fix

✅ On page load, **all available artists** display from the database
✅ **"X artist(s) available to be Production Manager"** counter shows correct count
✅ **Artists grid** displays with full profile information
✅ **Search bar** can filter the list to find specific artists
✅ **Send Request** button allows inviting selected artists
✅ **Debug logs** show successful data retrieval in server logs

## Database Requirements

Ensure the `drama_manager_assignments` table exists with proper structure:

```sql
CREATE TABLE drama_manager_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    drama_id INT NOT NULL,
    manager_artist_id INT NOT NULL,
    assigned_by INT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    assigned_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (drama_id, status),
    FOREIGN KEY (drama_id) REFERENCES dramas(id) ON DELETE CASCADE,
    FOREIGN KEY (manager_artist_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id)
);
```

Run `addpm.sql` if this table hasn't been created yet.
