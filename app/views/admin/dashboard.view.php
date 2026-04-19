<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
     <title><?= APP_NAME ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <!-- Google Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <!-- Font Awesome -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<!-- CSS -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css" />
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard-page.css" />
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/toast.css" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">
  </head>
  <body>
    <!-- Toast Notification Script -->
    <script src="<?= ROOT ?>/assets/JS/toast.js"></script>
    <?php if (!empty($_SESSION['success_message'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        toastSuccess('<?= addslashes($_SESSION['success_message']); ?>');
      });
    </script>
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_message'])): ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        toastError('<?= addslashes($_SESSION['error_message']); ?>');
      });
    </script>
    <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    <div class="dashboard-container">
      <!-- Dashboard Sidebar -->
      <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="dashboard-brand">
          <div class="logo">
            <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" />
          </div>
        </div>
        <nav class="dashboard-nav">
          <div class="dashboard-nav-section">
            <a href="#" class="dashboard-nav-item active" data-view="overview">
              <span class="nav-icon bx bx-home"></span>
              <span class="nav-label">Overview</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="users">
              <span class="nav-icon bx bx-user"></span>
              <span class="nav-label">User Management</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="registrations">
              <span class="nav-icon bx bx-user-plus"></span>
              <span class="nav-label">User Approvals</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="drama-approvals">
              <span class="nav-icon bx bx-check-circle"></span>
              <span class="nav-label">Drama Approvals</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="content">
              <span class="nav-icon bx bx-file"></span>
              <span class="nav-label">Content</span>
            </a>
          </div>
        </nav>
        <!-- Back to Site Button -->

      </aside>
      <div class="dashboard-sidebar-overlay" id="dashboardSidebarOverlay"></div>
      <!-- Dashboard Main Content -->
      <main class="dashboard-main">
        <!-- Dashboard Header -->
        <header class="dashboard-header">
          <!-- Header Content -->
          <div class="dashboard-header-content">
            <button class="dashboard-sidebar-toggle">
              <span class="bx bx-menu"></span>
            </button>
            <h1 class="dashboard-header-title" id="dashboardTitle">Overview</h1>
          </div>
          <!-- Search Container -->

          <!-- Header Actions -->
          <div class="dashboard-header-actions">
            <!-- Mobile Search Button -->
            <button class="mobile-search-btn btn btn-ghost" id="mobileSearchBtn">
              <span class="bx bx-search"></span>
            </button>
            <!-- Notification Button -->

            <!-- Admin Role Badge -->
            <div class="admin-role-badge">
              <i class='bx bxs-star'></i>
              <span>Admin</span>
            </div>

            <!-- User Profile -->
            <div class="user-menu" id="userMenu">
              <div class="user-menu-trigger" id="user-menu-trigger">
                <div class="user-avatar-small">
                  <img
                    src="<?= htmlspecialchars($data['dashboard_profile_image'] ?? (ROOT . '/uploads/profile_images/user_profile.png')) ?>"
                    onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'"
                    alt="Admin Avatar" />
                </div>
              </div>
              <div class="user-menu-dropdown">
                <a href="#" class="user-menu-item" id="adminProfileMenuItem">
                  <span class="icon bx bx-user"></span>
                  <span>Profile</span>
                </a>
                <!-- Theme Toggle inside dropdown -->
               
                <a href="<?= ROOT ?>/Logout" class="user-menu-item">
                  <span class="icon bx bx-log-out"></span>
                  <span>Log Out</span>
                </a>
              </div>
            </div>
          </div>
        </header>
        <!-- Dashboard Content -->
        <div class="dashboard-content">
          <!-- Tab Views -->
          <?php require __DIR__ . '/tabs/overview.tab.php'; ?>
          <?php require __DIR__ . '/tabs/users.tab.php'; ?>
          <?php require __DIR__ . '/tabs/registrations.tab.php'; ?>
          <?php require __DIR__ . '/tabs/drama-approvals.tab.php'; ?>
          <?php require __DIR__ . '/tabs/content.tab.php'; ?>
        </div>
      </main>
    </div>
    <!-- Scripts -->
    <script>var ROOT = '<?= ROOT ?>';</script>
    <script src="<?= ROOT ?>/assets/JS/admin/admindashboard.js?v=20260404"></script>
    <script src="<?= ROOT ?>/assets/JS/admin/admin-verification.js"></script>
    <script src="<?= ROOT ?>/assets/JS/admin/admin-user-management.js"></script>
    <script src="<?= ROOT ?>/assets/JS/admin/admin-content-management.js"></script>
    <script src="<?= ROOT ?>/assets/JS/admin/admindashboard-page.js"></script>
  </body>
</html>