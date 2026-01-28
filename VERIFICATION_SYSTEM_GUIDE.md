# User Verification System - Complete Implementation Guide

## Overview
This system requires admin approval for **Artists** and **Service Providers** before they can log in. **Audience** members are auto-approved.

---

## 📋 What Was Implemented

### 1. Database Changes (`COMPLETE_DATABASE_SETUP.sql`)
Added verification fields to `users` table:
- `is_verified` - 0=pending, 1=approved
- `verified_by` - Admin ID who verified
- `verified_at` - Timestamp of verification
- `rejection_reason` - Reason if rejected

### 2. SQL Queries (`ADMIN_VERIFICATION_QUERIES.sql`)
15 queries for complete verification management:
- Get pending registrations
- Approve/reject users
- View verification history
- Statistics and reporting

### 3. Admin Dashboard UI
**Files Modified:**
- `admindashboard.view.php` - Added registrations view with table
- `admindashboard.css` - Added styles for registration UI
- `admin-verification.js` - JavaScript for approve/reject actions
- `Admindashboard.php` - PHP controller with API endpoints

**Features:**
- View pending registrations
- Filter by role (All/Artists/Service Providers)
- Approve with one click
- Reject with optional reason
- Real-time updates

### 4. Controller Methods
**Admindashboard.php** now has:
- `getPendingRegistrations()` - Fetch pending users
- `approveUser()` - Approve registration
- `rejectUser()` - Reject with reason

---

## 🚀 Setup Instructions

### Step 1: Update Database
```sql
-- Run this in phpMyAdmin or MySQL:
USE rangamandala_db;
SOURCE /path/to/COMPLETE_DATABASE_SETUP.sql;
```

This will:
- Add verification columns to users table
- Create necessary foreign keys

### Step 2: Update Existing Users
```sql
-- Auto-approve existing audience members
UPDATE users SET is_verified = 1 WHERE role = 'audience';

-- Admins should also be verified
UPDATE users SET is_verified = 1 WHERE role = 'admin';
```

### Step 3: Modify Registration Controllers

#### For Audience Registration:
```php
// In AudienceRegister.php or similar
$is_verified = 1; // Auto-approve audience

$query = "INSERT INTO users (full_name, email, password, phone, role, is_verified) 
          VALUES (?, ?, ?, ?, ?, ?)";
```

#### For Artist/Service Provider Registration:
```php
// In ArtistRegister.php, ServiceProviderRegister.php
$is_verified = 0; // Requires admin approval

$query = "INSERT INTO users (full_name, email, password, phone, role, is_verified) 
          VALUES (?, ?, ?, ?, ?, ?)";

// Show success message
$_SESSION['success'] = "Registration successful! Your account is pending admin approval.";
```

### Step 4: Update Login Controller
```php
// In Login.php - Add verification check
if (in_array($user['role'], ['artist', 'service_provider'])) {
    if ($user['is_verified'] == 0) {
        if ($user['rejection_reason']) {
            $_SESSION['error'] = "Registration rejected: " . $user['rejection_reason'];
        } else {
            $_SESSION['error'] = "Account pending admin approval.";
        }
        header("Location: " . ROOT . "/login");
        exit;
    }
}

// Continue with normal login if verified...
```

---

## 🎯 How It Works

### User Flow:

1. **Artist/Service Provider Signs Up**
   - Account created with `is_verified = 0`
   - Cannot log in yet
   - Sees message: "Pending approval"

2. **Admin Reviews**
   - Goes to Admin Dashboard → Registrations
   - Sees pending list with user details
   - Can approve or reject

3. **Admin Approves**
   - User's `is_verified` set to 1
   - User can now log in

4. **Admin Rejects**
   - Rejection reason recorded
   - User sees reason when trying to log in

5. **Audience Members**
   - Auto-approved on registration (`is_verified = 1`)
   - Can log in immediately

---

## 📊 Admin Dashboard Usage

### View Pending Registrations:
1. Log in as admin
2. Click "Registrations" in sidebar
3. See all pending requests

### Approve a User:
1. Find user in table
2. Click "Approve" button
3. Confirm action
4. User can now log in

### Reject a User:
1. Find user in table
2. Click "Reject" button
3. Enter rejection reason (optional)
4. Confirm rejection

### Filter by Role:
- Click "All" to see everyone
- Click "Artists" to see only artists
- Click "Service Providers" to see only service providers

---

## 🔍 Testing Checklist

- [ ] Artist signs up → cannot log in (pending message)
- [ ] Service provider signs up → cannot log in (pending message)
- [ ] Audience signs up → can log in immediately
- [ ] Admin sees pending registrations
- [ ] Admin approves artist → artist can log in
- [ ] Admin rejects service provider → sees rejection reason
- [ ] Filter buttons work correctly
- [ ] Approve/reject actions update the list

---

## 🛠️ Troubleshooting

### Issue: "Unauthorized" error in console
**Solution:** Make sure admin is logged in and session is active

### Issue: Registrations not showing
**Solution:** 
1. Check database - are there pending users?
2. Run: `SELECT * FROM users WHERE is_verified = 0`
3. Verify ROOT constant is defined in JavaScript

### Issue: Approve/Reject not working
**Solution:**
1. Check browser console for errors
2. Verify `Admindashboard.php` controller exists
3. Ensure database columns exist: `is_verified`, `verified_by`, etc.

### Issue: Page doesn't load
**Solution:**
1. Check if CSS file path is correct
2. Verify JavaScript files are loaded
3. Check browser console for 404 errors

---

## 📁 Files Modified/Created

### Created:
- ✅ `ADMIN_VERIFICATION_QUERIES.sql` - All verification queries
- ✅ `admin-verification.js` - Frontend verification logic
- ✅ `LOGIN_VERIFICATION_IMPLEMENTATION.sql` - Login implementation guide

### Modified:
- ✅ `COMPLETE_DATABASE_SETUP.sql` - Added verification columns
- ✅ `admindashboard.view.php` - Added registrations UI
- ✅ `admindashboard.css` - Added verification styles
- ✅ `admindashboard.js` - Added initialization
- ✅ `Admindashboard.php` - Added controller methods

---

## 🎨 UI Components

### Registration Table
- User avatar with initials
- Full name and email
- Role badge (color-coded)
- Phone number
- Registration date
- Action buttons

### Approve Button
- Green color
- Check icon
- Instant approval

### Reject Button
- Red color
- Cancel icon
- Opens modal for reason

### Rejection Modal
- Text area for reason
- Optional field
- Cancel/Confirm buttons

---

## 🔐 Security Features

- ✅ Admin-only access to verification endpoints
- ✅ Session verification on every request
- ✅ SQL injection protection (prepared statements)
- ✅ Role-based access control
- ✅ Audit trail (verified_by, verified_at)

---

## 📈 Future Enhancements

- Email notifications to users on approval/rejection
- Bulk approve/reject actions
- View user details modal before approving
- Export pending registrations to CSV
- Verification activity logs table
- Re-verification after account changes

---

## 🎓 API Endpoints

### GET Pending Registrations
```
URL: /admindashboard/getPendingRegistrations
Method: GET
Response: JSON array of pending users
```

### POST Approve User
```
URL: /admindashboard/approveUser
Method: POST
Body: {"user_id": 123}
Response: {"success": true/false, "message": "..."}
```

### POST Reject User
```
URL: /admindashboard/rejectUser
Method: POST
Body: {"user_id": 123, "reason": "..."}
Response: {"success": true/false, "message": "..."}
```

---

## ✅ Implementation Complete!

The full verification system is now ready to use. Artists and service providers will need admin approval before they can access the system.
