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

// Event variables
$event = isset($event) ? $event : null;
$drama = isset($drama) ? $drama : null;
$my_role = isset($my_role) ? $my_role : null;
$dramaId = isset($drama->id) ? $drama->id : ($_GET['drama_id'] ?? '');

// Event type config
$typeConfig = [
    'rehearsal'   => ['icon' => 'fa-theater-masks', 'label' => 'Rehearsal',          'color' => '#ba8e23', 'bg' => 'rgba(186, 142, 35, 0.08)'],
    'performance' => ['icon' => 'fa-star',          'label' => 'Performance',         'color' => '#ba8e23', 'bg' => 'rgba(186, 142, 35, 0.08)'],
    'meeting'     => ['icon' => 'fa-users',         'label' => 'Production Meeting',  'color' => '#ba8e23', 'bg' => 'rgba(186, 142, 35, 0.08)'],
    'interview'   => ['icon' => 'fa-user-check',    'label' => 'Interview',           'color' => '#ba8e23', 'bg' => 'rgba(186, 142, 35, 0.08)'],
];

$statusConfig = [
    'scheduled' => ['label' => 'Scheduled', 'class' => 'pending',    'icon' => 'fa-clock'],
    'confirmed' => ['label' => 'Confirmed', 'class' => 'assigned',   'icon' => 'fa-check-circle'],
    'completed' => ['label' => 'Completed', 'class' => '',           'icon' => 'fa-flag-checkered', 'style' => 'background:#6c757d;color:#fff;'],
    'cancelled' => ['label' => 'Cancelled', 'class' => 'unassigned', 'icon' => 'fa-times-circle'],
];

$evtType   = $event ? ($typeConfig[$event->event_type] ?? $typeConfig['rehearsal']) : $typeConfig['rehearsal'];
$evtStatus = $event ? ($statusConfig[$event->status]   ?? $statusConfig['scheduled']) : $statusConfig['scheduled'];

// Countdown
$isPast = false;
$countdownText = '';
if ($event) {
    $eventDateTime = new DateTime($event->scheduled_date . ' ' . $event->start_time);
    $now = new DateTime();
    if ($eventDateTime > $now) {
        $diff = $now->diff($eventDateTime);
        if ($diff->days == 0) {
            $countdownText = 'Today!';
        } elseif ($diff->days == 1) {
            $countdownText = 'Tomorrow';
        } else {
            $countdownText = $diff->days . ' days away';
        }
    } else {
        $isPast = true;
        $countdownText = 'Event has passed';
    }
}

