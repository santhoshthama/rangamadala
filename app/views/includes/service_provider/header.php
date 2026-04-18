<div class="header--wrapper">
    <div class="header--title">
        <span>Service Provider</span>
        <h2><?= isset($pageTitle) ? $pageTitle : 'Dashboard' ?></h2>
    </div>
    <?php
    $serviceProviderHeaderImage = ROOT . '/uploads/profile_images/user_profile.png';
    if (isset($provider) && !empty($provider->profile_image)) {
        $serviceProviderHeaderImage = ROOT . '/uploads/profile_images/' . $provider->profile_image;
    }
    ?>
    <div class="dashboard-header-actions">
        <div class="audience-role-badge">
            <i class='bx bxs-star'></i>
            <span>Service Provider</span>
        </div>

        <div class="user-menu" id="userMenu">
            <div class="user-menu-trigger" id="user-menu-trigger" aria-haspopup="true" aria-expanded="false" aria-label="Open user menu">
                <div class="user-avatar-small">
                    <img
                        src="<?= htmlspecialchars($serviceProviderHeaderImage) ?>"
                        onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'"
                        alt="User Avatar" />
                </div>
            </div>

            <div class="user-menu-dropdown">
                <a href="<?= ROOT ?>/ServiceProviderProfile" class="user-menu-item">
                    <i class='icon bx bx-user'></i>
                    <span>Profile</span>
                </a>

                <a href="<?= ROOT ?>/Logout" class="user-menu-item">
                    <i class='icon bx bx-log-out'></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    if (!window.__serviceProviderHeaderMenuInit) {
        window.__serviceProviderHeaderMenuInit = true;

        document.addEventListener('DOMContentLoaded', function () {
            var userMenu = document.getElementById('userMenu');
            var userMenuTrigger = document.getElementById('user-menu-trigger');

            if (!userMenu || !userMenuTrigger) {
                return;
            }

            userMenuTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                userMenu.classList.toggle('active');
                userMenuTrigger.setAttribute('aria-expanded', userMenu.classList.contains('active') ? 'true' : 'false');
            });

            document.addEventListener('click', function (e) {
                if (!userMenu.contains(e.target)) {
                    userMenu.classList.remove('active');
                    userMenuTrigger.setAttribute('aria-expanded', 'false');
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && userMenu.classList.contains('active')) {
                    userMenu.classList.remove('active');
                    userMenuTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        });
    }
</script>
