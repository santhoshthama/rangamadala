<?php

class ServiceAvailability
{
    use Controller;

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has service_provider role
        if (($_SESSION['role'] ?? '') !== 'service_provider') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        // Get provider details for profile image
        $model = new M_service_provider();
        $provider = $model->getProviderById($_SESSION['user_id']);
        
        $data = [
            'provider' => $provider,
            'pageTitle' => 'Availability Calendar'
        ];

        $this->view('service_availability', $data);
    }
}
