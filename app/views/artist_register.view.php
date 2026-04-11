<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?> - Artist Registration</title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/register.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <style>
    .step-indicator {
      margin: 0 0 18px;
      text-align: center;
      color: #a89968;
      font-size: 13px;
      font-weight: 500;
      letter-spacing: 0.3px;
    }
    .step-indicator strong {
      color: #d4af37;
    }
    .form-step {
      display: none;
    }
    .form-step.active {
      display: block;
    }
    .step-actions {
      display: flex;
      gap: 12px;
      margin-top: 14px;
    }
    .step-actions button {
      margin-top: 0;
    }
    .secondary-btn {
      background: transparent !important;
      color: #d4af37 !important;
      border: 2px solid rgba(212, 175, 55, 0.5) !important;
      box-shadow: none !important;
    }
    .secondary-btn:hover {
      transform: none !important;
      background: rgba(212, 175, 55, 0.1) !important;
      color: #f5f0e8 !important;
    }
    .file-upload-group {
      margin: 15px 0;
      text-align: left;
    }
    .file-upload-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #d4af37;
      font-size: 14px;
    }
    .file-upload-group input[type="file"] {
      width: 100%;
      padding: 12px;
      border: 2px dashed rgba(212, 175, 55, 0.45);
      border-radius: 12px;
      background: rgba(245, 240, 232, 0.04);
      color: #f5f0e8;
      cursor: pointer;
    }
    .file-upload-group input[type="file"]:hover {
      border-color: #ba8e23;
      background: rgba(212, 175, 55, 0.08);
    }
    .file-upload-group small {
      display: block;
      margin-top: 6px;
      color: #a89968;
      font-size: 12px;
    }
    .info-notice {
      background: linear-gradient(135deg, rgba(45, 24, 16, 0.95) 0%, rgba(61, 40, 23, 0.95) 100%);
      border: 1px solid rgba(212, 175, 55, 0.5);
      border-radius: 8px;
      padding: 12px 15px;
      margin: 15px 0;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13px;
      color: #e8d5a8;
    }
    .info-notice i {
      color: #d4af37;
      font-size: 18px;
    }
  </style>
</head>

<body>
  <div class="back-container">
    <a href="<?= ROOT ?>/Signup" class="back-link">
      <button type="button" class="back-btn">
        <i class="bx bx-arrow-left"></i> Back to Selection
      </button>
    </a>
  </div>

  <div class="signup-container signup-artist">
    <div class="form-box">
      <h2>Artist Signup</h2>

      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $error): ?>
            <p><?= $error ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php $old = $old ?? []; ?>

      <form method="POST" enctype="multipart/form-data" action="<?= ROOT ?>/ArtistRegister">
        <div class="step-indicator">Step <strong id="currentStep">1</strong> of 2</div>

        <div class="form-step active" id="step1">
          <div class="input-box">
            <i class="bx bx-user"></i>
            <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($old['full_name'] ?? '') ?>" required>
          </div>

          <div class="input-box">
            <i class="bx bx-envelope"></i>
            <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
          </div>

          <div class="input-box">
            <i class="bx bx-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
          </div>

          <div class="input-box">
            <i class="bx bx-check-circle"></i>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
          </div>

          <div class="input-box">
            <i class="bx bx-phone"></i>
            <input type="text" name="phone" placeholder="Phone Number" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" required>
          </div>

          <div class="step-actions">
            <button type="button" id="nextBtn">Next</button>
          </div>
        </div>

        <div class="form-step" id="step2">
          <div class="input-box">
            <i class="bx bx-id-card"></i>
            <input type="text" name="nic_number" placeholder="NIC Number (e.g. 200012345678 or 951234567V)" value="<?= htmlspecialchars($old['nic_number'] ?? '') ?>" required>
          </div>

          <div class="file-upload-group">
            <label for="nic_photo_front">NIC Front Photo</label>
            <input type="file" name="nic_photo_front" id="nic_photo_front" accept=".jpg,.jpeg,.png" required>
            <small>Upload the front side of your NIC (JPG/PNG, max 5MB)</small>
          </div>

          <div class="file-upload-group">
            <label for="nic_photo_back">NIC Back Photo</label>
            <input type="file" name="nic_photo_back" id="nic_photo_back" accept=".jpg,.jpeg,.png" required>
            <small>Upload the back side of your NIC (JPG/PNG, max 5MB)</small>
          </div>

          <div class="info-notice">
            <i class="bx bx-info-circle"></i>
            <span>Your account will be reviewed by admin. You can login after verification.</span>
          </div>

          <div class="step-actions">
            <button type="button" class="secondary-btn" id="backBtn">Back</button>
            <button type="submit">Join as Artist</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const nextBtn = document.getElementById('nextBtn');
    const backBtn = document.getElementById('backBtn');
    const currentStep = document.getElementById('currentStep');

    function showStep(stepNumber) {
      const showFirst = stepNumber === 1;
      step1.classList.toggle('active', showFirst);
      step2.classList.toggle('active', !showFirst);
      currentStep.textContent = String(stepNumber);
    }

    function validateStepOne() {
      const requiredFields = step1.querySelectorAll('input[required]');
      for (const field of requiredFields) {
        if (!field.value.trim()) {
          field.focus();
          return false;
        }
      }

      const emailInput = step1.querySelector('input[name="email"]');
      if (emailInput && !emailInput.checkValidity()) {
        emailInput.focus();
        return false;
      }

      return true;
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        if (validateStepOne()) {
          showStep(2);
        }
      });
    }

    if (backBtn) {
      backBtn.addEventListener('click', function () {
        showStep(1);
      });
    }
  </script>
</body>

</html>
