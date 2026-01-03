<?php
session_start();
include 'db_connect.php';

$provider_id = $_GET['id'] ?? null;

if (!$provider_id) {
    $_SESSION['error'] = "Invalid provider ID";
    header("Location: dashboard.php");
    exit;
}

try {
    // Start transaction for safe deletion
    $conn->beginTransaction();
    
    // Delete all services associated with this provider
    $stmt = $conn->prepare("DELETE FROM services WHERE provider_id = ?");
    $stmt->execute([$provider_id]);
    
    // Delete all projects associated with this provider
    $stmt = $conn->prepare("DELETE FROM projects WHERE provider_id = ?");
    $stmt->execute([$provider_id]);
    
    // Delete the provider profile
    $stmt = $conn->prepare("DELETE FROM serviceprovider WHERE id = ?");
    $stmt->execute([$provider_id]);
    
    // Commit the transaction
    $conn->commit();
    
    $_SESSION['success'] = "Profile deleted successfully!";
    
    // Clear any provider-specific session data
    unset($_SESSION['provider_id']);
    
    // Redirect to dashboard or login page
    header("Location: first_dashboard.html");
    exit;
    
} catch(PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    $_SESSION['error'] = "Error deleting profile: " . $e->getMessage();
    header("Location: view_profile.php?id=" . $provider_id);
    exit;
}
?>