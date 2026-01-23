<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Manager Dashboard - Rangamadala</title>
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
                <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-theater-masks"></i>
                    <span>Theater Bookings</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Service Schedule</span>
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
                <span>Production Manager Dashboard</span>
                <h2><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    Certificate: <?= isset($drama->certificate_number) ? esc($drama->certificate_number) : 'N/A' ?> | Status: <span class="status-badge assigned">Active</span>
                </p>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="fas fa-user-tie"></i>
                    Production Manager
                </div>
                <img src="<?= ROOT ?>/assets/images/default-avatar.jpg" alt="Avatar">
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
            <div class="stat-card" style="background: linear-gradient(135deg, var(--brand), var(--brand-strong));">
                <h3><?= isset($theaterBookings) && is_array($theaterBookings) ? count($theaterBookings) : '0' ?></h3>
                <p>Theater Bookings</p>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-briefcase"></i> Manage Services
            </a>
            <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-chart-bar"></i> Budget Management
            </a>
            <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-theater-masks"></i> Theater Bookings
            </a>
            <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="nav-tab-btn">
                <i class="fas fa-calendar-alt"></i> Service Schedule
            </a>
        </div>

        <!-- Content Sections -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Budget Overview Section -->
                    <div class="card-section">
                        <h3>
                            <i class="fas fa-wallet" style="color: var(--brand);"></i>
                            <span>Budget Overview</span>
                            <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-pencil-alt"></i>
                                Manage Budget
                            </a>
                        </h3>
                        <?php if (isset($totalBudget) && $totalBudget > 0): ?>
                            <div style="display: flex; align-items: center; gap: 20px; margin-top: 16px;">
                                <div style="flex: 1;">
                                    <div style="background: #f0f0f0; border-radius: 10px; height: 30px; overflow: hidden;">
                                        <div style="background: linear-gradient(90deg, var(--brand), var(--brand-strong)); width: <?= isset($budgetUsed) ? round(($budgetUsed / $totalBudget) * 100) : 0 ?>%; height: 100%; border-radius: 10px; transition: width 0.3s ease;"></div>
                                    </div>
                                    <p style="font-size: 12px; color: var(--muted); margin-top: 6px;"><?= isset($budgetUsed) ? round(($budgetUsed / $totalBudget) * 100) : 0 ?>% of budget used (LKR <?= isset($budgetUsed) ? number_format($budgetUsed) : '0' ?> of LKR <?= isset($totalBudget) ? number_format($totalBudget) : '0' ?>)</p>
                                </div>
                            </div>
                            <div class="drama-info" style="margin-top: 16px;">
                                <div class="service-info-item">
                                    <span class="service-info-label">Total Budget</span>
                                    <span class="service-info-value">LKR <?= number_format($totalBudget) ?></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Budget Used</span>
                                    <span class="service-info-value">LKR <?= number_format($budgetUsed) ?></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Remaining Budget</span>
                                    <span class="service-info-value">LKR <?= number_format($totalBudget - $budgetUsed) ?></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label">Budget Status</span>
                                    <span class="service-info-value"><span class="status-badge <?= ($budgetUsed / $totalBudget) < 0.9 ? 'assigned' : 'pending' ?>">On Track</span></span>
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 30px; color: var(--muted);">
                                <p>No budget allocated yet. <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">Set budget now</a></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Service Requests Section -->
                    <div class="card-section">
                        <h3>
                            <span>Recent Service Requests</span>
                            <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-secondary" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-arrow-right"></i>
                                View All Services
                            </a>
                        </h3>
                        <?php if (isset($services) && is_array($services) && !empty($services)): ?>
                            <ul>
                                <?php foreach (array_slice($services, 0, 4) as $service): ?>
                                    <li>
                                        <div>
                                            <strong><?= isset($service->service_required) ? esc($service->service_required) : 'Service' ?></strong>
                                            <div class="request-info">From: <?= isset($service->requester_name) ? esc($service->requester_name) : 'N/A' ?> | Requested: <?= isset($service->created_at) ? date('Y-m-d', strtotime($service->created_at)) : 'N/A' ?></div>
                                        </div>
                                        <span class="status-badge <?= $service->status === 'confirmed' ? 'assigned' : ($service->status === 'pending' ? 'pending' : 'requested') ?>"><?= isset($service->status) ? ucfirst($service->status) : 'Pending' ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div style="text-align: center; padding: 30px; color: var(--muted);">
                                <p>No service requests yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Theater Bookings Section -->
                    <div class="card-section">
                        <h3>
                            <span>Upcoming Theater Bookings</span>
                            <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-success" style="font-size: 12px; padding: 8px 16px;">
                                <i class="fas fa-plus"></i>
                                Book Theater
                            </a>
                        </h3>
                        <?php if (isset($theaterBookings) && is_array($theaterBookings) && !empty($theaterBookings)): ?>
                            <ul>
                                <?php foreach ($theaterBookings as $booking): ?>
                                    <li>
                                        <div>
                                            <strong><?= isset($booking->theater_name) ? esc($booking->theater_name) : 'Theater' ?></strong>
                                            <div class="request-info">Date: <?= isset($booking->booking_date) ? date('M d, Y', strtotime($booking->booking_date)) : 'N/A' ?> | Time: <?= isset($booking->start_time) && isset($booking->end_time) ? esc($booking->start_time) . ' - ' . esc($booking->end_time) : 'N/A' ?> | Cost: LKR <?= isset($booking->cost) ? number_format($booking->cost) : '0' ?></div>
                                        </div>
                                        <span class="status-badge <?= $booking->status === 'confirmed' ? 'assigned' : ($booking->status === 'pending' ? 'pending' : 'requested') ?>"><?= isset($booking->status) ? ucfirst($booking->status) : 'Pending' ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div style="text-align: center; padding: 30px; color: var(--muted);">
                                <p>No theater bookings yet. <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">Book a theater</a></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Service Schedule Section -->
                    <div class="card-section">
                        <h3>
                            <span>Service Schedule</span>
                            <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">Manage Schedule</a>
                        </h3>
                        <?php if (isset($schedules) && is_array($schedules) && !empty($schedules)): ?>
                            <ul id="serviceScheduleList">
                                <?php foreach ($schedules as $schedule): ?>
                                    <li>
                                        <div>
                                            <strong><?= isset($schedule->service_name) ? esc($schedule->service_name) : 'Service' ?></strong>
                                            <div class="request-info">Date: <?= isset($schedule->scheduled_date) ? date('Y-m-d', strtotime($schedule->scheduled_date)) : 'N/A' ?> | Time: <?= isset($schedule->scheduled_time) ? esc($schedule->scheduled_time) : 'N/A' ?> | Venue: <?= isset($schedule->venue) ? esc($schedule->venue) : 'N/A' ?></div>
                                        </div>
                                        <span class="status-badge <?= $schedule->status === 'scheduled' ? 'assigned' : 'pending' ?>"><?= isset($schedule->status) ? ucfirst($schedule->status) : 'Pending' ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div style="text-align: center; padding: 30px; color: var(--muted);">
                                <p>No scheduled services yet. <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">Add a schedule</a></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card-section">
                        <h3>Quick Actions</h3>
                        <div class="permission-controls">
                            <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-primary">
                                <i class="fas fa-briefcase"></i>
                                Manage Services
                            </a>
                            <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-success">
                                <i class="fas fa-chart-bar"></i>
                                Manage Budget
                            </a>
                            <a href="<?= ROOT ?>/production_manager/book_theater?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-warning">
                                <i class="fas fa-theater-masks"></i>
                                Book Theater
                            </a>
                            <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="btn btn-secondary">
                                <i class="fas fa-calendar-alt"></i>
                                Service Schedule
                            </a>
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
