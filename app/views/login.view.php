
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
    .success-box { background:#e6ffe6; color:#008800; padding:10px; border-radius:6px; margin-bottom:15px; text-align:center; }
    .error-box { background:#ffe6e6; color:#cc0000; padding:10px; border-radius:6px; margin-bottom:15px; text-align:center; }

    
 

        /* SUCCESS TOAST */
.toast-success, .toast-error {
    position: absolute;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: #28a745;
    color: #fff;
    padding: 14px 22px;
    font-size: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);

    /* SHOW + HIDE AUTOMATICALLY */
    animation: fadeIn 0.3s ease, fadeOut 0.3s ease 1.7s forwards;

    z-index: 9999;
    font-weight: 500;
    min-width: 300px;
    text-align: center;
}

.toast-error {
    background: #dc3545;
}

/* Fade-in animation */
@keyframes fadeIn {
    from { 
        opacity: 0; 
        transform: translate(-50%, -15px); 
    }
    to { 
        opacity: 1; 
        transform: translate(-50%, 0); 
    }
}

/* Fade-out animation */
@keyframes fadeOut {
    from { 
        opacity: 1; 
        transform: translate(-50%, 0); 
    }
    to { 
        opacity: 0; 
        transform: translate(-50%, -15px); 
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

 

    <div class="input-box">
      <input type="email" name="email" placeholder="Email" required>
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

<script>
  const toggle = document.getElementById("togglePassword");
  const password = document.getElementById("password");

  toggle.addEventListener("click", function () {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);
    this.classList.toggle("bx-show");
    this.classList.toggle("bx-hide");
  });
</script>

</body>
</html>
<?php if (!empty($_SESSION['success_message'])): ?>
    <div class="toast-success" id="successToast">
        <?= $_SESSION['success_message']; ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="toast-error" id="errorToast">
        <?= $_SESSION['error_message']; ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>
<script>
    window.onload = function() {
        const successToast = document.getElementById('successToast');
        const errorToast = document.getElementById('errorToast');

        if (successToast) {
            setTimeout(() => {
                successToast.style.display = 'none';
            }, 3000);
        }

        if (errorToast) {
            setTimeout(() => {
                errorToast.style.display = 'none';
            }, 3000);
        }
    };
