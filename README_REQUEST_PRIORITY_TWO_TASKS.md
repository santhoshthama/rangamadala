# Viva Plan: Request Handling (Task 1 + Task 2)

This guide gives you two connected tasks:
- Task 1 is simple: add a new request field.
- Task 2 is an extension: filter existing request data using that same field.

Both tasks are based on your existing Service Request flow.

---

## Task Summary

## Task 1 (Simple)
Add a new field: `priority` (Low / Normal / High) to service requests.

## Task 2 (Extension)
Show filtered requests by priority in Service Provider Requests page.

This is ideal for viva because Task 2 directly builds on Task 1.

---

## Current Request Flow (already in your project)

1. Request submit endpoint: [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php#L17) (`submit()`)
2. Request payload creation: [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php#L94)
3. Save request model method: [app/models/M_service_request.php](app/models/M_service_request.php#L13) (`createRequest()`)
4. Provider request listing query: [app/models/M_service_request.php](app/models/M_service_request.php#L92) (`getRequestsByProvider()`)
5. Provider request page: [app/views/service_requests.view.php](app/views/service_requests.view.php)
6. Request form modal view: [app/views/service_request_form.view.php](app/views/service_request_form.view.php#L128)

---

## Files You Will Touch

1. [app/views/service_request_form.view.php](app/views/service_request_form.view.php)
2. [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php)
3. [app/models/M_service_request.php](app/models/M_service_request.php)
4. [app/views/service_requests.view.php](app/views/service_requests.view.php)
5. SQL schema file for `service_requests` table:
   - [rangamadala_db.sql](rangamadala_db.sql#L461)
   - and keep setup files in sync if needed:
     - [database_setup.sql](database_setup.sql#L452)
     - [final_shashindu_clean.sql](final_shashindu_clean.sql#L461)
     - [rangamadala_complete_setup.sql](rangamadala_complete_setup.sql#L462)

---

## Task 1: Add `priority` Field

## Step 1.1 - Add DB column

### File
- [rangamadala_db.sql](rangamadala_db.sql#L461)

### Change
Inside `service_requests` table, add:

```sql
priority VARCHAR(20) DEFAULT 'normal',
```

Use lowercase values in DB: `low`, `normal`, `high`.

If DB already exists, run migration:

```sql
ALTER TABLE service_requests
ADD COLUMN priority VARCHAR(20) DEFAULT 'normal' AFTER service_type;
```

---

## Step 1.2 - Add field in request form

### File
- [app/views/service_request_form.view.php](app/views/service_request_form.view.php#L156)

### Current anchors
- Form starts at [app/views/service_request_form.view.php](app/views/service_request_form.view.php#L128)
- `service_type` hidden input at [app/views/service_request_form.view.php](app/views/service_request_form.view.php#L156)
- date hidden inputs around [app/views/service_request_form.view.php](app/views/service_request_form.view.php#L192)

### Change
Add a select input near other request details:

```php
<div class="form-group">
    <label>Priority <span class="required">*</span></label>
    <select name="priority" class="form-input" required>
        <option value="normal" selected>Normal</option>
        <option value="high">High</option>
        <option value="low">Low</option>
    </select>
</div>
```

---

## Step 1.3 - Read and validate in controller

### File
- [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php#L17)

### Function
- `submit()`

### Current anchors
- request array starts at [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php#L94)
- save call at [app/controllers/ServiceProviderRequest.php](app/controllers/ServiceProviderRequest.php#L157)

### Change
1. Add priority to request payload:

```php
'priority' => strtolower(trim($_POST['priority'] ?? 'normal')),
```

2. Validate allowed values before save:

```php
$allowedPriorities = ['low', 'normal', 'high'];
if (!in_array($request['priority'], $allowedPriorities, true)) {
    $errors[] = 'Invalid priority selected.';
}
```

---

## Step 1.4 - Save in model

### File
- [app/models/M_service_request.php](app/models/M_service_request.php#L13)

### Function
- `createRequest($data)`

### Change
In INSERT columns, add `priority`.
In VALUES, add `:priority`.
Bind value:

```php
$this->db->bind(':priority', $data['priority'] ?? 'normal');
```

---

## Step 1.5 - Return and show in provider request list

### File A
- [app/models/M_service_request.php](app/models/M_service_request.php#L92)

Ensure query includes `sr.priority` (or keep `sr.*`, which already includes it).

### File B
- [app/views/service_requests.view.php](app/views/service_requests.view.php#L84)

Add display badge in each request card:

```php
<?php $priority = strtolower((string)($req->priority ?? 'normal')); ?>
<span class="priority-badge priority-<?= htmlspecialchars($priority) ?>">
    <?= htmlspecialchars(ucfirst($priority)) ?>
</span>
```

Also add it as a data attribute on request item container:

```php
data-priority="<?= htmlspecialchars($priority) ?>"
```

---

## Task 2: Extension - Filter Existing Data by Priority

You can do Task 2 in two ways. For viva, do Option A first (easy), then mention Option B as upgrade.

## Option A (Frontend filter, easiest)

### File
- [app/views/service_requests.view.php](app/views/service_requests.view.php)

### Change
1. Add filter dropdown above request list:

```html
<select id="priorityFilter" onchange="applyPriorityFilter()">
  <option value="all">All Priorities</option>
  <option value="high">High</option>
  <option value="normal">Normal</option>
  <option value="low">Low</option>
</select>
```

2. Add JS function using existing DOM cards (`.request-item`) and `data-priority`:

```javascript
function applyPriorityFilter() {
  const selected = document.getElementById('priorityFilter').value;
  document.querySelectorAll('.request-item').forEach(item => {
    const pr = (item.getAttribute('data-priority') || 'normal').toLowerCase();
    const statusMatch = currentTab === 'all' || item.getAttribute('data-category') === currentTab;
    const priorityMatch = selected === 'all' || pr === selected;
    item.style.display = (statusMatch && priorityMatch) ? 'flex' : 'none';
  });
}
```

3. Call `applyPriorityFilter()` at the end of `switchTab()`.

This keeps your existing status tab system and adds one extra filter layer.

---

## Option B (Server-side filter, advanced extension)

### Files
- [app/controllers/ServiceRequests.php](app/controllers/ServiceRequests.php#L14)
- [app/models/M_service_request.php](app/models/M_service_request.php#L92)

### Change
1. In controller `index()`, read query param:

```php
$priority = strtolower(trim($_GET['priority'] ?? 'all'));
```

2. Pass to model method:

```php
$requests = $reqModel->getRequestsByProvider($_SESSION['user_id'], $priority);
```

3. Extend model function signature:

```php
public function getRequestsByProvider($provider_id, $priority = 'all')
```

4. Add SQL condition when priority is not all:

```php
if (in_array($priority, ['low','normal','high'], true)) {
    $sql .= " AND sr.priority = :priority";
    $this->db->bind(':priority', $priority);
}
```

---

## Viva Script (What to say)

- Task 1: I added a new `priority` field to service requests and persisted it from form to database.
- Task 2: I extended that by adding request filtering by priority in the provider dashboard.
- This improves operational triage by helping providers process urgent requests first.

---

## Quick Test Cases

1. Submit three requests with low, normal, high priorities.
2. Open provider requests page.
3. Verify priority badge appears on each request.
4. Apply filter: High -> only high requests should remain.
5. Apply filter: All -> all should return.
6. Try tampered POST priority (e.g., `critical`) -> should be rejected by validation.

---

## Minimal Task Scope (if examiner gives little time)

Implement only:
1. DB column
2. Form select
3. Controller validation
4. Model save bind
5. Show priority text on request card

Then mention filtering as extension.
