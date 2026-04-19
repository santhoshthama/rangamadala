<?php
    $receiptType = strtolower((string)($receipt_type ?? ''));
    $receipt = $receipt ?? null;
    $receiptNumber = (string)($receipt_number ?? '');

    $isClassReceipt = $receiptType === 'class';
    $title = $isClassReceipt
        ? ($receipt->class_title ?? 'Class Payment Receipt')
        : ($receipt->title ?? 'Showing Payment Receipt');

    $amount = $isClassReceipt
        ? (float)($receipt->amount ?? 0)
        : (float)($receipt->ticket_price ?? 0);

    if (!$isClassReceipt && !empty($receipt->showing_prices)) {
        $showingPriceText = trim((string)$receipt->showing_prices);
        if (preg_match('/\d+(?:\.\d+)?/', str_replace(',', '', $showingPriceText), $match)) {
            $amount = (float)$match[0];
        }
    }

    $paidAt = !empty($receipt->paid_at) ? date('M d, Y h:i A', strtotime($receipt->paid_at)) : 'N/A';
    $status = $isClassReceipt
        ? ucfirst((string)($receipt->status ?? 'completed'))
        : ucfirst((string)($receipt->booking_status ?? 'confirmed'));
    $reference = $isClassReceipt
        ? (string)($receipt->order_id ?? 'N/A')
        : (string)($receipt->payhere_order_id ?? 'N/A');
    $eventDate = $isClassReceipt
        ? (!empty($receipt->class_date) ? date('M d, Y', strtotime($receipt->class_date)) : 'TBA')
        : (!empty($receipt->event_date) ? date('M d, Y', strtotime($receipt->event_date)) : 'TBA');
    $venue = trim((string)($receipt->venue ?? '')) !== '' ? trim((string)($receipt->venue ?? '')) : 'N/A';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($receiptNumber) ?> - <?= APP_NAME ?></title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f1e6;
            color: #2d2416;
        }

        .receipt {
            max-width: 820px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e6d7b4;
            border-radius: 14px;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .receipt-header {
            padding: 22px 26px;
            border-bottom: 1px solid #e6d7b4;
            background: #fbfaf6;
        }

        .receipt-header-top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .receipt-type {
            display: inline-block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #8b6a1f;
        }

        .receipt-header h1 {
            margin: 0;
            font-size: 28px;
            line-height: 1.2;
        }

        .receipt-number {
            margin-top: 6px;
            font-size: 14px;
            color: #67583a;
        }

        .receipt-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn-link,
        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
        }

        .btn-link {
            background: #fff;
            border: 1px solid #d7c59a;
            color: #7a5a16;
        }

        .btn-print {
            background: #ba8e23;
            color: #fff;
            border: 1px solid #a57a1c;
            cursor: pointer;
        }

        .receipt-body {
            padding: 22px 26px;
        }

        .amount-box {
            border: 1px solid #e3cf9f;
            background: #fff9ea;
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            margin-bottom: 18px;
        }

        .amount-box .label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #8b6a1f;
            margin-bottom: 8px;
        }

        .amount-box .value {
            font-size: 36px;
            font-weight: 800;
            color: #a67d1e;
            line-height: 1;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .detail-item {
            border: 1px solid #e9dcc0;
            border-radius: 10px;
            padding: 14px 16px;
            background: #fff;
        }

        .detail-label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #81652a;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .detail-value {
            font-size: 15px;
            font-weight: 600;
            color: #2d2416;
            word-break: break-word;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #f3e3bd;
            color: #7a5a16;
            font-weight: 700;
            font-size: 13px;
        }

        .receipt-footer {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            padding: 18px 26px 22px;
            border-top: 1px solid #e6d7b4;
            background: #fbfaf6;
        }

        .footer-note {
            font-size: 13px;
            color: #6d5e43;
        }

        @media (max-width: 720px) {
            body {
                padding: 14px;
            }

            .receipt-header-top,
            .receipt-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .amount-box .value {
                font-size: 30px;
            }
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                border: none;
                border-radius: 0;
            }

            .receipt-actions,
            .btn-print,
            .btn-link {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <div class="receipt-header-top">
                <div>
                    <div class="receipt-type"><?= htmlspecialchars($isClassReceipt ? 'Class Payment Receipt' : 'Drama Showing Receipt') ?></div>
                    <h1><?= htmlspecialchars($title) ?></h1>
                    <div class="receipt-number">Receipt No: <?= htmlspecialchars($receiptNumber) ?></div>
                </div>
                <div class="receipt-actions">
                    <a class="btn-link" href="<?= ROOT ?>/audiencedashboard#payments"><i class="bx bx-arrow-back"></i> Back</a>
                    <button class="btn-print" type="button" onclick="window.print()"><i class="bx bx-printer"></i> Print / Save PDF</button>
                </div>
            </div>
        </div>

        <div class="receipt-body">
            <div class="amount-box">
                <span class="label">Total Paid</span>
                <div class="value">Rs. <?= number_format($amount, 2) ?></div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Reference</span>
                    <div class="detail-value"><?= htmlspecialchars($reference) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Paid At</span>
                    <div class="detail-value"><?= htmlspecialchars($paidAt) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Status</span>
                    <div class="detail-value"><span class="status-pill"><?= htmlspecialchars($status) ?></span></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date</span>
                    <div class="detail-value"><?= htmlspecialchars($eventDate) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Venue</span>
                    <div class="detail-value"><?= htmlspecialchars($venue) ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Type</span>
                    <div class="detail-value"><?= htmlspecialchars($isClassReceipt ? 'Class Enrollment' : 'Drama Showing') ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Audience Member</span>
                    <div class="detail-value"><?= htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Audience Member') ?></div>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Generated</span>
                    <div class="detail-value"><?= htmlspecialchars(date('M d, Y h:i A')) ?></div>
                </div>
            </div>
        </div>

        <div class="receipt-footer">
            <div class="footer-note">Use Print / Save PDF to download this receipt.</div>
            <a class="btn-link" href="<?= ROOT ?>/audiencedashboard#payments"><i class="bx bx-list-ul"></i> Payments</a>
        </div>
    </div>
</body>
</html>