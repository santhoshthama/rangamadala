<?php
$dramaId = isset($dramaId) ? (int)$dramaId : (int)($drama->id ?? 0);
$profileImageSrc = isset($profileImageSrc) && is_string($profileImageSrc) && $profileImageSrc !== ''
    ? $profileImageSrc
    : ROOT . '/assets/images/default-avatar.jpg';
$formatCurrency = static function ($amount) {
    return 'Rs. ' . number_format((float)$amount, 2);
};
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
<body class="director-dashboard-page">
    <?php $currentPage = 'dashboard'; require __DIR__ . '/_partials/sidebar.php'; ?>

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
                <?php
                    $pmProfileImageSrc = isset($profileImageSrc) && is_string($profileImageSrc) && $profileImageSrc !== ''
                        ? $profileImageSrc
                        : (ROOT . '/assets/images/default-avatar.jpg');
                    $pmRoleLabel = 'Production Manager';
                    $pmProfileUrl = ROOT . '/profile';
                    $pmLogoutUrl = ROOT . '/logout';
                    require __DIR__ . '/_partials/user_menu.php';
                ?>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Budget Allocated</div>
                    <div class="stat-card-icon primary">
                        <i class='bx bx-wallet'></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $formatCurrency(isset($totalBudget) ? $totalBudget : 0) ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Budget Used</div>
                    <div class="stat-card-icon info">
                        <i class='bx bx-line-chart'></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $formatCurrency(isset($budgetUsed) ? $budgetUsed : 0) ?></div>
                <p>Usage: <?= isset($totalBudget) && $totalBudget > 0 ? round(($budgetUsed / $totalBudget) * 100) : '0' ?>%</p>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Active Service Requests</div>
                    <div class="stat-card-icon success">
                        <i class='bx bx-briefcase-alt-2'></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($services) && is_array($services) ? count($services) : '0' ?></div>
            </div>
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
        
        console.log('Current Drama ID:', dramaId);
    </script>
    <script src="/Rangamadala/public/assets/JS/production-manager-dashboard.js"></script>
    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
</body>
</html>
