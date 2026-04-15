<?php

class ServiceProviderNotifications
{
    use Controller;

    private function providerNotificationTypes()
    {
        return [
            'service_request_created_pm',
            'provider_quote_confirmed_by_pm',
            'provider_quote_rejected_by_pm',
            'service_request_cancelled_by_pm',
            'payment_submitted_by_pm',
            'payment_completed_by_pm',
        ];
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        if (($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header('Location: ' . ROOT . '/Home');
            exit;
        }

        $providerModel = new M_service_provider();
        $provider = $providerModel->getProviderById($_SESSION['user_id']);

        $notificationModel = $this->getModel('M_notification');
        $userId = (int)$_SESSION['user_id'];
        $types = $this->providerNotificationTypes();

        $data = [
            'provider' => $provider,
            'pageTitle' => 'Notifications',
            'notifications' => $notificationModel ? $notificationModel->getNotificationsByUserTypes($userId, $types, 100) : [],
            'unreadCount' => $notificationModel ? $notificationModel->getUnreadCountByTypes($userId, $types) : 0,
        ];

        $this->view('service_provider_notifications', $data);
    }

    public function markRead()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        $notificationId = $_GET['id'] ?? $_POST['notification_id'] ?? null;

        if ($notificationId) {
            $notificationModel = $this->getModel('M_notification');
            if ($notificationModel) {
                $notificationModel->markAsRead((int)$notificationId, (int)$_SESSION['user_id']);
            }
        }

        header('Location: ' . ROOT . '/ServiceProviderNotifications');
        exit;
    }

    public function detail()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        $notificationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($notificationId <= 0) {
            $_SESSION['message'] = 'Invalid notification.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/ServiceProviderNotifications');
            exit;
        }

        $notificationModel = $this->getModel('M_notification');
        if (!$notificationModel) {
            $_SESSION['message'] = 'Notification service unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/ServiceProviderNotifications');
            exit;
        }

        $notification = $notificationModel->getNotificationByIdForUser($notificationId, (int)$_SESSION['user_id']);
        if (!$notification) {
            $_SESSION['message'] = 'Notification not found.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/ServiceProviderNotifications');
            exit;
        }

        if (!(int)($notification->is_read ?? 0)) {
            $notificationModel->markAsRead($notificationId, (int)$_SESSION['user_id']);
            $notification->is_read = 1;
        }

        $providerModel = new M_service_provider();
        $provider = $providerModel->getProviderById($_SESSION['user_id']);

        $data = [
            'provider' => $provider,
            'pageTitle' => 'Notification Details',
            'notification' => $notification,
        ];

        $this->view('service_provider_notification_detail', $data);
    }

    public function markAllRead()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        $notificationModel = $this->getModel('M_notification');
        if ($notificationModel) {
            $notificationModel->markAllAsReadByTypes((int)$_SESSION['user_id'], $this->providerNotificationTypes());
        }

        $_SESSION['message'] = 'All notifications marked as read.';
        $_SESSION['message_type'] = 'success';

        header('Location: ' . ROOT . '/ServiceProviderNotifications');
        exit;
    }
}
