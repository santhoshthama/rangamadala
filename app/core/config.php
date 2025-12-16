<?php 

if ($_SERVER['SERVER_NAME'] == 'localhost') 
{
    // Local development configuration
    define('DB_NAME', 'rangamadala');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_DRIVER', '');
    define('DB_PORT', '3306');
    define('DB_CHARSET', 'utf8mb4');
    define("APPROOT", dirname(dirname(__FILE__)));

    define('ROOT', 'http://localhost/Rangamadala/public');
} 
else 
{
    // Live/production configuration
    define('DB_NAME', 'rangamadala');
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_DRIVER', '');
    define('DB_PORT', '3306');
    define('DB_CHARSET', 'utf8mb4');
    
    define('ROOT', 'https://www.rangamadala.com');
}

// Application constants
define('APP_NAME', "Rangamadala");
define('APP_DESC', "A social media platform to connect all drama professionals.");

// true = show errors during development
define('DEBUG', true);
