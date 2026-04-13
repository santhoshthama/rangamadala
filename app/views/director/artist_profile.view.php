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

$nicDownload = !empty($artist->nic_photo)
    ? ROOT . '/' . ltrim(str_replace('\\', '/', $artist->nic_photo), '/')
    : '';

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
    <title>View Profile - <?= esc($artistName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .profile-preview {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
            padding: 16px;
            border-radius: 14px;
            background: rgba(186, 142, 35, 0.08);
            border: 1px solid rgba(186, 142, 35, 0.22);
        }

        .profile-preview img {
            width: 88px;
            height: 88px;
            border-radius: 16px;
            object-fit: cover;
            border: 2px solid rgba(186, 142, 35, 0.2);
        }

        .profile-preview h3 {
            margin: 0 0 6px;
            color: var(--ink);
            font-size: 24px;
        }

        .profile-preview p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .form-input[readonly],
        textarea.form-input[readonly] {
            background: #f8fafc;
            color: #334155;
            cursor: default;
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
        <a class="back-button" href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?><?= $roleId ? '&role_id=' . esc($roleId) : '' ?>"><i class="bx bx-arrow-left"></i>Back to Artist Search</a>

        <div class="header--wrapper" style="margin-bottom: 20px;">
            <div class="header--title">
                <span><?= esc($drama->drama_name ?? 'Drama') ?></span>
                <h2>View Profile</h2>
            </div>
            <div class="user--info">
                <div class="role-badge"><i class="bx bx-video"></i> Director</div>
                <img src="<?= esc($directorImageSrc) ?>" alt="Director Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                    <i class="bx bx-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <div class="card-section">
                        <h3><span><i class="bx bx-info-circle"></i> Profile Overview</span></h3>

                        <div class="profile-preview">
                            <img src="<?= esc($artistImageSrc ?: (ROOT . '/assets/images/default-avatar.jpg')) ?>" alt="<?= esc($artistName) ?>" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                            <div>
                                <h3><?= esc($artistName) ?></h3>
                                <p>
                                    <?= !empty($artist->years_experience) ? esc($artist->years_experience) . ' years experience' : 'Experience not added yet' ?>
                                    <?php if (!empty($artist->location)): ?> · <?= esc($artist->location) ?><?php endif; ?>
                                </p>
                            </div>
                        </div>

                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-theater-masks"></i> Drama</span>
                                <span class="service-info-value"><?= esc($drama->drama_name ?? 'N/A') ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-user-pin"></i> Requested Role</span>
                                <span class="service-info-value"><?= esc($roleName ?? 'N/A') ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-tag"></i> Role Type</span>
                                <span class="service-info-value"><?= esc(isset($role->role_type) ? ucfirst($role->role_type) : 'N/A') ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-user-check"></i> Availability</span>
                                <span class="service-info-value">Visible in artist search</span>
                            </div>
                        </div>
                    </div>

                    <div class="card-section">
                        <h3><span><i class="bx bx-id-card"></i> Profile Details</span></h3>

                        <div class="form-container">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Full Name</label>
                                    <input type="text" class="form-input" value="<?= esc($artist->full_name ?? '') ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Email Address</label>
                                    <input type="email" class="form-input" value="<?= esc($artist->email ?? '') ?>" readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Mobile Number</label>
                                    <input type="text" class="form-input" value="<?= esc($artist->phone ?? '') ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Years of Experience</label>
                                    <input type="text" class="form-input" value="<?= esc(isset($artist->years_experience) && $artist->years_experience !== null ? $artist->years_experience : 'Not added') ?>" readonly>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Location</label>
                                    <input type="text" class="form-input" value="<?= esc($artist->location ?? 'Not added') ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label>Website / Portfolio</label>
                                    <input type="text" class="form-input" value="<?= esc($artist->website ?? 'Not added') ?>" readonly>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Bio / About Artist</label>
                                <textarea class="form-input" rows="6" readonly><?= esc($artist->bio ?? 'No bio provided') ?></textarea>
                            </div>

                            <div style="display: flex; gap: 12px; margin-top: 20px; flex-wrap: wrap;">
                                <a href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?><?= $roleId ? '&role_id=' . esc($roleId) : '' ?>" class="btn btn-secondary">
                                    <i class="bx bx-arrow-left"></i> Back to Artist Search
                                </a>

                                <?php if ($nicDownload): ?>
                                    <a href="<?= esc($nicDownload) ?>" target="_blank" rel="noopener" class="btn btn-primary">
                                        <i class="bx bx-id-card"></i> View NIC Upload
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
