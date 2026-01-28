<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$role = $role ?? null;
if (!$role) {
    echo '<p>Role not found.</p>';
    return;
}

$roleApplications = isset($roleApplications) && is_array($roleApplications) ? $roleApplications : [];
$roleRequests = isset($roleRequests) && is_array($roleRequests) ? $roleRequests : [];
$assignments = isset($assignments) && is_array($assignments) ? $assignments : [];

$roleTypes = [
    'lead' => 'Lead',
    'supporting' => 'Supporting',
    'ensemble' => 'Ensemble',
    'dancer' => 'Dancer',
    'musician' => 'Musician',
    'other' => 'Other',
];

$roleStatuses = [
    'open' => 'Open',
    'filled' => 'Filled',
    'closed' => 'Closed',
];

$dramaId = isset($drama->id) ? (int)$drama->id : 0;
$roleId = (int)($role->id ?? 0);

$updateDefaults = [
    'role_name' => $role->role_name ?? '',
    'role_description' => $role->role_description ?? '',
    'role_type' => $role->role_type ?? 'supporting',
    'salary' => isset($role->salary) && $role->salary !== null ? number_format((float)$role->salary, 2, '.', '') : '',
    'positions_available' => isset($role->positions_available) ? (string)$role->positions_available : '1',
    'requirements' => $role->requirements ?? '',
    'status' => $role->status ?? 'open',
];

$updateValues = $updateDefaults;
$updateErrors = [];
if (($role_form_mode ?? null) === 'update' && (int)($role_form_role_id ?? 0) === $roleId && !empty($role_form_data)) {
    $updateValues = array_merge($updateDefaults, $role_form_data);
    $updateErrors = $role_form_errors ?? [];
}

function groupByStatus(array $items, $statusKey = 'status') {
    $grouped = [];
    foreach ($items as $item) {
        $status = strtolower($item->{$statusKey} ?? 'pending');
        $grouped[$status][] = $item;
    }
    return $grouped;
}

