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
          <span>Admin</span>
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
              <span class="nav-label">Registrations</span>
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
        <div class="sidebar-footer">
          <a href="<?= ROOT ?>/home" class="btn btn-secondary sidebar-back-button">
            <span class="bx bx-home"></span>
            <span class="btn-label">Back to Site</span>
          </a>
        </div>
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

            <!-- User Profile -->
            <div class="user-menu" id="userMenu">
              <div class="user-menu-trigger" id="user-menu-trigger">
                <div class="user-avatar-small">
                  <span><?= htmlspecialchars(strtoupper(substr($_SESSION['full_name'] ?? $_SESSION['user_name'] ?? 'A', 0, 1))) ?></span>
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
          <!-- Overview View -->
          <div class="dashboard-view active" id="overview">
            <!-- Stats Cards -->
            <div class="stats-grid">
              <div class="stat-card">
                <div class="stat-card-header">
                  <div class="stat-card-title">Total Users</div>
                  <div class="stat-card-icon primary">
                    <span class="bx bx-user"></span>
                  </div>
                </div>
                <div class="stat-card-value" id="statTotalUsers">0</div>
                <div class="stat-card-change">
                  <span class="bx bx-group"></span>
                  <span>Registered non-admin users</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-card-header">
                  <div class="stat-card-title">Active Dramas</div>
                  <div class="stat-card-icon success">
                    <span class="material-symbols-rounded">theater_comedy</span>
                  </div>
                </div>
                <div class="stat-card-value" id="statActiveDramas">0</div>
                <div class="stat-card-change">
                  <span class="material-symbols-rounded">event_available</span>
                  <span>Currently active drama records</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-card-header">
                  <div class="stat-card-title">Pending User Approvals</div>
                  <div class="stat-card-icon warning">
                    <span class="material-symbols-rounded">pending_actions</span>
                  </div>
                </div>
                <div class="stat-card-value" id="statPendingUserApprovals">0</div>
                <div class="stat-card-change negative">
                  <span class="material-symbols-rounded">schedule</span>
                  <span>Awaiting approval</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-card-header">
                  <div class="stat-card-title">Pending Drama Approvals</div>
                  <div class="stat-card-icon info">
                    <span class="bx bx-check-circle"></span>
                  </div>
                </div>
                <div class="stat-card-value" id="statPendingDramaApprovals">0</div>
                <div class="stat-card-change negative">
                  <span class="bx bx-hourglass"></span>
                  <span>Waiting for admin review</span>
                </div>
              </div>
            </div>
            <!-- Charts -->
            <div class="chart-grid">
              <div class="chart-card">
                <div class="chart-card-header">
                  <h3 class="chart-card-title">User Registration Trend</h3>
                  <p class="chart-card-subtitle">New users over time</p>
                </div>
                <div class="chart-container">
                  <canvas id="userTrendChart"></canvas>
                </div>
              </div>
              <div class="chart-card">
                <div class="chart-card-header">
                  <h3 class="chart-card-title">User Distribution by Role</h3>
                  <p class="chart-card-subtitle">Distribution across roles</p>
                </div>
                <div class="chart-container">
                  <canvas id="roleDistributionChart"></canvas>
                </div>
              </div>
            </div>
            <!-- Recent Activity -->
            <div class="dashboard-table-container">
              <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Recent User Activity</h3>
                <a href="#" class="btn btn-primary">Manage Users</a>
              </div>
              <table class="dashboard-table">
                <thead>
                  <tr>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Joined Date</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="project-title-cell">
                        <div class="project-icon">
                          <span class="bx bx-user"></span>
                        </div>
                        <div class="project-info">
                          <div class="project-title-text">Rajesh Kumar</div>
                          <div class="project-meta-text">rajesh@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Artist</td>
                    <td><span class="status-badge success">Active</span></td>
                    <td>Jan 15, 2025</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="project-title-cell">
                        <div class="project-icon">
                          <span class="bx bx-user"></span>
                        </div>
                        <div class="project-info">
                          <div class="project-title-text">Priya Sharma</div>
                          <div class="project-meta-text">priya@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Service Provider</td>
                    <td><span class="status-badge success">Active</span></td>
                    <td>Jan 10, 2025</td>
                  </tr>
                  <tr>
                    <td>
                      <div class="project-title-cell">
                        <div class="project-icon">
                          <span class="bx bx-user"></span>
                        </div>
                        <div class="project-info">
                          <div class="project-title-text">Aman Singh</div>
                          <div class="project-meta-text">aman@example.com</div>
                        </div>
                      </div>
                    </td>
                    <td>Audience</td>
                    <td><span class="status-badge warning">Pending Approval</span></td>
                    <td>Jan 18, 2025</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- Projects View -->
          <div class="dashboard-view" id="users">
            <div class="dashboard-table-container">
              <div class="dashboard-table-header">
                <h2 class="dashboard-table-title">User Management</h2>
                <div class="header-actions">
                  <div class="filter-buttons">
                    <button class="btn btn-secondary filter-btn active" data-filter="all" data-target="users">All</button>
                    <button class="btn btn-secondary filter-btn" data-filter="artist" data-target="users">Artists</button>
                    <button class="btn btn-secondary filter-btn" data-filter="audience" data-target="users">Audience</button>
                    <button class="btn btn-secondary filter-btn" data-filter="service_provider" data-target="users">Service Providers</button>
                  </div>
                  <button class="btn btn-primary" onclick="showAddUserModal()">
                    <span class="material-symbols-rounded">add</span>
                    Add New User
                  </button>
                </div>
              </div>
              
              <div id="usersTableContainer">
                <!-- Loading state -->
                <div class="loading-state" id="usersLoading">
                  <span class="material-symbols-rounded spinning">progress_activity</span>
                  <p>Loading users...</p>
                </div>
                
                <!-- Empty state -->
                <div class="empty-state" id="usersEmpty" style="display: none;">
                  <div class="empty-state-icon">
                    <span class="bx bx-user"></span>
                  </div>
                  <h3 class="empty-state-title">No Users Found</h3>
                  <p class="empty-state-description">There are no users in the system yet. Add a new user to get started.</p>
                  <button class="btn btn-primary" style="margin-top: 20px;" onclick="showAddUserModal()">
                    <span class="bx bx-plus"></span>
                    Add New User
                  </button>
                </div>
                
                <!-- Users table -->
                <table class="dashboard-table" id="usersTable" style="display: none;">
                  <thead>
                    <tr>
                      <th>User Details</th>
                      <th>Role</th>
                      <th>Contact</th>
                      <th>Status</th>
                      <th>Joined Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="usersTableBody">
                    <!-- Data will be loaded dynamically -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- Tasks View -->
          <div class="dashboard-view" id="registrations">
            <div class="dashboard-table-container">
              <div class="dashboard-table-header">
                <h2 class="dashboard-table-title">Pending Registrations</h2>
                <div class="filter-buttons">
                  <button class="btn btn-secondary filter-btn active" data-filter="all">All</button>
                  <button class="btn btn-secondary filter-btn" data-filter="artist">Artists</button>
                  <button class="btn btn-secondary filter-btn" data-filter="service_provider">Service Providers</button>
                </div>
              </div>
              
              <div id="registrationsTableContainer">
                <!-- Loading state -->
                <div class="loading-state" id="registrationsLoading">
                  <span class="material-symbols-rounded spinning">progress_activity</span>
                  <p>Loading registrations...</p>
                </div>
                
                <!-- Empty state -->
                <div class="empty-state" id="registrationsEmpty" style="display: none;">
                  <div class="empty-state-icon">
                    <span class="bx bx-task"></span>
                  </div>
                  <h3 class="empty-state-title">No Pending Registrations</h3>
                  <p class="empty-state-description">All registration requests have been processed.</p>
                </div>
                
                <!-- Registrations table -->
                <table class="dashboard-table" id="registrationsTable" style="display: none;">
                  <thead>
                    <tr>
                      <th>User Details</th>
                      <th>Role</th>
                      <th>Contact</th>
                      <th>Registration Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody id="registrationsTableBody">
                    <!-- Data will be loaded dynamically -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="dashboard-view" id="drama-approvals">
            <div class="dashboard-table-container">
              <div class="dashboard-table-header">
                <h2 class="dashboard-table-title">Pending Drama Creation Requests</h2>
              </div>

              <div class="loading-state" id="dramaRequestsLoading">
                <span class="bx bx-loader-circle"></span>
                <p>Loading drama requests...</p>
              </div>

              <div class="empty-state" id="dramaRequestsEmpty" style="display: none;">
                <div class="empty-state-icon">
                  <span class="bx bx-task"></span>
                </div>
                <h3 class="empty-state-title">No Pending Drama Requests</h3>
                <p class="empty-state-description">All drama creation requests have been processed.</p>
              </div>

              <table class="dashboard-table" id="dramaRequestsTable" style="display: none;">
                <thead>
                  <tr>
                    <th>Drama</th>
                    <th>Artist</th>
                    <th>Certificate No.</th>
                    <th>Requested Date</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="dramaRequestsTableBody"></tbody>
              </table>
            </div>
          </div>
          <!-- Settings View -->
          <div class="dashboard-view" id="content">
            <!-- Content Management Tabs -->
            <div class="content-tabs">
              <button class="content-tab active" data-content-tab="swiper">
                <span class="bx bx-mask"></span>
                Drama Slides
              </button>
              <button class="content-tab" data-content-tab="gallery">
                <span class="bx bx-image"></span>
                Stage Highlights
              </button>
              <button class="content-tab" data-content-tab="testimonials">
                <span class="bx bx-comment"></span>
                Testimonials
              </button>
            </div>

            <!-- Swiper/Drama Slides Section -->
            <div class="content-section active" id="swiperSection">
              <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Drama Slides (Swiper)</h3>
                <button class="btn btn-primary" onclick="showAddSwiperModal()">
                  <span class="bx bx-mask"></span>
                  Add Slide
                </button>
              </div>
              <div class="content-grid" id="swiperGrid">
                <div class="loading-state" id="swiperLoading">
                  <span class="bx bx-loader-circle"></span>
                  <p>Loading slides...</p>
                </div>
              </div>
            </div>

            <!-- Gallery Section -->
            <div class="content-section" id="gallerySection">
              <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Stage Highlights (Gallery)</h3>
                <button class="btn btn-primary" onclick="showAddGalleryModal()">
                  <span class="bx bx-image"></span>
                  Add Image
                </button>
              </div>
              <div class="content-grid" id="galleryGrid">
                <!-- Content loads when tab is clicked -->
              </div>
            </div>

            <!-- Testimonials Section -->
            <div class="content-section" id="testimonialsSection">
              <div class="dashboard-table-header">
                <h3 class="dashboard-table-title">Testimonials</h3>
                <button class="btn btn-primary" onclick="showAddTestimonialModal()">
                  <span class="bx bx-comment"></span>
                  Add Testimonial
                </button>
              </div>
              <div class="testimonials-list" id="testimonialsList">
                <!-- Content loads when tab is clicked -->
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
    <!-- Scripts -->
    <script>var ROOT = '<?= ROOT ?>';</script>
    <script src="<?= ROOT ?>/assets/JS/admindashboard.js?v=20260404"></script>
    <script src="<?= ROOT ?>/assets/JS/admin-verification.js"></script>
    <script src="<?= ROOT ?>/assets/JS/admin-user-management.js"></script>
    <script src="<?= ROOT ?>/assets/JS/admin-content-management.js"></script>
    <script src="<?= ROOT ?>/assets/JS/admindashboard-page.js"></script>
  </body>
</html>