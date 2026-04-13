<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Payment - <?= APP_NAME ?></title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/payment/cash.css">
</head>
<body class="payment-cash-form">
    <div class="payment-container">
        <h1><i class="bx bx-money-bill-wave"></i> Record Cash Payment</h1>

        <div class="payment-meta">
            <div class="payment-meta-row"><span>Drama</span><strong><?= htmlspecialchars($request->drama_name ?? 'N/A') ?></strong></div>
            <div class="payment-meta-row"><span>Service</span><strong><?= htmlspecialchars($request->service_type ?? 'N/A') ?></strong></div>
            <div class="payment-meta-row"><span>Payment Type</span><strong><?= htmlspecialchars(ucfirst($type)) ?></strong></div>
            <div class="payment-meta-row"><span>Amount</span><strong>Rs <?= number_format((float)$amount, 2) ?></strong></div>
        </div>

        <form method="POST" action="<?= ROOT ?>/Payment/submitCashPayment">
            <input type="hidden" name="request_id" value="<?= (int)$request->id ?>">
            <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
            <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

            <div class="payment-form-group">
                <label for="received_date">Cash Given Date</label>
                <input type="date" id="received_date" name="received_date" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="payment-form-group">
                <label for="note">Note (optional)</label>
                <textarea id="note" name="note" rows="3" placeholder="Example: Handed over in person after rehearsal"></textarea>
                <div class="payment-help warning">⚠️ This payment will remain pending until provider confirms receipt. Provider cannot accept the service request until confirming.</div>
            </div>

            <div class="payment-actions">
                <button type="submit" class="payment-btn payment-btn-primary"><i class="bx bx-check-circle"></i> Save Cash Payment</button>
                <a href="<?= ROOT ?>/Production_manager/manage_services?drama_id=<?= (int)($request->drama_id ?? 0) ?>" class="payment-btn payment-btn-secondary"><i class="bx bx-arrow-left"></i> Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
