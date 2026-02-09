<?php
// Configure session settings BEFORE session_start()
ini_set('session.cookie_lifetime', 0);  // Session ends when browser closes
ini_set('session.gc_maxlifetime', 86400);  // 24 hours server-side
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access
ini_set('session.cookie_samesite', 'Lax');  // Prevent CSRF
ini_set('session.use_strict_mode', 1);  // Prevent session fixation

// Ensure session directory exists
$sessionPath = "C:/xampp/tmp";
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

session_start();

// Include initialization file
require '../app/core/init.php';

// Enable debug mode errors
if (defined('DEBUG') && DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Optional: force errors to show in HTML
set_error_handler(function($errno, $errstr, $errfile, $errline){
    echo "<b>Error:</b> [$errno] $errstr in $errfile on line $errline<br>";
});

// Start the app
$app = new App();
$app->loadController();
