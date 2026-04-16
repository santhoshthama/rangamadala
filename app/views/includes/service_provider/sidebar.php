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
    .notification-icon-wrap {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .notification-dot {
        position: absolute;
        top: -4px;
        right: -6px;
        width: 9px;
        height: 9px;
        background: #ef4444;
        border-radius: 50%;
        border: 2px solid #111827;
        display: none;
        box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.15);
    }

    .notification-dot.is-visible {
        display: block;
        animation: notificationPulse 1.8s infinite;
    }

    .notification-count-badge {
        margin-left: auto;
        min-width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #ef4444;
        color: #ffffff;
        font-size: 11px;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
        padding: 0 5px;
        display: none;
    }

    .notification-count-badge.is-visible {
        display: inline-block;
    }

    @keyframes notificationPulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
</style>

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
            <li class="<?= $activePage === 'notifications' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceProviderNotifications/open" id="spNotificationsLink">
                    <span class="notification-icon-wrap">
                        <i class="fas fa-bell"></i>
                        <span class="notification-dot <?= $spUnreadCount > 0 ? 'is-visible' : '' ?>" id="spNotificationDot"></span>
                    </span>
                    <span>Notifications</span>
                    <span class="notification-count-badge <?= $spUnreadCount > 0 ? 'is-visible' : '' ?>" id="spNotificationBadge"><?= $spUnreadCount ?></span>
                </a>
            </li>
            <li class="<?= $activePage === 'reports' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceReports">
                    <i class="fas fa-file-alt"></i>
                    <span>Reports</span>
                </a>
            </li>
            <!-- <li class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                <a href="<?= ROOT ?>/ServiceProviderProfile">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </li> -->
        </ul>
    </div>
</div>

<script>
    (function () {
        const dot = document.getElementById('spNotificationDot');
        const badge = document.getElementById('spNotificationBadge');
        const link = document.getElementById('spNotificationsLink');

        if (!dot || !badge || !link) {
            return;
        }

        function applyUnreadCount(count) {
            const unread = Number(count) || 0;
            if (unread > 0) {
                dot.classList.add('is-visible');
                badge.classList.add('is-visible');
                badge.textContent = unread > 99 ? '99+' : String(unread);
            } else {
                dot.classList.remove('is-visible');
                badge.classList.remove('is-visible');
                badge.textContent = '0';
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
                    applyUnreadCount(payload.unreadCount || 0);
                }
            } catch (error) {
                // Keep existing indicator state when polling fails
            }
        }

        link.addEventListener('click', function () {
            applyUnreadCount(0);
        });

        setInterval(refreshUnreadCount, 30000);
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden) {
                refreshUnreadCount();
            }
        });
    })();
</script>
