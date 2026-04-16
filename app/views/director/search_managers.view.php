<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$availableManagers = isset($availableManagers) && is_array($availableManagers) ? $availableManagers : [];
$searchTerm = isset($searchTerm) ? (string)$searchTerm : '';

$dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 0);

require_once __DIR__ . '/_profile_image_helper.php';
$profileImageSrc = directorResolveProfileImageSrc((int)($_SESSION['user_id'] ?? 0));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Production Manager - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
        .filters-card { background: #fffdfb; border-radius: 16px; border: 1px solid #ead7a4; padding: 24px; margin-bottom: 24px; box-shadow: 0 4px 14px rgba(186,142,35,.08); }
        .filters-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 6px; color: #2f2410; }
        .artist-grid { display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
        .artist-card { background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%); border-radius: 14px; border: 1px solid #ead7a4; padding: 18px; box-shadow: 0 4px 12px rgba(186,142,35,.08); display: flex; flex-direction: column; gap: 12px; transition: all 0.2s ease; }
        .artist-card:hover { box-shadow: 0 8px 20px rgba(186,142,35,.15); transform: translateY(-2px); }
        .artist-status { font-size: 13px; color: #7a6121; font-weight: 500; }
        .badge { display: inline-flex; align-items: center; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .badge-available { background: rgba(30, 144, 84, 0.12); color: #1a5e42; }
        .badge-requested { background: rgba(245, 158, 11, 0.12); color: #854a0e; }
        .badge-assigned { background: rgba(59, 130, 246, 0.12); color: #1e40af; }
        .empty-state { padding: 40px; text-align: center; border: 1px dashed #ead7a4; border-radius: 14px; color: #8a6a1f; background: linear-gradient(180deg, #fffdfb 0%, #fff8f0 100%); }
        .search-card { background: linear-gradient(180deg, #fffdfb 0%, #fff8f0 100%); border-radius: 14px; border: 1px solid #ead7a4; padding: 18px 24px; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(186,142,35,.08); }
        .search-form { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .search-input-wrapper { flex: 1; min-width: 240px; position: relative; }
        .search-input-wrapper i { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: #8a6a1f; font-size: 14px; }
        .search-input { width: 100%; padding: 12px 14px 12px 42px; border-radius: 10px; border: 1px solid #ead7a4; background: #fffdfb; color: #2f2410; font-size: 14px; transition: border-color .2s ease; }
        .search-input::placeholder { color: #a39b87; }
        .search-input:focus { outline: none; border-color: #ba8e23; box-shadow: 0 0 0 3px rgba(186,142,35,.14); }
        .search-button { padding: 12px 20px; border-radius: 10px; border: none; background: linear-gradient(135deg, #ba8e23 0%, #a0781e 100%); color: #fff; font-weight: 600; font-size: 14px; cursor: pointer; transition: all .2s ease; box-shadow: 0 4px 10px rgba(186,142,35,.12); }
        .search-button:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(186,142,35,.20); }
        .search-clear { padding: 11px 18px; border-radius: 10px; border: 1px solid #ead7a4; background: #fffdfb; color: #7a6121; font-weight: 600; font-size: 14px; text-decoration: none; transition: all .2s ease; }
        .search-clear:hover { border-color: #ba8e23; color: #ba8e23; background: linear-gradient(180deg, #fffdfb 0%, rgba(186,142,35,.05) 100%); }
        .results-hint { margin: -8px 0 18px; color: #8a6a1f; font-size: 13px; font-weight: 500; }

        /* Director dashboard button styling override */
        .director-dashboard-page .btn {
            border-radius: 10px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(186,142,35,.12);
            transition: all 0.2s ease;
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .director-dashboard-page .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(186,142,35,.20);
        }

        .director-dashboard-page .btn-secondary {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            border: 1px solid #f0dfb4;
            color: #4a3a14;
        }

        .director-dashboard-page .btn-secondary:hover {
            background: linear-gradient(180deg, #fffaf0 0%, #fff2da 100%);
            color: #3f2f12;
        }

        .director-dashboard-page .btn-success {
            background: linear-gradient(135deg, #d8b566 0%, #c59b3d 100%);
            border: 1px solid #c9a14a;
            color: #2f2410;
        }

        .director-dashboard-page .btn-success:hover {
            box-shadow: 0 10px 20px rgba(186,142,35,.24);
        }

        .director-dashboard-page .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body class="director-dashboard-page">
    <?php
    $directorSidebarDramaId = (int)$dramaId;
    $directorSidebarActive = 'production-manager';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <main class="main--content">
        <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= esc($dramaId) ?>" class="back-button"><i class="bx bx-arrow-left"></i>Back to Production Manager</a>

        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($drama->drama_name ?? 'Drama') ?></span>
                <h2>Invite Production Manager</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Browse artists, review experience, and send Production Manager request.</p>
            </div>
            <div class="user--info">
                <?php
                $directorProfileImageSrc = $profileImageSrc;
                $directorRoleLabel = 'Director';
                include __DIR__ . '/_partials/user_menu.php';
                ?>
            </div>
        </div>

        <section class="search-card">
            <form class="search-form" method="get" action="<?= ROOT ?>/director/search_managers">
                <input type="hidden" name="drama_id" value="<?= esc($dramaId) ?>">
                <div class="search-input-wrapper">
                    <i class="bx bx-search"></i>
                    <input type="text" name="search" value="<?= esc($searchTerm) ?>" class="search-input" placeholder="Search artists by name">
                </div>
                <button type="submit" class="search-button"><i class="bx bx-search" style="margin-right:6px;"></i>Search</button>
                <?php if ($searchTerm !== ''): ?>
                    <a href="<?= ROOT ?>/director/search_managers?drama_id=<?= esc($dramaId) ?>" class="search-clear">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <?php if (empty($availableManagers)): ?>
            <div class="empty-state">
                <i class="bx bx-users" style="font-size: 28px; display: block; margin-bottom: 12px;"></i>
                <?php if ($searchTerm === ''): ?>
                    No artists are currently available. Invite performers to join or publish a vacancy to attract talent.
                <?php else: ?>
                    No artists matched "<?= esc($searchTerm) ?>". Try a different name or clear the search to view everyone.
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="results-hint">
                Showing <?= count($availableManagers) ?> artist<?= count($availableManagers) === 1 ? '' : 's' ?><?php if ($searchTerm !== ''): ?> for "<?= esc($searchTerm) ?>"<?php endif; ?>.
            </p>
            <section class="artist-grid">
                <?php foreach ($availableManagers as $artist): ?>
                    <?php
                        // Check if this artist has pending request
                        $requestStatus = strtolower($artist->request_status ?? '');
                        $assignmentStatus = strtolower($artist->assignment_status ?? '');
                        $isAssigned = $assignmentStatus === 'active';
                        $hasPendingRequest = in_array($requestStatus, ['pending','interview'], true);
                    ?>
                    <article class="artist-card">
                        <div>
                            <h3 style="margin: 0 0 6px; font-size: 20px;"><?= esc($artist->full_name ?? 'Artist') ?></h3>
                            <div class="artist-status">
                                <?php if (!empty($artist->years_experience)): ?>
                                    Experience: <?= esc($artist->years_experience) ?> years
                                <?php else: ?>
                                    Experience: Not specified
                                <?php endif; ?>
                            </div>
                        </div>

                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php if ($isAssigned): ?>
                                <span class="badge badge-assigned"><i class="bx bx-user-check" style="margin-right:6px;"></i>Already assigned</span>
                            <?php elseif ($hasPendingRequest): ?>
                                <span class="badge badge-requested"><i class="bx bx-paper-plane" style="margin-right:6px;"></i>Request pending</span>
                            <?php else: ?>
                                <span class="badge badge-available"><i class="bx bx-circle" style="font-size: 6px; margin-right:6px;"></i>Available</span>
                            <?php endif; ?>
                        </div>

                        <div style="font-size: 13px; color: var(--muted);">
                            <?php if (!empty($artist->email)): ?>Email: <?= esc($artist->email) ?><br><?php endif; ?>
                            <?php if (!empty($artist->phone)): ?>Phone: <?= esc($artist->phone) ?><?php endif; ?>
                        </div>

                        <div class="actions-inline" style="margin-top: auto; gap: 10px; display: flex;">
                            <a href="<?= ROOT ?>/director/artist_profile?drama_id=<?= esc($dramaId) ?>&artist_id=<?= esc($artist->id) ?>" class="btn btn-secondary"><i class="bx bx-id-card"></i>View Profile</a>
                            <?php if ($isAssigned): ?>
                                <button type="button" class="btn btn-secondary" disabled><i class="bx bx-lock"></i>Assigned</button>
                            <?php elseif ($hasPendingRequest): ?>
                                <button type="button" class="btn btn-secondary" disabled><i class="bx bx-hourglass-half"></i>Awaiting reply</button>
                            <?php else: ?>
                                <form class="js-role-action" data-action="request" action="<?= ROOT ?>/director/send_manager_request?drama_id=<?= esc($dramaId) ?>" method="POST" style="margin: 0;">
                                    <input type="hidden" name="artist_id" value="<?= esc($artist->id) ?>">
                                    <button type="submit" class="btn btn-success"><i class="bx bx-paper-plane"></i>Request Manager</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
