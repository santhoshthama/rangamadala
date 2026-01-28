<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header */
        .checkout-header {
            background: white;
            padding: 20px 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .checkout-header h1 {
            font-size: 24px;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: #e7f5e9;
            border-radius: 20px;
            font-size: 13px;
            color: #2d7a3e;
            font-weight: 500;
        }

        /* Progress Steps */
        .progress-steps {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 12.5%;
            right: 12.5%;
            height: 2px;
            background: #e5e7eb;
            z-index: 0;
        }

        .step {
            text-align: center;
            position: relative;
            flex: 1;
        }

        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .step.active .step-circle {
            background: #d4af37;
            color: white;
        }

        .step.completed .step-circle {
            background: #10b981;
            color: white;
        }

        .step-label {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }

        .step.active .step-label {
            color: #1a1a1a;
            font-weight: 600;
        }

        /* Main Content */
        .checkout-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 20px;
        }

        .payment-section, .order-summary {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Service Details */
        .service-details {
            background: #f9fafb;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
        }

        .detail-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 600;
            text-align: right;
        }

        /* Payment Method */
        .payment-method {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-method:hover {
            border-color: #d4af37;
            background: #fffef8;
        }

        .payment-method.selected {
            border-color: #d4af37;
            background: #fffef8;
        }

        .method-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .method-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .method-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .method-name {
            font-weight: 600;
            color: #1f2937;
        }

        .method-desc {
            font-size: 12px;
            color: #6b7280;
            margin-top: 2px;
        }

        .radio-btn {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-method.selected .radio-btn {
            border-color: #d4af37;
        }

        .radio-btn::after {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #d4af37;
            display: none;
        }

        .payment-method.selected .radio-btn::after {
            display: block;
        }

        /* Order Summary */
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 14px;
        }

        .summary-row.total {
            border-top: 2px solid #e5e7eb;
            margin-top: 12px;
            padding-top: 16px;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .amount {
            color: #d4af37;
        }

        /* Terms */
        .terms-checkbox {
            display: flex;
            align-items: start;
            gap: 10px;
            padding: 16px;
            background: #f9fafb;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 13px;
            color: #4b5563;
        }

        .terms-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            cursor: pointer;
            accent-color: #d4af37;
        }

        .terms-checkbox label {
            cursor: pointer;
        }

        .terms-checkbox a {
            color: #d4af37;
            text-decoration: none;
        }

        .terms-checkbox a:hover {
            text-decoration: underline;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            width: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d4af37, #aa8c2c);
            color: #1a1410;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #aa8c2c, #8b6f47);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            margin-bottom: 12px;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* Security Info */
        .security-info {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 6px;
            padding: 12px;
            margin-top: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #0369a1;
        }

        /* Info Box */
        .info-box {
            background: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 14px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #92400e;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .checkout-content {
                grid-template-columns: 1fr;
            }

            .checkout-header {
                flex-direction: column;
                text-align: center;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="checkout-header">
            <h1>
                <i class="fas fa-lock"></i>
                Secure Checkout
            </h1>
            <div class="secure-badge">
                <i class="fas fa-shield-alt"></i>
                SSL Encrypted
            </div>
        </div>

        <!-- Progress Steps -->
        <div class="progress-steps">
            <div class="steps">
                <div class="step completed">
                    <div class="step-circle">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-label">Service Confirmed</div>
                </div>
                <div class="step active">
                    <div class="step-circle">2</div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <div class="step-label">Complete</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="checkout-content">
            <!-- Payment Section -->
            <div class="payment-section">
                <h2 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Payment Method
                </h2>

                <!-- Info about advance payment -->
                <?php if ($type === 'advance'): ?>
                <div class="info-box">
                    <strong>ℹ️ Advance Payment Required</strong><br>
                    This service provider requires an advance payment to confirm your booking. The remaining amount will be due after service completion.
                </div>
                <?php endif; ?>

                <!-- Service Details -->
                <div class="service-details">
                    <h3 style="font-size: 15px; font-weight: 600; margin-bottom: 12px; color: #374151;">Service Details</h3>
                    <div class="detail-row">
                        <span class="detail-label">Drama</span>
                        <span class="detail-value"><?= htmlspecialchars($request->drama_name) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Service Type</span>
                        <span class="detail-value"><?= htmlspecialchars($request->service_type) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Provider</span>
                        <span class="detail-value"><?= htmlspecialchars($request->provider_name ?? '-') ?></span>
                    </div>
                    <?php if (!empty($request->service_date)): ?>
                    <div class="detail-row">
                        <span class="detail-label">Service Date</span>
                        <span class="detail-value"><?= htmlspecialchars($request->service_date) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Payment Methods -->
                <div class="payment-method selected" onclick="selectPaymentMethod('paypal')">
                    <div class="method-header">
                        <div class="method-info">
                            <div class="method-icon">
                                <i class="fab fa-paypal" style="color: #003087;"></i>
                            </div>
                            <div>
                                <div class="method-name">PayPal</div>
                                <div class="method-desc">Pay securely with your PayPal account</div>
                            </div>
                        </div>
                        <div class="radio-btn"></div>
                    </div>
                </div>

                <div class="payment-method" onclick="selectPaymentMethod('card')">
                    <div class="method-header">
                        <div class="method-info">
                            <div class="method-icon">
                                <i class="fas fa-credit-card" style="color: #6b7280;"></i>
                            </div>
                            <div>
                                <div class="method-name">Credit/Debit Card</div>
                                <div class="method-desc">Coming soon</div>
                            </div>
                        </div>
                        <div class="radio-btn"></div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="terms-checkbox">
                    <input type="checkbox" id="agreeTerms" required>
                    <label for="agreeTerms">
                        I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Payment Policy</a>
                    </label>
                </div>

                <!-- Security Info -->
                <div class="security-info">
                    <i class="fas fa-lock"></i>
                    <div>
                        Your payment information is encrypted and secure. We never store your card details.
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h2 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Order Summary
                </h2>

                <div class="summary-row">
                    <span>Payment Type</span>
                    <span><?= ucfirst($type) ?> Payment</span>
                </div>

                <?php if (!empty($provider_response['quote_amount'])): ?>
                <div class="summary-row">
                    <span>Total Quotation</span>
                    <span>Rs <?= number_format($provider_response['quote_amount'], 2) ?></span>
                </div>
                <?php endif; ?>

                <div class="summary-row total">
                    <span>Amount to Pay</span>
                    <span class="amount">Rs <?= number_format($amount, 2) ?></span>
                </div>

                <?php if ($type === 'advance' && !empty($provider_response['quote_amount'])): ?>
                <div class="summary-row" style="font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; margin-top: 12px; padding-top: 12px;">
                    <span>Remaining Balance</span>
                    <span>Rs <?= number_format($provider_response['quote_amount'] - $amount, 2) ?></span>
                </div>
                <?php endif; ?>

                <!-- Payment Details -->
                <?php if (!empty($provider_response['final_payment_due_date'])): ?>
                <div style="margin-top: 20px; padding: 12px; background: #f9fafb; border-radius: 6px; font-size: 13px;">
                    <div style="color: #6b7280; margin-bottom: 4px;">Final Payment Due</div>
                    <div style="color: #1f2937; font-weight: 600;"><?= htmlspecialchars($provider_response['final_payment_due_date']) ?></div>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div style="margin-top: 24px;">
                    <button type="button" class="btn btn-primary" id="proceedPaymentBtn" onclick="proceedToPayment()">
                        <i class="fas fa-lock"></i>
                        Proceed to PayPal
                    </button>
                    <a href="<?= ROOT ?>/Production_manager/manage_services?drama_id=<?= $request->drama_id ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Services
                    </a>
                </div>

                <!-- Provider Info -->
                <div style="margin-top: 20px; padding: 12px; background: #f9fafb; border-radius: 6px;">
                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 6px;">Service Provider</div>
                    <div style="font-weight: 600; color: #1f2937;"><?= htmlspecialchars($request->provider_name ?? 'Service Provider') ?></div>
                    <?php if (!empty($request->provider_email)): ?>
                    <div style="font-size: 12px; color: #6b7280; margin-top: 4px;">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($request->provider_email) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMethod = 'paypal';

        function selectPaymentMethod(method) {
            selectedMethod = method;
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }

        function proceedToPayment() {
            const termsChecked = document.getElementById('agreeTerms').checked;
            
            if (!termsChecked) {
                alert('Please agree to the Terms & Conditions to proceed');
                return;
            }

            const btn = document.getElementById('proceedPaymentBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            if (selectedMethod === 'paypal') {
                // Initiate PayPal payment
                initiatePayPalPayment();
            } else {
                alert('This payment method is coming soon. Please use PayPal.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-lock"></i> Proceed to PayPal';
            }
        }

        function initiatePayPalPayment() {
            // TODO: Integrate with PayPal SDK
            // For now, create payment record and show placeholder
            
            fetch('<?= ROOT ?>/Payment/initiate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    request_id: '<?= $request->id ?>',
                    amount: '<?= $amount ?>',
                    type: '<?= $type ?>',
                    stage: 'after_confirmation'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // TODO: Redirect to PayPal or open PayPal modal
                    alert('PayPal integration pending. Payment record created with ID: ' + data.payment_id);
                    
                    // For testing, simulate successful payment
                    setTimeout(() => {
                        window.location.href = '<?= ROOT ?>/Payment/success?payment_id=' + data.payment_id;
                    }, 2000);
                } else {
                    alert('Error: ' + data.error);
                    document.getElementById('proceedPaymentBtn').disabled = false;
                    document.getElementById('proceedPaymentBtn').innerHTML = '<i class="fas fa-lock"></i> Proceed to PayPal';
                }
            })
            .catch(error => {
                alert('Network error: ' + error.message);
                document.getElementById('proceedPaymentBtn').disabled = false;
                document.getElementById('proceedPaymentBtn').innerHTML = '<i class="fas fa-lock"></i> Proceed to PayPal';
            });
        }
    </script>
</body>
</html>
