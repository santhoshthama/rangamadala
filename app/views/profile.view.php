<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$user = $user ?? null;
$form = $form ?? ['full_name' => '', 'phone' => '', 'years_experience' => '', 'bio' => '', 'location' => '', 'website' => ''];
$errors = $errors ?? [];
$success = $success ?? '';

// Get dashboard link based on role
$role = $_SESSION['user_role'] ?? 'artist';
$dashboardLinks = [
    'artist' => ROOT . '/artistdashboard',
    'director' => ROOT . '/director',
    'admin' => ROOT . '/admindashboard',
    'audience' => ROOT . '/audiencedashboard',
    'service_provider' => ROOT . '/serviceproviderdashboard',
];
$dashboardLink = $dashboardLinks[$role] ?? ROOT . '/artistdashboard';

// Get page title based on role
$roleTitles = [
    'artist' => 'Artist Profile',
    'director' => 'Director Profile',
    'admin' => 'Admin Profile',
    'audience' => 'Audience Profile',
    'service_provider' => 'Service Provider Profile',
];
$pageTitle = $roleTitles[$role] ?? 'Profile';

$profileImageSrc = $role === 'artist'
    ? ROOT . '/uploads/profile_images/user_profile.png'
    : ROOT . '/assets/images/default-avatar.jpg';
if ($user && !empty($user->profile_image)) {
    $imageValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif ($role !== 'artist' && $user && !empty($user->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $user->nic_photo), '/');
}

$nicDownload = $user && !empty($user->nic_photo)
    ? ROOT . '/' . ltrim(str_replace('\\', '/', $user->nic_photo), '/')
    : '';

$currentImageLabel = $user && !empty($user->profile_image)
    ? basename(str_replace('\\', '/', $user->profile_image))
    : 'Recommended 800x800 JPG/PNG';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle) ?></title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/profile.css">
</head>
<body>
    <div class="page-wrapper">
        <a class="back-link" href="<?= esc($dashboardLink) ?>">
            <i class="bx bx-arrow-left"></i>
            <span>Back to Dashboard</span>
        </a>

        <div class="profile-card">
            <aside class="profile-summary">
                <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= $role === 'artist' ? ROOT . '/uploads/profile_images/user_profile.png' : ROOT . '/assets/images/default-avatar.jpg' ?>'">

                <div>
                    <h2><?= $user ? esc($user->full_name ?? 'User') : 'User' ?></h2>
                    <p><i class="bx bx-envelope"></i> <?= $user ? esc($user->email ?? 'N/A') : 'N/A' ?></p>
                    <p><i class="bx bx-phone"></i> <?= $user ? esc($user->phone ?? 'N/A') : 'N/A' ?></p>
                    <?php if ($user && !empty($user->location)): ?>
                    <p><i class="bx bx-map-marker-alt"></i> <?= esc($user->location) ?></p>
                    <?php endif; ?>

                    <div class="summary-item">
                        <span>Years of Experience</span>
                        <strong><?= $user && isset($user->years_experience) && $user->years_experience !== null ? esc($user->years_experience) . ' years' : 'Not added yet' ?></strong>
                    </div>


                </div>
            </aside>

            <section class="profile-form">
                <h1>Profile Details</h1>
                <p class="subtitle">Keep your information up to date.</p>

                <?php if (!empty($success)): ?>
                    <div class="alerts">
                        <div class="alert alert-success">
                            <i class="bx bx-check-circle"></i> <?= esc($success) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alerts">
                        <?php foreach ($errors as $error): ?>
                            <div class="alert alert-error">
                                <i class="bx bx-exclamation-triangle"></i> <?= esc($error) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input id="full_name" name="full_name" type="text" value="<?= esc($form['full_name'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" value="<?= $user ? esc($user->email ?? '') : '' ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" type="tel" value="<?= esc($form['phone'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="years_experience">Years of Experience</label>
                        <input id="years_experience" name="years_experience" type="number" min="0" placeholder="e.g. 5" value="<?= esc($form['years_experience'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input id="location" name="location" type="text" placeholder="e.g. Colombo, Sri Lanka" value="<?= esc($form['location'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="location">Location</label>
                        <input id="location" name="location" type="text" placeholder="e.g. Colombo, Sri Lanka" value="<?= esc($form['location'] ?? '') ?>">
                    </div>

                    <div class="form-group full">
                        <label for="bio">Bio / About Me</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself, your experience, and what makes you unique..."><?= esc($form['bio'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group full">
                        <label for="website">Links (Social / Portfolio / Any)</label>
                        <input id="website" name="website" type="text" placeholder="Add one or more links (Instagram, YouTube, portfolio, etc.)" value="<?= esc($form['website'] ?? '') ?>">
                    </div>

                    <div class="form-group full">
                        <label>Profile Image</label>
                        <div class="file-input">
                            <label for="profile_image">
                                <i class="bx bx-upload"></i>
                                <span>Upload new image</span>
                            </label>
                            <input id="profile_image" name="profile_image" type="file" accept="image/*">
                            <span><?= esc($currentImageLabel) ?></span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit">
                            <i class="bx bx-save"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
