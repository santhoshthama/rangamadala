<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$role = $role ?? null;
$artists = isset($artists) && is_array($artists) ? $artists : [];
$searchTerm = isset($searchTerm) ? (string)$searchTerm : '';

$dramaId = isset($drama->id) ? (int)$drama->id : (int)($_GET['drama_id'] ?? 0);
$roleId = isset($role->id) ? (int)$role->id : (int)($_GET['role_id'] ?? 0);
$roleName = $role->role_name ?? 'Role';

require_once __DIR__ . '/_profile_image_helper.php';
$profileImageSrc = directorResolveProfileImageSrc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Artist - <?= esc($roleName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
        .filters-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm, 0 4px 14px rgba(15,23,42,.08)); }
        .filters-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 6px; }
        .artist-grid { display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
        .artist-card {
            background: linear-gradient(180deg, #fffefb 0%, #fff8ea 100%);
            border: 1px solid #ead7a4;
            border-left: 4px solid var(--brand);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 6px 16px rgba(186, 142, 35, 0.10);
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-height: 100%;
        }
        .artist-card:hover {
            box-shadow: 0 12px 24px rgba(186, 142, 35, 0.18);
            transform: translateY(-3px);
        }
        .artist-card-header { display: flex; align-items: center; gap: 14px; }
        .artist-avatar { width: 72px; height: 72px; border-radius: 14px; object-fit: cover; border: 2px solid #f0dfb4; box-shadow: 0 4px 10px rgba(186, 142, 35, 0.08); }
        .artist-status { font-size: 12px; color: #6a5120; }
        .badge { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-available { background: rgba(76,175,80,.12); color: #256029; }
        .badge-requested { background: rgba(255,193,7,.16); color: #7a4f02; }
        .badge-assigned { background: rgba(0,123,255,.12); color: #0b5394; }
        .empty-state { padding: 40px; text-align: center; border: 1px dashed #ead7a4; border-radius: 16px; color: #6a5120; background: linear-gradient(180deg, #fffefb 0%, #fff8ea 100%); }
        .search-card { background: linear-gradient(180deg, #fffefb 0%, #fff8ea 100%); border-radius: 16px; border: 1px solid #ead7a4; padding: 18px 24px; margin-bottom: 24px; box-shadow: 0 6px 16px rgba(186, 142, 35, 0.08); }
        .search-form { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .search-input-wrapper { flex: 1; min-width: 240px; position: relative; }
        .search-input-wrapper i { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: var(--muted); font-size: 14px; }
        .search-input { width: 100%; padding: 12px 14px 12px 42px; border-radius: 999px; border: 1px solid var(--border); font-size: 14px; transition: border-color .2s ease; }
        .search-input:focus { outline: none; border-color: var(--brand, #6c63ff); box-shadow: 0 0 0 3px rgba(108,99,255,.12); }
        .search-button { padding: 12px 20px; border-radius: 999px; border: none; background: var(--brand, #6c63ff); color: #fff; font-weight: 600; font-size: 14px; cursor: pointer; transition: background .2s ease; }
        .search-button:hover { background: var(--brand-dark, #574bff); }
        .search-clear { padding: 11px 18px; border-radius: 999px; border: 1px solid var(--border); background: #fff; color: var(--muted-strong, #3f4860); font-weight: 600; font-size: 14px; text-decoration: none; transition: border-color .2s ease, color .2s ease, background .2s ease; }
        .search-clear:hover { border-color: var(--brand, #6c63ff); color: var(--brand, #6c63ff); background: rgba(108,99,255,.08); }
        .results-hint { margin: -8px 0 18px; color: #6a5120; font-size: 13px; }

        .artist-card .actions-inline {
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }

        .artist-card .actions-inline .btn {
            min-height: 42px;
            border-radius: 10px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(186, 142, 35, 0.10);
        }

        .artist-card .actions-inline .btn-secondary {
            background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
            border: 1px solid #f0dfb4;
            color: #4a3a14;
        }

        .artist-card .actions-inline .btn-secondary:hover {
            background: linear-gradient(180deg, #fffaf0 0%, #fff2da 100%);
            color: #3f2f12;
        }

        .artist-card .actions-inline .btn-success {
            background: linear-gradient(135deg, #d8b566 0%, #c59b3d 100%);
            border: 1px solid #c9a14a;
            color: #2f2410;
        }

        .artist-card .actions-inline .btn-success:hover {
            box-shadow: 0 10px 20px rgba(186, 142, 35, 0.18);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="director-dashboard-page">
    <?php
    $directorSidebarDramaId = (int)$dramaId;
    $directorSidebarActive = 'artist-roles';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <main class="main--content">
        <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>" class="back-button"><i class="bx bx-arrow-left"></i>Back to Manage Roles</a>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?? 'info' ?>" style="padding: 16px; border-radius: 12px; margin-bottom: 20px; background: <?= ($_SESSION['message_type'] ?? '') === 'success' ? '#d4edda' : (($_SESSION['message_type'] ?? '') === 'error' ? '#f8d7da' : '#d1ecf1') ?>; color: <?= ($_SESSION['message_type'] ?? '') === 'success' ? '#155724' : (($_SESSION['message_type'] ?? '') === 'error' ? '#721c24' : '#0c5460') ?>; border: 1px solid <?= ($_SESSION['message_type'] ?? '') === 'success' ? '#c3e6cb' : (($_SESSION['message_type'] ?? '') === 'error' ? '#f5c6cb' : '#bee5eb') ?>;">
                <i class="bx bx-<?= ($_SESSION['message_type'] ?? '') === 'success' ? 'check-circle' : (($_SESSION['message_type'] ?? '') === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($drama->drama_name ?? 'Drama') ?></span>
                <h2>Invite Artists for "<?= esc($roleName) ?>"</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Browse artists, review experience, and send collaboration requests.</p>
            </div>
            <div class="user--info">
                <?php
                $directorProfileImageSrc = $profileImageSrc;
                $directorRoleLabel = 'Director';
                include __DIR__ . '/_partials/user_menu.php';
                ?>
            </div>
        </div>

        <?php 
        $isRoleFull = isset($role->positions_filled, $role->positions_available) && 
                      (int)$role->positions_filled >= (int)$role->positions_available;
        ?>
        <?php if ($isRoleFull): ?>
            <div style="padding: 16px 20px; margin-bottom: 20px; background: rgba(244,67,54,0.1); border-left: 4px solid #f44336; border-radius: 8px; color: #721c24;">
                <i class="bx bx-exclamation-triangle" style="color: #d32f2f; margin-right: 10px;"></i>
                <strong>All positions filled for this role.</strong> You cannot send new requests until you remove a currently assigned artist. 
                <a href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>" style="color: #d32f2f; text-decoration: underline; margin-left: 8px;">View assigned artists</a>
            </div>
        <?php endif; ?>

        <section class="search-card">
            <form class="search-form" method="get" action="<?= ROOT ?>/director/search_artists">
                <input type="hidden" name="drama_id" value="<?= esc($dramaId) ?>">
                <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                <div class="search-input-wrapper">
                    <i class="bx bx-search"></i>
                    <input type="text" name="search" value="<?= esc($searchTerm) ?>" class="search-input" placeholder="Search artists by name">
                </div>
                <button type="submit" class="search-button"><i class="bx bx-search" style="margin-right:6px;"></i>Search</button>
                <?php if ($searchTerm !== ''): ?>
                    <a href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>" class="search-clear">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <?php if (empty($artists)): ?>
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
                Showing <?= count($artists) ?> artist<?= count($artists) === 1 ? '' : 's' ?><?php if ($searchTerm !== ''): ?> for "<?= esc($searchTerm) ?>"<?php endif; ?>.
            </p>
            <section class="artist-grid">
                <?php foreach ($artists as $artist): ?>
                    <?php
                        $requestStatus = strtolower($artist->request_status ?? '');
                        $assignmentStatus = strtolower($artist->assignment_status ?? '');
                        $isAssigned = $assignmentStatus === 'active';
                        $hasPendingRequest = in_array($requestStatus, ['pending','interview'], true);
                        
                        // Get artist profile image
                        $artistImageSrc = ROOT . '/assets/images/default-avatar.jpg';
                        if (!empty($artist->profile_image)) {
                            $imageValue = str_replace('\\', '/', $artist->profile_image);
                            if (strpos($imageValue, '/') !== false) {
                                $artistImageSrc = ROOT . '/' . ltrim($imageValue, '/');
                            } else {
                                $artistImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
                            }
                        } elseif (!empty($artist->nic_photo)) {
                            $artistImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $artist->nic_photo), '/');
                        }
                    ?>
                    <article class="artist-card">
                        <div class="artist-card-header">
                            <img src="<?= esc($artistImageSrc) ?>" alt="<?= esc($artist->full_name ?? 'Artist') ?>" class="artist-avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 6px; font-size: 20px;"><?= esc($artist->full_name ?? 'Artist') ?></h3>
                                <div class="artist-status">
                                    <?php if (!empty($artist->years_experience)): ?>
                                        Experience: <?= esc($artist->years_experience) ?> years
                                    <?php else: ?>
                                        Experience: Not specified
                                    <?php endif; ?>
                                </div>
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
                            <a href="<?= ROOT ?>/director/artist_profile?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>&artist_id=<?= esc($artist->id) ?>" class="btn btn-secondary"><i class="bx bx-id-card"></i>View Profile</a>
                            <?php if ($isAssigned): ?>
                                <button type="button" class="btn btn-secondary" disabled><i class="bx bx-lock"></i>Assigned</button>
                            <?php elseif ($hasPendingRequest): ?>
                                <button type="button" class="btn btn-secondary" disabled><i class="bx bx-hourglass-half"></i>Awaiting reply</button>
                            <?php elseif ($isRoleFull): ?>
                                <button type="button" class="btn btn-secondary" disabled title="All positions filled"><i class="bx bx-ban"></i>Role Full</button>
                            <?php else: ?>
                                <form class="js-role-action" data-action="request" action="<?= ROOT ?>/director/send_role_request?drama_id=<?= esc($dramaId) ?>" method="POST" style="margin: 0;">
                                    <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                                    <input type="hidden" name="artist_id" value="<?= esc($artist->id) ?>">
                                    <button type="submit" class="btn btn-success"><i class="bx bx-paper-plane"></i>Request Artist</button>
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
