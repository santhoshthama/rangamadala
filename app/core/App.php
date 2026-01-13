<?php

class App 
{
    private $controller = 'Home';  // Default controller
    private $method = 'index';     // Default method
    private $params = [];          // To hold URL parameters

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
