# Add Birthday Field for Service Provider Profile

This guide shows exactly what to change, where to change it, and why.

## Goal
Add a new birthday field for service providers so it can:
1. Save in database
2. Validate on submit
3. Show in profile
4. Edit in basic info form

## Important Note About Line Numbers
Line numbers below match the current codebase state on 2026-04-19. If code moves, use the nearby anchor text shown in each step.

---

## Step 1: Add birthday column in database

### File 1 (main runtime DB)
- File: `rangamadala_db.sql`
- Around line: `256` (inside `CREATE TABLE IF NOT EXISTS serviceprovider`)

Add this column near phone/location:

```sql
`birthday` date DEFAULT NULL,
```

Why:
- Birthday must exist in DB before app code can save/read it.

### File 2-4 (setup dumps for fresh installs)
Repeat the same column addition in:
- `database_setup.sql` around line `255`
- `final_shashindu_clean.sql` around line `256`
- `rangamadala_complete_setup.sql` around line `257`

Why:
- Keeps all setup SQL files consistent for new environments.

### If production DB already exists
Run migration SQL:

```sql
ALTER TABLE serviceprovider
ADD COLUMN birthday DATE NULL AFTER phone;
```

Why:
- Existing databases need ALTER, not CREATE TABLE edits.

---

## Step 2: Read birthday in provider profile fetch

### File
- `app/models/M_service_provider.php`
- Function: `getProviderById`
- Around line: `48`
- Add in SELECT list near line `76`

Find this block:

```php
sp.professional_title,
sp.location,
sp.social_media_link,
```

Change to:

```phpz
sp.professional_title,
sp.location,
sp.social_media_link,
sp.birthday,
```

Why:
- Makes birthday available in both profile display and edit form data.

---

## Step 3: Save birthday during full profile save flow

### File
- `app/models/M_service_provider.php`
- Function: `saveFullProfile`
- Around line: `189`

### 3.1 Add birthday into serviceprovider column list
Around line `229`, change:

```php
$serviceProviderColumns = ['user_id', 'professional_title', 'location', 'social_media_link', 'availability', 'availability_notes'];
```

To:

```php
$serviceProviderColumns = ['user_id', 'professional_title', 'location', 'social_media_link', 'birthday', 'availability', 'availability_notes'];
```

### 3.2 Add placeholder in VALUES
Find SQL VALUES part and include `:birthday`:

From:

```php
VALUES (:user_id, :professional_title, :location, :social_media_link, :availability, :availability_notes
```

To:

```php
VALUES (:user_id, :professional_title, :location, :social_media_link, :birthday, :availability, :availability_notes
```

### 3.3 Update ON DUPLICATE KEY UPDATE
Add:

```php
birthday = VALUES(birthday),
```

after social media link update line.

### 3.4 Bind birthday
Add bind near other binds:

```php
$this->db->bind(':birthday', !empty($provider['birthday']) ? $provider['birthday'] : null);
```

Why:
- Ensures birthday persists during complete profile create/update path, not only edit-basic-info path.

---

## Step 4: Save birthday in basic info update method

### File
- `app/models/M_service_provider.php`
- Function: `updateBasicInfo`
- Around line: `691`

### 4.1 Update method signature
From:

```php
public function updateBasicInfo($provider_id, $full_name, $professional_title, $email, $phone, 
                                $location, $website, $years_experience, $professional_summary, 
                                $availability, $availability_notes)
```

To:

```php
public function updateBasicInfo($provider_id, $full_name, $professional_title, $email, $phone, 
                                $location, $website, $birthday, $years_experience, $professional_summary, 
                                $availability, $availability_notes)
```

### 4.2 Update SQL set list
In serviceprovider UPDATE SQL (around line `721`), add:

```php
birthday = :birthday,
```

### 4.3 Bind value
Before execute, add:

```php
$this->db->bind(':birthday', $birthday);
```

Why:
- This is the method called by edit basic info form, so birthday must be saved here.

---

