<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$roles = isset($roles) && is_array($roles) ? $roles : [];
$roleStats = $roleStats ?? null;

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

$formMode = $role_form_mode ?? 'create';
$formErrors = $role_form_errors ?? [];
$formData = isset($role_form_data) && is_array($role_form_data) ? $role_form_data : [];
$formRoleId = $role_form_role_id ?? null;

$createDefaults = [
    'role_name' => '',
    'role_description' => '',
    'role_type' => 'supporting',
    'salary' => '',
    'positions_available' => '1',
    'requirements' => '',
];

$createValues = $createDefaults;
if ($formMode === 'create' && !empty($formData)) {
    $createValues = array_merge($createDefaults, $formData);
}
$createErrors = $formMode === 'create' ? $formErrors : [];

$editingRole = $editing_role ?? null;
$updateValues = null;
$updateErrors = [];

if ($editingRole) {
    $updateDefaults = [
        'role_name' => $editingRole->role_name ?? '',
        'role_description' => $editingRole->role_description ?? '',
        'role_type' => $editingRole->role_type ?? 'supporting',
        'salary' => isset($editingRole->salary) && $editingRole->salary !== null ? number_format((float)$editingRole->salary, 2, '.', '') : '',
        'positions_available' => isset($editingRole->positions_available) ? (string)$editingRole->positions_available : '1',
        'requirements' => $editingRole->requirements ?? '',
        'status' => $editingRole->status ?? 'open',
    ];

    $updateValues = $updateDefaults;

    if ($formMode === 'update' && $formRoleId && (int)$formRoleId === (int)$editingRole->id && !empty($formData)) {
        $updateValues = array_merge($updateDefaults, $formData);
        $updateErrors = $formErrors;
    }
}

$dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 1);
$dramaIdForLinks = $dramaId;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artist Roles - <?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .message {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.info {
            background: #e9ecef;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .role-card {
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #ffffff;
            box-shadow: var(--shadow-sm, 0 2px 10px rgba(0,0,0,0.08));
        }

        .role-card + .role-card {
            margin-top: 16px;
        }

        .role-card--active {
            border-color: var(--brand);
            box-shadow: var(--shadow-md, 0 8px 24px rgba(0,0,0,0.12));
        }

        .role-card__header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 12px;
        }

        .role-card__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            color: var(--muted);
        }

        .role-card__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }

        .form-error {
            color: var(--danger);
            font-size: 12px;
            margin-top: 6px;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--brand);
            margin-bottom: 16px;
            font-size: 18px;
            font-weight: 600;
        }

        .form-footer {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .role-card__header {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaIdForLinks) ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= esc($dramaIdForLinks) ?>">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaIdForLinks) ?>">
                    <i class="fas fa-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= esc($dramaIdForLinks) ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= esc($dramaIdForLinks) ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= esc($dramaIdForLinks) ?>">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Services & Budget</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaIdForLinks) ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
                <h2>Manage Artist Roles</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    Keep your casting information up to date for this production.
                </p>
            </div>
            <div class="header-controls">
                <a href="#create-role" class="btn btn-primary">
                    <i class="fas fa-plus-circle"></i>
                    New Role
                </a>
                <a href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaIdForLinks) ?>" class="btn btn-success">
                    <i class="fas fa-search"></i>
                    Search Artists
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?? 'info' ?>">
                <i class="fas fa-<?= ($_SESSION['message_type'] ?? '') === 'success' ? 'check-circle' : (($_SESSION['message_type'] ?? '') === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <?php if ($roleStats): ?>
            <div class="stats-grid" style="margin-bottom: 24px;">
                <div class="stat-card">
                    <h3><?= esc($roleStats->total_roles ?? 0) ?></h3>
                    <p>Total Roles</p>
                </div>
                <div class="stat-card">
                    <h3><?= esc($roleStats->total_positions ?? 0) ?></h3>
                    <p>Total Positions</p>
                </div>
                <div class="stat-card">
                    <h3><?= esc($roleStats->filled_positions ?? 0) ?></h3>
                    <p>Filled Positions</p>
                </div>
                <div class="stat-card">
                    <h3><?= esc($roleStats->open_roles ?? 0) ?></h3>
                    <p>Open Roles</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <?php if ($editingRole && $updateValues): ?>
                        <div class="card-section" id="edit-role" style="margin-bottom: 30px;">
                            <h3 class="form-section-title">
                                <i class="fas fa-edit"></i>
                                Edit Role &mdash; <?= esc($editingRole->role_name ?? '') ?>
                            </h3>
                            <form action="<?= ROOT ?>/director/update_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($editingRole->id) ?>" method="POST">
                                <div class="form-group">
                                    <label for="edit_role_name">Role Name <span class="required">*</span></label>
                                    <input type="text" id="edit_role_name" name="role_name" class="form-control" value="<?= esc($updateValues['role_name']) ?>" required>
                                    <?php if (isset($updateErrors['role_name'])): ?>
                                        <div class="form-error"><?= esc($updateErrors['role_name']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="edit_role_type">Role Type <span class="required">*</span></label>
                                    <select id="edit_role_type" name="role_type" class="form-control" required>
                                        <?php foreach ($roleTypes as $typeKey => $typeLabel): ?>
                                            <option value="<?= esc($typeKey) ?>" <?= $updateValues['role_type'] === $typeKey ? 'selected' : '' ?>><?= esc($typeLabel) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($updateErrors['role_type'])): ?>
                                        <div class="form-error"><?= esc($updateErrors['role_type']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="edit_role_description">Role Description <span class="required">*</span></label>
                                    <textarea id="edit_role_description" name="role_description" class="form-control" rows="4" required><?= esc($updateValues['role_description']) ?></textarea>
                                    <?php if (isset($updateErrors['role_description'])): ?>
                                        <div class="form-error"><?= esc($updateErrors['role_description']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="edit_role_salary">Salary (LKR)</label>
                                        <input type="number" step="0.01" min="0" id="edit_role_salary" name="salary" class="form-control" value="<?= esc($updateValues['salary']) ?>">
                                        <?php if (isset($updateErrors['salary'])): ?>
                                            <div class="form-error"><?= esc($updateErrors['salary']) ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group">
                                        <label for="edit_positions_available">Positions Available <span class="required">*</span></label>
                                        <input type="number" min="1" id="edit_positions_available" name="positions_available" class="form-control" value="<?= esc($updateValues['positions_available']) ?>" required>
                                        <?php if (isset($updateErrors['positions_available'])): ?>
                                            <div class="form-error"><?= esc($updateErrors['positions_available']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="edit_requirements">Special Requirements</label>
                                    <textarea id="edit_requirements" name="requirements" class="form-control" rows="3"><?= esc($updateValues['requirements']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="edit_status">Role Status <span class="required">*</span></label>
                                    <select id="edit_status" name="status" class="form-control" required>
                                        <?php foreach ($roleStatuses as $statusKey => $statusLabel): ?>
                                            <option value="<?= esc($statusKey) ?>" <?= $updateValues['status'] === $statusKey ? 'selected' : '' ?>><?= esc($statusLabel) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($updateErrors['status'])): ?>
                                        <div class="form-error"><?= esc($updateErrors['status']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i>
                                        Save Changes
                                    </button>
                                    <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="card-section" id="create-role" style="margin-bottom: 30px;">
                        <h3 class="form-section-title">
                            <i class="fas fa-plus-circle"></i>
                            Create New Role
                        </h3>
                        <form action="<?= ROOT ?>/director/create_role?drama_id=<?= esc($dramaId) ?>" method="POST">
                            <div class="form-group">
                                <label for="create_role_name">Role Name <span class="required">*</span></label>
                                <input type="text" id="create_role_name" name="role_name" class="form-control" value="<?= esc($createValues['role_name']) ?>" required>
                                <?php if (isset($createErrors['role_name'])): ?>
                                    <div class="form-error"><?= esc($createErrors['role_name']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="create_role_type">Role Type <span class="required">*</span></label>
                                <select id="create_role_type" name="role_type" class="form-control" required>
                                    <?php foreach ($roleTypes as $typeKey => $typeLabel): ?>
                                        <option value="<?= esc($typeKey) ?>" <?= $createValues['role_type'] === $typeKey ? 'selected' : '' ?>><?= esc($typeLabel) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($createErrors['role_type'])): ?>
                                    <div class="form-error"><?= esc($createErrors['role_type']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="create_role_description">Role Description <span class="required">*</span></label>
                                <textarea id="create_role_description" name="role_description" class="form-control" rows="4" required><?= esc($createValues['role_description']) ?></textarea>
                                <?php if (isset($createErrors['role_description'])): ?>
                                    <div class="form-error"><?= esc($createErrors['role_description']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="create_role_salary">Salary (LKR)</label>
                                    <input type="number" step="0.01" min="0" id="create_role_salary" name="salary" class="form-control" value="<?= esc($createValues['salary']) ?>">
                                    <?php if (isset($createErrors['salary'])): ?>
                                        <div class="form-error"><?= esc($createErrors['salary']) ?></div>
                                    <?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label for="create_positions_available">Positions Available <span class="required">*</span></label>
                                    <input type="number" min="1" id="create_positions_available" name="positions_available" class="form-control" value="<?= esc($createValues['positions_available']) ?>" required>
                                    <?php if (isset($createErrors['positions_available'])): ?>
                                        <div class="form-error"><?= esc($createErrors['positions_available']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="create_requirements">Special Requirements</label>
                                <textarea id="create_requirements" name="requirements" class="form-control" rows="3"><?= esc($createValues['requirements']) ?></textarea>
                            </div>

                            <div class="form-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check-circle"></i>
                                    Create Role
                                </button>
                                <a href="#roles-list" class="btn btn-secondary">
                                    <i class="fas fa-list"></i>
                                    View Roles
                                </a>
                            </div>
                        </form>
                    </div>

                    <div class="card-section" id="roles-list">
                        <h3 class="form-section-title">
                            <i class="fas fa-users"></i>
                            Existing Roles
                        </h3>

                        <?php if (empty($roles)): ?>
                            <div class="info-box" style="display: flex; align-items: center; gap: 10px; padding: 16px; border-radius: 10px; background: var(--brand-soft, rgba(186, 142, 35, 0.12)); color: var(--brand-strong, #a0781e);">
                                <i class="fas fa-info-circle"></i>
                                No roles have been created for this drama yet. Use the form above to add your first role.
                            </div>
                        <?php else: ?>
                            <?php foreach ($roles as $role): ?>
                                <?php
                                    $statusKey = strtolower($role->status ?? 'open');
                                    $statusClassMap = [
                                        'open' => 'status-badge pending',
                                        'filled' => 'status-badge assigned',
                                        'closed' => 'status-badge unassigned',
                                    ];
                                    $statusClass = $statusClassMap[$statusKey] ?? 'status-badge pending';
                                    $statusLabel = $roleStatuses[$statusKey] ?? ucfirst($statusKey);

                                    $positionsAvailable = isset($role->positions_available) ? (int)$role->positions_available : 0;
                                    $positionsFilled = isset($role->positions_filled) ? (int)$role->positions_filled : 0;
                                    $openSlots = max(0, $positionsAvailable - $positionsFilled);

                                    $salaryDisplay = '';
                                    if (isset($role->salary) && $role->salary !== null) {
                                        $salaryDisplay = 'LKR ' . number_format((float)$role->salary, 2);
                                    }

                                    $isActive = $editingRole && (int)$editingRole->id === (int)$role->id;
                                ?>
                                <div class="role-card<?= $isActive ? ' role-card--active' : '' ?>">
                                    <div class="role-card__header">
                                        <div>
                                            <h4 style="margin: 0 0 6px; font-size: 18px;"><?= esc($role->role_name ?? 'Role') ?></h4>
                                            <div class="role-card__meta">
                                                <span><strong>Type:</strong> <?= isset($roleTypes[$role->role_type ?? '']) ? esc($roleTypes[$role->role_type]) : esc(ucfirst($role->role_type ?? 'N/A')) ?></span>
                                                <span><strong>Status:</strong> <span class="<?= $statusClass ?>"><?= esc($statusLabel) ?></span></span>
                                                <span><strong>Positions:</strong> <?= esc($positionsFilled) ?> / <?= esc($positionsAvailable) ?> filled</span>
                                                <span><strong>Open Slots:</strong> <?= esc($openSlots) ?></span>
                                                <?php if ($salaryDisplay): ?>
                                                    <span><strong>Salary:</strong> <?= esc($salaryDisplay) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right; font-size: 12px; color: var(--muted);">
                                            <?php if (!empty($role->created_at)): ?>
                                                <div>Created: <?= esc(date('Y-m-d H:i', strtotime($role->created_at))) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($role->updated_at)): ?>
                                                <div>Updated: <?= esc(date('Y-m-d H:i', strtotime($role->updated_at))) ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($role->created_by_name)): ?>
                                                <div>By <?= esc($role->created_by_name) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div style="margin-bottom: 12px;">
                                        <strong>Description:</strong>
                                        <div style="margin-top: 6px; white-space: pre-wrap; color: var(--ink);">
                                            <?= nl2br(esc($role->role_description ?? '')) ?>
                                        </div>
                                    </div>

                                    <?php if (!empty($role->requirements)): ?>
                                        <div style="margin-bottom: 12px;">
                                            <strong>Requirements:</strong>
                                            <div style="margin-top: 6px; white-space: pre-wrap; color: var(--ink);">
                                                <?= nl2br(esc($role->requirements)) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="role-card__actions">
                                        <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>" class="btn btn-secondary" style="font-size: 13px; padding: 8px 16px;">
                                            <i class="fas fa-edit"></i>
                                            Edit
                                        </a>
                                        <form action="<?= ROOT ?>/director/delete_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>" method="POST" data-confirm="Are you sure you want to remove the role '<?= esc($role->role_name ?? 'Role') ?>'?">
                                            <button type="submit" class="btn btn-danger" style="font-size: 13px; padding: 8px 16px;">
                                                <i class="fas fa-trash-alt"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
