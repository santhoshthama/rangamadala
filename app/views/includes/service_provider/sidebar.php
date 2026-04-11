<?php
/**
 * Service Provider Sidebar Navigation
 * 
 * Usage: Set $activePage variable before including this file
 * Example: $activePage = 'dashboard'; or $activePage = 'profile';
 */

// Default to no active page if not set
$activePage = $activePage ?? '';
?>

<div class="sidebar">
    <div class="logo">
        <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala" style="width: 100%; max-width: 180px; display: block; margin: 0 auto 30px auto; padding: 20px 0;">
        <ul class="menu">
            <li class="<?= $activePage === 'home' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/Home">
                    <i class="bx bx-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceProviderDashboard">
                    <i class="bx bx-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= $activePage === 'requests' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceRequests">
                    <i class="bx bx-clipboard-list"></i>
                    <span>Service Requests</span>
                </a>
            </li>
            <li class="<?= $activePage === 'availability' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceAvailability">
                    <i class="bx bx-calendar-check"></i>
                    <span>Availability</span>
                </a>
            </li>
            <li class="<?= $activePage === 'payments' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServicePayment">
                    <i class="bx bx-money-bill-wave"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="<?= $activePage === 'reports' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceReports">
                    <i class="bx bx-file-alt"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceProviderProfile">
                    <i class="bx bx-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/Logout">
                    <i class="bx bx-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
