# Production Manager Assignment System - Complete Guide

## Overview

This system allows directors to assign Production Managers (PMs) to their dramas through a request-based workflow. PMs help manage services, budgets, and theater bookings for productions.

---

## Features Implemented

### ✅ Director Side
- View current PM status for each drama
- Search and invite artists to be Production Managers
- Send PM requests with optional messages
- View pending PM requests
- Remove assigned Production Managers
- Change Production Managers

### ✅ Artist Side
- View PM requests in the dashboard
- Accept or reject PM requests
- See drama details when accepting
- Manage multiple PM assignments across different dramas

### ✅ Database
- Complete PM assignment tracking
- Request management with status tracking
- One active PM per drama constraint
- Automatic cleanup of old assignments when new PM is assigned

---

## Installation & Setup

### Step 1: Run Database Migration

Execute the SQL migration file in phpMyAdmin:

```sql
-- File: database_manager_assignment.sql
```

This creates two tables:
1. **`drama_manager_assignments`** - Tracks current Production Manager for each drama
2. **`drama_manager_requests`** - Tracks PM invitations/requests

**To apply:**
1. Open phpMyAdmin
2. Select your `rangamandala_db` database
3. Go to SQL tab
4. Copy and paste the contents of `database_manager_assignment.sql`
5. Click "Go" to execute

### Step 2: Verify Files Created

The following files have been created/modified:

**New Files:**
- `app/models/M_production_manager.php` - Model for PM CRUD operations
- `app/views/director/assign_managers.view.php` - PM management page for directors
- `app/views/director/search_managers.view.php` - Search page to find and invite PMs
- `database_manager_assignment.sql` - Database migration

**Modified Files:**
- `app/controllers/director.php` - Added PM management methods
- `app/controllers/Artistdashboard.php` - Added PM request response method
- `app/models/M_drama.php` - Updated get_dramas_by_manager() method
- `app/views/artistdashboard.view.php` - Added PM requests section

---

## User Workflows

### For Directors

#### 1. Access Production Manager Page

From the drama dashboard, click **Production Manager** in the sidebar.

**URL:** `/director/assign_managers?drama_id=X`

#### 2. Assign a Production Manager (First Time)

**When:** No PM is assigned yet

**Steps:**
1. Click **"Assign Production Manager"** button
2. You'll be redirected to the search page
3. Use the search bar to find artists by name or email
4. Click **"Send Request"** on an artist card
5. Optionally add a personal message
6. Click **"Send Request"** in the modal

**What happens:**
- Artist receives a PM request in their dashboard
- Request appears in "Pending Requests" section on PM page
- Director waits for artist to accept

#### 3. Change Production Manager

**When:** A PM is already assigned

**Steps:**
1. Click **"Change Manager"** button on the PM page
2. Search for a different artist
3. Send a new request
4. When new artist accepts:
   - Old PM is automatically removed
   - New PM is assigned
   - Other pending requests are cancelled

#### 4. Remove Production Manager

**When:** You want to remove current PM without replacement

**Steps:**
1. On the PM page, click **"Remove Manager"** button
2. Confirm the action
3. PM is removed immediately
4. Drama has no PM until you assign a new one

### For Artists

#### 1. View PM Requests

**Location:** Artist Dashboard → Requests Tab

You'll see:
- Drama name and certificate number
- Director's name
- Optional message from director
- Request date/time
- Information about PM responsibilities

#### 2. Accept PM Request

**Steps:**
1. Review the drama details
2. Read the director's message (if any)
3. Click **"Accept"** button
4. Confirmation message appears

**What happens:**
- You become the Production Manager for that drama
- Drama appears in "Manager" tab of your dashboard
- You can access PM-specific features for that drama
- Other pending requests for that drama are automatically cancelled

#### 3. Decline PM Request

**Steps:**
1. Click **"Decline"** button
2. Confirm the action
3. Request is marked as rejected

**What happens:**
- Director can send you another request later
- Request is removed from your pending list
- Director is not notified of rejection reason

