<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$application = $application ?? null;
$artist = $artist ?? null;
$drama = $drama ?? null;

if (!$application || !$artist || !$drama) {
    echo '<p>Application details unavailable.</p>';
    return;
}

$dramaId = (int)($drama->id ?? 0);
$roleId = (int)($application->role_id ?? 0);
$dramaName = $drama->drama_name ?? 'Drama';
$profileReviewedAt = !empty($application->profile_viewed_at) ? date('M d, Y \a\t H:i', strtotime($application->profile_viewed_at)) : null;
$interviewStatus = strtolower($application->interview_status ?? 'pending');
$interviewAtValue = !empty($application->interview_at) ? date('Y-m-d\TH:i', strtotime($application->interview_at)) : '';
$interviewReadable = !empty($application->interview_at) ? date('M d, Y \a\t H:i', strtotime($application->interview_at)) : null;
$applicationStatus = strtolower($application->status ?? 'pending');
$confirmationStatus = strtolower($application->interview_confirmation_status ?? 'pending');
$confirmationTimestamp = !empty($application->interview_confirmed_at) ? date('M d, Y \a\t H:i', strtotime($application->interview_confirmed_at)) : null;
$confirmationColor = $confirmationStatus === 'confirmed' ? '#256029' : '#a3202c';
$confirmationBackground = $confirmationStatus === 'confirmed' ? 'rgba(76,175,80,.12)' : 'rgba(244,67,54,.15)';
$pendingDecision = $applicationStatus === 'pending';
$nameSource = $artist->full_name ?? 'A';
$artistInitial = strtoupper((function_exists('mb_substr') ? mb_substr($nameSource, 0, 1) : substr($nameSource, 0, 1)) ?: 'A');
$profileImage = !empty($artist->profile_image) ? ROOT . '/uploads/profile_images/' . $artist->profile_image : null;

