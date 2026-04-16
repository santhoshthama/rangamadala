<?php
$directorProfileImageSrc = $directorProfileImageSrc ?? (ROOT . '/assets/images/default-avatar.jpg');
$directorRoleLabel = $directorRoleLabel ?? 'Director';
$directorProfileUrl = $directorProfileUrl ?? (ROOT . '/profile');
$directorLogoutUrl = $directorLogoutUrl ?? (ROOT . '/logout');
?>
<div class="role-badge">
    <i class="bx bx-star"></i>
    <?= esc($directorRoleLabel) ?>
</div>
<div class="user-menu" id="userMenu">
    <div class="user-menu-trigger" id="user-menu-trigger" aria-haspopup="true" aria-expanded="false" aria-label="Open user menu">
        <div class="user-avatar-small">
            <img src="<?= esc($directorProfileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
        </div>
    </div>
    <div class="user-menu-dropdown">
        <a href="<?= $directorProfileUrl ?>" class="user-menu-item">
            <i class='bx bxs-user icon'></i>
            <span>Profile</span>
        </a>
        <a href="<?= $directorLogoutUrl ?>" class="user-menu-item">
            <i class='bx bx-log-out icon'></i>
            <span>Logout</span>
        </a>
    </div>
</div>
