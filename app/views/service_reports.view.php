<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= isset($pageTitle) ? $pageTitle : 'Reports' ?> - Rangamadala</title>
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            crossorigin="anonymous" />
        <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_dashboard.css">
        <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_reports.css">
        <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    </head>
    <body>
        <?php $activePage = 'reports'; include 'includes/service_provider/sidebar.php'; ?>

        <div class="main--content">
            <?php include 'includes/service_provider/header.php'; ?>

            <!-- Quick Report Templates -->
            <div class="card--container">
                <h3 class="main--title">Quick Report Templates</h3>
                <div class="card--wrapper">
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title">Monthly Revenue</span>
                            <p class="report-desc">Current month earnings breakdown</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('revenue', 'this_month')">
                            <i class="fas fa-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title">Recent Bookings</span>
                            <p class="report-desc">Last 30 days bookings</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('bookings', 'this_month')">
                            <i class="fas fa-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title">Client Reviews</span>
                            <p class="report-desc">All ratings and feedback</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('reviews', 'last_3_months')">
                            <i class="fas fa-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title">Year Performance</span>
                            <p class="report-desc">Annual overview report</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('performance', 'this_year')">
                            <i class="fas fa-bolt"></i> Quick Generate
                        </button>
                    </div>
                </div>
            </div>

            <!-- Custom Report Generator -->
            <div class="card--container" style="margin-top: 20px;">
                <h3 class="main--title">Generate Custom Report</h3>
                <div class="card--wrapper">
                    <div class="productionCount--card filter-card">
                        <form id="reportForm" class="report-filter-form">
                            <div class="filter-grid">
                                <!-- Report Type -->
                                <div class="filter-group">
                                    <label for="reportType">Report Type</label>
                                    <select id="reportType" name="reportType" required>
                                        <option value="">Select Report Type</option>
                                        <option value="revenue">Revenue Report</option>
                                        <option value="bookings">Bookings Report</option>
                                        <option value="reviews">Reviews Report</option>
                                        <option value="performance">Performance Report</option>
                                        <option value="services">Services Report</option>
                                        <option value="clients">Client Report</option>
                                    </select>
                                </div>

                                <!-- Date Range Preset -->
                                <div class="filter-group">
                                    <label for="dateRange">Date Range</label>
                                    <select id="dateRange" name="dateRange" onchange="toggleCustomDate()">
                                        <option value="this_week">This Week</option>
                                        <option value="this_month" selected>This Month</option>
                                        <option value="last_month">Last Month</option>
                                        <option value="last_3_months">Last 3 Months</option>
                                        <option value="last_6_months">Last 6 Months</option>
                                        <option value="this_year">This Year</option>
                                        <option value="last_year">Last Year</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>

                                <!-- Service Category -->
                                <div class="filter-group">
                                    <label for="serviceCategory">Service Category</label>
                                    <select id="serviceCategory" name="serviceCategory">
                                        <option value="all">All Services</option>
                                        <option value="sound">Sound Engineering</option>
                                        <option value="lighting">Lighting</option>
                                        <option value="stage">Stage Design</option>
                                        <option value="equipment">Equipment Rental</option>
                                        <option value="technical">Technical Support</option>
                                    </select>
                                </div>

                                <!-- Status Filter -->
                                <div class="filter-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status">
                                        <option value="all">All Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="confirmed">Confirmed</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>

                                <!-- Export Format -->
                                <div class="filter-group">
                                    <label for="exportFormat">Export Format</label>
                                    <select id="exportFormat" name="exportFormat">
                                        <option value="pdf">PDF Document</option>
                                        <option value="excel">Excel Spreadsheet</option>
                                        <option value="csv">CSV File</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Custom Date Range (Hidden by default) -->
                            <div id="customDateSection" class="custom-date-section" style="display: none;">
                                <div class="filter-grid-custom">
                                    <div class="filter-group">
                                        <label for="startDate">Start Date</label>
                                        <input type="date" id="startDate" name="startDate" max="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="filter-group">
                                        <label for="endDate">End Date</label>
                                        <input type="date" id="endDate" name="endDate" max="<?= date('Y-m-d') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Generate Button -->
                            <div class="filter-actions">
                                <button type="button" class="btn-reset" onclick="resetFilters()">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn-generate">
                                    <i class="fas fa-chart-bar"></i> Generate Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Recent Reports -->
            <div class="chart-card" style="margin-top: 20px;">
                <div class="section-header">
                    <h3>Recent Reports</h3>
                </div>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Revenue Report - December 2025</div>
                            <div class="activity-time">Generated on Dec 31, 2025</div>
                        </div>
                        <button class="btn-download-small">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Bookings Report - Q4 2025</div>
                            <div class="activity-time">Generated on Dec 30, 2025</div>
                        </div>
                        <button class="btn-download-small">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Performance Report - November 2025</div>
                            <div class="activity-time">Generated on Nov 30, 2025</div>
                        </div>
                        <button class="btn-download-small">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Reviews Report - October 2025</div>
                            <div class="activity-time">Generated on Oct 31, 2025</div>
                        </div>
                        <button class="btn-download-small">
                            <i class="fas fa-download"></i> Download
                        </button>
                    </div>
                </div>
            </div>

            <script>
                function toggleCustomDate() {
                    const dateRange = document.getElementById('dateRange').value;
                    const customSection = document.getElementById('customDateSection');
                    customSection.style.display = dateRange === 'custom' ? 'block' : 'none';
                }

                function resetFilters() {
                    document.getElementById('reportForm').reset();
                    document.getElementById('customDateSection').style.display = 'none';
                }

                function quickReport(type, range) {
                    document.getElementById('reportType').value = type;
                    document.getElementById('dateRange').value = range;
                    document.getElementById('reportForm').dispatchEvent(new Event('submit'));
                }

                document.getElementById('reportForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const reportType = formData.get('reportType');
                    const dateRange = formData.get('dateRange');
                    const exportFormat = formData.get('exportFormat');
                    const serviceCategory = formData.get('serviceCategory');
                    const status = formData.get('status');
                    
                    let params = new URLSearchParams({
                        type: reportType,
                        range: dateRange,
                        format: exportFormat,
                        service: serviceCategory,
                        status: status
                    });

                    // Add custom dates if selected
                    if (dateRange === 'custom') {
                        const startDate = formData.get('startDate');
                        const endDate = formData.get('endDate');
                        
                        if (!startDate || !endDate) {
                            alert('Please select both start and end dates for custom range');
                            return;
                        }
                        
                        if (new Date(startDate) > new Date(endDate)) {
                            alert('Start date cannot be after end date');
                            return;
                        }
                        
                        params.append('start_date', startDate);
                        params.append('end_date', endDate);
                    }

                    // Show loading message
                    const btn = this.querySelector('.btn-generate');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
                    btn.disabled = true;

                    // Simulate report generation (replace with actual API call)
                    setTimeout(() => {
                        // In production, this would download the file
                        // window.location.href = '<?= ROOT ?>/ServiceReports/generate?' + params.toString();
                        
                        alert(`Generating ${reportType} report for ${dateRange} as ${exportFormat.toUpperCase()}\n\nFilters:\n- Service: ${serviceCategory}\n- Status: ${status}`);
                        
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }, 1500);
                });
            </script>
        </div>
    </body>
</html>
