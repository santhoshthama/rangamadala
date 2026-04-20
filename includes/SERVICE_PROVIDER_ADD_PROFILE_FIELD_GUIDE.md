# Service Provider Profile: Add a New Field (Full Implementation Guide)

This guide explains how to add a **new field** to Service Provider Profile with:
- Database update
- Backend read/write update
- Edit form update
- Profile display update
- Optional signup update

It includes **exact files**, **functions**, **current line anchors**, and **existing vs updated code snippets**.

---

## 0) Choose your field first

Pick:
- Field label: e.g. `LinkedIn URL`
- DB column: e.g. `linkedin_url`
- Data type: e.g. `VARCHAR(255)`
- Table: usually `serviceprovider` for service-provider-specific profile details

In this guide, placeholders are used:
- `NEW_FIELD_LABEL`
- `new_field_column`

You can replace them with your real field name.

---

## 1) Database change (mandatory)

### File to update
- `temp/rangamadala_db.sql`

### Where
- Table definition starts around line **734**:
  `CREATE TABLE serviceprovider (...)`

### Existing code (from table structure)
```sql
CREATE TABLE `serviceprovider` (
  `user_id` int(11) NOT NULL,
  `professional_title` varchar(100) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `social_media_link` varchar(255) DEFAULT NULL,
  `professional_summary` text DEFAULT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `availability_notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `birthday` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Update to apply
Add your column to the table definition and existing DB using migration SQL.

```sql
-- 1) For schema dumps/new setup
-- add inside CREATE TABLE serviceprovider:
`new_field_column` varchar(255) DEFAULT NULL,

-- 2) For existing local DBs
ALTER TABLE `serviceprovider`
ADD COLUMN `new_field_column` varchar(255) DEFAULT NULL AFTER `social_media_link`;
```

Note:
- If field is numeric/date/boolean, choose proper type instead of `varchar(255)`.

---

## 2) Load field in profile read query

### File to update
- `app/models/M_service_provider.php`

### Function
- `getProviderById($user_id)` at around line **80**

### Existing code (snippet)
```php
$this->db->query("SELECT
                    sp.user_id,
                    sp.professional_title,
                    sp.location,
                    sp.birthday,
                    sp.social_media_link,
                    sp.birthday,
                    {$yearsExpr} AS years_experience,
                    sp.availability,
                    sp.availability_notes,
                    sp.created_at,
                    sp.updated_at,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.nic_number,
                    u.nic_photo,
                    u.nic_photo_back,
                    u.profile_image,
                    {$summaryExpr} AS professional_summary
                  FROM serviceprovider sp
                  INNER JOIN users u ON u.id = sp.user_id
                  WHERE sp.user_id = :user_id");
```

### Update to apply
Add your new column in SELECT list:

```php
$this->db->query("SELECT
                    sp.user_id,
                    sp.professional_title,
                    sp.location,
                    sp.birthday,
                    sp.social_media_link,
                    sp.new_field_column,
                    sp.birthday,
                    {$yearsExpr} AS years_experience,
                    sp.availability,
                    sp.availability_notes,
                    sp.created_at,
                    sp.updated_at,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.nic_number,
                    u.nic_photo,
                    u.nic_photo_back,
                    u.profile_image,
                    {$summaryExpr} AS professional_summary
                  FROM serviceprovider sp
                  INNER JOIN users u ON u.id = sp.user_id
                  WHERE sp.user_id = :user_id");
```

---

## 3) Save field from edit form (controller)

### File to update
- `app/controllers/ServiceProviderProfile.php`

### Function
- `editBasicInfo()` around line **551**
- Call to model update around line **565**

### Existing code (call)
```php
$result = $model->updateBasicInfo(
    $provider_id,
    $_POST['full_name'],
    $_POST['professional_title'],
    $_POST['email'],
    $_POST['phone'],
    $_POST['location'],
    $_POST['social_media_link'] ?? '',
    $birthday !== '' ? $birthday : null,
    $_POST['years_experience'],
    $_POST['professional_summary'] ?? '',
    (int)($_POST['availability'] ?? 0),
    $_POST['availability_notes'] ?? ''
);
```

### Update to apply
Pass new field value to model:

```php
$result = $model->updateBasicInfo(
    $provider_id,
    $_POST['full_name'],
    $_POST['professional_title'],
    $_POST['email'],
    $_POST['phone'],
    $_POST['location'],
    $_POST['social_media_link'] ?? '',
    $_POST['new_field_column'] ?? '',
    $birthday !== '' ? $birthday : null,
    $_POST['years_experience'],
    $_POST['professional_summary'] ?? '',
    (int)($_POST['availability'] ?? 0),
    $_POST['availability_notes'] ?? ''
);
```

Important:
- You must also update the model function signature (next step), otherwise argument mismatch error occurs.

---

## 4) Save field in DB update function (model)

### File to update
- `app/models/M_service_provider.php`

### Function
- `updateBasicInfo(...)` around line **829**

### Existing code (signature)
```php
public function updateBasicInfo($provider_id, $full_name, $professional_title, $email, $phone,
                                $location, $website, $birthday, $years_experience, $professional_summary, 
                                $availability, $availability_notes) {
```

### Updated signature
```php
public function updateBasicInfo($provider_id, $full_name, $professional_title, $email, $phone,
                                $location, $website, $new_field_column, $birthday, $years_experience, $professional_summary,
                                $availability, $availability_notes) {
```

### Existing code (serviceprovider update SQL)
```php
$serviceProviderUpdateSql = "UPDATE serviceprovider SET
                     professional_title = :professional_title,
                     location = :location,
                     social_media_link = :social_media_link,
                     birthday = :birthday,
                     availability = :availability,
                     availability_notes = :availability_notes";
```