$groupedApplications = groupByStatus($roleApplications, 'status');
$groupedRequests = groupByStatus($roleRequests, 'status');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($role->role_name ?? 'Role') ?> - Role Details - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .card { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-sm, 0 6px 18px rgba(15, 23, 42, .08)); margin-bottom: 24px; }
        .grid-two { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; }
        .list-item { border: 1px solid var(--border); border-radius: 12px; padding: 16px; margin-bottom: 12px; background: #fff; }
        .list-item:last-child { margin-bottom: 0; }
        .badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-open { background: rgba(76,175,80,.12); color: #256029; }
        .badge-filled { background: rgba(0,123,255,.12); color: #0b5394; }
        .badge-closed { background: rgba(244,67,54,.12); color: #a52714; }
        .badge-pending { background: rgba(255,193,7,.15); color: #7a4f02; }
        .badge-accepted { background: rgba(76,175,80,.12); color: #256029; }
        .badge-rejected { background: rgba(244,67,54,.12); color: #a52714; }
        .actions-inline { display: flex; gap: 10px; flex-wrap: wrap; }
        .form-error { color: var(--danger); font-size: 12px; margin-top: 4px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo"><h2>🎭</h2></div>
        <ul class="menu">
            <li><a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li><a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-users"></i><span>Artist Roles</span></a></li>
            <li class="active"><a href="#"><i class="fas fa-mask"></i><span><?= esc($role->role_name ?? 'Role') ?></span></a></li>
            <li><a href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>"><i class="fas fa-user-plus"></i><span>Assign Artist</span></a></li>
            <li><a href="<?= ROOT ?>/artistdashboard"><i class="fas fa-arrow-left"></i><span>Back to Profile</span></a></li>
        </ul>
    </aside>

    <main class="main--content">
        <a class="back-button" href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-arrow-left"></i>Back to Manage Roles</a>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="card" style="border-left: 4px solid var(--brand); background: rgba(186,142,35,0.1); color: var(--ink);">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <section class="card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div>
                    <h2 style="margin: 0 0 8px;"><?= esc($role->role_name ?? 'Role') ?></h2>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; font-size: 13px; color: var(--muted);">
                        <span><strong>Type:</strong> <?= isset($roleTypes[$role->role_type ?? '']) ? esc($roleTypes[$role->role_type]) : esc(ucfirst($role->role_type ?? 'N/A')) ?></span>
                        <span><strong>Status:</strong>
                            <span class="badge badge-<?= esc(strtolower($role->status ?? 'open')) ?>"><?= esc($roleStatuses[strtolower($role->status ?? 'open')] ?? ucfirst($role->status ?? 'Open')) ?></span>
                        </span>
                        <span><strong>Positions:</strong> <?= esc($role->positions_filled ?? 0) ?> / <?= esc($role->positions_available ?? 0) ?></span>
                        <span><strong>Vacancy:</strong> <?= (int)($role->is_published ?? 0) === 1 ? 'Published' : 'Not published' ?></span>
                    </div>
                </div>
                <div class="actions-inline">
                    <?php $isRoleFull = (int)($role->positions_filled ?? 0) >= (int)($role->positions_available ?? 0); ?>
                    <?php if ($isRoleFull): ?>
                        <button class="btn btn-secondary" disabled title="All positions filled"><i class="fas fa-user-slash"></i>Role Full</button>
                    <?php else: ?>
                        <a class="btn btn-secondary" href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>"><i class="fas fa-user-search"></i>Find Artists</a>
                    <?php endif; ?>
                    <?php if ((int)($role->is_published ?? 0) === 1): ?>
                        <form class="js-role-action" data-action="unpublish" action="<?= ROOT ?>/director/unpublish_vacancy?drama_id=<?= esc($dramaId) ?>" method="POST" data-confirm="Unpublish this vacancy?">
                            <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                            <button type="submit" class="btn btn-secondary"><i class="fas fa-eye-slash"></i>Unpublish</button>
                        </form>
                    <?php else: ?>
                        <form class="js-role-action" data-action="publish" action="<?= ROOT ?>/director/publish_vacancy?drama_id=<?= esc($dramaId) ?>" method="POST">
                            <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                            <input type="hidden" name="message" value="">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-bullhorn"></i>Publish Vacancy</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div style="margin-top: 16px; font-size: 14px; line-height: 1.6;">
                <?= nl2br(esc($role->role_description ?? 'No description provided.')) ?>
            </div>
            <?php if (!empty($role->requirements)): ?>
                <div style="margin-top: 16px;">
                    <strong>Requirements:</strong>
                    <div style="margin-top: 6px; white-space: pre-wrap;"><?= nl2br(esc($role->requirements)) ?></div>
                </div>
            <?php endif; ?>
            <?php if ($isRoleFull): ?>
                <div style="margin-top: 16px; padding: 12px 16px; background: rgba(255,193,7,0.1); border-left: 4px solid #ffc107; border-radius: 8px; font-size: 14px;">
                    <i class="fas fa-info-circle" style="color: #f57c00; margin-right: 8px;"></i>
                    <strong>All positions filled.</strong> To assign a new artist, you must first remove a currently assigned artist from this role.
                </div>
            <?php endif; ?>
        </section>

        <section class="card">
            <h3 style="margin-top: 0;">Edit Role</h3>
            <form action="<?= ROOT ?>/director/update_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>" method="POST" class="grid-two">
                <div class="form-group">
                    <label for="edit_role_name">Role Name</label>
                    <input type="text" id="edit_role_name" name="role_name" class="form-control" value="<?= esc($updateValues['role_name']) ?>" required>
                    <?php if (isset($updateErrors['role_name'])): ?><div class="form-error"><?= esc($updateErrors['role_name']) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="edit_role_type">Role Type</label>
                    <select id="edit_role_type" name="role_type" class="form-control" required>
                        <?php foreach ($roleTypes as $typeKey => $typeLabel): ?>
                            <option value="<?= esc($typeKey) ?>" <?= $updateValues['role_type'] === $typeKey ? 'selected' : '' ?>><?= esc($typeLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($updateErrors['role_type'])): ?><div class="form-error"><?= esc($updateErrors['role_type']) ?></div><?php endif; ?>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="edit_role_description">Description</label>
                    <textarea id="edit_role_description" name="role_description" class="form-control" rows="4" required><?= esc($updateValues['role_description']) ?></textarea>
                    <?php if (isset($updateErrors['role_description'])): ?><div class="form-error"><?= esc($updateErrors['role_description']) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="edit_salary">Salary (LKR)<?php if ($isRoleFull): ?> <span style="color: var(--muted); font-weight: normal; font-size: 12px;">(Locked - role filled)</span><?php endif; ?></label>
                    <input type="number" step="0.01" min="0" id="edit_salary" name="salary" class="form-control" value="<?= esc($updateValues['salary']) ?>" <?= $isRoleFull ? 'disabled' : '' ?>>
                    <?php if (isset($updateErrors['salary'])): ?><div class="form-error"><?= esc($updateErrors['salary']) ?></div><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="edit_positions_available">Positions Available</label>
                    <input type="number" min="1" id="edit_positions_available" name="positions_available" class="form-control" value="<?= esc($updateValues['positions_available']) ?>" required>
                    <?php if (isset($updateErrors['positions_available'])): ?><div class="form-error"><?= esc($updateErrors['positions_available']) ?></div><?php endif; ?>
                </div>
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label for="edit_requirements">Requirements</label>
                    <textarea id="edit_requirements" name="requirements" class="form-control" rows="3"><?= esc($updateValues['requirements']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_status">Status<?php if ($isRoleFull): ?> <span style="color: var(--muted); font-weight: normal; font-size: 12px;">(Locked - role filled)</span><?php endif; ?></label>
                    <select id="edit_status" name="status" class="form-control" required <?= $isRoleFull ? 'disabled' : '' ?>>
                        <?php foreach ($roleStatuses as $statusKey => $statusLabel): ?>
                            <option value="<?= esc($statusKey) ?>" <?= $updateValues['status'] === $statusKey ? 'selected' : '' ?>><?= esc($statusLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($updateErrors['status'])): ?><div class="form-error"><?= esc($updateErrors['status']) ?></div><?php endif; ?>
                    <?php if ($isRoleFull): ?>
                        <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Status cannot be changed while artists are assigned to this role.</div>
                    <?php endif; ?>
                </div>
                <div style="grid-column: 1 / -1;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </form>
        </section>

        <section class="card">
            <h3 style="margin-top: 0;">Assigned Artists (<?= count($assignments) ?>)</h3>
            <?php if ($isRoleFull): ?>
                <div style="padding: 10px 14px; margin-bottom: 16px; background: rgba(76,175,80,0.1); border-left: 4px solid #4caf50; border-radius: 6px; font-size: 13px; color: #256029;">
                    <i class="fas fa-check-circle" style="margin-right: 8px;"></i>
                    <strong>All positions filled.</strong> To assign different artists, remove one of the current assignments first.
                </div>
            <?php endif; ?>
            <?php if (empty($assignments)): ?>
                <div class="list-item" style="text-align: center; color: var(--muted);">No artists assigned yet. Use "Find Artists" to send requests.</div>
            <?php else: ?>
                <?php foreach ($assignments as $assignment): ?>
                    <div class="list-item">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                    <strong style="font-size: 16px;"><?= esc($assignment->artist_name ?? 'Artist') ?></strong>
                                    <span class="badge badge-open">Active</span>
                                </div>
                                <div style="font-size: 13px; color: var(--muted); line-height: 1.6;">
                                    <?php if (!empty($assignment->artist_email)): ?>
                                        <div><i class="fas fa-envelope" style="width: 16px; margin-right: 6px;"></i><?= esc($assignment->artist_email) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($assignment->artist_phone)): ?>
                                        <div><i class="fas fa-phone" style="width: 16px; margin-right: 6px;"></i><?= esc($assignment->artist_phone) ?></div>
                                    <?php endif; ?>
                                    <div><i class="fas fa-calendar" style="width: 16px; margin-right: 6px;"></i>Assigned on <?= esc(date('M d, Y', strtotime($assignment->assigned_at ?? 'now'))) ?></div>
                                </div>
                            </div>
                            <form action="<?= ROOT ?>/director/remove_assignment?drama_id=<?= esc($dramaId) ?>" method="POST" class="js-role-action" data-action="remove" data-confirm="Remove <?= esc($assignment->artist_name ?? 'this artist') ?> from this role?">
                                <input type="hidden" name="assignment_id" value="<?= esc($assignment->id ?? 0) ?>">
                                <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                                <input type="hidden" name="return_to" value="role_details">
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-user-times"></i>Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="card">
            <h3 style="margin-top: 0;">Pending Requests</h3>
            <?php $pending = $groupedRequests['pending'] ?? $groupedRequests['interview'] ?? []; ?>
            <?php if (empty($pending)): ?>
                <div class="list-item" style="text-align: center; color: var(--muted);">No pending requests for this role.</div>
            <?php else: ?>
                <?php foreach ($pending as $request): ?>
                    <div class="list-item">
                        <div style="display: flex; justify-content: space-between;">
                            <div>
                                <strong><?= esc($request->artist_name ?? 'Artist') ?></strong>
                                <div style="font-size: 13px; color: var(--muted);">Requested <?= esc(date('Y-m-d H:i', strtotime($request->requested_at ?? 'now'))) ?></div>
                                <?php if (!empty($request->note)): ?><div style="margin-top: 8px; white-space: pre-wrap;"><?= nl2br(esc($request->note)) ?></div><?php endif; ?>
                            </div>
                            <span class="badge badge-pending">Pending</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section class="card">
            <h3 style="margin-top: 0;">Applications</h3>
            <?php $pendingApps = $groupedApplications['pending'] ?? []; ?>
            <?php if (empty($roleApplications)): ?>
                <div class="list-item" style="text-align: center; color: var(--muted);">No applications received yet.</div>
            <?php else: ?>
                <?php foreach ($roleApplications as $application): ?>
                    <div class="list-item">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                            <div>
                                <strong><?= esc($application->artist_name ?? 'Artist') ?></strong>
                                <div style="font-size: 13px; color: var(--muted);">Applied <?= esc(date('Y-m-d H:i', strtotime($application->applied_at ?? 'now'))) ?></div>
                                <?php if (!empty($application->application_message)): ?>
                                    <div style="margin-top: 8px; white-space: pre-wrap;"><?= nl2br(esc($application->application_message)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge badge-<?= esc(strtolower($application->status ?? 'pending')) ?>" style="margin-bottom: 8px; display: inline-block;">
                                    <?= esc(ucfirst($application->status ?? 'pending')) ?>
                                </span>
                                <?php if (strtolower($application->status ?? '') === 'pending'): ?>
                                    <div class="actions-inline" style="justify-content: flex-end;">
                                        <form class="js-role-action" data-action="accept" action="<?= ROOT ?>/director/accept_application?drama_id=<?= esc($dramaId) ?>" method="POST">
                                            <input type="hidden" name="application_id" value="<?= esc($application->id) ?>">
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i>Accept</button>
                                        </form>
                                        <form class="js-role-action" data-action="reject" action="<?= ROOT ?>/director/reject_application?drama_id=<?= esc($dramaId) ?>" method="POST" data-confirm="Reject this application?">
                                            <input type="hidden" name="application_id" value="<?= esc($application->id) ?>">
                                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-times"></i>Reject</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
