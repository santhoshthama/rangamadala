<?php

class ServiceProviderNotifications
{
    use Controller;

    private function jsonResponse(array $payload, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code($statusCode);
        echo json_encode($payload);
        exit;
    }

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

    public function open()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        $notificationModel = $this->getModel('M_notification');
        if ($notificationModel) {
            $notificationModel->markAllAsReadByTypes((int)$_SESSION['user_id'], $this->providerNotificationTypes());
        }

        header('Location: ' . ROOT . '/ServiceProviderNotifications');
        exit;
    }

    public function unreadCount()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        $notificationModel = $this->getModel('M_notification');
        $count = 0;

        if ($notificationModel) {
            $count = (int)$notificationModel->getUnreadCountByTypes((int)$_SESSION['user_id'], $this->providerNotificationTypes());
        }

        $this->jsonResponse([
            'success' => true,
            'unreadCount' => $count,
        ]);
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
