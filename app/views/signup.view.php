<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="shortcut icon" href="<?php echo ROOT; ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/Signup.css">

</head>

<body>

    <div class="main-wrapper">
        <div class="back-container">
            <a href="<?= ROOT ?>/Home" class="back-link">
                <button type="button" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Selection
                </button>
            </a>
        </div>

        <div class="header-section">
            <h1>Sign Up</h1>
            <p>Choose your role to create your account and connect with drama professionals</p>
        </div>

        <div class="role-selection-container">
            <!-- Audience Card -->
            <a href="<?= ROOT ?>/AudienceRegister" class="role-card">
                <i class="fas fa-users"></i>
                <h3>Audience</h3>
                <p>Discover and enjoy amazing drama performances. Book tickets, follow your favorite artists, and stay
                    updated with the latest shows.</p>
            </a>

            <!-- Artist Card -->
            <a href="<?= ROOT ?>/ArtistRegister" class="role-card">
                <i class="fas fa-theater-masks"></i>
                <h3>Artist</h3>
                <p>Showcase your talent and connect with directors and production teams. Build your portfolio and find
                    exciting opportunities in drama.</p>
            </a>

            <!-- Service Provider Card -->
            <a href="<?= ROOT ?>/ServiceProviderRegister" class="role-card">
                <i class="fas fa-tools"></i>
                <h3>Service Provider</h3>
                <p>Offer your professional services to drama productions. Connect with production teams and provide
                    essential services for performances.</p>
            </a>
        </div>

        <div class="register-link" style="text-align: center; margin-top: 40px;">
            <p>Already have an account? <a href="<?= ROOT ?>/Login">Login</a></p>
        </div>
    </div>





















    <!--
    <div class="signup_container">
    <form action="<?//php echo ROOT ?>/signup" method="post">
        <?/*php
      if (isset($data['error'])) {
          echo '<p class="error" style="color:red">'.$data['error'].'</p> </br>';
      }*/
        ?>
        <label for="name">Name</label>
        <input type="text" name="name" id="name" value="<?/*php $data['name']*/ ?>" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" rvalue="<?/*php $data['email']*/ ?>" equired>
        
        <label for="password">Password</label>
        <input type="password" name="password" id="password" required>

         <label for="confirm_password">Confirm password</label>
        <input type="password" name="confirm_password" id="confirm_password" required>

        <label for="profie_image">image</label>
        <input type="file" name="profile_image" id="profile_image" accept="image/*">

        <button type="submit">Sign Up</button>
    </form>
</div>

-->

</body>

</html>