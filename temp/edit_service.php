<?php
session_start();
include 'db_connect.php';

$service_id = $_GET['id'] ?? null;

if (!$service_id) {
    header("Location: view_profile.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("UPDATE services SET 
            service_name = ?, 
            rate_per_hour = ?, 
            description = ?
            WHERE id = ?");
        
        $stmt->execute([
            $_POST['service_name'],
            $_POST['rate_per_hour'],
            $_POST['description'],
            $service_id
        ]);
        
        // Get provider_id for redirect
        $stmt = $conn->prepare("SELECT provider_id FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['success'] = "Service updated successfully!";
        header("Location: view_profile.php?id=" . $service['provider_id']);
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error updating service: " . $e->getMessage();
    }
}

// Fetch current service data
$stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$service_id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    die("Service not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Service</title>
    <link rel="stylesheet" href="profile_styles.css">
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='view_profile.php?id=<?php echo $service['provider_id']; ?>'">
            <span>←</span>
            <span>Back to Profile</span>
        </button>

        <div class="register-card">
            <div class="register-header">
                <h2>Edit Service</h2>
                <p>Update service details and rates</p>
            </div>

            <div class="register-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="section">
                        <div class="form-group">
                            <label class="form-label">Service Name <span class="required">*</span></label>
                            <input type="text" name="service_name" class="form-input" 
                                value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Rate per Hour (Rs) <span class="required">*</span></label>
                            <input type="number" step="0.01" name="rate_per_hour" class="form-input" 
                                value="<?php echo htmlspecialchars($service['rate_per_hour']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-input textarea" rows="5"><?php echo htmlspecialchars($service['description']); ?></textarea>
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Save Changes</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='view_profile.php?id=<?php echo $service['provider_id']; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>