# Task 4 (Top Clients Notes + Access Rule)

This task pair:
- Task 1: Add note per top client relation (CRUD-style update/read).
- Task 2: Business rule: editable only if client has at least 2 bookings.

## Existing files/functions/lines to update

1. Dashboard controller
- File: app/controllers/ServiceProviderDashboard.php
- Function index: line 7
- Top clients load: line 77
- Data payload: line 89

2. Request model (top clients source)
- File: app/models/M_service_request.php
- Function getTopClients: line 870

3. Dashboard view (Top Clients UI)
- File: app/views/service_provider_dashboard.view.php
- Top Clients section starts around line 170

## 0) DB Migration (new table)

~~~sql
CREATE TABLE provider_client_notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    requester_email VARCHAR(255) NOT NULL,
    note TEXT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_provider_requester (provider_id, requester_email)
);
~~~

## 1) Model updates

### 1.1 Extend getTopClients to include note
File: app/models/M_service_request.php line 870

Join notes table by provider_id + requester_email and select note.

### 1.2 Add methods in M_service_request
Add below getTopClients:

~~~php
public function upsertClientNote($provider_id, $requester_email, $note)
{
    $this->db->query("INSERT INTO provider_client_notes (provider_id, requester_email, note)
                      VALUES (:provider_id, :requester_email, :note)
                      ON DUPLICATE KEY UPDATE note = VALUES(note), updated_at = NOW()");
    $this->db->bind(':provider_id', (int)$provider_id);
    $this->db->bind(':requester_email', trim($requester_email));
    $this->db->bind(':note', $note);
    return $this->db->execute();
}
~~~

## 2) Controller updates

File: app/controllers/ServiceProviderDashboard.php

### 2.1 Add save-note action in same controller
Add new method under index():

~~~php
public function saveClientNote()
{
    if (!isset($_SESSION['user_id']) || (($_SESSION['user_role'] ?? '') !== 'service_provider')) {
        header("Location: " . ROOT . "/Login");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: " . ROOT . "/ServiceProviderDashboard");
        exit;
    }

    $providerId = (int)$_SESSION['user_id'];
    $requesterEmail = trim($_POST['requester_email'] ?? '');
    $note = trim($_POST['client_note'] ?? '');

    $requestModel = new M_service_request();
    $requestModel->upsertClientNote($providerId, $requesterEmail, $note);

    $_SESSION['success'] = 'Client note saved.';
    header("Location: " . ROOT . "/ServiceProviderDashboard");
    exit;
}
~~~

## 3) View updates (Top Clients card)

File: app/views/service_provider_dashboard.view.php around line 170

Inside each top client card add form:

~~~php
<form method="POST" action="<?= ROOT ?>/ServiceProviderDashboard/saveClientNote" style="margin-top:10px;">
    <input type="hidden" name="requester_email" value="<?= htmlspecialchars($client->requester_email ?? '') ?>">
    <textarea name="client_note" class="form-input" rows="2" placeholder="Add internal note..."><?= htmlspecialchars($client->note ?? '') ?></textarea>
    <button type="submit" class="btn" style="margin-top:6px;">Save Note</button>
</form>
~~~

## Task 2 Extension (Business Logic)

Rule:
- Allow note editing only when booking_count >= 2.

### 4) Enforce in view

~~~php
<?php if ((int)($client->booking_count ?? 0) >= 2): ?>
    <!-- show editable form -->
<?php else: ?>
    <div class="activity-time">Notes unlock after 2 bookings.</div>
<?php endif; ?>
~~~

### 5) Enforce in controller (recommended server-side)

Before upsert in saveClientNote(), validate this requester has at least 2 bookings for current provider.
If not:

~~~php
$_SESSION['error'] = 'Cannot add note: client has fewer than 2 bookings.';
header("Location: " . ROOT . "/ServiceProviderDashboard");
exit;
~~~
