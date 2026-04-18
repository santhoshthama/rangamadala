<?php 
// Extract data array
if(isset($data) && is_array($data)) {
    extract($data);
}

$profileImageSrc = ROOT . '/uploads/profile_images/user_profile.png';
if (isset($user->profile_image) && !empty($user->profile_image)) {
    $imageValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
}

$grouped = isset($grouped_notifications) ? $grouped_notifications : [];
$unreadCount = isset($unread_count) ? (int)$unread_count : 0;
$allNotifications = isset($all_notifications) ? $all_notifications : [];

// Notification type config
$typeConfig = [
    'role_assigned'         => ['icon' => 'bx-user-check',   'color' => '#28a745', 'label' => 'Role Assigned'],
    'role_removed'          => ['icon' => 'bx-user-x',       'color' => '#dc3545', 'label' => 'Role Removed'],
    'role_artist_removed'   => ['icon' => 'bx-user-minus',   'color' => '#b45309', 'label' => 'Artist Removed (Director)'],
    'event_scheduled'       => ['icon' => 'bx-calendar-plus','color' => '#ba8e23', 'label' => 'Event Scheduled'],
    'event_updated'         => ['icon' => 'bx-calendar-check','color' => '#17a2b8', 'label' => 'Event Updated'],
    'event_cancelled'       => ['icon' => 'bx-calendar-x',   'color' => '#dc3545', 'label' => 'Event Cancelled'],
    'application_accepted'  => ['icon' => 'bx-check-circle', 'color' => '#28a745', 'label' => 'Application Accepted'],
    'application_rejected'  => ['icon' => 'bx-x-circle',     'color' => '#dc3545', 'label' => 'Application Rejected'],
    'interview_scheduled'   => ['icon' => 'bx-user-voice',   'color' => '#ba8e23', 'label' => 'Interview Scheduled'],
    'pm_provider_responded_quote' => ['icon' => 'bx-receipt', 'color' => '#2563eb', 'label' => 'Quote Received'],
    'pm_provider_accepted_terms' => ['icon' => 'bx-check-shield', 'color' => '#16a34a', 'label' => 'Terms Accepted'],
    'pm_provider_rejected_terms' => ['icon' => 'bx-x-circle', 'color' => '#dc2626', 'label' => 'Terms Rejected'],
    'pm_provider_marked_completed' => ['icon' => 'bx-flag', 'color' => '#0ea5e9', 'label' => 'Service Completed'],
    'pm_provider_rejected_request' => ['icon' => 'bx-user-x', 'color' => '#b91c1c', 'label' => 'Request Rejected'],
    'pm_provider_confirmed_manual_payment' => ['icon' => 'bx-wallet', 'color' => '#15803d', 'label' => 'Payment Confirmed'],
    'pm_provider_rejected_manual_payment' => ['icon' => 'bx-error-circle', 'color' => '#d97706', 'label' => 'Payment Verification Failed'],
];

function getNotificationSourceCategory($type) {
    $type = (string)$type;

    // Explicit role-removal routing
    // role_removed        -> actor-facing message (includes reason)
    // role_artist_removed -> director-facing action log
    if ($type === 'role_removed' || $type === 'role_assigned') {
        return 'actor';
    }
    if ($type === 'role_artist_removed') {
        return 'director';
    }

    // PM-origin notifications
    if (
        strpos($type, '_by_pm') !== false ||
        strpos($type, 'service_request_created_pm') === 0 ||
        strpos($type, 'payment_submitted_by_pm') === 0 ||
        strpos($type, 'payment_completed_by_pm') === 0
    ) {
        return 'pm';
    }

    // Actor-origin notifications (including service-provider updates to PM)
    if (
        strpos($type, 'pm_provider_') === 0 ||
        strpos($type, 'actor_') === 0 ||
        strpos($type, 'artist_') === 0
    ) {
        return 'actor';
    }

    // Director-origin notifications
    if (
        strpos($type, 'role_') === 0 ||
        strpos($type, 'application_') === 0 ||
        strpos($type, 'interview_') === 0 ||
        strpos($type, 'event_') === 0
    ) {
        return 'director';
    }

    // Fallback
    return 'drama';
}

