<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Audience Profile</title>

    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Audience_profile.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

<div class="back-container">
    <a href="<?= ROOT ?>/Audiencedashboard" class="back-link">
        <button class="back-btn">
            <i class='bx bx-arrow-back'></i> Back
        </button>
    </a>
</div>

<div class="wrapper">

    <!-- Profile Image -->
    <div class="profile-pic">
        <!-- Debug: <?= var_export($data['profile_image'], true) ?> -->
        <?php if (!empty($data['profile_image'])): ?>
            <img src="<?= ROOT ?>/uploads/profile_images/<?= htmlspecialchars($data['profile_image']) ?>" alt="Profile" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, #d4af37, #aa8c2c); display: none; align-items: center; justify-content: center; font-size: 48px; color: #1a1410; font-weight: bold; margin: 0 auto;">
                <?= strtoupper(substr($data['profile']->full_name ?? 'U', 0, 1)) ?>
            </div>
        <?php else: ?>
            <div style="width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg, #d4af37, #aa8c2c); display: flex; align-items: center; justify-content: center; font-size: 48px; color: #1a1410; font-weight: bold; margin: 0 auto;">
                <?= strtoupper(substr($data['profile']->full_name ?? 'U', 0, 1)) ?>
            </div>
        <?php endif; ?>
        <h2 class="name"><?= htmlspecialchars($data['profile']->full_name ?? 'N/A') ?></h2>
        <p class="email"><?= htmlspecialchars($data['profile']->email ?? 'N/A') ?></p>
    </div>

    <!-- Profile Info Card -->
    <div class="info-card">
        <h3>Personal Details</h3>
        <p><i class='bx bxs-user'></i> Full Name: <span><?= htmlspecialchars($data['signup_details']->full_name ?? 'N/A') ?></span></p>
        <p><i class='bx bxs-phone'></i> Phone: <span><?= htmlspecialchars($data['signup_details']->phone ?? 'N/A') ?></span></p>
        <p><i class='bx bxs-envelope'></i> Email: <span><?= htmlspecialchars($data['signup_details']->email ?? 'N/A') ?></span></p>
        <p><i class='bx bx-edit'></i> Bio: <span><?= htmlspecialchars($data['bio'] ?? 'No bio added yet.') ?></span></p>
    </div>

    <div class="info-card">
        <h3>Social Profiles</h3>
        <p><i class='bx bxl-facebook-circle'></i> Facebook: 
            <a href="#">fb.com/nuwanmusic</a>
        </p>

        <p><i class='bx bxl-instagram-alt'></i> Instagram:
            <a href="#">instagram.com/nuwan_ig</a>
        </p>

        <p><i class='bx bxl-youtube'></i> YouTube:
            <a href="#">youtube.com/@nuwan</a>
        </p>
    </div>

    <a href="<?= ROOT ?>/AudienceProfileEdit" class="edit-btn">Edit Profile</a>

</div>

</body>
</html>
