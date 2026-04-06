<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?> - Artist Registration</title>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/register.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <style>
    .file-upload-group {
      margin: 15px 0;
      text-align: left;
    }
    .file-upload-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #3d2817;
      font-size: 14px;
    }
    .file-upload-group input[type="file"] {
      width: 100%;
      padding: 12px;
      border: 2px dashed #d4af37;
      border-radius: 8px;
      background: #faf8f3;
      cursor: pointer;
    }
    .file-upload-group input[type="file"]:hover {
      border-color: #ba8e23;
      background: #fff7e6;
    }
    .file-upload-group small {
      display: block;
      margin-top: 6px;
      color: #8b7355;
      font-size: 12px;
    }
    .info-notice {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
      border: 1px solid #ffc107;
      border-radius: 8px;
      padding: 12px 15px;
      margin: 15px 0;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 13px;
      color: #856404;
    }
    .info-notice i {
      color: #d4af37;
      font-size: 18px;
    }
  </style>
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
        
        <div class="file-upload-group">
          <label for="nic_photo">Upload NIC Photo (for verification)</label>
          <input type="file" name="nic_photo" id="nic_photo" accept=".jpg,.jpeg,.png" required>
          <small>Upload a clear photo of your National ID Card</small>
        </div>
        
        <div class="info-notice">
          <i class="fas fa-info-circle"></i>
          <span>Your account will be reviewed by admin. You can login after verification.</span>
        </div>
        
        <button type="submit">Join as Artist</button>
      </form>
    </div>
  </div>
</body>

</html>
