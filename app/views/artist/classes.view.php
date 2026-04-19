<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$formatClassTimeRange = function ($startTime, $durationMinutes) {
    if (empty($startTime)) {
        return 'TBA';
    }

    $startTimestamp = strtotime($startTime);
    if ($startTimestamp === false) {
        return 'TBA';
    }

    $label = date('g:i A', $startTimestamp);
    $duration = (int)$durationMinutes;

    if ($duration > 0) {
        $endTimestamp = strtotime('+' . $duration . ' minutes', $startTimestamp);
        if ($endTimestamp !== false) {
            $label .= ' - ' . date('g:i A', $endTimestamp);
        }
    }

    return $label;
};

$profileImageSrc = ROOT . '/uploads/profile_images/user_profile.png';
if (isset($user->profile_image) && !empty($user->profile_image)) {
    $storedValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($storedValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($storedValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($storedValue);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Classes - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/artist-classes-page.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/toast.css">
    <link rel="shortcut icon" href="<?php echo ROOT; ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <link rel="apple-touch-icon" href="<?=ROOT?>/assets/images/Rangamadala logo.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
</head>
<body>
    <script src="<?= ROOT ?>/assets/JS/toast.js"></script>
    <?php if (!empty($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastSuccess('<?= addslashes($_SESSION['success_message']); ?>');
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toastError('<?= addslashes($_SESSION['error_message']); ?>');
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php
    $artistSidebarActive = 'classes';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <main class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Artist Classes</span>
                <h2><?= isset($user->full_name) ? esc($user->full_name) : 'Artist' ?></h2>
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

        <div class="classes-hero">
            <h2 style="margin: 0 0 6px;">Manage Your Classes In One Place</h2>
            <p style="margin: 0; opacity: 0.95;">Create classes, publish or unpublish your own, and browse classes created by other directors.</p>
        </div>

        <div class="nav-tabs-bar" style="margin-bottom: 20px;">
            <a href="#create" class="nav-tab-btn active" onclick="openClassesTab(event, 'create-tab')">
                <i class="bx bx-plus-circle"></i> Create Class
            </a>
            <a href="#manage" class="nav-tab-btn" onclick="openClassesTab(event, 'manage-tab')">
                <i class="bx bx-chalkboard-teacher"></i> Manage My Classes
            </a>
            <a href="#available" class="nav-tab-btn" onclick="openClassesTab(event, 'available-tab')">
                <i class="bx bx-globe"></i> Available Classes
            </a>
            <a href="#my-enrolments" class="nav-tab-btn" onclick="openClassesTab(event, 'my-enrolments-tab')">
                <i class="bx bx-user-graduate"></i> My Enrolments
            </a>
        </div>

        <div id="create-tab" class="tab-content active">
            <div class="card-section create-class-card" style="margin-bottom: 24px;">
                <h3>
                    <span><i class="bx bx-plus-circle"></i> Create New Class</span>
                </h3>
                <form method="POST" action="<?= ROOT ?>/artistdashboard/create_class" class="class-form-grid">
                    <div class="class-form-field form-span-full">
                        <label>Class Title *</label>
                        <input type="text" name="title" required class="form-control" placeholder="e.g., Stage Voice Masterclass">
                    </div>
                    <div class="class-form-field form-span-full">
                        <label>Description</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="What students will learn"></textarea>
                    </div>
                    <div class="class-form-field">
                        <label>Level</label>
                        <select name="class_level" class="form-control">
                            <option value="all_levels">All Levels</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="class-form-field">
                        <label>Fee (LKR)</label>
                        <input type="number" min="0" step="0.01" name="fee" class="form-control" value="0">
                    </div>
                    <div class="class-form-field">
                        <label>Capacity</label>
                        <input type="number" min="1" name="capacity" class="form-control" value="30">
                    </div>
                    <div class="class-form-field">
                        <label>Class Date</label>
                        <input type="date" name="class_date" class="form-control">
                    </div>
                    <div class="class-form-field">
                        <label>Class Time From</label>
                        <input type="time" name="start_time" class="form-control">
                    </div>
                    <div class="class-form-field">
                        <label>Class Time To</label>
                        <input type="time" name="end_time" class="form-control">
                    </div>
                    <div class="class-form-field form-span-full">
                        <label>Venue</label>
                        <input type="text" name="venue" class="form-control" placeholder="Location or online meeting link">
                    </div>
                    <div class="create-class-footer">
                        <label class="publish-toggle">
                            <input type="checkbox" name="is_published" value="1" checked>
                            Publish immediately
                        </label>
                        <button type="submit" class="btn btn-primary save-class-btn">
                            <i class="bx bx-save"></i> Save Class
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="manage-tab" class="tab-content">
            <div class="card-section" style="margin-bottom: 24px;">
                <h3>
                    <span><i class="bx bx-chalkboard-teacher"></i> Classes You Created</span>
                </h3>
                <?php if (empty($my_classes)): ?>
                    <div class="no-results">
                        <i class="bx bx-school"></i>
                        <h3>No Classes Yet</h3>
                        <p>Create your first class to start teaching.</p>
                    </div>
                <?php else: ?>
                    <div class="artists-grid">
                        <?php foreach ($my_classes as $class): ?>
                            <div class="artist-card">
                                <div class="artist-header">
                                    <h3 class="artist-name"><?= esc($class->title) ?></h3>
                                    <p class="artist-experience"><?= esc(ucwords(str_replace('_', ' ', $class->class_level ?? 'all_levels'))) ?></p>
                                </div>
                                <div class="artist-body">
                                    <div class="info-row">
                                        <span class="info-label"><i class="bx bx-calendar"></i> Date:</span>
                                        <span class="info-value"><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="bx bx-time-five"></i> Time:</span>
                                        <span class="info-value"><?= esc($formatClassTimeRange($class->start_time ?? null, $class->duration_minutes ?? 0)) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Fee:</span>
                                        <span class="info-value">LKR <?= number_format((float)($class->fee ?? 0), 2) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Enrolled:</span>
                                        <span class="info-value"><?= (int)($class->enrolled_count ?? 0) ?> / <?= (int)($class->capacity ?? 0) ?></span>
                                    </div>
                                    <?php if (!empty($class->venue)): ?>
                                        <div class="info-row">
                                            <span class="info-label">Venue:</span>
                                            <span class="info-value"><?= esc($class->venue) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="info-row">
                                        <span class="info-label">Public:</span>
                                        <span class="info-value">
                                            <?php if (!empty($class->is_published)): ?>
                                                <span class="status-badge assigned">Published</span>
                                            <?php else: ?>
                                                <span class="status-badge pending">Unpublished</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="artist-footer">
                                    <a href="<?= ROOT ?>/artistdashboard/edit_class/<?= (int)$class->id ?>" class="btn btn-primary">
                                        <i class="bx bx-edit"></i> Edit Class
                                    </a>

                                    <form method="POST" action="<?= ROOT ?>/artistdashboard/toggle_class_publish">
                                        <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">
                                        <button type="submit" class="btn btn-secondary">
                                            <i class="bx bx-bullhorn"></i> <?= !empty($class->is_published) ? 'Unpublish' : 'Publish' ?>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?= ROOT ?>/artistdashboard/delete_class" onsubmit="return confirm('Delete this class? This will remove all enrollments too.');">
                                        <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="bx bx-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="available-tab" class="tab-content">
            <div class="card-section" style="margin-bottom: 24px;">
                <h3>
                    <span><i class="bx bx-globe"></i> Available Classes by Other Directors</span>
                </h3>
                <?php if (empty($available_classes)): ?>
                    <div class="no-results">
                        <i class="bx bx-search"></i>
                        <h3>No Published Classes</h3>
                        <p>No classes from other directors are available right now.</p>
                    </div>
                <?php else: ?>
                    <div class="artists-grid">
                        <?php foreach ($available_classes as $class): ?>
                            <div class="artist-card">
                                <div class="artist-header" style="background: linear-gradient(135deg, #343a40, #1f2327);">
                                    <h3 class="artist-name"><?= esc($class->title) ?></h3>
                                    <p class="artist-experience">By <?= esc($class->creator_name ?? 'Director') ?></p>
                                </div>
                                <div class="artist-body">
                                    <div class="info-row">
                                        <span class="info-label"><i class="bx bx-calendar"></i> Date:</span>
                                        <span class="info-value"><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="bx bx-time-five"></i> Time:</span>
                                        <span class="info-value"><?= esc($formatClassTimeRange($class->start_time ?? null, $class->duration_minutes ?? 0)) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Fee:</span>
                                        <span class="info-value">LKR <?= number_format((float)($class->fee ?? 0), 2) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Seats:</span>
                                        <span class="info-value"><?= (int)($class->enrolled_count ?? 0) ?> / <?= (int)($class->capacity ?? 0) ?></span>
                                    </div>
                                </div>
                                <div class="artist-footer">
                                    <form method="POST" action="<?= ROOT ?>/artistdashboard/start_class_payment" class="class-enroll-payment-form" style="width: 100%;">
                                        <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">
                                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                                            <i class="bx bx-user-plus"></i> Enroll Now
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="my-enrolments-tab" class="tab-content">
            <div class="card-section" style="margin-bottom: 24px;">
                <h3>
                    <span><i class="bx bx-user-graduate"></i> My Enrolments</span>
                </h3>
                <?php if (empty($enrolled_classes)): ?>
                    <div class="no-results">
                        <i class="bx bx-clipboard-list"></i>
                        <h3>No Enrollments Yet</h3>
                        <p>Enroll in another director's class to see it here.</p>
                    </div>
                <?php else: ?>
                    <div class="artists-grid">
                        <?php foreach ($enrolled_classes as $class): ?>
                            <div class="artist-card">
                                <div class="artist-header" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                                    <h3 class="artist-name"><?= esc($class->title) ?></h3>
                                    <p class="artist-experience">By <?= esc($class->creator_name ?? 'Director') ?></p>
                                </div>
                                <div class="artist-body">
                                    <div class="info-row">
                                        <span class="info-label"><i class="bx bx-calendar"></i> Date:</span>
                                        <span class="info-value"><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label"><i class="bx bx-time-five"></i> Time:</span>
                                        <span class="info-value"><?= esc($formatClassTimeRange($class->start_time ?? null, $class->duration_minutes ?? 0)) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Fee:</span>
                                        <span class="info-value">LKR <?= number_format((float)($class->fee ?? 0), 2) ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Status:</span>
                                        <span class="info-value"><span class="status-badge assigned">Enrolled</span></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="<?=ROOT?>/assets/JS/artist-classes-page.js"></script>
</body>
</html>