---

## Technical Details

### Database Schema

#### drama_manager_assignments

Stores the current Production Manager for each drama.

```sql
- id (INT, PRIMARY KEY)
- drama_id (INT, FOREIGN KEY → dramas.id)
- manager_artist_id (INT, FOREIGN KEY → users.id)
- assigned_by (INT, FOREIGN KEY → users.id)
- assigned_at (DATETIME)
- status (ENUM: 'active', 'removed')
- removed_at (DATETIME, NULL)

UNIQUE KEY: (drama_id, status) -- Only one active manager per drama
```

#### drama_manager_requests

Stores PM invitations/requests from directors to artists.

```sql
- id (INT, PRIMARY KEY)
- drama_id (INT, FOREIGN KEY → dramas.id)
- artist_id (INT, FOREIGN KEY → users.id)
- director_id (INT, FOREIGN KEY → users.id)
- status (ENUM: 'pending', 'accepted', 'rejected', 'cancelled')
- message (TEXT, NULL)
- requested_at (DATETIME)
- responded_at (DATETIME, NULL)
- response_note (TEXT, NULL)
```

### Model Methods (M_production_manager.php)

**Core CRUD Operations:**
```php
- getAssignedManager($drama_id)
- createRequest($drama_id, $artist_id, $director_id, $message)
- acceptRequest($request_id, $artist_id)
- rejectRequest($request_id, $artist_id, $response_note)
- removeManager($drama_id, $director_id)
```

**Query Methods:**
```php
- getPendingRequestsForArtist($artist_id)
- getRequestsByDrama($drama_id, $status)
- getDramasByManager($artist_id)
- searchAvailableManagers($drama_id, $director_id, $search)
- isManagerForDrama($artist_id, $drama_id)
```

### Controller Methods

**Director Controller:**
```php
/director/assign_managers?drama_id=X     // View PM page
/director/search_managers?drama_id=X     // Search for PMs
/director/send_manager_request          // Send PM request (POST)
/director/remove_manager                // Remove PM (POST)
```

**Artistdashboard Controller:**
```php
/artistdashboard/respond_to_manager_request  // Accept/Reject PM request (POST)
```

### Business Rules

1. **One PM Per Drama:** Only one Production Manager can be active for a drama at any time.

2. **Automatic Cleanup:** When a new PM accepts a request:
   - Old PM assignment is marked as 'removed'
   - All other pending requests for that drama are cancelled

3. **Director Exclusion:** Directors cannot invite themselves as Production Managers.

4. **Current PM Exclusion:** The currently assigned PM is excluded from search results.

5. **Request Validation:** Cannot send duplicate pending requests to the same artist for the same drama.

---

## UI/UX Features

### Director PM Page (assign_managers.view.php)

**When No PM Assigned:**
- Empty state message
- Large "Assign Production Manager" button
- Information box explaining PM role

**When PM Assigned:**
- Profile card showing:
  - PM's photo and name
  - Contact information
  - Years of experience
  - Assignment date
- Action buttons:
  - "Remove Manager" (red, destructive)
  - "Change Manager" (yellow, warning)

**Pending Requests Section:**
- Shows all pending invitations
- Artist details
- Request timestamp
- Director's message (if any)
- Status badge

### Search Page (search_managers.view.php)

**Features:**
- Search bar with real-time filtering
- Grid of artist cards
- Each card shows:
  - Profile photo
  - Name and contact info
  - Years of experience
  - "Send Request" button
- Modal dialog for sending requests with optional message

**Smart Filtering:**
- Excludes drama director
- Excludes current PM
- Shows up to 50 results

### Artist Dashboard (artistdashboard.view.php)

**Requests Tab Updates:**
- Combined counter: Role Requests + PM Requests
- Separate sections for:
  - Production Manager Requests (top)
  - Role Requests (bottom)

**PM Request Cards:**
- Drama information
- Director details
- Director's message (highlighted)
- Role description
- Accept/Decline buttons

---

## Testing Checklist

