<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
}

$profileImageSrc = ROOT . '/assets/images/default-avatar.jpg';
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
    <title>Artist Dashboard - Rangamadala</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand: #ba8e23;
            --brand-strong: #a0781e;
            --brand-soft: rgba(186, 142, 35, 0.12);
            --ink: #1f2933;
            --muted: #6b7280;
            --card: #ffffff;
            --bg: #f5f5f5;
            --border: #e0e0e0;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
            --radius: 12px;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 8px 32px rgba(0, 0, 0, 0.12);
            --shadow-lg: 0 15px 40px rgba(0, 0, 0, 0.16);
            --transition: all 0.25s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', 'Arial', sans-serif;
            color: var(--ink);
            background: var(--bg);
            min-height: 100vh;
            display: flex;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 72px;
            height: 100vh;
            padding: 0;
            color: #fff;
            overflow: hidden;
            transition: width 0.4s ease;
            background: var(--brand);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            z-index: 1000;
        }

        .sidebar:hover {
            width: 240px;
        }

        .logo {
            height: 80px;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo h2 {
            font-size: 28px;
            margin: 0;
            min-width: 40px;
            text-align: center;
        }

        .logo span {
            font-size: 18px;
            font-weight: 700;
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar:hover .logo span {
            opacity: 1;
        }

        .menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu li {
            margin: 0;
            transition: var(--transition);
        }

        .menu li:hover,
        .menu li.active {
            background: rgba(255, 255, 255, 0.16);
        }

        .menu a {
            color: #fff;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            white-space: nowrap;
            padding: 16px 20px;
            transition: var(--transition);
        }

        .menu a i {
            font-size: 1.2rem;
            min-width: 20px;
            text-align: center;
        }

        .menu a span {
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar:hover .menu a span {
            opacity: 1;
        }

        .main--content {
            margin-left: 72px;
            padding: 20px;
            min-height: 100vh;
            flex: 1;
        }

        .header--wrapper {
            background: var(--card);
            padding: 20px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }

        .header--title h1,
        .header--title h2 {
            color: var(--ink);
            margin-top: 4px;
        }

        .header--title span {
            color: var(--brand);
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .user--info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user--info img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 3px solid var(--brand);
            object-fit: cover;
        }

        .role-badge {
            background: var(--brand);
            color: #fff;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn {
            padding: 10px 16px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--brand), var(--brand-strong));
            color: #fff;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: linear-gradient(45deg, var(--success), #1f9b3b);
            color: #fff;
        }

        .btn-danger {
            background: linear-gradient(45deg, var(--danger), #c82333);
            color: #fff;
        }

        .btn-warning {
            background: linear-gradient(45deg, var(--warning), #e0a800);
            color: #1f1f1f;
        }

        .content,
        .artist-card {
            background: var(--card);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 26px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            color: #fff;
            padding: 20px;
            border-radius: var(--radius);
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .stat-card h3 {
            font-size: 24px;
            margin-bottom: 6px;
        }

        .stat-card p {
            opacity: 0.9;
            font-size: 14px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .assigned {
            background: #d4edda;
            color: #155724;
        }

        .pending {
            background: #fff3cd;
            color: #856404;
        }

        .requested {
            background: #d1ecf1;
            color: #0c5460;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 26px;
            border-bottom: 2px solid var(--border);
        }

        .tab-button {
            padding: 12px 18px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            color: #999;
            border-bottom: 3px solid transparent;
            transition: var(--transition);
        }

        .tab-button.active {
            color: var(--brand);
            border-bottom-color: var(--brand);
        }

        .tab-button:hover {
            color: var(--ink);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .artists-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 18px;
        }

        .artist-card {
            overflow: hidden;
            transition: var(--transition);
        }

        .artist-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg);
        }

        .artist-header {
            background: linear-gradient(135deg, rgba(186, 142, 35, 0.9), rgba(186, 142, 35, 0.75));
            color: #fff;
            padding: 18px;
            text-align: center;
        }

        .artist-name {
            font-size: 18px;
            font-weight: 700;
        }

        .artist-experience {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }

        .artist-body {
            padding: 18px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(222, 226, 230, 0.7);
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #6c757d;
            font-weight: 700;
        }

        .info-value {
            color: var(--ink);
            font-weight: 600;
            text-align: right;
        }

        .artist-footer {
            padding: 16px;
            border-top: 1px solid rgba(222, 226, 230, 0.7);
            display: flex;
            gap: 10px;
        }

        .no-results {
            text-align: center;
            padding: 50px 16px;
            color: #6c757d;
        }

        .no-results i {
            font-size: 46px;
            margin-bottom: 16px;
            opacity: 0.55;
        }

        .no-results h3 {
            color: var(--ink);
            margin-bottom: 8px;
        }

        .role-info-card {
            background: var(--brand-soft);
            border-left: 4px solid var(--brand);
            padding: 16px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .role-info-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(186, 142, 35, 0.2);
        }

        .role-info-item:last-child {
            border-bottom: none;
        }

        .role-info-label {
            font-weight: 700;
            color: var(--ink);
            flex: 1;
        }

        .role-info-value {
            color: var(--brand);
            font-weight: 700;
            flex: 1;
            text-align: right;
        }

        .info-box {
            background: #f0f7ff;
            color: #4b5563;
            font-size: 13px;
            border-left: 4px solid var(--brand);
            padding: 16px;
            margin-bottom: 20px;
            border-radius: var(--radius);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
            <span>Rangamadala</span>
        </div>
        <ul class="menu">
            <li class="active">
                <a href="<?=ROOT?>/artistdashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistprofile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/browseDramas">
                    <i class="fas fa-theater-masks"></i>
                    <span>Browse Dramas</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/classes">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>Classes</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/portfolio">
                    <i class="fas fa-briefcase"></i>
                    <span>Portfolio</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main--content">
        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Welcome Back</span>
                <h1><?= isset($user->full_name) ? esc($user->full_name) : 'Artist' ?></h1>
            </div>
            <div class="user--info">
                <div>
                    <h4><?= isset($user->full_name) ? esc($user->full_name) : 'Artist' ?></h4>
                    <small class="role-badge">
                        <i class="fas fa-star"></i> Artist
                    </small>
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-theater-masks" style="font-size: 32px; margin-bottom: 8px;"></i>
                <h3><?= isset($stats['total_dramas']) ? $stats['total_dramas'] : 0 ?></h3>
                <p>Total Dramas</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #17a2b8, #138496);">
                <i class="fas fa-film" style="font-size: 32px; margin-bottom: 8px;"></i>
                <h3><?= isset($stats['as_director']) ? $stats['as_director'] : 0 ?></h3>
                <p>As Director</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745, #1f9b3b);">
                <i class="fas fa-briefcase" style="font-size: 32px; margin-bottom: 8px;"></i>
                <h3><?= isset($stats['as_manager']) ? $stats['as_manager'] : 0 ?></h3>
                <p>As Production Manager</p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #ffc107, #e0a800);">
                <i class="fas fa-user-tie" style="font-size: 32px; margin-bottom: 8px;"></i>
                <h3><?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?></h3>
                <p>As Actor</p>
            </div>
        </div>

        <!-- Tabs for Drama Categories -->
        <div class="content">
            <div style="padding: 24px;">
                <div class="tabs">
                    <button class="tab-button active" onclick="openTab(event, 'director-tab')">
                        <i class="fas fa-film"></i> As Director (<?= isset($stats['as_director']) ? $stats['as_director'] : 0 ?>)
                    </button>
                    <button class="tab-button" onclick="openTab(event, 'manager-tab')">
                        <i class="fas fa-briefcase"></i> As Production Manager (<?= isset($stats['as_manager']) ? $stats['as_manager'] : 0 ?>)
                    </button>
                    <button class="tab-button" onclick="openTab(event, 'actor-tab')">
                        <i class="fas fa-user-tie"></i> As Actor (<?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?>)
                    </button>
                    <button class="tab-button" onclick="openTab(event, 'requests-tab')">
                        <i class="fas fa-envelope"></i> Requests (<?= isset($stats['pending_requests']) ? $stats['pending_requests'] : 0 ?>)
                    </button>
                </div>

                <!-- As Director Tab -->
                <div id="director-tab" class="tab-content active">
                    <h3 style="margin-bottom: 20px; color: var(--ink);">
                        <i class="fas fa-film"></i> Dramas You're Directing
                    </h3>
                    <?php if (!isset($dramas_as_director) || empty($dramas_as_director)): ?>
                        <div class="no-results">
                            <i class="fas fa-film"></i>
                            <h3>No Dramas Yet</h3>
                            <p>You haven't created any dramas. Start your journey as a director!</p>
                            <button class="btn btn-primary" style="margin-top: 16px;" onclick="window.location.href='<?=ROOT?>/createDrama'">
                                <i class="fas fa-plus"></i> Create Drama
                            </button>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h3 style="margin: 0; color: var(--ink);">
        
                            </h3>
                            <button class="btn btn-primary" onclick="window.location.href='<?=ROOT?>/createDrama'">
                                <i class="fas fa-plus"></i> Create New Drama
                            </button>
                        </div>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_director as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header" style="background: linear-gradient(135deg, #17a2b8, #138496);">
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
                                        <button class="btn btn-primary" style="flex: 1;" onclick="window.location.href='<?=ROOT?>/director/dashboard?drama_id=<?=$drama->id?>'">
                                            <i class="fas fa-tachometer-alt"></i> Manage
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- As Production Manager Tab -->
                <div id="manager-tab" class="tab-content">
                    <h3 style="margin-bottom: 20px; color: var(--ink);">
                        <i class="fas fa-briefcase"></i> Dramas You're Managing as Production Manager
                    </h3>
                    <?php if (!isset($dramas_as_manager) || empty($dramas_as_manager)): ?>
                        <div class="no-results">
                            <i class="fas fa-briefcase"></i>
                            <h3>No Production Manager Roles</h3>
                            <p>You haven't been assigned as a production manager for any dramas yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_manager as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header" style="background: linear-gradient(135deg, #28a745, #1f9b3b);">
                                        <h3 class="artist-name"><?= esc($drama->title) ?></h3>
                                        <p class="artist-experience"><?= esc($drama->genre ?? 'Drama') ?></p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Director:</span>
                                            <span class="info-value"><?= esc($drama->director_name ?? 'Unknown') ?></span>
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
                                        <button class="btn btn-success" style="flex: 1;" onclick="window.location.href='<?=ROOT?>/productionmanager/dashboard?drama_id=<?=$drama->id?>'">
                                            <i class="fas fa-tasks"></i> Manage
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- As Actor Tab -->
                <div id="actor-tab" class="tab-content">
                    <h3 style="margin-bottom: 20px; color: var(--ink);">
                        <i class="fas fa-user-tie"></i> Dramas You're Acting In
                    </h3>
                    <?php if (!isset($dramas_as_actor) || empty($dramas_as_actor)): ?>
                        <div class="no-results">
                            <i class="fas fa-user-tie"></i>
                            <h3>No Acting Roles</h3>
                            <p>You haven't been cast in any dramas yet. Browse available roles!</p>
                            <button class="btn btn-primary" style="margin-top: 16px;" onclick="window.location.href='<?=ROOT?>/browseDramas'">
                                <i class="fas fa-search"></i> Browse Roles
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_actor as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header" style="background: linear-gradient(135deg, #ffc107, #e0a800); color: #1f1f1f;">
                                        <h3 class="artist-name" style="color: #1f1f1f;"><?= esc($drama->title) ?></h3>
                                        <p class="artist-experience"><?= esc($drama->genre ?? 'Drama') ?></p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Your Role:</span>
                                            <span class="info-value" style="color: var(--warning);">
                                                <strong><?= esc($drama->role_name ?? 'Actor') ?></strong>
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Director:</span>
                                            <span class="info-value"><?= esc($drama->director_name ?? 'Unknown') ?></span>
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
                                        <button class="btn btn-warning" style="flex: 1;" onclick="window.location.href='<?=ROOT?>/drama/details?drama_id=<?=$drama->id?>'">
                                            <i class="fas fa-eye"></i> View Details
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Requests Tab -->
                <div id="requests-tab" class="tab-content">
                    <h3 style="margin-bottom: 20px; color: var(--ink);">
                        <i class="fas fa-envelope"></i> Role Requests
                    </h3>
                    <?php if (!isset($role_requests) || empty($role_requests)): ?>
                        <div class="no-results">
                            <i class="fas fa-inbox"></i>
                            <h3>No Pending Requests</h3>
                            <p>You don't have any role requests at the moment.</p>
                        </div>
                    <?php else: ?>
                        <div style="display: grid; gap: 16px;">
                            <?php foreach ($role_requests as $request): ?>
                                <div class="role-info-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                        <div>
                                            <h3 style="color: var(--ink); margin-bottom: 8px;">
                                                <i class="fas fa-theater-masks"></i> <?= esc($request->drama_title) ?>
                                            </h3>
                                            <p style="color: var(--muted); font-size: 13px;">
                                                <strong>Director:</strong> <?= esc($request->director_name) ?>
                                            </p>
                                        </div>
                                        <span class="status-badge requested">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    </div>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="fas fa-user-tag"></i> Role:
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
                                                <i class="fas fa-money-bill-wave"></i> Salary:
                                            </span>
                                            <span class="role-info-value">LKR <?= number_format($request->salary) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="fas fa-calendar"></i> Requested:
                                        </span>
                                        <span class="role-info-value"><?= date('M d, Y', strtotime($request->request_date)) ?></span>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                            <input type="hidden" name="response" value="accept">
                                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                                <i class="fas fa-check"></i> Accept Role
                                            </button>
                                        </form>
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                            <input type="hidden" name="response" value="reject">
                                            <button type="submit" class="btn btn-danger" style="width: 100%;">
                                                <i class="fas fa-times"></i> Decline
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

    <script>
        function openTab(evt, tabName) {
            // Hide all tab contents
            const tabContents = document.getElementsByClassName('tab-content');
            for (let i = 0; i < tabContents.length; i++) {
                tabContents[i].classList.remove('active');
            }
            
            // Remove active class from all tab buttons
            const tabButtons = document.getElementsByClassName('tab-button');
            for (let i = 0; i < tabButtons.length; i++) {
                tabButtons[i].classList.remove('active');
            }
            
            // Show the selected tab and mark button as active
            document.getElementById(tabName).classList.add('active');
            evt.currentTarget.classList.add('active');
        }
    </script>
</body>
</html>
