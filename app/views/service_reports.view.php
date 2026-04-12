<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= isset($pageTitle) ? $pageTitle : 'Reports' ?> - Rangamadala</title>
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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
                <div class="card--wrapper quick-template-grid">
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title">Monthly Revenue</span>
                            <p class="report-desc">Current month earnings breakdown</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('revenue', 'this_month')">
                            <i class="bx bx-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title"> Recent Bookings</span>
                            <p class="report-desc">This month bookings</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('bookings', 'this_month')">
                            <i class="bx bx-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title"> Service Performance</span>
                            <p class="report-desc">Service requests for this month</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('reviews', 'last_3_months')">
                            <i class="bx bx-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title"> Cancellations</span>
                            <p class="report-desc">Rejections & Cancellations in this month</p>
                        </div>
                        <button class="btn-download-report" onclick="quickReport('performance', 'this_year')">
                            <i class="bx bx-bolt"></i> Quick Generate
                        </button>
                    </div>
                </div>
            </div>

            <!-- Custom Report Generator -->
            <div class="card--container" style="margin-top: 20px;">
                <h3 class="main--title">Generate Custom Report</h3>
                <div class="card--wrapper">
                    <div class="productionCount--card filter-card">
                        <form id="reportForm" method="POST" class="report-filter-form">
                            <div class="filter-grid">
                                <!-- Report Type -->
                                <div class="filter-group">
                                    <label for="reportType">Report Type</label>
                                    <select id="reportType" name="reportType" required>
                                        
                                        <option value="revenue">Revenue Report</option>
                                        <option value="bookings">Bookings Report</option>
                                        <option value="performance">Service Performance</option>
                                        <option value="cancellation">Cancellation / Rejection</option>
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

                                <!-- Export Format -->
                                <div class="filter-group">
                                    <label for="exportFormat">Export Format</label>
                                    <select id="exportFormat" name="exportFormat">
                                        <option value="">View Report</option>
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
                                    <i class="bx bx-undo"></i> Reset
                                </button>
                                <button type="submit" class="btn-generate">
                                    <i class="bx bx-chart-bar"></i> Generate Report
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Report Results Section -->
            <?php if ($reportType): ?>
                <div class="card--container report-results-container" style="margin-top: 30px;">
                    <h3 class="main--title">
                        <?php 
                        $reportTitles = [
                            'revenue' => 'Revenue Report',
                            'bookings' => 'Bookings Report',
                            'performance' => 'Service Performance Report',
                            'cancellation' => 'Cancellation / Rejection Report'
                        ];
                        echo $reportTitles[$reportType] ?? 'Report';
                        ?>
                        <span style="font-size: 14px; color: #666; font-weight: normal;">
                            (<?= date('d M Y', strtotime($start_date)) ?> to <?= date('d M Y', strtotime($end_date)) ?>)
                        </span>
                    </h3>
                    
                    <!-- REVENUE REPORT -->
                    <?php if ($reportType === 'revenue' && isset($revenue_summary)): ?>
                        <div class="card--wrapper">
                            <!-- Summary Cards -->
                            <div class="report-summary-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
                                <div class="productionCount--card">
                                    <div class="Count">
                                        <span class="number">Rs <?= number_format($revenue_summary->total_revenue ?? 0, 2) ?></span>
                                        <span class="title">Total Revenue</span>
                                    </div>
                                </div>
                                <div class="productionCount--card">
                                    <div class="Count">
                                        <span class="number"><?= $revenue_summary->total_transactions ?? 0 ?></span>
                                        <span class="title">Total Transactions</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Revenue Table -->
                            <div class="productionCount--card filter-card">
                                <table class="report-table" style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #8b6914; color: white;">
                                            <th style="padding: 12px; text-align: left;">Transaction ID</th>
                                            <th style="padding: 12px; text-align: left;">Drama / Project</th>
                                            <th style="padding: 12px; text-align: left;">Service Type</th>
                                            <th style="padding: 12px; text-align: left;">Payment Type</th>
                                            <th style="padding: 12px; text-align: left;">Method</th>
                                            <th style="padding: 12px; text-align: right;">Amount</th>
                                            <th style="padding: 12px; text-align: left;">Date</th>
                                            <th style="padding: 12px; text-align: left;">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($revenue_report)): ?>
                                            <?php foreach ($revenue_report as $row): ?>
                                                <tr style="border-bottom: 1px solid #eee;">
                                                    <td style="padding: 12px;"><strong>PAY-<?= str_pad($row->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->drama_name) ?></td>
                                                    <td style="padding: 12px;"><span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= ucfirst(str_replace('_', ' ', $row->service_type)) ?></span></td>
                                                    <td style="padding: 12px;">
                                                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; <?php
                                                            echo ($row->payment_type === 'advance') ? 'background: #cfe2ff; color: #084298;' : 
                                                                (($row->payment_type === 'remaining') ? 'background: #fff3cd; color: #856404;' : 'background: #d4edda; color: #155724;');
                                                        ?>">
                                                            <?= ucfirst($row->payment_type) ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 12px;"><?= ucfirst($row->payment_gateway) ?></td>
                                                    <td style="padding: 12px; text-align: right;"><strong>Rs <?= number_format($row->amount, 2) ?></strong></td>
                                                    <td style="padding: 12px; font-size: 12px;"><?= date('d M Y', strtotime($row->paid_at ?? $row->created_at)) ?></td>
                                                    <td style="padding: 12px; font-size: 11px;"><?= $row->reference_number ?? 'N/A' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No revenue data available for this period</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <button class="btn-download-small">
                            <i class="bx bx-download"></i> Download
                        </button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Bookings Report - Q4 2025</div>
                            <div class="activity-time">Generated on Dec 30, 2025</div>
                        </div>
                        <button class="btn-download-small">
                            <i class="bx bx-download"></i> Download
                        </button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Performance Report - November 2025</div>
                            <div class="activity-time">Generated on Nov 30, 2025</div>
                        </div>
                        <button class="btn-download-small">
                            <i class="bx bx-download"></i> Download
                        </button>
                    </div>
                    <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">Reviews Report - October 2025</div>
                            <div class="activity-time">Generated on Oct 31, 2025</div>
                        </div>
                        <button class="btn-download-small">
                            <i class="bx bx-download"></i> Download
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <script>
                function toggleCustomDate() {
                    const dateRange = document.getElementById('dateRange').value;
                    const customSection = document.getElementById('customDateSection');
                    customSection.style.display = dateRange === 'custom' ? 'block' : 'none';
                }

                function resetFilters() {
                    document.getElementById('reportForm').reset();
                    document.getElementById('customDateSection').style.display = 'none';
                    document.getElementById('reportType').focus();
                }

                function quickReport(type, range) {
                    const form = document.getElementById('reportForm');
                    document.getElementById('reportType').value = type;
                    document.getElementById('dateRange').value = range;
                    
                    // Hide custom date section
                    document.getElementById('customDateSection').style.display = 'none';

                    // Submit directly so quick templates always work without extra submit handling
                    form.submit();
                }

                document.getElementById('reportForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const reportType = document.getElementById('reportType').value;
                    const dateRange = document.getElementById('dateRange').value;

                    if (!reportType) {
                        alert('Please select a report type');
                        return;
                    }

                    if (dateRange === 'custom') {
                        const startDate = document.getElementById('startDate').value;
                        const endDate = document.getElementById('endDate').value;

                        if (!startDate || !endDate) {
                            alert('Please select both start and end dates for custom range');
                            return;
                        }

                        if (new Date(startDate) > new Date(endDate)) {
                            alert('Start date cannot be after end date');
                            return;
                        }
                    }

                    // Show loading message
                    const btn = this.querySelector('.btn-generate');
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="bx bx-spinner bx-spin"></i> Generating...';
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
