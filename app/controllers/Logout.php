<?php

class Logout
{
    use Controller;

    public function index()
    {
        // Clear all session variables first
        $_SESSION = [];

        // Expire session cookie in browser
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                (bool)($params['secure'] ?? false),
                (bool)($params['httponly'] ?? true)
            );
        }

        // Destroy server-side session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        // Prevent cached authenticated pages from being reused after logout
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
        
        // Redirect to login page with logout flag
        header("Location: " . ROOT . "/Login?logged_out=1");
        exit;
    }
}

?>
