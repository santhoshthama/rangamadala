<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            animation: slideUp 0.5s ease-out;
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

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            animation: scaleIn 0.6s ease-out 0.2s both;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }

        .success-icon i {
            font-size: 40px;
            color: white;
        }

        h1 {
            font-size: 28px;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .subtitle {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 32px;
        }

        .payment-details {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            text-align: left;
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
        }

        .detail-value {
            font-size: 14px;
            color: #1f2937;
            font-weight: 600;
        }

        .amount {
            font-size: 20px;
            color: #10b981;
            font-weight: 700;
        }

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #1e40af;
            text-align: left;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            margin: 6px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #d4af37, #aa8c2c);
            color: #1a1410;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(212, 175, 55, 0.3);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .footer-text {
            margin-top: 24px;
            font-size: 13px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1>Payment Successful!</h1>
        <p class="subtitle">Your payment has been processed successfully</p>

        <div class="payment-details">
            <div class="detail-row">
                <span class="detail-label">Payment ID</span>
                <span class="detail-value">#<?= str_pad($payment->id, 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Drama</span>
                <span class="detail-value"><?= htmlspecialchars($request->drama_name) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Service</span>
                <span class="detail-value"><?= htmlspecialchars($request->service_type) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Provider</span>
                <span class="detail-value"><?= htmlspecialchars($request->provider_name) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Type</span>
                <span class="detail-value"><?= ucfirst($payment->payment_type) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount Paid</span>
                <span class="amount">Rs <?= number_format($payment->amount, 2) ?></span>
            </div>
        </div>

        <div class="info-box">
            <strong>📧 Confirmation Sent</strong><br>
            A payment receipt has been sent to your email. The service provider has been notified and will proceed with your service.
        </div>

        <div>
            <a href="<?= ROOT ?>/Production_manager/manage_services?drama_id=<?= $request->drama_id ?>" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Services
            </a>
            <a href="<?= ROOT ?>/artistdashboard" class="btn btn-secondary">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
        </div>

        <p class="footer-text">
            Payment processed on <?= date('M d, Y \a\t h:i A') ?>
        </p>
    </div>
</body>
</html>
