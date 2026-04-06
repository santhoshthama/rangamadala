<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$payment = $payment ?? null;
$request = $request ?? null;
$paidBy = $paidBy ?? null;
$paidTo = $paidTo ?? null;
$transactionData = $transactionData ?? [];
$receiptNumber = $receipt_number ?? '';
$isProviderView = !empty($isProviderView);
$bankSlipPath = $transactionData['bank_slip_path'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Payment Receipt - <?= esc(APP_NAME) ?></title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f5f7fa; padding:24px; }
        .card { max-width:960px; margin:0 auto; background:#fff; border-radius:20px; box-shadow:0 20px 50px rgba(15,23,42,.12); overflow:hidden; }
        .header { padding:28px; text-align:center; border-bottom:2px solid #d4af37; }
        .body { padding:28px; }
        .section { margin-bottom:22px; }
        .section h2 { margin:0 0 12px; font-size:18px; }
        .row { display:flex; justify-content:space-between; gap:16px; padding:12px 0; border-bottom:1px solid #e5e7eb; }
        .row:last-child { border-bottom:none; }
        .label { color:#6b7280; }
        .value { font-weight:700; text-align:right; }
        .status { padding:10px 14px; border-radius:999px; display:inline-block; font-weight:700; }
        .success { background:#dcfce7; color:#166534; }
        .pending { background:#fef3c7; color:#92400e; }
        .failed { background:#fee2e2; color:#991b1b; }
        .actions { display:flex; gap:12px; justify-content:center; padding:22px 28px 28px; flex-wrap:wrap; background:#f9fafb; border-top:1px solid #e5e7eb; }
        .btn { padding:13px 18px; border-radius:12px; text-decoration:none; font-weight:700; border:none; cursor:pointer; }
        .primary { background:#d4af37; color:#1a1410; }
        .secondary { background:#fff; color:#374151; border:1px solid #d1d5db; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <h1 style="margin:0;">Service Payment Receipt</h1>
        <p style="margin:6px 0 0; color:#6b7280;">Receipt #<?= esc($receiptNumber) ?></p>
    </div>
    <div class="body">
        <?php
            $status = strtolower((string)($payment->payment_status ?? 'pending'));
            $statusClass = in_array($status, ['completed', 'success'], true) ? 'success' : (in_array($status, ['failed', 'canceled', 'cancelled'], true) ? 'failed' : 'pending');
            $statusLabel = $statusClass === 'success' ? 'Payment Successful' : ($statusClass === 'failed' ? 'Payment Cancelled' : 'Payment Pending');
        ?>
        <div class="section" style="text-align:center; margin-bottom:18px;">
            <span class="status <?= $statusClass ?>"><?= esc($statusLabel) ?></span>
        </div>

        <div class="section">
            <h2>Payment Information</h2>
            <div class="row"><span class="label">Amount</span><span class="value">Rs <?= number_format((float)($payment->amount ?? 0), 2) ?></span></div>
            <div class="row"><span class="label">Payment Type</span><span class="value"><?= esc(ucfirst((string)($payment->payment_type ?? ''))) ?></span></div>
            <div class="row"><span class="label">Method</span><span class="value"><?= esc(($payment->payment_gateway ?? 'payhere') === 'bank_transfer' ? 'Bank Transfer' : (($payment->payment_gateway ?? '') === 'cash' ? 'Cash' : 'PayHere')) ?></span></div>
            <div class="row"><span class="label">Date</span><span class="value"><?= esc(date('M d, Y h:i A', strtotime($payment->paid_at ?? $payment->created_at ?? 'now'))) ?></span></div>
            <?php if (!empty($bankSlipPath)): ?>
                <div class="row"><span class="label">Bank Slip</span><span class="value"><a href="<?= ROOT ?>/Payment/viewBankSlip/<?= (int)$payment->id ?>" target="_blank">View Slip</a></span></div>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Service Details</h2>
            <div class="row"><span class="label">Drama</span><span class="value"><?= esc($request->drama_name ?? 'N/A') ?></span></div>
            <div class="row"><span class="label">Service</span><span class="value"><?= esc($request->service_type ?? 'N/A') ?></span></div>
        </div>

        <div class="section">
            <h2>Transaction Details</h2>
            <div class="row"><span class="label">Paid By</span><span class="value"><?= esc($paidBy->full_name ?? 'N/A') ?></span></div>
            <div class="row"><span class="label">Paid To</span><span class="value"><?= esc($paidTo->full_name ?? 'N/A') ?></span></div>
            <div class="row"><span class="label">Reference</span><span class="value"><?= esc($payment->reference_number ?? $payment->gateway_order_id ?? 'N/A') ?></span></div>
        </div>
    </div>
    <div class="actions">
        <button type="button" class="btn primary" onclick="window.print()">Print Receipt</button>
        <a class="btn secondary" href="<?= $isProviderView ? ROOT . '/ServicePayment' : ROOT . '/Production_manager/manage_services' ?>">Back</a>
    </div>
</div>
</body>
</html>
