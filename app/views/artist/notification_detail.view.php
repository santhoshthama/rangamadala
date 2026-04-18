<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$notification = $notification ?? null;
$user = $user ?? null;
$backUrl = $back_url ?? (ROOT . '/artistdashboard/notifications');

if (!$notification) {
    echo '<p>Notification not found.</p>';
    return;
}

$typeConfig = [
    'role_assigned' => ['icon' => 'bx-user-check', 'color' => '#28a745', 'label' => 'Role Assigned'],
    'role_removed' => ['icon' => 'bx-user-x', 'color' => '#dc3545', 'label' => 'Role Removed'],
    'role_artist_removed' => ['icon' => 'bx-user-minus', 'color' => '#b45309', 'label' => 'Artist Removed (Director)'],
    'event_scheduled' => ['icon' => 'bx-calendar-plus', 'color' => '#ba8e23', 'label' => 'Event Scheduled'],
    'event_updated' => ['icon' => 'bx-calendar-check', 'color' => '#17a2b8', 'label' => 'Event Updated'],
    'event_cancelled' => ['icon' => 'bx-calendar-x', 'color' => '#dc3545', 'label' => 'Event Cancelled'],
    'application_accepted' => ['icon' => 'bx-check-circle', 'color' => '#28a745', 'label' => 'Application Accepted'],
    'application_rejected' => ['icon' => 'bx-x-circle', 'color' => '#dc3545', 'label' => 'Application Rejected'],
    'interview_scheduled' => ['icon' => 'bx-user-voice', 'color' => '#ba8e23', 'label' => 'Interview Scheduled'],
    'pm_provider_responded_quote' => ['icon' => 'bx-receipt', 'color' => '#2563eb', 'label' => 'Quote Received'],
    'pm_provider_accepted_terms' => ['icon' => 'bx-check-shield', 'color' => '#16a34a', 'label' => 'Terms Accepted'],
    'pm_provider_rejected_terms' => ['icon' => 'bx-x-circle', 'color' => '#dc2626', 'label' => 'Terms Rejected'],
    'pm_provider_marked_completed' => ['icon' => 'bx-flag', 'color' => '#0ea5e9', 'label' => 'Service Completed'],
    'pm_provider_rejected_request' => ['icon' => 'bx-user-x', 'color' => '#b91c1c', 'label' => 'Request Rejected'],
    'pm_provider_confirmed_manual_payment' => ['icon' => 'bx-wallet', 'color' => '#15803d', 'label' => 'Payment Confirmed'],
    'pm_provider_rejected_manual_payment' => ['icon' => 'bx-error-circle', 'color' => '#d97706', 'label' => 'Payment Verification Failed'],
];

$cfg = $typeConfig[$notification->type ?? ''] ?? ['icon' => 'bx-bell', 'color' => '#6b7280', 'label' => 'Notification'];
$profileImageSrc = ROOT . '/uploads/profile_images/user_profile.png';
if (isset($user->profile_image) && !empty($user->profile_image)) {
    $imageValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif (isset($user->nic_photo) && !empty($user->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $user->nic_photo), '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Detail - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .detail-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm, 0 6px 16px rgba(15,23,42,.08)); }
        .detail-top { display: flex; gap: 14px; align-items: center; margin-bottom: 16px; }
        .detail-icon { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 22px; }
        .meta-line { color: var(--muted); font-size: 13px; margin-top: 4px; display: flex; gap: 10px; flex-wrap: wrap; }
        .message-box { margin-top: 16px; padding: 16px; border-radius: 12px; background: #f8fafc; border: 1px solid var(--border); white-space: pre-wrap; line-height: 1.65; }
        .action-row { margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .badge-type { display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
    </style>
</head>
<body>
    <?php
    $artistSidebarActive = 'notifications';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <main class="main--content">
        <a href="<?= esc($backUrl) ?>" class="back-button"><i class="bx bx-arrow-left"></i>Back to Notifications</a>

        <div class="header--wrapper">
            <div class="header--title">
                <span>Notifications</span>
                <h2>Notification Detail</h2>
            </div>
            <div class="user--info">
                <img src="<?= esc($profileImageSrc) ?>" alt="Artist Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout"><i class="bx bx-sign-out-alt"></i></a>
            </div>
        </div>

        <section class="detail-card">
            <div class="detail-top">
                <div class="detail-icon" style="background: <?= esc($cfg['color']) ?>;"><i class="bx <?= esc($cfg['icon']) ?>"></i></div>
                <div>
                    <h2 style="margin: 0 0 6px;"><?= esc($notification->title ?? 'Notification') ?></h2>
                    <span class="badge-type" style="background: <?= esc($cfg['color']) ?>20; color: <?= esc($cfg['color']) ?>;"><i class="bx <?= esc($cfg['icon']) ?>"></i><?= esc($cfg['label']) ?></span>
                    <div class="meta-line">
                        <span><i class="bx bx-time"></i> <?= esc(date('Y-m-d H:i', strtotime($notification->created_at ?? 'now'))) ?></span>
                        <?php if (!empty($notification->drama_name)): ?>
                            <span><i class="bx bx-film"></i> <?= esc($notification->drama_name) ?></span>
                        <?php endif; ?>
                        <span><i class="bx bx-check-circle"></i> Read</span>
                    </div>
                </div>
            </div>

            <div class="message-box"><?= esc($notification->message ?? '') ?></div>

            <div class="action-row">
                <a href="<?= esc($backUrl) ?>" class="btn btn-secondary"><i class="bx bx-arrow-back"></i>Back</a>
                <?php if (!empty($notification->link)): ?>
                    <a href="<?= esc($notification->link) ?>" class="btn btn-primary"><i class="bx bx-link-external"></i>Go to Related Page</a>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
