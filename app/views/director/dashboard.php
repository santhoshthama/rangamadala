<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

if (!isset($drama) && isset($data['drama'])) {
    $drama = $data['drama'];
}

$dramaId = isset($drama->id) ? (int)$drama->id : 0;
$dashboardStats = isset($dashboardStats) && is_array($dashboardStats) ? $dashboardStats : [];

$totalRoles = (int)($dashboardStats['total_roles'] ?? 0);
$totalPositions = (int)($dashboardStats['total_positions'] ?? 0);
$filledPositions = (int)($dashboardStats['filled_positions'] ?? 0);
$productionManagersCount = (int)($dashboardStats['production_managers'] ?? 0);
$pendingApplicationsCount = (int)($dashboardStats['pending_applications'] ?? 0);

require_once __DIR__ . '/_profile_image_helper.php';
$profileImageSrc = directorResolveProfileImageSrc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Dashboard - <?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="director-dashboard-page">
    <!-- Sidebar -->
    <?php
    $directorSidebarDramaId = $dramaId;
    $directorSidebarActive = 'dashboard';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/artistdashboard" class="back-button">
            <i class="bx bx-arrow-left"></i>
            Back to Profile
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Director Dashboard</span>
                <h2><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    <?= !empty($drama->description) ? esc($drama->description) : 'No description provided yet.' ?>
                </p>
            </div>
            <div class="user--info">
                <?php
                $directorProfileImageSrc = $profileImageSrc;
                $directorRoleLabel = 'Director';
                include __DIR__ . '/_partials/user_menu.php';
                ?>
            </div>
        </div>

        <!-- Statistics Cards for THIS Drama -->
        <div class="stats-grid director-stats-grid">
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Roles</div>
                    <div class="stat-card-icon primary">
                        <i class="bx bx-mask"></i>
                    </div>
                </div>
                <div class="stat-card-value" id="totalRoles"><?= $totalRoles ?></div>
            </div>
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Filled Roles</div>
                    <div class="stat-card-icon info">
                        <i class="bx bx-grid-alt"></i>
                    </div>
                </div>
                <div class="stat-card-value" id="filledRoles"><?= $filledPositions . '/' . $totalPositions ?></div>
            </div>
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Production Manager</div>
                    <div class="stat-card-icon success">
                        <i class="bx bx-briefcase"></i>
                    </div>
                </div>
                <div class="stat-card-value" id="productionManagers"><?= $productionManagersCount ?></div>
            </div>
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Pending Applications</div>
                    <div class="stat-card-icon warning">
                        <i class="bx bx-time-five"></i>
                    </div>
                </div>
                <div class="stat-card-value" id="pendingApplications"><?= $pendingApplicationsCount ?></div>
            </div>
        </div>



        <!-- Recent Dramas -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Drama Overview Card Section -->
                    <div class="card-section drama-overview-card">
                        <h3>
                            <span>Drama Overview</span>
                            <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= $dramaId ?>" class="btn btn-primary btn-compact">
                                <i class="bx bx-eye"></i>
                                View Details
                            </a>
                        </h3>
                        <div class="service-info-item">
                            <span class="service-info-label">Public Status</span>
                            <span class="service-info-value">
                                <?php if (!empty($drama->is_published)): ?>
                                    <span class="drama-overview-status is-published"><i class="bx bxs-check-circle"></i> Published</span>
                                <?php else: ?>
                                    <span class="drama-overview-status is-unpublished"><i class="bx bxs-x-circle"></i> Not Published</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label"><i class="bx bx-user"></i> Owner</span>
                            <span class="service-info-value"><?= isset($drama->owner_name) ? esc($drama->owner_name) : 'N/A' ?></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label"><i class="bx bx-id-card"></i> Certificate Number</span>
                            <span class="service-info-value"><?= isset($drama->certificate_number) ? esc($drama->certificate_number) : 'N/A' ?></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label"><i class="bx bx-file"></i> Certificate Document</span>
                            <span class="service-info-value">
                                <?php if (!empty($drama->certificate_image)): ?>
                                    <a href="<?= ROOT ?>/uploads/certificates/<?= esc($drama->certificate_image) ?>" target="_blank" rel="noopener">View certificate</a>
                                <?php else: ?>
                                    No certificate uploaded
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="service-info-item service-info-item--description">
                            <span class="service-info-label"><i class="bx bx-message-dots"></i> Description</span>
                            <span class="service-info-value service-info-value--description"><?= !empty($drama->description) ? esc($drama->description) : 'No description provided yet.' ?></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label"><i class="bx bx-calendar"></i> Created On</span>
                            <span class="service-info-value"><?= isset($drama->created_at) ? esc(date('Y-m-d H:i', strtotime($drama->created_at))) : 'N/A' ?></span>
                        </div>
                        <div class="service-info-item">
                            <span class="service-info-label"><i class="bx bx-refresh"></i> Last Updated</span>
                            <span class="service-info-value"><?= isset($drama->updated_at) ? esc(date('Y-m-d H:i', strtotime($drama->updated_at))) : 'N/A' ?></span>
                        </div>
                        <?php if (!empty($drama->published_at)): ?>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-check-circle"></i> Published On</span>
                                <span class="service-info-value"><?= esc(date('Y-m-d H:i', strtotime($drama->published_at))) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($productionManager) && $productionManager): ?>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-user"></i> Production Manager</span>
                                <span class="service-info-value"><?= esc((is_object($productionManager) && isset($productionManager->manager_name)) ? (string)$productionManager->manager_name : 'N/A') ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="dashboard-card-action">
                            <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= $dramaId ?>#publish-section" class="btn btn-primary btn-compact">
                                <i class="bx bx-bullhorn"></i>
                                <?= !empty($drama->is_published) ? 'Update Publish Details' : 'Publish Drama' ?>
                            </a>
                        </div>
                    </div>

                    <!-- Assigned Artists -->
                    <?php if (isset($assignedArtists) && !empty($assignedArtists)): ?>
                        <div class="card-section">
                            <h3>
                                <span>Assigned Artists (<?= count($assignedArtists) ?>)</span>
                                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= $dramaId ?>" class="btn btn-primary btn-compact">
                                    <i class="bx bx-users"></i>
                                    Manage Roles
                                </a>   
                                
                            </h3>
                            <ul>
                                <?php foreach ($assignedArtists as $artist): ?>
                                    <li>
                                        <div>
                                            <strong><?= esc($artist->artist_name) ?></strong>
                                            <div class="request-info">
                                                Role: <?= esc($artist->role_name) ?> (<?= esc(ucfirst($artist->role_type)) ?>)
                                                <?php if ($artist->assigned_at): ?>
                                                    | Assigned: <?= esc(date('M d, Y', strtotime($artist->assigned_at))) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="status-badge assigned"><i class="bx bxs-check-circle"></i> Active</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

         
                </div>
            </div>
        </div>
    </main>

    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
</body>
</html>
