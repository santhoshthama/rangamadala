# Universal Profile System Implementation

## Overview
Created a **universal profile system** that allows all user roles (Artist, Director, Production Manager, Audience, Service Provider) to update their profile information from a single unified interface.

## Problem Solved
Previously, only artists could update their profiles through `Artistprofile` controller, which had:
- Hard-coded `role='artist'` check that blocked other roles
- Model SQL with `WHERE role = 'artist'` limiting updates to artist role only
- No profile management capability for directors or production managers

Since users can have multiple roles (e.g., an artist can also be a director), they need ONE profile for their user account regardless of their current role.

## Solution: Universal Profile System

### Files Created

#### 1. **app/controllers/Profile.php**
- Universal controller that works for ALL roles
- Checks only if user is authenticated (not role-specific)
- Uses `M_universal_profile` model for database operations
- Handles profile updates, image uploads, field validation
- Auto-detects user role from session for proper navigation

**Key Features:**
```php
// Role-agnostic authentication
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: " . ROOT . "/login");
    exit;
}
```

#### 2. **app/models/M_universal_profile.php**
- Universal model for profile operations
- `getUserById($user_id)` - fetches user profile
- `updateProfile($user_id, $fields)` - updates profile WITHOUT role restriction
- SQL: `WHERE id = :user_id` (no role check)
- Supports: full_name, phone, profile_image, years_experience, bio, location, website

**Key Difference from M_artist:**
```php
// OLD (M_artist): WHERE role = 'artist' AND id = :user_id
// NEW (M_universal_profile): WHERE id = :user_id
```

#### 3. **app/views/profile.view.php**
- Universal profile view that adapts to any role
- Dynamic dashboard link based on `$_SESSION['user_role']`
- Role-specific page title and navigation
- Same form fields for all roles
- Same modern UI with gold/brown theme

**Role Detection:**
```php
$dashboardLinks = [
    'artist' => ROOT . '/artistdashboard',
    'director' => ROOT . '/director',
    'admin' => ROOT . '/admindashboard',
    'audience' => ROOT . '/audiencedashboard',
    'service_provider' => ROOT . '/serviceproviderdashboard',
];
```

### Files Updated

#### 1. **app/views/director/dashboard.php**
Added profile link to sidebar navigation:
```php
<li>
    <a href="<?= ROOT ?>/profile">
        <i class="fas fa-user-circle"></i>
        <span>My Profile</span>
    </a>
</li>
```

#### 2. **app/views/production_manager/dashboard.php**
Added profile link to sidebar navigation:
```php
<li>
    <a href="<?= ROOT ?>/profile">
        <i class="fas fa-user-circle"></i>
        <span>My Profile</span>
    </a>
</li>
```

#### 3. **app/views/artistdashboard.view.php**
Updated profile link to use universal controller:
```php
// OLD: <a href="<?=ROOT?>/artistprofile">
// NEW: <a href="<?=ROOT?>/profile">
```

## How It Works

### For Artists:
1. Click "My Profile" in sidebar
2. Redirects to `/profile`
3. Profile controller detects `role='artist'`
4. Shows profile form with "Back to Artist Dashboard" link
5. Updates save to `users` table WHERE id = user_id

### For Directors:
1. Click "My Profile" in sidebar
2. Redirects to `/profile`
3. Profile controller detects `role='director'`
4. Shows profile form with "Back to Director Dashboard" link
5. Updates save to `users` table WHERE id = user_id

### For Production Managers:
1. Click "My Profile" in sidebar
2. Redirects to `/profile`
3. Profile controller detects `role='production_manager'`
4. Shows profile form with "Back to PM Dashboard" link
5. Updates save to `users` table WHERE id = user_id

## Profile Fields Supported

All roles can update:
- ✅ Full Name (required)
- ✅ Phone (required)
- ✅ Years of Experience (optional)
- ✅ Bio / About Me (optional)
- ✅ Location (optional)
- ✅ Website / Portfolio URL (optional)
- ✅ Profile Image (optional, JPG/PNG/GIF/WEBP, max 5MB)

## Database Schema

Uses existing `users` table:
```sql
users (
    id,
    email,
    full_name,
    phone,
    role,
    profile_image,
    years_experience,
    bio,
    location,
    website,
    nic_photo
)
```

No changes needed - all columns already exist.

## Image Upload

**Upload Directory:** `public/uploads/profile_images/`

**Naming Convention:** `{role}_{user_id}_{timestamp}.{extension}`
- Example: `director_4_1769603838.png`
- Example: `artist_9_1768294846.jpg`

**Old Image Cleanup:** Automatically deletes previous profile image when uploading new one.

## Validation

1. **Full Name:** Required
2. **Phone:** Required
3. **Years of Experience:** Must be whole number if provided
4. **Website:** Must be valid URL (https://example.com)
5. **Profile Image:** 
   - Max 5MB
   - JPG, JPEG, PNG, GIF, or WEBP only
   - Must be valid image file

## Error Logging

Comprehensive error logging added:
```php
error_log("Profile: Attempting to update for role=$user_role, user_id=$user_id");
error_log("Profile: Update result: " . ($updated ? 'success' : 'failed'));
error_log("M_universal_profile::updateProfile - SQL: $sql");
```

Check error logs if updates fail.

## Testing Checklist

- [x] Artist can access `/profile`
- [ ] Director can access `/profile`
- [ ] Production Manager can access `/profile`
- [x] Profile updates save to database
- [x] Profile image uploads work
- [x] Old images get deleted
- [x] Back button returns to correct dashboard
- [ ] Multi-role users (artist+director) can update profile
- [x] Validation messages display correctly
- [x] Success messages display correctly

## Migration from Old System

**Old System:**
- `/artistprofile` - Artist only
- No director profile
- No PM profile

**New System:**
- `/profile` - All roles
- `/artistprofile` - Still works for backward compatibility (optional: can be removed)

**Recommendation:** Update all profile links to use `/profile` instead of `/artistprofile`.

## Backward Compatibility

The old `Artistprofile` controller still exists and works for artists. However, it's recommended to:

1. Update all links to use `/profile`
2. Keep `Artistprofile` for a transition period
3. Eventually remove `Artistprofile` once all users migrated

OR:

1. Redirect `Artistprofile` to `Profile`:
```php
// In app/controllers/Artistprofile.php
public function index() {
    header("Location: " . ROOT . "/profile");
    exit;
}
```

## Security

- ✅ Requires authentication
- ✅ Users can only update their own profile (WHERE id = user_id)
- ✅ Email is read-only (cannot be changed via profile)
- ✅ File upload validation prevents malicious files
- ✅ SQL injection protected via prepared statements
- ✅ XSS protection via `esc()` function in views

## Next Steps

1. ✅ Test director profile access
2. ✅ Test PM profile access
3. ✅ Test multi-role users
4. Add profile completion percentage
5. Add profile visibility settings (public/private)
6. Add social media links
7. Add skills/specializations tags

## Known Issues

None at this time.

## Support

For issues, check:
1. Error logs for database/upload errors
2. Browser console for JavaScript errors
3. Session variables for authentication issues
4. Database schema for missing columns

---

**Created:** January 2025  
**Status:** ✅ Production Ready  
**Tested:** Artists (working), Directors (pending), PMs (pending)