// Get current user profile image
$userModel = new M_universal_profile();
$currentUser = $userModel->getUserById($_SESSION['user_id']);
$directorImageSrc = ROOT . '/assets/images/default-avatar.jpg';
if ($currentUser && !empty($currentUser->profile_image)) {
    $imageValue = str_replace('\\', '/', $currentUser->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $directorImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $directorImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif ($currentUser && !empty($currentUser->nic_photo)) {
    $directorImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $currentUser->nic_photo), '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Review - <?= esc($artist->full_name ?? 'Artist') ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
        .page-grid { display: grid; grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.2fr); gap: 20px; }
        .card { background: #fff; border-radius: 18px; padding: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-sm, 0 8px 24px rgba(15, 23, 42, .08)); }
        .artist-header { display: flex; gap: 20px; align-items: center; }
        .avatar-shell { width: 96px; height: 96px; border-radius: 20px; overflow: hidden; background: linear-gradient(135deg, #ffe0b5, #ffc778); display: flex; align-items: center; justify-content: center; font-size: 34px; font-weight: 700; color: #8a5200; }
        .avatar-shell img { width: 100%; height: 100%; object-fit: cover; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 14px; margin-top: 18px; }
        .info-chip { padding: 14px; border: 1px solid var(--border); border-radius: 12px; background: #faf7f1; font-size: 13px; }
        .info-chip span { display: block; font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; }
        .status-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .status-chip.ready { background: rgba(76,175,80,.12); color: #256029; }
        .status-chip.pending { background: rgba(255,193,7,.18); color: #7a4f02; }
        .status-chip.alert { background: rgba(244,67,54,.15); color: #a52714; }
        .timeline { display: flex; flex-direction: column; gap: 10px; margin-top: 16px; }
        .timeline-row { display: flex; gap: 12px; align-items: flex-start; font-size: 13px; }
        .timeline-row i { width: 24px; height: 24px; border-radius: 50%; background: #f4e1c1; color: #8a5200; display: flex; align-items: center; justify-content: center; }
        .schedule-form { display: flex; flex-direction: column; gap: 14px; }
        .schedule-form input, .schedule-form textarea { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid var(--border); font-size: 14px; }
        .schedule-form label { font-weight: 600; font-size: 13px; color: var(--muted); }
        .muted { color: var(--muted); font-size: 13px; }
        .pill-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .pill { padding: 6px 14px; border-radius: 999px; border: 1px solid var(--border); font-size: 12px; text-transform: uppercase; letter-spacing: .04em; }
        .actions-inline { display: flex; gap: 10px; flex-wrap: wrap; }
        .note-block { background: #fff7ec; border: 1px solid #ffe1ba; border-radius: 12px; padding: 14px; font-size: 13px; color: #8a5200; }
        @media (max-width: 1080px) {
            .page-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo"><h2>🎭</h2></div>
        <ul class="menu">
            <li><a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>"><i class="bx bx-home"></i><span>Dashboard</span></a></li>
            <li><a href="<?= ROOT ?>/director/drama_details?drama_id=<?= esc($dramaId) ?>"><i class="bx bx-film"></i><span>Drama Details</span></a></li>
            <li class="active"><a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="bx bx-users"></i><span>Artist Roles</span></a></li>
            <li><a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= esc($dramaId) ?>"><i class="bx bx-user-tie"></i><span>Production Manager</span></a></li>
            <li><a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= esc($dramaId) ?>"><i class="bx bx-calendar-alt"></i><span>Schedule</span></a></li>
            <li><a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= esc($dramaId) ?>"><i class="bx bx-dollar-sign"></i><span>Services & Budget</span></a></li>
            <li><a href="<?= ROOT ?>/artistdashboard"><i class="bx bx-arrow-left"></i><span>Back to Profile</span></a></li>
        </ul>
    </aside>

    <main class="main--content">
        <a class="back-button" href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="bx bx-arrow-left"></i>Back to Manage Roles</a>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="card" style="border-left: 4px solid var(--brand); background: rgba(186,142,35,0.08); color: var(--ink);">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <header style="margin-bottom: 24px;">
            <span class="muted"><?= esc($dramaName) ?> · <?= esc($application->role_name ?? 'Role') ?></span>
            <h1 style="margin: 6px 0 4px;">Application Review</h1>
            <p class="muted" style="max-width: 520px;">Review the artist profile, capture interview intent, and keep an audit trail before making a decision.</p>
        </header>

        <div class="header--wrapper" style="margin-bottom: 20px;">
            <div style="flex: 1;"></div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-video"></i> Director
                </div>
                <img src="<?= esc($directorImageSrc) ?>" alt="Director Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                    <i class="bx bx-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="page-grid">
            <section class="card">
                <div class="artist-header">
                    <div class="avatar-shell">
                        <?php if ($profileImage): ?>
                            <img src="<?= esc($profileImage) ?>" alt="<?= esc($artist->full_name ?? 'Artist') ?> profile">
                        <?php else: ?>
                            <?= esc($artistInitial) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 style="margin: 0 0 6px;"><?= esc($artist->full_name ?? 'Artist') ?></h2>
                        <div class="pill-row">
                            <?php if (!empty($artist->years_experience)): ?><span class="pill"><i class="bx bx-star"></i> <?= esc($artist->years_experience) ?> yrs exp</span><?php endif; ?>
                            <?php if (!empty($artist->location)): ?><span class="pill"><i class="bx bx-map-marker-alt"></i> <?= esc($artist->location) ?></span><?php endif; ?>
                            <?php if (!empty($artist->website)): ?><span class="pill"><i class="bx bx-link"></i> Links Added</span><?php endif; ?>
                        </div>
                        <div class="muted" style="margin-top: 8px;">Profile review updated <?= $profileReviewedAt ? esc($profileReviewedAt) : 'just now' ?></div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-chip"><span>Email</span><?= esc($artist->email ?? 'Not provided') ?></div>
                    <div class="info-chip"><span>Phone</span><?= esc($artist->phone ?? 'Not provided') ?></div>
                    <div class="info-chip"><span>Location</span><?= esc($artist->location ?? 'Not provided') ?></div>
                    <div class="info-chip"><span>Links</span>
                        <?php if (!empty($artist->website)): ?>
                            <div style="white-space: pre-wrap;"><?= nl2br(esc($artist->website)) ?></div>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($artist->bio)): ?>
                    <div style="margin-top: 18px;">
                        <strong>Bio</strong>
                        <p style="margin-top: 6px; line-height: 1.6; white-space: pre-wrap;"><?= nl2br(esc($artist->bio)) ?></p>
                    </div>
                <?php endif; ?>
            </section>

            <section class="card">
                <h3 style="margin-top: 0;">Application Timeline</h3>
                <div class="timeline">
                    <div class="timeline-row">
                        <i class="bx bx-paper-plane"></i>
                        <div>
                            <strong>Applied</strong>
                            <div class="muted"><?= esc(date('M d, Y \a\t H:i', strtotime($application->applied_at ?? 'now'))) ?></div>
                        </div>
                    </div>
                    <div class="timeline-row">
                        <i class="bx bx-user-check"></i>
                        <div>
                            <strong>Profile Review</strong>
                            <div class="muted"><?= $profileReviewedAt ? esc($profileReviewedAt) : 'Recorded now' ?></div>
                        </div>
                    </div>
                    <div class="timeline-row">
                        <i class="bx bx-calendar-alt"></i>
                        <div>
                            <strong>Interview</strong>
                            <div class="muted">
                                <?php if ($interviewReadable): ?>
                                    <?= esc($interviewReadable) ?> · <?= esc(strtoupper($interviewStatus)) ?>
                                <?php else: ?>
                                    Not scheduled yet
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="timeline-row">
                        <i class="bx bx-clipboard-check"></i>
                        <div>
                            <strong>Artist Confirmation</strong>
                            <div class="muted">
                                <?php if ($confirmationStatus === 'pending'): ?>
                                    Awaiting artist response
                                <?php else: ?>
                                    <?= $confirmationStatus === 'confirmed' ? 'Attendance confirmed' : 'Interview declined' ?>
                                    <?= $confirmationTimestamp ? ' · ' . esc($confirmationTimestamp) : '' ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($interviewReadable): ?>
                    <div style="margin-top: 18px;">
                        <strong>Artist Interview Response</strong>
                        <?php if ($confirmationStatus === 'pending'): ?>
                            <div class="note-block" style="margin-top: 8px;">
                                Waiting for the artist to confirm if they will join the interview slot.
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 8px; padding: 14px; border-radius: 12px; border: 1px solid <?= $confirmationColor ?>; background: <?= $confirmationBackground ?>;">
                                <div style="font-weight: 600; color: <?= $confirmationColor ?>;">
                                    <i class="bx <?= $confirmationStatus === 'confirmed' ? 'bx-user-check' : 'bx-user-times' ?>"></i>
                                    <?= $confirmationStatus === 'confirmed' ? 'Artist confirmed attendance' : 'Artist declined the interview' ?>
                                </div>
                                <?php if (!empty($application->interview_confirmation_note)): ?>
                                    <p style="margin-top: 8px; color: #3c2f11; white-space: pre-wrap;">"<?= nl2br(esc($application->interview_confirmation_note)) ?>"</p>
                                <?php endif; ?>
                                <div class="muted" style="margin-top: 6px;">
                                    Logged <?= $confirmationTimestamp ? esc($confirmationTimestamp) : 'just now' ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 18px;">
                    <strong>Application Message</strong>
                    <div class="note-block" style="margin-top: 8px;">
                        <?php if (!empty($application->application_message)): ?>
                            <?= nl2br(esc($application->application_message)) ?>
                        <?php else: ?>
                            No additional message provided.
                        <?php endif; ?>
                    </div>
                </div>

                <div style="margin-top: 18px;" class="pill-row">
                    <span class="status-chip <?= $applicationStatus === 'pending' ? 'pending' : 'ready' ?>">
                        <i class="bx bx-info-circle"></i>
                        <?= esc(ucfirst($application->status ?? 'pending')) ?>
                    </span>
                    <span class="status-chip <?= $interviewReadable ? 'ready' : 'pending' ?>">
                        <i class="bx bx-video"></i>
                        <?= $interviewReadable ? 'Interview Set' : 'Interview Needed' ?>
                    </span>
                </div>
            </section>
        </div>

        <section class="card" style="margin-top: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                <div>
                    <h3 style="margin-top: 0;">Schedule Interview</h3>
                    <p class="muted">Lock an interview time to unlock accept/reject controls. Artists automatically see the scheduled slot inside their workspace.</p>
                </div>
                <a class="btn btn-secondary" href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>">
                    <i class="bx bx-mask"></i>View Role
                </a>
            </div>

            <?php if ($pendingDecision): ?>
                <form class="schedule-form" action="<?= ROOT ?>/director/schedule_application_interview?drama_id=<?= esc($dramaId) ?>" method="POST">
                    <input type="hidden" name="application_id" value="<?= esc($application->id) ?>">
                    <input type="hidden" name="redirect_to" value="profile">
                    <div>
                        <label for="interview_at">Interview date & time</label>
                        <input type="datetime-local" id="interview_at" name="interview_at" value="<?= esc($interviewAtValue) ?>" min="<?= esc(date('Y-m-d\TH:i')) ?>" required>
                    </div>
                    <div>
                        <label for="interview_notes">Notes / agenda (optional)</label>
                        <textarea id="interview_notes" name="interview_notes" rows="3" placeholder="Add location, call link, or expectations."><?= esc($application->interview_notes ?? '') ?></textarea>
                    </div>
                    <div class="actions-inline" style="justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary"><i class="bx bx-calendar-check"></i>Save Interview Plan</button>
                    </div>
                </form>
            <?php else: ?>
                <div class="note-block">
                    This application is already <?= esc($application->status ?? 'processed') ?>. Interview updates are locked to preserve the audit trail.
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
