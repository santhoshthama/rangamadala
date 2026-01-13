<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/register.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
      <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">

</head>

<body>
  <div class="signup-container signup-artist">
     <div class="back-container">
      <a href="<?= ROOT ?>/Signup" class="back-link">
        <button type="button" class="back-btn">
          <i class="fas fa-arrow-left"></i> Back to Selection
        </button>
      </a>
    </div>

    <div class="form-box">
      <h2>Artist Signup</h2>

      <?php if (!empty($errors)): ?>
        <div class="error-box">
          <?php foreach ($errors as $error): ?>
            <p><?= $error ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form method="POST" enctype="multipart/form-data" action="<?= ROOT ?>/ArtistRegister">
       
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="file" name="nic_photo" accept=".jpg,.jpeg,.png,.pdf" required>
        <button type="submit">Join as Artist</button>
      </form>
    </div>
  </div>
</body>

</html>
