<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/CSS/service provider/service_provider_profile.css">
</head>
<body>
    <div class="container">
        <button class="back-button" onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['provider']->user_id; ?>'">
            <span>←</span>
            <span>Back to Profile</span>
        </button>

        <div class="register-card">
            <div class="register-header">
                <h2>Change Password</h2>
                <p>Update your account password</p>
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
                            <label class="form-label">Current Password <span class="required">*</span></label>
                            <input type="password" name="current_password" class="form-input" 
                                placeholder="Enter your current password" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">New Password <span class="required">*</span></label>
                                <input type="password" name="new_password" class="form-input" 
                                    placeholder="Enter new password" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm New Password <span class="required">*</span></label>
                                <input type="password" name="confirm_password" class="form-input" 
                                    placeholder="Confirm new password" required>
                            </div>
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Change Password</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['provider']->user_id; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
