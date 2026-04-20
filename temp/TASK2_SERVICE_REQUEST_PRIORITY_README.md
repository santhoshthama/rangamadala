# Task 2 (Service Request Priority + Rule)

This task pair:
- Task 1: Add CRUD field priority (Low/Medium/High) to service requests.
- Task 2: Add business rule for High priority.

## Existing files/functions/lines to update

1. Request form view
- File: app/views/service_request_form.view.php
- Form anchor: line 128
- Hidden provider_id: line 129
- Hidden service_type: line 156
- Hidden start/end date: lines 192-193

2. Request controller
- File: app/controllers/ServiceProviderRequest.php
- Function submit: line 17
- Function respond: line 191 (optional if you want priority edits later)

3. Request model
- File: app/models/M_service_request.php
- Function createRequest: line 13
- INSERT query anchor: line 21
- Ongoing list query (if you show priority): line 844 in getOngoingServices

4. Provider dashboard (optional display)
- File: app/controllers/ServiceProviderDashboard.php
- Ongoing services load: line 76
- File: app/views/service_provider_dashboard.view.php
- Ongoing Services heading: line 143

## 0) DB Migration

~~~sql
ALTER TABLE service_requests
ADD COLUMN priority ENUM('Low','Medium','High') NOT NULL DEFAULT 'Medium' AFTER service_type;
~~~

## 1) View: add priority field in request form

File: app/views/service_request_form.view.php
Insert inside form near service_type/start_date area.

~~~php
<div class="form-group" style="margin-top: 12px;">
    <label for="prioritySelect">Priority</label>
    <select id="prioritySelect" name="priority" class="form-input" required>
        <option value="Low">Low</option>
        <option value="Medium" selected>Medium</option>
        <option value="High">High</option>
    </select>
</div>
~~~

## 2) Controller: read and validate priority

File: app/controllers/ServiceProviderRequest.php
Function: submit() line 17

Add this near other POST input mapping:

~~~php
$priority = trim($_POST['priority'] ?? 'Medium');
if (!in_array($priority, ['Low', 'Medium', 'High'], true)) {
    $priority = 'Medium';
}
~~~

Then include priority in request data array passed to model createRequest.

Example block to add in payload:

~~~php
'priority' => $priority,
~~~

## 3) Model: persist priority in createRequest

File: app/models/M_service_request.php
Function: createRequest line 13

Update INSERT columns to include priority, and add bind:

~~~php
$this->db->bind(':priority', $data['priority'] ?? 'Medium');
~~~

If your query currently has explicit column list, add priority in both column list and values list.

## 4) Show priority in ongoing dashboard list (optional but recommended)

### 4.1 Ensure model query selects priority
- File: app/models/M_service_request.php
- Function: getOngoingServices line 844
- Add sr.priority in SELECT list.

### 4.2 Show in view
- File: app/views/service_provider_dashboard.view.php
- Section: Ongoing Services list near line 143

Add this under activity-title:

~~~php
<?php if (!empty($service->priority)): ?>
    <div class="activity-time">Priority: <?= htmlspecialchars($service->priority) ?></div>
<?php endif; ?>
~~~

## Task 2 Extension (Business Logic)

Rule:
- If priority is High, start date must be at least 2 days from today.

Add in submit() before model createRequest call:

~~~php
if ($priority === 'High') {
    $startTs = strtotime((string)($_POST['start_date'] ?? ''));
    $minTs = strtotime('+2 days');
    if (!$startTs || $startTs < $minTs) {
        $_SESSION['error'] = 'High priority requests must start at least 2 days from today.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders'));
        exit;
    }
}
~~~

Optional extension:
- Sort ongoing services by priority first (High > Medium > Low) inside getOngoingServices query ORDER BY.
