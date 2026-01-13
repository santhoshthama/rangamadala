<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?> - Rangamadala</title>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            crossorigin="anonymous" />
        
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
                <div class="card--wrapper">
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Total Revenue</span>
                            <span class="Count-value">Rs. 840,000</span>
                            <div class="metric-change positive">
                                <span class="arrow">↑</span>
                                <span>12.5%</span>
                            </div>
                        </div>
                        <i class="fas fa-money-bill-wave icon"></i>
                    </div>
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Total Bookings</span>
                            <span class="Count-value">47</span>
                            <div class="metric-change positive">
                                <span class="arrow">↑</span>
                                <span>8.3%</span>
                            </div>
                        </div>
                        <i class="fas fa-calendar-check icon light-gold"></i>
                    </div>
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Average Rating</span>
                            <span class="Count-value">4.8 / 5.0</span>
                            <div class="metric-change positive">
                                <span class="arrow">↑</span>
                                <span>0.3</span>
                            </div>
                        </div>
                        <i class="fas fa-star icon"></i>
                    </div>
                    <div class="productionCount--card">
                        <div class="Count">
                            <span class="title">Completion Rate</span>
                            <span class="Count-value">94%</span>
                            <div class="metric-change negative">
                                <span class="arrow">↓</span>
                                <span>2%</span>
                            </div>
                        </div>
                        <i class="fas fa-check-circle icon light-gold"></i>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="charts-container" style="margin-top: 20px;">
                <!-- Revenue Chart -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h3>Revenue Trend</h3>
                        <select class="chart-filter" onchange="updateRevenueChart(this.value)">
                            <option value="weekly">Weekly</option>
                            <option value="monthly" selected>Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="chart-body">
                        <div class="bar-chart">
                            <div class="bar" style="height: 60%"><span>Jan<br>Rs. 60,000</span></div>
                            <div class="bar" style="height: 75%"><span>Feb<br>Rs. 90,000</span></div>
                            <div class="bar" style="height: 85%"><span>Mar<br>Rs. 112,500</span></div>
                            <div class="bar" style="height: 70%"><span>Apr<br>Rs. 82,500</span></div>
                            <div class="bar" style="height: 90%"><span>May<br>Rs. 127,500</span></div>
                            <div class="bar" style="height: 95%"><span>Jun<br>Rs. 142,500</span></div>
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
                            <div class="service-item">
                                <div class="service-info">
                                    <span class="service-name">Sound Equipment</span>
                                    <span class="service-count">18 bookings</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 38%"></div>
                                </div>
                                <span class="service-percentage">38%</span>
                            </div>
                            <div class="service-item">
                                <div class="service-info">
                                    <span class="service-name">Lighting Design</span>
                                    <span class="service-count">14 bookings</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 30%"></div>
                                </div>
                                <span class="service-percentage">30%</span>
                            </div>
                            <div class="service-item">
                                <div class="service-info">
                                    <span class="service-name">Costume Design</span>
                                    <span class="service-count">10 bookings</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 21%"></div>
                                </div>
                                <span class="service-percentage">21%</span>
                            </div>
                            <div class="service-item">
                                <div class="service-info">
                                    <span class="service-name">Stage Design</span>
                                    <span class="service-count">5 bookings</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: 11%"></div>
                                </div>
                                <span class="service-percentage">11%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="chart-card" style="margin-top: 20px;">
                <div class="section-header">
                    <h3>Recent Activity</h3>
                </div>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Completed service for "Romeo and Juliet"</div>
                            <div class="activity-time">2 hours ago</div>
                        </div>
                        <div class="activity-amount">+Rs. 37,500</div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Received 5-star review from Theatre Group ABC</div>
                            <div class="activity-time">5 hours ago</div>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">New booking for "Hamlet" lighting setup</div>
                            <div class="activity-time">1 day ago</div>
                        </div>
                        <div class="activity-amount">Rs. 56,250</div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Payment received for "Macbeth"</div>
                            <div class="activity-time">2 days ago</div>
                        </div>
                        <div class="activity-amount">+Rs. 90,000</div>
                    </div>
                </div>
            </div>

            <!-- Top Clients -->
            <div class="chart-card" style="margin-top: 20px;">
                <div class="section-header">
                    <h3>Top Clients</h3>
                </div>
                <div class="clients-grid">
                    <div class="client-card">
                        <div class="client-header">
                            <div class="client-avatar">TA</div>
                            <div class="client-info">
                                <h4>Theatre Group ABC</h4>
                                <span class="client-bookings">12 bookings</span>
                            </div>
                        </div>
                        <div class="client-stats">
                            <div class="stat">
                                <span class="stat-label">Total Spent</span>
                                <span class="stat-value">Rs. 240,000</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Last Booking</span>
                                <span class="stat-value">2 days ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="client-card">
                        <div class="client-header">
                            <div class="client-avatar">DS</div>
                            <div class="client-info">
                                <h4>Drama Society</h4>
                                <span class="client-bookings">8 bookings</span>
                            </div>
                        </div>
                        <div class="client-stats">
                            <div class="stat">
                                <span class="stat-label">Total Spent</span>
                                <span class="stat-value">Rs. 157,500</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Last Booking</span>
                                <span class="stat-value">1 week ago</span>
                            </div>
                        </div>
                    </div>
                    <div class="client-card">
                        <div class="client-header">
                            <div class="client-avatar">CT</div>
                            <div class="client-info">
                                <h4>City Theatre</h4>
                                <span class="client-bookings">6 bookings</span>
                            </div>
                        </div>
                        <div class="client-stats">
                            <div class="stat">
                                <span class="stat-label">Total Spent</span>
                                <span class="stat-value">Rs. 135,000</span>
                            </div>
                            <div class="stat">
                                <span class="stat-label">Last Booking</span>
                                <span class="stat-value">3 days ago</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script src="<?= ROOT ?>/assets/JS/service_provider_dashboard.js"></script>
        </div>
    </body>
</html>