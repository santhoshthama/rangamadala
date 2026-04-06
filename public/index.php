<?php
// Set session save path (fix for session warnings)
session_save_path("C:/xampp/tmp"); // make sure this folder exists
session_start();

// Include initialization file
require '../app/core/init.php';

// Disable display of raw PHP errors to users
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Custom error handler - shows professional error page instead of raw errors
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Log the error (optional - create logs folder if needed)
    $logMessage = date('Y-m-d H:i:s') . " | Error [$errno]: $errstr in $errfile on line $errline\n";
    error_log($logMessage, 3, dirname(__FILE__) . '/../app/logs/error.log');
    
    // For fatal errors, show the error page
    if ($errno == E_ERROR || $errno == E_USER_ERROR) {
        showErrorPage('Something went wrong', 'We encountered an unexpected error. Please try again later.');
        exit;
    }
    
    // For non-fatal errors in development mode, continue execution
    if (defined('DEBUG') && DEBUG) {
        // Log to browser console instead of displaying raw errors
        echo "<script>console.error('PHP Error: " . addslashes($errstr) . "');</script>";
    }
    
    return true; // Don't execute PHP's internal error handler
});

// Custom exception handler
set_exception_handler(function($exception) {
    // Log the exception
    $logMessage = date('Y-m-d H:i:s') . " | Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine() . "\n";
    error_log($logMessage, 3, dirname(__FILE__) . '/../app/logs/error.log');
    
    showErrorPage('Oops! Something went wrong', 'An unexpected error occurred. Our team has been notified.');
    exit;
});

// Function to display professional error page
function showErrorPage($title = 'Error', $message = 'Something went wrong') {
    http_response_code(500);
    include dirname(__FILE__) . '/../app/views/error.view.php';
}

// Start the app
try {
    $app = new App();
    $app->loadController();
} catch (Exception $e) {
    // Log and show error page for any uncaught exceptions
    $logMessage = date('Y-m-d H:i:s') . " | Critical: " . $e->getMessage() . "\n";
    error_log($logMessage, 3, dirname(__FILE__) . '/../app/logs/error.log');
    showErrorPage('Service Unavailable', 'We\'re experiencing technical difficulties. Please try again later.');
}
