<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$drama = $drama ?? null;
$artist = $artist ?? null;
$role = $role ?? null;
$roleId = isset($roleId) ? (int)$roleId : (int)($_GET['role_id'] ?? 0);

if (!$drama || !$artist) {
    echo '<p>Artist profile details unavailable.</p>';
    return;
}

$dramaId = (int)($drama->id ?? 0);
$roleName = $role->role_name ?? null;
$artistName = $artist->full_name ?? 'Artist';
$initial = strtoupper((function_exists('mb_substr') ? mb_substr($artistName, 0, 1) : substr($artistName, 0, 1)) ?: 'A');

$artistImageSrc = null;
if (!empty($artist->profile_image)) {
    $imageValue = str_replace('\\', '/', $artist->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $artistImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $artistImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif (!empty($artist->nic_photo)) {
    $artistImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $artist->nic_photo), '/');
}

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
    <title>Profile - <?= esc($artistName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .profile-layout { max-width: 900px; margin: 0 auto; }
        .profile-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm, 0 8px 24px rgba(15,23,42,.08)); }
        .profile-head { display: flex; gap: 20px; align-items: center; margin-bottom: 18px; }
        .avatar-shell { width: 96px; height: 96px; border-radius: 20px; overflow: hidden; background: linear-gradient(135deg, #ffe0b5, #ffc778); display: flex; align-items: center; justify-content: center; font-size: 34px; font-weight: 700; color: #8a5200; }
        .avatar-shell img { width: 100%; height: 100%; object-fit: cover; }
        .chip-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; }
        .chip { border: 1px solid var(--border); border-radius: 999px; padding: 6px 12px; font-size: 12px; background: #faf7f1; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 12px; margin-top: 14px; }
        .info-item { border: 1px solid var(--border); border-radius: 12px; padding: 12px; background: #fffdf9; }
        .info-item small { display: block; color: var(--muted); text-transform: uppercase; letter-spacing: .04em; margin-bottom: 4px; }
        .bio { margin-top: 16px; padding: 14px; border: 1px solid #ffe1ba; border-radius: 12px; background: #fff7ec; white-space: pre-wrap; }
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
        <a class="back-button" href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?><?= $roleId ? '&role_id=' . esc($roleId) : '' ?>"><i class="bx bx-arrow-left"></i>Back to Artist Search</a>

        <div class="header--wrapper" style="margin-bottom: 20px;">
            <div class="header--title">
                <span><?= esc($drama->drama_name ?? 'Drama') ?></span>
                <h2>Profile</h2>
            </div>
            <div class="user--info">
                <div class="role-badge"><i class="bx bx-video"></i> Director</div>
                <img src="<?= esc($directorImageSrc) ?>" alt="Director Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                    <i class="bx bx-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="profile-layout">
            <section class="profile-card">
                <div class="profile-head">
                    <div class="avatar-shell">
                        <?php if ($artistImageSrc): ?>
                            <img src="<?= esc($artistImageSrc) ?>" alt="<?= esc($artistName) ?>" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                        <?php else: ?>
                            <?= esc($initial) ?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h2 style="margin: 0 0 4px;"><?= esc($artistName) ?></h2>
                        <p style="margin: 0 0 8px; color: var(--muted);">Profile Details</p>
                        <div class="chip-row">
                            <?php if (!empty($roleName)): ?><span class="chip">Role: <?= esc($roleName) ?></span><?php endif; ?>
                            <?php if (!empty($artist->years_experience)): ?><span class="chip"><?= esc($artist->years_experience) ?> years experience</span><?php endif; ?>
                            <?php if (!empty($artist->location)): ?><span class="chip"><?= esc($artist->location) ?></span><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item"><small>Email</small><?= esc($artist->email ?? 'Not provided') ?></div>
                    <div class="info-item"><small>Phone</small><?= esc($artist->phone ?? 'Not provided') ?></div>
                    <div class="info-item"><small>Location</small><?= esc($artist->location ?? 'Not provided') ?></div>
                    <div class="info-item"><small>Website</small>
                        <?php if (!empty($artist->website)): ?>
                            <a href="<?= esc($artist->website) ?>" target="_blank" rel="noopener" style="color: var(--brand);">Visit website</a>
                        <?php else: ?>
                            Not provided
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($artist->bio)): ?>
                    <div class="bio">
                        <strong>Bio</strong>
                        <div style="margin-top:8px;"><?= nl2br(esc($artist->bio)) ?></div>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
