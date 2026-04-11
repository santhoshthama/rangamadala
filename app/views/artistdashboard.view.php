<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
}

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

$requestPath = strtolower((string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''));
$sidebarActive = [
    'dashboard' => false,
    'notifications' => false,
    'vacancies' => false,
    'classes' => false,
    'showings' => false,
];

if (strpos($requestPath, '/artistdashboard/notifications') !== false) {
    $sidebarActive['notifications'] = true;
} elseif (strpos($requestPath, '/artistdashboard/browse_vacancies') !== false) {
    $sidebarActive['vacancies'] = true;
} elseif (strpos($requestPath, '/artistdashboard/classes') !== false) {
    $sidebarActive['classes'] = true;
} else {
    $sidebarActive['dashboard'] = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/toast.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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

        .header--wrapper .role-badge i {
            font-size: 12px;
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

        .artist-stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
        }

        .artist-stat-card {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            border: 1px solid #f0dfb4;
            border-radius: var(--radius);
            padding: 22px;
            text-align: left;
            color: #4a3a14;
            box-shadow: 0 4px 12px rgba(186, 142, 35, 0.12);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .artist-stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(186, 142, 35, 0.2);
        }

        .artist-stat-card .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .artist-stat-card .stat-card-title {
            font-size: 14px;
            font-weight: 600;
            color: #7a6121;
        }

        .artist-stat-card .stat-card-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .artist-stat-card .stat-card-icon.primary {
            background: rgba(186, 142, 35, 0.14);
            color: var(--brand);
        }

        .artist-stat-card .stat-card-icon.info {
            background: rgba(186, 142, 35, 0.14);
            color: var(--brand);
        }

        .artist-stat-card .stat-card-icon.success {
            background: rgba(186, 142, 35, 0.14);
            color: var(--brand);
        }

        .artist-stat-card .stat-card-icon.warning {
            background: rgba(186, 142, 35, 0.14);
            color: var(--brand);
        }

        .artist-stat-card .stat-card-value {
            font-size: 34px;
            font-weight: 700;
            line-height: 1;
            margin: 0;
            color: #5a4415;
        }

        .btn-compact {
            padding: 8px 16px;
            font-size: 15px;
            min-height: 32px;
            border-radius: 7px;
            gap: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1.2;
            white-space: nowrap;
        }

        .btn-compact i {
            font-size: 15px;
            line-height: 1;
            display: inline-block;
            vertical-align: middle;
            margin: 0;
            position: static;
            transform: none;
        }

        .no-results h3 {
            font-weight: 500;
            color: var(--muted);
        }

        body.showings-only .artist-stats-grid,
        body.showings-only .vacancies-banner,
        body.showings-only .nav-tabs-bar {
            display: none;
        }

        #my-showings-tab .classes-subtabs {
            display: flex;
            gap: 0;
            margin: 10px 0 18px;
            flex-wrap: wrap;
            background: linear-gradient(180deg, #f6f5f2 0%, #efede8 100%);
            border: 1px solid #ddd9cf;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 14px rgba(0, 0, 0, 0.05);
        }

        #my-showings-tab .classes-subtab-btn {
            border: none;
            background: transparent;
            color: #6c7484;
            padding: 14px 18px;
            min-height: 56px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            line-height: 1;
            border-bottom: 3px solid transparent;
            border-right: 1px solid #e2dfd7;
        }

        #my-showings-tab .classes-subtab-btn:last-child {
            border-right: none;
        }

        #my-showings-tab .classes-subtab-btn i {
            font-size: 16px;
            line-height: 1;
        }

        #my-showings-tab .classes-subtab-btn:hover {
            background: rgba(186, 142, 35, 0.1);
            color: #8c6c20;
        }

        #my-showings-tab .classes-subtab-btn.active {
            background: linear-gradient(180deg, #f5efe1 0%, #efe6d2 100%);
            color: #b48218;
            border-bottom-color: #b48218;
            box-shadow: inset 0 -1px 0 rgba(180, 130, 24, 0.12);
        }

        #my-showings-tab .classes-subtab-panel {
            display: none;
        }

        #my-showings-tab .classes-subtab-panel.active {
            display: block;
        }
    </style>
