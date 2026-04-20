# Service Provider Location Add / Update / Filter Guide

This is a quick reference for every place where service-provider location data is added, updated, displayed, or filtered.

Use this together with `temp/Admin_Check.php`, which already contains a compact scratchpad of the same location snippets.

---

## 1) Database / schema

### `temp/rangamadala_db.sql`
- `serviceprovider.location` is defined at line 737.
- `serviceprovider` seed insert includes `location` at line 751.
- `users.location` is defined at line 1122.
- `users` seed insert includes `location` at line 1137.
- `idx_users_location` is created at line 1521.
- `service_theater_details.location_address` is defined at line 974.
- `service_theater_details` seed insert includes `location_address` at line 982.

What to update:
- Keep `serviceprovider.location` for provider profile location.
- Keep `service_theater_details.location_address` for the specific service venue address.
- If you add a new location-like field, decide whether it belongs to the provider profile table or a service detail table.

---

## 2) Add / save location during provider registration

### `app/controllers/ServiceProviderRegister.php`
- `location` is collected into `$provider` at line 131.
- `location_address` is collected for service detail data at line 354.

Existing code:
```php
'location' => trim($_POST['location'] ?? ''),
```

What it does:
- Takes the location from the registration form and stores it in the provider payload.

### `app/views/service_provider_register.view.php`
- Location input appears around lines 124-125.

Existing code:
```php
<label class="form-label">Location</label>
<input type="text" name="location" class="form-input" placeholder="City, Country" value="<?= htmlspecialchars($formData['location'] ?? '') ?>">
```

What to update:
- If you rename the field, update both the controller key and the form `name` attribute.
- If the field becomes required, add validation in `ServiceProviderRegister::submit()`.

---

## 3) Save location into the provider profile row

### `app/models/M_service_provider.php`

#### `getProviderById($user_id)`
- Starts around line 80.
- Reads `sp.location` at line 91.

Existing code:
```php
sp.location,
```

What it does:
- Loads the provider location for profile and detail views.

#### `saveFullProfile($provider, $user_id, $services = [], $projects = [])`
- Starts around line 255.
- `location` is included in the serviceprovider column list around line 264.
- `:location` is inserted in the SQL around line 271.
- `location = VALUES(location)` is updated around line 280.
- `:location` is bound around line 294.

Existing code:
```php
$serviceProviderColumns = ['user_id', 'professional_title', 'location', 'social_media_link', 'birthday','availability', 'availability_notes'];
```

What it does:
- Saves the provider location during registration / full profile save.

#### `updateBasicInfo(...)`
- Starts around line 829.
- `location` is updated in the SQL around line 791.
- `:location` is bound around line 808.

Existing code:
```php
location = :location,
```

What it does:
- Updates the provider location when the user edits basic profile information.

What to update if adding a new field:
- Add the new column to the `serviceprovider` table.
- Add the field to the insert/upsert column list in `saveFullProfile()`.
- Add the field to the update SQL and bind list in `updateBasicInfo()`.
- Add the field to `getProviderById()` so the profile can display it.

---

## 4) Update location in the edit profile flow

### `app/controllers/ServiceProviderProfile.php`
- `editBasicInfo()` starts around line 551.
- The update call using `location` is around line 565.

Existing code:
```php
$_POST['location'],
```

What it does:
- Pulls the edited location from the form and sends it to the model update method.

### `app/views/service_edit_basic_info.view.php`
- Location field is in the Basic Information section around lines 61-63.

Existing code:
```php
<label class="form-label">Location <span class="required">*</span></label>
<input type="text" name="location" class="form-input" 
    value="<?php echo htmlspecialchars($data['provider']->location); ?>" required>
```

What to update:
- Keep the form field name aligned with the controller and model.
- If the field should not be required, remove `required` from the input and adjust validation.

---

## 5) Filter providers by location

### `app/controllers/BrowseServiceProviders.php`
- Location filter is added to `$filters` around lines 10-16.
- The location dropdown list is loaded around line 22.

Existing code:
```php
'location' => $_GET['location'] ?? '',
```

What it does:
- Passes the selected location into the browse filter state.

### `app/models/M_service_provider.php`
- `getAllProvidersWithServices($filters = [])` starts around line 920.
- `sp.location` is selected at line 933.
- The location SQL filter is at line 961.

Existing code:
```php
sp.location,
```

Existing filter code:
```php
$sql .= " AND sp.location LIKE :location";
```

What it does:
- Uses provider location to filter browse/search results.

### `app/views/browse_service_providers.view.php`
- Provider location is displayed in the cards around line 161.

Existing code:
```php
<span><i class="bx bx-map"></i> <?= htmlspecialchars($provider->location) ?></span>
```

What to update:
- If you rename the filter field, update the controller, model bind, and view form name together.
- If you want exact-match filtering instead of partial search, change `LIKE` to `=` in the model.

---

## 6) Show location on provider profile pages

### `app/views/service_provider_profile.view.php`
- Profile summary location is shown at lines 51-52.
- Read-only basic-info location is shown around lines 129-132.
- Service venue address appears separately around lines 303-307.

Existing code:
```php
<?php if (!empty($data['provider']->location)): ?>
<p><i class="bx bx-map-marker-alt"></i> <?php echo htmlspecialchars($data['provider']->location); ?></p>
<?php endif; ?>
```

Existing code:
```php
<label>Location</label>
<input type="text" value="<?php echo htmlspecialchars($data['provider']->location ?? 'Not provided'); ?>" readonly>
```

What it does:
- Displays the provider's profile location in the summary and basic info card.

### `app/views/service_provider_detail.view.php`
- Provider location appears at line 65.
- Contact information location appears at lines 130-132.
- Service venue address appears at lines 241-243.

Existing code:
```php
<?= htmlspecialchars($data['provider']->location) ?>
```

What it does:
- Shows the provider location to visitors on the public detail page.

---

## 7) Service venue address vs provider location

### `app/models/M_service_provider.php`
- `location_address` is assigned for theater service details at line 531.

Existing code:
```php
'location_address' => $svc['location_address'] ?? null,
```

What it does:
- Stores the service-specific venue address, which is different from the provider's profile location.

### `app/views/service_provider_profile.view.php`
- Service venue address display is around lines 303-307.

### `app/views/service_provider_detail.view.php`
- Service venue address display is around lines 241-243.

What to remember:
- `location` = provider profile / general area
- `location_address` = service-specific venue address

---

## 8) Quick change checklist

If you change the provider location field:
1. Update the DB column if needed.
2. Update registration form input name and controller collection.
3. Update `saveFullProfile()` and `updateBasicInfo()` in the model.
4. Update profile display views.
5. Update browse filtering if the field is used in search.

If you change the service venue address field:
1. Update the detail-table schema.
2. Update the service detail payload in `buildDetailPayload()`.
3. Update the service detail view.

---

## 9) Admin_Check.php reference

### `temp/Admin_Check.php`
This temp file currently contains a combined reference snippet for:
- Location filter UI
- Provider location display
- Registration location input

It is useful as a scratchpad, but the real source files are the ones listed above.
