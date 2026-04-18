<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
}

$profileImageSrc = isset($profileImageSrc) && is_string($profileImageSrc) && $profileImageSrc !== ''
    ? $profileImageSrc
    : ROOT . '/uploads/profile_images/user_profile.png';

$sidebarActiveDefaults = [
    'dashboard' => false,
    'notifications' => false,
    'vacancies' => false,
    'classes' => false,
    'showings' => false,
    'calendar' => false,
];

$sidebarActive = (isset($sidebarActive) && is_array($sidebarActive))
    ? array_merge($sidebarActiveDefaults, $sidebarActive)
    : $sidebarActiveDefaults;

$toastSuccessMessage = isset($toastSuccessMessage) ? (string)$toastSuccessMessage : '';
$toastErrorMessage = isset($toastErrorMessage) ? (string)$toastErrorMessage : '';
$infoMessage = isset($infoMessage) ? (string)$infoMessage : '';
$infoMessageType = isset($infoMessageType) ? (string)$infoMessageType : 'info';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/toast.css">
        <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/artistdashboard-page.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
<?php if (false): ?>
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
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
            width: 100%;
        }

        .artist-stats-grid .artist-stat-card {
            min-width: 0;
            width: 100%;
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

        .vacancies-banner .btn.btn-primary.btn-compact {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            color: #4a3a14;
            border: 1px solid #f0dfb4;
            box-shadow: 0 4px 12px rgba(186, 142, 35, 0.12);
        }

        .vacancies-banner {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            color: #4a3a14;
            border: 1px solid #f0dfb4;
            padding: 30px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            box-shadow: 0 4px 12px rgba(186, 142, 35, 0.12);
        }

        .vacancies-banner h2 {
            color: #4a3a14;
        }

        .vacancies-banner p {
            color: #6a5120;
        }

        .vacancies-banner .btn.btn-primary.btn-compact:hover {
            background: linear-gradient(180deg, #fffaf0 0%, #fff2da 100%);
            color: #3f2f12;
        }

        /* Drama cards: light design style for Director/Manager/Actor tabs */
        #director-tab .artist-card,
        #manager-tab .artist-card,
        #actor-tab .artist-card {
            border: 1px solid #e8cf97;
            border-radius: 16px;
            background: linear-gradient(180deg, #fffefb 0%, #fff8ea 100%);
            box-shadow: 0 6px 16px rgba(186, 142, 35, 0.12);
        }

        #director-tab .artist-card:hover,
        #manager-tab .artist-card:hover,
        #actor-tab .artist-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(186, 142, 35, 0.2);
        }

        #director-tab .artist-header,
        #manager-tab .artist-header,
        #actor-tab .artist-header {
            background: linear-gradient(180deg, #f7e8c1 0%, #ead094 100%) !important;
            color: #3f2f12 !important;
            border-bottom: 1px solid #ddbc73;
            text-align: left;
            padding: 20px;
        }

        #director-tab .artist-header .artist-name,
        #manager-tab .artist-header .artist-name,
        #actor-tab .artist-header .artist-name {
            color: #2f2410 !important;
            margin-bottom: 8px;
            font-size: 22px;
            line-height: 1.15;
        }

        #director-tab .artist-header .artist-experience,
        #manager-tab .artist-header .artist-experience,
        #actor-tab .artist-header .artist-experience {
            color: #6a5120;
            opacity: 1;
            text-transform: none;
            letter-spacing: 0;
            font-size: 17px;
        }

        #director-tab .artist-body,
        #manager-tab .artist-body,
        #actor-tab .artist-body {
            background: transparent;
            padding: 18px 20px;
        }

        #director-tab .info-row,
        #manager-tab .info-row,
        #actor-tab .info-row {
            margin-bottom: 0;
            padding: 10px 0;
            border-bottom: 1px dashed #ead8a9;
        }

        #director-tab .artist-footer,
        #manager-tab .artist-footer,
        #actor-tab .artist-footer {
            background: #fffdf7;
            border-top: 1px solid #ecd9ad;
            padding: 14px 20px 18px;
        }

        #director-tab .artist-footer .btn,
        #manager-tab .artist-footer .btn,
        #actor-tab .artist-footer .btn {
            min-height: 44px;
            border-radius: 10px;
            font-weight: 700;
        }

        #director-tab .artist-footer .btn-primary,
        #manager-tab .artist-footer .btn-primary,
        #actor-tab .artist-footer .btn-primary,
        #director-tab .btn-director-publish {
            background: linear-gradient(135deg, #d8b566 0%, #c59b3d 100%);
            color: #2f2410;
            border: 1px solid #c9a14a;
            box-shadow: 0 6px 14px rgba(186, 142, 35, 0.2);
        }

        #director-tab .artist-footer .btn-primary:hover,
        #manager-tab .artist-footer .btn-primary:hover,
        #actor-tab .artist-footer .btn-primary:hover,
        #director-tab .btn-director-publish:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(186, 142, 35, 0.3);
        }

        #director-tab .director-cert-link {
            color: #9a7318;
            font-weight: 700;
        }

        #director-tab .director-cert-link:hover {
            color: #7f5f13;
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

        @media (max-width: 1200px) {
            .artist-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 680px) {
            .artist-stats-grid {
                grid-template-columns: 1fr;
            }
        }

        #my-showings-tab .classes-subtabs {
            display: flex;
            gap: 0;
            margin-bottom: 26px;
            border-bottom: 2px solid var(--border);
            background: var(--card);
            border-radius: var(--radius) var(--radius) 0 0;
            box-shadow: var(--shadow-sm);
            overflow-x: auto;
            scroll-behavior: smooth;
        }

        #my-showings-tab .classes-subtab-btn {
            padding: 14px 20px;
            border: none;
            background: transparent;
            color: var(--muted);
            min-height: 0;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            line-height: 1;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
            position: relative;
        }

        #my-showings-tab .classes-subtabs::-webkit-scrollbar {
            height: 4px;
        }

        #my-showings-tab .classes-subtabs::-webkit-scrollbar-track {
            background: transparent;
        }

        #my-showings-tab .classes-subtabs::-webkit-scrollbar-thumb {
            background: rgba(186, 142, 35, 0.3);
            border-radius: 4px;
        }

        #my-showings-tab .classes-subtabs::-webkit-scrollbar-thumb:hover {
            background: rgba(186, 142, 35, 0.6);
        }

        #my-showings-tab .classes-subtab-btn i {
            font-size: 16px;
            line-height: 1;
        }

        #my-showings-tab .classes-subtab-btn:hover {
            color: var(--ink);
            background: rgba(186, 142, 35, 0.05);
        }

        #my-showings-tab .classes-subtab-btn.active {
            color: var(--brand);
            border-bottom-color: var(--brand);
            background: rgba(186, 142, 35, 0.08);
        }

        #my-showings-tab .classes-subtab-panel {
            display: none;
        }

        #my-showings-tab .classes-subtab-panel.active {
            display: block;
        }

        #my-showings-tab .accepted-showings-toolbar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin-bottom: 14px;
            padding: 12px;
            border: 1px solid #ead7a4;
            border-radius: 12px;
            background: linear-gradient(180deg, #fffefb 0%, #fff8e9 100%);
        }

        #my-showings-tab .accepted-showings-toolbar label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #7a6121;
            margin-bottom: 6px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        #my-showings-tab .accepted-showings-toolbar input {
            width: 100%;
            border: 1px solid #e1cb95;
            border-radius: 10px;
            padding: 10px 12px;
            background: #fffefb;
            color: #2f2f2f;
            font-size: 14px;
        }

        #my-showings-tab .accepted-showings-toolbar .btn {
            align-self: end;
            height: 42px;
        }

        #my-showings-tab .accepted-showings-empty {
            display: none;
            margin-bottom: 16px;
            padding: 12px;
            border-radius: 10px;
            border: 1px dashed #d9c28a;
            color: #7a6121;
            background: rgba(255, 248, 233, 0.8);
            font-size: 14px;
        }

        #my-showings-tab .pending-slot-conflict {
            display: none;
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            border-left: 3px solid #f59e0b;
            background: rgba(245, 158, 11, 0.12);
            color: #92400e;
            font-size: 13px;
        }

        #my-showings-tab .role-info-card.pending-showing-card,
        #my-showings-tab .role-info-card.accepted-showing-card,
        #my-showings-tab .role-info-card.rejected-showing-card {
            background: linear-gradient(180deg, #f3f1e9 0%, #ece8dc 100%);
            border: 1px solid #ded4bc;
            border-left: 4px solid #be9227;
            border-radius: 14px;
            padding: 18px 18px 16px;
            box-shadow: 0 6px 14px rgba(80, 67, 36, 0.08);
        }

        #my-showings-tab .showing-card-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 12px;
            margin-bottom: 12px;
        }

        #my-showings-tab .showing-card-title {
            margin: 0 0 6px;
            color: #b58514;
            font-size: 30px;
            line-height: 1.2;
        }

        #my-showings-tab .showing-card-audience {
            margin: 0;
            color: #5f6b7c;
            font-size: 14px;
        }

        #my-showings-tab .accepted-showing-card .status-badge.success {
            background: transparent;
            color: #0f172a;
            border: none;
            padding: 0;
            font-size: 12px;
            letter-spacing: 0.8px;
        }

        #my-showings-tab .pending-showing-card .status-badge.requested,
        #my-showings-tab .rejected-showing-card .status-badge.danger {
            font-size: 12px;
        }

        #my-showings-tab .pending-showing-card .role-info-item,
        #my-showings-tab .accepted-showing-card .role-info-item,
        #my-showings-tab .rejected-showing-card .role-info-item {
            display: grid;
            grid-template-columns: minmax(250px, 42%) minmax(0, 1fr);
            align-items: center;
            gap: 14px;
            padding: 10px 0;
            border-bottom: 1px solid #d9cfb5;
        }

        #my-showings-tab .pending-showing-card .role-info-item:last-of-type,
        #my-showings-tab .accepted-showing-card .role-info-item:last-of-type,
        #my-showings-tab .rejected-showing-card .role-info-item:last-of-type {
            border-bottom: none;
        }

        #my-showings-tab .pending-showing-card .role-info-label,
        #my-showings-tab .accepted-showing-card .role-info-label,
        #my-showings-tab .rejected-showing-card .role-info-label {
            color: #0f2744;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
        }

        #my-showings-tab .pending-showing-card .role-info-value,
        #my-showings-tab .accepted-showing-card .role-info-value,
        #my-showings-tab .rejected-showing-card .role-info-value {
            color: #b58514;
            text-align: right;
            font-weight: 700;
            font-size: 15px;
            line-height: 1.35;
            word-break: break-word;
        }

        #my-showings-tab .pending-showing-card .role-info-label i,
        #my-showings-tab .accepted-showing-card .role-info-label i,
        #my-showings-tab .rejected-showing-card .role-info-label i {
            color: #0f2744;
            margin-right: 6px;
        }

        #my-showings-tab .pending-showing-card .btn-success {
            background: linear-gradient(135deg, #be9227, #a67d1e);
            border: 1px solid rgba(145, 108, 24, 0.35);
            box-shadow: 0 4px 10px rgba(166, 125, 30, 0.2);
        }

        @media (max-width: 768px) {
            #my-showings-tab .pending-showing-card .role-info-item,
            #my-showings-tab .accepted-showing-card .role-info-item,
            #my-showings-tab .rejected-showing-card .role-info-item {
                grid-template-columns: 1fr;
                align-items: start;
                gap: 4px;
            }

            #my-showings-tab .pending-showing-card .role-info-value,
            #my-showings-tab .accepted-showing-card .role-info-value,
            #my-showings-tab .rejected-showing-card .role-info-value {
                text-align: left;
            }
        }
    </style>
