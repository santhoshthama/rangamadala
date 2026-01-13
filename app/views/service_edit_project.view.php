<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/CSS/service provider/service_provider_profile.css">
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['project']->provider_id; ?>'">
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
                                <input type="number" name="year" class="form-input" min="1970" max="<?php echo date('Y'); ?>"
                                    value="<?php echo htmlspecialchars($data['project']->year); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Project Name <span class="required">*</span></label>
                                <input type="text" name="project_name" class="form-input" 
                                    value="<?php echo htmlspecialchars($data['project']->project_name); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Services Provided</label>
                            <input type="text" name="services_provided" class="form-input" 
                                value="<?php echo htmlspecialchars($data['project']->services_provided ?? ''); ?>"
                                placeholder="e.g., Lighting Design, Sound Systems">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-input textarea" rows="5"><?php echo htmlspecialchars($data['project']->description ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Save Changes</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['project']->provider_id; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
