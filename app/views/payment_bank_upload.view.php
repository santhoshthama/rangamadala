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
    <title>Bank Transfer Payment - <?= esc(APP_NAME) ?></title>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f5f7fa; padding:24px; }
        .card { max-width:760px; margin:0 auto; background:#fff; border-radius:20px; padding:28px; box-shadow:0 20px 50px rgba(15,23,42,.12); }
        .row { display:flex; justify-content:space-between; gap:16px; padding:10px 0; border-bottom:1px solid #e5e7eb; }
        .row:last-child { border-bottom:none; }
        label { display:block; margin:18px 0 8px; font-weight:700; }
        input[type="file"] { width:100%; padding:12px; border:1px solid #d1d5db; border-radius:12px; }
        .help { font-size:13px; color:#6b7280; margin-top:8px; }
        .warn { color:#92400e; background:#fffbeb; border:1px solid #fbbf24; padding:12px; border-radius:12px; margin-top:12px; }
        .actions { display:flex; gap:12px; justify-content:flex-end; margin-top:20px; flex-wrap:wrap; }
        .btn { padding:13px 18px; border-radius:12px; text-decoration:none; font-weight:700; border:none; cursor:pointer; }
        .primary { background: linear-gradient(135deg, #d4af37, #a67c1b); color:#1a1410; }
        .secondary { background:#f3f4f6; color:#374151; }
    </style>
</head>
<body>
<div class="card">
    <h1 style="margin-top:0;">Bank Transfer Payment</h1>
    <p style="color:#6b7280;">Upload your bank slip evidence for provider verification.</p>

    <div class="row"><span>Drama</span><strong><?= esc($request->drama_name ?? 'N/A') ?></strong></div>
    <div class="row"><span>Service Type</span><strong><?= esc($request->service_type ?? 'N/A') ?></strong></div>
    <div class="row"><span>Payment Type</span><strong><?= esc(ucfirst($type)) ?></strong></div>
    <div class="row"><span>Amount</span><strong>Rs <?= number_format((float)$amount, 2) ?></strong></div>

    <div class="warn"><strong>Instructions:</strong> Transfer the payment first, then upload the slip. Accepted files: JPG, PNG, PDF (max 5MB).</div>

    <form method="POST" action="<?= ROOT ?>/Payment/submitBankSlip" enctype="multipart/form-data">
        <input type="hidden" name="request_id" value="<?= (int)($request->id ?? 0) ?>">
        <input type="hidden" name="amount" value="<?= esc($amount) ?>">
        <input type="hidden" name="type" value="<?= esc($type) ?>">

        <label for="bank_slip">Bank Slip File</label>
        <input type="file" id="bank_slip" name="bank_slip" accept=".jpg,.jpeg,.png,.pdf" required>
        <div class="help">Accepted formats: JPG, PNG, PDF | Max size: 5MB</div>

        <div class="actions">
            <a class="btn secondary" href="<?= ROOT ?>/Production_manager/manage_services">Cancel</a>
            <button type="submit" class="btn primary">Upload Evidence</button>
        </div>
    </form>
</div>
</body>
</html>