## Step 5: Validate birthday in controller before save

### File
- `app/controllers/ServiceProviderProfile.php`
- Function: `editBasicInfo`
- Around line: `551`
- Update call at line around `563`

Add validation before calling model update:

```php
$birthday = trim($_POST['birthday'] ?? '');

if ($birthday !== '') {
    $dt = DateTime::createFromFormat('Y-m-d', $birthday);
    $validFormat = $dt && $dt->format('Y-m-d') === $birthday;

    if (!$validFormat) {
        $_SESSION['error'] = 'Birthday must be a valid date (YYYY-MM-DD).';
        header("Location: " . ROOT . "/ServiceProviderProfile/editBasicInfo?id=" . $provider_id);
        exit;
    }

    if ($birthday > date('Y-m-d')) {
        $_SESSION['error'] = 'Birthday cannot be in the future.';
        header("Location: " . ROOT . "/ServiceProviderProfile/editBasicInfo?id=" . $provider_id);
        exit;
    }

    $age = (new DateTime($birthday))->diff(new DateTime('today'))->y;
    if ($age < 16) {
        $_SESSION['error'] = 'Minimum age is 16 years.';
        header("Location: " . ROOT . "/ServiceProviderProfile/editBasicInfo?id=" . $provider_id);
        exit;
    }
}
```

Then pass birthday into model call:

From:

```php
$_POST['social_media_link'] ?? '',
$_POST['years_experience'],
```

To:

```php
$_POST['social_media_link'] ?? '',
$birthday !== '' ? $birthday : null,
$_POST['years_experience'],
```

Why:
- Prevents invalid/future dates and enforces business rule.
- Keeps validation server-side (secure and reliable).

---

## Step 6: Add birthday field to Edit Basic Info form

### File
- `app/views/service_edit_basic_info.view.php`
- Around line: `65` to `78`

Add this form group (best place: after Phone Number or before Years of Experience):

```php
<div class="form-group">
    <label class="form-label">Birthday</label>
    <input type="date" name="birthday" class="form-input"
        value="<?php echo htmlspecialchars($data['provider']->birthday ?? ''); ?>"
        max="<?php echo date('Y-m-d'); ?>">
</div>
```

Why:
- Allows user to edit birthday.
- `max=today` gives basic client-side future-date prevention.

---

## Step 7: Show birthday in read-only profile page

### File
- `app/views/service_provider_profile.view.php`
- Around lines: `121` to `131` (Phone/Location/Social area)

Add a new readonly field in the same grid:

```php
<div class="form-group">
    <label>Birthday</label>
    <input type="text" value="<?php echo !empty($data['provider']->birthday) ? htmlspecialchars(date('d M Y', strtotime($data['provider']->birthday))) : 'Not provided'; ?>" readonly>
</div>
```

Optional: also show in left summary card under phone/location.

Why:
- Completes requirement to show birthday in profile.

---

## Step 8: Optional browse/list exposure

If Production Manager browse cards should show birthday too:
- Add `sp.birthday` in `getAllProvidersWithServices` query in `app/models/M_service_provider.php` around line `851`.
- Render in browse card view files.

Why:
- Needed only if birthday should be public beyond owner profile.

---

## Step 9: Quick verification checklist

1. Open Edit Basic Info, set valid birthday, save.
2. Confirm birthday appears on profile page.
3. Try future birthday, ensure error blocks save.
4. Try invalid date by manual POST, ensure server blocks save.
5. Keep birthday empty and save, ensure no crash.

---

## Minimal Change Summary
You must touch these files for the full feature:
1. `app/controllers/ServiceProviderProfile.php`
2. `app/models/M_service_provider.php`
3. `app/views/service_edit_basic_info.view.php`
4. `app/views/service_provider_profile.view.php`
5. `rangamadala_db.sql`
6. `database_setup.sql`
7. `final_shashindu_clean.sql`
8. `rangamadala_complete_setup.sql`

If you want, next I can apply all these edits for you directly in code.
