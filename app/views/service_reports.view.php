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
                <div class="card--wrapper quick-template-grid">
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title">Monthly Revenue</span>
                            <p class="report-desc">Current month earnings breakdown</p>
                        </div>
                        <button type="button" class="btn-download-report" onclick="quickReport('revenue', 'this_month')">
                            <i class="fas fa-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title"> Recent Bookings</span>
                            <p class="report-desc">This month bookings</p>
                        </div>
                        <button type="button" class="btn-download-report" onclick="quickReport('bookings', 'this_month')">
                            <i class="fas fa-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title"> Service Performance</span>
                            <p class="report-desc">Service requests for this month</p>
                        </div>
                        <button type="button" class="btn-download-report" onclick="quickReport('performance', 'this_month')">
                            <i class="fas fa-bolt"></i> Quick Generate
                        </button>
                    </div>
                    <div class="productionCount--card report-card template-card">
                        <div class="Count">
                            <span class="title"> Cancellations</span>
                            <p class="report-desc">Rejections & Cancellations in this month</p>
                        </div>
                        <button type="button" class="btn-download-report" onclick="quickReport('cancellation', 'this_month')">
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
                    <?php endif; ?>

                    <!-- BOOKINGS REPORT -->
                    <?php if ($reportType === 'bookings' && isset($bookings_report)): ?>
                        <div class="card--wrapper">
                            <div class="productionCount--card filter-card">
                                <table class="report-table" style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #8b6914; color: white;">
                                            <th style="padding: 12px; text-align: left;">Request ID</th>
                                            <th style="padding: 12px; text-align: left;">Drama / Project</th>
                                            <th style="padding: 12px; text-align: left;">Service Type</th>
                                            <th style="padding: 12px; text-align: left;">Client</th>
                                            <th style="padding: 12px; text-align: center;">Budget</th>
                                            <th style="padding: 12px; text-align: left;">Duration</th>
                                            <th style="padding: 12px; text-align: center;">Status</th>
                                            <th style="padding: 12px; text-align: center;">Payment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($bookings_report)): ?>
                                            <?php foreach ($bookings_report as $row): ?>
                                                <tr style="border-bottom: 1px solid #eee;">
                                                    <td style="padding: 12px;"><strong><?= 'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->drama_name) ?></td>
                                                    <td style="padding: 12px;"><span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= ucfirst(str_replace('_', ' ', $row->service_type)) ?></span></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->requester_name) ?></td>
                                                    <td style="padding: 12px; text-align: center;"><strong>Rs <?= number_format($row->budget ?? 0, 2) ?></strong></td>
                                                    <td style="padding: 12px; font-size: 12px;">
                                                        <?= date('d M', strtotime($row->start_date)) ?> - <?= date('d M Y', strtotime($row->end_date)) ?>
                                                    </td>
                                                    <td style="padding: 12px; text-align: center;">
                                                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; <?php
                                                            switch ($row->status) {
                                                                case 'pending': echo 'background: #fff3cd; color: #856404;'; break;
                                                                case 'provider_responded': echo 'background: #cfe2ff; color: #084298;'; break;
                                                                case 'confirmed': echo 'background: #cfe2ff; color: #084298;'; break;
                                                                case 'accepted': echo 'background: #d1ecf1; color: #0c5460;'; break;
                                                                case 'completed': echo 'background: #d4edda; color: #155724;'; break;
                                                                case 'rejected': echo 'background: #f8d7da; color: #842029;'; break;
                                                                case 'cancelled': echo 'background: #f5f5f5; color: #666;'; break;
                                                                default: echo 'background: #e2e3e5; color: #383d41;'; break;
                                                            }
                                                        ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $row->status)) ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 12px; text-align: center;">
                                                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; <?php
                                                            switch ($row->payment_status) {
                                                                case 'fully_paid': echo 'background: #d4edda; color: #155724;'; break;
                                                                case 'partially_paid': echo 'background: #fff3cd; color: #856404;'; break;
                                                                case 'pending': echo 'background: #e2e3e5; color: #383d41;'; break;
                                                                case 'unpaid': echo 'background: #f8d7da; color: #842029;'; break;
                                                                default: echo 'background: #e2e3e5; color: #383d41;'; break;
                                                            }
                                                        ?>">
                                                            <?= ucfirst(str_replace('_', ' ', $row->payment_status)) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No bookings found for this period</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- SERVICE PERFORMANCE REPORT -->
                    <?php if ($reportType === 'performance' && isset($service_performance)): ?>
                        <div class="card--wrapper">
                            <div class="productionCount--card filter-card">
                                <table class="report-table" style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #8b6914; color: white;">
                                            <th style="padding: 12px; text-align: left;">Request ID</th>
                                            <th style="padding: 12px; text-align: left;">Drama / Project</th>
                                            <th style="padding: 12px; text-align: left;">Service Type</th>
                                            <th style="padding: 12px; text-align: left;">Client</th>
                                            <th style="padding: 12px; text-align: right;">Amount</th>
                                            <th style="padding: 12px; text-align: right;">Paid</th>
                                            <th style="padding: 12px; text-align: left;">Duration</th>
                                            <th style="padding: 12px; text-align: center;">Completed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($service_performance)): ?>
                                            <?php foreach ($service_performance as $row): ?>
                                                <tr style="border-bottom: 1px solid #eee;">
                                                    <td style="padding: 12px;"><strong><?= 'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->drama_name) ?></td>
                                                    <td style="padding: 12px;"><span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= ucfirst(str_replace('_', ' ', $row->service_type)) ?></span></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->requester_name) ?></td>
                                                    <td style="padding: 12px; text-align: right;"><strong>Rs <?= number_format($row->amount ?? $row->amount_paid ?? 0, 2) ?></strong></td>
                                                    <td style="padding: 12px; text-align: right;">Rs <?= number_format($row->amount_paid ?? 0, 2) ?></td>
                                                    <td style="padding: 12px; font-size: 12px;">
                                                        <?= date('d M', strtotime($row->start_date)) ?> - <?= date('d M Y', strtotime($row->end_date)) ?>
                                                    </td>
                                                    <td style="padding: 12px; text-align: center;">
                                                        <?php if ($row->is_completed): ?>
                                                            <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d4edda; color: #155724;">✓ Yes</span>
                                                        <?php else: ?>
                                                            <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fff3cd; color: #856404;">Pending</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No service data available for this period</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- CANCELLATION / REJECTION REPORT -->
                    <?php if ($reportType === 'cancellation' && isset($cancellation_report)): ?>
                        <div class="card--wrapper">
                            <div class="productionCount--card filter-card">
                                <table class="report-table" style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr style="background: #8b6914; color: white;">
                                            <th style="padding: 12px; text-align: left;">Request ID</th>
                                            <th style="padding: 12px; text-align: left;">Drama / Project</th>
                                            <th style="padding: 12px; text-align: left;">Service Type</th>
                                            <th style="padding: 12px; text-align: left;">Client</th>
                                            <th style="padding: 12px; text-align: right;">Budget</th>
                                            <th style="padding: 12px; text-align: left;">Date</th>
                                            <th style="padding: 12px; text-align: center;">Status</th>
                                            <th style="padding: 12px; text-align: left;">Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($cancellation_report)): ?>
                                            <?php foreach ($cancellation_report as $row): ?>
                                                <tr style="border-bottom: 1px solid #eee;">
                                                    <td style="padding: 12px;"><strong><?= 'REQ-' . str_pad($row->id, 5, '0', STR_PAD_LEFT) ?></strong></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->drama_name) ?></td>
                                                    <td style="padding: 12px;"><span style="background: #f0f0f0; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?= ucfirst(str_replace('_', ' ', $row->service_type)) ?></span></td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->requester_name) ?></td>
                                                    <td style="padding: 12px; text-align: right;"><strong>Rs <?= number_format($row->budget ?? 0, 2) ?></strong></td>
                                                    <td style="padding: 12px; font-size: 12px;"><?= date('d M Y', strtotime($row->created_at)) ?></td>
                                                    <td style="padding: 12px; text-align: center;">
                                                        <span style="padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f8d7da; color: #842029;">
                                                            <?= ucfirst($row->status) ?>
                                                        </span>
                                                    </td>
                                                    <td style="padding: 12px;"><?= htmlspecialchars($row->rejection_reason ?? 'N/A') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="8" style="text-align: center; padding: 20px; color: #999;">No cancellations/rejections for this period</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Export Actions -->
                    <div class="report-export-actions" style="margin-top: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reportType" value="<?= $reportType ?>">
                            <input type="hidden" name="dateRange" value="<?= $dateRange ?>">
                            <input type="hidden" name="startDate" value="<?= $start_date ?>">
                            <input type="hidden" name="endDate" value="<?= $end_date ?>">
                            <input type="hidden" name="exportFormat" value="pdf">
                            <button type="submit" class="btn-download-report" title="Download as PDF">
                                <i class="fas fa-file-pdf"></i> Download PDF
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reportType" value="<?= $reportType ?>">
                            <input type="hidden" name="dateRange" value="<?= $dateRange ?>">
                            <input type="hidden" name="startDate" value="<?= $start_date ?>">
                            <input type="hidden" name="endDate" value="<?= $end_date ?>">
                            <input type="hidden" name="exportFormat" value="excel">
                            <button type="submit" class="btn-download-report" title="Download as Excel">
                                <i class="fas fa-file-excel"></i> Download Excel
                            </button>
                        </form>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="reportType" value="<?= $reportType ?>">
                            <input type="hidden" name="dateRange" value="<?= $dateRange ?>">
                            <input type="hidden" name="startDate" value="<?= $start_date ?>">
                            <input type="hidden" name="endDate" value="<?= $end_date ?>">
                            <input type="hidden" name="exportFormat" value="csv">
                            <button type="submit" class="btn-download-report" title="Download as CSV">
                                <i class="fas fa-file-csv"></i> Download CSV
                            </button>
                        </form>
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

                    this.submit();
                });
            </script>
        </div>
    </body>
</html>
