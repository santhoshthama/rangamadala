<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
}

// Get current user profile image
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($drama->drama_name ?? 'Drama Details') ?> - Rangamadala</title>
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
                <a href="<?=ROOT?>/artistdashboard">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="#">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistdashboard/notifications">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/profile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?=ROOT?>/artistdashboard" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Drama Details</span>
                <h2><?= esc($drama->drama_name ?? 'Unknown Drama') ?></h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    <?= !empty($drama->description) ? esc($drama->description) : 'No description provided yet.' ?>
                </p>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="fas fa-user-tie"></i> Actor
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="Artist Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- My Role Card (if assigned) -->
        <?php if (isset($my_role) && $my_role): ?>
        <div class="card-section" style="background: linear-gradient(135deg, #ba8e23, #a0781e); color: #fff; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <h2 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 700; color: #fff;">
                        <i class="fas fa-star"></i> Your Role: <?= esc($my_role->role_name) ?>
                    </h2>
                    <p style="margin: 0; font-size: 16px; line-height: 1.5; color: #fff;">
                        <strong>Type:</strong> <?= esc(ucfirst($my_role->role_type)) ?>
                        <?php if (!empty($my_role->salary)): ?>
                        | <strong>Salary:</strong> LKR <?= number_format($my_role->salary) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($my_role->role_description)): ?>
                    <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 14px; color: #fff;">
                        <?= esc($my_role->role_description) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Content Section -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Drama Information -->
                    <div class="card-section">
                        <h3><i class="fas fa-film"></i> Drama Information</h3>
                        
                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label">Drama Name</span>
                                <span class="service-info-value"><?= esc($drama->drama_name) ?></span>
                            </div>

                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Number</span>
                                <span class="service-info-value"><?= esc($drama->certificate_number ?? 'N/A') ?></span>
                            </div>

                            <div class="service-info-item">
                                <span class="service-info-label">Drama Owner</span>
                                <span class="service-info-value"><?= esc($drama->owner_name ?? 'N/A') ?></span>
                            </div>

                            <div class="service-info-item">
                                <span class="service-info-label">Created On</span>
                                <span class="service-info-value"><?= isset($drama->created_at) ? date('M d, Y', strtotime($drama->created_at)) : 'N/A' ?></span>
                            </div>

                            <?php if (!empty($drama->description)): ?>
                            <div class="service-info-item" style="flex-direction: column; align-items: flex-start; gap: 6px;">
                                <span class="service-info-label">Description</span>
                                <span class="service-info-value" style="white-space: pre-wrap;"><?= esc($drama->description) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($drama->certificate_image)): ?>
                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Document</span>
                                <span class="service-info-value">
                                    <a href="<?= ROOT ?>/uploads/certificates/<?= esc(rawurlencode($drama->certificate_image)) ?>" 
                                       target="_blank" 
                                       rel="noopener">
                                        View certificate
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- All Roles in Drama -->
                    <div class="card-section" style="margin-top: 30px;">
                        <h3>
                            <span><i class="fas fa-users"></i> All Roles in This Drama</span>
                        </h3>
                        
                        <?php if (empty($roles)): ?>
                            <div class="no-results">
                                <i class="fas fa-user-times"></i>
                                <h3>No Roles Added Yet</h3>
                                <p>The director hasn't added any roles to this drama yet.</p>
                            </div>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($roles as $role): ?>
                                    <li>
                                        <div>
                                            <strong style="color: <?= isset($my_role) && $my_role->id == $role->id ? 'var(--brand)' : 'var(--ink)' ?>;">
                                                <?= esc($role->role_name) ?>
                                                <?php if (isset($my_role) && $my_role->id == $role->id): ?>
                                                    <i class="fas fa-star" style="margin-left: 8px; color: var(--brand);"></i>
                                                <?php endif; ?>
                                            </strong>
                                            <div class="request-info">
                                                Type: <?= esc(ucfirst($role->role_type)) ?>
                                                <?php if (!empty($role->salary)): ?>
                                                    | Salary: LKR <?= number_format($role->salary) ?>
                                                <?php endif; ?>
                                                <?php if (!empty($role->role_description)): ?>
                                                    <br><?= esc($role->role_description) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-end;">
                                            <?php if ($role->is_filled): ?>
                                                <span class="status-badge assigned">
                                                    <i class="fas fa-user-check"></i> Filled
                                                </span>
                                                <?php if (!empty($role->assigned_artist_name)): ?>
                                                    <span style="font-size: 12px; color: var(--muted);">
                                                        <?php if (isset($my_role) && $my_role->id == $role->id): ?>
                                                            <strong style="color: var(--brand);">You</strong>
                                                        <?php else: ?>
                                                            <?= esc($role->assigned_artist_name) ?>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="status-badge pending">
                                                    <i class="fas fa-hourglass-half"></i> Vacant
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <!-- Schedule/Rehearsal Information -->
                    <div class="card-section" style="margin-top: 30px;">
                        <h3>
                            <span><i class="fas fa-calendar-alt"></i> Upcoming Schedule & Rehearsals</span>
                        </h3>

                        <?php
                        // Prepare variables
                        $schedules = isset($schedules) ? $schedules : [];
                        $past_schedules = isset($past_schedules) ? $past_schedules : [];
                        $my_interviews = isset($my_interviews) ? $my_interviews : [];
                        $schedule_stats = isset($schedule_stats) ? $schedule_stats : null;
                        ?>

                        <?php if ($schedule_stats && (int)$schedule_stats->total_upcoming > 0): ?>
                        <!-- Mini Stats -->
                        <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 18px;">
                            <?php if ((int)$schedule_stats->upcoming_rehearsals > 0): ?>
                            <div style="background: linear-gradient(135deg, #007bff, #0056b3); color: #fff; padding: 10px 18px; border-radius: 8px; text-align: center; min-width: 90px;">
                                <strong style="font-size: 20px;"><?= (int)$schedule_stats->upcoming_rehearsals ?></strong>
                                <div style="font-size: 11px; opacity: 0.9;">Rehearsals</div>
                            </div>
                            <?php endif; ?>
                            <?php if ((int)$schedule_stats->upcoming_performances > 0): ?>
                            <div style="background: linear-gradient(135deg, #ba8e23, #a0781e); color: #fff; padding: 10px 18px; border-radius: 8px; text-align: center; min-width: 90px;">
                                <strong style="font-size: 20px;"><?= (int)$schedule_stats->upcoming_performances ?></strong>
                                <div style="font-size: 11px; opacity: 0.9;">Performances</div>
                            </div>
                            <?php endif; ?>
                            <?php if ((int)$schedule_stats->upcoming_meetings > 0): ?>
                            <div style="background: linear-gradient(135deg, #ffc107, #d39e00); color: #fff; padding: 10px 18px; border-radius: 8px; text-align: center; min-width: 90px;">
                                <strong style="font-size: 20px;"><?= (int)$schedule_stats->upcoming_meetings ?></strong>
                                <div style="font-size: 11px; opacity: 0.9;">Meetings</div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                        <?php if (empty($schedules) && empty($my_interviews)): ?>
                            <div class="view-only-notice" style="margin-top: 15px;">
                                <i class="fas fa-info-circle"></i>
                                No upcoming rehearsals or events scheduled yet.
                            </div>
                        <?php else: ?>

                            <!-- Upcoming Interview (if any) -->
                            <?php if (!empty($my_interviews)): ?>
                            <h4 style="margin: 16px 0 10px; color: var(--brand); font-size: 15px;">
                                <i class="fas fa-user-check"></i> Your Interviews
                            </h4>
                            <ul>
                                <?php foreach ($my_interviews as $interview): ?>
                                <li>
                                    <div>
                                        <strong style="color: #28a745;">
                                            <i class="fas fa-user-check"></i> 
                                            Interview for: <?= esc($interview->role_name) ?>
                                        </strong>
                                        <div class="request-info">
                                            <i class="fas fa-clock"></i> 
                                            <?= date('M d, Y \a\t h:i A', strtotime($interview->interview_at)) ?>
                                            <?php if (!empty($interview->interview_notes)): ?>
                                                <br><i class="fas fa-sticky-note"></i> <?= esc($interview->interview_notes) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="status-badge <?= $interview->interview_status === 'confirmed' ? 'assigned' : 'pending' ?>">
                                            <?= esc(ucfirst($interview->interview_status ?? 'Pending')) ?>
                                        </span>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>

                            <!-- Upcoming Rehearsals / Performances / Meetings -->
                            <?php if (!empty($schedules)): ?>
                            <h4 style="margin: 16px 0 10px; color: var(--ink); font-size: 15px;">
                                <i class="fas fa-calendar-day"></i> Upcoming Events
                            </h4>
                            <ul>
                                <?php foreach ($schedules as $schedule): ?>
                                <li>
                                    <div style="flex: 1;">
                                        <strong>
                                            <?php
                                            // Event type icon
                                            $typeIcons = [
                                                'rehearsal' => 'fa-theater-masks',
                                                'performance' => 'fa-star',
                                                'meeting' => 'fa-users',
                                            ];
                                            $icon = $typeIcons[$schedule->event_type] ?? 'fa-calendar';
                                            ?>
                                            <i class="fas <?= $icon ?>"></i> 
                                            <?= esc($schedule->event_title) ?>
                                        </strong>
                                        <div class="request-info">
                                            <i class="fas fa-calendar"></i> 
                                            <?= date('M d, Y (l)', strtotime($schedule->scheduled_date)) ?>
                                            &nbsp;|&nbsp;
                                            <i class="fas fa-clock"></i> 
                                            <?= date('h:i A', strtotime($schedule->start_time)) ?> 
                                            - <?= date('h:i A', strtotime($schedule->end_time)) ?>
                                            <br>
                                            <i class="fas fa-map-marker-alt"></i> 
                                            <strong>Venue:</strong> <?= esc($schedule->venue) ?>
                                            <?php if (!empty($schedule->event_description)): ?>
                                                <br><i class="fas fa-info-circle"></i> <?= esc($schedule->event_description) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($schedule->notes)): ?>
                                                <br><i class="fas fa-sticky-note"></i> <?= esc($schedule->notes) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($schedule->role_name)): ?>
                                                <br><i class="fas fa-user-tag"></i> Role: <?= esc($schedule->role_name) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                                        <?php
                                        $typeBadgeColors = [
                                            'rehearsal' => 'background: #007bff; color: #fff;',
                                            'performance' => 'background: #ba8e23; color: #fff;',
                                            'meeting' => 'background: #ffc107; color: #333;',
                                        ];
                                        $statusBadge = [
                                            'scheduled' => 'pending',
                                            'confirmed' => 'assigned',
                                            'completed' => '',
                                        ];
                                        ?>
                                        <span class="status-badge" style="<?= $typeBadgeColors[$schedule->event_type] ?? '' ?>; font-size: 11px; padding: 3px 10px; border-radius: 12px;">
                                            <?= esc(ucfirst($schedule->event_type)) ?>
                                        </span>
                                        <span class="status-badge <?= $statusBadge[$schedule->status] ?? 'pending' ?>" style="font-size: 11px;">
                                            <?= esc(ucfirst($schedule->status)) ?>
                                        </span>
                                        <a href="<?= ROOT ?>/artistdashboard/event_detail?event_id=<?= (int)$schedule->id ?>&drama_id=<?= (int)$drama->id ?>" 
                                           class="btn btn-primary" 
                                           style="font-size: 13px; padding: 8px 18px; border-radius: 8px; text-decoration: none; margin-top: 4px; font-weight: 700; letter-spacing: 0.3px;">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Past Events History -->
                    <?php if (!empty($past_schedules)): ?>
                    <div class="card-section" style="margin-top: 30px;">
                        <h3>
                            <span><i class="fas fa-history"></i> Past Events</span>
                        </h3>
                        <ul>
                            <?php foreach ($past_schedules as $pastEvt): ?>
                            <li style="opacity: 0.75;">
                                <div style="flex: 1;">
                                    <strong>
                                        <?php
                                        $pIcon = [
                                            'rehearsal' => 'fa-theater-masks',
                                            'performance' => 'fa-star',
                                            'meeting' => 'fa-users',
                                        ];
                                        ?>
                                        <i class="fas <?= $pIcon[$pastEvt->event_type] ?? 'fa-calendar' ?>"></i>
                                        <?= esc($pastEvt->event_title) ?>
                                    </strong>
                                    <div class="request-info">
                                        <i class="fas fa-calendar"></i> 
                                        <?= date('M d, Y', strtotime($pastEvt->scheduled_date)) ?>
                                        &nbsp;|&nbsp;
                                        <i class="fas fa-clock"></i> 
                                        <?= date('h:i A', strtotime($pastEvt->start_time)) ?> 
                                        - <?= date('h:i A', strtotime($pastEvt->end_time)) ?>
                                        <br>
                                        <i class="fas fa-map-marker-alt"></i> <?= esc($pastEvt->venue) ?>
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-end;">
                                    <?php
                                    $pastStatusStyle = '';
                                    if ($pastEvt->status === 'completed') $pastStatusStyle = 'background: #6c757d; color: #fff;';
                                    if ($pastEvt->status === 'cancelled') $pastStatusStyle = 'background: #dc3545; color: #fff;';
                                    ?>
                                    <span class="status-badge" style="<?= $pastStatusStyle ?>; font-size: 11px; padding: 3px 10px; border-radius: 12px;">
                                        <?= esc(ucfirst($pastEvt->status)) ?>
                                    </span>
                                    <a href="<?= ROOT ?>/artistdashboard/event_detail?event_id=<?= (int)$pastEvt->id ?>&drama_id=<?= (int)$drama->id ?>" 
                                       class="btn btn-secondary" 
                                       style="font-size: 13px; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 700; letter-spacing: 0.3px;">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
