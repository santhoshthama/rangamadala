<?php
$profile = $data['profile'] ?? null;
$profileImage = $data['profile_image'] ?? null;
$errors = $data['errors'] ?? [];
$success = $data['success'] ?? '';
$error = $data['error'] ?? '';

$profileImageSrc = ROOT . '/uploads/profile_images/user_profile.png';
if (!empty($profileImage)) {
    $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($profileImage);
}

$currentImageLabel = !empty($profileImage)
    ? basename(str_replace('\\', '/', $profileImage))
    : 'Recommended 800x800 JPG/PNG';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audience Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/edit_profile.css">
</head>
<body>
    <div class="page-wrapper">
        <a class="back-link" href="<?= ROOT ?>/AudienceProfile">
            <i class="bx bx-arrow-left"></i>
            <span>Back to Profile</span>
        </a>

        <div class="profile-card">
            <aside class="profile-summary">
                <img id="profilePreview" src="<?= esc($profileImageSrc) ?>" alt="Audience profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
                <div>
                    <h2><?= esc($profile->full_name ?? 'Audience') ?></h2>
                    <p><i class="bx bx-envelope"></i> <?= esc($profile->email ?? 'N/A') ?></p>
                    <p><i class="bx bx-phone"></i> <?= esc($profile->phone ?? 'N/A') ?></p>
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

                <?php if (!empty($error)): ?>
                    <div class="alerts">
                        <div class="alert alert-error">
                            <i class="bx bx-exclamation-triangle"></i> <?= esc($error) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alerts">
                        <?php foreach ($errors as $item): ?>
                            <div class="alert alert-error">
                                <i class="bx bx-exclamation-triangle"></i> <?= esc($item) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input id="full_name" name="full_name" type="text" value="<?= esc($profile->full_name ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="<?= esc($profile->email ?? '') ?>" required>
                    </div>

                    <div class="form-group full">
                        <label for="phone">Phone</label>
                        <input id="phone" name="phone" type="text" value="<?= esc($profile->phone ?? '') ?>" required>
                    </div>

                    <div class="form-group full">
                        <label for="bio">Bio / About Me</label>
                        <textarea id="bio" name="bio" rows="4" placeholder="Tell us about yourself..."><?= esc($data['bio'] ?? '') ?></textarea>
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
                        <a href="<?= ROOT ?>/AudienceProfile" class="btn btn-cancel">Cancel</a>
                        <button type="submit" class="btn btn-save">
                            <i class="bx bx-save"></i>
                            Save Changes
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('profile_image');
        const preview = document.getElementById('profilePreview');

        if (imageInput && preview) {
            imageInput.addEventListener('change', function(event) {
                const file = event.target.files && event.target.files[0];
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
</body>
</html>
