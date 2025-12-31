<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            crossorigin="anonymous" />
        <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/service_provider_dashboard.css">        
        <title><?= APP_NAME ?> - Service Provider Dashboard</title>
    </head>
    <body>
        <div class="sidebar">
            <div class="logo">
                <ul class="menu" class="active">
                    <li>
                        <a href="<?= ROOT ?>/Home">
                            <i class="fas fa-home"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="active">
                        <a href="<?= ROOT ?>/ServiceProviderDashboard">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-star"></i>
                            <span>Backstage</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-question-circle"></i>
                            <span>FAQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fas fa-user"></i>
                            <span>Profile</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= ROOT ?>/Logout">
                            <i class="fas fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        
        <div class="main--content">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Service Provider</span>
                    <h2>Dashboard</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" placeholder="Search">
                    </div>
                    <img src="<?= ROOT ?>/assets/images/user-default.jpg" alt="Profile" onerror="this.src='<?= ROOT ?>/assets/images/Rangamadala logo.png'">
                </div>
            </div>
           
            <section class="work">

                <div class="tabs">

                    <!-- Tab 1 -->
                    <input type="radio" name="tab" id="tab1" checked>
                    <label for="tab1" class="tab-label">Service Requests</label>
                    <!-- Tab 2 -->
                    <input type="radio" name="tab" id="tab2">
                    <label for="tab2" class="tab-label">Availability</label>
                    <!-- Tab 3 -->
                    <input type="radio" name="tab" id="tab3">
                    <label for="tab3" class="tab-label">Payments</label>
                    <!-- Tab 4 -->
                    <input type="radio" name="tab" id="tab4">
                    <label for="tab4" class="tab-label">Analytics</label>

                    <!-- Tab Contents -->
                    <div id="content1" class="tab-content">
                        <?php include __DIR__ . '/service_requests.view.php'; ?>
                    </div>

                    <div id="content2" class="tab-content">
                        <?php include __DIR__ . '/service_availability.view.php'; ?>
                    </div>
                    <div id="content3" class="tab-content">
                        <?php include __DIR__ . '/service_payment.view.php'; ?>
                    </div>
                    <div id="content4" class="tab-content">
                        <?php include __DIR__ . '/service_analytics.view.php'; ?>
                    </div>
                </div>
            </section>

            <script src="<?= ROOT ?>/assets/JS/service_provider_dashboard.js"></script>
        </div>
    </body>
</html>