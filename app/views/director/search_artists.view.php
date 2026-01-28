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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Artist - <?= esc($roleName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .filters-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm, 0 4px 14px rgba(15,23,42,.08)); }
        .filters-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
        .form-group { display: flex; flex-direction: column; }
        label { font-weight: 600; margin-bottom: 6px; }
        .artist-grid { display: grid; gap: 20px; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); }
        .artist-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 20px; box-shadow: var(--shadow-xs, 0 2px 10px rgba(15,23,42,.05)); display: flex; flex-direction: column; gap: 12px; }
        .artist-card-header { display: flex; align-items: center; gap: 14px; }
        .artist-avatar { width: 64px; height: 64px; border-radius: 12px; object-fit: cover; border: 2px solid var(--border); }
        .artist-status { font-size: 12px; color: var(--muted); }
        .badge { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-available { background: rgba(76,175,80,.15); color: #256029; }
        .badge-requested { background: rgba(255,193,7,.18); color: #7a4f02; }
        .badge-assigned { background: rgba(0,123,255,.15); color: #0b5394; }
        .empty-state { padding: 40px; text-align: center; border: 1px dashed var(--border); border-radius: 16px; color: var(--muted); background: rgba(248,249,252,.6); }
        .search-card { background: #fff; border-radius: 16px; border: 1px solid var(--border); padding: 18px 24px; margin-bottom: 24px; box-shadow: var(--shadow-xs, 0 2px 10px rgba(15,23,42,.05)); }
        .search-form { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .search-input-wrapper { flex: 1; min-width: 240px; position: relative; }
        .search-input-wrapper i { position: absolute; top: 50%; left: 14px; transform: translateY(-50%); color: var(--muted); font-size: 14px; }
        .search-input { width: 100%; padding: 12px 14px 12px 42px; border-radius: 999px; border: 1px solid var(--border); font-size: 14px; transition: border-color .2s ease; }
        .search-input:focus { outline: none; border-color: var(--brand, #6c63ff); box-shadow: 0 0 0 3px rgba(108,99,255,.12); }
        .search-button { padding: 12px 20px; border-radius: 999px; border: none; background: var(--brand, #6c63ff); color: #fff; font-weight: 600; font-size: 14px; cursor: pointer; transition: background .2s ease; }
        .search-button:hover { background: var(--brand-dark, #574bff); }
        .search-clear { padding: 11px 18px; border-radius: 999px; border: 1px solid var(--border); background: #fff; color: var(--muted-strong, #3f4860); font-weight: 600; font-size: 14px; text-decoration: none; transition: border-color .2s ease, color .2s ease, background .2s ease; }
        .search-clear:hover { border-color: var(--brand, #6c63ff); color: var(--brand, #6c63ff); background: rgba(108,99,255,.08); }
        .results-hint { margin: -8px 0 18px; color: var(--muted); font-size: 13px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo"><h2>🎭</h2></div>
        <ul class="menu">
            <li><a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
<li class="active"><a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-users"></i><span>Artist Roles</span></a></li>
            <li><a href="#"><i class="fas fa-user-search"></i><span>Find Artists</span></a></li>
            <li><a href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>"><i class="fas fa-mask"></i><span><?= esc($roleName) ?></span></a></li>
            <li><a href="<?= ROOT ?>/artistdashboard"><i class="fas fa-arrow-left"></i><span>Back to Profile</span></a></li>
        </ul>
    </aside>

    <main class="main--content">
        <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>" class="back-button"><i class="fas fa-arrow-left"></i>Back to Manage Roles</a>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?? 'info' ?>" style="padding: 16px; border-radius: 12px; margin-bottom: 20px; background: <?= ($_SESSION['message_type'] ?? '') === 'success' ? '#d4edda' : (($_SESSION['message_type'] ?? '') === 'error' ? '#f8d7da' : '#d1ecf1') ?>; color: <?= ($_SESSION['message_type'] ?? '') === 'success' ? '#155724' : (($_SESSION['message_type'] ?? '') === 'error' ? '#721c24' : '#0c5460') ?>; border: 1px solid <?= ($_SESSION['message_type'] ?? '') === 'success' ? '#c3e6cb' : (($_SESSION['message_type'] ?? '') === 'error' ? '#f5c6cb' : '#bee5eb') ?>;">
                <i class="fas fa-<?= ($_SESSION['message_type'] ?? '') === 'success' ? 'check-circle' : (($_SESSION['message_type'] ?? '') === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
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
        </div>

        <?php 
        $isRoleFull = isset($role->positions_filled, $role->positions_available) && 
                      (int)$role->positions_filled >= (int)$role->positions_available;
        ?>
        <?php if ($isRoleFull): ?>
            <div style="padding: 16px 20px; margin-bottom: 20px; background: rgba(244,67,54,0.1); border-left: 4px solid #f44336; border-radius: 8px; color: #721c24;">
                <i class="fas fa-exclamation-triangle" style="color: #d32f2f; margin-right: 10px;"></i>
                <strong>All positions filled for this role.</strong> You cannot send new requests until you remove a currently assigned artist. 
                <a href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>" style="color: #d32f2f; text-decoration: underline; margin-left: 8px;">View assigned artists</a>
            </div>
        <?php endif; ?>

        <section class="search-card">
            <form class="search-form" method="get" action="<?= ROOT ?>/director/search_artists">
                <input type="hidden" name="drama_id" value="<?= esc($dramaId) ?>">
                <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" value="<?= esc($searchTerm) ?>" class="search-input" placeholder="Search artists by name">
                </div>
                <button type="submit" class="search-button"><i class="fas fa-search" style="margin-right:6px;"></i>Search</button>
                <?php if ($searchTerm !== ''): ?>
                    <a href="<?= ROOT ?>/director/search_artists?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>" class="search-clear">Clear</a>
                <?php endif; ?>
            </form>
        </section>

        <?php if (empty($artists)): ?>
            <div class="empty-state">
                <i class="fas fa-users" style="font-size: 28px; display: block; margin-bottom: 12px;"></i>
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
                                <span class="badge badge-assigned"><i class="fas fa-user-check" style="margin-right:6px;"></i>Already assigned</span>
                            <?php elseif ($hasPendingRequest): ?>
                                <span class="badge badge-requested"><i class="fas fa-paper-plane" style="margin-right:6px;"></i>Request pending</span>
                            <?php else: ?>
                                <span class="badge badge-available"><i class="fas fa-circle" style="font-size: 6px; margin-right:6px;"></i>Available</span>
                            <?php endif; ?>
                        </div>

                        <div style="font-size: 13px; color: var(--muted);">
                            <?php if (!empty($artist->email)): ?>Email: <?= esc($artist->email) ?><br><?php endif; ?>
                            <?php if (!empty($artist->phone)): ?>Phone: <?= esc($artist->phone) ?><?php endif; ?>
                        </div>

                        <div class="actions-inline" style="margin-top: auto; gap: 10px; display: flex;">
                            <button type="button" class="btn btn-secondary" disabled><i class="fas fa-id-card"></i>Profile</button>
                            <?php if ($isAssigned): ?>
                                <button type="button" class="btn btn-secondary" disabled><i class="fas fa-lock"></i>Assigned</button>
                            <?php elseif ($hasPendingRequest): ?>
                                <button type="button" class="btn btn-secondary" disabled><i class="fas fa-hourglass-half"></i>Awaiting reply</button>
                            <?php elseif ($isRoleFull): ?>
                                <button type="button" class="btn btn-secondary" disabled title="All positions filled"><i class="fas fa-ban"></i>Role Full</button>
                            <?php else: ?>
                                <form class="js-role-action" data-action="request" action="<?= ROOT ?>/director/send_role_request?drama_id=<?= esc($dramaId) ?>" method="POST" style="margin: 0;">
                                    <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                                    <input type="hidden" name="artist_id" value="<?= esc($artist->id) ?>">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i>Request Artist</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <script src="/Rangamadala/public/assets/JS/manage-roles.js"></script>
</body>
</html>
