<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$roles = isset($roles) && is_array($roles) ? $roles : [];
$roleStats = $roleStats ?? null;
$pendingApplications = isset($pendingApplications) && is_array($pendingApplications) ? $pendingApplications : [];
$pendingRequests = isset($pendingRequests) && is_array($pendingRequests) ? $pendingRequests : [];
$publishedRoles = isset($publishedRoles) && is_array($publishedRoles) ? $publishedRoles : [];

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

$dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 0);
$dramaName = isset($drama->drama_name) ? $drama->drama_name : 'Drama';

$publishableRoles = array_filter($roles, function ($role) {
    $status = strtolower($role->status ?? 'open');
    return $status !== 'filled';
});

$publishedRoleIds = array_map(function ($role) {
    return (int)$role->id;
}, $publishedRoles);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artist Roles - <?= esc($dramaName) ?> - Rangamadala</title>
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

        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.info { background: #e9ecef; color: #383d41; border: 1px solid #d6d8db; }

        .roles-table { width: 100%; border-collapse: collapse; }
        .roles-table th, .roles-table td { padding: 14px; border-bottom: 1px solid var(--border); text-align: left; }
        .roles-table th { font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; color: var(--muted); }
        .roles-table td { font-size: 14px; }
        .roles-table tbody tr:hover { background: rgba(0,0,0,0.02); }

        .actions-inline { display: flex; gap: 8px; flex-wrap: wrap; }

        .tab-buttons { display: flex; gap: 12px; margin-top: 32px; margin-bottom: 12px; }
        .tab-buttons button {
            padding: 10px 18px;
            border-radius: 30px;
            border: 1px solid var(--border);
            background: #fff;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        .tab-buttons button.active {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .tab-content { display: none; background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.05)); }
        .tab-content.active { display: block; }

        .application-card, .request-card, .vacancy-card {
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 16px;
            background: #fff;
            box-shadow: var(--shadow-xs, 0 2px 6px rgba(0,0,0,0.05));
        }
        .card-header { display: flex; justify-content: space-between; gap: 12px; }
        .card-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 13px; color: var(--muted); }

        .form-inline { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; }
        .form-inline .form-group { flex: 1 1 220px; }

        .empty-state { padding: 32px; text-align: center; border: 1px dashed var(--border); border-radius: 12px; color: var(--muted); }

        @media (max-width: 768px) {
            .roles-table thead { display: none; }
            .roles-table tbody tr { display: block; border: 1px solid var(--border); border-radius: 12px; margin-bottom: 12px; padding: 12px; }
            .roles-table td { display: flex; justify-content: space-between; padding: 8px 0; }
            .roles-table td::before { content: attr(data-label); font-weight: 600; color: var(--muted); }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo"><h2>🎭</h2></div>
        <ul class="menu">
            <li><a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li><a href="<?= ROOT ?>/director/drama_details?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-film"></i><span>Drama Details</span></a></li>
            <li class="active"><a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-users"></i><span>Artist Roles</span></a></li>
            <li><a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-user-tie"></i><span>Production Manager</span></a></li>
            <li><a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-calendar-alt"></i><span>Schedule</span></a></li>
            <li><a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-dollar-sign"></i><span>Services & Budget</span></a></li>
            <li><a href="<?= ROOT ?>/artistdashboard"><i class="fas fa-arrow-left"></i><span>Back to Profile</span></a></li>
            <li><a href="<?= ROOT ?>/logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main--content">
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>" class="back-button"><i class="fas fa-arrow-left"></i>Back to Dashboard</a>

        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($dramaName) ?></span>
                <h2>Manage Artist Roles</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Review open roles, handle applications, and collaborate with artists in one place.</p>
            </div>
            <div class="header-controls">
                <a href="<?= ROOT ?>/director/create_role?drama_id=<?= esc($dramaId) ?>" class="btn btn-primary"><i class="fas fa-plus-circle"></i>Create Role</a>
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
                <div class="stat-card"><h3><?= esc($roleStats->total_roles ?? 0) ?></h3><p>Total Roles</p></div>
                <div class="stat-card"><h3><?= esc($roleStats->open_roles ?? 0) ?></h3><p>Open Roles</p></div>
                <div class="stat-card"><h3><?= esc($roleStats->filled_positions ?? 0) ?></h3><p>Filled Positions</p></div>
                <div class="stat-card"><h3><?= esc($roleStats->published_roles ?? 0) ?></h3><p>Published Vacancies</p></div>
            </div>
        <?php endif; ?>

        <section class="card-section" style="border: 1px solid var(--border); border-radius: 16px; padding: 24px; background: #fff; box-shadow: var(--shadow-sm, 0 2px 8px rgba(0,0,0,0.04));">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 style="margin: 0; font-size: 20px;">Roles for this Drama</h3>
                <span style="color: var(--muted); font-size: 13px;">Click Assign to invite artists or View to manage details.</span>
            </div>

            <?php if (empty($roles)): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list" style="font-size: 28px; display: block; margin-bottom: 12px;"></i>
                    No roles created yet. Use the "Create Role" button to get started.
                </div>
            <?php else: ?>
                <div class="responsive-table">
                    <table class="roles-table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Type</th>
                                <th>Positions</th>
                                <th>Status</th>
                                <th>Vacancy</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($roles as $role): ?>
                                <?php
                                    $statusKey = strtolower($role->status ?? 'open');
                                    $statusLabel = $roleStatuses[$statusKey] ?? ucfirst($statusKey);
                                    $positionsAvailable = isset($role->positions_available) ? (int)$role->positions_available : 0;
                                    $positionsFilled = isset($role->positions_filled) ? (int)$role->positions_filled : 0;
                                    $openSlots = max(0, $positionsAvailable - $positionsFilled);
                                    $isPublished = (int)($role->is_published ?? 0) === 1;
                                    $salaryDisplay = '';
                                    if (isset($role->salary) && $role->salary !== null) {
                                        $salaryDisplay = 'LKR ' . number_format((float)$role->salary, 2);
                                    }
                                ?>
                                <tr>
                                    <td data-label="Role">
                                        <strong><?= esc($role->role_name ?? 'Role') ?></strong>
                                        <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">
                                            <?= esc(mb_strimwidth($role->role_description ?? 'No description', 0, 80, '…')) ?>
                                        </div>
                                    </td>
                                    <td data-label="Type"><?= isset($roleTypes[$role->role_type ?? '']) ? esc($roleTypes[$role->role_type]) : esc(ucfirst($role->role_type ?? 'N/A')) ?></td>
                                    <td data-label="Positions">
                                        <?= esc($positionsFilled) ?> / <?= esc($positionsAvailable) ?>
                                        <div style="font-size: 12px; color: var(--muted);">Open slots: <?= esc($openSlots) ?></div>
                                    </td>
                                    <td data-label="Status">
                                        <span class="status-badge <?= $statusKey === 'open' ? 'pending' : ($statusKey === 'filled' ? 'assigned' : 'unassigned') ?>"><?= esc($statusLabel) ?></span>
                                    </td>
                                    <td data-label="Vacancy">
                                        <?php if ($isPublished): ?>
                                            <span class="status-badge assigned">Published</span>
                                        <?php else: ?>
                                            <span class="status-badge unassigned">Not Published</span>
                                        <?php endif; ?>
                                        <?php if ($salaryDisplay): ?>
                                            <div style="font-size: 12px; color: var(--muted); margin-top: 4px;">Salary: <?= esc($salaryDisplay) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="actions-inline">
                                            <a class="btn btn-secondary" href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>"><i class="fas fa-eye"></i>View</a>
                                            <a class="btn btn-success" href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>"><i class="fas fa-user-plus"></i>Assign Artist</a>
                                            <form action="<?= ROOT ?>/director/delete_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>" method="POST" data-confirm="Delete role '<?= esc($role->role_name ?? 'Role') ?>'?">
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i>Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <div class="tab-buttons">
            <button class="tab-trigger active" data-tab="applications">Applications (<?= count($pendingApplications) ?>)</button>
            <button class="tab-trigger" data-tab="vacancies">Publish Vacancy</button>
            <button class="tab-trigger" data-tab="requests">Requests (<?= count($pendingRequests) ?>)</button>
        </div>

        <section id="tab-applications" class="tab-content active">
            <?php if (empty($pendingApplications)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox" style="font-size: 28px; display: block; margin-bottom: 12px;"></i>
                    No pending applications right now. Vacancies you publish will appear here as artists apply.
                </div>
            <?php else: ?>
                <?php foreach ($pendingApplications as $application): ?>
                    <div class="application-card">
                        <div class="card-header">
                            <div>
                                <h4 style="margin: 0 0 6px;"><?= esc($application->artist_name ?? 'Artist') ?></h4>
                                <div class="card-meta">
                                    <span><strong>Role:</strong> <?= esc($application->role_name ?? 'Role') ?></span>
                                    <span><strong>Applied:</strong> <?= esc(date('Y-m-d H:i', strtotime($application->applied_at ?? 'now'))) ?></span>
                                    <?php if (!empty($application->artist_email)): ?><span><?= esc($application->artist_email) ?></span><?php endif; ?>
                                </div>
                            </div>
                            <div class="actions-inline">
                                <form class="js-role-action" data-action="accept" action="<?= ROOT ?>/director/accept_application?drama_id=<?= esc($dramaId) ?>" method="POST">
                                    <input type="hidden" name="application_id" value="<?= esc($application->id) ?>">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check"></i>Accept</button>
                                </form>
                                <form class="js-role-action" data-action="reject" action="<?= ROOT ?>/director/reject_application?drama_id=<?= esc($dramaId) ?>" method="POST" data-confirm="Reject this application?">
                                    <input type="hidden" name="application_id" value="<?= esc($application->id) ?>">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i>Reject</button>
                                </form>
                            </div>
                        </div>
                        <?php if (!empty($application->application_message)): ?>
                            <div style="margin-top: 12px; white-space: pre-wrap;"><?= nl2br(esc($application->application_message)) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section id="tab-vacancies" class="tab-content">
            <h4 style="margin-top: 0;">Publish a new vacancy</h4>
            <?php if (empty($publishableRoles)): ?>
                <div class="empty-state" style="margin-bottom: 20px;">
                    <i class="fas fa-check-double" style="font-size: 26px; display: block; margin-bottom: 10px;"></i>
                    All roles are currently filled. Update a role to open it for new applicants.
                </div>
            <?php else: ?>
                <form class="form-inline js-role-action" data-action="publish" action="<?= ROOT ?>/director/publish_vacancy?drama_id=<?= esc($dramaId) ?>" method="POST">
                    <div class="form-group">
                        <label for="publish_role_id">Role</label>
                        <select id="publish_role_id" name="role_id" class="form-control" required>
                            <option value="">Select role</option>
                            <?php foreach ($publishableRoles as $role): ?>
                                <option value="<?= esc($role->id) ?>" <?= in_array((int)$role->id, $publishedRoleIds, true) ? 'disabled' : '' ?>>
                                    <?= esc($role->role_name) ?>
                                    <?php if (in_array((int)$role->id, $publishedRoleIds, true)): ?> (Published)<?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="publish_message">Vacancy message</label>
                        <textarea id="publish_message" name="message" class="form-control" rows="2" placeholder="Highlight requirements, audition dates, etc."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-bullhorn"></i>Publish Vacancy</button>
                </form>
            <?php endif; ?>

            <h4 style="margin: 24px 0 12px;">Published vacancies</h4>
            <?php if (empty($publishedRoles)): ?>
                <div class="empty-state">
                    <i class="fas fa-briefcase" style="font-size: 26px; display: block; margin-bottom: 10px;"></i>
                    No active vacancies. Publish a role to reach available artists.
                </div>
            <?php else: ?>
                <?php foreach ($publishedRoles as $role): ?>
                    <div class="vacancy-card">
                        <div class="card-header">
                            <div>
                                <h4 style="margin: 0 0 6px;"><?= esc($role->role_name ?? 'Role') ?></h4>
                                <div class="card-meta">
                                    <span><strong>Published:</strong> <?= esc(date('Y-m-d H:i', strtotime($role->published_at ?? 'now'))) ?></span>
                                    <?php if (!empty($role->director_name)): ?><span><strong>By:</strong> <?= esc($role->director_name) ?></span><?php endif; ?>
                                </div>
                            </div>
                            <form class="js-role-action" data-action="unpublish" action="<?= ROOT ?>/director/unpublish_vacancy?drama_id=<?= esc($dramaId) ?>" method="POST" data-confirm="Unpublish this vacancy?">
                                <input type="hidden" name="role_id" value="<?= esc($role->id) ?>">
                                <button type="submit" class="btn btn-secondary"><i class="fas fa-eye-slash"></i>Unpublish</button>
                            </form>
                        </div>
                        <?php if (!empty($role->published_message)): ?>
                            <div style="margin-top: 10px; white-space: pre-wrap;"><?= nl2br(esc($role->published_message)) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section id="tab-requests" class="tab-content">
            <?php if (empty($pendingRequests)): ?>
                <div class="empty-state">
                    <i class="fas fa-users" style="font-size: 26px; display: block; margin-bottom: 10px;"></i>
                    No pending direct requests. Use "Assign Artist" to reach out to performers.
                </div>
            <?php else: ?>
                <?php foreach ($pendingRequests as $request): ?>
                    <div class="request-card">
                        <div class="card-header">
                            <div>
                                <h4 style="margin: 0 0 6px;"><?= esc($request->artist_name ?? 'Artist') ?></h4>
                                <div class="card-meta">
                                    <span><strong>Role:</strong> <?= esc($request->role_name ?? 'Role') ?></span>
                                    <span><strong>Requested:</strong> <?= esc(date('Y-m-d H:i', strtotime($request->requested_at ?? 'now'))) ?></span>
                                </div>
                            </div>
                            <a class="btn btn-secondary" href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($request->role_id) ?>"><i class="fas fa-eye"></i>View Role</a>
                        </div>
                        <?php if (!empty($request->note)): ?>
                            <div style="margin-top: 10px; white-space: pre-wrap;"><?= nl2br(esc($request->note)) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabButtons = document.querySelectorAll('.tab-trigger');
            const tabContents = {
                applications: document.getElementById('tab-applications'),
                vacancies: document.getElementById('tab-vacancies'),
                requests: document.getElementById('tab-requests')
            };

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabButtons.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    Object.values(tabContents).forEach(section => section.classList.remove('active'));
                    const target = tabContents[btn.dataset.tab];
                    if (target) {
                        target.classList.add('active');
                    }
                });
            });
        });
    </script>
    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
