<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password - <?= APP_NAME ?></title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Signin.css">
  <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  <style>
    .helper-text {
      color: #e8d5a8;
      font-size: 14px;
      line-height: 1.6;
      margin-bottom: 18px;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <?php if (!empty($error) && empty($token)): ?>
      <div class="confirm-box">
        <div class="confirm-header">Reset Password</div>
        <div class="confirm-body"><?= htmlspecialchars($error) ?></div>
        <div class="confirm-actions">
          <a href="<?= ROOT ?>/ForgotPassword" class="confirm-btn cancel">Try again</a>
          <a href="<?= ROOT ?>/Login" class="confirm-btn logout">Back to login</a>
        </div>
      </div>
    <?php else: ?>
      <form action="<?= ROOT ?>/ForgotPassword/reset" method="POST" id="resetPasswordForm" novalidate>
        <h1>Reset Password</h1>
        <p class="helper-text">Create a new password for <?= htmlspecialchars($email ?? 'your account') ?>.</p>

        <?php if (!empty($error)): ?>
          <div class="error-message" style="display:block; text-align:left;">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">

        <div class="input-box">
          <input type="password" name="password" id="password" placeholder="New password" required>
          <i class='bx bx-lock-alt'></i>
        </div>

        <div class="input-box">
          <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm password" required>
          <i class='bx bx-lock-alt'></i>
        </div>

        <button type="submit" class="btn">Update Password</button>

        <div class="register-link">
          <p><a href="<?= ROOT ?>/Login">Back to login</a></p>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
