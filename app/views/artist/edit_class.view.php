<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$profileImageSrc = ROOT . '/uploads/profile_images/user_profile.png';
if (isset($user->profile_image) && !empty($user->profile_image)) {
    $storedValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($storedValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($storedValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($storedValue);
    }
}

$editStartTime = !empty($class->start_time) ? date('H:i', strtotime($class->start_time)) : '';
$editEndTime = '';
if (!empty($class->start_time) && !empty($class->duration_minutes)) {
    $endTimestamp = strtotime('+' . (int)$class->duration_minutes . ' minutes', strtotime($class->start_time));
    if ($endTimestamp !== false) {
        $editEndTime = date('H:i', $endTimestamp);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Class - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/artist-classes-page.css">
    <link rel="shortcut icon" href="<?php echo ROOT; ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?=ROOT?>/assets/images/Rangamadala logo.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <?php
    $artistSidebarActive = 'classes';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <main class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Artist Classes</span>
                <h2>Edit Class</h2>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-star"></i> Artist
                </div>
                <div class="user-menu" id="userMenu">
                    <div class="user-menu-trigger" id="user-menu-trigger">
                        <div class="user-avatar-small">
                            <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
                        </div>
                    </div>
                    <div class="user-menu-dropdown">
                        <a href="<?= ROOT ?>/profile" class="user-menu-item">
                            <i class='bx bxs-user icon'></i>
                            <span>Profile</span>
                        </a>
                        <a href="<?= ROOT ?>/logout" class="user-menu-item">
                            <i class='bx bx-log-out icon'></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="card-section create-class-card" style="margin-bottom: 24px;">
            <h3>
                <span><i class="bx bx-edit"></i> Edit Class Details</span>
            </h3>

            <form method="POST" action="<?= ROOT ?>/artistdashboard/update_class" class="class-form-grid">
                <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">

                <div class="class-form-field form-span-full">
                    <label>Class Title *</label>
                    <input type="text" name="title" required class="form-control" value="<?= esc($class->title) ?>">
                </div>

                <div class="class-form-field form-span-full">
                    <label>Description</label>
                    <textarea name="description" rows="3" class="form-control"><?= esc($class->description ?? '') ?></textarea>
                </div>

                <div class="class-form-field">
                    <label>Level</label>
                    <select name="class_level" class="form-control">
                        <option value="all_levels" <?= ($class->class_level ?? 'all_levels') === 'all_levels' ? 'selected' : '' ?>>All Levels</option>
                        <option value="beginner" <?= ($class->class_level ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                        <option value="intermediate" <?= ($class->class_level ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                        <option value="advanced" <?= ($class->class_level ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                    </select>
                </div>

                <div class="class-form-field">
                    <label>Fee (LKR)</label>
                    <input type="number" min="0" step="0.01" name="fee" class="form-control" value="<?= (float)($class->fee ?? 0) ?>">
                </div>

                <div class="class-form-field">
                    <label>Capacity</label>
                    <input type="number" min="1" name="capacity" class="form-control" value="<?= (int)($class->capacity ?? 30) ?>">
                </div>

                <div class="class-form-field">
                    <label>Class Date</label>
                    <input type="date" name="class_date" class="form-control" value="<?= esc($class->class_date ?? '') ?>">
                </div>

                <div class="class-form-field">
                    <label>Class Time From</label>
                    <input type="time" name="start_time" class="form-control" value="<?= esc($editStartTime) ?>">
                </div>

                <div class="class-form-field">
                    <label>Class Time To</label>
                    <input type="time" name="end_time" class="form-control" value="<?= esc($editEndTime) ?>">
                </div>

                <div class="class-form-field form-span-full">
                    <label>Venue</label>
                    <input type="text" name="venue" class="form-control" value="<?= esc($class->venue ?? '') ?>">
                </div>

                <div class="create-class-footer">
                    <label class="publish-toggle">
                        <input type="checkbox" name="is_published" value="1" <?= !empty($class->is_published) ? 'checked' : '' ?>>
                        Publish immediately
                    </label>
                    <div style="display: flex; gap: 10px;">
                        <a href="<?= ROOT ?>/artistdashboard/classes" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary save-class-btn">
                            <i class="bx bx-save"></i> Update Class
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <script src="<?=ROOT?>/assets/JS/artist-classes-page.js"></script>
</body>
</html>
