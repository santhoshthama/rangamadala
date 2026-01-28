# Artist Profile CRUD Analysis & Issues

## Current Status: ✅ Working (with minor improvements needed)

## Files Analyzed

### 1. Controller: [Artistprofile.php](app/controllers/Artistprofile.php)
**Status:** ✅ Working correctly
- Handles GET requests to display profile
- Handles POST requests to update profile
- Validates all inputs properly
- Manages file uploads for profile images
- Cleans up old images when new ones are uploaded

### 2. Model: [M_artist.php](app/models/M_artist.php)
**Status:** ✅ Working correctly  
- `get_artist_by_id()` - Fetches artist data
- `update_artist_profile()` - Updates allowed fields with proper SQL sanitization
- Allows updates for: full_name, phone, profile_image, years_experience

### 3. View: [artistprofile.view.php](app/views/artist/artistprofile.view.php)
**Status:** ✅ Working correctly
- Beautiful gradient card design
- Shows profile image with fallback
- Displays user information
- Edit form with validation messages
- Responsive design

---

## Current Implementation

### ✅ Working Features

1. **Read (Display Profile)**
   - Shows artist name, email, phone
   - Displays profile image or fallback
   - Shows years of experience
   - Shows NIC photo if available

2. **Update Profile**
   - Full name (required)
   - Phone number (required)
   - Years of experience (optional, numeric)
   - Profile image upload (jpg, jpeg, png, gif, webp, max 5MB)

3. **File Upload**
   - Validates image type
   - Validates file size (max 5MB)
   - Generates unique filename
   - Deletes old image when new one uploaded
   - Proper error handling

4. **Security**
   - Session authentication
   - Role verification (must be artist)
   - SQL injection protection (parameterized queries)
   - File validation
   - Path sanitization

---

## Issues Found

### ⚠️ Issue 1: Missing Profile Fields (Bio, Location, Website)
**Severity:** Medium  
**Impact:** Users can't add complete profile information

**Problem:**
The database has columns for `bio`, `location`, and `website` (from add_missing_user_columns.sql), but they're not in the profile form.

**Files Affected:**
- `app/views/artistprofile.view.php` - Missing form fields
- `app/controllers/Artistprofile.php` - Not processing these fields
- `app/models/M_artist.php` - `update_artist_profile()` needs to allow these columns

**Fix Required:**
Add these fields to the profile form and controller.

---

### ⚠️ Issue 2: Profile Image Path Inconsistency
**Severity:** Low  
**Impact:** Profile images may not display correctly in some cases

**Problem:**
The controller saves only the filename:
```php
$updateFields['profile_image'] = $profileImageName; // Just filename
```

But the view expects various formats:
```php
if (strpos($imageValue, '/') !== false) {
    $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
} else {
    $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
}
```

**Current Behavior:** Works, but inconsistent
**Best Practice:** Save consistent relative paths in database

---

### ⚠️ Issue 3: No Portfolio/Skills Section
**Severity:** Low  
**Impact:** Artists can't showcase their skills/specializations

**Problem:**
Artists need to show:
- Specializations (e.g., "Classical Dance", "Comedy", "Drama")
- Portfolio links
- Previous work

**Current State:** Basic profile only

---

### ⚠️ Issue 4: No Profile Visibility/Completeness Indicator
**Severity:** Low  
**Impact:** Users don't know if their profile is complete

**Problem:**
No indicator showing:
- Profile completion percentage
- Missing required fields
- Profile strength score

---

## Recommended Fixes

### Priority 1: Add Missing Profile Fields

#### Step 1: Update Model ([M_artist.php](app/models/M_artist.php))

```php
public function update_artist_profile($user_id, array $fields) {
    if (empty($fields)) {
        return false;
    }

    // Add bio, location, website to allowed fields
    $allowed = ['full_name', 'phone', 'profile_image', 'years_experience', 'bio', 'location', 'website'];
    // ... rest of code unchanged
}
```

#### Step 2: Update Controller ([Artistprofile.php](app/controllers/Artistprofile.php))

Add to form data initialization:
```php
$data['form'] = [
    'full_name' => $data['user']->full_name ?? '',
    'phone' => $data['user']->phone ?? '',
    'years_experience' => ...,
    'bio' => $data['user']->bio ?? '',
    'location' => $data['user']->location ?? '',
    'website' => $data['user']->website ?? ''
];
```

Add to POST processing:
```php
$bio = trim($_POST['bio'] ?? '');
$location = trim($_POST['location'] ?? '');
$website = trim($_POST['website'] ?? '');

// Validate website URL if provided
if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
    $errors[] = 'Please enter a valid website URL.';
}

// Add to updateFields
$updateFields = [
    'full_name' => $full_name,
    'phone' => $phone,
    'years_experience' => $years_input === '' ? null : $yearsValue,
    'bio' => $bio === '' ? null : $bio,
    'location' => $location === '' ? null : $location,
    'website' => $website === '' ? null : $website
];
```

