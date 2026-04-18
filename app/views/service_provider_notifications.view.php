<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Notifications' ?> - Rangamadala</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- Admin Design Library CSS -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Button.css">
    <!-- Service Provider Styles -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service provider/service_provider_dashboard.css">
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <style>
        .notifications-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .notifications-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--color-border);
            background: #fafafa;
        }

        .notifications-toolbar h3 {
            margin: 0;
            font-size: 18px;
            color: #1f2937;
        }

        .notifications-toolbar .count {
            font-size: 13px;
            color: #6b7280;
            margin-left: 8px;
        }

        .mark-all-btn {
            background: #111827;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
        }

        .mark-all-btn:hover {
            background: #374151;
        }

        .notification-list {
            display: grid;
            gap: 10px;
            padding: 14px;
        }

        .notification-item {
            border: 1px solid #e5e7eb;
            border-left: 4px solid #d1d5db;
            border-radius: 10px;
            padding: 14px;
            background: #fff;
        }

        .notification-item.unread {
            border-left-color: #d4af37;
            background: #fffbef;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: flex-start;
            margin-bottom: 6px;
        }

        .notification-title {
            margin: 0;
            font-size: 15px;
            color: #111827;
        }

        .notification-time {
            font-size: 12px;
            color: #6b7280;
            white-space: nowrap;
        }

        .notification-message {
            margin: 0;
            color: #374151;
            font-size: 14px;
            line-height: 1.5;
        }

        .notification-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .notification-actions a {
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        }

        .action-link {
            color: #2563eb;
        }

        .action-mark {
            color: #059669;
        }

        .empty-state {
            text-align: center;
            color: #6b7280;
            padding: 40px 16px;
        }

        .flash-message {
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
        }

        .flash-message.success {
            background: #dcfce7;
            color: #166534;
        }

        .flash-message.error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    <?php $activePage = 'notifications'; include 'includes/service_provider/sidebar.php'; ?>

    <div class="main--content">
        <?php include 'includes/service_provider/header.php'; ?>

        <div class="container">
            <?php
                $notifications = isset($notifications) && is_array($notifications) ? $notifications : [];
                $unreadCount = isset($unreadCount) ? (int)$unreadCount : 0;
            ?>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="flash-message <?= ($_SESSION['message_type'] ?? '') === 'success' ? 'success' : 'error' ?>">
                    <?= htmlspecialchars($_SESSION['message']) ?>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <div class="notifications-container">
                <div class="notifications-toolbar">
                    <h3>
                        Provider Notifications
                        <span class="count"><?= count($notifications) ?> total, <?= $unreadCount ?> unread</span>
                    </h3>
                    <?php if ($unreadCount > 0): ?>
                        <a class="mark-all-btn" href="<?= ROOT ?>/ServiceProviderNotifications/markAllRead">Mark All Read</a>
                    <?php endif; ?>
                </div>

                <?php if (empty($notifications)): ?>
                    <div class="empty-state">
                        <i class="bx bxs-bell" style="font-size: 24px; margin-bottom: 8px;"></i>
                        <p>No notifications yet.</p>
                    </div>
                <?php else: ?>
                    <div class="notification-list">
                        <?php foreach ($notifications as $n): ?>
                            <?php
                                $isUnread = (int)($n->is_read ?? 0) === 0;
                                $targetLink = !empty($n->link) ? $n->link : (ROOT . '/ServiceRequests');
                                $createdAt = !empty($n->created_at) ? date('Y-m-d H:i', strtotime($n->created_at)) : '-';
                            ?>
                            <div class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                <div class="notification-header">
                                    <h4 class="notification-title"><?= htmlspecialchars($n->title ?? 'Notification') ?></h4>
                                    <span class="notification-time"><?= htmlspecialchars($createdAt) ?></span>
                                </div>
                                <p class="notification-message"><?= htmlspecialchars($n->message ?? '') ?></p>
                                <div class="notification-actions">
                                    <a class="action-link" href="<?= ROOT ?>/ServiceProviderNotifications/detail?id=<?= (int)($n->id ?? 0) ?>">View Details</a>
                                    <?php if ($isUnread): ?>
                                        <a class="action-mark" href="<?= ROOT ?>/ServiceProviderNotifications/markRead?id=<?= (int)($n->id ?? 0) ?>">Mark as read</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