### Updated SQL + bind
```php
$serviceProviderUpdateSql = "UPDATE serviceprovider SET
                     professional_title = :professional_title,
                     location = :location,
                     social_media_link = :social_media_link,
                     new_field_column = :new_field_column,
                     birthday = :birthday,
                     availability = :availability,
                     availability_notes = :availability_notes";

// ...
$this->db->bind(':new_field_column', $new_field_column);
```

---

## 5) Add input in Edit Basic Info page

### File to update
- `app/views/service_edit_basic_info.view.php`

### Good insertion point
- Near social link field around lines **66-68**

### Existing code
```php
<div class="form-group">
    <label class="form-label">Social Media Link</label>
    <input type="url" name="social_media_link" class="form-input" 
        value="<?php echo htmlspecialchars($data['provider']->social_media_link ?? ''); ?>">
</div>
```

### Updated code
```php
<div class="form-group">
    <label class="form-label">Social Media Link</label>
    <input type="url" name="social_media_link" class="form-input" 
        value="<?php echo htmlspecialchars($data['provider']->social_media_link ?? ''); ?>">
</div>

<div class="form-group">
    <label class="form-label">NEW_FIELD_LABEL</label>
    <input type="text" name="new_field_column" class="form-input"
        value="<?php echo htmlspecialchars($data['provider']->new_field_column ?? ''); ?>">
</div>
```

If required field, add `required` and validate in controller.

---

## 6) Show field in profile display page

### File to update
- `app/views/service_provider_profile.view.php`

### Good insertion point
- Basic Information section around lines **133-158**

### Existing code (part)
```php
<div class="form-group">
    <label>Social Media Link</label>
    <input type="text" value="<?php echo $data['provider']->social_media_link ? htmlspecialchars($data['provider']->social_media_link) : 'Not provided'; ?>" readonly>
</div>
```

### Updated code
```php
<div class="form-group">
    <label>Social Media Link</label>
    <input type="text" value="<?php echo $data['provider']->social_media_link ? htmlspecialchars($data['provider']->social_media_link) : 'Not provided'; ?>" readonly>
</div>

<div class="form-group">
    <label>NEW_FIELD_LABEL</label>
    <input type="text" value="<?php echo $data['provider']->new_field_column ? htmlspecialchars($data['provider']->new_field_column) : 'Not provided'; ?>" readonly>
</div>
```

---

## 7) Optional: include this field during signup flow also

If you want field captured during registration (not only profile edit), update these too.

### Files
- `app/views/service_provider_register.view.php`
- `app/controllers/ServiceProviderRegister.php`
- `app/models/M_service_provider.php` in `saveFullProfile(...)` (around line **255**)

### Current anchor points
- Register view has related fields around lines **86**, **141**, **147**
- Register controller builds `$provider` at around line **122+** and calls `saveFullProfile(...)` at line **305**

### What to do
1. Add input in register view:
```php
<input type="text" name="new_field_column" class="form-input" value="<?= htmlspecialchars($formData['new_field_column'] ?? '') ?>">
```

2. Collect in register controller `$provider` array:
```php
'new_field_column' => trim($_POST['new_field_column'] ?? ''),
```

3. In model `saveFullProfile(...)`, include this in serviceprovider insert/upsert:
- Add `new_field_column` to `$serviceProviderColumns`
- Add `:new_field_column` in VALUES
- Add `new_field_column = VALUES(new_field_column)` in ON DUPLICATE KEY UPDATE
- Bind value:
```php
$this->db->bind(':new_field_column', $provider['new_field_column'] ?? null);
```

---

## 8) Validation checklist (recommended)

In `ServiceProviderProfile::editBasicInfo()`:
- Trim input: `$newField = trim($_POST['new_field_column'] ?? '');`
- Validate length/type
- Optional URL format if URL field: `filter_var($newField, FILTER_VALIDATE_URL)`

Example:
```php
$newField = trim($_POST['new_field_column'] ?? '');
if (strlen($newField) > 255) {
    $_SESSION['error'] = 'NEW_FIELD_LABEL is too long.';
    header("Location: " . ROOT . "/ServiceProviderProfile/editBasicInfo?id=" . $provider_id);
    exit;
}
```

---

## 9) Quick test plan

1. Run DB alter for `serviceprovider`.
2. Open edit page: `/ServiceProviderProfile/editBasicInfo?id={your_user_id}`
3. Enter new field value and save.
4. Verify value appears on profile page `/ServiceProviderProfile`.
5. Check DB row:
```sql
SELECT user_id, new_field_column
FROM serviceprovider
WHERE user_id = <your_user_id>;
```
6. (Optional) Verify registration path if implemented.

---

## 10) Common mistakes to avoid

- Updating view only, but not model SQL bind.
- Updating controller argument list, but not model function signature.
- Adding column in dump file only, but not executing `ALTER TABLE` in current DB.
- Naming mismatch between form `name="new_field_column"` and SQL bind `:new_field_column`.

---

## Files summary (minimum profile-only change)

1. `temp/rangamadala_db.sql` (or migration SQL): add DB column
2. `app/models/M_service_provider.php`:
   - `getProviderById()`
   - `updateBasicInfo()`
3. `app/controllers/ServiceProviderProfile.php`:
   - `editBasicInfo()`
4. `app/views/service_edit_basic_info.view.php`: add editable input
5. `app/views/service_provider_profile.view.php`: add read-only display

---

If you tell me the exact field name and type (example: `linkedin_url VARCHAR(255)`), I can generate the **exact final code** for each file with no placeholders.