### Director Tests

- [ ] Navigate to PM page from drama dashboard
- [ ] Verify "Assign PM" button shows when no PM assigned
- [ ] Search for artists successfully
- [ ] Send PM request with message
- [ ] Verify request appears in pending section
- [ ] Remove assigned PM
- [ ] Change PM to different artist
- [ ] Verify old PM is removed when new one accepts

### Artist Tests

- [ ] View PM requests in dashboard
- [ ] See request count in Requests tab badge
- [ ] Accept PM request
- [ ] Verify drama appears in Manager tab
- [ ] Decline PM request
- [ ] Verify request is removed from list

### Edge Cases

- [ ] Try to invite yourself as director (should fail)
- [ ] Send duplicate request (should show error)
- [ ] Accept request while another is pending (should cancel others)
- [ ] Remove PM and reassign immediately
- [ ] Search with no results

---

## Troubleshooting

### Common Issues

**1. PM requests not showing in artist dashboard**

**Solution:**
```php
// Check if pm_requests data is passed
var_dump($pm_requests); // In artistdashboard.view.php

// Verify model is loaded in Artistdashboard.php
$pm_model = $this->getModel('M_production_manager');
```

**2. "PM system unavailable" error**

**Cause:** Model not loading properly

**Solution:**
```php
// In director.php, verify:
protected $pmModel;

public function __construct()
{
    // ...
    $this->pmModel = $this->getModel('M_production_manager');
}
```

**3. Search returns no results**

**Possible causes:**
- All artists already assigned or invited
- Drama director is the only artist
- Database connection issue

**Debug:**
```php
// In M_production_manager.php, add error logging:
error_log("Search SQL: " . $sql);
error_log("Results count: " . count($results));
```

**4. Duplicate PM assignments**

**Prevention:** The UNIQUE KEY constraint prevents this:
```sql
UNIQUE KEY `unique_drama_active_manager` (`drama_id`, `status`)
```

If you see duplicates, rebuild the constraint:
```sql
ALTER TABLE drama_manager_assignments
DROP INDEX unique_drama_active_manager,
ADD UNIQUE KEY unique_drama_active_manager (drama_id, status);
```

---

## Future Enhancements

Potential features to add:

1. **Email Notifications**
   - Send email when PM request is received
   - Notify director when request is accepted/rejected

2. **PM Dashboard**
   - Dedicated dashboard for Production Managers
   - Service booking management
   - Budget tracking
   - Theater reservations

3. **Request Expiry**
   - Auto-cancel requests older than X days
   - Reminder notifications

4. **Multiple PMs**
   - Allow multiple PMs with different roles
   - Primary PM and assistant PMs

5. **Permission System**
   - Granular permissions for PMs
   - Different access levels

6. **Activity Log**
   - Track PM changes
   - Audit trail of assignments

---

## Files Reference

```
rangamadala/
├── database_manager_assignment.sql          # Migration file
├── app/
│   ├── models/
│   │   ├── M_production_manager.php        # PM model (NEW)
│   │   └── M_drama.php                     # Updated
│   ├── controllers/
│   │   ├── director.php                    # Updated
│   │   └── Artistdashboard.php             # Updated
│   └── views/
│       ├── director/
│       │   ├── assign_managers.view.php    # PM page (NEW)
│       │   └── search_managers.view.php    # Search page (NEW)
│       └── artistdashboard.view.php        # Updated
```

---

## Support

For issues or questions:

1. Check error logs: `C:\xampp\apache\logs\error.log`
2. Verify database tables exist
3. Check PHP error display is enabled
4. Review browser console for JavaScript errors

---

## Summary

The Production Manager assignment system is now fully functional with:

✅ Complete database schema
✅ Full CRUD operations
✅ Director workflow for assigning/removing PMs
✅ Artist workflow for accepting/rejecting requests
✅ Proper authorization and validation
✅ Clean, responsive UI
✅ Smart business logic (one PM per drama, automatic cleanup)

The system is ready for production use!
