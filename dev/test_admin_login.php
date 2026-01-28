<?php
// Quick CLI test for admin login
// Usage: php dev/test_admin_login.php

// Simulate web environment for config/init
if (!isset($_SERVER['SERVER_NAME'])) {
    $_SERVER['SERVER_NAME'] = 'localhost';
}

session_save_path("C:/xampp/tmp");
session_start();

// Change working directory so autoloader relative paths resolve like in web
chdir(__DIR__ . '/../public');

require __DIR__ . '/../app/core/init.php';

$loginModel = new M_login();

$email = 'admin@rangamadala.com';
$password = 'Admin@123';

$user = $loginModel->authenticate($email, $password);

if ($user && isset($user->role) && $user->role === 'admin') {
    echo "OK: Admin login works.\n";
    echo "User ID: " . $user->id . "\n";
    echo "Name: " . $user->full_name . "\n";
} else {
    echo "FAIL: Admin login failed.\n";
    if ($user) {
        echo "User record found but not admin or other mismatch. Role: " . ($user->role ?? 'N/A') . "\n";
    } else {
        echo "No matching user or password mismatch for $email.\n";

        // Extra diagnostics: check if admin user exists and show hash
        echo "Checking database for admin user...\n";
        $db = new Database();
        $db->query("SELECT id, full_name, email, role, password FROM users WHERE email = :email");
        $db->bind(":email", $email);
        $admin = $db->single();
        if ($admin) {
            echo "Found user in DB. ID=" . $admin->id . ", role=" . $admin->role . "\n";
            echo "Stored password hash: " . $admin->password . "\n";
        } else {
            echo "No user row found in users table for that email.\n";
        }
    }
}
