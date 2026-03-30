<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Bank Slip - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/payment/bank_transfer.css">
</head>
<body class="payment-bank-upload">
    <div class="payment-card">
        <div class="payment-header">
            <h1><i class="fas fa-university"></i> Bank Transfer Payment</h1>
            <p>Upload your bank slip evidence for provider verification.</p>
        </div>
        <div class="payment-body">
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="payment-alert error">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <div class="payment-info-grid">
                <div class="payment-info-item">
                    <span>Drama</span>
                    <strong><?= htmlspecialchars($request->drama_name ?? 'N/A') ?></strong>
                </div>
                <div class="payment-info-item">
                    <span>Service Type</span>
                    <strong><?= htmlspecialchars($request->service_type ?? 'N/A') ?></strong>
                </div>
                <div class="payment-info-item">
                    <span>Payment Type</span>
                    <strong><?= htmlspecialchars(ucfirst($type)) ?></strong>
                </div>
                <div class="payment-info-item">
                    <span>Amount</span>
                    <strong>Rs <?= number_format((float)$amount, 2) ?></strong>
                </div>
            </div>

            <div class="payment-alert">
                <strong>Instructions:</strong> Make the bank transfer first, then upload the payment slip. Accepted files: JPG, PNG, PDF (max 5MB).
            </div>

            <form method="POST" action="<?= ROOT ?>/Payment/submitBankSlip" enctype="multipart/form-data">
                <input type="hidden" name="request_id" value="<?= (int)$request->id ?>">
                <input type="hidden" name="amount" value="<?= htmlspecialchars($amount) ?>">
                <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">

                <div class="payment-form-group">
                    <label class="payment-label" for="bank_slip">Bank Slip File</label>
                    <input class="payment-input" type="file" id="bank_slip" name="bank_slip" accept=".jpg,.jpeg,.png,.pdf" required>
                    <div class="payment-help">Accepted formats: JPG, PNG, PDF | Max file size: 5MB</div>
                    <div class="payment-help warning">⚠️ This payment will remain pending until provider confirms receipt. Provider cannot accept the service request until confirming.</div>
                </div>

                <div class="payment-actions">
                    <a class="payment-btn payment-btn-secondary" href="<?= ROOT ?>/Production_manager/manage_services?drama_id=<?= (int)($request->drama_id ?? 0) ?>">
                        Cancel
                    </a>
                    <button type="submit" class="payment-btn payment-btn-primary">
                        <i class="fas fa-upload"></i> Upload Evidence
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