</head>
<body>
        <!-- Toast Notification Script -->
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

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2><i class='bx bxs-theater'></i></h2>
        </div>
        <ul class="menu">
            <li class="<?= $sidebarActive['dashboard'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard">
                    <i class='bx bxs-home'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['vacancies'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies">
                    <i class='bx bxs-megaphone'></i>
                    <span>View All Vacancies</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['notifications'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/notifications">
                    <i class='bx bxs-bell'></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['classes'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/classes">
                    <i class='bx bxs-graduation'></i>
                    <span>Classes</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['showings'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard#my-showings">
                    <i class='bx bx-calendar-event'></i>
                    <span>Showings</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Artist Dashboard</span>
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

        <!-- Statistics -->
        <div class="stats-grid artist-stats-grid">
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Dramas</div>
                    <div class="stat-card-icon primary">
                        <i class="bx bx-theater-masks"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['total_dramas']) ? $stats['total_dramas'] : 0 ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">As Director</div>
                    <div class="stat-card-icon info">
                        <i class="bx bx-film"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['as_director']) ? $stats['as_director'] : 0 ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">As Production Manager</div>
                    <div class="stat-card-icon success">
                        <i class="bx bx-briefcase"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['as_manager']) ? $stats['as_manager'] : 0 ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">As Actor</div>
                    <div class="stat-card-icon warning">
                        <i class="bx bx-user-tie"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?></div>
            </div>
        </div>

        <!-- Drama Role Vacancies Banner -->
        <div class="card-section vacancies-banner" style="background: linear-gradient(135deg, #ba8e23, #a0781e); color: white; padding: 30px; border-radius: var(--radius); margin-bottom: 30px; box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <h2 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 700; color: white;">
                        Drama Role Vacancies Now Open!
                    </h2>
                    <p style="margin: 0; opacity: 0.95; font-size: 16px; line-height: 1.5; color: white;">
                        Discover available roles and apply to be part of our upcoming drama productions.
                    </p>
                </div>
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies" class="btn btn-primary btn-compact" style="background: white; color: var(--brand); font-weight: 600;">
                    <i class="bx bx-search"></i> Search Vacancies
                </a>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="#director" class="nav-tab-btn active" onclick="openTabLink(event, 'director-tab')">
                <i class="bx bx-film"></i> As Director (<?= isset($stats['as_director']) ? $stats['as_director'] : 0 ?>)
            </a>
            <a href="#manager" class="nav-tab-btn" onclick="openTabLink(event, 'manager-tab')">
                <i class="bx bx-briefcase"></i> As Production Manager (<?= isset($stats['as_manager']) ? $stats['as_manager'] : 0 ?>)
            </a>
            <a href="#actor" class="nav-tab-btn" onclick="openTabLink(event, 'actor-tab')">
                <i class="bx bx-user-tie"></i> As Actor (<?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?>)
            </a>
            <a href="#interviews" class="nav-tab-btn" onclick="openTabLink(event, 'interviews-tab')">
                <i class="bx bx-calendar-check"></i> View Interview Schedules (<?= isset($stats['upcoming_interviews']) ? $stats['upcoming_interviews'] : 0 ?>)
            </a>
            <a href="#requests" class="nav-tab-btn" onclick="openTabLink(event, 'requests-tab')">
                <i class="bx bx-envelope"></i> Requests 
                (<?= (isset($stats['pending_requests']) ? $stats['pending_requests'] : 0) + (isset($stats['pending_pm_requests']) ? $stats['pending_pm_requests'] : 0) ?>)
            </a>
        </div>

        <!-- Tabs for Drama Categories -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">

                <!-- As Director Tab -->
                <div id="director-tab" class="tab-content active">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-film"></i> Dramas You're Directing</span>
                            <?php if (isset($dramas_as_director) && !empty($dramas_as_director)): ?>
                                <a href="<?=ROOT?>/createDrama" class="btn btn-primary btn-compact">
                                    <i class="bx bx-plus"></i> Create New Drama
                                </a>
                            <?php endif; ?>
                        </h3>
                    <?php if (!isset($dramas_as_director) || empty($dramas_as_director)): ?>
                        <div class="no-results">
                            <i class="bx bx-film"></i>
                            <h3>No Dramas Yet</h3>
                            <p>You haven't created any dramas. Start your journey as a director!</p>
                            <button class="btn btn-primary btn-compact" style="margin-top: 16px;" onclick="window.location.href='<?=ROOT?>/createDrama'">
                                <i class="bx bx-plus"></i> Create Drama
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_director as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header" style="background: linear-gradient(135deg, #ba8e23, #8f6d1b);">
                                        <h3 class="artist-name"><?= esc($drama->drama_name ?? 'Registered Drama') ?></h3>
                                        <p class="artist-experience">Certificate <?= esc($drama->certificate_number ?? 'N/A') ?></p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Owner:</span>
                                            <span class="info-value"><?= esc($drama->owner_name ?? 'Not recorded') ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Created:</span>
                                            <span class="info-value"><?= isset($drama->created_at) ? date('M d, Y', strtotime($drama->created_at)) : 'N/A' ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Public:</span>
                                            <span class="info-value">
                                                <?php if (!empty($drama->is_published)): ?>
                                                    <span class="status-badge assigned">Published</span>
                                                <?php else: ?>
                                                    <span class="status-badge pending">Not Published</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Certificate Image:</span>
                                            <span class="info-value">
                                                <?php if (!empty($drama->certificate_image)): ?>
                                                    <a href="<?= ROOT ?>/uploads/certificates/<?= esc(rawurlencode($drama->certificate_image)) ?>" target="_blank" style="color: var(--brand); font-weight: 600;">
                                                        View
                                                    </a>
                                                <?php else: ?>
                                                    <span class="status-badge pending">Pending</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <button class="btn btn-primary" style="flex: 1;" onclick="handleDirectorManage(<?=$drama->id?>)">
                                            <i class="bx bx-tachometer-alt"></i> Manage
                                        </button>
                                        <a class="btn" style="flex: 1; text-align: center; background: linear-gradient(135deg, #8f6d1b, #6f5415); color: #fff;" href="<?= ROOT ?>/director/drama_details?drama_id=<?= (int)$drama->id ?>#publish-section">
                                            <i class="bx bx-bullhorn"></i> <?= !empty($drama->is_published) ? 'Update Publish' : 'Publish' ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- As Production Manager Tab -->
                <div id="manager-tab" class="tab-content">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-briefcase"></i> Dramas You're Managing as Production Manager</span>
                        </h3>
                    <?php if (!isset($dramas_as_manager) || empty($dramas_as_manager)): ?>
                        <div class="no-results">
                            <i class="bx bx-briefcase"></i>
                            <h3>No Production Manager Roles</h3>
                            <p>You haven't been assigned as a production manager for any dramas yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_manager as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header" style="background: linear-gradient(135deg, #b88920, #8a6718);">
                                        <h3 class="artist-name"><?= esc($drama->drama_name ?? 'Drama') ?></h3>
                                        <p class="artist-experience"><?= esc($drama->description ?? 'Production Manager') ?></p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Director:</span>
                                            <span class="info-value"><?= esc($drama->creator_name ?? 'Unknown') ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Language:</span>
                                            <span class="info-value"><?= esc($drama->language ?? 'Sinhala') ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Status:</span>
                                            <span class="info-value">
                                                <span class="status-badge <?= $drama->status === 'active' ? 'assigned' : 'pending' ?>">
                                                    <?= esc(ucfirst($drama->status)) ?>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <button class="btn btn-primary" style="flex: 1;" onclick="handlePMManage(<?=$drama->id?>)">
                                            <i class="bx bx-tasks"></i> Manage
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- As Actor Tab -->
                <div id="actor-tab" class="tab-content">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-user-tie"></i> Your Acting Roles</span>
                        </h3>
                    <?php if (!isset($roles_as_actor) || empty($roles_as_actor)): ?>
                        <div class="no-results">
                            <i class="bx bx-user-tie"></i>
                            <h3>No Acting Roles</h3>
                            <p>You haven't been cast in any roles yet. Browse available vacancies!</p>
                            <button class="btn btn-primary btn-compact" style="margin-top: 16px;" onclick="window.location.href='<?=ROOT?>/artistdashboard/browse_vacancies'">
                                <i class="bx bx-search"></i> Browse Vacancies
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($roles_as_actor as $role): ?>
                                <div class="artist-card">
                                    <div class="artist-header" style="background: linear-gradient(135deg, #d3a635, #b7881f); color: #2a1f08;">
                                        <h3 class="artist-name" style="color: #2a1f08;"><?= esc($role->role_name) ?></h3>
                                        <p class="artist-experience"><?= esc(ucfirst($role->role_type)) ?> Role</p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Drama:</span>
                                            <span class="info-value" style="color: var(--brand);">
                                                <strong><?= esc($role->drama_name) ?></strong>
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Director:</span>
                                            <span class="info-value"><?= esc($role->director_name ?? 'Unknown') ?></span>
                                        </div>
                                        <?php if (!empty($role->salary)): ?>
                                        <div class="info-row">
                                            <span class="info-label">Salary:</span>
                                            <span class="info-value">LKR <?= number_format($role->salary) ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="info-row">
                                            <span class="info-label">Assigned:</span>
                                            <span class="info-value"><?= date('M d, Y', strtotime($role->assigned_at)) ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Status:</span>
                                            <span class="info-value">
                                                <span class="status-badge assigned">
                                                    <i class="bx bx-check-circle"></i> Active
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <button class="btn btn-primary" style="flex: 1;" onclick="window.location.href='<?=ROOT?>/artistdashboard/view_drama?drama_id=<?=$role->drama_id?>'">
                                            <i class="bx bx-eye"></i> View Drama
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- View Interview Schedules Tab -->
                <div id="interviews-tab" class="tab-content">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-calendar-check"></i> View Interview Schedules</span>
                        </h3>
                        <?php if (isset($upcoming_interviews) && !empty($upcoming_interviews)): ?>
                            <p style="margin-bottom: 16px; color: #5a4b10;">Confirm your participation so the director knows you are joining.</p>
                            <div style="display: grid; gap: 16px;">
                                <?php foreach ($upcoming_interviews as $application): ?>
                                    <?php
                                        $interviewTime = date('M d, Y g:i A', strtotime($application->interview_at));
                                        $confirmationStatus = strtolower($application->interview_confirmation_status ?? 'pending');
                                        $statusPalette = [
                                            'confirmed' => 'background: rgba(40, 167, 69, 0.15); color: #155724;',
                                            'declined' => 'background: rgba(220, 53, 69, 0.15); color: #721c24;',
                                            'pending' => 'background: rgba(255, 193, 7, 0.2); color: #8a6d1a;',
                                        ];
                                        $badgeStyle = $statusPalette[$confirmationStatus] ?? $statusPalette['pending'];
                                    ?>
                                    <div class="role-info-card" style="border-left: 4px solid #e0a800;">
                                        <div style="display: flex; justify-content: space-between; gap: 12px; flex-wrap: wrap;">
                                            <div>
                                                <h4 style="margin: 0;"><?= esc($application->role_name ?? 'Role') ?> <small style="color: var(--muted); font-weight: normal;">in <?= esc($application->drama_name ?? 'Drama') ?></small></h4>
                                                <div style="font-size: 13px; color: var(--muted);">
                                                    Directed by <?= esc($application->director_name ?? 'Director') ?>
                                                </div>
                                            </div>
                                            <span class="status-badge" style="<?= $badgeStyle ?> text-transform: capitalize;">
                                                <?= esc($confirmationStatus) ?>
                                            </span>
                                        </div>
                                        <div class="role-info-item" style="margin-top: 12px;">
                                            <span class="role-info-label"><i class="bx bx-calendar"></i> Interview:</span>
                                            <span class="role-info-value"><?= esc($interviewTime) ?></span>
                                        </div>
                                        <?php if (!empty($application->interview_notes)): ?>
                                            <div style="margin-top: 12px; padding: 12px; background: rgba(0, 0, 0, 0.04); border-radius: 6px;">
                                                <strong>Director notes:</strong>
                                                <p style="margin: 6px 0 0; color: #4a4a4a; white-space: pre-wrap;"><?= nl2br(esc($application->interview_notes)) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($confirmationStatus === 'pending'): ?>
                                            <form method="POST" action="<?= ROOT ?>/artistdashboard/confirm_interview" class="interview-response" style="margin-top: 16px; display: flex; flex-direction: column; gap: 12px;">
                                                <input type="hidden" name="application_id" value="<?= (int)$application->id ?>">
                                                <label style="font-size: 13px; color: var(--muted);">Send an optional note to the director</label>
                                                <textarea name="note" rows="2" class="form-control" placeholder="Add details about your availability (optional)"></textarea>
                                                <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                                                    <button type="submit" name="response" value="confirm" class="btn btn-success" style="flex: 1; min-width: 140px;">
                                                        <i class="bx bx-check"></i> Confirm Attendance
                                                    </button>
                                                    <button type="submit" name="response" value="decline" class="btn btn-danger" style="flex: 1; min-width: 120px;">
                                                        <i class="bx bx-times"></i> Decline
                                                    </button>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <div style="margin-top: 12px; font-size: 13px; color: #555;">
                                                Response sent <?= !empty($application->interview_confirmed_at) ? esc(date('M d, Y g:i A', strtotime($application->interview_confirmed_at))) : 'recently' ?>
                                                <?php if (!empty($application->interview_confirmation_note)): ?>
                                                    <div style="margin-top: 6px; padding: 10px; background: rgba(0, 0, 0, 0.04); border-radius: 4px;">"<?= esc($application->interview_confirmation_note) ?>"</div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="bx bx-calendar-check"></i>
                                <h3>No Interview Schedules</h3>
                                <p>You don't have any upcoming interview schedules at the moment.</p>
                                <button class="btn btn-primary btn-compact" style="margin-top: 16px;" onclick="window.location.href='<?=ROOT?>/artistdashboard/browse_vacancies'">
                                    <i class="bx bx-search"></i> Browse Vacancies
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- My Showings Tab -->
                <div id="my-showings-tab" class="tab-content">
                    <h3 style="margin-bottom: 20px; color: var(--ink);">
                        <i class="bx bx-calendar-event"></i> My Showings
                    </h3>

                    <div class="classes-subtabs" role="tablist" aria-label="My showings tabs">
                        <button type="button" class="classes-subtab-btn active" data-showings-tab="requests" role="tab" aria-selected="true">
                            <i class="bx bx-time-five"></i> Showings Requests (<?= isset($show_requests_pending) ? count($show_requests_pending) : 0 ?>)
                        </button>
                        <button type="button" class="classes-subtab-btn" data-showings-tab="accepted" role="tab" aria-selected="false">
                            <i class="bx bx-check-circle"></i> Accepted Showings (<?= isset($show_requests_accepted) ? count($show_requests_accepted) : 0 ?>)
                        </button>
                        <button type="button" class="classes-subtab-btn" data-showings-tab="rejected" role="tab" aria-selected="false">
                            <i class="bx bx-x-circle"></i> Rejected Showings (<?= isset($show_requests_rejected) ? count($show_requests_rejected) : 0 ?>)
                        </button>
                    </div>

                    <div class="classes-subtab-panel active" data-showings-panel="requests" role="tabpanel">
                    <?php if (!empty($show_requests_pending)): ?>
                        <div style="display: grid; gap: 16px; margin-bottom: 18px;">
                            <?php foreach ($show_requests_pending as $show_request): ?>
                                <?php
                                    $requestDetails = [];
                                    if (!empty($show_request->request_details_json)) {
                                        $decoded = json_decode((string)$show_request->request_details_json, true);
                                        if (is_array($decoded)) {
                                            $requestDetails = $decoded;
                                        }
                                    }
                                    $requestedVenue = trim((string)($requestDetails['request_venue'] ?? 'Not specified'));
                                    $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
                                    $requestedShowTime = trim((string)($requestDetails['show_time'] ?? ''));
                                    $requestedSchedule = trim((string)($requestDetails['show_datetime'] ?? ''));
                                    $requestedStartRaw = trim((string)($requestDetails['request_start_at'] ?? ''));
                                    $requestedEndRaw = trim((string)($requestDetails['request_end_at'] ?? ''));
                                    $requestedStart = $requestedStartRaw !== '' ? date('M d, Y g:i A', strtotime($requestedStartRaw)) : 'Not specified';
                                    $requestedEnd = $requestedEndRaw !== '' ? date('M d, Y g:i A', strtotime($requestedEndRaw)) : 'Not specified';
                                    $requestedShowDate = $requestedShowDateRaw !== '' ? date('M d, Y', strtotime($requestedShowDateRaw)) : 'Not specified';
                                    if ($requestedShowTime === '' && $requestedSchedule !== '') {
                                        $timeParts = preg_split('/\s+/', $requestedSchedule, 2);
                                        $requestedShowTime = trim((string)($timeParts[1] ?? ''));
                                    }
                                    if ($requestedShowTime === '') {
                                        $requestedShowTime = 'Not specified';
                                    }
                                    if ($requestedSchedule === '' && ($requestedStartRaw !== '' || $requestedEndRaw !== '')) {
                                        $requestedSchedule = trim($requestedStart . ' to ' . $requestedEnd);
                                    }
                                    if ($requestedSchedule === '') {
                                        $requestedSchedule = 'Not specified';
                                    }
                                    $presentCount = (int)($requestDetails['present_count'] ?? 0);
                                    $requestNotes = trim((string)($requestDetails['request_notes'] ?? ''));
                                ?>
                                <div class="role-info-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 14px; gap: 12px;">
                                        <div>
                                            <h3 style="color: var(--brand); margin-bottom: 8px;"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p style="color: var(--muted); font-size: 13px; margin: 0;"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge requested"><i class="bx bx-time"></i> Pending</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-map"></i> Requested Place:</span><span class="role-info-value"><?= esc($requestedVenue) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate !== 'Not specified' ? $requestedShowDate : $requestedSchedule) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-group"></i> Expected Present Count:</span><span class="role-info-value"><?= $presentCount > 0 ? (int)$presentCount : 'Not specified' ?></span></div>
                                    <?php if ($requestNotes !== ''): ?>
                                        <div style="margin: 12px 0; padding: 12px; background: rgba(255,255,255,0.65); border-radius: 8px; border-left: 3px solid var(--brand);">
                                            <strong style="color: var(--ink);"><i class="bx bx-note"></i> Additional Notes:</strong>
                                            <p style="color: #555; margin-top: 6px; font-size: 14px;"><?= esc($requestNotes) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div style="display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap;">
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_show_request" style="flex: 1; min-width: 180px;">
                                            <input type="hidden" name="request_id" value="<?= (int)$show_request->id ?>">
                                            <input type="hidden" name="response" value="accept">
                                            <button type="submit" class="btn btn-success" style="width: 100%;"><i class="bx bx-check"></i> Accept Show</button>
                                        </form>
                                        <button type="button" class="btn btn-danger show-reject-reason-btn" data-target="reject-form-<?= (int)$show_request->id ?>" style="flex: 1; min-width: 180px;">
                                            <i class="bx bx-x"></i> Reject
                                        </button>
                                    </div>
                                    <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_show_request" id="reject-form-<?= (int)$show_request->id ?>" class="showings-reject-form" style="display: none; margin-top: 12px; gap: 8px;">
                                        <input type="hidden" name="request_id" value="<?= (int)$show_request->id ?>">
                                        <input type="hidden" name="response" value="reject">
                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Add reason for rejection" required></textarea>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <button type="submit" class="btn btn-danger" style="white-space: nowrap;" onclick="return confirm('Reject this show request with this reason?');"><i class="bx bx-send"></i> Submit Rejection</button>
                                            <button type="button" class="btn btn-outline reject-cancel-btn" data-target="reject-form-<?= (int)$show_request->id ?>" style="white-space: nowrap;">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results" style="margin-bottom: 18px;">
                            <i class="bx bx-inbox"></i>
                            <h3>No Pending Show Requests</h3>
                            <p>No audience show requests are waiting for your decision.</p>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div class="classes-subtab-panel" data-showings-panel="accepted" role="tabpanel">
                    <?php if (!empty($show_requests_accepted)): ?>
                        <div style="display: grid; gap: 16px; margin-bottom: 18px;">
                            <?php foreach ($show_requests_accepted as $show_request): ?>
                                <?php
                                    $requestDetails = [];
                                    if (!empty($show_request->request_details_json)) {
                                        $decoded = json_decode((string)$show_request->request_details_json, true);
                                        if (is_array($decoded)) {
                                            $requestDetails = $decoded;
                                        }
                                    }
                                    $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
                                    $requestedShowDate = $requestedShowDateRaw !== '' ? date('M d, Y', strtotime($requestedShowDateRaw)) : 'Not specified';
                                    $requestedShowTime = trim((string)($requestDetails['show_time'] ?? 'Not specified'));
                                ?>
                                <div class="role-info-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                                        <div>
                                            <h3 style="color: var(--brand); margin-bottom: 8px;"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p style="color: var(--muted); font-size: 13px; margin: 0;"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge success"><i class="bx bx-check-circle"></i> Accepted</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime !== '' ? $requestedShowTime : 'Not specified') ?></span></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results" style="margin-bottom: 18px;"><i class="bx bx-check-shield"></i><h3>No Accepted Showings</h3><p>You have not accepted any audience showings yet.</p></div>
                    <?php endif; ?>
                    </div>

                    <div class="classes-subtab-panel" data-showings-panel="rejected" role="tabpanel">
                    <?php if (!empty($show_requests_rejected)): ?>
                        <div style="display: grid; gap: 16px; margin-bottom: 10px;">
                            <?php foreach ($show_requests_rejected as $show_request): ?>
                                <?php
                                    $requestDetails = [];
                                    if (!empty($show_request->request_details_json)) {
                                        $decoded = json_decode((string)$show_request->request_details_json, true);
                                        if (is_array($decoded)) {
                                            $requestDetails = $decoded;
                                        }
                                    }
                                    $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
                                    $requestedShowDate = $requestedShowDateRaw !== '' ? date('M d, Y', strtotime($requestedShowDateRaw)) : 'Not specified';
                                    $requestedShowTime = trim((string)($requestDetails['show_time'] ?? 'Not specified'));
                                    $rejectionReason = trim((string)($show_request->rejection_reason ?? ''));
                                ?>
                                <div class="role-info-card" style="border-left: 4px solid #ef4444;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                                        <div>
                                            <h3 style="color: var(--brand); margin-bottom: 8px;"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p style="color: var(--muted); font-size: 13px; margin: 0;"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge danger"><i class="bx bx-x-circle"></i> Rejected</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime !== '' ? $requestedShowTime : 'Not specified') ?></span></div>
                                    <div style="margin-top: 12px; padding: 10px; background: rgba(239, 68, 68, 0.08); border-radius: 8px; border-left: 3px solid #ef4444;">
                                        <strong style="color: #9f1239;"><i class="bx bx-error-circle"></i> Rejection Reason:</strong>
                                        <p style="margin: 6px 0 0; color: #7f1d1d;"><?= esc($rejectionReason !== '' ? $rejectionReason : 'No reason provided.') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results" style="margin-bottom: 10px;"><i class="bx bx-smile"></i><h3>No Rejected Showings</h3><p>No rejected audience show requests found.</p></div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- Requests Tab -->
                <div id="requests-tab" class="tab-content">
                    
                    <!-- Production Manager Requests -->
                    <?php if (isset($pm_requests) && !empty($pm_requests)): ?>
                        <h3 style="margin-bottom: 20px; color: var(--ink);">
                            <i class="bx bx-user-tie"></i> Production Manager Requests
                        </h3>
                        <div style="display: grid; gap: 16px; margin-bottom: 40px;">
                            <?php foreach ($pm_requests as $pm_request): ?>
                                <div class="role-info-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                        <div>
                                            <h3 style="color: var(--brand); margin-bottom: 8px;">
                                                <i class="bx bx-film"></i> <?= esc($pm_request->drama_name) ?>
                                            </h3>
                                            <p style="color: var(--muted); font-size: 13px;">
                                                <strong>Director:</strong> <?= esc($pm_request->director_name) ?>
                                            </p>
                                            <p style="color: var(--muted); font-size: 13px;">
                                                <strong>Certificate:</strong> <?= esc($pm_request->certificate_number) ?>
                                            </p>
                                        </div>
                                        <span class="status-badge requested">
                                            <i class="bx bx-clock"></i> Pending
                                        </span>
                                    </div>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="bx bx-briefcase"></i> Position:
                                        </span>
                                        <span class="role-info-value">Production Manager</span>
                                    </div>
                                    
                                    <?php if (!empty($pm_request->message)): ?>
                                        <div style="margin: 12px 0; padding: 12px; background: rgba(186, 142, 35, 0.08); border-radius: 8px; border-left: 3px solid var(--brand);">
                                            <strong style="color: var(--ink);"><i class="bx bx-comment"></i> Message from Director:</strong>
                                            <p style="color: #555; margin-top: 6px; font-size: 14px;"><?= esc($pm_request->message) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="bx bx-calendar"></i> Requested:
                                        </span>
                                        <span class="role-info-value"><?= date('M d, Y g:i A', strtotime($pm_request->requested_at)) ?></span>
                                    </div>
                                    
                                    <div style="margin-top: 12px; padding: 10px; background: rgba(33, 150, 243, 0.08); border-radius: 6px;">
                                        <p style="color: #1976d2; font-size: 13px; margin: 0;">
                                            <i class="bx bx-info-circle"></i> <strong>About this role:</strong> 
                                            As Production Manager, you'll oversee services, budget management, and theater bookings for this drama.
                                        </p>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                            <input type="hidden" name="response" value="accept">
                                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                                <i class="bx bx-check"></i> Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                            <input type="hidden" name="response" value="reject">
                                            <button type="submit" class="btn btn-danger" style="width: 100%;" 
                                                    onclick="return confirm('Are you sure you want to decline this Production Manager request?');">
                                                <i class="bx bx-times"></i> Decline
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Role Requests -->
                    <h3 style="margin-bottom: 20px; color: var(--ink);">
                        <i class="bx bx-theater-masks"></i> Role Requests
                    </h3>
                    <?php if (!isset($role_requests) || empty($role_requests)): ?>
                        <?php if (!isset($pm_requests) || empty($pm_requests)): ?>
                            <div class="no-results">
                                <i class="bx bx-inbox"></i>
                                <h3>No Pending Requests</h3>
                                <p>You don't have any requests at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="bx bx-inbox"></i>
                                <h3>No Pending Role Requests</h3>
                                <p>You don't have any role requests at the moment.</p>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div style="display: grid; gap: 16px;">
                            <?php foreach ($role_requests as $request): ?>
                                <div class="role-info-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                        <div>
                                            <h3 style="color: var(--ink); margin-bottom: 8px;">
                                                <i class="bx bx-theater-masks"></i> <?= esc($request->drama_name) ?>
                                            </h3>
                                            <p style="color: var(--muted); font-size: 13px;">
                                                <strong>Director:</strong> <?= esc($request->director_name) ?>
                                            </p>
                                        </div>
                                        <span class="status-badge requested">
                                            <i class="bx bx-clock"></i> Pending
                                        </span>
                                    </div>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="bx bx-user-tag"></i> Role:
                                        </span>
                                        <span class="role-info-value"><?= esc($request->role_name) ?></span>
                                    </div>
                                    
                                    <?php if (!empty($request->role_description)): ?>
                                        <div style="margin: 12px 0; padding: 12px; background: rgba(255,255,255,0.6); border-radius: 8px;">
                                            <strong style="color: var(--ink);">Description:</strong>
                                            <p style="color: #555; margin-top: 6px; font-size: 14px;"><?= esc($request->role_description) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($request->salary)): ?>
                                        <div class="role-info-item">
                                            <span class="role-info-label">
                                                <i class="bx bx-money-bill-wave"></i> Salary:
                                            </span>
                                            <span class="role-info-value">LKR <?= number_format($request->salary) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="bx bx-calendar"></i> Requested:
                                        </span>
                                        <span class="role-info-value"><?= isset($request->requested_at) && $request->requested_at ? date('M d, Y', strtotime($request->requested_at)) : 'N/A' ?></span>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                            <input type="hidden" name="response" value="accept">
                                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                                <i class="bx bx-check"></i> Accept Role
                                            </button>
                                        </form>
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                            <input type="hidden" name="response" value="reject">
                                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                                <i class="bx bx-times"></i> Decline
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                </div>
            </div>
        </div>
    </main>

    <script>
        function openTabLink(evt, tabName) {
            evt.preventDefault();
            
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tab links
            const tabButtons = document.getElementsByClassName('nav-tab-btn');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            
            // Show the selected tab and mark button as active
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');

            const tabToHash = {
                'director-tab': 'director',
                'manager-tab': 'manager',
                'actor-tab': 'actor',
                'interviews-tab': 'interviews',
                'my-showings-tab': 'my-showings',
                'requests-tab': 'requests'
            };
            const hash = tabToHash[tabName] || 'director';
            if (window.location.hash !== '#' + hash) {
                history.replaceState(null, '', '#' + hash);
            }
        }

        function activateTabFromHash() {
            const hashMap = {
                '#director': 'director-tab',
                '#manager': 'manager-tab',
                '#actor': 'actor-tab',
                '#interviews': 'interviews-tab',
                '#my-showings': 'my-showings-tab',
                '#requests': 'requests-tab'
            };

            const targetTab = hashMap[window.location.hash || ''];
            if (!targetTab) {
                return;
            }

            const tabButton = document.querySelector('.nav-tab-btn[onclick*="' + targetTab + '"]');
            if (tabButton) {
                openTabLink({
                    preventDefault: function () {},
                    currentTarget: tabButton
                }, targetTab);
                return;
            }

            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }

            const tabButtons = document.getElementsByClassName('nav-tab-btn');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }

            const targetPanel = document.getElementById(targetTab);
            if (targetPanel) {
                targetPanel.classList.add('active');
            }
        }

        function syncSidebarWithHash() {
            if (window.location.hash !== '#my-showings') {
                return;
            }

            const menuItems = document.querySelectorAll('.sidebar .menu li');
            menuItems.forEach(function (item) {
                item.classList.remove('active');
            });

            const showingsLink = document.querySelector('.sidebar .menu a[href*="#my-showings"]');
            if (showingsLink && showingsLink.parentElement) {
                showingsLink.parentElement.classList.add('active');
            }
        }

        function updateShowingsOnlyMode() {
            document.body.classList.toggle('showings-only', window.location.hash === '#my-showings');
        }

        function initArtistShowingsTabs() {
            const showingsView = document.getElementById('my-showings-tab');
            if (!showingsView) {
                return;
            }

            const buttons = showingsView.querySelectorAll('.classes-subtab-btn[data-showings-tab]');
            const panels = showingsView.querySelectorAll('.classes-subtab-panel[data-showings-panel]');

            if (!buttons.length || !panels.length) {
                return;
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const target = button.getAttribute('data-showings-tab');

                    buttons.forEach(function (btn) {
                        const isActive = btn === button;
                        btn.classList.toggle('active', isActive);
                        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                    });

                    panels.forEach(function (panel) {
                        panel.classList.toggle('active', panel.getAttribute('data-showings-panel') === target);
                    });
                });
            });
        }

        function initShowRequestRejectForms() {
            const rejectButtons = document.querySelectorAll('.show-reject-reason-btn[data-target]');
            const cancelButtons = document.querySelectorAll('.reject-cancel-btn[data-target]');

            rejectButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const formId = button.getAttribute('data-target');
                    const form = document.getElementById(formId);
                    if (!form) {
                        return;
                    }

                    form.style.display = 'grid';
                    const textarea = form.querySelector('textarea[name="rejection_reason"]');
                    if (textarea) {
                        textarea.focus();
                    }
                });
            });

            cancelButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const formId = button.getAttribute('data-target');
                    const form = document.getElementById(formId);
                    if (!form) {
                        return;
                    }

                    const textarea = form.querySelector('textarea[name="rejection_reason"]');
                    if (textarea) {
                        textarea.value = '';
                    }

                    form.style.display = 'none';
                });
            });
        }

        function handleDirectorManage(dramaId) {
            const url = '<?=ROOT?>/director/dashboard?drama_id=' + dramaId;
            console.log('Director manage - Navigating to:', url);
            window.location.href = url;
        }

        function handlePMManage(dramaId) {
            const url = '<?=ROOT?>/Production_manager/dashboard?drama_id=' + dramaId;
            console.log('PM manage - Navigating to:', url);
            window.location.href = url;
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

        window.addEventListener('hashchange', function () {
            activateTabFromHash();
            syncSidebarWithHash();
            updateShowingsOnlyMode();
        });
        activateTabFromHash();
        syncSidebarWithHash();
        updateShowingsOnlyMode();
        initArtistShowingsTabs();
        initShowRequestRejectForms();
    </script>
</body>
</html>
