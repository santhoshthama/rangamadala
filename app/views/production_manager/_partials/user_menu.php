<?php
/**
 * Production Manager User Menu Partial
 * Displays role badge and user profile dropdown menu
 * 
 * Required variables:
 * - $pmProfileImageSrc (string): User's profile image URL
 * - $pmRoleLabel (string, optional): Role display label, default: 'Production Manager'
 * - $pmProfileUrl (string, optional): URL to profile page, default: ROOT . '/profile'
 * - $pmLogoutUrl (string, optional): URL to logout, default: ROOT . '/logout'
 */

$pmProfileImageSrc = $pmProfileImageSrc ?? (ROOT . '/assets/images/default-avatar.jpg');
$pmRoleLabel = $pmRoleLabel ?? 'Production Manager';
$pmProfileUrl = $pmProfileUrl ?? (ROOT . '/profile');
$pmLogoutUrl = $pmLogoutUrl ?? (ROOT . '/logout');
?>

<div class="role-badge">
    <i class="bx bx-user-tie"></i>
    <?= esc($pmRoleLabel) ?>
</div>
<div class="user-menu" id="userMenu">
    <div class="user-menu-trigger" id="user-menu-trigger" aria-haspopup="true" aria-expanded="false" aria-label="Open user menu">
        <div class="user-avatar-small">
            <img src="<?= esc($pmProfileImageSrc) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
        </div>
    </div>
    <div class="user-menu-dropdown">
        <a href="<?= $pmProfileUrl ?>" class="user-menu-item">
            <i class='bx bxs-user icon'></i>
            <span>Profile</span>
        </a>
        <a href="<?= $pmLogoutUrl ?>" class="user-menu-item">
            <i class='bx bx-log-out icon'></i>
            <span>Logout</span>
        </a>
    </div>
</div>
