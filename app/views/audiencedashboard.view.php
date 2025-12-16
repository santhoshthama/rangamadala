<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= APP_NAME ?> - Audience Dashboard</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <!-- Google Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />

  <!-- Dashboard CSS -->
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css" />

  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>

<body>
  <div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="dashboard-sidebar" id="dashboardSidebar">
      <div class="dashboard-brand">
        <button class="dashboard-sidebar-toggle">
          <span class="material-symbols-rounded">menu</span>
        </button>
        <a class="logo">Audience Dashboard</a>
      </div>

      <nav class="dashboard-nav">
        <div class="dashboard-nav-section">

          <!-- Overview -->
          <a href="#" class="dashboard-nav-item active" data-view="overview">
            <span class="nav-icon material-symbols-rounded">home</span>
            <span class="nav-label">Overview</span>
          </a>

          <!-- Browse Dramas / Events -->
          <a href="#" class="dashboard-nav-item" data-view="browse">
            <span class="nav-icon material-symbols-rounded">movie</span>
            <span class="nav-label">Browse Dramas</span>
          </a>

          <!-- My Tickets -->
          <a href="#" class="dashboard-nav-item" data-view="tickets">
            <span class="nav-icon material-symbols-rounded">confirmation_number</span>
            <span class="nav-label">My Tickets</span>
          </a>

          <!-- Payment History -->
          <a href="#" class="dashboard-nav-item" data-view="payments">
            <span class="nav-icon material-symbols-rounded">receipt_long</span>
            <span class="nav-label">Payment History</span>
          </a>

          <!-- Settings -->
          <a href="#" class="dashboard-nav-item" data-view="settings">
            <span class="nav-icon material-symbols-rounded">settings</span>
            <span class="nav-label">Account Settings</span>
          </a>

        </div>
      </nav>

      <div class="sidebar-footer">
        <a href="<?= ROOT ?>/home" class="btn btn-secondary sidebar-back-button">
          <span class="material-symbols-rounded">logout</span>
          <span class="btn-label">Logout</span>
        </a>
      </div>
    </aside>

    <div class="dashboard-sidebar-overlay" id="dashboardSidebarOverlay"></div>

    <!-- MAIN CONTENT -->
    <main class="dashboard-main">

      <header class="dashboard-header">
        <div class="dashboard-header-content">
          <button class="dashboard-sidebar-toggle">
            <span class="material-symbols-rounded">menu</span>
          </button>
          <h1 class="dashboard-header-title" id="dashboardTitle">Overview</h1>
        </div>

        <div class="dashboard-header-actions">
          <div class="user-menu" id="userMenu">
            <div class="user-menu-trigger" id="user-menu-trigger">
              <div class="user-avatar-small">
                <img
                  src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop&crop=face&auto=format"
                  alt="User Avatar" />
              </div>
            </div>

            <div class="user-menu-dropdown">
              <a href="#" class="user-menu-item">
                <span class="icon material-symbols-rounded">person</span>
                <span>Profile</span>
              </a>
              <div class="user-menu-item theme-item">
                <span class="icon material-symbols-rounded">palette</span>
                <div class="theme-toggle" id="theme-toggle">
                  <div class="theme-option active" data-theme="light">Light</div>
                  <div class="theme-option" data-theme="dark">Dark</div>
                </div>
              </div>

              <a href="#" class="user-menu-item">
                <span class="icon material-symbols-rounded">logout</span>
                <span>Logout</span>
              </a>
            </div>
          </div>
        </div>
      </header>

      <!-- Dashboard Views -->
      <div class="dashboard-content">

        <!-- Overview -->
        <div class="dashboard-view active" id="overview">
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Total Bookings</div>
                <div class="stat-card-icon primary">
                  <span class="material-symbols-rounded">confirmation_number</span>
                </div>
              </div>
              <div class="stat-card-value">4</div>
              <div class="stat-card-change positive">
                <span class="material-symbols-rounded">trending_up</span>
                <span>+1 this month</span>
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Upcoming Shows</div>
                <div class="stat-card-icon info">
                  <span class="material-symbols-rounded">event</span>
                </div>
              </div>
              <div class="stat-card-value">2</div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Watched Dramas</div>
                <div class="stat-card-icon success">
                  <span class="material-symbols-rounded">done_all</span>
                </div>
              </div>
              <div class="stat-card-value">6</div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Notifications</div>
                <div class="stat-card-icon warning">
                  <span class="material-symbols-rounded">notifications</span>
                </div>
              </div>
              <div class="stat-card-value">3</div>
            </div>
          </div>

          <div class="dashboard-table-container">
            <div class="dashboard-table-header">
              <h3 class="dashboard-table-title">Recent Bookings</h3>
              <a href="#" class="btn btn-secondary">View All</a>
            </div>

            <table class="dashboard-table">
              <thead>
                <tr>
                  <th>Drama</th>
                  <th>Date</th>
                  <th>Venue</th>
                  <th>Status</th>
                </tr>
              </thead>

              <tbody>
                <tr>
                  <td>Stage Play - "Sonduru Siththam"</td>
                  <td>Nov 23, 2024</td>
                  <td>Colombo</td>
                  <td><span class="status-badge success">Confirmed</span></td>
                </tr>

                <tr>
                  <td>Drama - "Siri Pade"</td>
                  <td>Dec 05, 2024</td>
                  <td>Kandy</td>
                  <td><span class="status-badge warning">Pending</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Browse Dramas -->
        <div class="dashboard-view" id="browse">
          <div class="empty-state">
            <div class="empty-state-icon">
              <span class="material-symbols-rounded">movie</span>
            </div>
            <h3 class="empty-state-title">Browse Dramas</h3>
            <p class="empty-state-description">Explore latest dramas and stage shows.</p>
          </div>
        </div>

        <!-- My Tickets -->
        <div class="dashboard-view" id="tickets">
          <div class="empty-state">
            <div class="empty-state-icon">
              <span class="material-symbols-rounded">confirmation_number</span>
            </div>
            <h3 class="empty-state-title">My Tickets</h3>
            <p class="empty-state-description">Your booked drama tickets will appear here.</p>
          </div>
        </div>

        <!-- Payment History -->
        <div class="dashboard-view" id="payments">
          <div class="empty-state">
            <div class="empty-state-icon">
              <span class="material-symbols-rounded">receipt_long</span>
            </div>
            <h3 class="empty-state-title">Payment History</h3>
            <p class="empty-state-description">View your booking payment records.</p>
          </div>
        </div>

        <!-- Settings -->
        <div class="dashboard-view" id="settings">
          <div class="empty-state">
            <div class="empty-state-icon">
              <span class="material-symbols-rounded">settings</span>
            </div>
            <h3 class="empty-state-title">Account Settings</h3>
            <p class="empty-state-description">Change your password, update profile, and manage preferences.</p>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= ROOT ?>/assets/JS/admindashboard.js"></script>
</body>

</html>