// Duration
$durationStr = '';
if ($event) {
    $start = new DateTime($event->start_time);
    $end   = new DateTime($event->end_time);
    $duration = $start->diff($end);
    if ($duration->h > 0) $durationStr .= $duration->h . 'h ';
    if ($duration->i > 0) $durationStr .= $duration->i . 'min';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($event->event_title ?? 'Event Details') ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard/view_drama?drama_id=<?= (int)$dramaId ?>">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li class="active">
                <a href="#">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Event Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard/notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/profile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/artistdashboard/view_drama?drama_id=<?= (int)$dramaId ?>" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to <?= esc($drama->drama_name ?? 'Drama') ?>
        </a>

        <?php if (!$event): ?>
            <!-- Event not found -->
            <div class="content" style="padding: 60px 20px; text-align: center;">
                <div class="no-results">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Event Not Found</h3>
                    <p>This event may have been deleted or you don't have access to view it.</p>
                    <a href="<?= ROOT ?>/artistdashboard/view_drama?drama_id=<?= (int)$dramaId ?>" class="btn btn-primary" style="margin-top: 16px;">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                </div>
            </div>
        <?php else: ?>

            <!-- Header (matches other pages) -->
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Event Details</span>
                    <h2><?= esc($event->event_title) ?></h2>
                    <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                        <i class="fas fa-film"></i> <?= esc($drama->drama_name ?? 'Drama') ?>
                        <?php if ($my_role): ?>
                            &nbsp;|&nbsp; <i class="fas fa-star"></i> Your Role: <?= esc($my_role->role_name) ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="user--info">
                    <div class="role-badge">
                        <i class="fas <?= $evtType['icon'] ?>"></i> <?= $evtType['label'] ?>
                    </div>
                    <img src="<?= esc($profileImageSrc) ?>" alt="Artist Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                    <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                    <?= esc($_SESSION['message']) ?>
                </div>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            <?php endif; ?>

            <!-- Status + Countdown Banner -->
            <div class="card-section" style="background: linear-gradient(135deg, <?= $evtType['color'] ?>, <?= $evtType['color'] ?>cc); color: #fff; margin-bottom: 24px; border-left-color: <?= $evtType['color'] ?>;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h2 style="margin: 0 0 6px; font-size: 22px; font-weight: 800; color: #fff;">
                            <i class="fas <?= $evtType['icon'] ?>"></i> <?= esc($event->event_title) ?>
                        </h2>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px;">
                            <span class="status-badge" style="background: rgba(255,255,255,0.25); color: #fff;">
                                <i class="fas <?= $evtType['icon'] ?>"></i> <?= $evtType['label'] ?>
                            </span>
                            <span class="status-badge" style="background: rgba(255,255,255,0.25); color: #fff;">
                                <i class="fas <?= $evtStatus['icon'] ?>"></i> <?= $evtStatus['label'] ?>
                            </span>
                            <?php if (!empty($event->role_name)): ?>
                            <span class="status-badge" style="background: rgba(255,255,255,0.25); color: #fff;">
                                <i class="fas fa-user-tag"></i> <?= esc($event->role_name) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <?php if (!$isPast): ?>
                            <span class="status-badge" style="background: rgba(255,255,255,0.3); color: #fff; font-size: 14px; padding: 8px 18px;">
                                <i class="fas fa-hourglass-half"></i> <?= $countdownText ?>
                            </span>
                        <?php else: ?>
                            <span class="status-badge" style="background: rgba(0,0,0,0.2); color: #fff; font-size: 14px; padding: 8px 18px;">
                                <i class="fas fa-flag-checkered"></i> <?= $countdownText ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Stats Strip -->
            <div class="stats-grid" style="grid-template-columns: repeat(5, 1fr);">
                <div class="stat-card">
                    <h3><?= date('d', strtotime($event->scheduled_date)) ?></h3>
                    <p><?= date('F Y', strtotime($event->scheduled_date)) ?></p>
                </div>
                <div class="stat-card">
                    <h3 style="font-size: 18px;"><?= date('l', strtotime($event->scheduled_date)) ?></h3>
                    <p><?= date('M d, Y', strtotime($event->scheduled_date)) ?></p>
                </div>
                <div class="stat-card">
                    <h3 style="font-size: 18px;"><?= date('h:i A', strtotime($event->start_time)) ?></h3>
                    <p>Start Time</p>
                </div>
                <div class="stat-card">
                    <h3 style="font-size: 18px;"><?= date('h:i A', strtotime($event->end_time)) ?></h3>
                    <p>End Time</p>
                </div>
                <div class="stat-card" style="background: linear-gradient(135deg, #6c757d, #545b62);">
                    <h3 style="font-size: 18px;"><?= trim($durationStr) ?: 'N/A' ?></h3>
                    <p>Duration</p>
                </div>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">

                        <!-- Venue & Time Details -->
                        <div class="card-section">
                            <h3><i class="fas fa-map-marker-alt"></i> Venue & Time</h3>
                            <div class="drama-info">
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-map-marker-alt"></i> Venue</span>
                                    <span class="service-info-value"><?= esc($event->venue) ?></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-calendar-day"></i> Date</span>
                                    <span class="service-info-value"><?= date('l, F d, Y', strtotime($event->scheduled_date)) ?></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-clock"></i> Time</span>
                                    <span class="service-info-value">
                                        <?= date('h:i A', strtotime($event->start_time)) ?> — <?= date('h:i A', strtotime($event->end_time)) ?>
                                    </span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-hourglass-half"></i> Duration</span>
                                    <span class="service-info-value"><?= trim($durationStr) ?: 'N/A' ?></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas <?= $evtType['icon'] ?>"></i> Event Type</span>
                                    <span class="service-info-value"><?= $evtType['label'] ?></span>
                                </div>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas <?= $evtStatus['icon'] ?>"></i> Status</span>
                                    <span class="service-info-value">
                                        <span class="status-badge <?= $evtStatus['class'] ?>" style="<?= $evtStatus['style'] ?? '' ?>">
                                            <?= $evtStatus['label'] ?>
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Drama & Role Info -->
                        <div class="card-section">
                            <h3><i class="fas fa-film"></i> Drama Information</h3>
                            <div class="drama-info">
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-film"></i> Drama</span>
                                    <span class="service-info-value"><?= esc($drama->drama_name ?? 'N/A') ?></span>
                                </div>
                                <?php if ($my_role): ?>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-star"></i> Your Role</span>
                                    <span class="service-info-value"><?= esc($my_role->role_name) ?> (<?= esc(ucfirst($my_role->role_type)) ?>)</span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($event->role_name)): ?>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-user-tag"></i> Related Role</span>
                                    <span class="service-info-value"><?= esc($event->role_name) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($event->creator_name)): ?>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-user-shield"></i> Scheduled By</span>
                                    <span class="service-info-value"><?= esc($event->creator_name) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($event->created_at)): ?>
                                <div class="service-info-item">
                                    <span class="service-info-label"><i class="fas fa-calendar-plus"></i> Created On</span>
                                    <span class="service-info-value"><?= date('M d, Y \a\t h:i A', strtotime($event->created_at)) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Description -->
                        <?php if (!empty($event->event_description)): ?>
                        <div class="card-section">
                            <h3><i class="fas fa-align-left"></i> Description</h3>
                            <p style="color: var(--ink); line-height: 1.7; font-size: 14px; white-space: pre-wrap;">
                                <?= nl2br(esc($event->event_description)) ?>
                            </p>
                        </div>
                        <?php endif; ?>

                        <!-- Director's Notes -->
                        <?php if (!empty($event->notes)): ?>
                        <div class="card-section">
                            <h3><i class="fas fa-sticky-note"></i> Director's Notes</h3>
                            <div class="info-box" style="background: #fff3cd; color: #856404; border-left-color: #ffc107;">
                                <?= nl2br(esc($event->notes)) ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Reminder / Status Notice -->
                        <?php if (!$isPast && $event->status !== 'cancelled'): ?>
                        <div class="card-section" style="background: #d4edda; border-left-color: #28a745;">
                            <h3 style="color: #155724;"><i class="fas fa-bell"></i> Reminder</h3>
                            <p style="color: #155724; font-size: 14px; line-height: 1.6; margin: 0;">
                                <?php if ($event->event_type === 'rehearsal'): ?>
                                    Please ensure you arrive at <strong><?= esc($event->venue) ?></strong> before 
                                    <strong><?= date('h:i A', strtotime($event->start_time)) ?></strong>. 
                                    Review your lines and come prepared for the rehearsal.
                                <?php elseif ($event->event_type === 'performance'): ?>
                                    This is a <strong>live performance</strong>! Please arrive at <strong><?= esc($event->venue) ?></strong> 
                                    well in advance. Ensure costumes, makeup, and props are ready.
                                <?php elseif ($event->event_type === 'meeting'): ?>
                                    Production meeting at <strong><?= esc($event->venue) ?></strong>. 
                                    Please be punctual and bring any materials you've been asked to prepare.
                                <?php else: ?>
                                    Please be at <strong><?= esc($event->venue) ?></strong> on time.
                                <?php endif; ?>
                            </p>
                        </div>
                        <?php elseif ($event->status === 'cancelled'): ?>
                        <div class="card-section" style="background: #f8d7da; border-left-color: #dc3545;">
                            <h3 style="color: #721c24;"><i class="fas fa-times-circle"></i> Event Cancelled</h3>
                            <p style="color: #721c24; font-size: 14px; margin: 0;">
                                The director has cancelled this event. Please check for any rescheduled dates.
                            </p>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="margin-top: 24px; display: flex; gap: 12px;">
                <a href="<?= ROOT ?>/artistdashboard/view_drama?drama_id=<?= (int)$dramaId ?>" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Back to Drama
                </a>
                <a href="<?= ROOT ?>/artistdashboard" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>

        <?php endif; ?>
    </main>
</body>
</html>
