<?php

class ServiceProviderDashboard {
    use Controller;

    public function index() {
        // Check if user is logged in and is a service provider
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'service_provider') {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        // Render the dashboard view
        $this->view('service_provider_dashboard');
    }
}
?>
