<div class="header--wrapper">
    <div class="header--title">
        <span>Service Provider</span>
        <h2><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h2>
    </div>
    <div class="user--info">
        <a href="<?= ROOT ?>/ServiceProviderProfile" style="cursor: pointer;">
            <img src="<?= !empty($provider->profile_image) ? ROOT . '/uploads/profile_images/' . $provider->profile_image : ROOT . '/uploads/profile_images/default_user.jpg' ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.jpg'" style="cursor: pointer;">
        </a>
        <a href="<?= ROOT ?>/Logout" class="header-logout" title="Logout">
            <i class="fas fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>
</div>
