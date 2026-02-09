<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .cancel-container {
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

        .cancel-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
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

        .cancel-icon i {
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

        .info-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            font-size: 14px;
            color: #991b1b;
            text-align: left;
        }

        .help-box {
            background: #f9fafb;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            text-align: left;
        }

        .help-box h3 {
            font-size: 16px;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .help-box ul {
            list-style: none;
            padding: 0;
        }

        .help-box li {
            padding: 8px 0;
            font-size: 14px;
            color: #4b5563;
            display: flex;
            align-items: start;
            gap: 10px;
        }

        .help-box li i {
            color: #10b981;
            margin-top: 2px;
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
    <div class="cancel-container">
        <div class="cancel-icon">
            <i class="fas fa-times"></i>
        </div>

        <h1>Payment Cancelled</h1>
        <p class="subtitle">Your payment was not completed</p>

        <div class="info-box">
            <strong>⚠️ No charges were made</strong><br>
            Your payment was cancelled and no money has been deducted from your account. The service request is still pending.
        </div>

        <div class="help-box">
            <h3>What would you like to do?</h3>
            <ul>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Try the payment again with a different method</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Review the service details and proceed later</span>
                </li>
                <li>
                    <i class="fas fa-check-circle"></i>
                    <span>Contact support if you encountered any issues</span>
                </li>
            </ul>
        </div>

        <div>
            <?php if (isset($payment_id) && $payment_id): ?>
            <a href="<?= ROOT ?>/Payment/retry?payment_id=<?= $payment_id ?>" class="btn btn-primary">
                <i class="fas fa-redo"></i>
                Try Again
            </a>
            <?php endif; ?>
            <a href="<?= ROOT ?>/Production_manager/manage_services" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Services
            </a>
        </div>

        <p class="footer-text">
            Need help? Contact us at support@rangamadala.com
        </p>
    </div>
</body>
</html>
