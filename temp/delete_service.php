<?php
session_start();
include 'db_connect.php';

$service_id = $_GET['id'] ?? null;

if (!$service_id) {
    $_SESSION['error'] = "Invalid service ID";
    header("Location: view_profile.php");
    exit;
}

try {
    // Get provider_id before deleting
    $stmt = $conn->prepare("SELECT provider_id FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        $_SESSION['error'] = "Service not found";
        header("Location: view_profile.php");
        exit;
    }
    
    // Delete the service
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    
    $_SESSION['success'] = "Service deleted successfully!";
    header("Location: view_profile.php?id=" . $service['provider_id']);
    exit;
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Error deleting service: " . $e->getMessage();
    header("Location: view_profile.php");
    exit;
}
?>