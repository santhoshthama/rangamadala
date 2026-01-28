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
    <!-- CSS -->
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="shortcut icon" href="<?php echo ROOT;?>/assets/images/Rangamadala logo.png" type="image/x-icon">

  </head>
  <body>
    <div class="dashboard-container">
      <!-- Dashboard Sidebar -->
      <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div class="dashboard-brand">
          <button class="dashboard-sidebar-toggle">
            <span class="material-symbols-rounded">menu</span>
          </button>
          <a class="logo">ADMIN Dashboard</a>
        </div>
        <nav class="dashboard-nav">
          <div class="dashboard-nav-section">
            <a href="#" class="dashboard-nav-item active" data-view="overview">
              <span class="nav-icon material-symbols-rounded">dashboard</span>
              <span class="nav-label">Overview</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="users">
              <span class="nav-icon material-symbols-rounded">people</span>
              <span class="nav-label">User Management</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="registrations">
              <span class="nav-icon material-symbols-rounded">app_registration</span>
              <span class="nav-label">Registrations</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="permissions">
              <span class="nav-icon material-symbols-rounded">security</span>
              <span class="nav-label">Permissions</span>
            </a>
            <a href="#" class="dashboard-nav-item" data-view="content">
              <span class="nav-icon material-symbols-rounded">article</span>
              <span class="nav-label">Content</span>
            </a>
          </div>
        </nav>
        <!-- Back to Site Button -->
        <div class="sidebar-footer">
          <a href="<?= ROOT ?>/home" class="btn btn-secondary sidebar-back-button">
            <span class="material-symbols-rounded">home</span>
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
              <span class="material-symbols-rounded">menu</span>
            </button>
            <h1 class="dashboard-header-title" id="dashboardTitle">Overview</h1>
          </div>
          <!-- Search Container -->
          <div class="search-container" id="searchContainer">
            <span class="search-icon material-symbols-rounded">search</span>
            <input type="search" class="search-input form-input" placeholder="Search projects, tasks, reports..." id="searchInput" />
            <button class="search-close btn" id="searchClose">
              <span class="material-symbols-rounded">close</span>
            </button>
          </div>
          <!-- Header Actions -->
          <div class="dashboard-header-actions">
            <!-- Mobile Search Button -->
            <button class="mobile-search-btn btn btn-ghost" id="mobileSearchBtn">
              <span class="material-symbols-rounded">search</span>
            </button>
            <!-- Notification Button -->
            <div class="notification-button">
              <span class="material-symbols-rounded">notifications</span>
              <div class="notification-badge">3</div>
            </div>
            <!-- User Profile -->
            <div class="user-menu" id="userMenu">
              <div class="user-menu-trigger" id="user-menu-trigger">
                <div class="user-avatar-small">
                  <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face&auto=format" alt="User Avatar" />
                </div>
              </div>
              <div class="user-menu-dropdown">
                <a href="#" class="user-menu-item">
                  <span class="icon material-symbols-rounded">person</span>
                  <span>Profile</span>
                </a>
                <!-- Theme Toggle inside dropdown -->
                <div class="user-menu-item theme-item">
                  <span class="icon material-symbols-rounded">palette</span>
                  <div class="theme-toggle" id="theme-toggle">
                    <div class="theme-option active" data-theme="light">Light</div>
                    <div class="theme-option" data-theme="dark">Dark</div>
                  </div>
                </div>
                <a href="<?= ROOT ?>/Logout" class="user-menu-item">
                  <span class="icon material-symbols-rounded">logout</span>
                  <span>Sign Out</span>
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
                    <span class="material-symbols-rounded">people</span>
                  </div>
                </div>
                <div class="stat-card-value">156</div>
                <div class="stat-card-change positive">
                  <span class="material-symbols-rounded">trending_up</span>
                  <span>+12 this month</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-card-header">
                  <div class="stat-card-title">Active Dramas</div>
                  <div class="stat-card-icon success">
                    <span class="material-symbols-rounded">theater_comedy</span>
                  </div>
                </div>
                <div class="stat-card-value">24</div>
                <div class="stat-card-change positive">
                  <span class="material-symbols-rounded">trending_up</span>
                  <span>+3 this week</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-card-header">
                  <div class="stat-card-title">Pending Registrations</div>
                  <div class="stat-card-icon warning">
                    <span class="material-symbols-rounded">pending_actions</span>
                  </div>
                </div>
                <div class="stat-card-value">8</div>
                <div class="stat-card-change negative">
                  <span class="material-symbols-rounded">schedule</span>
                  <span>Awaiting approval</span>
                </div>
              </div>
              <div class="stat-card">
                <div class="stat-card-header">
                  <div class="stat-card-title">Active Roles</div>
                  <div class="stat-card-icon info">
                    <span class="material-symbols-rounded">assignment</span>
                  </div>
                </div>
                <div class="stat-card-value">5</div>
                <div class="stat-card-change positive">
                  <span class="material-symbols-rounded">check_circle</span>
                  <span>Configured</span>
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
                          <span class="material-symbols-rounded">person</span>
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
                          <span class="material-symbols-rounded">person</span>
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
                          <span class="material-symbols-rounded">person</span>
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
            <div class="empty-state">
              <div class="empty-state-icon">
                <span class="material-symbols-rounded">people</span>
              </div>
              <h3 class="empty-state-title">User Management</h3>
              <p class="empty-state-description">Add, remove, view, and edit user accounts. Manage user details and account status.</p>
              <button class="btn btn-primary" style="margin-top: 20px;">
                <span class="material-symbols-rounded">add</span>
                Add New User
              </button>
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
                    <span class="material-symbols-rounded">task_alt</span>
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
          <!-- Reports View -->
          <div class="dashboard-view" id="permissions">
            <div class="empty-state">
              <div class="empty-state-icon">
                <span class="material-symbols-rounded">security</span>
              </div>
              <h3 class="empty-state-title">Permissions Management</h3>
              <p class="empty-state-description">Configure user roles and permissions. Manage access levels for different user types.</p>
            </div>
          </div>
          <!-- Settings View -->
          <div class="dashboard-view" id="content">
            <div class="empty-state">
              <div class="empty-state-icon">
                <span class="material-symbols-rounded">article</span>
              </div>
              <h3 class="empty-state-title">Website Content Management</h3>
              <p class="empty-state-description">Add, edit, or delete website content. Manage pages and content sections.</p>
              <button class="btn btn-primary" style="margin-top: 20px;">
                <span class="material-symbols-rounded">add</span>
                Add New Content
              </button>
            </div>
          </div>
        </div>
      </main>
    </div>
    <!-- Scripts -->
    <script src="<?= ROOT ?>/assets/JS/admindashboard.js"></script>
    <script src="<?= ROOT ?>/assets/JS/admin-verification.js"></script>
    <script>
      const ROOT = '<?= ROOT ?>';
    </script>
  </body>
</html>