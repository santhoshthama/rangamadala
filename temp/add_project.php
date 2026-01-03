<?php
session_start();
include 'db_connect.php';

$provider_id = $_GET['provider_id'] ?? null;

if (!$provider_id) {
    header("Location: view_profile.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("INSERT INTO projects (provider_id, year, project_name, services_provided, description) 
                               VALUES (?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $provider_id,
            $_POST['year'],
            $_POST['project_name'],
            $_POST['services_provided'],
            $_POST['description']
        ]);
        
        $_SESSION['success'] = "Project added successfully!";
        header("Location: view_profile.php?id=" . $provider_id);
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error adding project: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <link rel="stylesheet" href="profile_styles.css">
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='view_profile.php?id=<?php echo $provider_id; ?>'">
            <span>←</span>
            <span>Back to Profile</span>
        </button>

        <div class="register-card">
            <div class="register-header">
                <h2>Add New Project</h2>
                <p>Showcase your work and experience</p>
            </div>

            <div class="register-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="section">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Year <span class="required">*</span></label>
                                <input type="number" name="year" class="form-input" min="1970" max="2025" 
                                    placeholder="2024" value="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Project Name <span class="required">*</span></label>
                                <input type="text" name="project_name" class="form-input" 
                                    placeholder="e.g., Romeo & Juliet" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Services Provided</label>
                            <input type="text" name="services_provided" class="form-input" 
                                placeholder="e.g., Lighting Design, Sound Systems">
                            <small style="color: #6c757d; display: block; margin-top: 5px;">
                                List the services you provided for this project
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-input textarea" rows="5" 
                                placeholder="Brief project description, your role, and achievements..."></textarea>
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Add Project</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='view_profile.php?id=<?php echo $provider_id; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>