<?php
// Web-accessible setup script
// Access this at: http://localhost/Rangamadala/setup_db.php

// Make sure we can execute
header('Content-Type: text/plain');

// Database connection details
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'rangamandala_db';

try {
    $conn = new mysqli($host, $user, $password, $database);
    
    if ($conn->connect_error) {
        die('Connection error: ' . $conn->connect_error);
    }
    
    echo "=== Database Setup ===\n\n";
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'provider_availability'");
    
    if ($result->num_rows === 0) {
        echo "[1] Creating provider_availability table...\n";
        
        $sql = "CREATE TABLE IF NOT EXISTS `provider_availability` (
          `id` int NOT NULL AUTO_INCREMENT,
          `provider_id` int NOT NULL,
          `available_date` date NOT NULL,
          `status` enum('available','booked') NOT NULL DEFAULT 'available',
          `description` text,
          `booked_for` varchar(255) DEFAULT NULL,
          `booking_details` text,
          `service_request_id` int DEFAULT NULL,
          `added_on` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
          `booked_on` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          UNIQUE KEY `provider_date` (`provider_id`, `available_date`),
          KEY `provider_id` (`provider_id`),
          KEY `available_date` (`available_date`),
          CONSTRAINT `availability_ibfk_provider` FOREIGN KEY (`provider_id`) REFERENCES `serviceprovider` (`user_id`) ON DELETE CASCADE,
          CONSTRAINT `availability_ibfk_request` FOREIGN KEY (`service_request_id`) REFERENCES `service_requests` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        if ($conn->query($sql) === TRUE) {
            echo "✓ SUCCESS! Table 'provider_availability' created.\n\n";
        } else {
            echo "✗ ERROR: " . $conn->error . "\n\n";
        }
    } else {
        echo "[1] Table 'provider_availability' already exists.\n\n";
    }
    
    // Show table structure
    echo "[2] Table Structure:\n";
    echo "-------------------\n";
    $columns = $conn->query("DESC provider_availability");
    if ($columns) {
        while ($row = $columns->fetch_assoc()) {
            echo sprintf("%-20s %-30s %s\n", $row['Field'], $row['Type'], $row['Null'] === 'YES' ? 'NULL' : 'NOT NULL');
        }
    }
    
    echo "\n✓ Setup complete! You can now use the Availability Calendar.\n";
    echo "\nNote: You can delete this file (setup_db.php) after running it.\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
