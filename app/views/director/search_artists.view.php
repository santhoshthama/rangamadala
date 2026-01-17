<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$role = $role ?? null;
$artists = isset($artists) && is_array($artists) ? $artists : [];
$filters = isset($filters) && is_array($filters) ? $filters : [];

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
        .artist-status { font-size: 12px; color: var(--muted); }
        .badge { display: inline-flex; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .badge-available { background: rgba(76,175,80,.15); color: #256029; }
        .badge-requested { background: rgba(255,193,7,.18); color: #7a4f02; }
        .badge-assigned { background: rgba(0,123,255,.15); color: #0b5394; }
        .empty-state { padding: 40px; text-align: center; border: 1px dashed var(--border); border-radius: 16px; color: var(--muted); background: rgba(248,249,252,.6); }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="logo"><h2>🎭</h2></div>
        <ul class="menu">
            <li><a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li><a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>"><i class="fas fa-users"></i><span>Artist Roles</span></a></li>
            <li class="active"><a href="#"><i class="fas fa-user-search"></i><span>Find Artists</span></a></li>
            <li><a href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>"><i class="fas fa-mask"></i><span><?= esc($roleName) ?></span></a></li>
            <li><a href="<?= ROOT ?>/artistdashboard"><i class="fas fa-arrow-left"></i><span>Back to Profile</span></a></li>
        </ul>
    </aside>

    <main class="main--content">
        <a href="<?= ROOT ?>/director/view_role?drama_id=<?= esc($dramaId) ?>&role_id=<?= esc($roleId) ?>" class="back-button"><i class="fas fa-arrow-left"></i>Back to Role</a>

        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($drama->drama_name ?? 'Drama') ?></span>
                <h2>Invite Artists for "<?= esc($roleName) ?>"</h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">Browse artists, review experience, and send collaboration requests.</p>
            </div>
        </div>

        <section class="filters-card">
            <form class="filters-grid" method="GET" action="<?= ROOT ?>/director/search_artists">
                <input type="hidden" name="drama_id" value="<?= esc($dramaId) ?>">
                <input type="hidden" name="role_id" value="<?= esc($roleId) ?>">
                <div class="form-group">
                    <label for="filter_search">Name or keyword</label>
                    <input type="text" id="filter_search" name="q" class="form-control" placeholder="Eg: Kasun" value="<?= esc($filters['search'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="filter_min_exp">Min experience (years)</label>
                    <input type="number" min="0" id="filter_min_exp" name="min_exp" class="form-control" value="<?= esc($filters['min_experience'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="filter_max_exp">Max experience (years)</label>
                    <input type="number" min="0" id="filter_max_exp" name="max_exp" class="form-control" value="<?= esc($filters['max_experience'] ?? '') ?>">
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i>Search</button>
                </div>
            </form>
        </section>

        <?php if (empty($artists)): ?>
            <div class="empty-state">
                <i class="fas fa-users" style="font-size: 28px; display: block; margin-bottom: 12px;"></i>
                No artists matched your filters. Try broadening your search or invite artists directly by sharing the vacancy.
            </div>
        <?php else: ?>
            <section class="artist-grid">
                <?php foreach ($artists as $artist): ?>
                    <?php
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
