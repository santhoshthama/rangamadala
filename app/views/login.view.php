
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
        background: linear-gradient(135deg, #1a4d1a 0%, #2d5a2d 100%);
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
  </style>
</head>
<body>

<div class="wrapper">
<form action="<?= ROOT ?>/Login" method="POST" id="loginForm" novalidate>
    <div class="back-container">
      <a href="<?= ROOT ?>/Home" class="back-link">
        <button type="button" class="back-btn">
          <i class="fas fa-arrow-left"></i> Back to Selection
        </button>
      </a>
    </div>

    <h1>Login</h1>
    <p>Access your professional theater dashboard</p>

    <?php if (!empty($_SESSION['registration_success'])): ?>
        <div class="registration-success-message">
            <i class="fas fa-check-circle"></i>
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

    <?php if (!empty($success)): ?>
        <div class="success-message">
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <div class="input-box" id="emailBox">
      <input type="email" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>">
      <i class='bx bxs-user'></i>
      <div class="validation-error" id="emailError"><i class='bx bx-error-circle'></i><span></span></div>
    </div>

    <div class="input-box" id="passwordBox">
      <input type="password" name="password" id="password" placeholder="Password">
      <i class='bx bx-hide' id="togglePassword"></i>
      <div class="validation-error" id="passwordError"><i class='bx bx-error-circle'></i><span></span></div>
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
  const email = document.getElementById("email");
  const form = document.getElementById("loginForm");
  const emailBox = document.getElementById("emailBox");
  const passwordBox = document.getElementById("passwordBox");
  const emailError = document.getElementById("emailError").querySelector("span");
  const passwordError = document.getElementById("passwordError").querySelector("span");

  // Password toggle
  toggle.addEventListener("click", function () {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    this.classList.toggle("bx-show");
    this.classList.toggle("bx-hide");
  });

  // Show error function
  function showError(box, errorSpan, message) {
    box.classList.add("error");
    errorSpan.textContent = message;
    errorSpan.parentElement.style.display = "block";
  }

  // Hide error function
  function hideError(box, errorSpan) {
    box.classList.remove("error");
    errorSpan.parentElement.style.display = "none";
  }

  // Email validation
  function validateEmail(emailValue) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(emailValue);
  }

  // Real-time validation
  email.addEventListener("input", function() {
    if (this.value.trim() === "") {
      hideError(emailBox, emailError);
    } else if (!validateEmail(this.value.trim())) {
      showError(emailBox, emailError, "Please enter a valid email address");
    } else {
      hideError(emailBox, emailError);
    }
  });

  password.addEventListener("input", function() {
    if (this.value.trim() !== "") {
      hideError(passwordBox, passwordError);
    }
  });

  // Form validation on submit
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
