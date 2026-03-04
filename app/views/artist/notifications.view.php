<?php 
// Extract data array
if(isset($data) && is_array($data)) {
    extract($data);
}

// Profile image
$userModel = new M_universal_profile();
$currentUser = $userModel->getUserById($_SESSION['user_id']);

$profileImageSrc = ROOT . '/assets/images/default-avatar.jpg';
if ($currentUser && !empty($currentUser->profile_image)) {
    $imageValue = str_replace('\\', '/', $currentUser->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif ($currentUser && !empty($currentUser->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $currentUser->nic_photo), '/');
}

$grouped = isset($grouped_notifications) ? $grouped_notifications : [];
$unreadCount = isset($unread_count) ? (int)$unread_count : 0;
$allNotifications = isset($all_notifications) ? $all_notifications : [];

// Notification type config
$typeConfig = [
    'role_assigned'         => ['icon' => 'fa-user-check',     'color' => '#28a745', 'label' => 'Role Assigned'],
    'role_removed'          => ['icon' => 'fa-user-times',     'color' => '#dc3545', 'label' => 'Role Removed'],
    'event_scheduled'       => ['icon' => 'fa-calendar-plus',  'color' => '#ba8e23', 'label' => 'Event Scheduled'],
    'event_updated'         => ['icon' => 'fa-calendar-check', 'color' => '#17a2b8', 'label' => 'Event Updated'],
    'event_cancelled'       => ['icon' => 'fa-calendar-times', 'color' => '#dc3545', 'label' => 'Event Cancelled'],
    'application_accepted'  => ['icon' => 'fa-check-circle',   'color' => '#28a745', 'label' => 'Application Accepted'],
    'application_rejected'  => ['icon' => 'fa-times-circle',   'color' => '#dc3545', 'label' => 'Application Rejected'],
    'interview_scheduled'   => ['icon' => 'fa-user-clock',     'color' => '#ba8e23', 'label' => 'Interview Scheduled'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .notification-item {
            display: flex;
            gap: 14px;
            padding: 14px 16px;
            border-bottom: 1px solid #eee;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            border-radius: 8px;
        }
        .notification-item:hover {
            background: rgba(186, 142, 35, 0.06);
        }
        .notification-item.unread {
            background: rgba(186, 142, 35, 0.08);
            border-left: 3px solid var(--brand);
        }
        .notification-item.unread:hover {
            background: rgba(186, 142, 35, 0.14);
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
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            color: #fff;
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
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 8px 20px;
            border: 2px solid var(--border);
            border-radius: 999px;
            background: var(--card);
            color: var(--ink);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .tab-btn:hover {
            border-color: var(--brand);
            color: var(--brand);
        }
        .tab-btn.active {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
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
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?= ROOT ?>/artistdashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/profile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/artistdashboard/notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard/browse_vacancies">
                    <i class="fas fa-bullhorn"></i>
                    <span>View All Vacancies</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/browseDramas">
                    <i class="fas fa-theater-masks"></i>
                    <span>Browse Dramas</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/artistdashboard" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Notifications</span>
                <h2>Your Notifications <?php if ($unreadCount > 0): ?><span style="font-size: 16px; color: var(--brand);">(<?= $unreadCount ?> unread)</span><?php endif; ?></h2>
            </div>
            <div class="user--info">
                <?php if ($unreadCount > 0): ?>
                    <a href="<?= ROOT ?>/artistdashboard/mark_all_notifications_read" class="btn btn-secondary" style="font-size: 12px; padding: 8px 16px;">
                        <i class="fas fa-check-double"></i> Mark All Read
                    </a>
                <?php endif; ?>
                <img src="<?= esc($profileImageSrc) ?>" alt="Artist Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
            <div class="stat-card">
                <h3><?= count($allNotifications) ?></h3>
                <p>Total Notifications</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745, #1e7e34);">
                <h3><?= $unreadCount ?></h3>
                <p>Unread</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #6c757d, #545b62);">
                <h3><?= count($grouped) ?></h3>
                <p>Dramas</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="switchTab('all', this)">
                <i class="fas fa-bell"></i> All Notifications
            </button>
            <button class="tab-btn" onclick="switchTab('grouped', this)">
                <i class="fas fa-film"></i> By Drama
            </button>
            <button class="tab-btn" onclick="switchTab('unread', this)">
                <i class="fas fa-circle" style="font-size: 8px; color: var(--brand);"></i> Unread Only
            </button>
        </div>

        <!-- Tab: All Notifications -->
        <div id="tab-all" class="tab-panel active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3><i class="fas fa-bell"></i> All Notifications</h3>
                            <?php if (empty($allNotifications)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-bell-slash"></i>
                                    <h3>No Notifications Yet</h3>
                                    <p>You'll receive notifications when directors schedule events, assign roles, or update drama details.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($allNotifications as $n): 
                                    $tc = $typeConfig[$n->type] ?? ['icon' => 'fa-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                    $isUnread = !(int)$n->is_read;
                                    $timeAgo = timeAgoStr($n->created_at);
                                ?>
                                <a href="<?= ROOT ?>/artistdashboard/mark_notification_read?id=<?= (int)$n->id ?>&redirect=<?= urlencode($n->link ?? ROOT . '/artistdashboard/notifications') ?>" 
                                   class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                    <div class="notification-icon" style="background: <?= $tc['color'] ?>;">
                                        <i class="fas <?= $tc['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-body">
                                        <div class="notification-title">
                                            <?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?>
                                            <?= esc($n->title) ?>
                                        </div>
                                        <div class="notification-message"><?= esc($n->message) ?></div>
                                        <div class="notification-time">
                                            <i class="fas fa-clock"></i> <?= $timeAgo ?>
                                            <?php if ($n->drama_name): ?>
                                                &nbsp;|&nbsp; <i class="fas fa-film"></i> <?= esc($n->drama_name) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="flex-shrink: 0;">
                                        <span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;">
                                            <i class="fas <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
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
                                    <i class="fas fa-bell-slash"></i>
                                    <h3>No Notifications Yet</h3>
                                    <p>Notifications will appear here grouped by drama.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($grouped as $dramaKey => $group): ?>
                                <div class="card-section" style="margin-bottom: 16px;">
                                    <div class="drama-group-header">
                                        <h4>
                                            <i class="fas fa-film"></i> <?= esc($group['drama_name']) ?>
                                        </h4>
                                        <?php if ($group['unread_count'] > 0): ?>
                                            <span style="background: rgba(255,255,255,0.25); color: #fff; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;">
                                                <?= $group['unread_count'] ?> new
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <?php foreach ($group['notifications'] as $n): 
                                        $tc = $typeConfig[$n->type] ?? ['icon' => 'fa-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                        $isUnread = !(int)$n->is_read;
                                        $timeAgo = timeAgoStr($n->created_at);
                                    ?>
                                    <a href="<?= ROOT ?>/artistdashboard/mark_notification_read?id=<?= (int)$n->id ?>&redirect=<?= urlencode($n->link ?? ROOT . '/artistdashboard/notifications') ?>" 
                                       class="notification-item <?= $isUnread ? 'unread' : '' ?>">
                                        <div class="notification-icon" style="background: <?= $tc['color'] ?>;">
                                            <i class="fas <?= $tc['icon'] ?>"></i>
                                        </div>
                                        <div class="notification-body">
                                            <div class="notification-title">
                                                <?php if ($isUnread): ?><span class="unread-dot"></span><?php endif; ?>
                                                <?= esc($n->title) ?>
                                            </div>
                                            <div class="notification-message"><?= esc($n->message) ?></div>
                                            <div class="notification-time">
                                                <i class="fas fa-clock"></i> <?= $timeAgo ?>
                                            </div>
                                        </div>
                                        <div style="flex-shrink: 0;">
                                            <span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;">
                                                <i class="fas <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
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
                            <h3><i class="fas fa-circle" style="font-size: 10px; color: var(--brand);"></i> Unread Notifications</h3>
                            <?php 
                            $unreadNotifications = array_filter($allNotifications, function($n) { return !(int)$n->is_read; });
                            ?>
                            <?php if (empty($unreadNotifications)): ?>
                                <div class="empty-state">
                                    <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                    <h3>All Caught Up!</h3>
                                    <p>You have no unread notifications.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($unreadNotifications as $n): 
                                    $tc = $typeConfig[$n->type] ?? ['icon' => 'fa-bell', 'color' => '#6c757d', 'label' => 'Notification'];
                                    $timeAgo = timeAgoStr($n->created_at);
                                ?>
                                <a href="<?= ROOT ?>/artistdashboard/mark_notification_read?id=<?= (int)$n->id ?>&redirect=<?= urlencode($n->link ?? ROOT . '/artistdashboard/notifications') ?>" 
                                   class="notification-item unread">
                                    <div class="notification-icon" style="background: <?= $tc['color'] ?>;">
                                        <i class="fas <?= $tc['icon'] ?>"></i>
                                    </div>
                                    <div class="notification-body">
                                        <div class="notification-title">
                                            <span class="unread-dot"></span> <?= esc($n->title) ?>
                                        </div>
                                        <div class="notification-message"><?= esc($n->message) ?></div>
                                        <div class="notification-time">
                                            <i class="fas fa-clock"></i> <?= $timeAgo ?>
                                            <?php if ($n->drama_name): ?>
                                                &nbsp;|&nbsp; <i class="fas fa-film"></i> <?= esc($n->drama_name) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="flex-shrink: 0;">
                                        <span class="notification-badge" style="background: <?= $tc['color'] ?>20; color: <?= $tc['color'] ?>;">
                                            <i class="fas <?= $tc['icon'] ?>"></i> <?= $tc['label'] ?>
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
        // Hide all panels
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        
        // Show selected
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }
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
