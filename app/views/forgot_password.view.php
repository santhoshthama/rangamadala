<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password - <?= APP_NAME ?></title>
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

    .inline-link-box {
      margin-top: 18px;
      padding: 14px 16px;
      border-radius: 10px;
      border: 1px solid rgba(212, 175, 55, 0.35);
      background: rgba(45, 24, 16, 0.45);
      color: #f1e1b0;
      font-size: 13px;
      line-height: 1.6;
      word-break: break-word;
    }

    .inline-link-box a {
      color: #ffd966;
      text-decoration: underline;
      word-break: break-all;
    }
  </style>
</head>
<body>
  <div class="wrapper">
    <form action="<?= ROOT ?>/ForgotPassword" method="POST" id="forgotPasswordForm" novalidate>
      <h1>Forgot Password</h1>
      <p class="helper-text">Enter your email address and we will generate a password reset link.</p>

      <?php if (!empty($error)): ?>
        <div class="error-message" style="display:block; text-align:left;">
          <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($message)): ?>
        <div class="success-message">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <div class="input-box">
        <input type="email" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required>
        <i class='bx bx-envelope'></i>
      </div>

      <button type="submit" class="btn">Send Reset Link</button>

      <div class="register-link">
        <p><a href="<?= ROOT ?>/Login">Back to login</a></p>
      </div>

      <?php if (!empty($reset_link)): ?>
        <div class="inline-link-box">
          Temporary reset link:<br>
          <a href="<?= htmlspecialchars($reset_link) ?>"><?= htmlspecialchars($reset_link) ?></a>
        </div>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
