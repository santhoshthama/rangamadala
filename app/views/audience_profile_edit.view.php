<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - <?= APP_NAME ?></title>
    <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.1/css/all.min.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/edit_profile.css">
    <style>
        .message-box {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .success-box {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-box {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .error-list {
            margin: 10px 0 0 20px;
        }
        .back-container {
            text-align: left;
            margin-bottom: 20px;
        }
        .back-link {
            text-decoration: none;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: transparent;
            color: #d4af37;
            padding: 8px 14px;
            border: 2px solid #d4af37;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.3s ease;
        }
        .back-btn:hover {
            background-color: #d4af37;
            color: #1a1410;
        }
        .back-btn i {
            font-size: 14px;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="back-container">
        <a href="<?= ROOT ?>/AudienceProfile" class="back-link">
            <button class="back-btn" type="button">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </button>
        </a>
    </div>

    <h2 class="page-title">Edit Profile</h2>
    <hr class="divider">

    <?php if (!empty($data['success'])): ?>
        <div class="message-box success-box">
            <?= htmlspecialchars($data['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="message-box error-box">
            <?= htmlspecialchars($data['error']) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['errors'])): ?>
        <div class="message-box error-box">
            <strong>Please fix the following errors:</strong>
            <ul class="error-list">
                <?php foreach ($data['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="profile-form" method="POST" enctype="multipart/form-data">

        <div class="form-grid">

            <!-- Contact Details -->
            <div class="card">
                <h3 class="card-title">Contact Details</h3>

                <div class="input-grid">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($data['profile']->full_name ?? '') ?>" required>

                    <label>Phone Number *</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($data['profile']->phone ?? '') ?>" required>

                    <label>Email *</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($data['profile']->email ?? '') ?>" required>
                </div>
            </div>

            <!-- Profile Upload -->
            <div class="card">
                <h3 class="card-title">Upload Profile Photo</h3>

                <div class="upload-box">
                    <div class="photo-preview">
                        <?php if (!empty($data['profile_image'])): ?>
                            <img src="<?= ROOT ?>/uploads/profile_images/<?= htmlspecialchars($data['profile_image']) ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>

                    <input type="file" name="profile_image" id="profileUpload" accept="image/*" hidden>
                    <label class="btn btn-primary-soft" for="profileUpload">Upload Photo</label>

                    <p class="note">JPG, PNG or GIF (Max 5MB)</p>
                </div>
            </div>
        </div>

        <!-- Bio Section -->
        <div class="card">
            <h3 class="card-title">Bio / About You</h3>
            <div class="input-grid">
                <label>Bio</label>
                <textarea name="bio" rows="5" placeholder="Tell us about yourself..."><?= htmlspecialchars($data['bio'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Buttons -->
        <div class="btn-row">
            <a href="<?= ROOT ?>/AudienceProfile" class="btn btn-error">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>

    </form>
</div>

<script>
    // Preview image before upload
    document.getElementById('profileUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.querySelector('.photo-preview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
