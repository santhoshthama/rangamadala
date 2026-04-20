# Task 5 (Rate Revision Log + Threshold Business Rules)

This task pair:
- Task 1: Log each rate update (CRUD read + optional delete).
- Task 2: Apply business validation on large rate changes.

## Existing files/functions/lines to update

1. Service edit controller
- File: app/controllers/ServiceProviderProfile.php
- Function editService: line 245
- Update call to model.updateService: line 363

2. Service model update flow
- File: app/models/M_service_provider.php
- Function updateService: line 510
- Function getServiceDetails: line 453
- Function buildDetailPayload: line 560

3. Service edit view
- File: app/views/service_edit_service.view.php
- Rate type/select anchor: line 39
- Rate input anchor: line 46
- Save button anchor: line 503

## 0) DB Migration (new table)

~~~sql
CREATE TABLE service_rate_revisions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    old_rate DECIMAL(10,2) NULL,
    new_rate DECIMAL(10,2) NOT NULL,
    change_reason VARCHAR(255) NULL,
    changed_by INT NOT NULL,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_service_id (service_id)
);
~~~

## 1) View: add optional reason field in edit form

File: app/views/service_edit_service.view.php
Insert below rate input (around line 46):

~~~php
<div class="form-group">
    <label class="form-label">Rate Change Reason (required for large increases)</label>
    <input type="text" name="rate_change_reason" class="form-input" maxlength="255" placeholder="Optional unless rate increase > 30%">
</div>
~~~

## 2) Controller: pass reason to model update

File: app/controllers/ServiceProviderProfile.php
Function: editService (line 245)

Before calling updateService(), add reason to extras:

~~~php
$extras['rate_change_reason'] = trim($_POST['rate_change_reason'] ?? '');
~~~

## 3) Model: add revision log helper

File: app/models/M_service_provider.php
Add new method near updateService (line 510):

~~~php
private function logRateRevision(int $serviceId, ?float $oldRate, float $newRate, ?string $reason, int $changedBy): bool
{
    $this->db->query("INSERT INTO service_rate_revisions (service_id, old_rate, new_rate, change_reason, changed_by)
                      VALUES (:service_id, :old_rate, :new_rate, :change_reason, :changed_by)");
    $this->db->bind(':service_id', $serviceId);
    $this->db->bind(':old_rate', $oldRate);
    $this->db->bind(':new_rate', $newRate);
    $this->db->bind(':change_reason', $reason);
    $this->db->bind(':changed_by', $changedBy);
    return $this->db->execute();
}
~~~

## 4) Model: enforce business rules inside updateService

File: app/models/M_service_provider.php
Function: updateService (line 510)

Inside updateService(), before upsertServiceDetail:

1. Fetch current detail rate:
~~~php
$currentDetails = $this->getServiceDetails($service_id, $service_type_name);
$oldRate = isset($currentDetails->rate_per_hour) ? (float)$currentDetails->rate_per_hour : null;
$newRate = isset($extras['rate']) ? (float)$extras['rate'] : null;
$reason = trim((string)($extras['rate_change_reason'] ?? ''));
~~~

2. Business rules:
~~~php
if ($oldRate !== null && $newRate !== null && $oldRate > 0) {
    $decreasePct = (($oldRate - $newRate) / $oldRate) * 100;
    $increasePct = (($newRate - $oldRate) / $oldRate) * 100;

    // Block decrease > 50%
    if ($decreasePct > 50) {
        return false;
    }

    // Require reason for increase > 30%
    if ($increasePct > 30 && $reason === '') {
        return false;
    }
}
~~~

3. After successful upsertServiceDetail(...), log revision:
~~~php
if ($newRate !== null && ($oldRate === null || $oldRate != $newRate)) {
    $changedBy = (int)($_SESSION['user_id'] ?? 0);
    $this->logRateRevision((int)$service_id, $oldRate, $newRate, $reason !== '' ? $reason : null, $changedBy);
}
~~~

## 5) Optional: show revision history

Add method in model:
~~~php
public function getRateRevisions(int $serviceId)
{
    $this->db->query("SELECT * FROM service_rate_revisions WHERE service_id = :service_id ORDER BY changed_at DESC");
    $this->db->bind(':service_id', $serviceId);
    return $this->db->resultSet();
}
~~~

Then in ServiceProviderProfile::editService() load revisions and pass to view.

In app/views/service_edit_service.view.php, render revisions below Save button.

## Task 2 Result Criteria

- YES: blocks >50% decrease and asks reason for >30% increase.
- NO: allows out-of-policy changes.
