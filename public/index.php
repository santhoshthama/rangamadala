<?php
// Set session save path (fix for session warnings)
session_save_path("C:/xampp/tmp"); // make sure this folder exists
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
