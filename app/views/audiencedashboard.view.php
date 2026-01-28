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

  <!-- Material Design Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

  <!-- Dashboard CSS -->
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css" />

  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>

<body>
  <div class="dashboard-container">

    <!-- Sidebar -->
    <aside class="dashboard-sidebar" id="dashboardSidebar">
      <div class="dashboard-brand">
        <div class="logo">👥</div>
        <span>Audience</span>
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
        <a href="<?= ROOT ?>/Logout" class="btn btn-secondary sidebar-back-button">
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
              <a href="<?= ROOT ?>/AudienceProfile" class="user-menu-item">
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

              <a href="<?= ROOT ?>/Logout" class="user-menu-item">
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
          <div class="browse-dramas-container">
            <div class="browse-header">
              <h2 class="browse-title">Browse Dramas</h2>
              <p class="browse-subtitle">Discover amazing theatrical performances</p>
            </div>

            <!-- Search & Filter -->
            <div class="browse-search-filter">
              <div class="search-box">
                <span class="material-symbols-rounded">search</span>
                <input type="text" id="dramaSearch" placeholder="Search dramas..." />
              </div>
              <div class="filter-box">
                <span class="material-symbols-rounded">filter_list</span>
                <select id="categoryFilter">
                  <option value="">All Categories</option>
                  <?php if (!empty($data['categories'])): ?>
                    <?php foreach ($data['categories'] as $category): ?>
                      <option value="<?= $category->id ?>"><?= htmlspecialchars($category->name) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <!-- Results Info -->
            <div class="browse-results-info">
              <p>Found <strong id="dramaCount"><?= $data['total_dramas'] ?></strong> drama(s)</p>
            </div>

            <!-- Dramas Grid -->
            <div class="dramas-grid" id="dramasGrid">
              <?php if (!empty($data['dramas'])): ?>
                <?php foreach ($data['dramas'] as $drama): ?>
                  <div class="drama-card" data-category="<?= $drama->category_id ?>" data-title="<?= strtolower($drama->title) ?>">
                    <div class="drama-image">
                      <?php if (!empty($drama->image)): ?>
                        <img src="<?= ROOT ?>/uploads/dramas/<?= htmlspecialchars($drama->image) ?>" alt="<?= htmlspecialchars($drama->title) ?>">
                      <?php else: ?>
                        <div class="placeholder-image">
                          <span class="material-symbols-rounded">movie</span>
                        </div>
                      <?php endif; ?>
                      <div class="category-badge"><?= htmlspecialchars($drama->category_name ?? 'Uncategorized') ?></div>
                    </div>

                    <div class="drama-content">
                      <h3 class="drama-title"><?= htmlspecialchars($drama->title) ?></h3>
                      <p class="drama-description"><?= htmlspecialchars(substr($drama->description ?? '', 0, 100)) ?>...</p>

                      <div class="drama-info">
                        <div class="info-item">
                          <span class="material-symbols-rounded">calendar_today</span>
                          <span><?= $drama->event_date ? date('M d, Y', strtotime($drama->event_date)) : 'TBA' ?></span>
                        </div>
                        <div class="info-item">
                          <span class="material-symbols-rounded">schedule</span>
                          <span><?= htmlspecialchars($drama->event_time ?? 'TBA') ?></span>
                        </div>
                        <div class="info-item">
                          <span class="material-symbols-rounded">location_on</span>
                          <span><?= htmlspecialchars($drama->venue ?? 'TBA') ?></span>
                        </div>
                      </div>

                      <div class="drama-footer">
                        <div class="price">
                          <span class="material-symbols-rounded">sell</span>
                          <span>LKR <?= number_format($drama->ticket_price ?? 0, 2) ?></span>
                        </div>
                        <button class="btn btn-secondary btn-book" data-drama-id="<?= $drama->id ?>">
                          <span class="material-symbols-rounded">confirmation_number</span>
                          <span>Book Now</span>
                        </button>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <span class="material-symbols-rounded">theaters</span>
                  </div>
                  <h3 class="empty-state-title">No Dramas Available</h3>
                  <p class="empty-state-description">Check back later for upcoming shows</p>
                </div>
              <?php endif; ?>
            </div>
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

  <script src="<?= ROOT ?>/assets/JS/audiencedashboard.js"></script>
</body>

</html>