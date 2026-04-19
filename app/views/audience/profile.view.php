<?php
$profile = $data['profile'] ?? null;
$signup = $data['signup_details'] ?? null;
$bio = $data['bio'] ?? null;
$profileImage = $data['profile_image'] ?? null;

$profileImageSrc = ROOT . '/uploads/profile_images/user_profile.png';
if (!empty($profileImage)) {
    $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($profileImage);
}

$displayName = $profile->full_name ?? ($signup->full_name ?? 'Audience');
$displayEmail = $profile->email ?? ($signup->email ?? 'N/A');
$displayPhone = $profile->phone ?? ($signup->phone ?? 'N/A');
$displayLocation = !empty($profile->location) ? (string)$profile->location : '';
$displayBio = !empty($bio) ? (string)$bio : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Audience Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Audience_profile.css">
</head>
<body>
    <div class="page-wrapper">
        <a class="back-link" href="<?= ROOT ?>/Audiencedashboard">
            <i class="bx bx-arrow-left"></i>
            <span>Back to Dashboard</span>
        </a>

        <div class="profile-card">
            <aside class="profile-summary">
                <img src="<?= esc($profileImageSrc) ?>" alt="Audience profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">

                <div>
                    <h2><?= esc($displayName) ?></h2>
                    <p><i class="bx bx-envelope"></i> <?= esc($displayEmail) ?></p>
                    <p><i class="bx bx-phone"></i> <?= esc($displayPhone) ?></p>
                    <?php if ($displayLocation !== ''): ?>
                        <p><i class="bx bx-map-marker-alt"></i> <?= esc($displayLocation) ?></p>
                    <?php endif; ?>

                    <div class="summary-actions">
                        <a href="<?= ROOT ?>/AudienceProfileEdit">
                            <i class="bx bx-edit-alt"></i>
                            Edit Profile
                        </a>
                    </div>
                </div>
            </aside>

            <section class="profile-details">
                <h1>Profile Details</h1>
                <p class="subtitle">Keep your information up to date.</p>

                <div class="grid">
                    <div class="item">
                        <label>Full Name</label>
                        <div class="value-box"><?= esc($displayName) ?></div>
                    </div>

                    <div class="item">
                        <label>Email</label>
                        <div class="value-box"><?= esc($displayEmail) ?></div>
                    </div>

                    <div class="item">
                        <label>Phone</label>
                        <div class="value-box"><?= esc($displayPhone) ?></div>
                    </div>

                    <?php if ($displayBio !== ''): ?>
                        <div class="item full">
                            <label>Bio / About Me</label>
                            <div class="value-box textarea"><?= esc($displayBio) ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
