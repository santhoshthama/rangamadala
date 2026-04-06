<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Payment Receipt - <?= APP_NAME ?></title>
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
            width: 100%;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 16px;
            line-height: 1.5;
            margin: 0;
            padding-top: 30px;
            padding-bottom: 30px;
        }

        .receipt-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            max-width: 950px;
            width: 100%;
            height: auto;
            padding: 0;
            animation: slideUp 0.5s ease-out;
            overflow: visible;
            display: grid;
            grid-template-rows: auto 1fr auto;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .receipt-header {
            background: white;
            color: #1a1a1a;
            padding: 32px 60px;
            text-align: center;
            border-bottom: 2px solid #d4af37;
            flex-shrink: 0;
        }

        .receipt-icon {
            width: 60px;
            height: 60px;
            background: #fffef8;
            border: 2px solid #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }

        .receipt-icon i {
            font-size: 32px;
            color: #d4af37;
        }

        .receipt-header h1 {
            font-size: 22px;
            margin-bottom: 4px;
            font-weight: 600;
            letter-spacing: -0.5px;
            color: #1a1a1a;
        }

        .receipt-header p {
            font-size: 12px;
            opacity: 0.8;
            margin: 0;
            letter-spacing: 0.5px;
            color: #6b7280;
        }

        .receipt-body {
            padding: 36px 60px;
            max-width: 100%;
            margin: 0 auto;
            padding-bottom: 0;
        }

        @media (max-width: 900px) {
            .receipt-body {
                padding: 28px 40px;
                padding-bottom: 0;
            }

            .receipt-header {
                padding: 28px 40px;
            }

            .button-group {
                flex-direction: column;
                padding: 24px 40px;
            }

            .footer {
                padding: 18px 40px;
            }

            .btn {
                width: 100%;
            }
        }

        .status-section {
            text-align: center;
            margin-bottom: 20px;
            grid-column: 1 / -1;
            padding: 24px;
            background: linear-gradient(135deg, #f0fdf4 0%, #f7fee7 100%);
            border-radius: 8px;
            border: 1px solid #d1fae5;
        }

        .status-icon {
            width: 56px;
            height: 56px;
            background: #d4af37;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            animation: checkmarkScale 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .status-icon i {
            font-size: 28px;
            color: white;
        }

        @keyframes checkmarkScale {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
            }
        }

        .status-badge {
            display: block;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            color: #2d7a3e;
            letter-spacing: -0.5px;
        }

        .status-success {
            color: #2d7a3e;
        }

        .status-pending {
            color: #854d0e;
        }

        .status-failed {
            color: #991b1b;
        }

        .amount-highlight {
            background: linear-gradient(135deg, #fffef8 0%, #fefce8 100%);
            border: 2px solid #d4af37;
            border-radius: 8px;
            padding: 18px 20px;
            text-align: center;
            margin-bottom: 14px;
            grid-column: 1 / -1;
        }

        .amount-label {
            color: #7c6b47;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .amount-value {
            font-size: 42px;
            color: #d4af37;
            font-weight: 800;
            margin: 0;
            letter-spacing: -1px;
        }

        .section {
            margin-bottom: 24px;
        }

        .section-title {
            font-size: 13px;
            color: #1a1a1a;
            margin-bottom: 16px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            padding-bottom: 10px;
            padding-left: 20px;
            border-bottom: 2px solid #d4af37;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 20px;
            border-bottom: 1px solid #e5e7eb;
            gap: 40px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 14px;
            color: #6b7280;
            font-weight: 500;
            flex-shrink: 0;
            min-width: 140px;
        }

        .detail-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 600;
            text-align: right;
            flex: 1;
        }

        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 20px 0;
        }

        .button-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            padding: 28px 60px;
            flex-direction: column;
            align-items: stretch;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            margin-top: 0;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 12px 22px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            width: 100%;
        }

        .btn-primary {
            background: #d4af37;
            color: #1a1410;
            border: none;
        }

        .btn-primary:hover {
            background: #c9a32e;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(212, 175, 55, 0.25);
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f3f4f6;
            border-color: #9ca3af;
        }

        .footer {
            padding: 20px 60px;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            flex-shrink: 0;
        }

        .footer-text {
            font-size: 11px;
            color: #9ca3af;
            margin: 0;
            line-height: 1.5;
        }

        .footer-text strong {
            color: #6b7280;
            font-weight: 600;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .receipt-container {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }

            .button-group {
                display: none;
            }

            .footer {
                display: none;
            }
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
                height: auto;
            }

            .receipt-container {
                max-height: none;
            }

            .receipt-body {
                padding: 24px 28px;
                padding-bottom: 0;
            }

            .receipt-header {
                padding: 24px 28px;
            }

            .detail-row {
                padding: 12px 16px;
                gap: 20px;
            }

            .detail-label {
                font-size: 13px;
                min-width: 100px;
            }

            .detail-value {
                font-size: 13px;
            }

            .section-title {
                font-size: 12px;
                padding-left: 16px;
            }

            .btn {
                width: 100%;
                min-width: auto;
                padding: 11px 18px;
                font-size: 13px;
            }

            .button-group {
                padding: 20px 28px;
            }

            .footer {
                padding: 16px 28px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="receipt-icon">
                <i class="fas fa-receipt"></i>
            </div>
            <h1>Service Payment Receipt</h1>
            <p>Receipt #<?= htmlspecialchars($receipt_number) ?></p>
        </div>

        <!-- Body -->
        <div class="receipt-body">
            <!-- Payment Details Section -->
            <div class="section">
                <div class="section-title">Payment Information</div>
                
                <?php 
                $statusClass = 'status-pending';
                $statusText = 'Payment Pending';
                $bankSlipPath = $transactionData['bank_slip_path'] ?? '';
                if ($payment->payment_status === 'success' || $payment->payment_status === 'completed') {
                    $statusClass = 'status-success';
                    $statusText = 'Payment Successful';
                } elseif ($payment->payment_status === 'failed' || $payment->payment_status === 'canceled') {
                    $statusClass = 'status-failed';
                    $statusText = 'Payment Cancelled';
                }
                ?>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value" style="color: <?php echo ($statusClass === 'status-success' ? '#2d7a3e' : ($statusClass === 'status-failed' ? '#991b1b' : '#854d0e')); ?>"><?= htmlspecialchars($statusText) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value" style="color: #d4af37; font-weight: 700; font-size: 14px;">Rs <?= number_format($payment->amount, 2) ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Request ID</span>
                    <span class="detail-value"><?= htmlspecialchars($payment->service_request_id) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Type</span>
                    <span class="detail-value"><?php 
                        $typeLabel = ucfirst($payment->payment_type);
                        if ($payment->payment_type === 'remaining') {
                            $typeLabel = 'Remaining Payment';
                        } elseif ($payment->payment_type === 'advance') {
                            $typeLabel = 'Advance Payment';
                        } elseif ($payment->payment_type === 'full') {
                            $typeLabel = 'Full Payment';
                        }
                        echo htmlspecialchars($typeLabel);
                    ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">
                        <?php
                            if (($payment->payment_gateway ?? '') === 'bank_transfer') {
                                echo 'Bank Transfer';
                            } elseif (($payment->payment_gateway ?? '') === 'cash') {
                                echo 'Cash';
                            } else {
                                echo isset($transactionData['method']) ? htmlspecialchars($transactionData['method']) : 'PayHere';
                            }
                        ?>
                    </span>
                </div>
                <?php if (($payment->payment_gateway ?? '') === 'bank_transfer' && !empty($bankSlipPath)): ?>
                <div class="detail-row">
                    <span class="detail-label">Bank Slip Evidence</span>
                    <span class="detail-value">
                        <a href="<?= ROOT ?>/Payment/viewBankSlip/<?= (int)$payment->id ?>" target="_blank" style="color:#1d4ed8;text-decoration:none;font-weight:600;">View Uploaded Slip</a>
                    </span>
                </div>
                <?php endif; ?>
                <?php if (($payment->payment_gateway ?? '') === 'cash'): ?>
                <?php $cashReceivedDate = $transactionData['received_date'] ?? ''; ?>
                <?php $cashNote = $transactionData['note'] ?? ''; ?>
                <?php if (!empty($cashReceivedDate)): ?>
                <div class="detail-row">
                    <span class="detail-label">Cash Received Date</span>
                    <span class="detail-value"><?= htmlspecialchars($cashReceivedDate) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($cashNote)): ?>
                <div class="detail-row">
                    <span class="detail-label">Note</span>
                    <span class="detail-value"><?= htmlspecialchars($cashNote) ?></span>
                </div>
                <?php endif; ?>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="detail-label">Date & Time</span>
                    <span class="detail-value"><?= date('M d, Y h:i A', strtotime($payment->paid_at ?? $payment->created_at)) ?></span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Service Request Details Section -->
            <div class="section">
                <div class="section-title">Service Details</div>
                <div class="detail-row">
                    <span class="detail-label">Drama</span>
                    <span class="detail-value"><?= htmlspecialchars($request->drama_name ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Service</span>
                    <span class="detail-value"><?= htmlspecialchars($request->service_type ?? 'N/A') ?></span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Transaction Details Section -->
            <div class="section">
                <div class="section-title">Transaction Details</div>
                <div class="detail-row">
                    <span class="detail-label">Paid By</span>
                    <span class="detail-value"><?= htmlspecialchars($paidBy->full_name ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Paid To (Provider)</span>
                    <span class="detail-value"><?= htmlspecialchars($paidTo->full_name ?? 'N/A') ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Request Reference</span>
                    <span class="detail-value\"><?= htmlspecialchars($payment->reference_number ?? $payment->gateway_order_id ?? 'N/A') ?></span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="button-group">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i>
                    Print Receipt
                </button>
                <a href="<?= !empty($isProviderView) ? (ROOT . '/ServicePayment') : (ROOT . '/Production_manager/manage_services?drama_id=' . ($request->drama_id ?? '')) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    <?= !empty($isProviderView) ? 'Back to Payments' : 'Back to Services' ?>
                </a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                <strong>Rangamadala</strong> | Service Payment Receipt<br>
                This is a computer-generated receipt. For support, contact support@rangamadala.com
            </p>
        </div>
    </div>
</body>
</html>
