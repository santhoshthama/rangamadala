<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Basic Information</title>
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
                <h2>Edit Basic Information & Availability</h2>
                <p>Update your professional details</p>
            </div>

            <div class="register-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="section">
                        <h3 class="section-title">Basic Information</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Name <span class="required">*</span></label>
                                <input type="text" name="full_name" class="form-input" 
                                    value="<?php echo htmlspecialchars($data['provider']->full_name); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Professional Title <span class="required">*</span></label>
                                <input type="text" name="professional_title" class="form-input" 
                                    value="<?php echo htmlspecialchars($data['provider']->professional_title); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="required">*</span></label>
                                <input type="email" name="email" class="form-input" 
                                    value="<?php echo htmlspecialchars($data['provider']->email); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone Number <span class="required">*</span></label>
                                <input type="tel" name="phone" class="form-input" 
                                    value="<?php echo htmlspecialchars($data['provider']->phone); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Location <span class="required">*</span></label>
                                <input type="text" name="location" class="form-input" 
                                    value="<?php echo htmlspecialchars($data['provider']->location); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Social Media Link</label>
                                <input type="url" name="social_media_link" class="form-input" 
                                    value="<?php echo htmlspecialchars($data['provider']->social_media_link ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Years of Experience <span class="required">*</span></label>
                            <input type="number" name="years_experience" class="form-input" 
                                value="<?php echo htmlspecialchars($data['provider']->years_experience); ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Professional Summary <span class="required">*</span></label>
                            <textarea name="professional_summary" class="form-input textarea" required><?php echo htmlspecialchars($data['provider']->professional_summary ?? ''); ?></textarea>
                        </div>
                    </div>

                    <!-- Availability Section -->
                    <div class="section">
                        <h3 class="section-title">Availability</h3>
                        <div class="availability-toggle">
                            <span class="toggle-label">Currently Available for New Projects</span>
                            <input type="hidden" name="availability" id="availabilityInput" 
                                value="<?php echo $data['provider']->availability ? '1' : '0'; ?>">
                            <div id="availabilityToggle" 
                                class="toggle <?php echo $data['provider']->availability ? 'active' : ''; ?>" 
                                onclick="toggleAvailability()"></div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Availability Notes</label>
                            <input type="text" name="availability_notes" class="form-input" 
                                value="<?php echo htmlspecialchars($data['provider']->availability_notes ?? ''); ?>"
                                placeholder="e.g., Available weekdays, weekends only, etc.">
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Save Changes</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['provider']->user_id; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleAvailability() {
            const toggle = document.getElementById('availabilityToggle');
            toggle.classList.toggle('active');
            const input = document.getElementById('availabilityInput');
            input.value = toggle.classList.contains('active') ? '1' : '0';
        }
    </script>
</body>
</html>
