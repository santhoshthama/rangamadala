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

$profileImageSrc = ROOT . '/uploads/profile_images/default_user.jpg';
if (isset($user->profile_image) && !empty($user->profile_image)) {
    $storedValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($storedValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($storedValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($storedValue);
    }
} elseif (isset($user->nic_photo) && !empty($user->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $user->nic_photo), '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Classes - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/toast.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>
<style>
        .header--wrapper .user--info {
            gap: 16px;
        }

        .header--wrapper .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, #be9227, #a67d1e);
            color: #fff;
            border: 1px solid rgba(145, 108, 24, 0.35);
            box-shadow: 0 4px 10px rgba(166, 125, 30, 0.2);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            line-height: 1;
        }

        .header--wrapper .user-menu-trigger {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 3px solid #b88b22;
            box-shadow: 0 0 0 2px rgba(224, 191, 105, 0.45);
            overflow: hidden;
            cursor: pointer;
            transition: var(--transition);
        }

        .header--wrapper .user-menu-trigger:hover {
            transform: scale(1.04);
        }

        .header--wrapper .user-avatar-small {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: transparent;
        }

        .header--wrapper .user-avatar-small img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            display: block;
            border: 0;
        }

        .header--wrapper .user-menu {
            position: relative;
        }

        .header--wrapper .user-menu-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            z-index: 1000;
            background: #ffffff;
            border: 1px solid #f0d79d;
            border-radius: 14px;
            box-shadow: 0 16px 30px rgba(74, 58, 20, 0.18);
            min-width: 210px;
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.2s ease;
        }

        .header--wrapper .user-menu.active .user-menu-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .header--wrapper .user-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            color: #2f2f2f;
            text-decoration: none;
            font-size: 15px;
            border-radius: 10px;
            transition: var(--transition);
            cursor: pointer;
        }

        .header--wrapper .user-menu-item:hover {
            background: rgba(186, 142, 35, 0.1);
            color: #5a4415;
        }

        .header--wrapper .user-menu-item .icon {
            font-size: 20px;
        }

        .classes-hero {
            background: linear-gradient(135deg, #ba8e23, #a0781e);
            color: white;
            padding: 26px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            margin-bottom: 24px;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .create-class-card {
            background: linear-gradient(180deg, #fffdf8 0%, #fff8ea 100%);
            border: 1px solid #edd9a8;
            border-radius: 16px;
            padding: 26px;
            box-shadow: 0 10px 24px rgba(122, 95, 31, 0.12);
        }

        .create-class-card h3 {
            color: #5e4718;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(186, 142, 35, 0.22);
        }

        .class-form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-top: 14px;
        }

        .form-span-full {
            grid-column: 1 / -1;
        }

        .class-form-field label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #4f3e17;
        }

        .class-form-field input,
        .class-form-field textarea,
        .class-form-field select {
            width: 100%;
            border: 1px solid #e1cb95;
            border-radius: 10px;
            padding: 11px 12px;
            background: #fffefb;
            color: #2f2f2f;
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .class-form-field textarea {
            min-height: 108px;
            resize: vertical;
        }

        .class-form-field input:focus,
        .class-form-field textarea:focus,
        .class-form-field select:focus {
            outline: none;
            border-color: #ba8e23;
            box-shadow: 0 0 0 3px rgba(186, 142, 35, 0.2);
            background: #fff;
        }

        .create-class-footer {
            grid-column: 1 / -1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px dashed rgba(186, 142, 35, 0.3);
        }

        .publish-toggle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #4f3e17;
        }

        .save-class-btn {
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(166, 125, 30, 0.25);
        }

        @media (max-width: 768px) {
            .create-class-card {
                padding: 18px;
            }

            .create-class-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .save-class-btn {
                width: 100%;
            }
        }
    </style>
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

    <aside class="sidebar">
        <div class="logo">
            <h2><i class='bx bxs-theater'></i></h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?=ROOT?>/artistdashboard">
                    <i class='bx bxs-home'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistdashboard/notifications">
                    <i class='bx bxs-bell'></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies">
                    <i class='bx bxs-megaphone'></i>
                    <span>View All Vacancies</span>
                </a>
            </li>
            <li class="active">
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
                            <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.jpg'">
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
                                <div class="artist-header" style="background: linear-gradient(135deg, #7f56d9, #5b3ab0);">
                                    <h3 class="artist-name"><?= esc($class->title) ?></h3>
                                    <p class="artist-experience"><?= esc(ucwords(str_replace('_', ' ', $class->class_level ?? 'all_levels'))) ?></p>
                                </div>
                                <div class="artist-body">
                                    <div class="info-row">
                                        <span class="info-label">Date:</span>
                                        <span class="info-value"><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Time:</span>
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
                                <div class="artist-footer" style="display: flex; gap: 10px;">
                                    <form method="POST" action="<?= ROOT ?>/artistdashboard/toggle_class_publish" style="flex: 1;">
                                        <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">
                                        <button type="submit" class="btn btn-secondary" style="width: 100%;">
                                            <i class="bx bx-bullhorn"></i> <?= !empty($class->is_published) ? 'Unpublish' : 'Publish' ?>
                                        </button>
                                    </form>
                                    <form method="POST" action="<?= ROOT ?>/artistdashboard/delete_class" style="flex: 1;" onsubmit="return confirm('Delete this class? This will remove all enrollments too.');">
                                        <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">
                                        <button type="submit" class="btn btn-danger" style="width: 100%;">
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
                                        <span class="info-label">Date:</span>
                                        <span class="info-value"><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Time:</span>
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
                                        <span class="info-label">Date:</span>
                                        <span class="info-value"><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Time:</span>
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

    <script>
        function initArtistClassPayments() {
            const enrollForms = document.querySelectorAll('.class-enroll-payment-form');
            if (!enrollForms.length) {
                return;
            }

            enrollForms.forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    if (typeof payhere === 'undefined') {
                        alert('PayHere is not available right now. Please refresh and try again.');
                        return;
                    }

                    const submitBtn = form.querySelector('button[type="submit"]');
                    const classIdInput = form.querySelector('input[name="class_id"]');
                    if (!classIdInput || !classIdInput.value) {
                        alert('Invalid class selected.');
                        return;
                    }

                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }

                    fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: 'class_id=' + encodeURIComponent(classIdInput.value)
                    })
                        .then(function (response) {
                            return response.json();
                        })
                        .then(function (data) {
                            if (!data.success) {
                                alert(data.error || 'Unable to initialize class payment.');
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                }
                                return;
                            }

                            const payment = {
                                sandbox: !!data.sandbox,
                                merchant_id: data.merchant_id,
                                return_url: data.return_url,
                                cancel_url: data.cancel_url,
                                notify_url: data.notify_url,
                                order_id: data.order_id,
                                items: data.title || 'Drama Class',
                                amount: data.amount,
                                currency: 'LKR',
                                hash: data.hash,
                                first_name: 'Artist',
                                last_name: 'User',
                                email: 'artist@example.com',
                                phone: '0770000000',
                                address: 'Sri Lanka',
                                city: 'Colombo',
                                country: 'Sri Lanka'
                            };

                            payhere.onCompleted = function () {
                                window.location.href = data.return_url;
                            };

                            payhere.onDismissed = function () {
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                }
                            };

                            payhere.onError = function (error) {
                                alert('Payment error: ' + error);
                                if (submitBtn) {
                                    submitBtn.disabled = false;
                                }
                            };

                            payhere.startPayment(payment);
                        })
                        .catch(function () {
                            alert('Payment initialization failed. Please try again.');
                            if (submitBtn) {
                                submitBtn.disabled = false;
                            }
                        });
                });
            });
        }

        function openClassesTab(evt, tabId) {
            evt.preventDefault();

            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }

            const tabButtons = document.getElementsByClassName('nav-tab-btn');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }

            document.getElementById(tabId).classList.add('active');
            evt.currentTarget.classList.add('active');
        }

        const userMenu = document.getElementById('userMenu');
        const userMenuTrigger = document.getElementById('user-menu-trigger');

        if (userMenu && userMenuTrigger) {
            userMenuTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenu.classList.toggle('active');
            });

            document.addEventListener('click', function (e) {
                if (!userMenu.contains(e.target)) {
                    userMenu.classList.remove('active');
                }
            });
        }

        initArtistClassPayments();
    </script>
</body>
</html>
