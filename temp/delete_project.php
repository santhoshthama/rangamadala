<?php
session_start();
include 'db_connect.php';

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    $_SESSION['error'] = "Invalid project ID";
    header("Location: view_profile.php");
    exit;
}

try {
    // Get provider_id before deleting
    $stmt = $conn->prepare("SELECT provider_id FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        $_SESSION['error'] = "Project not found";
        header("Location: view_profile.php");
        exit;
    }
    
    // Delete the project
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$project_id]);
    
    $_SESSION['success'] = "Project deleted successfully!";
    header("Location: view_profile.php?id=" . $project['provider_id']);
    exit;

} catch(PDOException $e) {
    $_SESSION['error'] = "Error deleting project: " . $e->getMessage();
    header("Location: view_profile.php");
    exit;
}
?>