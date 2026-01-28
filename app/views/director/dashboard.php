<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

if (!isset($drama) && isset($data['drama'])) {
    $drama = $data['drama'];
}

// Get current user profile image
$userModel = new M_universal_profile();
$currentUser = $userModel->getUserById($_SESSION['user_id']);

$profileImageSrc = ROOT . '/assets/images/default-avatar.jpg';
if ($currentUser && !empty($currentUser->profile_image)) {
    $imageValue = str_replace('\\', '/', $currentUser->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif ($currentUser && !empty($currentUser->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $currentUser->nic_photo), '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Director Dashboard - <?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?> - Rangamadala</title>
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
            <li class="active">
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-dollar-sign"></i>
                    <span>Services & Budget</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/profile">
                    <i class="fas fa-user-circle"></i>
                    <span>My Profile</span>
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
        <a href="<?= ROOT ?>/artistdashboard" class="back-button">
            <i class="fas fa-arrow-left"></i>
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
                <div class="role-badge">
                    <i class="fas fa-video"></i>
                    Director
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="Director Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
            </div>
        </div>

        <!-- Statistics Cards for THIS Drama -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3 id="totalRoles">15</h3>
                <p>Total Roles</p>
            </div>
            <div class="stat-card">
                <h3 id="filledRoles">12/15</h3>
                <p>Filled Roles</p>
            </div>
            <div class="stat-card">
                <h3 id="productionManagers">1</h3>
                <p>Production Manager</p>
            </div>
            <div class="stat-card">
                <h3 id="pendingApplications">8</h3>
                <p>Pending Applications</p>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-film"></i>  Drama Details
            </a>
            <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-users"></i> Artist Roles
            </a>
            <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-user-tie"></i> Production Manager
            </a>
            <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-calendar-alt"></i> Schedule
            </a>
            <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-dollar-sign"></i> Services & Budget
            </a>
        </div>

        <!-- Recent Dramas -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Drama Overview Card Section -->
                    <div class="card-section">
                        <h3>
                            <span>Drama Overview</span>
                            <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                        </h3>
                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label">Owner</span>
                                <span class="service-info-value"><?= isset($drama->owner_name) ? esc($drama->owner_name) : 'N/A' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Number</span>
                                <span class="service-info-value"><?= isset($drama->certificate_number) ? esc($drama->certificate_number) : 'N/A' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Document</span>
                                <span class="service-info-value">
                                    <?php if (!empty($drama->certificate_image)): ?>
                                        <a href="<?= ROOT ?>/uploads/certificates/<?= esc($drama->certificate_image) ?>" target="_blank" rel="noopener">View certificate</a>
                                    <?php else: ?>
                                        No certificate uploaded
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="service-info-item" style="flex-direction: column; align-items: flex-start; gap: 6px;">
                                <span class="service-info-label">Description</span>
                                <span class="service-info-value" style="white-space: pre-wrap;"><?= !empty($drama->description) ? esc($drama->description) : 'No description provided yet.' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Created On</span>
                                <span class="service-info-value"><?= isset($drama->created_at) ? esc(date('Y-m-d H:i', strtotime($drama->created_at))) : 'N/A' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Last Updated</span>
                                <span class="service-info-value"><?= isset($drama->updated_at) ? esc(date('Y-m-d H:i', strtotime($drama->updated_at))) : 'N/A' ?></span>
                            </div>
                        </div>
                            <?php if (isset($productionManager) && $productionManager): ?>
                                <div class="service-info-item">
                                    <span class="service-info-label">Production Manager</span>
                                    <span class="service-info-value"><?= esc($productionManager->manager_name ?? 'N/A') ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Assigned Artists -->
                    <?php if (isset($assignedArtists) && !empty($assignedArtists)): ?>
                        <div class="card-section">
                            <h3>
                                <span>Assigned Artists (<?= count($assignedArtists) ?>)</span>
                                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                    <i class="fas fa-users"></i>
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
                                        <span class="status-badge assigned">Active</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Services & Budget Overview -->
                    <div class="card-section">
                        <h3>
                            <span>Services & Budget Overview</span>
                            <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">View Details</a>
                        </h3>
                        <div class="view-only-notice" style="margin-top: 15px;">
                            <i class="fas fa-info-circle"></i>
                            Budget is managed by Production Managers. You have view-only access.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Get drama_id from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const dramaId = urlParams.get('drama_id') || 1;
        
        // Mark active navigation tab based on current page
        const currentPage = window.location.pathname.split('/').pop();
        const navTabs = document.querySelectorAll('.nav-tab-btn');
        
        navTabs.forEach(tab => {
            // Remove active class from all tabs
            tab.classList.remove('active');
            
            // Add active class to matching tab
            const href = tab.getAttribute('href');
            if (href && href.includes(currentPage)) {
                tab.classList.add('active');
            }
        });
        
        // Special case: if on dashboard.php, mark dashboard tab as active
        if (currentPage === 'dashboard.php' || currentPage === '') {
            navTabs[0]?.classList.add('active');
        }
        
        console.log('Current Drama ID:', dramaId);
        // Backend will use this to load drama-specific data
    </script>
    <script src="/Rangamadala/public/assets/JS/director-dashboard.js"></script>
</body>
</html>
