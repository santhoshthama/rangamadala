<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?> - Rangamadala</title>
        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
        <!-- Boxicons -->
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <!-- Admin Design Library CSS -->
        <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css">
        <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Button.css">
        <!-- Service Provider Styles -->
        <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_dashboard.css">
        <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    </head>
    <body>
        <?php $activePage = 'dashboard'; include 'includes/service_provider/sidebar.php'; ?>

        
        <div class="main--content">
            <?php include 'includes/service_provider/header.php'; ?>
           
            <!-- Key Metrics Cards -->
            <div class="card--container">
                <h3 class="main--title">Overview</h3>
                <div class="card--wrapper overview-cards">
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Total Revenue</span>
                            <span class="Count-value">Rs. <?= number_format($total_revenue ?? 0, 2) ?></span>
                            <div class="metric-change <?= (($revenue_change ?? 0) >= 0) ? 'positive' : 'negative' ?>">
                                <span class="arrow"><?= (($revenue_change ?? 0) >= 0) ? '↑' : '↓' ?></span>
                                <span><?= number_format(abs($revenue_change ?? 0), 1) ?>%</span>
                            </div>
                        </div>
                        <i class="bx bx-wallet icon"></i>
                    </div>
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Total Bookings</span>
                            <span class="Count-value"><?= (int)($total_bookings ?? 0) ?></span>
                            <div class="metric-change <?= (($booking_change ?? 0) >= 0) ? 'positive' : 'negative' ?>">
                                <span class="arrow"><?= (($booking_change ?? 0) >= 0) ? '↑' : '↓' ?></span>
                                <span><?= number_format(abs($booking_change ?? 0), 1) ?>%</span>
                            </div>
                        </div>
                        <i class="bx bxs-calendar-check icon light-gold"></i>
                    </div>
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Active Services</span>
                            <span class="Count-value"><?= (int)($active_services ?? 0) ?></span>
                            <div class="metric-change positive">
                                <span class="arrow">●</span>
                                <span>Live</span>
                            </div>
                        </div>
                        <i class="bx bxs-star icon"></i>
                    </div>
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Completion Rate</span>
                            <span class="Count-value"><?= number_format($completion_rate ?? 0, 1) ?>%</span>
                            <div class="metric-change positive">
                                <span><?= (int)($completed_services ?? 0) ?> completed</span>
                            </div>
                        </div>
                        <i class="bx bxs-check-circle icon light-gold"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-container" style="margin-top: 20px;">
                <!-- Revenue Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Revenue Trend</h3>
                        <select class="chart-filter" 
                            onchange="window.location.href='<?= ROOT ?>/ServiceProviderDashboard?trend=' + this.value">
                            <option value="weekly" <?= ($trend_range ?? 'monthly') === 'weekly' ? 'selected' : '' ?>>Last 7 Days</option>
                            <option value="monthly" <?= ($trend_range ?? 'monthly') === 'monthly' ? 'selected' : '' ?>>Last 6 Months</option>
                            <option value="yearly" <?= ($trend_range ?? 'monthly') === 'yearly' ? 'selected' : '' ?>>Last 5 Years</option>
                        </select>
                    </div>
                    <div class="chart-body">
                        <div class="bar-chart">
                            <?php if (!empty($revenue_trend)): ?>
                                <?php foreach ($revenue_trend as $trend): ?>
                                    <div class="bar" style="height: <?= (int)($trend['height'] ?? 8) ?>%">
                                        <span><?= htmlspecialchars($trend['label'] ?? '-') ?><br>Rs. <?= number_format($trend['amount'] ?? 0, 0) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="bar" style="height: 8%"><span>No data<br>Rs. 0</span></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Service Distribution -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Service Distribution</h3>
                    </div>
                    <div class="chart-body">
                        <div class="service-stats">
                            <?php if (!empty($service_distribution)): ?>
                                <?php foreach ($service_distribution as $item): ?>
                                    <div class="service-item">
                                        <div class="service-info">
                                            <span class="service-name"><?= htmlspecialchars($item->service_label ?? 'N/A') ?></span>
                                            <span class="service-count"><?= (int)($item->booking_count ?? 0) ?> bookings</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?= (int)($item->percentage ?? 0) ?>%"></div>
                                        </div>
                                        <span class="service-percentage"><?= (int)($item->percentage ?? 0) ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="service-item">
                                    <div class="service-info">
                                        <span class="service-name">No service bookings yet</span>
                                        <span class="service-count">0 bookings</span>
                                    </div>
                                    <div class="progress-bar"><div class="progress-fill" style="width: 0%"></div></div>
                                    <span class="service-percentage">0%</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="chart-card" style="margin-top: 20px;">
                <div class="section-header">
                    <h3>Ongoing Services</h3>
                </div>
                <div class="activity-list">
                    <?php if (!empty($ongoing_services)): ?>
                        <?php foreach ($ongoing_services as $service): ?>
                            <div class="activity-item">
                                <div class="activity-content">
                                    <div class="activity-title">
                                        <?= htmlspecialchars($service->drama_name ?? 'N/A') ?> - <?= htmlspecialchars(ucwords(str_replace('_', ' ', $service->service_type ?? 'service'))) ?>
                                    </div>
                                    <div class="activity-time">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $service->status ?? 'pending'))) ?> | Updated: <?= date('d M Y', strtotime($service->updated_at ?? 'now')) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="activity-item">
                            <div class="activity-content">
                                <div class="activity-title">No ongoing services</div>
                                <div class="activity-time">New requests will appear here</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="chart-card" style="margin-top: 20px;">
                <div class="section-header">
                    <h3>Top Clients</h3>
                </div>
                <div class="clients-grid">
                    <?php if (!empty($top_clients)): ?>
                        <?php foreach ($top_clients as $client): ?>
                            <div class="client-card">
                                <div class="client-header">
                                    <div class="client-avatar"><?= htmlspecialchars($client->initials ?? 'NA') ?></div>
                                    <div class="client-info">
                                        <h4><?= htmlspecialchars($client->requester_name ?? 'N/A') ?></h4>
                                        <span class="client-bookings"><?= (int)($client->booking_count ?? 0) ?> bookings</span>
                                    </div>
                                </div>
                                <div class="client-stats">
                                    <div class="stat">
                                        <span class="stat-label">Total Spent</span>
                                        <span class="stat-value">Rs. <?= number_format($client->total_spent ?? 0, 2) ?></span>
                                    </div>
                                    <div class="stat">
                                        <span class="stat-label">Last Booking</span>
                                        <span class="stat-value"><?= !empty($client->last_booking) ? date('d M Y', strtotime($client->last_booking)) : 'N/A' ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="client-card">
                            <div class="client-header">
                                <div class="client-avatar">NA</div>
                                <div class="client-info">
                                    <h4>No clients yet</h4>
                                    <span class="client-bookings">0 bookings</span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <script src="<?= ROOT ?>/assets/JS/service_provider_dashboard.js"></script>
        </div>
    </body>
</html>