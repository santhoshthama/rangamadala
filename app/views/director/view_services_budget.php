<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$dramaId = isset($drama->id) ? (int)$drama->id : (isset($_GET['drama_id']) ? (int)$_GET['drama_id'] : 0);

$services = isset($services) && is_array($services) ? $services : [];
$budgetItems = isset($budgetItems) && is_array($budgetItems) ? $budgetItems : [];
$budgetCategories = isset($budgetCategories) && is_array($budgetCategories) ? $budgetCategories : [];
$theaterBookings = isset($theaterBookings) && is_array($theaterBookings) ? $theaterBookings : [];
$budgetSummary = isset($budgetSummary) && is_array($budgetSummary) ? $budgetSummary : [];

$getField = static function ($item, string $key, $default = null) {
    if (is_array($item) && array_key_exists($key, $item)) {
        return $item[$key];
    }

    if (is_object($item) && isset($item->$key)) {
        return $item->$key;
    }

    return $default;
};

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
    <title>Services & Budget - Rangamadala</title>
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
            <li>
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= $dramaId ?>">
                    <i class="bx bx-dollar-sign"></i>
                    <span>Services & Budget</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard">
                    <i class="bx bx-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $dramaId ?>" class="back-button">
            <i class="bx bx-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
                <h2>Services & Budget Overview</h2>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-video"></i> Director
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="Director Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                    <i class="bx bx-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <!-- View-Only Notice -->
        <div class="view-only-notice" style="margin-bottom: 30px;">
            <i class="bx bx-eye"></i>
            <strong>View-Only Access:</strong> Services and Budget are managed by your Production Managers. You can view all details but cannot make changes.
        </div>

        <!-- Budget Summary Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= isset($budgetSummary['total_budget']) ? 'LKR ' . number_format((float)$budgetSummary['total_budget'], 0) : '—' ?></h3>
                <p>Total Budget</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #1f9b3b);">
                <h3><?= isset($budgetSummary['used_budget']) ? 'LKR ' . number_format((float)$budgetSummary['used_budget'], 0) : '—' ?></h3>
                <p>Budget Used<?= isset($budgetSummary['used_percentage']) ? ' (' . (float)$budgetSummary['used_percentage'] . '%)' : '' ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #e0a800);">
                <h3><?= isset($budgetSummary['remaining_budget']) ? 'LKR ' . number_format((float)$budgetSummary['remaining_budget'], 0) : '—' ?></h3>
                <p>Remaining<?= isset($budgetSummary['remaining_percentage']) ? ' (' . (float)$budgetSummary['remaining_percentage'] . '%)' : '' ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, var(--danger), #c82333);">
                <h3><?= isset($budgetSummary['pending_payments']) ? 'LKR ' . number_format((float)$budgetSummary['pending_payments'], 0) : '—' ?></h3>
                <p>Pending Payments</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('services')">
                <i class="bx bx-handshake"></i>
                Services
            </button>
            <button class="tab-button" onclick="showTab('budget')">
                <i class="bx bx-dollar-sign"></i>
                Budget Items
            </button>
            <button class="tab-button" onclick="showTab('theaters')">
                <i class="bx bx-theater-masks"></i>
                Theater Bookings
            </button>
        </div>

        <!-- Tab: Services -->
        <div id="servicesTab" class="tab-content active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>
                                <span>Booked Services</span>
                            </h3>
                            <ul>
                                <?php if (!empty($services)): ?>
                                    <?php foreach ($services as $service): ?>
                                        <?php $serviceTitle = $getField($service, 'title', 'Service'); ?>
                                        <?php $serviceManager = $getField($service, 'managed_by'); ?>
                                        <?php $serviceDetails = $getField($service, 'details'); ?>
                                        <?php $serviceStatus = $getField($service, 'status', 'Status Unknown'); ?>
                                        <?php $servicePaymentStatus = $getField($service, 'payment_status'); ?>
                                        <li>
                                            <div>
                                                <strong><?= esc($serviceTitle) ?></strong>
                                                <?php if (!empty($serviceManager)): ?>
                                                    <div class="request-info">Managed by: <?= esc($serviceManager) ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($serviceDetails)): ?>
                                                    <div class="request-info"><?= esc($serviceDetails) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="display: flex; gap: 8px; align-items: center;">
                                                <span class="status-badge assigned"><?= esc($serviceStatus) ?></span>
                                                <?php if (!empty($servicePaymentStatus)): ?>
                                                    <span class="status-badge pending"><?= esc($servicePaymentStatus) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>
                                        <div>
                                            <strong>No service records available</strong>
                                            <div class="request-info">Production managers have not added service entries for this drama yet.</div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Budget Items -->
        <div id="budgetTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Budget Breakdown</h3>
                            <ul>
                                <?php if (!empty($budgetItems)): ?>
                                    <?php foreach ($budgetItems as $item): ?>
                                        <?php $itemTitle = $getField($item, 'title', 'Budget Item'); ?>
                                        <?php $itemDetails = $getField($item, 'details'); ?>
                                        <?php $itemAmount = $getField($item, 'amount'); ?>
                                        <?php $itemStatus = $getField($item, 'status'); ?>
                                        <li>
                                            <div>
                                                <strong><?= esc($itemTitle) ?></strong>
                                                <?php if (!empty($itemDetails)): ?>
                                                    <div class="request-info"><?= esc($itemDetails) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="display: flex; gap: 8px; align-items: center;">
                                                <span style="font-weight: 700; color: var(--brand);">
                                                    <?= $itemAmount !== null ? 'LKR ' . number_format((float)$itemAmount, 0) : '—' ?>
                                                </span>
                                                <?php if (!empty($itemStatus)): ?>
                                                    <span class="status-badge pending"><?= esc($itemStatus) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>
                                        <div>
                                            <strong>No budget items available</strong>
                                            <div class="request-info">Budget entries will appear here once they are added by production managers.</div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Budget by Category -->
                        <div class="card-section">
                            <h3>Budget by Category</h3>
                            <div class="drama-info">
                                <?php if (!empty($budgetCategories)): ?>
                                    <?php foreach ($budgetCategories as $category): ?>
                                        <?php $categoryName = $getField($category, 'name', 'Category'); ?>
                                        <?php $categoryAmount = $getField($category, 'amount'); ?>
                                        <?php $categoryPercentage = $getField($category, 'percentage'); ?>
                                        <div class="service-info-item">
                                            <span class="service-info-label"><?= esc($categoryName) ?></span>
                                            <span class="service-info-value">
                                                <?= $categoryAmount !== null ? 'LKR ' . number_format((float)$categoryAmount, 0) : '—' ?>
                                                <?= $categoryPercentage !== null ? ' (' . (float)$categoryPercentage . '%)' : '' ?>
                                            </span>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="service-info-item">
                                        <span class="service-info-label">No category totals available</span>
                                        <span class="service-info-value">—</span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Theater Bookings -->
        <div id="theatersTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Theater Bookings</h3>
                            <ul>
                                <?php if (!empty($theaterBookings)): ?>
                                    <?php foreach ($theaterBookings as $booking): ?>
                                        <?php $bookingVenue = $getField($booking, 'venue', 'Theater'); ?>
                                        <?php $bookingDetails = $getField($booking, 'details'); ?>
                                        <?php $bookingFee = $getField($booking, 'booking_fee'); ?>
                                        <?php $bookingStatus = $getField($booking, 'status', 'Status Unknown'); ?>
                                        <?php $bookingPaymentStatus = $getField($booking, 'payment_status'); ?>
                                        <li>
                                            <div>
                                                <strong><?= esc($bookingVenue) ?></strong>
                                                <?php if (!empty($bookingDetails)): ?>
                                                    <div class="request-info"><?= esc($bookingDetails) ?></div>
                                                <?php endif; ?>
                                                <?php if (!empty($bookingFee)): ?>
                                                    <div class="request-info">Booking Fee: LKR <?= number_format((float)$bookingFee, 0) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div style="display: flex; gap: 8px; align-items: center;">
                                                <span class="status-badge assigned"><?= esc($bookingStatus) ?></span>
                                                <?php if (!empty($bookingPaymentStatus)): ?>
                                                    <span class="status-badge pending"><?= esc($bookingPaymentStatus) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>
                                        <div>
                                            <strong>No theater bookings available</strong>
                                            <div class="request-info">Theater bookings will appear once they are scheduled by production managers.</div>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const dramaId = urlParams.get('drama_id') || 1;
        console.log('Current Drama ID:', dramaId);
    </script>
    <script src="/Rangamadala/public/assets/JS/view-services-budget.js"></script>
</body>
</html>
