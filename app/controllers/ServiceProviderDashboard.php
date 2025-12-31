<?php

class ServiceProviderDashboard
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
        if ($_SESSION['user_role'] !== 'service_provider') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $this->view('service_provider_dashboard');
    }
}
?>
