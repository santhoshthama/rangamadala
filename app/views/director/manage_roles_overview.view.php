<?php
if (isset($data) && is_array($data)) {
    extract($data, EXTR_SKIP);
}

$roles = is_array($roles ?? null) ? $roles : [];
$roleStats = $roleStats ?? null;
$pendingApplications = is_array($pendingApplications ?? null) ? $pendingApplications : [];
$pendingRequests = is_array($pendingRequests ?? null) ? $pendingRequests : [];
$publishedRoles = is_array($publishedRoles ?? null) ? $publishedRoles : [];
$publishableRoles = is_array($publishableRoles ?? null) ? $publishableRoles : [];
$publishedRoleIds = is_array($publishedRoleIds ?? null) ? $publishedRoleIds : [];
$roleTypes = is_array($roleTypes ?? null) ? $roleTypes : [];
$roleStatuses = is_array($roleStatuses ?? null) ? $roleStatuses : [];
$dramaId = (int)($dramaId ?? ($drama->id ?? 0));
$dramaName = (string)($dramaName ?? ($drama->drama_name ?? 'Drama'));
$flash = isset($flash) && is_array($flash) ? $flash : null;
$profileImageSrc = (string)($profileImageSrc ?? (ROOT . '/assets/images/default-avatar.jpg'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Artist Roles - <?= esc($dramaName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/director-role-overview.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="director-dashboard-page">
    <?php
    $directorSidebarDramaId = (int)$dramaId;
    $directorSidebarActive = 'artist-roles';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <main class="main--content">

        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($dramaName) ?></span>
                <h2>Manage Artist Roles</h2>
                <p class="roles-overview-intro">Review open roles, handle applications, and collaborate with artists in one place.</p>
            </div>
            <div class="user--info">
                <?php
                $directorProfileImageSrc = $profileImageSrc;
                $directorRoleLabel = 'Director';
                include __DIR__ . '/_partials/user_menu.php';
                ?>
            </div>
        </div>

        <?php if (!empty($flash)): ?>
            <?php include APPROOT . '/views/_partials/flash.php'; ?>
        <?php endif; ?>

        <?php if ($roleStats): ?>
            <div class="stats-grid director-stats-grid director-stats-grid-spaced">
                <div class="stat-card director-stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Total Roles</div>
                        <div class="stat-card-icon primary">
                            <i class="bx bx-mask"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?= esc($roleStats->total_roles ?? 0) ?></div>
                </div>
                <div class="stat-card director-stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Open Roles</div>
                        <div class="stat-card-icon info">
                            <i class="bx bx-folder-open"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?= esc($roleStats->open_roles ?? 0) ?></div>
                </div>
                <div class="stat-card director-stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Filled Positions</div>
                        <div class="stat-card-icon success">
                            <i class="bx bx-check-shield"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?= esc($roleStats->filled_positions ?? 0) ?></div>
                </div>
                <div class="stat-card director-stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-title">Published Vacancies</div>
                        <div class="stat-card-icon warning">
                            <i class="bx bx-volume-full"></i>
                        </div>
                    </div>
                    <div class="stat-card-value"><?= esc($roleStats->published_roles ?? 0) ?></div>
                </div>
            </div>
        <?php endif; ?>

        <section class="card-section roles-overview-section">
            <div class="roles-overview-header-row">
                <div>
                    <h3 class="roles-overview-title">Roles for this Drama</h3>
                    <span class="section-subtitle">Click Assign to invite artists or View to manage details.</span>
                </div>
                <a href="<?= ROOT ?>/director/create_role?drama_id=<?= esc($dramaId) ?>" class="btn btn-success"><i class="bx bx-plus-circle"></i>Create New Role</a>
            </div>

            <?php if (empty($roles)): ?>
                <div class="empty-state">
                    <i class="bx bx-clipboard-list empty-state-icon empty-state-icon-lg"></i>
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
                                        <div class="role-description-text">
                                            <?= esc(mb_strimwidth($role->role_description ?? 'No description', 0, 80, '…')) ?>
                                        </div>
                                    </td>
                                    <td data-label="Type"><?= isset($roleTypes[$role->role_type ?? '']) ? esc($roleTypes[$role->role_type]) : esc(ucfirst($role->role_type ?? 'N/A')) ?></td>
                                    <td data-label="Positions">
                                        <?= esc($positionsFilled) ?> / <?= esc($positionsAvailable) ?>
                                        <div class="open-slots-text">Open slots: <?= esc($openSlots) ?></div>
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
                                            <div class="salary-text">Salary (Per session): <?= esc($salaryDisplay) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Actions">
                                        <div class="actions-inline">
                                            <a class="btn btn-secondary" href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>"><i class="bx bx-eye"></i>View</a>
                                            <a class="btn btn-success" href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>"><i class="bx bx-user-plus"></i>Assign Artist</a>
                                            <form action="<?= ROOT ?>/director/delete_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($role->id) ?>" method="POST" data-confirm="Delete role '<?= esc($role->role_name ?? 'Role') ?>'?">
                                                <button type="submit" class="btn btn-danger"><i class="bx bx-trash"></i>Delete</button>
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

        <div class="tab-buttons nav-tabs-bar" role="tablist" aria-label="Role management tabs">
            <button class="tab-trigger nav-tab-btn active" data-tab="applications">Applications (<?= count($pendingApplications) ?>)</button>
            <button class="tab-trigger nav-tab-btn" data-tab="vacancies">Publish Vacancy</button>
            <button class="tab-trigger nav-tab-btn" data-tab="published">Published Vacancies (<?= count($publishedRoles) ?>)</button>
            <button class="tab-trigger nav-tab-btn" data-tab="requests">Requests (<?= count($pendingRequests) ?>)</button>
        </div>

        <section id="tab-applications" class="tab-content active">
            <?php if (empty($pendingApplications)): ?>
                <div class="empty-state">
                    <i class="bx bx-inbox empty-state-icon empty-state-icon-lg"></i>
                    No pending applications right now. Vacancies you publish will appear here as artists apply.
                </div>
            <?php else: ?>
                <?php foreach ($pendingApplications as $application): ?>
                    <?php
                        $interviewScheduled = !empty($application->interview_at ?? null);
                        $interviewStatus = strtolower($application->interview_status ?? 'pending');
                        $confirmationStatus = strtolower($application->interview_confirmation_status ?? 'pending');
                        $confirmationSeen = !empty($application->interview_confirmation_seen_at ?? null);
                        $confirmationToneClass = $confirmationStatus === 'confirmed' ? 'interview-confirmation--confirmed' : 'interview-confirmation--declined';
                    ?>
                    <div class="application-card">
                        <div class="card-header">
                            <div>
                                <h4 class="card-title-compact"><?= esc($application->artist_name ?? 'Artist') ?></h4>
                                <div class="card-meta">
                                    <span><strong>Role:</strong> <?= esc($application->role_name ?? 'Role') ?></span>
                                    <span><strong>Applied:</strong> <?= esc(date('Y-m-d H:i', strtotime($application->applied_at ?? 'now'))) ?></span>
                                    <?php if (!empty($application->artist_email)): ?><span><?= esc($application->artist_email) ?></span><?php endif; ?>
                                </div>
                                <div class="review-status review-status-row">
                                    <span class="status-chip <?= $interviewScheduled ? 'ready' : 'pending' ?>">
                                        <i class="bx bx-calendar-alt"></i>
                                        <?= $interviewScheduled ? 'Interview Scheduled' : 'Interview Pending' ?>
                                    </span>
                                </div>
                            </div>
                            <div class="application-actions">
                                <a class="btn btn-secondary" href="<?= ROOT ?>/director/application_profile?drama_id=<?= esc($dramaId) ?>&application_id=<?= esc($application->id) ?>">
                                    <i class="bx bx-id-card"></i>View Profile
                                </a>
                                <div class="actions-inline">
                                    <form class="js-role-action" data-action="accept" action="<?= ROOT ?>/director/accept_application?drama_id=<?= esc($dramaId) ?>" method="POST">
                                        <input type="hidden" name="application_id" value="<?= esc($application->id) ?>">
                                        <button type="submit" class="btn btn-success"><i class="bx bx-check"></i>Accept</button>
                                    </form>
                                    <form class="js-role-action" data-action="reject" action="<?= ROOT ?>/director/reject_application?drama_id=<?= esc($dramaId) ?>" method="POST" data-confirm="Reject this application?">
                                        <input type="hidden" name="application_id" value="<?= esc($application->id) ?>">
                                        <button type="submit" class="btn btn-danger"><i class="bx bx-times"></i>Reject</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($application->application_message)): ?>
                            <div class="prewrap-message message-spacing-md"><?= nl2br(esc($application->application_message)) ?></div>
                        <?php endif; ?>
                        <?php if ($interviewScheduled): ?>
                            <div class="interview-summary">
                                <i class="bx bx-video"></i>
                                Scheduled for <?= esc(date('Y-m-d H:i', strtotime($application->interview_at))) ?>
                                <span class="status-badge interview-status-badge <?= $interviewStatus === 'completed' ? 'assigned' : ($interviewStatus === 'cancelled' ? 'unassigned' : 'pending') ?>">
                                    <?= esc($interviewStatus === '' ? 'pending' : $interviewStatus) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                        <?php if ($confirmationStatus !== 'pending'): ?>
                            <div class="interview-confirmation interview-confirmation-box <?= esc($confirmationToneClass) ?>">
                                <div class="interview-confirmation-header">
                                    <strong class="interview-confirmation-title">
                                        <i class="bx <?= $confirmationStatus === 'confirmed' ? 'bx-user-check' : 'bx-user-times' ?>"></i>
                                        <?= $confirmationStatus === 'confirmed' ? 'Artist confirmed attendance' : 'Artist declined the interview' ?>
                                    </strong>
                                    <?php if (!$confirmationSeen): ?>
                                        <span class="status-badge assigned status-badge-new">New</span>
                                    <?php endif; ?>
                                </div>
                                <div class="interview-confirmation-meta">
                                    Received <?= !empty($application->interview_confirmed_at) ? esc(date('Y-m-d H:i', strtotime($application->interview_confirmed_at))) : 'just now' ?>
                                    <?php if (!empty($application->interview_confirmation_note)): ?>
                                        <div class="interview-confirmation-note">"<?= esc($application->interview_confirmation_note) ?>"</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section id="tab-vacancies" class="tab-content">
            <h4 class="section-title-topless">Publish a new vacancy</h4>
            <?php if (empty($publishableRoles)): ?>
                <div class="empty-state empty-state-spaced">
                    <i class="bx bx-check-double empty-state-icon empty-state-icon-md"></i>
                    All roles are currently filled. Update a role to open it for new applicants.
                </div>
            <?php else: ?>
                <form class="form-inline publish-vacancy-form js-role-action" data-action="publish" action="<?= ROOT ?>/director/publish_vacancy?drama_id=<?= esc($dramaId) ?>" method="POST">
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
                    <button type="submit" class="btn btn-primary publish-submit-btn"><i class="bx bx-bullhorn"></i>Publish Vacancy</button>
                </form>
            <?php endif; ?>
        </section>

        <section id="tab-published" class="tab-content">
            <h4 class="section-title-topless">Published vacancies</h4>
            <?php if (empty($publishedRoles)): ?>
                <div class="empty-state">
                    <i class="bx bx-briefcase empty-state-icon empty-state-icon-md"></i>
                    No active vacancies. Publish a role to reach available artists.
                </div>
            <?php else: ?>
                <?php foreach ($publishedRoles as $role): ?>
                    <div class="vacancy-card">
                        <div class="card-header">
                            <div>
                                <h4 class="card-title-compact"><?= esc($role->role_name ?? 'Role') ?></h4>
                                <div class="card-meta">
                                    <span><strong>Published:</strong> <?= esc(date('Y-m-d H:i', strtotime($role->published_at ?? 'now'))) ?></span>
                                    <?php if (!empty($role->director_name)): ?><span><strong>By:</strong> <?= esc($role->director_name) ?></span><?php endif; ?>
                                </div>
                            </div>
                            <form class="js-role-action" data-action="unpublish" action="<?= ROOT ?>/director/unpublish_vacancy?drama_id=<?= esc($dramaId) ?>" method="POST" data-confirm="Unpublish this vacancy?">
                                <input type="hidden" name="role_id" value="<?= esc($role->id) ?>">
                                <button type="submit" class="btn btn-secondary"><i class="bx bx-eye-slash"></i>Unpublish</button>
                            </form>
                        </div>
                        <?php if (!empty($role->published_message)): ?>
                            <div class="prewrap-message message-spacing-sm"><?= nl2br(esc($role->published_message)) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <section id="tab-requests" class="tab-content">
            <?php if (empty($pendingRequests)): ?>
                <div class="empty-state">
                    <i class="bx bx-users empty-state-icon empty-state-icon-md"></i>
                    No pending direct requests. Use "Assign Artist" to reach out to performers.
                </div>
            <?php else: ?>
                <?php foreach ($pendingRequests as $request): ?>
                    <?php
                        $requestStatus = strtolower((string)($request->status ?? 'pending'));
                        $requestStatusLabel = $requestStatus === 'interview' ? 'Interview' : 'Pending';
                        $interviewValue = !empty($request->interview_at) ? date('Y-m-d\TH:i', strtotime($request->interview_at)) : '';
                    ?>
                    <div class="request-card">
                        <div class="card-header">
                            <div>
                                <h4 class="card-title-compact"><?= esc($request->artist_name ?? 'Artist') ?></h4>
                                <div class="card-meta">
                                    <span><strong>Role:</strong> <?= esc($request->role_name ?? 'Role') ?></span>
                                    <span><strong>Requested:</strong> <?= esc(date('Y-m-d H:i', strtotime($request->requested_at ?? 'now'))) ?></span>
                                </div>
                                <div class="card-meta">
                                    <span><strong>Status:</strong> <?= esc($requestStatusLabel) ?></span>
                                    <?php if (!empty($request->interview_at)): ?>
                                        <span><strong>Interview:</strong> <?= esc(date('Y-m-d H:i', strtotime($request->interview_at))) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="actions-inline">
                                <a class="btn btn-secondary" href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($request->role_id) ?>"><i class="fas fa-eye"></i>View Role</a>
                                <form class="js-role-action" action="<?= ROOT ?>/director/remove_role_request?drama_id=<?= esc($dramaId) ?>" method="POST" data-confirm="Remove this artist request?">
                                    <input type="hidden" name="request_id" value="<?= esc($request->id) ?>">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i>Remove</button>
                                </form>
                            </div>
                        </div>
                        <?php if (!empty($request->note)): ?>
                            <div class="prewrap-message message-spacing-sm"><?= nl2br(esc($request->note)) ?></div>
                        <?php endif; ?>
                        <details class="request-edit-block" style="margin-top: 10px;">
                            <summary style="cursor: pointer; color: var(--ink); font-weight: 600;">Edit Request</summary>
                            <form class="js-role-action" action="<?= ROOT ?>/director/update_role_request?drama_id=<?= esc($dramaId) ?>" method="POST" style="margin-top: 10px;">
                                <input type="hidden" name="request_id" value="<?= esc($request->id) ?>">
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="request_status_<?= esc($request->id) ?>">Status</label>
                                    <select id="request_status_<?= esc($request->id) ?>" name="status" class="form-control" required>
                                        <option value="pending" <?= $requestStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="interview" <?= $requestStatus === 'interview' ? 'selected' : '' ?>>Interview</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="request_interview_<?= esc($request->id) ?>">Interview date & time (optional)</label>
                                    <input type="datetime-local" id="request_interview_<?= esc($request->id) ?>" name="interview_at" class="form-control" value="<?= esc($interviewValue) ?>">
                                </div>
                                <div class="form-group" style="margin-bottom: 10px;">
                                    <label for="request_note_<?= esc($request->id) ?>">Note (optional)</label>
                                    <textarea id="request_note_<?= esc($request->id) ?>" name="note" class="form-control" rows="3" maxlength="1000" placeholder="Add or update request note"><?= esc($request->note ?? '') ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i>Update Request</button>
                            </form>
                        </details>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
