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
      <form method="POST" enctype="multipart/form-data" action="<?= ROOT ?>/AudienceRegister">
     
        <div class="input-box">
          <i class="bx bx-user"></i>
          <input type="text" name="full_name" placeholder="Full Name" required>
        </div>

        <div class="input-box">
          <i class="bx bx-envelope"></i>
          <input type="email" name="email" placeholder="Email" required>
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
          <input type="text" name="phone" placeholder="Phone Number" required>
        </div>

        <button type="submit">Sign Up</button>
      </form>

    </div>
  </div>

</body>

</html>