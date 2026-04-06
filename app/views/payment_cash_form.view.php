<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$request = $request ?? null;
$amount = $amount ?? 0;
$type = $type ?? 'advance';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Payment - <?= esc(APP_NAME) ?></title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f5f7fa; padding:24px; }
        .card { max-width:700px; margin:0 auto; background:#fff; border-radius:20px; padding:28px; box-shadow:0 20px 50px rgba(15,23,42,.12); }
        .row { display:flex; justify-content:space-between; gap:16px; padding:10px 0; border-bottom:1px solid #e5e7eb; }
        .row:last-child { border-bottom:none; }
        label { display:block; margin:18px 0 8px; font-weight:700; }
        input, textarea { width:100%; padding:12px 14px; border:1px solid #d1d5db; border-radius:12px; font: inherit; }
        textarea { resize:vertical; min-height:100px; }
        .warn { color:#92400e; background:#fffbeb; border:1px solid #fbbf24; padding:12px; border-radius:12px; margin-top:12px; }
        .actions { display:flex; gap:12px; justify-content:flex-end; margin-top:20px; flex-wrap:wrap; }
        .btn { padding:13px 18px; border-radius:12px; text-decoration:none; font-weight:700; border:none; cursor:pointer; }
        .primary { background: linear-gradient(135deg, #d4af37, #a67c1b); color:#1a1410; }
        .secondary { background:#f3f4f6; color:#374151; }
    </style>
</head>
<body>
<div class="card">
    <h1 style="margin-top:0;">Record Cash Payment</h1>
    <div class="row"><span>Drama</span><strong><?= esc($request->drama_name ?? 'N/A') ?></strong></div>
    <div class="row"><span>Service</span><strong><?= esc($request->service_type ?? 'N/A') ?></strong></div>
    <div class="row"><span>Payment Type</span><strong><?= esc(ucfirst($type)) ?></strong></div>
    <div class="row"><span>Amount</span><strong>Rs <?= number_format((float)$amount, 2) ?></strong></div>

    <form method="POST" action="<?= ROOT ?>/Payment/submitCashPayment">
        <input type="hidden" name="request_id" value="<?= (int)($request->id ?? 0) ?>">
        <input type="hidden" name="amount" value="<?= esc($amount) ?>">
        <input type="hidden" name="type" value="<?= esc($type) ?>">

        <label for="received_date">Cash Given Date</label>
        <input type="date" id="received_date" name="received_date" value="<?= date('Y-m-d') ?>" required>

        <label for="note">Note (optional)</label>
        <textarea id="note" name="note" placeholder="Example: Handed over after rehearsal"></textarea>

        <div class="warn"><strong>Note:</strong> This payment will remain pending until the provider confirms receipt.</div>

        <div class="actions">
            <a class="btn secondary" href="<?= ROOT ?>/Production_manager/manage_services">Cancel</a>
            <button type="submit" class="btn primary">Save Cash Payment</button>
        </div>
    </form>
</div>
</body>
</html>
