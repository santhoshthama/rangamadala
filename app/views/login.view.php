
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
     <title><?= APP_NAME ?></title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Signin.css">
    <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">

  <style>
    /* Custom validation tooltip styles */
    .input-box {
      position: relative;
    }
    
    .validation-error {
      position: absolute;
      bottom: -28px;
      left: 0;
      right: 0;
      background: linear-gradient(135deg, #2d1810 0%, #3d2817 100%);
      color: #d4af37;
      font-size: 12px;
      padding: 6px 12px;
      border-radius: 6px;
      border: 1px solid #d4af37;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.25);
      display: none;
      animation: tooltipFade 0.3s ease;
      z-index: 10;
    }
    
    .validation-error::before {
      content: '';
      position: absolute;
      top: -6px;
      left: 20px;
      width: 10px;
      height: 10px;
      background: #2d1810;
      border-left: 1px solid #d4af37;
      border-top: 1px solid #d4af37;
      transform: rotate(45deg);
    }
    
    .validation-error i {
      margin-right: 6px;
      color: #d4af37;
    }
    
    .input-box.error input {
      border-color: #d4af37 !important;
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.3) !important;
    }
    
    @keyframes tooltipFade {
      from {
        opacity: 0;
        transform: translateY(-5px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .success-message {
      background: linear-gradient(135deg, #1a4d1a 0%, #2d5a2d 100%);
      color: #90EE90;
      padding: 14px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: center;
      border: 1px solid #4a7c4a;
      box-shadow: 0 4px 15px rgba(45, 90, 45, 0.3);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      animation: slideDown 0.4s ease;
    }

    .registration-success-message {
      background: linear-gradient(135deg, #2d1810 0%, #3d2817 100%);
      color: #d4af37;
      padding: 18px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: left;
      border: 1px solid #d4af37;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
      animation: slideDown 0.4s ease;
      font-size: 14px;
      line-height: 1.6;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    .registration-success-message i {
      font-size: 24px;
      margin-top: 2px;
      color: #d4af37;
    }

    .registration-success-message strong {
      display: block;
      margin-bottom: 4px;
      font-size: 15px;
      color: #e8d5a8;
    }

    .error-message {
      background: linear-gradient(135deg, #2d1810 0%, #4a1515 100%);
      color: #ff9999;
      padding: 16px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      text-align: left;
      border: 1px solid #d4af37;
      border-left: 4px solid #d4af37;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
      animation: slideDown 0.4s ease;
      font-size: 14px;
      line-height: 1.6;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }
    
    .error-message::before {
      content: '⚠';
      font-size: 18px;
      color: #d4af37;
      flex-shrink: 0;
    }

    .error-message strong {
      font-weight: 600;
      color: #e8d5a8;
    }

    .success-message::before {
      content: '✓';
      font-weight: bold;
      font-size: 18px;
      color: #90EE90;
    }

    .forgot-password-link {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      color: #d4af37;
      text-decoration: none;
      font-size: 13px;
      font-weight: 600;
      transition: color 0.2s ease;
    }

    .forgot-password-link:hover {
      color: #ffd966;
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
        background:linear-gradient(135deg, #a8e063, #56ab2f);
        color: #90EE90;
        padding: 16px 24px;
        font-size: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.35);
        z-index: 9999;
        font-weight: 500;
        min-width: 320px;
        text-align: center;
        border: 1px solid #4a7c4a;
        animation: toastSlideDown 0.4s ease forwards;
    }

    .toast-error {
        background: linear-gradient(135deg, #2d1810 0%, #4a1515 100%);
        border: 1px solid #d4af37;
        color: #ff9999;
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

    .confirm-box {
      width: 100%;
      max-width: 760px;
      background: rgba(26, 20, 16, 0.92);
      border: 2px solid rgba(212, 175, 55, 0.35);
      border-radius: 16px;
      overflow: hidden;
      -webkit-backdrop-filter: blur(25px);
      backdrop-filter: blur(25px);
      box-shadow: 0 14px 42px rgba(212, 175, 55, 0.22), 0 0 50px rgba(0, 0, 0, 0.38);
    }

    .confirm-header {
      padding: 18px 34px;
      border-bottom: 1px solid rgba(212, 175, 55, 0.25);
      font-size: 30px;
      font-weight: 700;
      color: #d4af37;
      letter-spacing: 0.4px;
    }

    .confirm-body {
      padding: 16px 34px;
      border-bottom: 1px solid rgba(212, 175, 55, 0.2);
      color: #f2e7cf;
      line-height: 1.45;
      font-size: 17px;
    }

    .confirm-actions {
      padding: 14px 34px 18px;
      display: flex;
      justify-content: flex-end;
      gap: 12px;
    }

    .wrapper.wrapper-confirm {
      max-width: 760px;
      background: transparent;
      border: none;
      box-shadow: none;
      -webkit-backdrop-filter: none;
      backdrop-filter: none;
      padding: 0;
    }

    .confirm-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      text-decoration: none;
      border-radius: 12px;
      padding: 11px 22px;
      font-size: 16px;
      font-weight: 600;
      border: 1px solid transparent;
      transition: all 0.3s ease;
    }

    .confirm-btn.cancel {
      background: rgba(212, 175, 55, 0.09);
      color: #f2e7cf;
      border-color: rgba(212, 175, 55, 0.35);
    }

    .confirm-btn.cancel:hover {
      background: rgba(212, 175, 55, 0.18);
      color: #fff5d8;
    }

    .confirm-btn.logout {
      background: linear-gradient(135deg, #d4af37 0%, #aa8c2c 100%);
      color: #1a1410;
      border-color: rgba(140, 109, 26, 0.45);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.25);
    }

    .confirm-btn.logout:hover {
      background: linear-gradient(135deg, #e8c547 0%, #b39632 100%);
      box-shadow: 0 10px 28px rgba(212, 175, 55, 0.38);
      transform: translateY(-2px);
    }

    .back-container {
      position: fixed;
      top: 20px;
      left: 20px;
      z-index: 20;
    }

    .back-link {
      text-decoration: none;
      display: inline-block;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background: rgba(212, 175, 55, 0.15);
      color: #d4af37;
      padding: 10px 18px;
      border: 1.5px solid rgba(212, 175, 55, 0.4);
      border-radius: 8px;
      font-size: 14px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      -webkit-backdrop-filter: blur(10px);
      backdrop-filter: blur(10px);
    }

    .back-btn:hover {
      background: rgba(212, 175, 55, 0.25);
      border-color: #d4af37;
      color: #f5f0e8;
      transform: translateX(-3px);
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
    }

    .back-btn i {
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .back-container {
        top: 12px;
        left: 12px;
      }

      .back-btn {
        padding: 9px 14px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>

<div class="back-container">
  <a href="<?= ROOT ?>/Home" class="back-link">
    <button type="button" class="back-btn">
      <i class="bx bx-arrow-left"></i> Back to Home
    </button>
  </a>
</div>

<div class="wrapper<?= !empty($already_logged_in) ? ' wrapper-confirm' : '' ?>">
<?php if (!empty($already_logged_in)): ?>
  <div class="confirm-box">
    <div class="confirm-header">Confirm</div>
    <div class="confirm-body">
      You are already logged in as <?= htmlspecialchars($current_user_name ?? 'User') ?>, you need to log out before logging in as different user.
    </div>
    <div class="confirm-actions">
      <a href="<?= htmlspecialchars($cancel_url ?? (ROOT . '/Home')) ?>" class="confirm-btn cancel">Cancel</a>
      <a href="<?= ROOT ?>/Logout" class="confirm-btn logout">Log out</a>
    </div>
  </div>
<?php else: ?>
<form action="<?= ROOT ?>/Login" method="POST" id="loginForm" novalidate>
    <h1>Login</h1>
    <p>Access your professional theater dashboard</p>

    <?php if (!empty($_SESSION['registration_success'])): ?>
        <div class="registration-success-message">
            <i class='bx bx-check'></i>
            <div>
                <strong>Registration Submitted!</strong><br>
                <?= $_SESSION['registration_message'] ?? 'Your account is pending verification.' ?>
            </div>
        </div>
        <?php unset($_SESSION['registration_success'], $_SESSION['registration_message']); ?>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="error-message" style="display: block; text-align: left;">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($approval_notice)): ?>
      <div class="registration-success-message">
        <i class='bx bx-badge-check'></i>
        <div>
          <strong>Account Approved!</strong><br>
          <?= htmlspecialchars($approval_notice) ?>
        </div>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success-message">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="input-box" id="emailBox">
      <input type="email" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>">
      <i class='bx bx-envelope'></i>
      <div class="validation-error" id="emailError"><i class='bx bx-error'></i><span></span></div>
    </div>

    <div class="input-box" id="passwordBox">
      <input type="password" name="password" id="password" placeholder="Password">
      <i class='bx bx-hide' id="togglePassword"></i>
      <div class="validation-error" id="passwordError"><i class='bx bx-error'></i><span></span></div>
    </div>

    <div class="remember-frogot">
      <label><input type="checkbox">Remember me</label>
      <a href="<?= ROOT ?>/ForgotPassword" class="forgot-password-link">Forgot password?</a>
    </div>

    <button type="submit" class="btn">Login</button>

    <div class="register-link">
      <p>Don't have an account? <a href="<?= ROOT ?>/Signup">Sign up</a></p>
    </div>
  </form>
<?php endif; ?>
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
  const email = document.getElementById("email");
  const form = document.getElementById("loginForm");
  const emailBox = document.getElementById("emailBox");
  const passwordBox = document.getElementById("passwordBox");
  const emailErrorNode = document.getElementById("emailError");
  const passwordErrorNode = document.getElementById("passwordError");
  const emailError = emailErrorNode ? emailErrorNode.querySelector("span") : null;
  const passwordError = passwordErrorNode ? passwordErrorNode.querySelector("span") : null;

  // Password toggle
  if (toggle && password) {
    toggle.addEventListener("click", function () {
      const type = password.getAttribute("type") === "password" ? "text" : "password";
      password.setAttribute("type", type);
      this.classList.toggle("bx-show");
      this.classList.toggle("bx-hide");
    });
  }

  // Show error function
  function showError(box, errorSpan, message) {
    if (!box || !errorSpan || !errorSpan.parentElement) return;
    box.classList.add("error");
    errorSpan.textContent = message;
    errorSpan.parentElement.style.display = "block";
  }

  // Hide error function
  function hideError(box, errorSpan) {
    if (!box || !errorSpan || !errorSpan.parentElement) return;
    box.classList.remove("error");
    errorSpan.parentElement.style.display = "none";
  }

  // Email validation
  function validateEmail(emailValue) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(emailValue);
  }

  // Real-time validation
  if (email) {
    email.addEventListener("input", function() {
      if (this.value.trim() === "") {
        hideError(emailBox, emailError);
      } else if (!validateEmail(this.value.trim())) {
        showError(emailBox, emailError, "Please enter a valid email address");
      } else {
        hideError(emailBox, emailError);
      }
    });
  }

  if (password) {
    password.addEventListener("input", function() {
      if (this.value.trim() !== "") {
        hideError(passwordBox, passwordError);
      }
    });
  }

  // Form validation on submit
  if (form && email && password) {
    form.addEventListener("submit", function(e) {
      let isValid = true;

      // Validate email
      if (email.value.trim() === "") {
        showError(emailBox, emailError, "Email address is required");
        isValid = false;
      } else if (!validateEmail(email.value.trim())) {
        showError(emailBox, emailError, "Please enter a valid email address");
        isValid = false;
      } else {
        hideError(emailBox, emailError);
      }

      // Validate password
      if (password.value.trim() === "") {
        showError(passwordBox, passwordError, "Password is required");
        isValid = false;
      } else {
        hideError(passwordBox, passwordError);
      }

      if (!isValid) {
        e.preventDefault();
      }
    });
  }

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
