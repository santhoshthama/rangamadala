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
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
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
                    <i class="fas fa-star"></i> Artist
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

        <!-- Drama Role Vacancies Banner -->
        <div class="card-section" style="background: linear-gradient(135deg, #ba8e23, #a0781e); color: white; padding: 30px; border-radius: var(--radius); margin-bottom: 30px; box-shadow: var(--shadow-md);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <h2 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 700; color: white;">
                        Drama Role Vacancies Now Open!
                    </h2>
                    <p style="margin: 0; opacity: 0.95; font-size: 16px; line-height: 1.5; color: white;">
                        Discover available roles and apply to be part of our upcoming drama productions.
                    </p>
                </div>
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies" class="btn btn-primary" style="background: white; color: var(--brand); font-weight: 600;">
                    <i class="fas fa-search"></i> Search Vacancies
                </a>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="#director" class="nav-tab-btn active" onclick="openTabLink(event, 'director-tab')">
                <i class="fas fa-film"></i> As Director (<?= isset($stats['as_director']) ? $stats['as_director'] : 0 ?>)
            </a>
            <a href="#manager" class="nav-tab-btn" onclick="openTabLink(event, 'manager-tab')">
                <i class="fas fa-briefcase"></i> As Production Manager (<?= isset($stats['as_manager']) ? $stats['as_manager'] : 0 ?>)
            </a>
            <a href="#actor" class="nav-tab-btn" onclick="openTabLink(event, 'actor-tab')">
                <i class="fas fa-user-tie"></i> As Actor (<?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?>)
            </a>
            <a href="#requests" class="nav-tab-btn" onclick="openTabLink(event, 'requests-tab')">
                <i class="fas fa-envelope"></i> Requests 
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
                            <span><i class="fas fa-film"></i> Dramas You're Directing</span>
                            <?php if (isset($dramas_as_director) && !empty($dramas_as_director)): ?>
                                <a href="<?=ROOT?>/createDrama" class="btn btn-primary" style="font-size: 12px; padding: 8px 16px;">
                                    <i class="fas fa-plus"></i> Create New Drama
                                </a>
                            <?php endif; ?>
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
                                        <button class="btn btn-primary" style="flex: 1;" onclick="handleDirectorManage(<?=$drama->id?>)">
                                            <i class="fas fa-tachometer-alt"></i> Manage
                                        </button>
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
                            <span><i class="fas fa-briefcase"></i> Dramas You're Managing as Production Manager</span>
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
                                        <button class="btn btn-success" style="flex: 1;" onclick="handlePMManage(<?=$drama->id?>)">
                                            <i class="fas fa-tasks"></i> Manage
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
                            <span><i class="fas fa-user-tie"></i> Your Acting Roles</span>
                        </h3>
                    <?php if (!isset($roles_as_actor) || empty($roles_as_actor)): ?>
                        <div class="no-results">
                            <i class="fas fa-user-tie"></i>
                            <h3>No Acting Roles</h3>
                            <p>You haven't been cast in any roles yet. Browse available vacancies!</p>
                            <button class="btn btn-primary" style="margin-top: 16px;" onclick="window.location.href='<?=ROOT?>/artistdashboard/browse_vacancies'">
                                <i class="fas fa-search"></i> Browse Vacancies
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($roles_as_actor as $role): ?>
                                <div class="artist-card">
                                    <div class="artist-header" style="background: linear-gradient(135deg, #ffc107, #e0a800); color: #1f1f1f;">
                                        <h3 class="artist-name" style="color: #1f1f1f;"><?= esc($role->role_name) ?></h3>
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
                                                    <i class="fas fa-check-circle"></i> Active
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <button class="btn btn-warning" style="flex: 1;" onclick="window.location.href='<?=ROOT?>/artistdashboard/view_drama?drama_id=<?=$role->drama_id?>'">
                                            <i class="fas fa-eye"></i> View Drama
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- Requests Tab -->
                <div id="requests-tab" class="tab-content">
                    
                    <!-- Production Manager Requests -->
                    <?php if (isset($pm_requests) && !empty($pm_requests)): ?>
                        <h3 style="margin-bottom: 20px; color: var(--ink);">
                            <i class="fas fa-user-tie"></i> Production Manager Requests
                        </h3>
                        <div style="display: grid; gap: 16px; margin-bottom: 40px;">
                            <?php foreach ($pm_requests as $pm_request): ?>
                                <div class="role-info-card">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
                                        <div>
                                            <h3 style="color: var(--brand); margin-bottom: 8px;">
                                                <i class="fas fa-film"></i> <?= esc($pm_request->drama_name) ?>
                                            </h3>
                                            <p style="color: var(--muted); font-size: 13px;">
                                                <strong>Director:</strong> <?= esc($pm_request->director_name) ?>
                                            </p>
                                            <p style="color: var(--muted); font-size: 13px;">
                                                <strong>Certificate:</strong> <?= esc($pm_request->certificate_number) ?>
                                            </p>
                                        </div>
                                        <span class="status-badge requested">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    </div>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="fas fa-briefcase"></i> Position:
                                        </span>
                                        <span class="role-info-value">Production Manager</span>
                                    </div>
                                    
                                    <?php if (!empty($pm_request->message)): ?>
                                        <div style="margin: 12px 0; padding: 12px; background: rgba(186, 142, 35, 0.08); border-radius: 8px; border-left: 3px solid var(--brand);">
                                            <strong style="color: var(--ink);"><i class="fas fa-comment"></i> Message from Director:</strong>
                                            <p style="color: #555; margin-top: 6px; font-size: 14px;"><?= esc($pm_request->message) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="role-info-item">
                                        <span class="role-info-label">
                                            <i class="fas fa-calendar"></i> Requested:
                                        </span>
                                        <span class="role-info-value"><?= date('M d, Y g:i A', strtotime($pm_request->requested_at)) ?></span>
                                    </div>
                                    
                                    <div style="margin-top: 12px; padding: 10px; background: rgba(33, 150, 243, 0.08); border-radius: 6px;">
                                        <p style="color: #1976d2; font-size: 13px; margin: 0;">
                                            <i class="fas fa-info-circle"></i> <strong>About this role:</strong> 
                                            As Production Manager, you'll oversee services, budget management, and theater bookings for this drama.
                                        </p>
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px; margin-top: 16px;">
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                            <input type="hidden" name="response" value="accept">
                                            <button type="submit" class="btn btn-success" style="width: 100%;">
                                                <i class="fas fa-check"></i> Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" style="flex: 1;">
                                            <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                            <input type="hidden" name="response" value="reject">
                                            <button type="submit" class="btn btn-danger" style="width: 100%;" 
                                                    onclick="return confirm('Are you sure you want to decline this Production Manager request?');">
                                                <i class="fas fa-times"></i> Decline
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Role Requests -->
                    <h3 style="margin-bottom: 20px; color: var(--ink);">
                        <i class="fas fa-theater-masks"></i> Role Requests
                    </h3>
                    <?php if (!isset($role_requests) || empty($role_requests)): ?>
                        <?php if (!isset($pm_requests) || empty($pm_requests)): ?>
                            <div class="no-results">
                                <i class="fas fa-inbox"></i>
                                <h3>No Pending Requests</h3>
                                <p>You don't have any requests at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="fas fa-inbox"></i>
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
                                                <i class="fas fa-theater-masks"></i> <?= esc($request->drama_name) ?>
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
                                        <span class="role-info-value"><?= isset($request->requested_at) && $request->requested_at ? date('M d, Y', strtotime($request->requested_at)) : 'N/A' ?></span>
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
    </script>
</body>
</html>
