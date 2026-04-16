<?php
$directorSidebarDramaId = isset($directorSidebarDramaId) ? (int)$directorSidebarDramaId : 0;
$directorSidebarActive = $directorSidebarActive ?? 'dashboard';
?>
<aside class="sidebar">
    <div class="logo">
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $directorSidebarDramaId ?>" class="logo-link">
            <img src="/Rangamadala/public/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" class="logo-image">
        </a>
    </div>
    <ul class="menu">
        <li class="<?= $directorSidebarActive === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $directorSidebarDramaId ?>">
                <i class="bx bx-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= $directorSidebarActive === 'drama-details' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= $directorSidebarDramaId ?>">
                <i class="bx bx-film"></i>
                <span>Drama Details</span>
            </a>
        </li>
        <li class="<?= $directorSidebarActive === 'artist-roles' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= $directorSidebarDramaId ?>">
                <i class="bx bxs-mask"></i>
                <span>Artist Roles</span>
            </a>
        </li>
        <li class="<?= $directorSidebarActive === 'production-manager' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= $directorSidebarDramaId ?>">
                <i class="bx bx-briefcase"></i>
                <span>Production Manager</span>
            </a>
        </li>
        <li class="<?= $directorSidebarActive === 'schedule' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= $directorSidebarDramaId ?>">
                <i class="bx bx-calendar-alt"></i>
                <span>Schedule</span>
            </a>
        </li>
        <li class="<?= $directorSidebarActive === 'services-budget' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= $directorSidebarDramaId ?>">
                <i class="bx bx-money"></i>
                <span>Services & Budget</span>
            </a>
        </li>
    </ul>
</aside>
