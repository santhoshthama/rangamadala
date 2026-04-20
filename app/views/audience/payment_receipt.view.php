<?php
    $receiptType = strtolower((string)($receipt_type ?? ''));
    $receipt = $receipt ?? null;
    $receiptNumber = (string)($receipt_number ?? '');

    $isClassReceipt = $receiptType === 'class';
    $title = $isClassReceipt
        ? ($receipt->class_title ?? 'Class Payment Receipt')
        : ($receipt->title ?? 'Showing Payment Receipt');

    $amount = $isClassReceipt
        ? (float)($receipt->amount ?? 0)
        : (float)($receipt->ticket_price ?? 0);

    if (!$isClassReceipt && !empty($receipt->showing_prices)) {
        $showingPriceText = trim((string)$receipt->showing_prices);
        if (preg_match('/\d+(?:\.\d+)?/', str_replace(',', '', $showingPriceText), $match)) {
            $amount = (float)$match[0];
        }
    }

    $paidAt = !empty($receipt->paid_at) ? date('M d, Y h:i A', strtotime($receipt->paid_at)) : 'N/A';
    $status = $isClassReceipt
        ? ucfirst((string)($receipt->status ?? 'completed'))
        : ucfirst((string)($receipt->booking_status ?? 'confirmed'));
    $reference = $isClassReceipt
        ? (string)($receipt->order_id ?? 'N/A')
        : (string)($receipt->payhere_order_id ?? 'N/A');
    $eventDate = $isClassReceipt
        ? (!empty($receipt->class_date) ? date('M d, Y', strtotime($receipt->class_date)) : 'TBA')
        : (!empty($receipt->event_date) ? date('M d, Y', strtotime($receipt->event_date)) : 'TBA');
    $venue = trim((string)($receipt->venue ?? '')) !== '' ? trim((string)($receipt->venue ?? '')) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($receiptNumber) ?> - <?= APP_NAME ?></title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/audience-payment-receipt.css">
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <div class="receipt-header-top">
                <div>
                    <div class="receipt-type"><?= htmlspecialchars($isClassReceipt ? 'Class Payment Receipt' : 'Drama Showing Receipt') ?></div>
                    <h1><?= htmlspecialchars($title) ?></h1>
                    <div class="receipt-number">Receipt No: <?= htmlspecialchars($receiptNumber) ?></div>
                </div>
                <div class="receipt-actions">
                    <a class="btn-link" href="<?= ROOT ?>/audiencedashboard#payments"><i class="bx bx-arrow-back"></i> Back</a>
                    <button class="btn-print" type="button" onclick="window.print()"><i class="bx bx-printer"></i> Print / Save PDF</button>
                </div>
            </div>
        </div>

        <div class="receipt-body">
            <div class="amount-box">
                <span class="label">Total Paid</span>
                <div class="value">Rs. <?= number_format($amount, 2) ?></div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Reference</span>
                    <div class="detail-value"><?= htmlspecialchars($reference) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Paid At</span>
                    <div class="detail-value"><?= htmlspecialchars($paidAt) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <div class="detail-value"><span class="status-pill"><?= htmlspecialchars($status) ?></span></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date</span>
                    <div class="detail-value"><?= htmlspecialchars($eventDate) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Venue</span>
                    <div class="detail-value"><?= htmlspecialchars($venue) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Type</span>
                    <div class="detail-value"><?= htmlspecialchars($isClassReceipt ? 'Class Enrollment' : 'Drama Showing') ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Audience Member</span>
                    <div class="detail-value"><?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Audience Member') ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Generated</span>
                    <div class="detail-value"><?= htmlspecialchars(date('M d, Y h:i A')) ?></div>
                </div>
            </div>
        </div>

        <div class="receipt-footer">
            <div class="footer-note">Use Print / Save PDF to download this receipt.</div>
            <a class="btn-link" href="<?= ROOT ?>/audiencedashboard#payments"><i class="bx bx-list-ul"></i> Payments</a>
        </div>
    </div>
</body>
</html>