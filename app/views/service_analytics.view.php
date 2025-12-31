<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_analytics.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title-section">
                <h1>Analytics Dashboard</h1>
                <p>Track your performance and insights</p>
            </div>
            <div class="period-selector">
                <button class="period-btn active" onclick="changePeriod('week')">Week</button>
                <button class="period-btn" onclick="changePeriod('month')">Month</button>
                <button class="period-btn" onclick="changePeriod('year')">Year</button>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="metrics-grid">
            <div class="metric-card revenue">
                <div class="metric-icon">💰</div>
                <div class="metric-content">
                    <h3>Total Revenue</h3>
                    <div class="metric-value">$11,200</div>
                    <div class="metric-change positive">
                        <span class="arrow">↑</span>
                        <span>12.5%</span>
                        <span class="change-label">from last month</span>
                    </div>
                </div>
            </div>

            <div class="metric-card bookings">
                <div class="metric-icon">📅</div>
                <div class="metric-content">
                    <h3>Total Bookings</h3>
                    <div class="metric-value">47</div>
                    <div class="metric-change positive">
                        <span class="arrow">↑</span>
                        <span>8.3%</span>
                        <span class="change-label">from last month</span>
                    </div>
                </div>
            </div>

            <div class="metric-card rating">
                <div class="metric-icon">⭐</div>
                <div class="metric-content">
                    <h3>Average Rating</h3>
                    <div class="metric-value">4.8</div>
                    <div class="metric-change positive">
                        <span class="arrow">↑</span>
                        <span>0.3</span>
                        <span class="change-label">from last month</span>
                    </div>
                </div>
            </div>

            <div class="metric-card completion">
                <div class="metric-icon">✓</div>
                <div class="metric-content">
                    <h3>Completion Rate</h3>
                    <div class="metric-value">94%</div>
                    <div class="metric-change negative">
                        <span class="arrow">↓</span>
                        <span>2%</span>
                        <span class="change-label">from last month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-container">
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
                    <canvas id="revenueChart"></canvas>
                    <div class="chart-placeholder">
                        <div class="bar-chart">
                            <div class="bar" style="height: 60%"><span>Jan<br>$800</span></div>
                            <div class="bar" style="height: 75%"><span>Feb<br>$1,200</span></div>
                            <div class="bar" style="height: 85%"><span>Mar<br>$1,500</span></div>
                            <div class="bar" style="height: 70%"><span>Apr<br>$1,100</span></div>
                            <div class="bar" style="height: 90%"><span>May<br>$1,700</span></div>
                            <div class="bar" style="height: 95%"><span>Jun<br>$1,900</span></div>
                        </div>
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
        <div class="activity-section">
            <div class="section-header">
                <h3>Recent Activity</h3>
                <button class="btn-view-all" onclick="viewAllActivity()">View All</button>
            </div>
            <div class="activity-list">
                <div class="activity-item">
                    <div class="activity-icon completed">✓</div>
                    <div class="activity-content">
                        <div class="activity-title">Completed service for "Romeo and Juliet"</div>
                        <div class="activity-time">2 hours ago</div>
                    </div>
                    <div class="activity-amount">+$500</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon new">★</div>
                    <div class="activity-content">
                        <div class="activity-title">Received 5-star review from Theatre Group ABC</div>
                        <div class="activity-time">5 hours ago</div>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon booking">📅</div>
                    <div class="activity-content">
                        <div class="activity-title">New booking for "Hamlet" lighting setup</div>
                        <div class="activity-time">1 day ago</div>
                    </div>
                    <div class="activity-amount">$750</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon payment">💳</div>
                    <div class="activity-content">
                        <div class="activity-title">Payment received for "Macbeth"</div>
                        <div class="activity-time">2 days ago</div>
                    </div>
                    <div class="activity-amount">+$1,200</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon completed">✓</div>
                    <div class="activity-content">
                        <div class="activity-title">Completed service for "A Midsummer Night's Dream"</div>
                        <div class="activity-time">3 days ago</div>
                    </div>
                    <div class="activity-amount">+$900</div>
                </div>
            </div>
        </div>

        <!-- Top Clients -->
        <div class="clients-section">
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
                            <span class="stat-value">$3,200</span>
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
                            <span class="stat-value">$2,100</span>
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
                            <span class="stat-value">$1,800</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Last Booking</span>
                            <span class="stat-value">3 days ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPeriod = 'month';

        function changePeriod(period) {
            currentPeriod = period;
            
            // Update button states
            document.querySelectorAll('.period-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update metrics based on period
            showMessage(`Viewing ${period}ly analytics`, 'info');
        }

        function updateRevenueChart(period) {
            showMessage(`Updating chart to ${period} view`, 'info');
        }

        function viewAllActivity() {
            showMessage('Loading all activity...', 'info');
        }

        function showMessage(text, type) {
            const message = document.createElement('div');
            message.className = `notification-message`;
            message.textContent = text;
            
            const colors = {
                success: { bg: '#28a745', color: 'white' },
                error: { bg: '#dc3545', color: 'white' },
                warning: { bg: '#ffc107', color: '#212529' },
                info: { bg: '#17a2b8', color: 'white' }
            };
            
            const color = colors[type] || colors.info;
            
            Object.assign(message.style, {
                position: 'fixed',
                top: '20px',
                right: '20px',
                padding: '16px 24px',
                background: color.bg,
                color: color.color,
                borderRadius: '8px',
                boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
                zIndex: '10000',
                fontWeight: '500',
                fontSize: '14px',
                maxWidth: '350px',
                animation: 'slideInRight 0.3s ease'
            });
            
            if (!document.getElementById('notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
                    @keyframes slideInRight {
                        from { transform: translateX(400px); opacity: 0; }
                        to { transform: translateX(0); opacity: 1; }
                    }
                    @keyframes slideOutRight {
                        from { transform: translateX(0); opacity: 1; }
                        to { transform: translateX(400px); opacity: 0; }
                    }
                `;
                document.head.appendChild(style);
            }
            
            document.body.appendChild(message);
            
            setTimeout(() => {
                message.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => message.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
