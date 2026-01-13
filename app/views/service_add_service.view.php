<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
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
                <h2>Add New Service</h2>
                <p>Add a service you offer to clients</p>
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
                            <select name="service_name" id="serviceSelect" class="form-input" required onchange="toggleCustomInput()">
                                <option value="">Select a service</option>
                                <?php foreach ($data['service_options'] as $option): ?>
                                    <option value="<?php echo htmlspecialchars($option); ?>">
                                        <?php echo htmlspecialchars($option); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group" id="customServiceGroup" style="display: none;">
                            <label class="form-label">Custom Service Name <span class="required">*</span></label>
                            <input type="text" id="customServiceInput" class="form-input" placeholder="Enter custom service name">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Rate per Hour (Rs) <span class="required">*</span></label>
                            <input type="number" step="0.01" name="rate_per_hour" class="form-input" placeholder="0.00" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-input textarea" rows="5" placeholder="Describe this service, your expertise, and what clients can expect..."></textarea>
                        </div>
                    </div>

                    <div class="buttons-section">
                        <button type="submit" class="btn">Add Service</button>
                        <button type="button" class="btn btn-secondary" 
                            onclick="window.location.href='<?php echo ROOT; ?>/ServiceProviderProfile/index?id=<?php echo $data['provider_id']; ?>'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleCustomInput() {
            const select = document.getElementById('serviceSelect');
            const customGroup = document.getElementById('customServiceGroup');
            const customInput = document.getElementById('customServiceInput');
            
            if (select.value === 'Other') {
                customGroup.style.display = 'block';
                customInput.required = true;
                select.removeAttribute('name');
                customInput.setAttribute('name', 'service_name');
            } else {
                customGroup.style.display = 'none';
                customInput.required = false;
                customInput.removeAttribute('name');
                select.setAttribute('name', 'service_name');
            }
        }
    </script>
</body>
</html>
