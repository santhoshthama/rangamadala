<?php

class Logout
{
    use Controller;

    public function index()
    {
        // Clear the $_SESSION array FIRST
        $_SESSION = [];
        
        // Delete the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-3600, '/');
        }
        
        // Destroy all session data
        session_destroy();
        
        // Redirect to home page
        header("Location: " . ROOT . "/Home");
        exit;
    }
}

?>
