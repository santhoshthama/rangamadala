<?php
/**
 * Production Manager Sidebar Partial
 * Shared navigation sidebar for all Production Manager pages
 * 
 * Required variables:
 * - $dramaId (int): The current drama ID for query parameters
 * - $currentPage (string, optional): The current page name to set active state
 *   Accepted values: 'dashboard', 'manage_services', 'manage_budget', 'manage_schedule'
 */

$dramaId = isset($dramaId) ? (int)$dramaId : 0;
$currentPage = isset($currentPage) ? (string)$currentPage : '';
?>

<aside class="sidebar">
    <div class="logo">
        <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= $dramaId ?>" class="logo-link">
            <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" class="logo-image">
        </a>
    </div>
    <ul class="menu">
        <li <?= $currentPage === 'dashboard' ? 'class="active"' : '' ?>>
            <a href="<?= ROOT ?>/production_manager/dashboard?drama_id=<?= $dramaId ?>">
                <i class="bx bx-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li <?= $currentPage === 'manage_services' ? 'class="active"' : '' ?>>
            <a href="<?= ROOT ?>/production_manager/manage_services?drama_id=<?= $dramaId ?>">
                <i class="bx bx-briefcase"></i>
                <span>Manage Services</span>
            </a>
        </li>
        <li <?= $currentPage === 'manage_budget' ? 'class="active"' : '' ?>>
            <a href="<?= ROOT ?>/production_manager/manage_budget?drama_id=<?= $dramaId ?>">
                <i class="bx bx-money"></i>
                <span>Budget Management</span>
            </a>
        </li>
        <li <?= $currentPage === 'manage_schedule' ? 'class="active"' : '' ?>>
            <a href="<?= ROOT ?>/production_manager/manage_schedule?drama_id=<?= $dramaId ?>">
                <i class="bx bx-calendar-alt"></i>
                <span>Service Schedule</span>
            </a>
        </li>
        <li>
            <a href="<?= ROOT ?>/artistdashboard/calendar">
                <i class="bx bx-calendar-week"></i>
                <span>Artist Calendar</span>
            </a>
        </li>
    </ul>
</aside>