<?php endif; ?>
</head>
<body>
        <!-- Toast Notification Script -->
        <script src="<?= ROOT ?>/assets/JS/toast.js"></script>
        <?php if ($toastSuccessMessage !== ''): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastSuccess('<?= addslashes($toastSuccessMessage); ?>');
            });
        </script>
        <?php endif; ?>
        <?php if ($toastErrorMessage !== ''): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastError('<?= addslashes($toastErrorMessage); ?>');
            });
        </script>
        <?php endif; ?>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <a href="<?=ROOT?>/artistdashboard" class="logo-link">
                <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" class="logo-image">
            </a>
        </div>
        <ul class="menu">
            <li class="<?= $sidebarActive['dashboard'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard">
                    <i class='bx bx-home'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['vacancies'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies">
                    <i class='bx bx-volume-full'></i>
                    <span>View All Vacancies</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['notifications'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/notifications">
                    <i class='bx bx-bell'></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['classes'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/classes">
                    <i class='bx bx-microphone'></i>
                    <span>Classes</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['showings'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard?tab=my-showings#my-showings">
                    <i class='bx bx-calendar-event'></i>
                    <span>Showings</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['calendar'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/calendar">
                    <i class='bx bx-calendar-week'></i>
                    <span>Artist Calendar</span>
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
                            <img src="<?= esc($profileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
                        </div>
                    </div>
                    <div class="user-menu-dropdown">
                        <a href="<?= ROOT ?>/profile" class="user-menu-item">
                            <i class='bx bx-user icon'></i>
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

        <?php if ($infoMessage !== ''): ?>
            <div class="info-box <?= $infoMessageType === 'success' ? 'artist-info-box--success' : 'artist-info-box--error' ?>">
                <?= esc($infoMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid artist-stats-grid">
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Dramas</div>
                    <div class="stat-card-icon primary">
                        <i class="bx bx-mask"></i>
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
                        <i class="bx bx-award"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?></div>
            </div>
        </div>

        <!-- Drama Role Vacancies Banner -->
        <div class="card-section vacancies-banner">
            <div class="artist-flex-between-wrap">
                <div class="artist-flex-1">
                    <h2 class="artist-vacancies-title">
                        Drama Role Vacancies Now Open!
                    </h2>
                    <p class="artist-vacancies-description">
                        Discover available roles and apply to be part of our upcoming drama productions.
                    </p>
                </div>
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies" class="btn btn-primary btn-compact artist-font-semibold">
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
            <div class="profile-container artist-profile-single-col">
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
                            <button class="btn btn-primary btn-compact artist-mt-16" onclick="window.location.href='<?=ROOT?>/createDrama'">
                                <i class="bx bx-plus"></i> Create Drama
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_director as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header">
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
                                                    <a href="<?= ROOT ?>/uploads/certificates/<?= esc(rawurlencode($drama->certificate_image)) ?>" target="_blank" class="director-cert-link">
                                                        View
                                                    </a>
                                                <?php else: ?>
                                                    <span class="status-badge pending">Pending</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <button class="btn btn-primary artist-flex-btn" onclick="handleDirectorManage(<?=$drama->id?>)">
                                            <i class="bx bx-tachometer-alt"></i> Manage
                                        </button>
                                        <a class="btn btn-director-publish artist-flex-btn artist-text-center" href="<?= ROOT ?>/director/drama_details?drama_id=<?= (int)$drama->id ?>#publish-section">
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
                                    <div class="artist-header artist-manager-header">
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
                                        <button class="btn btn-primary artist-flex-btn" onclick="handlePMManage(<?=$drama->id?>)">
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
                            <button class="btn btn-primary btn-compact artist-mt-16" onclick="window.location.href='<?=ROOT?>/artistdashboard/browse_vacancies'">
                                <i class="bx bx-search"></i> Browse Vacancies
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($roles_as_actor as $role): ?>
                                <div class="artist-card">
                                    <div class="artist-header artist-actor-header">
                                        <h3 class="artist-name artist-actor-name"><?= esc($role->role_name) ?></h3>
                                        <p class="artist-experience"><?= esc(ucfirst($role->role_type)) ?> Role</p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Drama:</span>
                                            <span class="info-value artist-brand-value">
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
                                        <button class="btn btn-primary artist-flex-btn" onclick="window.location.href='<?=ROOT?>/artistdashboard/view_drama?drama_id=<?=$role->drama_id?>'">
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
                            <p class="artist-note">Confirm your participation so the director knows you are joining.</p>
                            <div class="artist-grid-gap-16">
                                <?php foreach ($upcoming_interviews as $application): ?>
                                    <?php
                                        $interviewTime = date('M d, Y g:i A', strtotime($application->interview_at));
                                        $confirmationStatus = strtolower($application->interview_confirmation_status ?? 'pending');
                                        $statusPalette = [
                                            'confirmed' => 'artist-status-pill--confirmed',
                                            'declined' => 'artist-status-pill--declined',
                                            'pending' => 'artist-status-pill--pending',
                                        ];
                                        $badgeClass = $statusPalette[$confirmationStatus] ?? $statusPalette['pending'];
                                    ?>
                                    <div class="role-info-card artist-card-highlight-left">
                                        <div class="artist-flex-between-start-wrap">
                                            <div>
                                                <h4 class="artist-heading-zero"><?= esc($application->role_name ?? 'Role') ?> <small class="artist-muted-small-normal">in <?= esc($application->drama_name ?? 'Drama') ?></small></h4>
                                                <div class="artist-muted-small">
                                                    Directed by <?= esc($application->director_name ?? 'Director') ?>
                                                </div>
                                            </div>
                                            <span class="status-badge artist-status-pill <?= esc($badgeClass) ?>">
                                                <?= esc($confirmationStatus) ?>
                                            </span>
                                        </div>
                                        <div class="role-info-item artist-role-info-item-mt12">
                                            <span class="role-info-label"><i class="bx bx-calendar"></i> Interview:</span>
                                            <span class="role-info-value"><?= esc($interviewTime) ?></span>
                                        </div>
                                        <?php if (!empty($application->interview_notes)): ?>
                                            <div class="artist-note-box">
                                                <strong>Director notes:</strong>
                                                <p class="artist-note-text"><?= nl2br(esc($application->interview_notes)) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($confirmationStatus === 'pending'): ?>
                                            <form method="POST" action="<?= ROOT ?>/artistdashboard/confirm_interview" class="interview-response artist-form-stack-mt16">
                                                <input type="hidden" name="application_id" value="<?= (int)$application->id ?>">
                                                <label class="artist-label-muted-small">Send an optional note to the director</label>
                                                <textarea name="note" rows="2" class="form-control" placeholder="Add details about your availability (optional)"></textarea>
                                                <div class="artist-flex-wrap-gap-12">
                                                    <button type="submit" name="response" value="confirm" class="btn btn-success artist-btn-flex-min-140">
                                                        <i class="bx bx-check"></i> Confirm Attendance
                                                    </button>
                                                    <button type="submit" name="response" value="decline" class="btn btn-danger artist-btn-flex-min-120">
                                                        <i class="bx bx-times"></i> Decline
                                                    </button>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <div class="artist-response-meta">
                                                Response sent <?= !empty($application->interview_confirmed_at) ? esc(date('M d, Y g:i A', strtotime($application->interview_confirmed_at))) : 'recently' ?>
                                                <?php if (!empty($application->interview_confirmation_note)): ?>
                                                    <div class="artist-response-note">"<?= esc($application->interview_confirmation_note) ?>"</div>
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
                                <button class="btn btn-primary btn-compact artist-mt-16" onclick="window.location.href='<?=ROOT?>/artistdashboard/browse_vacancies'">
                                    <i class="bx bx-search"></i> Browse Vacancies
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- My Showings Tab -->
                <div id="my-showings-tab" class="tab-content">
                    <h3 class="artist-tab-title">
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
                        <div class="artist-grid-gap-16-mb18">
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
                                    $requestSenderName = trim((string)($requestDetails['request_sender_name'] ?? ($show_request->audience_name ?? 'Audience User')));
                                    $requestContactPhone = trim((string)($requestDetails['request_contact_phone'] ?? ($show_request->audience_phone ?? 'Not provided')));
                                    $requestContactEmail = trim((string)($requestDetails['request_contact_email'] ?? ($show_request->audience_email ?? 'Not provided')));
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
                                    $pendingShowDateForMatch = $requestedShowDateRaw !== '' ? date('Y-m-d', strtotime($requestedShowDateRaw)) : '';
                                    $pendingShowTimeForMatch = trim((string)($requestDetails['show_time_start'] ?? ($requestDetails['show_time'] ?? '')));
                                    $pendingShowTimeEndForMatch = trim((string)($requestDetails['show_time_end'] ?? ''));
                                    $presentCount = (int)($requestDetails['present_count'] ?? 0);
                                    $requestNotes = trim((string)($requestDetails['request_notes'] ?? ''));
                                ?>
                                <div class="role-info-card pending-showing-card" data-show-date="<?= esc($pendingShowDateForMatch) ?>" data-show-start="<?= esc($pendingShowTimeForMatch) ?>" data-show-end="<?= esc($pendingShowTimeEndForMatch) ?>">
                                    <div class="showing-card-header">
                                        <div>
                                            <h3 class="showing-card-title"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p class="showing-card-audience"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge requested"><i class="bx bx-time"></i> Pending</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-map"></i> Requested Place:</span><span class="role-info-value"><?= esc($requestedVenue) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-user"></i> Sender Name:</span><span class="role-info-value"><?= esc($requestSenderName !== '' ? $requestSenderName : 'Audience User') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-phone"></i> Contact Phone:</span><span class="role-info-value"><?= esc($requestContactPhone !== '' ? $requestContactPhone : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-envelope"></i> Contact Email:</span><span class="role-info-value"><?= esc($requestContactEmail !== '' ? $requestContactEmail : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate !== 'Not specified' ? $requestedShowDate : $requestedSchedule) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime) ?></span></div>
                                    <div class="pending-slot-conflict" data-conflict-hint></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-group"></i> Expected Present Count:</span><span class="role-info-value"><?= $presentCount > 0 ? (int)$presentCount : 'Not specified' ?></span></div>
                                    <?php if ($requestNotes !== ''): ?>
                                        <div class="artist-note-highlight">
                                            <strong class="artist-note-highlight-title"><i class="bx bx-note"></i> Additional Notes:</strong>
                                            <p class="artist-note-highlight-text"><?= esc($requestNotes) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="artist-flex-wrap-gap-10-mt16">
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_show_request" class="artist-form-flex-min-180">
                                            <input type="hidden" name="request_id" value="<?= (int)$show_request->id ?>">
                                            <input type="hidden" name="response" value="accept">
                                            <button type="submit" class="btn btn-success artist-btn-w-full"><i class="bx bx-check"></i> Accept Show</button>
                                        </form>
                                        <button type="button" class="btn btn-danger show-reject-reason-btn artist-form-flex-min-180" data-target="reject-form-<?= (int)$show_request->id ?>">
                                            <i class="bx bx-x"></i> Reject
                                        </button>
                                    </div>
                                    <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_show_request" id="reject-form-<?= (int)$show_request->id ?>" class="showings-reject-form artist-reject-form">
                                        <input type="hidden" name="request_id" value="<?= (int)$show_request->id ?>">
                                        <input type="hidden" name="response" value="reject">
                                        <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Add reason for rejection" required></textarea>
                                        <div class="artist-flex-wrap-gap-8">
                                            <button type="submit" class="btn btn-danger artist-nowrap" onclick="return confirm('Reject this show request with this reason?');"><i class="bx bx-send"></i> Submit Rejection</button>
                                            <button type="button" class="btn btn-outline reject-cancel-btn artist-nowrap" data-target="reject-form-<?= (int)$show_request->id ?>">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results artist-no-results-mb18">
                            <i class="bx bx-inbox"></i>
                            <h3>No Pending Show Requests</h3>
                            <p>No audience show requests are waiting for your decision.</p>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div class="classes-subtab-panel" data-showings-panel="accepted" role="tabpanel">
                    <?php if (!empty($show_requests_accepted)): ?>
                        <div class="accepted-showings-toolbar">
                            <div>
                                <label for="accepted-filter-date">Filter by Date</label>
                                <input type="date" id="accepted-filter-date" />
                            </div>
                            <div>
                                <label for="accepted-filter-start-time">Filter by Start Time</label>
                                <input type="time" id="accepted-filter-start-time" />
                            </div>
                            <div>
                                <label for="accepted-filter-end-time">Filter by End Time</label>
                                <input type="time" id="accepted-filter-end-time" />
                            </div>
                            <button type="button" class="btn btn-secondary" id="accepted-filter-clear">
                                <i class="bx bx-reset"></i> Clear Filter
                            </button>
                        </div>
                        <div class="accepted-showings-empty" id="accepted-showings-empty">
                            No accepted showings found for the selected date/time filter.
                        </div>
                        <div class="artist-grid-gap-16-mb18">
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
                                    $requestSenderName = trim((string)($requestDetails['request_sender_name'] ?? ($show_request->audience_name ?? 'Audience User')));
                                    $requestContactPhone = trim((string)($requestDetails['request_contact_phone'] ?? ($show_request->audience_phone ?? 'Not provided')));
                                    $requestContactEmail = trim((string)($requestDetails['request_contact_email'] ?? ($show_request->audience_email ?? 'Not provided')));
                                    $acceptedShowDateForMatch = $requestedShowDateRaw !== '' ? date('Y-m-d', strtotime($requestedShowDateRaw)) : '';
                                    $acceptedShowTimeForMatch = trim((string)($requestDetails['show_time_start'] ?? ($requestDetails['show_time'] ?? '')));
                                    $acceptedShowTimeEndForMatch = trim((string)($requestDetails['show_time_end'] ?? ''));
                                ?>
                                <div class="role-info-card accepted-showing-card" data-show-date="<?= esc($acceptedShowDateForMatch) ?>" data-show-start="<?= esc($acceptedShowTimeForMatch) ?>" data-show-end="<?= esc($acceptedShowTimeEndForMatch) ?>">
                                    <div class="showing-card-header">
                                        <div>
                                            <h3 class="showing-card-title"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p class="showing-card-audience"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge success"><i class="bx bx-check-circle"></i> Accepted</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-user"></i> Sender Name:</span><span class="role-info-value"><?= esc($requestSenderName !== '' ? $requestSenderName : 'Audience User') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-phone"></i> Contact Phone:</span><span class="role-info-value"><?= esc($requestContactPhone !== '' ? $requestContactPhone : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-envelope"></i> Contact Email:</span><span class="role-info-value"><?= esc($requestContactEmail !== '' ? $requestContactEmail : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime !== '' ? $requestedShowTime : 'Not specified') ?></span></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results artist-no-results-mb18"><i class="bx bx-check-shield"></i><h3>No Accepted Showings</h3><p>You have not accepted any audience showings yet.</p></div>
                    <?php endif; ?>
                    </div>

                    <div class="classes-subtab-panel" data-showings-panel="rejected" role="tabpanel">
                    <?php if (!empty($show_requests_rejected)): ?>
                        <div class="artist-grid-gap-16-mb10">
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
                                    $requestSenderName = trim((string)($requestDetails['request_sender_name'] ?? ($show_request->audience_name ?? 'Audience User')));
                                    $requestContactPhone = trim((string)($requestDetails['request_contact_phone'] ?? ($show_request->audience_phone ?? 'Not provided')));
                                    $requestContactEmail = trim((string)($requestDetails['request_contact_email'] ?? ($show_request->audience_email ?? 'Not provided')));
                                    $rejectionReason = trim((string)($show_request->rejection_reason ?? ''));
                                ?>
                                <div class="role-info-card rejected-showing-card">
                                    <div class="showing-card-header">
                                        <div>
                                            <h3 class="showing-card-title"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p class="showing-card-audience"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge danger"><i class="bx bx-x-circle"></i> Rejected</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-user"></i> Sender Name:</span><span class="role-info-value"><?= esc($requestSenderName !== '' ? $requestSenderName : 'Audience User') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-phone"></i> Contact Phone:</span><span class="role-info-value"><?= esc($requestContactPhone !== '' ? $requestContactPhone : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-envelope"></i> Contact Email:</span><span class="role-info-value"><?= esc($requestContactEmail !== '' ? $requestContactEmail : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime !== '' ? $requestedShowTime : 'Not specified') ?></span></div>
                                    <div class="artist-rejection-box">
                                        <strong class="artist-rejection-title"><i class="bx bx-error-circle"></i> Rejection Reason:</strong>
                                        <p class="artist-rejection-text"><?= esc($rejectionReason !== '' ? $rejectionReason : 'No reason provided.') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results artist-no-results-mb10"><i class="bx bx-smile"></i><h3>No Rejected Showings</h3><p>No rejected audience show requests found.</p></div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- Requests Tab -->
                <div id="requests-tab" class="tab-content">
                    <?php
                        $hasPmRequests = isset($pm_requests) && !empty($pm_requests);
                        $hasActorRequests = isset($role_requests) && !empty($role_requests);
                    ?>

                    <!-- Category: PM Requests -->
                    <div class="card-section artist-card-section-mb24">
                        <h3 class="artist-section-title">
                            <i class="bx bx-user-tie"></i> PM Requests
                            <span class="artist-section-count">
                                (<?= isset($pm_requests) ? count($pm_requests) : 0 ?>)
                            </span>
                        </h3>

                        <?php if ($hasPmRequests): ?>
                            <div class="artist-grid-gap-16">
                                <?php foreach ($pm_requests as $pm_request): ?>
                                    <div class="role-info-card">
                                        <div class="artist-card-header-row">
                                            <div>
                                                <h3 class="artist-card-title-brand">
                                                    <i class="bx bx-film"></i> <?= esc($pm_request->drama_name) ?>
                                                </h3>
                                                <p class="artist-muted-small">
                                                    <strong>Director:</strong> <?= esc($pm_request->director_name) ?>
                                                </p>
                                                <p class="artist-muted-small">
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
                                            <div class="artist-message-box">
                                                <strong class="artist-message-title"><i class="bx bx-comment"></i> Message from Director:</strong>
                                                <p class="artist-message-text"><?= esc($pm_request->message) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="role-info-item">
                                            <span class="role-info-label">
                                                <i class="bx bx-calendar"></i> Requested:
                                            </span>
                                            <span class="role-info-value"><?= date('M d, Y g:i A', strtotime($pm_request->requested_at)) ?></span>
                                        </div>
                                        
                                        <div class="artist-info-box-blue">
                                            <p class="artist-info-text-blue">
                                                <i class="bx bx-info-circle"></i> <strong>About this role:</strong> 
                                                As Production Manager, you'll oversee services, budget management, and theater bookings for this drama.
                                            </p>
                                        </div>
                                        
                                        <div class="artist-flex-gap-10-mt16">
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" class="artist-form-flex-1">
                                                <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                                <input type="hidden" name="response" value="accept">
                                                <button type="submit" class="btn btn-success artist-btn-w-full">
                                                    <i class="bx bx-check"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" class="artist-form-flex-1">
                                                <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                                <input type="hidden" name="response" value="reject">
                                                <button type="submit" class="btn btn-danger artist-btn-w-full" 
                                                        onclick="return confirm('Are you sure you want to decline this Production Manager request?');">
                                                    <i class="bx bx-times"></i> Decline
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="bx bx-inbox"></i>
                                <h3>No Pending PM Requests</h3>
                                <p>You don't have any production manager requests right now.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Category: Actor Requests -->
                    <div class="card-section">
                        <h3 class="artist-section-title">
                            <i class="bx bx-theater-masks"></i> Actor Requests
                            <span class="artist-section-count">
                                (<?= isset($role_requests) ? count($role_requests) : 0 ?>)
                            </span>
                        </h3>

                        <?php if (!$hasActorRequests): ?>
                            <div class="no-results">
                                <i class="bx bx-inbox"></i>
                                <h3>No Pending Actor Requests</h3>
                                <p>You don't have any actor role requests at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="artist-grid-gap-16">
                                <?php foreach ($role_requests as $request): ?>
                                    <div class="role-info-card">
                                        <div class="artist-card-header-row">
                                            <div>
                                                <h3 class="artist-card-title-ink">
                                                    <i class="bx bx-theater-masks"></i> <?= esc($request->drama_name) ?>
                                                </h3>
                                                <p class="artist-muted-small">
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
                                            <div class="artist-description-box">
                                                <strong class="artist-description-title">Description:</strong>
                                                <p class="artist-message-text"><?= esc($request->role_description) ?></p>
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
                                        
                                        <div class="artist-flex-gap-10-mt16">
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" class="artist-form-flex-1">
                                                <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                                <input type="hidden" name="response" value="accept">
                                                <button type="submit" class="btn btn-success artist-btn-w-full">
                                                    <i class="bx bx-check"></i> Accept Role
                                                </button>
                                            </form>
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" class="artist-form-flex-1">
                                                <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                                <input type="hidden" name="response" value="reject">
                                                <button type="submit" class="btn btn-danger artist-btn-w-full">
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
            const menuItems = document.querySelectorAll('.sidebar .menu li');
            menuItems.forEach(function (item) {
                item.classList.remove('active');
            });

            if (window.location.hash === '#my-showings') {
                const showingsLink = document.querySelector('.sidebar .menu a[href*="tab=my-showings"]');
                if (showingsLink && showingsLink.parentElement) {
                    showingsLink.parentElement.classList.add('active');
                }
                return;
            }

            const dashboardItem = document.querySelector('.sidebar .menu li:first-child');
            if (dashboardItem) {
                dashboardItem.classList.add('active');
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

        function parseTimeToMinutes(value) {
            let input = (value || '').trim();
            if (!input) {
                return null;
            }

            const rangeLeadingTime = input.match(/^(\d{1,2}:\d{2})(?:\s*(AM|PM))?/i);
            if (rangeLeadingTime) {
                const hhmm = rangeLeadingTime[1];
                const meridiem = (rangeLeadingTime[2] || '').toUpperCase();
                if (meridiem) {
                    input = hhmm + ' ' + meridiem;
                } else {
                    input = hhmm;
                }
            }

            const twentyFourHour = input.match(/^(\d{1,2}):(\d{2})$/);
            if (twentyFourHour) {
                const hh = parseInt(twentyFourHour[1], 10);
                const mm = parseInt(twentyFourHour[2], 10);
                if (hh >= 0 && hh <= 23 && mm >= 0 && mm <= 59) {
                    return (hh * 60) + mm;
                }
            }

            const twelveHour = input.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);
            if (twelveHour) {
                let hh = parseInt(twelveHour[1], 10);
                const mm = parseInt(twelveHour[2], 10);
                const meridiem = twelveHour[3].toUpperCase();

                if (hh === 12) {
                    hh = 0;
                }
                if (meridiem === 'PM') {
                    hh += 12;
                }

                if (hh >= 0 && hh <= 23 && mm >= 0 && mm <= 59) {
                    return (hh * 60) + mm;
                }
            }

            return null;
        }

        function initAcceptedShowingsFilters() {
            const acceptedPanel = document.querySelector('#my-showings-tab .classes-subtab-panel[data-showings-panel="accepted"]');
            if (!acceptedPanel) {
                return;
            }

            const cards = acceptedPanel.querySelectorAll('.accepted-showing-card');
            const dateInput = acceptedPanel.querySelector('#accepted-filter-date');
            const startTimeInput = acceptedPanel.querySelector('#accepted-filter-start-time');
            const endTimeInput = acceptedPanel.querySelector('#accepted-filter-end-time');
            const clearButton = acceptedPanel.querySelector('#accepted-filter-clear');
            const emptyState = acceptedPanel.querySelector('#accepted-showings-empty');

            if (!cards.length || !dateInput || !startTimeInput || !endTimeInput || !clearButton) {
                return;
            }

            const applyAcceptedFilters = function () {
                const selectedDate = dateInput.value;
                const selectedStartMins = parseTimeToMinutes(startTimeInput.value);
                const selectedEndMins = parseTimeToMinutes(endTimeInput.value);
                let visibleCount = 0;

                cards.forEach(function (card) {
                    const cardDate = (card.getAttribute('data-show-date') || '').trim();
                    const cardStartMins = parseTimeToMinutes(card.getAttribute('data-show-start') || '');
                    const cardEndMins = parseTimeToMinutes(card.getAttribute('data-show-end') || '');

                    const dateMatches = !selectedDate || (cardDate !== '' && cardDate === selectedDate);
                    const startMatches = selectedStartMins === null || (cardStartMins !== null && cardStartMins === selectedStartMins);
                    const endMatches = selectedEndMins === null || (cardEndMins !== null && cardEndMins === selectedEndMins);

                    const shouldShow = dateMatches && startMatches && endMatches;
                    card.style.display = shouldShow ? '' : 'none';
                    if (shouldShow) {
                        visibleCount += 1;
                    }
                });

                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            };

            dateInput.addEventListener('input', applyAcceptedFilters);
            startTimeInput.addEventListener('input', applyAcceptedFilters);
            endTimeInput.addEventListener('input', applyAcceptedFilters);
            clearButton.addEventListener('click', function () {
                dateInput.value = '';
                startTimeInput.value = '';
                endTimeInput.value = '';
                applyAcceptedFilters();
            });

            applyAcceptedFilters();
        }

        function initPendingShowingConflicts() {
            const acceptedCards = document.querySelectorAll('#my-showings-tab .accepted-showing-card');
            const pendingCards = document.querySelectorAll('#my-showings-tab .pending-showing-card');

            if (!acceptedCards.length || !pendingCards.length) {
                return;
            }

            const acceptedSlots = [];
            acceptedCards.forEach(function (card) {
                acceptedSlots.push({
                    date: (card.getAttribute('data-show-date') || '').trim(),
                    startMinutes: parseTimeToMinutes(card.getAttribute('data-show-start') || ''),
                    endMinutes: parseTimeToMinutes(card.getAttribute('data-show-end') || '')
                });
            });

            pendingCards.forEach(function (card) {
                const pendingDate = (card.getAttribute('data-show-date') || '').trim();
                const pendingStartMinutes = parseTimeToMinutes(card.getAttribute('data-show-start') || '');
                const pendingEndMinutes = parseTimeToMinutes(card.getAttribute('data-show-end') || '');
                const hint = card.querySelector('[data-conflict-hint]');

                if (!hint || pendingDate === '' || pendingStartMinutes === null) {
                    return;
                }

                const matches = acceptedSlots.filter(function (slot) {
                    if (slot.date === '' || slot.startMinutes === null) {
                        return false;
                    }

                    if (slot.date !== pendingDate || slot.startMinutes !== pendingStartMinutes) {
                        return false;
                    }

                    if (pendingEndMinutes !== null && slot.endMinutes !== null) {
                        return slot.endMinutes === pendingEndMinutes;
                    }

                    return true;
                }).length;

                if (matches > 0) {
                    hint.style.display = 'block';
                    hint.innerHTML = '<i class="bx bx-error-circle"></i> This requested slot matches ' + matches + ' accepted showing(s).';
                }
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
        initAcceptedShowingsFilters();
        initPendingShowingConflicts();
    </script>
</body>
</html>
