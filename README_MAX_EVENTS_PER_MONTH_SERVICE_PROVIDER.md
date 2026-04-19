# Service Provider Viva Task: Add `Max Events Per Month`

This guide explains exactly what to update for the Service Capacity task.

## Goal
Add a new field called `Max Events Per Month` to the service provider profile and make the system behave like this:
1. Save the value in the database
2. Show it in the edit form
3. Show it in the profile view
4. Validate that it is a positive integer
5. Auto-mark the provider as unavailable when the value is `0`

---

## What files to update

1. [app/views/service_edit_basic_info.view.php](app/views/service_edit_basic_info.view.php)
2. [app/controllers/ServiceProviderProfile.php](app/controllers/ServiceProviderProfile.php)
3. [app/models/M_service_provider.php](app/models/M_service_provider.php)
4. [app/views/service_provider_profile.view.php](app/views/service_provider_profile.view.php)
5. Your SQL schema file where `serviceprovider` is created, usually [rangamadala_db.sql](rangamadala_db.sql)

---

## Step 1: Add the database column

### File
- [rangamadala_db.sql](rangamadala_db.sql)
- Table block starts around line 256

### What to add
Add a new nullable integer column inside the `serviceprovider` table:

```sql
`max_events_per_month` int DEFAULT NULL,
```

### Where to place it
Put it near the other provider profile columns, for example after `availability_notes`.

### Why
The app cannot save or display the field unless the database has a column for it.

### If you also maintain setup dumps
If your project uses other schema files for fresh setup, add the same column there too:
- [database_setup.sql](database_setup.sql)
- [final_shashindu_clean.sql](final_shashindu_clean.sql)
- [rangamadala_complete_setup.sql](rangamadala_complete_setup.sql)

---

## Step 2: Show the field in the edit form

### File
- [app/views/service_edit_basic_info.view.php](app/views/service_edit_basic_info.view.php)

### Function / section
- This is the edit form view used by `editBasicInfo()`
- Add the field in the Basic Information section around lines `78` to `87`

### Current anchor lines
- Birthday field: line `78`
- Years of Experience field: line `86`

### What to add
Insert a new form group near the birthday and years fields:

```php
<div class="form-group">
    <label class="form-label">Max Events Per Month <span class="required">*</span></label>
    <input type="number" name="max_events_per_month" class="form-input" min="0" step="1"
        value="<?php echo htmlspecialchars($data['provider']->max_events_per_month ?? ''); ?>" required>
</div>
```

### Why
This lets the service provider edit the value from the profile settings page.

### Important UI rule
Use `type="number"`, `min="0"`, and `step="1"` so the browser prevents invalid decimals.

---

## Step 3: Validate and save in the controller

### File
- [app/controllers/ServiceProviderProfile.php](app/controllers/ServiceProviderProfile.php)

### Function
- `editBasicInfo()` around line `551`
- The model call is around line `565`

### What to update
Inside the POST block in `editBasicInfo()`:
1. Read `$_POST['max_events_per_month']`
2. Validate it as an integer
3. Reject negative values
4. If it is `0`, auto-set availability to `0`
5. Pass it into `updateBasicInfo()`

### Suggested validation logic

```php
$maxEventsRaw = trim($_POST['max_events_per_month'] ?? '');

if ($maxEventsRaw === '' || !ctype_digit($maxEventsRaw)) {
    $_SESSION['error'] = 'Max Events Per Month must be a positive integer or 0.';
    header("Location: " . ROOT . "/ServiceProviderProfile/editBasicInfo?id=" . $provider_id);
    exit;
}

$maxEventsPerMonth = (int)$maxEventsRaw;

if ($maxEventsPerMonth < 0) {
    $_SESSION['error'] = 'Max Events Per Month cannot be negative.';
    header("Location: " . ROOT . "/ServiceProviderProfile/editBasicInfo?id=" . $provider_id);
    exit;
}

$availability = (int)($_POST['availability'] ?? 0);
if ($maxEventsPerMonth === 0) {
    $availability = 0;
}
```

### Then pass it to the model
Add the new argument when calling `updateBasicInfo()`.

### Why
This is the business rule part of the viva task. It shows real logic, not just a form field.

---

## Step 4: Save the field in the model

### File
- [app/models/M_service_provider.php](app/models/M_service_provider.php)

### Functions
1. `getProviderById()` around line `80`
2. `updateBasicInfo()` around line `834`

### 4.1 Read the value in `getProviderById()`
Add `sp.max_events_per_month` to the SELECT list in the provider fetch query.

### Why
The edit form and profile page both load provider data from this method.

### 4.2 Save the value in `updateBasicInfo()`
In the `serviceprovider` update SQL, add:

```php
max_events_per_month = :max_events_per_month,
```

Then bind it before execute:

```php
$this->db->bind(':max_events_per_month', $max_events_per_month);
```

### 4.3 Auto-mark unavailable when zero
There are two clean ways to do this:

#### Option A: Controller handles it
- If max events is `0`, set `availability = 0` before calling the model

#### Option B: Model handles it
- If `max_events_per_month === 0`, override availability inside `updateBasicInfo()`

### Recommended approach
Use **Option A** because the controller is the business-rule layer and the model stays focused on persistence.

### Why
The model must persist the new field, and the controller must enforce the capacity rule.

---

## Step 5: Display the field in the profile view

### File
- [app/views/service_provider_profile.view.php](app/views/service_provider_profile.view.php)

### Current anchor lines
- Availability block: around line `158`
- Availability Notes block: around line `164`
- Years experience stat: around line `694`

### What to add
Show the field in the profile details section, near availability or location.

Example:

```php
<div class="form-group">
    <label>Max Events Per Month</label>
    <input type="text" value="<?php echo !empty($data['provider']->max_events_per_month) || $data['provider']->max_events_per_month === '0' ? htmlspecialchars($data['provider']->max_events_per_month) : 'Not provided'; ?>" readonly>
</div>
```

### Optional extra UI logic
If you want the profile to visually explain the rule, add a note like:
- `0 means unavailable`
- `This provider is currently unavailable because the capacity is set to 0`

### Why
This is needed so the value is visible after saving.

---

## Step 6: Best business logic wording for viva

If the examiner asks what the logic does, say this:

- The provider sets the maximum number of events they can handle in a month.
- The system validates that the value is a whole number.
- If the value is `0`, the provider is automatically marked unavailable.
- That prevents users from booking a provider who has no remaining capacity.

---

## Exact line summary

### [app/views/service_edit_basic_info.view.php](app/views/service_edit_basic_info.view.php)
- Add field near line `78` to `87`
- Use the existing Basic Information section

### [app/controllers/ServiceProviderProfile.php](app/controllers/ServiceProviderProfile.php)
- Update `editBasicInfo()` around line `551`
- Update the model call around line `565`

### [app/models/M_service_provider.php](app/models/M_service_provider.php)
- Update `getProviderById()` around line `80`
- Update `updateBasicInfo()` around line `834`

### [app/views/service_provider_profile.view.php](app/views/service_provider_profile.view.php)
- Add display field near the profile details block around line `158`

### [rangamadala_db.sql](rangamadala_db.sql)
- Add `max_events_per_month` inside the `serviceprovider` table creation block around line `256`

---

## Suggested final viva implementation order

1. Add DB column
2. Update model fetch and save methods
3. Update controller validation and auto-unavailable rule
4. Add form field in edit view
5. Show the field in profile view

---

## Best short project title for this task
Use:
- `Service Capacity Control`

This sounds more professional than just “add field”.
