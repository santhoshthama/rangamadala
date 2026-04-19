<?php

class App 
{
    private $controller = 'Home';  // Default controller
    private $method = 'index';     // Default method
    private $params = [];          // To hold URL parameters

    /**
     * Enforce a global lock for PM users with overdue final payments.
     * When active, only payment and logout routes are allowed.
     */
    private function enforceOverduePaymentLock(array $urlParts): void
    {
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        $role = strtolower((string)($_SESSION['user_role'] ?? $_SESSION['role'] ?? ''));
        if ($userId <= 0 || $role !== 'artist') {
            return;
        }

        $requestedController = ucfirst((string)($urlParts[0] ?? 'Home'));
        $allowedControllers = ['Payment', 'Logout'];
        if (in_array($requestedController, $allowedControllers, true)) {
            return;
        }

        try {
            $serviceRequestModel = new M_service_request();
            if (!method_exists($serviceRequestModel, 'getFirstOverdueFinalPaymentForManager')) {
                return;
            }

            $overdue = $serviceRequestModel->getFirstOverdueFinalPaymentForManager($userId);
            if (!$overdue) {
                return;
            }

            $_SESSION['warning_message'] = 'Final payment due date has passed. Please complete this payment first.';
            $amount = number_format((float)$overdue['remaining_amount'], 2, '.', '');
            header('Location: ' . ROOT . '/Payment/checkout?request_id=' . (int)$overdue['request_id'] . '&amount=' . $amount . '&type=remaining&forced_overdue=1');
            exit;
        } catch (Throwable $e) {
            error_log('enforceOverduePaymentLock error: ' . $e->getMessage());
            // Fail open on unexpected errors to avoid locking out valid users.
            return;
        }
    }

    /**
     * Split the incoming URL into parts
     */
    private function splitURL()
    { 
        // $_GET['url] = "Rangemadala/controller_name/method_name/param1/param2/..."
        $URL = $_GET['url'] ?? 'home';  // Default to 'home' if not set
        $URL = explode('/', trim($URL, "/"));  // Trim slashes and split by '/'
        // {"controller_name", "method_name", "param1", "param2", ...}
        return $URL;
    }

    /**
     * Load the controller, its method, and parameters
     */
    public function loadController()
    {
        $URL = $this->splitURL();

        // Global access lock for overdue PM payments.
        $this->enforceOverduePaymentLock($URL);

        // Build controller file path
        $filename = "../app/controllers/" . ucfirst($URL[0]) . ".php";

        // Check if controller exists
        if (file_exists($filename)) {
            require $filename;
            $this->controller = ucfirst($URL[0]);
            unset($URL[0]);
        } else {
            // Load 404 controller if not found
            require "../app/controllers/_404.php";
            $this->controller = "_404";
        }

        // Create controller object
        $controller = new $this->controller();

        // Check method existence
        if (!empty($URL[1]) && method_exists($controller, $URL[1])) {
            $this->method = $URL[1];
            unset($URL[1]);
        }

        // Clean up parameters (re-index)
        $this->params = $URL ? array_values($URL) : [];

        // Call the method dynamically
        call_user_func_array([$controller, $this->method], $this->params);
    }
}