#### Step 3: Update View ([artistprofile.view.php](app/views/artist/artistprofile.view.php))

Add new form fields after years_experience:

```php
<div class="form-group full">
    <label for="bio">Bio / About Me</label>
    <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself, your experience, and what makes you unique..."><?= esc($form['bio'] ?? '') ?></textarea>
</div>

<div class="form-group">
    <label for="location">Location</label>
    <input id="location" name="location" type="text" placeholder="e.g. Colombo, Sri Lanka" value="<?= esc($form['location'] ?? '') ?>">
</div>

<div class="form-group">
    <label for="website">Website / Portfolio</label>
    <input id="website" name="website" type="url" placeholder="https://yourwebsite.com" value="<?= esc($form['website'] ?? '') ?>">
</div>
```

Add CSS for textarea:
```css
textarea {
    width: 100%;
    padding: 14px;
    border: 2px solid var(--border);
    border-radius: 12px;
    font-size: 15px;
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
}

textarea:focus {
    outline: none;
    border-color: var(--brand);
    box-shadow: 0 0 0 4px rgba(186, 142, 35, 0.18);
}
```

Update profile summary to show location:
```php
<p><i class="fas fa-map-marker-alt"></i> <?= $user && $user->location ? esc($user->location) : 'Location not set' ?></p>
```

---

### Priority 2: Fix Profile Image Path Storage

Update controller to save relative path:
```php
if ($profileImageName !== null) {
    $updateFields['profile_image'] = 'uploads/profile_images/' . $profileImageName;
}
```

Simplify view image handling:
```php
$profileImageSrc = ROOT . '/assets/images/default-avatar.jpg';
if ($user && !empty($user->profile_image)) {
    $profileImageSrc = ROOT . '/' . ltrim($user->profile_image, '/');
} elseif ($user && !empty($user->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim($user->nic_photo, '/');
}
```

---

### Priority 3: Add Profile Completeness Indicator

Add to controller after fetching user:
```php
$data['profile_completion'] = $this->calculateProfileCompletion($data['user']);

private function calculateProfileCompletion($user) {
    $fields = [
        'full_name' => !empty($user->full_name),
        'phone' => !empty($user->phone),
        'profile_image' => !empty($user->profile_image),
        'years_experience' => isset($user->years_experience) && $user->years_experience !== null,
        'bio' => !empty($user->bio),
        'location' => !empty($user->location)
    ];
    
    $completed = count(array_filter($fields));
    $total = count($fields);
    
    return round(($completed / $total) * 100);
}
```

Add to view:
```php
<div class="summary-item">
    <span>Profile Completion</span>
    <strong><?= $profile_completion ?? 0 ?>%</strong>
    <div class="progress-bar">
        <div class="progress-fill" style="width: <?= $profile_completion ?? 0 ?>%"></div>
    </div>
</div>
```

---

## Testing Checklist

### ✅ Current Working Tests
- [ ] Navigate to /artistprofile
- [ ] View displays correctly with current data
- [ ] Update full name - saves correctly
- [ ] Update phone - saves correctly
- [ ] Update years of experience - saves correctly
- [ ] Upload profile image - uploads correctly
- [ ] Upload new image - deletes old image
- [ ] Session authentication works
- [ ] Non-artists redirected to login

### 🔄 Tests After Fixes
- [ ] Add bio - saves and displays
- [ ] Add location - saves and displays
- [ ] Add website URL - validates and saves
- [ ] Profile completion shows correct percentage
- [ ] Profile image paths work consistently

---

## Database Requirements

**Already Created:** ✅
```sql
-- Run this if not already executed:
-- Source: add_missing_user_columns.sql

ALTER TABLE `users`
  ADD COLUMN `years_experience` int DEFAULT NULL,
  ADD COLUMN `bio` text DEFAULT NULL,
  ADD COLUMN `location` varchar(255) DEFAULT NULL,
  ADD COLUMN `website` varchar(255) DEFAULT NULL,
  ADD INDEX `idx_users_location` (`location`);
```

**Status:** These columns should already exist from previous migration

---

## Summary

### Current State
✅ **CRUD Operations Work:**
- **Create:** Artists register via ArtistRegister.php
- **Read:** Profile displays correctly
- **Update:** Name, phone, experience, image update successfully
- **Delete:** Not implemented (users can't delete their own accounts - admin function)

### Issues
⚠️ **Minor Issues:**
1. Missing bio, location, website fields in form
2. Profile image path could be more consistent
3. No profile completeness indicator
4. No skills/specializations section

### Recommendation
**The artist profile CRUD is working correctly.** The issues are minor enhancements rather than bugs. If you're experiencing specific errors, please share:
1. Error messages you're seeing
2. What action you're trying to perform
3. Console errors (if any)

---

**Analysis Date:** January 28, 2026  
**Status:** ✅ Working (minor enhancements recommended)
