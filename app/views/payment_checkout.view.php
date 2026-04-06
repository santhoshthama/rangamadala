<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$request = $request ?? null;
$amount = $amount ?? 0;
$type = $type ?? 'advance';
$providerResponse = $provider_response ?? [];
$user = $user ?? null;
$payhereConfig = $payhere_config ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - <?= esc(APP_NAME) ?></title>
    <script src="https://www.payhere.lk/lib/payhere.js"></script>
    <style>
        body { margin:0; font-family: Arial, sans-serif; background: linear-gradient(135deg, #f5f7fa, #eef2ff); color:#1f2937; padding:24px; }
        .container { max-width: 1100px; margin:0 auto; }
        .panel { background:#fff; border-radius:20px; box-shadow:0 20px 50px rgba(15,23,42,.12); padding:28px; margin-bottom:20px; }
        .header { display:flex; justify-content:space-between; align-items:center; gap:16px; }
        .badge { padding:8px 14px; border-radius:999px; background:#e8f5e9; color:#166534; font-weight:700; font-size:13px; }
        .grid { display:grid; grid-template-columns: 1.2fr .8fr; gap:20px; margin-top:20px; }
        .box { background:#f9fafb; border:1px solid #e5e7eb; border-radius:16px; padding:18px; }
        .row { display:flex; justify-content:space-between; gap:16px; padding:10px 0; border-bottom:1px solid #e5e7eb; }
        .row:last-child { border-bottom:none; }
        .label { color:#6b7280; font-size:14px; }
        .value { font-weight:700; text-align:right; }
        .method { border:2px solid #e5e7eb; border-radius:16px; padding:16px; margin-bottom:12px; cursor:pointer; }
        .method.selected { border-color:#ba8e23; background:#fffdf6; }
        .method h3 { margin:0 0 4px; font-size:16px; }
        .method p { margin:0; color:#6b7280; font-size:13px; }
        .buttons { display:flex; flex-direction:column; gap:10px; margin-top:18px; }
        .btn { display:inline-flex; justify-content:center; align-items:center; gap:8px; padding:14px 18px; border:none; border-radius:12px; font-weight:700; cursor:pointer; text-decoration:none; }
        .btn-primary { background: linear-gradient(135deg, #d4af37, #a67c1b); color:#1a1410; }
        .btn-secondary { background:#f3f4f6; color:#374151; }
        .info { background:#fffbeb; border:1px solid #fbbf24; color:#92400e; border-radius:14px; padding:14px; margin-top:14px; }
        .terms { display:flex; gap:10px; align-items:flex-start; margin-top:16px; font-size:13px; color:#4b5563; }
        .terms input { margin-top:3px; }
        @media (max-width: 900px) { .grid { grid-template-columns:1fr; } .header { flex-direction:column; align-items:flex-start; } }
    </style>
</head>
<body>
<div class="container">
    <div class="panel">
        <div class="header">
            <div>
                <h1 style="margin:0;">Secure Checkout</h1>
                <p style="margin:6px 0 0; color:#6b7280;">Complete the payment for this service request.</p>
            </div>
            <div class="badge">SSL Encrypted</div>
        </div>
        <div class="grid">
            <div>
                <?php if ($type === 'advance'): ?>
                    <div class="info"><strong>Advance payment required.</strong> The remaining balance will be payable after service completion.</div>
                <?php endif; ?>
                <div class="box" style="margin-top:16px;">
                    <h2 style="margin:0 0 14px; font-size:18px;">Service Details</h2>
                    <div class="row"><span class="label">Drama</span><span class="value"><?= esc($request->drama_name ?? 'N/A') ?></span></div>
                    <div class="row"><span class="label">Service</span><span class="value"><?= esc($request->service_type ?? 'N/A') ?></span></div>
                    <div class="row"><span class="label">Provider</span><span class="value"><?= esc($request->provider_name ?? 'Service Provider') ?></span></div>
                </div>
                <div class="box" style="margin-top:16px;">
                    <h2 style="margin:0 0 14px; font-size:18px;">Payment Method</h2>
                    <div class="method selected" data-method="payhere">
                        <h3>PayHere</h3>
                        <p>Pay securely with card, wallet, or bank options.</p>
                    </div>
                    <div class="method" data-method="bank">
                        <h3>Bank Transfer</h3>
                        <p>Upload a bank slip for provider verification.</p>
                    </div>
                    <div class="method" data-method="cash">
                        <h3>Cash Payment</h3>
                        <p>Record a cash payment and wait for confirmation.</p>
                    </div>
                    <div class="terms">
                        <input type="checkbox" id="agreeTerms">
                        <label for="agreeTerms">I agree to the Terms & Conditions and Payment Policy.</label>
                    </div>
                    <div class="buttons">
                        <button type="button" class="btn btn-primary" id="proceedBtn">Proceed to Payment</button>
                        <a class="btn btn-secondary" href="<?= ROOT ?>/Production_manager/manage_services">Back to Services</a>
                    </div>
                </div>
            </div>
            <div>
                <div class="box">
                    <h2 style="margin:0 0 14px; font-size:18px;">Order Summary</h2>
                    <div class="row"><span class="label">Payment Type</span><span class="value"><?= esc(ucfirst($type)) ?></span></div>
                    <div class="row"><span class="label">Amount</span><span class="value">Rs <?= number_format((float)$amount, 2) ?></span></div>
                    <?php if (!empty($providerResponse['quote_amount'])): ?>
                        <div class="row"><span class="label">Quotation</span><span class="value">Rs <?= number_format((float)$providerResponse['quote_amount'], 2) ?></span></div>
                    <?php endif; ?>
                    <?php if (!empty($providerResponse['final_payment_due_date'])): ?>
                        <div style="margin-top:16px; padding:12px; background:#f9fafb; border-radius:12px;">
                            <div style="font-size:12px; color:#6b7280;">Final Payment Due</div>
                            <div style="font-weight:700; margin-top:4px;"><?= esc($providerResponse['final_payment_due_date']) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="box" style="margin-top:16px;">
                    <div style="font-size:12px; color:#6b7280; margin-bottom:6px;">Customer</div>
                    <div style="font-weight:700;"><?= esc($user->full_name ?? 'Customer') ?></div>
                    <div style="font-size:12px; color:#6b7280; margin-top:4px;"><?= esc($user->email ?? '') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    let selectedMethod = 'payhere';
    document.querySelectorAll('.method').forEach((item) => {
        item.addEventListener('click', () => {
            selectedMethod = item.dataset.method;
            document.querySelectorAll('.method').forEach((el) => el.classList.remove('selected'));
            item.classList.add('selected');
        });
    });

    document.getElementById('proceedBtn').addEventListener('click', () => {
        if (!document.getElementById('agreeTerms').checked) {
            alert('Please agree to the terms to continue.');
            return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const requestId = urlParams.get('request_id');
        const amount = urlParams.get('amount');
        const type = urlParams.get('type');
        const button = document.getElementById('proceedBtn');

        button.disabled = true;
        button.textContent = 'Processing...';

        if (selectedMethod === 'bank') {
            window.location.href = '<?= ROOT ?>/Payment/bankForm?request_id=' + encodeURIComponent(requestId) + '&amount=' + encodeURIComponent(amount) + '&type=' + encodeURIComponent(type);
            return;
        }

        if (selectedMethod === 'cash') {
            window.location.href = '<?= ROOT ?>/Payment/cashForm?request_id=' + encodeURIComponent(requestId) + '&amount=' + encodeURIComponent(amount) + '&type=' + encodeURIComponent(type);
            return;
        }

        fetch('<?= ROOT ?>/Payment/createPayHerePayment', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'request_id=' + encodeURIComponent(requestId) + '&amount=' + encodeURIComponent(amount) + '&type=' + encodeURIComponent(type)
        })
        .then((response) => response.json())
        .then((data) => {
            if (!data.success) {
                throw new Error(data.error || 'Failed to initialize payment');
            }

            if (typeof payhere === 'undefined') {
                throw new Error('PayHere library was not loaded');
            }

            const payment = {
                sandbox: <?= json_encode((bool)($payhereConfig['sandbox'] ?? true)) ?>,
                merchant_id: <?= json_encode((string)($payhereConfig['merchant_id'] ?? '')) ?>,
                return_url: <?= json_encode((string)($payhereConfig['return_url'] ?? (ROOT . '/Payment/paymentReturn'))) ?> + '?order_id=' + encodeURIComponent(data.order_id),
                cancel_url: <?= json_encode((string)($payhereConfig['cancel_url'] ?? (ROOT . '/Production_manager/manage_services'))) ?>,
                notify_url: <?= json_encode((string)($payhereConfig['notify_url'] ?? (ROOT . '/Payment/notify'))) ?>,
                order_id: data.order_id,
                items: <?= json_encode((string)($request->service_type ?? 'Service Request')) ?>,
                amount: <?= json_encode(number_format((float)$amount, 2, '.', '')) ?>,
                currency: 'LKR',
                hash: data.hash,
                first_name: <?= json_encode(explode(' ', trim((string)($user->full_name ?? 'Customer')))[0] ?? 'Customer') ?>,
                last_name: <?= json_encode(trim(implode(' ', array_slice(explode(' ', trim((string)($user->full_name ?? 'Customer'))), 1)))) ?>,
                email: <?= json_encode((string)($user->email ?? '')) ?>,
                phone: <?= json_encode((string)($user->phone ?? '')) ?>,
                address: <?= json_encode((string)($user->address ?? '')) ?>,
                city: <?= json_encode((string)($user->city ?? '')) ?>,
                country: 'Sri Lanka',
                custom_1: <?= json_encode((string)($request->id ?? '')) ?>,
                custom_2: <?= json_encode((string)$type) ?>
            };

            payhere.onCompleted = function () {
                window.location.href = payment.return_url;
            };
            payhere.onDismissed = function () {
                window.location.href = payment.cancel_url;
            };
            payhere.onError = function (error) {
                alert('Payment error: ' + error);
                button.disabled = false;
                button.textContent = 'Proceed to Payment';
            };

            payhere.startPayment(payment);
        })
        .catch((error) => {
            alert(error.message || 'Failed to initialize payment');
            button.disabled = false;
            button.textContent = 'Proceed to Payment';
        });
    });
</script>
</body>
</html>
