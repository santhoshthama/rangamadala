<?php
session_start();
include 'db_connect.php';

$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    header("Location: view_profile.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("UPDATE projects SET 
            year = ?, 
            project_name = ?, 
            services_provided = ?,
            description = ?
            WHERE id = ?");
        
        $stmt->execute([
            $_POST['year'],
            $_POST['project_name'],
            $_POST['services_provided'],
            $_POST['description'],
            $project_id
        ]);
        
        // Get provider_id for redirect
        $stmt = $conn->prepare("SELECT provider_id FROM projects WHERE id = ?");
        $stmt->execute([$project_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $_SESSION['success'] = "Project updated successfully!";
        header("Location: view_profile.php?id=" . $project['provider_id']);
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error updating project: " . $e->getMessage();
    }
}

// Fetch current project data
$stmt = $conn->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$project_id]);
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$project) {
    die("Project not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link rel="stylesheet" href="profile_styles.css">
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='view_profile.php?id=<?php echo $project['provider_id']; ?>'">
            <span>←</span>
            <span>Back to Profile</span>
        </button>

        <div class="register-card">
            <div class="register-header">
                <h2>Edit Project</h2>
                <p>Update project details</p>
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
                                    value="<?php echo htmlspecialchars($project['year']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Project Name <span class="required">*</span></label>
                                <input type="text" name="project_name" class="form-input" 
                                    value="<?php echo htmlspecialchars($project['project_name']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Services Provided</label>
                            <input type="text" name="services_provided" class="form-input" 
                                value="<?php echo htmlspecialchars($project['services_provided']); ?>"
                                placeholder="e.g., Lighting Design, Sound Systems">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-input textarea" rows="5"><?php echo htmlspecialchars($project['description']); ?></textarea>
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Save Changes</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='view_profile.php?id=<?php echo $project['provider_id']; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>