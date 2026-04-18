<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Payments' ?> - Rangamadala</title>
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
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_payment.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body>
    <?php $activePage = 'payments'; include 'includes/service_provider/sidebar.php'; ?>

    <div class="main--content">
        <?php include 'includes/service_provider/header.php'; ?>

        <div class="container">
            <?php
                $summary = isset($summary) && is_array($summary) ? $summary : [
                    'total_quoted' => 0,
                    'total_received' => 0,
                    'total_outstanding' => 0,
                    'all' => 0,
                    'unpaid' => 0,
                    'partially_paid' => 0,
                    'fully_paid' => 0,
                    'verification_pending' => 0,
                ];

                $servicePayments = isset($servicePayments) && is_array($servicePayments) ? $servicePayments : [];
                $initialTab = $summary['all'] > 0 ? 'all' : 'unpaid';
            ?>

            <div class="summary-grid">
                <div class="summary-card">
                    <span class="summary-card__label">Total Quoted</span>
                    <span class="summary-card__value">Rs <?= number_format((float)$summary['total_quoted'], 2) ?></span>
                </div>
                <div class="summary-card summary-card--success">
                    <span class="summary-card__label">Total Received</span>
                    <span class="summary-card__value">Rs <?= number_format((float)$summary['total_received'], 2) ?></span>
                </div>
                <div class="summary-card summary-card--warning">
                    <span class="summary-card__label">Outstanding</span>
                    <span class="summary-card__value">Rs <?= number_format((float)$summary['total_outstanding'], 2) ?></span>
                </div>
                <div class="summary-card summary-card--info">
                    <span class="summary-card__label">Verification Pending</span>
                    <span class="summary-card__value"><?= (int)$summary['verification_pending'] ?></span>
                </div>
            </div>

            <div class="tabs payment-tabs">
                <button class="tab" id="allTab" onclick="switchTab('all')"><?= (int)$summary['all'] ?> All</button>
                <button class="tab" id="unpaidTab" onclick="switchTab('unpaid')"><?= (int)$summary['unpaid'] ?> Unpaid</button>
                <button class="tab" id="partially_paidTab" onclick="switchTab('partially_paid')"><?= (int)$summary['partially_paid'] ?> Partially Paid</button>
                <button class="tab" id="fully_paidTab" onclick="switchTab('fully_paid')"><?= (int)$summary['fully_paid'] ?> Fully Paid</button>
                <button class="tab" id="verification_pendingTab" onclick="switchTab('verification_pending')"><?= (int)$summary['verification_pending'] ?> Verification Pending</button>
            </div>

            <div class="payments-list" id="paymentsList">
                <?php if (empty($servicePayments)): ?>
                    <div class="empty-state">
                        <h3>No service payments found</h3>
                        <p>Once requests are quoted and paid, they will appear here with quote, paid, and remaining amounts.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($servicePayments as $item): ?>
                        <?php
                            $categories = ['all', $item['status_key']];
                            if (!empty($item['verification_pending'])) {
                                $categories[] = 'verification_pending';
                            }

                            $statusLabelMap = [
                                'unpaid' => 'Unpaid',
                                'partially_paid' => 'Partially Paid',
                                'fully_paid' => 'Fully Paid',
                            ];
                            $statusLabel = $statusLabelMap[$item['status_key']] ?? ucfirst(str_replace('_', ' ', $item['status_key']));

                            $dueText = '';
                            if (!empty($item['needs_advance']) && !empty($item['advance_due_date']) && $item['status_key'] !== 'fully_paid') {
                                $dueText = 'Advance due: ' . htmlspecialchars($item['advance_due_date']);
                            } elseif (!empty($item['final_payment_due_date']) && $item['status_key'] !== 'fully_paid') {
                                $dueText = 'Final due: ' . htmlspecialchars($item['final_payment_due_date']);
                            }

                            $paymentSteps = isset($item['payment_steps']) && is_array($item['payment_steps']) ? $item['payment_steps'] : [];
                            $paymentCount = isset($item['payment_count']) ? (int)$item['payment_count'] : count($paymentSteps);
                            $requestNo = 'REQ-' . str_pad((int)($item['request_id'] ?? 0), 5, '0', STR_PAD_LEFT);

                            $gatewayLabelMap = [
                                'payhere' => 'Card (PayHere)',
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                            ];
                        ?>
                        <div class="payment-item" data-category="<?= htmlspecialchars(implode(' ', $categories)) ?>" style="display: none;">
                            <div class="payment-item__main">
                                <h3><?= htmlspecialchars($item['drama_name']) ?> <span class="service-type">- <?= htmlspecialchars($item['service_type']) ?></span></h3>
                                <div class="payment-item__meta">
                                    <span>Requested by <?= htmlspecialchars($item['requester_name']) ?></span>
                                    <span>Request <?= htmlspecialchars($requestNo) ?></span>
                                    <?php if ($dueText !== ''): ?>
                                        <span><?= $dueText ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="payment-columns">
                                <div class="metric-col">
                                    <label>Quote</label>
                                    <strong>Rs <?= number_format((float)$item['quote_amount'], 2) ?></strong>
                                </div>
                                <div class="metric-col">
                                    <label>Paid</label>
                                    <strong class="text-success">Rs <?= number_format((float)$item['total_paid'], 2) ?></strong>
                                </div>
                                <div class="metric-col">
                                    <label>Remaining</label>
                                    <strong class="text-warning">Rs <?= number_format((float)$item['remaining_amount'], 2) ?></strong>
                                </div>
                                <div class="metric-col">
                                    <label>Next Due</label>
                                    <strong>Rs <?= number_format((float)$item['next_due_amount'], 2) ?></strong>
                                </div>
                            </div>

                            <div class="payment-item__footer">
                                <span class="status-badge status-<?= htmlspecialchars($item['status_key']) ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                <?php if (!empty($item['verification_pending'])): ?>
                                    <span class="status-badge status-verification">Verification Pending</span>
                                <?php endif; ?>
                                <span class="detail-pill"><?= $paymentCount ?> Payment Step<?= $paymentCount === 1 ? '' : 's' ?></span>
                                <button
                                    type="button"
                                    class="view-more-btn"
                                    aria-expanded="false"
                                    aria-controls="history-<?= (int)$item['request_id'] ?>"
                                    onclick="togglePaymentHistory(<?= (int)$item['request_id'] ?>)">
                                    View More
                                </button>
                            </div>

                            <div class="payment-history" id="history-<?= (int)$item['request_id'] ?>" hidden>
                                <h4>Payment Steps & Receipts</h4>
                                <?php if (empty($paymentSteps)): ?>
                                    <p class="payment-history__empty">No payment records yet for this service request.</p>
                                <?php else: ?>
                                    <div class="payment-history__table-wrap">
                                        <table class="payment-history__table">
                                            <thead>
                                                <tr>
                                                    <th>Step</th>
                                                    <th>Method</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                    <th>Reference</th>
                                                    <th>Receipt</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($paymentSteps as $step): ?>
                                                    <?php
                                                        $gatewayKey = strtolower((string)($step['payment_gateway'] ?? ''));
                                                        $methodLabel = $gatewayLabelMap[$gatewayKey] ?? ucfirst(str_replace('_', ' ', $gatewayKey));

                                                        $stepType = ucfirst(strtolower((string)($step['payment_type'] ?? '')));
                                                        $stepStatusKey = strtolower((string)($step['payment_status'] ?? 'pending'));
                                                        $stepStatusLabel = ucfirst(str_replace('_', ' ', $stepStatusKey));

                                                        $stepDateRaw = !empty($step['paid_at']) ? $step['paid_at'] : ($step['created_at'] ?? null);
                                                        $stepDate = $stepDateRaw ? date('Y-m-d H:i', strtotime($stepDateRaw)) : '-';
                                                    ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($stepType) ?></td>
                                                        <td><?= htmlspecialchars($methodLabel) ?></td>
                                                        <td>Rs <?= number_format((float)($step['amount'] ?? 0), 2) ?></td>
                                                        <td>
                                                            <span class="history-status history-status-<?= htmlspecialchars($stepStatusKey) ?>">
                                                                <?= htmlspecialchars($stepStatusLabel) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= htmlspecialchars($stepDate) ?></td>
                                                        <td><?= !empty($step['reference_number']) ? htmlspecialchars($step['reference_number']) : '-' ?></td>
                                                        <td>
                                                            <a class="receipt-link" href="<?= ROOT ?>/Payment/receipt/<?= (int)($step['id'] ?? 0) ?>" target="_blank" rel="noopener noreferrer">
                                                                View Receipt
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="filter-message" id="filterMessage">No items found for this tab.</div>
        </div>

        <script>
            let currentTab = '<?= htmlspecialchars($initialTab) ?>';

            function switchTab(category) {
                document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
                const tabEl = document.getElementById(category + 'Tab');
                if (tabEl) tabEl.classList.add('active');

                let visibleCount = 0;
                document.querySelectorAll('.payment-item').forEach(item => {
                    item.querySelectorAll('.payment-history').forEach(history => {
                        history.hidden = true;
                    });
                    item.querySelectorAll('.view-more-btn').forEach(btn => {
                        btn.textContent = 'View More';
                        btn.setAttribute('aria-expanded', 'false');
                    });

                    const categories = (item.getAttribute('data-category') || '').split(' ');
                    if (categories.includes(category)) {
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        item.style.display = 'none';
                    }
                });

                const filterMessage = document.getElementById('filterMessage');
                if (filterMessage) {
                    filterMessage.style.display = visibleCount === 0 ? 'block' : 'none';
                }

                currentTab = category;
            }

            function togglePaymentHistory(requestId) {
                const history = document.getElementById('history-' + requestId);
                const button = document.querySelector('.view-more-btn[aria-controls="history-' + requestId + '"]');
                if (!history || !button) return;

                const isHidden = history.hidden;
                history.hidden = !isHidden;
                button.textContent = isHidden ? 'View Less' : 'View More';
                button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
            }

            document.addEventListener('DOMContentLoaded', function() {
                switchTab(currentTab);
            });
        </script>
    </div>
</body>
</html>