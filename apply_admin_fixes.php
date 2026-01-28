<?php
// Apply Admin Login Fixes
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== APPLYING ADMIN LOGIN FIXES ===\n\n";

try {
    $db = new PDO('mysql:host=localhost;dbname=rangamandala_db', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connected\n\n";
    
    // 1. Add missing columns
    echo "Step 1: Adding missing verification columns...\n";
    try {
        $db->exec("ALTER TABLE `users` ADD COLUMN `verified_by` int DEFAULT NULL AFTER `is_verified`");
        echo "  ✓ Added 'verified_by' column\n";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  - 'verified_by' column already exists\n";
        } else {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    try {
        $db->exec("ALTER TABLE `users` ADD COLUMN `verified_at` timestamp NULL DEFAULT NULL AFTER `verified_by`");
        echo "  ✓ Added 'verified_at' column\n";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  - 'verified_at' column already exists\n";
        } else {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    try {
        $db->exec("ALTER TABLE `users` ADD COLUMN `rejection_reason` text DEFAULT NULL AFTER `verified_at`");
        echo "  ✓ Added 'rejection_reason' column\n";
    } catch(PDOException $e) {
        if(strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "  - 'rejection_reason' column already exists\n";
        } else {
            echo "  ✗ Error: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n";
    
    // 2. Update admin password and verification status
    echo "Step 2: Updating admin password and verification status...\n";
    $newPassword = password_hash('Admin@123', PASSWORD_DEFAULT);
    
    $stmt = $db->prepare("UPDATE `users` SET `password` = :password, `is_verified` = 1, `status` = 'active' WHERE `email` = 'admin@rangamadala.com' AND `role` = 'admin'");
    $stmt->execute([':password' => $newPassword]);
    
    if($stmt->rowCount() > 0) {
        echo "  ✓ Admin password updated successfully\n";
        echo "  ✓ Admin account verified\n";
    } else {
        echo "  ✗ No admin user found to update\n";
    }
    
    echo "\n";
    
    // 3. Verify changes
    echo "Step 3: Verifying changes...\n";
    $stmt = $db->prepare("SELECT id, full_name, email, role, is_verified, status FROM users WHERE email = 'admin@rangamadala.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($admin) {
        echo "  Admin User Details:\n";
        echo "    ID: " . $admin['id'] . "\n";
        echo "    Name: " . $admin['full_name'] . "\n";
        echo "    Email: " . $admin['email'] . "\n";
        echo "    Role: " . $admin['role'] . "\n";
        echo "    Verified: " . ($admin['is_verified'] ? 'Yes' : 'No') . "\n";
        echo "    Status: " . $admin['status'] . "\n";
        
        // Test password
        $stmt2 = $db->prepare("SELECT password FROM users WHERE email = 'admin@rangamadala.com'");
        $stmt2->execute();
        $passwordRow = $stmt2->fetch(PDO::FETCH_ASSOC);
        
        if(password_verify('Admin@123', $passwordRow['password'])) {
            echo "\n  ✓ Password verification test PASSED\n";
        } else {
            echo "\n  ✗ Password verification test FAILED\n";
        }
    }
    
    echo "\n=== FIXES APPLIED SUCCESSFULLY ===\n";
    echo "\nYou can now login with:\n";
    echo "Email: admin@rangamadala.com\n";
    echo "Password: Admin@123\n";
    
} catch(PDOException $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}
?>
