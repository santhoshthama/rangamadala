<?php
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
    <title>Apply for Role - <?= htmlspecialchars($data['role']->role_name ?? 'Role') ?></title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2><i class='bx bxs-theater'></i></h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?=ROOT?>/artistdashboard">
                    <i class="bx bx-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies">
                    <i class="bx bx-bullhorn"></i>
                    <span>View All Vacancies</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistdashboard/notifications">
                    <i class="bx bx-bell"></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistdashboard/classes">
                    <i class='bx bxs-graduation'></i>
                    <span>Classes</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistdashboard#my-showings">
                    <i class='bx bx-calendar-event'></i>
                    <span>Showings</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?=ROOT?>/artistdashboard/browse_vacancies" class="back-button">
            <i class="bx bx-arrow-left"></i>
            Back to Vacancies
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Role Application</span>
                <h2><?= htmlspecialchars($data['role']->role_name ?? 'Unknown Role') ?></h2>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-star"></i> Artist
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                    <i class="bx bx-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <?php if (!empty($data['errors'])): ?>
            <?php foreach ($data['errors'] as $error): ?>
                <div class="info-box" style="background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545;">
                    <i class="bx bx-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Role Information Card -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <div class="card-section">
                        <h3><span><i class="bx bx-info-circle"></i> Role Details</span></h3>
                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-theater-masks"></i> Drama</span>
                                <span class="service-info-value"><?= htmlspecialchars($data['role']->drama_name ?? 'N/A') ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-tag"></i> Role Type</span>
                                <span class="service-info-value"><?= ucfirst(htmlspecialchars($data['role']->role_type ?? 'N/A')) ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-money-bill-wave"></i> Salary</span>
                                <span class="service-info-value">LKR <?= isset($data['role']->salary) && $data['role']->salary ? number_format($data['role']->salary) : 'Negotiable' ?></span>
                            </div>
                            <div class="service-info-item">
                                <span class="service-info-label"><i class="bx bx-users"></i> Openings</span>
                                <span class="service-info-value"><?= $data['role']->positions_available - $data['role']->positions_filled ?> positions</span>
                            </div>
                        </div>
                    </div>

                    <!-- Application Form -->
                    <div class="card-section">
                        <h3><span><i class="bx bx-file-alt"></i> Application Form</span></h3>
                        <form method="POST" action="<?= ROOT ?>/artistdashboard/submit_application" class="form-container">
                            <input type="hidden" name="role_id" value="<?= $data['role']->id ?? '' ?>">

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Full Name <span style="color: var(--danger);">*</span></label>
                                    <input type="text" name="artist_name" value="<?= htmlspecialchars($data['artist']->full_name ?? '') ?>" readonly class="form-input">
                                </div>

                                <div class="form-group">
                                    <label>Email Address <span style="color: var(--danger);">*</span></label>
                                    <input type="email" name="artist_email" value="<?= htmlspecialchars($data['artist']->email ?? '') ?>" readonly class="form-input">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Mobile Number <span style="color: var(--danger);">*</span></label>
                                    <input type="tel" name="artist_phone" value="<?= htmlspecialchars($data['artist']->phone ?? '') ?>" readonly class="form-input">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Media Links (Optional)</label>
                                <textarea name="media_links" class="form-input" rows="4" placeholder="Add links to your portfolio, YouTube videos, social media profiles, etc.&#10;&#10;Example:&#10;YouTube: https://youtube.com/channel/...&#10;Instagram: https://instagram.com/...&#10;Portfolio: https://mywebsite.com"></textarea>
                                <small style="color: var(--muted); display: block; margin-top: 8px;">Share links to showcase your work (YouTube, Instagram, portfolio website, etc.)</small>
                            </div>

                            <div class="form-group">
                                <label>Cover Letter / Message <span style="color: var(--danger);">*</span></label>
                                <textarea name="cover_letter" required class="form-input" rows="8" placeholder="Tell the director why you're the perfect fit for this role...&#10;&#10;Example:&#10;- Your relevant experience&#10;- Why you're interested in this role&#10;- What makes you a good fit"></textarea>
                                <small style="color: var(--muted); display: block; margin-top: 8px;">Introduce yourself and explain why you're interested in this role</small>
                            </div>

                            <div style="display: flex; gap: 15px; margin-top: 30px;">
                                <button type="button" class="btn btn-secondary" onclick="window.history.back()">
                                    <i class="bx bx-arrow-left"></i> Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-paper-plane"></i> Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
