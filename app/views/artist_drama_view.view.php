<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($drama->drama_name ?? 'Drama Details') ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="<?=ROOT?>/artistdashboard">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </a>
            </li>
            <li class="active">
                <a href="#">
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?=ROOT?>/artistprofile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
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
        <a href="<?=ROOT?>/artistdashboard" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Drama Details</span>
                <h2><?= esc($drama->drama_name ?? 'Unknown Drama') ?></h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 8px;">
                    <?= !empty($drama->description) ? esc($drama->description) : 'No description provided yet.' ?>
                </p>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="fas fa-user-tie"></i> Actor
                </div>
                <img src="<?=ROOT?>/assets/images/default-avatar.jpg" alt="Artist Avatar">
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- My Role Card (if assigned) -->
        <?php if (isset($my_role) && $my_role): ?>
        <div class="card-section" style="background: linear-gradient(135deg, #ffc107, #e0a800); color: #1f1f1f; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                <div style="flex: 1;">
                    <h2 style="margin: 0 0 10px 0; font-size: 24px; font-weight: 700; color: #1f1f1f;">
                        <i class="fas fa-star"></i> Your Role: <?= esc($my_role->role_name) ?>
                    </h2>
                    <p style="margin: 0; font-size: 16px; line-height: 1.5; color: #1f1f1f;">
                        <strong>Type:</strong> <?= esc(ucfirst($my_role->role_type)) ?>
                        <?php if (!empty($my_role->salary)): ?>
                        | <strong>Salary:</strong> LKR <?= number_format($my_role->salary) ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($my_role->role_description)): ?>
                    <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 14px; color: #1f1f1f;">
                        <?= esc($my_role->role_description) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Content Section -->
        <div class="content">
            <div class="profile-container" style="grid-template-columns: 1fr;">
                <div class="details">
                    <!-- Drama Information -->
                    <div class="card-section">
                        <h3><i class="fas fa-film"></i> Drama Information</h3>
                        
                        <div class="drama-info">
                            <div class="service-info-item">
                                <span class="service-info-label">Drama Name</span>
                                <span class="service-info-value"><?= esc($drama->drama_name) ?></span>
                            </div>

                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Number</span>
                                <span class="service-info-value"><?= esc($drama->certificate_number ?? 'N/A') ?></span>
                            </div>

                            <div class="service-info-item">
                                <span class="service-info-label">Drama Owner</span>
                                <span class="service-info-value"><?= esc($drama->owner_name ?? 'N/A') ?></span>
                            </div>

                            <div class="service-info-item">
                                <span class="service-info-label">Created On</span>
                                <span class="service-info-value"><?= isset($drama->created_at) ? date('M d, Y', strtotime($drama->created_at)) : 'N/A' ?></span>
                            </div>

                            <?php if (!empty($drama->description)): ?>
                            <div class="service-info-item" style="flex-direction: column; align-items: flex-start; gap: 6px;">
                                <span class="service-info-label">Description</span>
                                <span class="service-info-value" style="white-space: pre-wrap;"><?= esc($drama->description) ?></span>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($drama->certificate_image)): ?>
                            <div class="service-info-item">
                                <span class="service-info-label">Certificate Document</span>
                                <span class="service-info-value">
                                    <a href="<?= ROOT ?>/uploads/certificates/<?= esc(rawurlencode($drama->certificate_image)) ?>" 
                                       target="_blank" 
                                       rel="noopener">
                                        View certificate
                                    </a>
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- All Roles in Drama -->
                    <div class="card-section" style="margin-top: 30px;">
                        <h3>
                            <span><i class="fas fa-users"></i> All Roles in This Drama</span>
                        </h3>
                        
                        <?php if (empty($roles)): ?>
                            <div class="no-results">
                                <i class="fas fa-user-times"></i>
                                <h3>No Roles Added Yet</h3>
                                <p>The director hasn't added any roles to this drama yet.</p>
                            </div>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($roles as $role): ?>
                                    <li>
                                        <div>
                                            <strong style="color: <?= isset($my_role) && $my_role->id == $role->id ? 'var(--brand)' : 'var(--ink)' ?>;">
                                                <?= esc($role->role_name) ?>
                                                <?php if (isset($my_role) && $my_role->id == $role->id): ?>
                                                    <i class="fas fa-star" style="margin-left: 8px; color: var(--brand);"></i>
                                                <?php endif; ?>
                                            </strong>
                                            <div class="request-info">
                                                Type: <?= esc(ucfirst($role->role_type)) ?>
                                                <?php if (!empty($role->salary)): ?>
                                                    | Salary: LKR <?= number_format($role->salary) ?>
                                                <?php endif; ?>
                                                <?php if (!empty($role->role_description)): ?>
                                                    <br><?= esc($role->role_description) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-end;">
                                            <?php if ($role->is_filled): ?>
                                                <span class="status-badge assigned">
                                                    <i class="fas fa-user-check"></i> Filled
                                                </span>
                                                <?php if (!empty($role->assigned_artist_name)): ?>
                                                    <span style="font-size: 12px; color: var(--muted);">
                                                        <?php if (isset($my_role) && $my_role->id == $role->id): ?>
                                                            <strong style="color: var(--brand);">You</strong>
                                                        <?php else: ?>
                                                            <?= esc($role->assigned_artist_name) ?>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="status-badge pending">
                                                    <i class="fas fa-hourglass-half"></i> Vacant
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <!-- Schedule/Rehearsal Information -->
                    <div class="card-section" style="margin-top: 30px;">
                        <h3>
                            <span><i class="fas fa-calendar-alt"></i> Schedule & Rehearsals</span>
                        </h3>
                        
                        <?php if (empty($schedules)): ?>
                            <div class="view-only-notice" style="margin-top: 15px;">
                                <i class="fas fa-info-circle"></i>
                                The director hasn't added any rehearsal schedules yet.
                            </div>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($schedules as $schedule): ?>
                                    <li>
                                        <div>
                                            <strong><?= date('M d, Y', strtotime($schedule->date)) ?> at <?= esc($schedule->time) ?></strong>
                                            <div class="request-info">
                                                Venue: <?= esc($schedule->venue) ?>
                                                <?php if (!empty($schedule->details)): ?>
                                                    <br><?= esc($schedule->details) ?>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
