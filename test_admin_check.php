<?php
// Test Admin Login Functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== ADMIN LOGIN VERIFICATION TEST ===\n\n";

// Database connection
try {
    $db = new PDO('mysql:host=localhost;dbname=rangamandala_db', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection successful\n\n";
} catch(PDOException $e) {
    die("✗ Database connection failed: " . $e->getMessage() . "\n");
}

// Check users table structure
echo "--- Users Table Structure ---\n";
try {
    $stmt = $db->query('DESCRIBE users');
    $columns = [];
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
        echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    echo "\n";
    
    // Check for verification columns
    $requiredColumns = ['is_verified', 'verified_by', 'verified_at', 'rejection_reason'];
    $missingColumns = [];
    foreach($requiredColumns as $col) {
        if(!in_array($col, $columns)) {
            $missingColumns[] = $col;
        }
    }
    
    if(!empty($missingColumns)) {
        echo "⚠ WARNING: Missing verification columns: " . implode(', ', $missingColumns) . "\n";
        echo "  Run add_verification_fields.sql to add these columns\n\n";
    } else {
        echo "✓ All verification columns exist\n\n";
    }
} catch(PDOException $e) {
    echo "✗ Error checking table structure: " . $e->getMessage() . "\n\n";
}

// Check for admin user
echo "--- Admin User Check ---\n";
try {
    $stmt = $db->prepare("SELECT id, full_name, email, role, created_at FROM users WHERE role = 'admin'");
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if(empty($admins)) {
        echo "✗ No admin users found in database\n";
        echo "  Run create_default_admin.sql to create admin account\n\n";
    } else {
        echo "✓ Found " . count($admins) . " admin user(s):\n";
        foreach($admins as $admin) {
            echo "  - ID: " . $admin['id'] . "\n";
            echo "    Name: " . $admin['full_name'] . "\n";
            echo "    Email: " . $admin['email'] . "\n";
            echo "    Created: " . $admin['created_at'] . "\n\n";
        }
    }
} catch(PDOException $e) {
    echo "✗ Error checking admin users: " . $e->getMessage() . "\n\n";
}

// Test admin login credentials
echo "--- Admin Login Test ---\n";
$testEmail = 'admin@rangamadala.com';
$testPassword = 'Admin@123';

try {
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $testEmail]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user) {
        echo "✓ Admin user found with email: $testEmail\n";
        echo "  Role: " . $user['role'] . "\n";
        
        // Test password verification
        if(password_verify($testPassword, $user['password'])) {
            echo "✓ Password verification successful\n";
            echo "  Login credentials are correct!\n\n";
        } else {
            echo "✗ Password verification failed\n";
            echo "  The password hash in database doesn't match '$testPassword'\n";
            echo "  Current hash: " . $user['password'] . "\n\n";
        }
        
        // Check verification status (if column exists)
        if(isset($user['is_verified'])) {
            echo "  Verification status: " . ($user['is_verified'] ? 'Verified' : 'Not verified') . "\n";
        }
    } else {
        echo "✗ Admin user not found with email: $testEmail\n";
        echo "  Run create_default_admin.sql to create admin account\n\n";
    }
} catch(PDOException $e) {
    echo "✗ Error testing login: " . $e->getMessage() . "\n\n";
}

// Check Admindashboard controller
echo "--- Admin Dashboard Controller Check ---\n";
$controllerPath = __DIR__ . '/app/controllers/Admindashboard.php';
if(file_exists($controllerPath)) {
    echo "✓ Admindashboard controller exists\n";
    // Check if it has the required methods
    $content = file_get_contents($controllerPath);
    $requiredMethods = ['index', 'getPendingRegistrations', 'approveUser', 'rejectUser'];
    foreach($requiredMethods as $method) {
        if(strpos($content, "function $method") !== false) {
            echo "  ✓ Method: $method()\n";
        } else {
            echo "  ✗ Missing method: $method()\n";
        }
    }
} else {
    echo "✗ Admindashboard controller not found\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
