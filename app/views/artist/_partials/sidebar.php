<?php
$artistSidebarActive = $artistSidebarActive ?? 'dashboard';
?>
<aside class="sidebar">
    <div class="logo">
        <a href="<?= ROOT ?>/artistdashboard" class="logo-link">
            <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" class="logo-image">
        </a>
    </div>
    <ul class="menu">
        <li class="<?= $artistSidebarActive === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/artistdashboard">
                <i class="bx bxs-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= $artistSidebarActive === 'notifications' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/artistdashboard/notifications">
                <i class="bx bxs-bell"></i>
                <span>Notifications</span>
            </a>
        </li>
        <li class="<?= $artistSidebarActive === 'vacancies' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/artistdashboard/browse_vacancies">
                <i class="bx bxs-megaphone"></i>
                <span>View All Vacancies</span>
            </a>
        </li>
        <li class="<?= $artistSidebarActive === 'classes' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/artistdashboard/classes">
                <i class="bx bxs-graduation"></i>
                <span>Classes</span>
            </a>
        </li>
        <li class="<?= $artistSidebarActive === 'showings' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/artistdashboard?tab=showings&showings_tab=requests">
                <i class="bx bx-calendar-event"></i>
                <span>Showings</span>
            </a>
        </li>
    </ul>
</aside>
