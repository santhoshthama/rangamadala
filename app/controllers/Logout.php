<?php

class Logout
{
    use Controller;

    public function index()
    {
        // Destroy all session data
        session_destroy();
        
        // Clear the $_SESSION array
        $_SESSION = [];
        
        // Redirect to home page
        header("Location: " . ROOT . "/Home");
        exit;
    }
}

?>
