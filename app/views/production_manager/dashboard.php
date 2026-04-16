<?php
// Get current user profile image
$userModel = new M_universal_profile();
$currentUser = $userModel->getUserById($_SESSION['user_id']);
$dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 1);

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
    <title>Production Manager Dashboard - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li class="active">
                <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-calendar-alt"></i>
                    <span>Service Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/profile">
                    <i class="bx bx-user-circle"></i>
                    <span>My Profile</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/logout">
                    <i class="bx bx-sign-out-alt"></i>
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
                <span>Production Manager Dashboard</span>
                <h2><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    Certificate: <?= isset($drama->certificate_number) ? esc($drama->certificate_number) : 'N/A' ?> | Status: <span class="status-badge assigned">Active</span>
                </p>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-user-tie"></i>
                    Production Manager
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="PM Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3>LKR <?= isset($totalBudget) ? number_format($totalBudget) : '0' ?></h3>
                <p>Total Budget Allocated</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3>LKR <?= isset($budgetUsed) ? number_format($budgetUsed) : '0' ?></h3>
                <p>Budget Used (<?= isset($totalBudget) && $totalBudget > 0 ? round(($budgetUsed / $totalBudget) * 100) : '0' ?>%)</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3><?= isset($services) && is_array($services) ? count($services) : '0' ?></h3>
                <p>Active Service Requests</p>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= $dramaId ?>" class="nav-tab-btn active">
                <i class="bx bx-home"></i> Dashboard
            </a>
            <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $dramaId ?>" class="nav-tab-btn">
                <i class="bx bx-briefcase"></i> Manage Services
            </a>
            <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= $dramaId ?>" class="nav-tab-btn">
                <i class="bx bx-chart-bar"></i> Budget Management
            </a>
            <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= $dramaId ?>" class="nav-tab-btn">
                <i class="bx bx-calendar-alt"></i> Service Schedule
            </a>
        </div>

        <!-- Content Sections -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Drama Details -->
                    <div class="card-section">
                        <h3>
                            <i class="bx bx-movie-play" style="color: var(--brand);"></i>
                            <span>Drama Details</span>
                        </h3>
                        <div class="drama-info" style="margin-top: 16px;">
                            <div class="service-info-item">
                                <span class="service-info-label">Drama ID</span>
                                <span class="service-info-value">#<?= isset($drama->id) ? (int)$drama->id : 'N/A' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Drama Name</span>
                                <span class="service-info-value"><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'N/A' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Number</span>
                                <span class="service-info-value"><?= isset($drama->certificate_number) ? esc($drama->certificate_number) : 'N/A' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Category</span>
                                <span class="service-info-value"><?= isset($drama->drama_category) ? esc($drama->drama_category) : 'N/A' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Production Period</span>
                                <span class="service-info-value">
                                    <?php
                                        $start = isset($drama->start_date) && $drama->start_date ? date('Y-m-d', strtotime($drama->start_date)) : null;
                                        $end = isset($drama->end_date) && $drama->end_date ? date('Y-m-d', strtotime($drama->end_date)) : null;
                                        echo $start || $end ? esc(($start ?? 'N/A') . ' to ' . ($end ?? 'N/A')) : 'N/A';
                                    ?>
                                </span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label">Description</span>
                                <span class="service-info-value"><?= isset($drama->description) && $drama->description !== '' ? esc($drama->description) : 'No description available' ?></span>
                            </div>
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
    </script>
    <script src="/Rangamadala/public/assets/JS/production-manager-dashboard.js"></script>
</body>
</html>
