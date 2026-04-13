<?php
$profile = $data['profile'] ?? null;
$profileImage = $data['profile_image'] ?? null;
$errors = $data['errors'] ?? [];
$success = $data['success'] ?? '';
$error = $data['error'] ?? '';

$profileImageSrc = ROOT . '/uploads/profile_images/default_user.png';
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
    <style>
        :root {
            --brand: #ba8e23;
            --brand-strong: #a0781e;
            --card: #ffffff;
            --ink: #1f2933;
            --muted: #6b7280;
            --danger: #dc2626;
            --success: #15803d;
            --border: #e5e7eb;
            --radius: 16px;
            --shadow: 0 12px 40px rgba(31, 41, 51, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, rgba(186, 142, 35, 0.12), rgba(160, 120, 30, 0.08));
            color: var(--ink);
            min-height: 100vh;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        a { color: inherit; text-decoration: none; }

        .page-wrapper { width: min(1100px, 100%); }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--brand-strong);
            margin-bottom: 16px;
        }

        .profile-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: grid;
            grid-template-columns: 320px 1fr;
            overflow: hidden;
        }

        .profile-summary {
            background: linear-gradient(180deg, rgba(186, 142, 35, 0.95), rgba(160, 120, 30, 0.92));
            color: #fff;
            padding: 40px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .profile-summary img {
            width: 160px;
            height: 160px;
            border-radius: 20px;
            object-fit: cover;
            border: 6px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.24);
            align-self: center;
        }

        .profile-summary h2 {
            margin-top: 28px;
            margin-bottom: 8px;
            font-size: 28px;
        }

        .profile-summary p {
            margin: 4px 0;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.9);
        }

        .profile-form { padding: 40px; }

        .profile-form h1 {
            margin: 0 0 16px;
            font-size: 30px;
            font-weight: 700;
            color: var(--ink);
        }

        .profile-form p.subtitle {
            margin: 0 0 32px;
            color: var(--muted);
            font-size: 15px;
        }

        .alerts {
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(21, 128, 61, 0.12);
            color: var(--success);
            border: 1px solid rgba(21, 128, 61, 0.35);
        }

        .alert-error {
            background: rgba(220, 38, 38, 0.1);
            color: var(--danger);
            border: 1px solid rgba(220, 38, 38, 0.3);
        }

        form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full { grid-column: 1 / -1; }

        label {
            font-weight: 600;
            font-size: 14px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid var(--border);
            font-size: 15px;
            font-family: inherit;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(186, 142, 35, 0.18);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .file-input {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .file-input label {
            cursor: pointer;
            padding: 12px 18px;
            border-radius: 12px;
            background: rgba(186, 142, 35, 0.12);
            color: var(--brand-strong);
            font-weight: 600;
            letter-spacing: normal;
            text-transform: none;
        }

        .file-input span {
            font-size: 14px;
            color: var(--muted);
        }

        input[type="file"] { display: none; }

        .form-actions {
            grid-column: 1 / -1;
            margin-top: 12px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            padding: 14px 26px;
            border-radius: 12px;
            border: none;
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-cancel {
            background: #6b7280;
        }

        .btn-save {
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            box-shadow: 0 12px 30px rgba(186, 142, 35, 0.28);
        }

        @media (max-width: 960px) {
            .profile-card { grid-template-columns: 1fr; }

            .profile-summary { text-align: center; }

            form { grid-template-columns: 1fr; }

            .form-actions { justify-content: center; }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <a class="back-link" href="<?= ROOT ?>/AudienceProfile">
            <i class="bx bx-arrow-left"></i>
            <span>Back to Profile</span>
        </a>

        <div class="profile-card">
            <aside class="profile-summary">
                <img id="profilePreview" src="<?= esc($profileImageSrc) ?>" alt="Audience profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.png'">
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
