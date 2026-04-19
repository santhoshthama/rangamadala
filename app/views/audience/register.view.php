<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/register.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="shortcut icon" href="<?php echo ROOT; ?>/assets/images/Rangamadala logo.png" type="image/x-icon">

</head>

<body>
  <div class="back-container">
    <a href="<?= ROOT ?>/Signup" class="back-link">
      <button type="button" class="back-btn">
        <i class="bx bx-arrow-left"></i> Back to Selection
      </button>
    </a>
  </div>

  <div class="signup-container signup-audience">
    <div class="form-box">
      
      <h2>Audience Signup</h2>
      <?php $errors = $errors ?? []; $old = $old ?? []; ?>
      <?php if (!empty($errors)): ?>
        <div class="error-box audience-error-box">
          <?php foreach ($errors as $error): ?>
            <p class="error-box-item"><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <div id="audienceClientErrorBox" class="error-box audience-error-box audience-error-box-hidden" aria-live="polite"></div>
      <form id="audienceSignupForm" method="POST" enctype="multipart/form-data" action="<?= ROOT ?>/AudienceRegister">
     
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
          <input type="password" name="password" placeholder="Password" minlength="6" pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}" title="At least 6 characters with uppercase, lowercase, number, and symbol" required>
        </div>

        <div class="input-box">
          <i class="bx bx-check-circle"></i>
          <input type="password" name="confirm_password" placeholder="Confirm Password" minlength="6" required>
        </div>

        <div class="input-box">
          <i class="bx bx-phone"></i>
          <input type="text" name="phone" placeholder="Phone Number (07X XXX XXXX / +94 XXX XXX XXX)" value="<?= htmlspecialchars($old['phone'] ?? '') ?>" pattern="(?:\+94|94|0)7\d{8}" title="Enter a valid Sri Lankan mobile number (07X XXX XXXX or +94 XXX XXX XXX)" required>
        </div>

        <button type="submit">Sign Up</button>
      </form>

    </div>
  </div>

  <script>
    const audienceSignupForm = document.getElementById('audienceSignupForm');
    const audienceClientErrorBox = document.getElementById('audienceClientErrorBox');

    function showValidationErrors(errors) {
      if (!errors.length) return;
      if (audienceClientErrorBox) {
        audienceClientErrorBox.innerHTML = errors.map(function (msg) {
          return '<p class="error-box-item">' + msg + '</p>';
        }).join('');
        audienceClientErrorBox.style.display = 'block';
        audienceClientErrorBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }

    function clearValidationErrors() {
      if (audienceClientErrorBox) {
        audienceClientErrorBox.innerHTML = '';
        audienceClientErrorBox.style.display = 'none';
      }
    }

    if (audienceSignupForm) {
      audienceSignupForm.addEventListener('submit', function (e) {
        const fullName = audienceSignupForm.querySelector('input[name="full_name"]')?.value.trim() || '';
        const email = audienceSignupForm.querySelector('input[name="email"]')?.value.trim() || '';
        const password = audienceSignupForm.querySelector('input[name="password"]')?.value || '';
        const confirmPassword = audienceSignupForm.querySelector('input[name="confirm_password"]')?.value || '';
        const phone = audienceSignupForm.querySelector('input[name="phone"]')?.value.trim() || '';

        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/;
        const phonePattern = /^(?:\+94|94|0)7\d{8}$/;
        const errors = [];

        if (!fullName) {
          errors.push('Full Name is required.');
        }

        if (!email) {
          errors.push('Email is required.');
        } else if (!audienceSignupForm.querySelector('input[name="email"]').checkValidity()) {
          errors.push('Email format is invalid.');
        }

        if (!passwordPattern.test(password)) {
          errors.push('Password must be at least 6 characters and include uppercase, lowercase, number, and symbol.');
        }

        if (password !== confirmPassword) {
          errors.push('Password confirmation does not match.');
        }

        if (!phonePattern.test(phone)) {
          errors.push('Enter a valid Sri Lankan contact number (e.g. 07X XXX XXXX or +94 XXX XXX XXX).');
        }

        if (errors.length) {
          e.preventDefault();
          showValidationErrors(errors);
          return;
        }

        clearValidationErrors();
      });
    }
  </script>

</body>

</html>