<?php 
if(isset($data) && is_array($data)) {
    extract($data);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Role Vacancies - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            background: linear-gradient(135deg, var(--brand), var(--brand-strong));
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(186, 142, 35, 0.2);
        }

        .banner-content h3 {
            font-size: 24px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .banner-content p {
            opacity: 0.95;
            font-size: 16px;
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
            <span>Rangamadala</span>
        </div>
        <ul class="menu">
            <li>
                <a href="<?=ROOT?>/artistdashboard">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/profile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="active">
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies">
                    <i class="fas fa-bullhorn"></i>
                    <span>View All Vacancies</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/browseDramas">
                    <i class="fas fa-theater-masks"></i>
                    <span>Browse Dramas</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?>">
                <i class="fas fa-<?= ($_SESSION['message_type'] ?? 'info') === 'success' ? 'check-circle' : 'info-circle' ?>"></i>
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
                    <i class="fas fa-star"></i> Artist
                </div>
            </div>
        </div>

        <div class="content">
            <!-- Banner -->
            <div class="vacancy-banner">
                <div class="banner-content">
                    <h3><i class="fas fa-theater-masks"></i> Discover Exciting Opportunities</h3>
                    <p>Find and apply for roles that showcase your talent</p>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="card-section">
                <h3><span><i class="fas fa-filter"></i> Filters</span></h3>
                <form method="GET" action="<?=ROOT?>/artistdashboard/browse_vacancies" class="form-container">
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

                    <div style="margin-top: 15px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results Count -->
            <div class="card-section">
                <div style="display: flex; align-items: center; gap: 10px; color: var(--muted);">
                    <i class="fas fa-list"></i>
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
                                <span class="drama-name"><i class="fas fa-film"></i> <?= htmlspecialchars($vacancy->drama_name) ?></span>
                                <span><i class="fas fa-user-tie"></i> Director: <?= htmlspecialchars($vacancy->director_name) ?></span>
                            </div>

                            <?php if (!empty($vacancy->role_description)): ?>
                                <div class="vacancy-description">
                                    <?= nl2br(htmlspecialchars($vacancy->role_description)) ?>
                                </div>
                            <?php endif; ?>

                            <div class="vacancy-meta">
                                <div class="meta-item">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span class="meta-value">LKR <?= isset($vacancy->salary) && $vacancy->salary !== null ? number_format($vacancy->salary) : '0' ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users"></i>
                                    <span class="meta-value"><?= $vacancy->positions_remaining ?> opening<?= $vacancy->positions_remaining > 1 ? 's' : '' ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span><?= date('M d, Y', strtotime($vacancy->published_at)) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-info-circle"></i>
                                    <span class="meta-value"><?= ucfirst(htmlspecialchars($vacancy->status)) ?></span>
                                </div>
                            </div>

                            <div class="vacancy-footer">
                                <?php if (in_array($vacancy->id, $applied_role_ids ?? [])): ?>
                                    <span class="applied-badge">
                                        <i class="fas fa-check-circle"></i> Already Applied
                                    </span>
                                <?php else: ?>
                                    <a href="<?= ROOT ?>/artistdashboard/apply_for_role?role_id=<?= $vacancy->id ?>" class="btn-apply">
                                        <i class="fas fa-paper-plane"></i> Apply Now
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card-section" style="text-align: center; padding: 60px 20px;">
                    <i class="fas fa-search" style="font-size: 64px; color: var(--muted); margin-bottom: 20px;"></i>
                    <h3>No Vacancies Found</h3>
                    <p style="color: var(--muted);">Try adjusting your search filters or check back later for new opportunities.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
