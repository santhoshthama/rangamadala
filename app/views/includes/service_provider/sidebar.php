<?php
/**
 * Service Provider Sidebar Navigation
 * 
 * Usage: Set $activePage variable before including this file
 * Example: $activePage = 'dashboard'; or $activePage = 'profile';
 */

// Default to no active page if not set
$activePage = $activePage ?? '';

$spUnreadCount = 0;
if (isset($_SESSION['user_id']) && (($_SESSION['user_role'] ?? '') === 'service_provider')) {
    try {
        $notificationModel = new M_notification();
        $providerTypes = [
            'service_request_created_pm',
            'provider_quote_confirmed_by_pm',
            'provider_quote_rejected_by_pm',
            'service_request_cancelled_by_pm',
            'payment_submitted_by_pm',
            'payment_completed_by_pm',
        ];
        $spUnreadCount = (int)$notificationModel->getUnreadCountByTypes((int)$_SESSION['user_id'], $providerTypes);
    } catch (Throwable $e) {
        $spUnreadCount = 0;
    }
}
?>

<style>
    .notification-dot {
        width: 8px;
        height: 8px;
        background: #ffffff;
        border-radius: 50%;
        border: none;
        margin-left: 8px;
        vertical-align: middle;
        display: none;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.25);
    }

    .notification-dot.is-visible {
        display: block;
        animation: notificationPulse 1.8s infinite;
    }

    @keyframes notificationPulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

<div class="sidebar">
    <div class="logo">
        <a href="<?= ROOT ?>/ServiceProviderDashboard" class="logo-link" aria-label="Service Provider Dashboard">
            <img class="logo-image" src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala">
        </a>
    </div>

    <ul class="menu">
        <li class="<?= $activePage === 'home' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/Home">
                <i class="bx bx-home"></i>
                <span>Home</span>
            </a>
        </li>
        <li class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/ServiceProviderDashboard">
                <i class="bx bx-tachometer"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= $activePage === 'requests' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/ServiceRequests">
                <i class="bx bx-clipboard"></i>
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
                <i class="bx bx-money"></i>
                <span>Payments</span>
            </a>
        </li>
        <li class="<?= $activePage === 'notifications' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/ServiceProviderNotifications/open" id="spNotificationsLink">
                <i class="bx bx-bell"></i>
                <span>Notifications</span>
                <span class="notification-dot <?= $spUnreadCount > 0 ? 'is-visible' : '' ?>" id="spNotificationDot"></span>
            </a>
        </li>
        <li class="<?= $activePage === 'reports' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/ServiceReports">
                <i class="bx bx-file"></i>
                <span>Reports</span>
            </a>
        </li>
        <!-- <li class="<?= $activePage === 'profile' ? 'active' : '' ?>">
            <a href="<?= ROOT ?>/ServiceProviderProfile">
                <i class="bx bx-user"></i>
                <span>Profile</span>
            </a>
        </li> -->
    </ul>
</div>

<script>
    (function () {
        const dot = document.getElementById('spNotificationDot');
        const link = document.getElementById('spNotificationsLink');

        if (!dot || !link) {
            return;
        }

        function applyUnreadState(hasUnread) {
            if (hasUnread) {
                dot.classList.add('is-visible');
            } else {
                dot.classList.remove('is-visible');
            }
        }

        async function refreshUnreadCount() {
            try {
                const response = await fetch('<?= ROOT ?>/ServiceProviderNotifications/unreadCount', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const raw = await response.text();
                const payload = JSON.parse(raw);
                if (payload && payload.success) {
                    applyUnreadState((payload.unreadCount || 0) > 0);
                }
            } catch (error) {
                // Keep existing indicator state when polling fails
            }
        }

        link.addEventListener('click', function () {
            applyUnreadState(false);
        });

        setInterval(refreshUnreadCount, 30000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                refreshUnreadCount();
            }
        });
    })();
</script>
