<?php 
if(isset($data) && is_array($data)) {
    extract($data);
}

$profileImageSrc = ROOT . '/uploads/profile_images/user_profile.png';
if (isset($user->profile_image) && !empty($user->profile_image)) {
    $imageValue = str_replace('\\', '/', $user->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
}

$artistSidebarActive = 'vacancies';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Role Vacancies - Rangamadala</title>
        <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/toast.css">
    <link rel="icon" type="image/png" href="<?=ROOT?>/assets/images/Rangamadala%20logo.png">
    <link rel="apple-touch-icon" href="<?=ROOT?>/assets/images/Rangamadala%20logo.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid var(--success);
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--danger);
        }

        .vacancy-banner {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            color: #4a3a14;
            border: 1px solid #f0dfb4;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px rgba(186, 142, 35, 0.12);
        }

        .banner-content h3 {
            font-size: 24px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #4a3a14;
        }

        .banner-content p {
            opacity: 1;
            font-size: 16px;
            color: #6a5120;
        }

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

        .card-section .form-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .card-section .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: flex-end;
        }

        .card-section .form-row .form-group {
            flex: 1 1 220px;
            min-width: 220px;
        }

        .card-section .filter-actions {
            flex: 0 0 auto;
            min-width: 180px;
        }

        .card-section .btn-filter {
            width: 100%;
            min-height: 52px;
            border-radius: 10px;
            box-shadow: 0 6px 16px rgba(186, 142, 35, 0.18);
        }

        .card-section .btn-filter:hover {
            transform: translateY(-2px);
        }

        .filter-topic {
            margin: 0 0 14px;
        }

        .filter-topic span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .filter-topic-note {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--muted);
        }

        .vacancies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .vacancy-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .vacancy-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .vacancy-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .role-title {
            font-size: 20px;
            color: var(--ink);
            margin-bottom: 5px;
        }

        .role-type-badge {
            background: var(--brand-soft);
            color: var(--brand);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .drama-info {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .drama-name {
            font-weight: 600;
            color: var(--ink);
            font-size: 15px;
        }

        .vacancy-description {
            color: var(--ink);
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.6;
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .vacancy-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 15px 0;
            padding: 15px;
            background: var(--bg);
            border-radius: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
        }

        .meta-item i {
            color: var(--brand);
            width: 16px;
        }

        .meta-value {
            font-weight: 600;
            color: var(--ink);
        }

        .vacancy-footer {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
        }

        .btn-apply {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--brand);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.2s;
        }

        .btn-apply:hover {
            background: var(--brand-strong);
        }

        .applied-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--success);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .vacancies-grid {
                grid-template-columns: 1fr;
            }

            .vacancy-meta {
                grid-template-columns: 1fr;
            }

            .card-section .form-row .form-group,
            .card-section .filter-actions {
                min-width: 100%;
            }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .header {
            background: linear-gradient(135deg, #ba8e23, #a0781e);
            color: white;
            padding: 40px 20px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
        }

        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header p {
            font-size: 18px;
            opacity: 0.95;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: var(--brand);
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .back-btn:hover {
            transform: translateX(-5px);
        }

        .filters {
            background: white;
            padding: 25px;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            margin-bottom: 30px;
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-group label {
            font-weight: 500;
            font-size: 14px;
            color: var(--muted);
        }

        .filter-group input,
        .filter-group select {
            padding: 10px 14px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--brand);
        }

        .btn-filter {
            background: var(--brand);
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .btn-filter:hover {
            background: var(--brand-strong);
        }

        .vacancies-count {
            font-size: 18px;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 20px;
        }

        .vacancies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .vacancy-card {
            background: white;
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--brand);
        }

        .vacancy-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-md);
        }

        .vacancy-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .role-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 5px;
        }

        .role-type-badge {
            background: var(--brand-soft);
            color: var(--brand);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .drama-info {
            color: var(--muted);
            font-size: 15px;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .drama-name {
            font-weight: 600;
            color: var(--ink);
            font-size: 16px;
        }

        .vacancy-description {
            color: var(--ink);
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.6;
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .vacancy-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin: 15px 0;
            padding: 15px;
            background: var(--bg);
            border-radius: 8px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .meta-item i {
            color: var(--brand);
            width: 16px;
        }

        .meta-value {
            font-weight: 600;
            color: var(--ink);
        }

        .vacancy-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid var(--border);
        }

        .btn-apply {
            background: var(--brand);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
            text-decoration: none;
        }

        .btn-apply:hover {
            background: var(--brand-strong);
        }

        .btn-apply:disabled {
            background: var(--muted);
            cursor: not-allowed;
            opacity: 0.6;
        }

        .applied-badge {
            background: var(--success);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .no-vacancies {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .no-vacancies i {
            font-size: 64px;
            color: var(--muted);
            margin-bottom: 20px;
        }

        .no-vacancies h3 {
            font-size: 24px;
            color: var(--ink);
            margin-bottom: 10px;
        }

        .no-vacancies p {
            color: var(--muted);
            font-size: 16px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .alert i {
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 28px;
            }

            .vacancies-grid {
                grid-template-columns: 1fr;
            }

            .filters-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Match inner fields/cards with artist dashboard tab styling */
        .card-section {
            background: linear-gradient(180deg, #fffefb 0%, #fff7e7 100%);
            border: 1px solid #efdcb0;
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(186, 142, 35, 0.12);
        }

        /* Main card to sub-card border gap */
        .vacancies-content {
            padding: 18px;
        }

        /* Inner gap: keep content away from card borders */
        .vacancies-content > .card-section {
            padding: 22px;
        }

        .form-group label {
            color: #6f5a2e;
            font-weight: 600;
        }

        .form-input,
        .form-group input,
        .form-group select {
            background: #fffdf8;
            border: 1px solid #e6cf97;
            color: #3f2f12;
            border-radius: 10px;
        }

        .form-input:focus,
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ba8e23;
            box-shadow: 0 0 0 3px rgba(186, 142, 35, 0.16);
        }

        .vacancy-card {
            background: linear-gradient(180deg, #fffefb 0%, #fff7e7 100%);
            border: 1px solid #efdcb0;
            border-left: 4px solid #ba8e23;
            box-shadow: 0 8px 20px rgba(186, 142, 35, 0.12);
        }

        .vacancy-card:hover {
            box-shadow: 0 14px 26px rgba(186, 142, 35, 0.2);
        }

        .vacancy-meta {
            background: #fff9ec;
            border: 1px solid #efdcb0;
        }

        .role-type-badge {
            background: #f4e3be;
            color: #8f6717;
        }

        .btn-apply {
            background: linear-gradient(135deg, #ba8e23, #9b761d);
            color: #fff;
            border: 1px solid #8f6a17;
            box-shadow: 0 6px 14px rgba(186, 142, 35, 0.24);
        }

        .btn-apply:hover {
            background: linear-gradient(135deg, #c79a29, #a67d1e);
        }

        /* Add breathing room between bordered sections (filters/results/empty state) */
        .vacancies-content > .card-section + .card-section {
            margin-top: 18px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main--content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?>">
                <i class="bx bx-<?= ($_SESSION['message_type'] ?? 'info') === 'success' ? 'check-circle' : 'info-circle' ?>"></i>
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php 
                unset($_SESSION['message']);
                unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Opportunities</span>
                <h2>Browse Role Vacancies</h2>
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

        <div class="content vacancies-content">
            <!-- Banner -->
            <div class="vacancy-banner">
                <div class="banner-content">
                    <h3><i class="bx bx-theater-masks"></i> Discover Exciting Opportunities</h3>
                    <p>Find and apply for roles that showcase your talent</p>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="card-section">
                <h3 class="filter-topic"><span><i class="bx bx-search-alt-2"></i> Find Your Next Role</span></h3>
                <form method="GET" action="<?=ROOT?>/artistdashboard/browse_vacancies" class="form-container" id="vacancyFilterForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input type="text" id="search" name="search" placeholder="Role name, drama, description..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="role_type">Role Type</label>
                            <select id="role_type" name="role_type" class="form-input">
                                <option value="">All Types</option>
                                <option value="lead" <?= ($filters['role_type'] ?? '') === 'lead' ? 'selected' : '' ?>>Lead Role</option>
                                <option value="supporting" <?= ($filters['role_type'] ?? '') === 'supporting' ? 'selected' : '' ?>>Supporting Role</option>
                                <option value="minor" <?= ($filters['role_type'] ?? '') === 'minor' ? 'selected' : '' ?>>Minor Role</option>
                                <option value="extra" <?= ($filters['role_type'] ?? '') === 'extra' ? 'selected' : '' ?>>Extra</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sort">Sort By</label>
                            <select id="sort" name="sort" class="form-input">
                                <option value="latest" <?= ($filters['sort'] ?? '') === 'latest' ? 'selected' : '' ?>>Latest First</option>
                                <option value="oldest" <?= ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                                <option value="salary_high" <?= ($filters['sort'] ?? '') === 'salary_high' ? 'selected' : '' ?>>Highest Salary</option>
                                <option value="salary_low" <?= ($filters['sort'] ?? '') === 'salary_low' ? 'selected' : '' ?>>Lowest Salary</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Results Count -->
            <div class="card-section">
                <div style="display: flex; align-items: center; gap: 10px; color: var(--muted);">
                    <i class="bx bx-list"></i>
                    <span><?= $total_vacancies ?> <?= $total_vacancies === 1 ? 'vacancy' : 'vacancies' ?> found</span>
                </div>
            </div>

            <!-- Vacancies Grid -->
            <?php if (!empty($vacancies)): ?>
                <div class="vacancies-grid">
                    <?php foreach ($vacancies as $vacancy): ?>
                        <div class="vacancy-card">
                            <div class="vacancy-header">
                                <div>
                                    <h3 class="role-title"><?= htmlspecialchars($vacancy->role_name) ?></h3>
                                </div>
                                <span class="role-type-badge"><?= ucfirst(htmlspecialchars($vacancy->role_type)) ?></span>
                            </div>

                            <div class="drama-info">
                                <span class="drama-name"><i class="bx bx-film"></i> <?= htmlspecialchars($vacancy->drama_name) ?></span>
                                <span><i class="bx bx-movie-play"></i> Director: <?= htmlspecialchars($vacancy->director_name) ?></span>
                            </div>

                            <?php if (!empty($vacancy->role_description)): ?>
                                <div class="vacancy-description">
                                    <?= nl2br(htmlspecialchars($vacancy->role_description)) ?>
                                </div>
                            <?php endif; ?>

                            <div class="vacancy-meta">
                                <div class="meta-item">
                                    <i class="bx bx-money"></i>
                                    <span class="meta-value">LKR <?= isset($vacancy->salary) && $vacancy->salary !== null ? number_format($vacancy->salary) : '0' ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="bx bx-user"></i>
                                    <span class="meta-value"><?= $vacancy->positions_remaining ?> opening<?= $vacancy->positions_remaining > 1 ? 's' : '' ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="bx bx-calendar-alt"></i>
                                    <span><?= date('M d, Y', strtotime($vacancy->published_at)) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="bx bx-info-circle"></i>
                                    <span class="meta-value"><?= ucfirst(htmlspecialchars($vacancy->status)) ?></span>
                                </div>
                            </div>

                            <div class="vacancy-footer">
                                <?php if (in_array($vacancy->id, $applied_role_ids ?? [])): ?>
                                    <span class="applied-badge">
                                        <i class="bx bx-check-circle"></i> Already Applied
                                    </span>
                                <?php else: ?>
                                    <a href="<?= ROOT ?>/artistdashboard/apply_for_role?role_id=<?= $vacancy->id ?>" class="btn-apply">
                                        <i class="bx bx-paper-plane"></i> Apply Now
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card-section" style="text-align: center; padding: 60px 20px;">
                    <i class="bx bx-search" style="font-size: 64px; color: var(--muted); margin-bottom: 20px;"></i>
                    <h3>No Vacancies Found</h3>
                    <p style="color: var(--muted);">Try adjusting your search filters or check back later for new opportunities.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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

            const filterForm = document.getElementById('vacancyFilterForm');
            if (!filterForm) return;

            const searchInput = filterForm.querySelector('#search');
            const roleTypeSelect = filterForm.querySelector('#role_type');
            const sortSelect = filterForm.querySelector('#sort');
            let debounceTimer;

            const submitFilters = function () {
                if (typeof filterForm.requestSubmit === 'function') {
                    filterForm.requestSubmit();
                    return;
                }

                filterForm.submit();
            };

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(submitFilters, 400);
                });
            }

            if (roleTypeSelect) {
                roleTypeSelect.addEventListener('change', submitFilters);
            }

            if (sortSelect) {
                sortSelect.addEventListener('change', submitFilters);
            }
        });
    </script>
</body>
</html>