$actorNotifications = [];
$directorNotifications = [];
$pmNotifications = [];

foreach ($allNotifications as $notificationItem) {
    $source = getNotificationSourceCategory($notificationItem->type ?? '');
    if ($source === 'actor') {
        $actorNotifications[] = $notificationItem;
    } elseif ($source === 'director') {
        $directorNotifications[] = $notificationItem;
    } elseif ($source === 'pm') {
        $pmNotifications[] = $notificationItem;
    }
}

$actorUnreadCount = count(array_filter($actorNotifications, function ($n) {
    return !(int)($n->is_read ?? 0);
}));
$directorUnreadCount = count(array_filter($directorNotifications, function ($n) {
    return !(int)($n->is_read ?? 0);
}));
$pmUnreadCount = count(array_filter($pmNotifications, function ($n) {
    return !(int)($n->is_read ?? 0);
}));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Rangamadala</title>
    <link rel="icon" type="image/png" href="<?=ROOT?>/assets/images/Rangamadala%20logo.png">
    <link rel="apple-touch-icon" href="<?=ROOT?>/assets/images/Rangamadala%20logo.png">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
        <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/toast.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(212, 175, 55, 0.15);
            color: #d4af37;
            padding: 10px 18px;
            border: 1.5px solid rgba(212, 175, 55, 0.4);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            margin-bottom: 18px;
        }
        .back-button:hover {
            background: rgba(212, 175, 55, 0.25);
            border-color: #d4af37;
            color: #f5f0e8;
            transform: translateX(-3px);
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
        }
        .back-button i {
            font-size: 14px;
        }

                .header--wrapper .user--info {
                    gap: 16px;
                }

                .header--wrapper .role-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    padding: 6px 14px;
                    border-radius: 999px;
                    background: linear-gradient(135deg, #be9227, #a67d1e);
                    color: #fff;
                    border: 1px solid rgba(145, 108, 24, 0.35);
                    box-shadow: 0 4px 10px rgba(166, 125, 30, 0.2);
                    font-size: 12px;
                    font-weight: 700;
                    letter-spacing: 0.4px;
                    text-transform: uppercase;
                    line-height: 1;
                }

                .header--wrapper .user-menu-trigger {
                    width: 52px;
                    height: 52px;
                    border-radius: 50%;
                    border: 3px solid #b88b22;
                    box-shadow: 0 0 0 2px rgba(224, 191, 105, 0.45);
                    overflow: hidden;
                    cursor: pointer;
                    transition: var(--transition);
                }

                .header--wrapper .user-menu-trigger:hover {
                    transform: scale(1.04);
                }

                .header--wrapper .user-avatar-small {
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    background: transparent;
                }

                .header--wrapper .user-avatar-small img {
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                    object-fit: cover;
                    display: block;
                    border: 0;
                }

                .header--wrapper .user-menu {
                    position: relative;
                }

                .header--wrapper .user-menu-dropdown {
                    position: absolute;
                    top: calc(100% + 8px);
                    right: 0;
                    z-index: 1000;
                    background: #ffffff;
                    border: 1px solid #f0d79d;
                    border-radius: 14px;
                    box-shadow: 0 16px 30px rgba(74, 58, 20, 0.18);
                    min-width: 210px;
                    padding: 8px;
                    opacity: 0;
                    visibility: hidden;
                    transform: translateY(-10px);
                    transition: all 0.2s ease;
                }

                .header--wrapper .user-menu.active .user-menu-dropdown {
                    opacity: 1;
                    visibility: visible;
                    transform: translateY(0);
                }

                .header--wrapper .user-menu-item {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px 12px;
                    color: #2f2f2f;
                    text-decoration: none;
                    font-size: 15px;
                    border-radius: 10px;
                    transition: var(--transition);
                    cursor: pointer;
                }

                .header--wrapper .user-menu-item:hover {
                    background: rgba(186, 142, 35, 0.1);
                    color: #5a4415;
                }

                .header--wrapper .user-menu-item .icon {
                    font-size: 20px;
                }

        .artist-stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .artist-stat-card {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            border: 1px solid #f0dfb4;
            border-radius: var(--radius);
            padding: 22px;
            text-align: left;
            color: #4a3a14;
            box-shadow: 0 4px 12px rgba(186, 142, 35, 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .artist-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(186, 142, 35, 0.2);
        }

        .artist-stat-card .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .artist-stat-card .stat-card-title {
            font-size: 12px;
            font-weight: 600;
            color: #7a6121;
        }

        .artist-stat-card .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
        }

        .artist-stat-card .stat-card-icon.primary,
        .artist-stat-card .stat-card-icon.info,
        .artist-stat-card .stat-card-icon.success,
        .artist-stat-card .stat-card-icon.warning {
            background: rgba(186, 142, 35, 0.14);
            color: var(--brand);
        }

        .artist-stat-card .stat-card-value {
            font-size: 40px;
            line-height: 1;
            font-weight: 800;
            color: #7f5f13;
            margin-top: 2px;
        }

        .notification-item {
            display: flex;
            gap: 14px;
            padding: 16px 18px;
            margin-bottom: 12px;
            border: 1px solid #e8dcc9;
            background: #fffbf5;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(186, 142, 35, 0.08);
        }
        .notification-item:hover {
            background: #fff;
            border-color: #ba8e23;
            box-shadow: 0 4px 12px rgba(186, 142, 35, 0.15);
            transform: translateY(-1px);
        }
        .notification-item.unread {
            background: linear-gradient(135deg, #fff9f0 0%, #fffbf7 100%);
            border: 1px solid #dcc99e;
            border-left: 4px solid var(--brand);
        }
        .notification-item.unread:hover {
            background: linear-gradient(135deg, #fff5eb 0%, #fff8f0 100%);
            border-color: #ba8e23;
            box-shadow: 0 4px 12px rgba(186, 142, 35, 0.18);
        }
        .notification-icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #fff;
            font-size: 16px;
        }
        .notification-body {
            flex: 1;
            min-width: 0;
        }
        .notification-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--ink);
            margin-bottom: 4px;
        }
        .notification-message {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.5;
            display: -webkit-box;
            line-clamp: 2;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .notification-time {
            font-size: 11px;
            color: var(--muted-2);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .notification-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .drama-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            color: #4a3a14;
            border: 1px solid #f0dfb4;
            border-radius: 8px;
            margin-bottom: 8px;
            margin-top: 16px;
        }
        .drama-group-header:first-child {
            margin-top: 0;
        }
        .drama-group-header h4 {
            margin: 0;
            font-size: 15px;
            font-weight: 700;
        }
        .unread-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--brand);
            display: inline-block;
            flex-shrink: 0;
        }
        .tab-buttons {
            display: flex;
            gap: 0;
            margin-bottom: 20px;
            flex-wrap: wrap;
            background: linear-gradient(180deg, #fffefb 0%, #fff7e7 100%);
            border: 1px solid #ead7a4;
            border-bottom: 2px solid #dfc98f;
            border-radius: 14px 14px 0 0;
            box-shadow: 0 6px 16px rgba(186, 142, 35, 0.1);
            overflow-x: auto;
        }

        .tab-btn {
            padding: 14px 20px;
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            background: transparent;
            color: #5a636f;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .tab-btn:hover {
            color: #2f2410;
            background: rgba(186, 142, 35, 0.08);
        }

        .tab-btn.active {
            color: #a37917;
            border-bottom-color: #ba8e23;
            background: rgba(186, 142, 35, 0.12);
            box-shadow: none;
        }

        @media (max-width: 900px) {
            .artist-stats-grid {
                grid-template-columns: 1fr;
            }
        }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
        }
        .empty-state i {
            font-size: 48px;
            display: block;
            margin-bottom: 16px;
            color: var(--border);
        }
        .empty-state h3 {
            color: var(--ink);
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php
    $artistSidebarActive = 'notifications';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="main--content">
     

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Notifications</span>
                <h2>Your Notifications <?php if ($unreadCount > 0): ?><span style="font-size: 16px; color: var(--brand);">(<?= $unreadCount ?> unread)</span><?php endif; ?></h2>
            </div>
               <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-star"></i> Artist
                </div>
                <div class="user-menu" id="userMenu">
                    <div class="user-menu-trigger" id="user-menu-trigger">
                        <div class="user-avatar-small">
                            <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
                        </div>
                    </div>
                    <div class="user-menu-dropdown">
                        <a href="<?= ROOT ?>/profile" class="user-menu-item">
                            <i class='bx bxs-user icon'></i>
                            <span>Profile</span>
                        </a>
                        <a href="<?= ROOT ?>/logout" class="user-menu-item">
                            <i class='bx bx-log-out icon'></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid artist-stats-grid">
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Notifications</div>
                    <div class="stat-card-icon warning">
                        <i class="bx bx-bell"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= count($allNotifications) ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Unread</div>
                    <div class="stat-card-icon info">
                        <i class="bx bx-envelope"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $unreadCount ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Dramas</div>
                    <div class="stat-card-icon primary">
                        <i class="bx bx-film"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= count($grouped) ?></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="switchTab('all', this)">
                <i class="bx bx-bell"></i> All Notifications
            </button>
            <button class="tab-btn" onclick="switchTab('actor', this)">
                <i class="bx bx-user"></i> By Actor <?= $actorUnreadCount > 0 ? '(' . $actorUnreadCount . ')' : '' ?>
            </button>
            <button class="tab-btn" onclick="switchTab('director', this)">
                <i class="bx bx-video"></i> By Director <?= $directorUnreadCount > 0 ? '(' . $directorUnreadCount . ')' : '' ?>
            </button>
            <button class="tab-btn" onclick="switchTab('pm', this)">
                <i class="bx bx-briefcase"></i> Production Manager <?= $pmUnreadCount > 0 ? '(' . $pmUnreadCount . ')' : '' ?>
            </button>
            <button class="tab-btn" onclick="switchTab('grouped', this)">
                <i class="bx bx-film"></i> By Drama
            </button>
            <button class="tab-btn" onclick="switchTab('unread', this)">
                <i class="bx bx-envelope"></i> Unread Only
            </button>
        </div>

        <!-- Tab: All Notifications -->
        <div id="tab-all" class="tab-panel active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3><i class="bx bx-bell"></i> All Notifications</h3>
                            <?php if (empty($allNotifications)): ?>
                                <div class="empty-state">
                                    <i class="bx bx-bell-slash"></i>
                                    <h3>No Notifications Yet</h3>
                                    <p>You'll receive notifications when directors schedule events, assign roles, or update drama details.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($allNotifications as $n): 
                                    $tc = $typeConfig[$n->type] ?? ['icon' => 'bx-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                    $isUnread = !(int)$n->is_read;
                                    $timeAgo = timeAgoStr($n->created_at);
                                ?>
                                          <a href="<?= ROOT ?>/artistdashboard/notification_detail?id=<?= (int)$n->id ?>" 
                                   class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                    <div class="notification-icon" style="background: <?= $tc['color'] ?>;">
                                        <i class="bx <?= $tc['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-body">
                                        <div class="notification-title">
                                            <?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?>
                                            <?= esc($n->title) ?>
                                        </div>
                                        <div class="notification-message"><?= esc($n->message) ?></div>
                                        <div class="notification-time">
                                            <i class="bx bx-clock"></i> <?= $timeAgo ?>
                                            <?php if ($n->drama_name): ?>
                                                &nbsp;|&nbsp; <i class="bx bx-film"></i> <?= esc($n->drama_name) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="flex-shrink: 0;">
                                        <span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;">
                                            <i class="bx <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
                                        </span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: By Actor -->
        <div id="tab-actor" class="tab-panel">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3><i class="bx bx-user"></i> Notifications by Actor</h3>
                            <?php if (empty($actorNotifications)): ?>
                                <div class="empty-state">
                                    <i class="bx bx-inbox"></i>
                                    <h3>No Actor Notifications Yet</h3>
                                    <p>Actor-origin updates will appear here.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($actorNotifications as $n):
                                    $tc = $typeConfig[$n->type] ?? ['icon' => 'bx-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                    $isUnread = !(int)$n->is_read;
                                    $timeAgo = timeAgoStr($n->created_at);
                                ?>
                                <a href="<?= ROOT ?>/artistdashboard/notification_detail?id=<?= (int)$n->id ?>" class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                    <div class="notification-icon" style="background: <?= $tc['color'] ?>;"><i class="bx <?= $tc['icon'] ?>"></i></div>
                                    <div class="notification-body">
                                        <div class="notification-title"><?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?><?= esc($n->title) ?></div>
                                        <div class="notification-message"><?= esc($n->message) ?></div>
                                        <div class="notification-time"><i class="bx bx-clock"></i> <?= $timeAgo ?></div>
                                    </div>
                                    <div style="flex-shrink: 0;"><span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;"><i class="bx <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?></span></div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: By Director -->
        <div id="tab-director" class="tab-panel">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3><i class="bx bx-video"></i> Notifications by Director</h3>
                            <?php if (empty($directorNotifications)): ?>
                                <div class="empty-state">
                                    <i class="bx bx-inbox"></i>
                                    <h3>No Director Notifications Yet</h3>
                                    <p>Director-origin updates (role, interview, event) will appear here.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($directorNotifications as $n):
                                    $tc = $typeConfig[$n->type] ?? ['icon' => 'bx-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                    $isUnread = !(int)$n->is_read;
                                    $timeAgo = timeAgoStr($n->created_at);
                                ?>
                                <a href="<?= ROOT ?>/artistdashboard/notification_detail?id=<?= (int)$n->id ?>" class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                    <div class="notification-icon" style="background: <?= $tc['color'] ?>;"><i class="bx <?= $tc['icon'] ?>"></i></div>
                                    <div class="notification-body">
                                        <div class="notification-title"><?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?><?= esc($n->title) ?></div>
                                        <div class="notification-message"><?= esc($n->message) ?></div>
                                        <div class="notification-time"><i class="bx bx-clock"></i> <?= $timeAgo ?></div>
                                    </div>
                                    <div style="flex-shrink: 0;"><span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;"><i class="bx <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?></span></div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Production Manager -->
        <div id="tab-pm" class="tab-panel">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3><i class="bx bx-briefcase"></i> Production Manager Updates</h3>
                            <?php if (empty($pmNotifications)): ?>
                                <div class="empty-state">
                                    <i class="bx bx-inbox"></i>
                                    <h3>No PM Notifications Yet</h3>
                                    <p>Provider responses and request-stage updates will appear here.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pmNotifications as $n):
                                    $tc = $typeConfig[$n->type] ?? ['icon' => 'bx-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                    $isUnread = !(int)$n->is_read;
                                    $timeAgo = timeAgoStr($n->created_at);
                                ?>
                                          <a href="<?= ROOT ?>/artistdashboard/notification_detail?id=<?= (int)$n->id ?>"
                                   class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                    <div class="notification-icon" style="background: <?= $tc['color'] ?>;">
                                        <i class="bx <?= $tc['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-body">
                                        <div class="notification-title">
                                            <?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?>
                                            <?= esc($n->title) ?>
                                        </div>
                                        <div class="notification-message"><?= esc($n->message) ?></div>
                                        <div class="notification-time">
                                            <i class="bx bx-clock"></i> <?= $timeAgo ?>
                                            <?php if ($n->drama_name): ?>
                                                &nbsp;|&nbsp; <i class="bx bx-film"></i> <?= esc($n->drama_name) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="flex-shrink: 0;">
                                        <span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;">
                                            <i class="bx <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
                                        </span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Grouped by Drama -->
        <div id="tab-grouped" class="tab-panel">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <?php if (empty($grouped)): ?>
                            <div class="card-section">
                                <div class="empty-state">
                                    <i class="bx bx-bell-slash"></i>
                                    <h3>No Notifications Yet</h3>
                                    <p>Notifications will appear here grouped by drama.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($grouped as $dramaKey => $group): ?>
                                <div class="card-section" style="margin-bottom: 16px;">
                                    <div class="drama-group-header">
                                        <h4>
                                            <i class="bx bx-film"></i> <?= esc($group['drama_name']) ?>
                                        </h4>
                                        <?php if ($group['unread_count'] > 0): ?>
                                            <span style="background: rgba(255,255,255,0.25); color: #fff; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
                                                <?= $group['unread_count'] ?> new
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php foreach ($group['notifications'] as $n): 
                                        $tc = $typeConfig[$n->type] ?? ['icon' => 'bx-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                        $isUnread = !(int)$n->is_read;
                                        $timeAgo = timeAgoStr($n->created_at);
                                    ?>
                                                <a href="<?= ROOT ?>/artistdashboard/notification_detail?id=<?= (int)$n->id ?>" 
                                       class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                        <div class="notification-icon" style="background: <?= $tc['color'] ?>;">
                                            <i class="bx <?= $tc['icon'] ?>"></i>
                                        </div>
                                        <div class="notification-body">
                                            <div class="notification-title">
                                                <?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?>
                                                <?= esc($n->title) ?>
                                            </div>
                                            <div class="notification-message"><?= esc($n->message) ?></div>
                                            <div class="notification-time">
                                                <i class="bx bx-clock"></i> <?= $timeAgo ?>
                                            </div>
                                        </div>
                                        <div style="flex-shrink: 0;">
                                            <span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;">
                                                <i class="bx <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
                                            </span>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab: Unread Only -->
        <div id="tab-unread" class="tab-panel">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3><i class="bx bx-circle" style="font-size: 10px; color: var(--brand);"></i> Unread Notifications</h3>
                            <?php 
                            $unreadNotifications = array_filter($allNotifications, function($n) { return !(int)$n->is_read; });
                            ?>
                            <?php if (empty($unreadNotifications)): ?>
                                <div class="empty-state">
                                    <i class="bx bx-check-circle" style="color: #28a745;"></i>
                                    <h3>All Caught Up!</h3>
                                    <p>You have no unread notifications.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($unreadNotifications as $n): 
                                    $tc = $typeConfig[$n->type] ?? ['icon' => 'bx-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                    $timeAgo = timeAgoStr($n->created_at);
                                ?>
                                          <a href="<?= ROOT ?>/artistdashboard/notification_detail?id=<?= (int)$n->id ?>" 
                                   class="notification-item unread">
                                    <div class="notification-icon" style="background: <?= $tc['color'] ?>;">
                                        <i class="bx <?= $tc['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-body">
                                        <div class="notification-title">
                                            <span class="unread-dot"></span> <?= esc($n->title) ?>
                                        </div>
                                        <div class="notification-message"><?= esc($n->message) ?></div>
                                        <div class="notification-time">
                                            <i class="bx bx-clock"></i> <?= $timeAgo ?>
                                            <?php if ($n->drama_name): ?>
                                                &nbsp;|&nbsp; <i class="bx bx-film"></i> <?= esc($n->drama_name) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="flex-shrink: 0;">
                                        <span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;">
                                            <i class="bx <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
                                        </span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const userMenu = document.getElementById('userMenu');
        const userMenuTrigger = document.getElementById('user-menu-trigger');

        if (!userMenu || !userMenuTrigger) {
            return;
        }

        userMenuTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            userMenu.classList.toggle('active');
        });

        document.addEventListener('click', function (e) {
            if (!userMenu.contains(e.target)) {
                userMenu.classList.remove('active');
            }
        });
    });
    </script>
</body>
</html>

<?php
/**
 * Helper: human-friendly time-ago string
 */
function timeAgoStr($datetime) {
    $now = new DateTime();
    $dt = new DateTime($datetime);
    $diff = $now->diff($dt);

    if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0) {
        if ($diff->d == 1) return 'Yesterday';
        return $diff->d . ' days ago';
    }
    if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0) return $diff->i . ' min ago';
    return 'Just now';
}
?>
