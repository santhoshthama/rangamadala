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
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            <li class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceProviderDashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= $activePage === 'requests' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceRequests">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Service Requests</span>
                </a>
            </li>
            <li class="<?= $activePage === 'availability' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceAvailability">
                    <i class="fas fa-calendar-check"></i>
                    <span>Availability</span>
                </a>
            </li>
            <li class="<?= $activePage === 'payments' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServicePayment">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Payments</span>
                </a>
            </li>
            <li class="<?= $activePage === 'reports' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceReports">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceProviderProfile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/Logout">
                    <i class="fas fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>
