<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

if (!isset($drama) && isset($data['drama'])) {
    $drama = $data['drama'];
}

$currentManager = $data['currentManager'] ?? null;
$pendingRequests = $data['pendingRequests'] ?? [];
$drama_id = isset($drama->id) ? (int)$drama->id : ($_GET['drama_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Manager - <?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $drama_id ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= $drama_id ?>">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= $drama_id ?>">
                    <i class="fas fa-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= $drama_id ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= $drama_id ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= $drama_id ?>">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Services & Budget</span>
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
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $drama_id ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <h2>Production Manager</h2>
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?>">
                <i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Info Box -->
        <div class="info-box" style="margin-bottom: 30px;">
            <i class="fas fa-info-circle"></i>
            <strong>About Production Managers:</strong> The Production Manager oversees services, budget, and theater bookings for this drama. You can assign one PM at a time.
        </div>

        <!-- Content -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Current Manager Section -->
                    <div class="card-section">
                        <h3>
                            <span><i class="fas fa-user-tie"></i> Current Production Manager</span>
                            <?php if ($currentManager): ?>
                                <button class="btn btn-secondary" style="font-size: 12px; padding: 8px 16px; cursor: not-allowed; opacity: 0.7;" title="Remove the current PM before assigning a new one" disabled>
                                    <i class="fas fa-exchange-alt"></i>
                                    Change Manager
                                </button>
                            <?php else: ?>
                                <a href="<?= ROOT ?>/director/search_managers?drama_id=<?= $drama_id ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                    <i class="fas fa-user-plus"></i>
                                    Assign Manager
                                </a>
                            <?php endif; ?>
                        </h3>
                        <ul>
                            <?php if ($currentManager): ?>
                                <li>
                                    <div>
                                        <strong><?= esc($currentManager->manager_name) ?></strong>
                                        <div class="request-info">
                                            Email: <?= esc($currentManager->email) ?> | Phone: <?= esc($currentManager->phone ?? 'N/A') ?>
                                        </div>
                                        <div class="request-info">
                                            <strong>Assigned:</strong> <?= date('F j, Y', strtotime($currentManager->assigned_at)) ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Active</span>
                                        <form method="POST" action="<?= ROOT ?>/director/remove_manager?drama_id=<?= $drama_id ?>" 
                                              style="display: inline;" onsubmit="return confirm('Are you sure you want to remove this Production Manager?');">
                                            <button type="submit" class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;">
                                                <i class="fas fa-user-times"></i>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            <?php else: ?>
                                <li style="text-align: center; padding: 40px 20px; color: var(--muted);">
                                    <i class="fas fa-user-slash" style="font-size: 2em; margin-bottom: 10px; display: block;"></i>
                                    <strong>No Production Manager Assigned</strong>
                                    <p style="margin: 10px 0 0 0;">Click "Assign Manager" button above to assign one.</p>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Manager Permissions (if assigned) -->
                    <?php if ($currentManager): ?>
                        <div class="card-section">
                            <h3><i class="fas fa-key"></i> Manager Permissions</h3>
                            <div class="drama-info">
                                <div class="service-info-item">
                                    <span class="service-info-label">Services Management</span>
                                    <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Budget Management</span>
                                    <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Theater Bookings</span>
                                    <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Payment Tracking</span>
                                    <span class="service-info-value"><span class="status-badge assigned">Full Access</span></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Pending Requests Section -->
                    <?php if (!empty($pendingRequests)): ?>
                        <div class="card-section">
                            <h3><i class="fas fa-clock"></i> Pending Manager Requests (<?= count($pendingRequests) ?>)</h3>
                            <ul>
                                <?php foreach ($pendingRequests as $request): ?>
                                    <li>
                                        <div>
                                            <strong><?= esc($request->artist_name ?? 'Unknown Artist') ?></strong>
                                            <div class="request-info">
                                                <?= esc($request->artist_email ?? 'N/A') ?>
                                            </div>
                                            <div class="request-info">
                                                <strong>Requested:</strong> <?= date('F j, Y g:i A', strtotime($request->requested_at)) ?>
                                            </div>
                                            <?php if (!empty($request->message)): ?>
                                                <div class="request-info" style="font-style: italic; margin-top: 8px; padding: 8px; background: rgba(0,0,0,0.08); border-radius: 4px;">
                                                    "<?= esc($request->message) ?>"
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div style="display: flex; align-items: center;">
                                            <span class="status-badge pending">Pending</span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </main>

    <style>
        .manager-profile {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            padding: 20px;
            background: rgba(212, 175, 55, 0.03);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .manager-avatar img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--gold);
        }

        .manager-details h4 {
            color: var(--gold);
            margin: 0 0 10px 0;
            font-size: 1.4em;
        }

        .manager-details p {
            margin: 5px 0;
            color: var(--text-light);
        }

        .manager-details i {
            color: var(--gold);
            width: 20px;
        }

        .assigned-date {
            color: var(--gold) !important;
            font-weight: 600;
            margin-top: 10px !important;
        }

        .manager-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4em;
            color: var(--gold);
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 20px;
        }

        .requests-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .request-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: rgba(212, 175, 55, 0.03);
            border-radius: 8px;
            border-left: 3px solid var(--gold);
        }

        .request-info h4 {
            color: var(--gold);
            margin: 0 0 5px 0;
        }

        .request-info p {
            margin: 3px 0;
            color: var(--text-light);
            font-size: 0.9em;
        }

        .request-info i {
            color: var(--gold);
            width: 18px;
        }

        .request-date {
            color: var(--text-muted) !important;
            font-size: 0.85em !important;
        }

        .request-message {
            margin-top: 8px !important;
            padding: 8px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            font-style: italic;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            border: 1px solid #ffc107;
        }

        .info-box {
            background: rgba(33, 150, 243, 0.1);
            border-left: 4px solid #2196f3;
            padding: 15px 20px;
            border-radius: 4px;
            color: var(--text-light);
        }

        .info-box i {
            color: #2196f3;
            margin-right: 10px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            border-left: 4px solid #4caf50;
            color: #4caf50;
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.1);
            border-left: 4px solid #f44336;
            color: #f44336;
        }

        .alert i {
            margin-right: 10px;
        }
    </style>
</body>
</html>
