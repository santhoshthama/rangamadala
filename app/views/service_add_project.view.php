<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/CSS/service provider/service_provider_profile.css">
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['provider_id']; ?>'">
            <span>←</span>
            <span>Back to Profile</span>
        </button>

        <div class="register-card">
            <div class="register-header">
                <h2>Add New Project</h2>
                <p>Showcase your past work and achievements</p>
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
                                <input type="number" name="year" class="form-input" min="1970" max="<?php echo date('Y'); ?>"
                                    value="<?php echo date('Y'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Project Name <span class="required">*</span></label>
                                <input type="text" name="project_name" class="form-input" 
                                    placeholder="e.g., National Theatre Production" required>
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
                            onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['provider_id']; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
