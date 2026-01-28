
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
     <title><?= APP_NAME ?></title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Signin.css">
    <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">

  <style>
    .success-message {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: #fff;
      padding: 14px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      border-left: 4px solid #155724;
      box-shadow: 0 4px 12px rgba(40, 167, 69, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      animation: slideDown 0.4s ease;
    }

    .error-message {
      background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
      color: #fff;
      padding: 14px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      border-left: 4px solid #721c24;
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      animation: slideDown 0.4s ease;
    }

    .success-message::before {
      content: '✓';
      font-weight: bold;
      font-size: 18px;
    }

    .error-message::before {
      content: '✕';
      font-weight: bold;
      font-size: 18px;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* SUCCESS TOAST */
    .toast-success, .toast-error {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #28a745;
        color: #fff;
        padding: 16px 24px;
        font-size: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        z-index: 9999;
        font-weight: 500;
        min-width: 320px;
        text-align: center;
        border-left: 4px solid #155724;
        animation: toastSlideDown 0.4s ease forwards;
    }

    .toast-error {
        background: #dc3545;
        border-left: 4px solid #721c24;
    }

    /* Toast animations */
    @keyframes toastSlideDown {
        from {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    }

    @keyframes toastSlideUp {
        from {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        to {
            opacity: 0;
            transform: translateX(-50%) translateY(-20px);
        }
    }
  </style>
</head>
<body>

<div class="wrapper">
<form action="<?= ROOT ?>/Login" method="POST">
    <div class="back-container">
      <a href="<?= ROOT ?>/Home" class="back-link">
        <button type="button" class="back-btn">
          <i class="fas fa-arrow-left"></i> Back to Selection
        </button>
      </a>
    </div>

    <h1>Login</h1>
    <p>Access your professional theater dashboard</p>

    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success-message">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="input-box">
      <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required>
      <i class='bx bxs-user'></i>
    </div>

    <div class="input-box">
      <input type="password" name="password" id="password" placeholder="Password" required>
      <i class='bx bx-hide' id="togglePassword"></i>
    </div>

    <div class="remember-frogot">
      <label><input type="checkbox">Remember me</label>
      <a href="#">Forgot password?</a>
    </div>

    <button type="submit" class="btn">Login</button>

    <div class="register-link">
      <p>Don't have an account? <a href="<?= ROOT ?>/Signup">Sign up</a></p>
    </div>
  </form>
</div>

<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="toast-success" id="successToast">
        ✓ <?= $_SESSION['success_message']; ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="toast-error" id="errorToast">
        ✕ <?= $_SESSION['error_message']; ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<script>
  const toggle = document.getElementById("togglePassword");
  const password = document.getElementById("password");

  toggle.addEventListener("click", function () {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    this.classList.toggle("bx-show");
    this.classList.toggle("bx-hide");
  });

  // Handle toast messages
  window.addEventListener('load', function() {
      const successToast = document.getElementById('successToast');
      const errorToast = document.getElementById('errorToast');

      if (successToast) {
          setTimeout(() => {
              successToast.style.animation = 'toastSlideUp 0.4s ease forwards';
              setTimeout(() => {
                  successToast.remove();
              }, 400);
          }, 3600);
      }

      if (errorToast) {
          setTimeout(() => {
              errorToast.style.animation = 'toastSlideUp 0.4s ease forwards';
              setTimeout(() => {
                  errorToast.remove();
              }, 400);
          }, 3600);
      }
  });
</script>

</body>
</html>
