<?php
$dashboardProfileImage = $data['dashboard_profile_image'] ?? (ROOT . '/uploads/profile_images/default_user.png');
?>
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

  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

  <!-- Material Design Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  
  <!-- Font Awesome -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<!-- Dashboard CSS -->
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css" />
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/toast.css" />

  <style>
    .dashboard-header-actions {
      gap: 16px;
    }

    .audience-role-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 14px;
      border-radius: 999px;
      background: linear-gradient(135deg, #be9227, #a67d1e);
      color: #fff;
      border: 1px solid rgba(145, 108, 24, 0.35);
      box-shadow: 0 4px 10px rgba(166, 125, 30, 0.2);
      font-size: 12px;
      font-weight: 700;
      letter-spacing: 0.4px;
      text-transform: uppercase;
      line-height: 1;
    }

    .audience-role-badge i {
      font-size: 12px;
    }

    .dashboard-header-actions .user-menu-trigger {
      width: 52px;
      height: 52px;
      border-radius: 50%;
      border: 3px solid #b88b22;
      box-shadow: 0 0 0 2px rgba(224, 191, 105, 0.45);
      overflow: hidden;
    }

    .dashboard-header-actions .user-avatar-small {
      width: 100%;
      height: 100%;
      border-radius: 50%;
      background: transparent;
    }

    .dashboard-header-actions .user-avatar-small img {
      border-radius: 50%;
    }

    /* Audience overview stats cards: match artist dashboard design */
    #overview .stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 24px;
    }

    #overview .stat-card {
      background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%);
      border: 1px solid #f0dfb4;
      border-radius: 18px;
      padding: 22px;
      text-align: left;
      color: #4a3a14;
      box-shadow: 0 4px 12px rgba(186, 142, 35, 0.12);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
      overflow: hidden;
    }

    #overview .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 22px rgba(186, 142, 35, 0.2);
    }

    #overview .stat-card .stat-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    #overview .stat-card .stat-card-title {
      font-size: 14px;
      font-weight: 600;
      color: #7a6121;
    }

    #overview .stat-card .stat-card-icon {
      width: 40px;
      height: 40px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
    }

    #overview .stat-card .stat-card-icon.primary {
      background: rgba(186, 142, 35, 0.14);
      color: var(--brand);
    }

    #overview .stat-card .stat-card-icon.info {
      background: rgba(186, 142, 35, 0.14);
      color: var(--brand);
    }

    #overview .stat-card .stat-card-icon.success {
      background: rgba(186, 142, 35, 0.14);
      color: var(--brand);
    }

    #overview .stat-card .stat-card-icon.warning {
      background: rgba(186, 142, 35, 0.14);
      color: var(--brand);
    }

    #overview .stat-card .stat-card-value {
      font-size: 34px;
      font-weight: 700;
      line-height: 1;
      margin: 0;
      color: #5a4415;
    }

    #overview .stat-card-change.positive,
    #overview .stat-card-change.negative {
      color: #8a6a1f;
    }

    /* Browse dramas layout fixes */
    #browse .browse-dramas-container {
      width: 100%;
    }

    #browse .browse-header {
      margin-bottom: 18px;
    }

    #browse .browse-title {
      font-size: 34px;
      font-weight: 700;
      color: #3d2e12;
      margin-bottom: 4px;
    }

    #browse .browse-subtitle {
      color: #7d6a3b;
    }

    #browse .browse-search-filter {
      display: flex;
      gap: 12px;
      margin-bottom: 14px;
      flex-wrap: wrap;
    }

    #browse .search-box,
    #browse .filter-box {
      display: flex;
      align-items: center;
      gap: 8px;
      background: #fff;
      border: 1px solid #ead7a4;
      border-radius: 12px;
      padding: 10px 12px;
      min-height: 46px;
      box-shadow: 0 3px 12px rgba(186, 142, 35, 0.08);
    }

    #browse .search-box {
      flex: 1;
      min-width: 260px;
    }

    #browse .filter-box {
      min-width: 220px;
    }

    #browse .search-box input,
    #browse .filter-box select {
      border: none;
      outline: none;
      width: 100%;
      font-size: 15px;
      background: transparent;
      color: #3f3320;
    }

    #browse .browse-results-info {
      margin: 8px 0 16px;
      color: #6f5a2e;
      font-size: 18px;
    }

    #browse .dramas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }

    #classes .classes-subtabs {
      display: flex;
      gap: 0;
      margin: 10px 12px 18px;
      flex-wrap: wrap;
      background: linear-gradient(180deg, #f6f5f2 0%, #efede8 100%);
      border: 1px solid #ddd9cf;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 14px rgba(0, 0, 0, 0.05);
    }

    #classes .classes-subtab-btn {
      border: none;
      background: transparent;
      color: #6c7484;
      padding: 14px 18px;
      min-height: 56px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      line-height: 1;
      border-bottom: 3px solid transparent;
      border-right: 1px solid #e2dfd7;
    }

    #classes .classes-subtab-btn:last-child {
      border-right: none;
    }

    #classes .classes-subtab-btn i {
      font-size: 16px;
      line-height: 1;
    }

    #classes .classes-subtab-btn:hover {
      background: rgba(186, 142, 35, 0.1);
      color: #8c6c20;
    }

    #classes .classes-subtab-btn.active {
      background: linear-gradient(180deg, #f5efe1 0%, #efe6d2 100%);
      color: #b48218;
      border-bottom-color: #b48218;
      box-shadow: inset 0 -1px 0 rgba(180, 130, 24, 0.12);
    }

    #classes .classes-subtab-panel {
      display: none;
      padding: 0 12px 12px;
    }

    #classes .classes-subtab-panel.active {
      display: block;
    }

    #classes .dramas-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 18px;
    }

    #classes .drama-card {
      background: linear-gradient(180deg, #fffefb 0%, #fff8e9 100%);
      border: 1px solid #e8cf97;
      border-radius: 14px;
      overflow: hidden;
      box-shadow: 0 6px 16px rgba(186, 142, 35, 0.12);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    #classes .drama-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px rgba(186, 142, 35, 0.2);
    }

    #classes .drama-content {
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      flex: 1;
    }

    #classes .drama-title {
      margin: 0;
      font-size: 24px;
      font-weight: 700;
      color: #3f2f12;
      line-height: 1.3;
    }

    #classes .drama-description {
      margin: 0;
      color: #6f5a2e;
      line-height: 1.5;
      min-height: 44px;
    }

    #classes .drama-info {
      display: grid;
      gap: 7px;
      padding-top: 10px;
      border-top: 1px dashed #ead8a9;
    }

    #classes .info-item {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #5f4b23;
      font-size: 14px;
    }

    #classes .info-item .material-symbols-rounded {
      color: #a67d1e;
      font-size: 18px;
    }

    #classes .drama-footer {
      margin-top: auto;
      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      align-items: center;
      padding-top: 12px;
      border-top: 1px solid #ecd9ad;
    }

    #classes .drama-footer .status-badge {
      width: 100%;
      text-align: center;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    #classes .btn-book {
      width: 100%;
      justify-content: center;
      align-items: center;
      border-radius: 10px;
      font-weight: 700;
      height: 44px;
      min-height: 44px;
      padding: 8px 12px;
      border: 1px solid transparent;
      background: linear-gradient(135deg, #ba8e23, #9b761d);
      color: #fff;
      box-shadow: 0 8px 16px rgba(186, 142, 35, 0.28);
    }

    #classes .btn-book:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 18px rgba(186, 142, 35, 0.35);
    }

    @media (max-width: 768px) {
      #classes .classes-subtabs {
        display: grid;
        grid-template-columns: 1fr 1fr;
      }

      #classes .classes-subtab-btn {
        width: 100%;
        border-right: none;
      }

      #classes .dramas-grid {
        grid-template-columns: 1fr;
      }
    }

    #browse .drama-card {
      background: linear-gradient(180deg, #fffefb 0%, #fff7e7 100%);
      border: 1px solid #efdcb0;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 20px rgba(186, 142, 35, 0.14);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
      display: flex;
      flex-direction: column;
    }

    #browse .drama-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 14px 26px rgba(186, 142, 35, 0.22);
    }

    #browse .drama-image {
      position: relative;
      width: 100%;
      aspect-ratio: 16 / 10;
      min-height: 210px;
      overflow: hidden;
      border-bottom: 1px solid #f1dfb7;
      background: #f6ead0;
    }

    #browse .drama-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      object-position: center;
      display: block;
      transition: transform 0.3s ease;
    }

    #browse .drama-card:hover .drama-image img {
      transform: scale(1.05);
    }

    #browse .placeholder-image {
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #ead7a8, #d8b96f);
    }

    #browse .placeholder-image .material-symbols-rounded {
      font-size: 64px;
      color: #6d5320;
    }

    #browse .category-badge {
      position: absolute;
      right: 10px;
      top: 10px;
      padding: 5px 10px;
      border-radius: 999px;
      background: rgba(58, 43, 13, 0.86);
      color: #f5e5ba;
      font-size: 12px;
      font-weight: 600;
    }

    #browse .drama-content {
      padding: 16px;
      display: flex;
      flex-direction: column;
      gap: 10px;
      flex: 1;
    }

    #browse .drama-title {
      margin: 0;
      font-size: 22px;
      font-weight: 700;
      color: #3f2f12;
      line-height: 1.3;
    }

    #browse .drama-description {
      margin: 0;
      color: #6f5a2e;
      min-height: 44px;
      line-height: 1.5;
    }

    #browse .drama-info {
      display: grid;
      gap: 7px;
      padding-top: 10px;
      border-top: 1px dashed #ead8a9;
    }

    #browse .info-item {
      display: flex;
      align-items: center;
      gap: 8px;
      color: #5f4b23;
      font-size: 14px;
    }

    #browse .info-item .material-symbols-rounded {
      color: #a67d1e;
      font-size: 18px;
    }

    #browse .drama-footer {
      margin-top: auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      align-items: center;
      padding-top: 12px;
      border-top: 1px solid #efdfb8;
    }

    #browse .drama-footer .price {
      grid-column: 1 / -1;
      display: flex;
      align-items: center;
      gap: 8px;
      color: #8f6717;
      font-weight: 700;
      font-size: 18px;
    }

    #browse .drama-footer .form-hint {
      grid-column: 1 / -1;
      margin: 0;
      width: auto !important;
      color: #6f5a2e !important;
    }

    #browse .btn-book,
    #browse .btn-rate {
      width: 100%;
      justify-content: center;
      align-items: center;
      border-radius: 10px;
      font-weight: 600;
      height: 44px;
      min-height: 44px;
      padding: 8px 12px;
      border: 1px solid transparent;
    }

    #browse .btn-book {
      background: linear-gradient(135deg, #ba8e23, #9b761d);
      color: #fff;
    }

    #browse .btn-book:hover {
      box-shadow: 0 8px 16px rgba(186, 142, 35, 0.3);
      transform: translateY(-2px);
    }

    #browse .btn-rate {
      background: #fff;
      color: #8f6717;
      border-color: #e1c37f;
    }

    #browse .btn-rate:hover {
      background: #fff7e2;
      transform: translateY(-1px);
    }

    #my-showings .btn-pay-now {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 112px;
      border-radius: 10px;
      border: 1px solid #9f781a;
      background: linear-gradient(135deg, #c79a2b 0%, #9a7318 100%);
      color: #fff;
      font-weight: 700;
      letter-spacing: 0.2px;
      box-shadow: 0 6px 14px rgba(154, 115, 24, 0.28);
      transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    #my-showings .btn-pay-now:hover {
      color: #fff;
      transform: translateY(-1px);
      box-shadow: 0 10px 20px rgba(154, 115, 24, 0.34);
      filter: saturate(1.05);
    }

    #my-showings .btn-pay-now:focus-visible {
      outline: 3px solid rgba(199, 154, 43, 0.3);
      outline-offset: 2px;
    }

    #payments .payments-subtabs {
      display: flex;
      gap: 0;
      margin: 10px 12px 18px;
      flex-wrap: wrap;
      background: linear-gradient(180deg, #f6f5f2 0%, #efede8 100%);
      border: 1px solid #ddd9cf;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 14px rgba(0, 0, 0, 0.05);
    }

    #payments .payments-subtab-btn {
      border: none;
      background: transparent;
      color: #6c7484;
      padding: 14px 18px;
      min-height: 56px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      line-height: 1;
      border-bottom: 3px solid transparent;
      border-right: 1px solid #e2dfd7;
    }

    #payments .payments-subtab-btn:last-child {
      border-right: none;
    }

    #payments .payments-subtab-btn:hover {
      background: rgba(186, 142, 35, 0.1);
      color: #8c6c20;
    }

    #payments .payments-subtab-btn.active {
      background: linear-gradient(180deg, #f5efe1 0%, #efe6d2 100%);
      color: #b48218;
      border-bottom-color: #b48218;
      box-shadow: inset 0 -1px 0 rgba(180, 130, 24, 0.12);
    }

    #payments .payments-subtab-panel {
      display: none;
      padding: 0 12px 12px;
    }

    #payments .payments-subtab-panel.active {
      display: block;
    }

    #payments .payment-amount {
      font-weight: 700;
      color: #6e5214;
    }

    @media (max-width: 768px) {
      #payments .payments-subtabs {
        display: grid;
        grid-template-columns: 1fr;
      }

      #payments .payments-subtab-btn {
        width: 100%;
        border-right: none;
      }
    }

    @media (max-width: 768px) {
      #browse .dramas-grid {
        grid-template-columns: 1fr;
      }

      #browse .drama-image {
        min-height: 190px;
      }

      #browse .drama-footer {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
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

    <!-- Sidebar -->
    <aside class="dashboard-sidebar" id="dashboardSidebar">
      <div class="dashboard-brand">
        <div class="logo">
          <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" />
        </div>
        <span>Audience</span>
      </div>

      <nav class="dashboard-nav">
        <div class="dashboard-nav-section">

          <!-- Dashboard -->
          <a href="#" class="dashboard-nav-item active" data-view="overview">
            <i class='bx bxs-home nav-icon'></i>
            <span class="nav-label">Dashboard</span>
          </a>

          <!-- Browse Dramas / Events -->
          <a href="#" class="dashboard-nav-item" data-view="browse">
            <i class='bx bx-camera-movie nav-icon'></i>
            <span class="nav-label">Browse Dramas</span>
          </a>

          <!-- My Showings -->
          <a href="#" class="dashboard-nav-item" data-view="my-showings">
            <i class='bx bx-calendar nav-icon'></i>
            <span class="nav-label">My Showings</span>
          </a>

          <!-- Payment History -->
          <a href="#" class="dashboard-nav-item" data-view="classes">
            <i class='bx bx-book-reader nav-icon'></i>
            <span class="nav-label">Classes</span>
          </a>

          <!-- Payment History -->
          <a href="#" class="dashboard-nav-item" data-view="payments">
            <i class='bx bx-receipt nav-icon'></i>
            <span class="nav-label">Payment History</span>
          </a>

        </div>
      </nav>

      <div class="sidebar-footer">
        <a href="<?= ROOT ?>/Logout" class="btn btn-secondary sidebar-back-button">
          <i class='bx bx-log-out nav-icon'></i>
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
          <h1 class="dashboard-header-title" id="dashboardTitle">Dashboard</h1>
        </div>

        <div class="dashboard-header-actions">
          <div class="audience-role-badge">
            <i class='bx bxs-star'></i>
            <span>Audience</span>
          </div>
          <div class="user-menu" id="userMenu">
            <div class="user-menu-trigger" id="user-menu-trigger">
              <div class="user-avatar-small">
                <img
                  src="<?= htmlspecialchars($dashboardProfileImage) ?>"
                  onerror="this.src='<?= ROOT ?>/uploads/profile_images/default_user.png'"
                  alt="User Avatar" />
              </div>
            </div>

            <div class="user-menu-dropdown">
              <a href="<?= ROOT ?>/AudienceProfile" class="user-menu-item">
                <span class="icon material-symbols-rounded">person</span>
                <span>Profile</span>
              </a>

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

        <!-- Dashboard -->
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
                  <i class='bx bx-calendar'></i>
                </div>
              </div>
              <div class="stat-card-value">2</div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Watched Dramas</div>
                <div class="stat-card-icon success">
                  <i class='bx bx-check-double'></i>
                </div>
              </div>
              <div class="stat-card-value">6</div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Notifications</div>
                <div class="stat-card-icon warning">
                  <i class='bx bx-bell'></i>
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
                      <?php
                        $publicDescription = trim((string)($drama->description ?? ''));
                        $shortDescription = mb_strlen($publicDescription) > 130
                          ? mb_substr($publicDescription, 0, 130) . '...'
                          : $publicDescription;
                      ?>
                      <p class="drama-description"><?= htmlspecialchars($shortDescription !== '' ? $shortDescription : 'No public description available yet.') ?></p>

                      <div class="drama-info" style="margin-bottom: 8px;">
                        <div class="info-item">
                          <span class="material-symbols-rounded">person</span>
                          <span>Producer: <?= htmlspecialchars($drama->owner_name ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item">
                          <span class="material-symbols-rounded">call</span>
                          <span>Producer Contact: <?= htmlspecialchars(!empty($drama->producer_phone) ? $drama->producer_phone : 'Not available') ?></span>
                        </div>
                        <div class="info-item">
                          <span class="material-symbols-rounded">category</span>
                          <span>Category: <?= htmlspecialchars($drama->category_name ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item">
                          <span class="material-symbols-rounded">language</span>
                          <span>Language: <?= htmlspecialchars($drama->language ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item">
                          <span class="material-symbols-rounded">timer</span>
                          <span>Duration: <?= !empty($drama->duration_minutes) ? (int)$drama->duration_minutes . ' min' : 'N/A' ?></span>
                        </div>
                      </div>

                      <div class="drama-footer">
                        <div class="form-hint" style="margin: 6px 0 0; width: 100%; color: #666;">
                          Showing prices: <?= !empty($drama->showing_prices) ? htmlspecialchars($drama->showing_prices) : 'Not specified' ?>
                        </div>
                        <a class="btn btn-secondary btn-book" href="<?= ROOT ?>/BrowseDramas/bookShowings/<?= $drama->id ?>" data-drama-id="<?= $drama->id ?>">
                          <span class="material-symbols-rounded">visibility</span>
                          <span>Book Showings</span>
                        </a>
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

        <!-- Payment History -->
        <div class="dashboard-view" id="classes">
          <div class="dashboard-table-container">
            <div class="dashboard-table-header" style="margin-bottom: 18px;">
              <h3 class="dashboard-table-title">Drama Classes</h3>
            </div>

            <div class="classes-subtabs" role="tablist" aria-label="Classes tabs">
              <button type="button" class="classes-subtab-btn active" data-classes-tab="available" role="tab" aria-selected="true">
                <i class="bx bx-globe"></i> Available Classes (<?= count($data['classes'] ?? []) ?>)
              </button>
              <button type="button" class="classes-subtab-btn" data-classes-tab="my" role="tab" aria-selected="false">
                <i class="bx bx-user-graduate"></i> My Classes (<?= count($data['my_classes'] ?? []) ?>)
              </button>
            </div>

            <div class="classes-subtab-panel active" data-classes-panel="available" role="tabpanel">
              <h4 style="margin-bottom: 12px; color: #4f3a12;">Available Classes</h4>
              <?php if (!empty($data['classes'])): ?>
                <div class="dramas-grid">
                  <?php foreach ($data['classes'] as $class): ?>
                    <?php
                      $classTime = 'TBA';
                      if (!empty($class->start_time)) {
                        $startTs = strtotime($class->start_time);
                        if ($startTs !== false) {
                          $classTime = date('g:i A', $startTs);
                          $duration = (int)($class->duration_minutes ?? 0);
                          if ($duration > 0) {
                            $endTs = strtotime('+' . $duration . ' minutes', $startTs);
                            if ($endTs !== false) {
                              $classTime .= ' - ' . date('g:i A', $endTs);
                            }
                          }
                        }
                      }
                    ?>
                    <div class="drama-card">
                      <div class="drama-content">
                        <h3 class="drama-title"><?= htmlspecialchars($class->title ?? 'Class') ?></h3>
                        <p class="drama-description"><?= htmlspecialchars(substr((string)($class->description ?? ''), 0, 120)) ?><?= !empty($class->description) && strlen((string)$class->description) > 120 ? '...' : '' ?></p>
                        <div class="drama-info">
                          <div class="info-item">
                            <span class="material-symbols-rounded">person</span>
                            <span>By <?= htmlspecialchars($class->creator_name ?? 'Artist') ?></span>
                          </div>
                          <div class="info-item">
                            <span class="material-symbols-rounded">calendar_today</span>
                            <span><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                          </div>
                          <div class="info-item">
                            <span class="material-symbols-rounded">schedule</span>
                            <span><?= htmlspecialchars($classTime) ?></span>
                          </div>
                          <div class="info-item">
                            <span class="material-symbols-rounded">group</span>
                            <span><?= (int)($class->enrolled_count ?? 0) ?> / <?= (int)($class->capacity ?? 0) ?> enrolled</span>
                          </div>
                          <div class="info-item">
                            <span class="material-symbols-rounded">payments</span>
                            <span>LKR <?= number_format((float)($class->fee ?? 0), 2) ?></span>
                          </div>
                          <?php if (!empty($class->venue)): ?>
                            <div class="info-item">
                              <span class="material-symbols-rounded">location_on</span>
                              <span><?= htmlspecialchars($class->venue) ?></span>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="drama-footer">
                          <form method="POST" action="<?= ROOT ?>/audiencedashboard/start_class_payment" class="class-enroll-payment-form" style="width: 100%;">
                            <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">
                            <button type="submit" class="btn btn-book" style="width: 100%; display: inline-flex; justify-content: center; align-items: center;">
                              <span class="material-symbols-rounded">school</span>
                              <span>Enroll Now</span>
                            </button>
                          </form>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <span class="material-symbols-rounded">school</span>
                  </div>
                  <h3 class="empty-state-title">No Classes Available</h3>
                  <p class="empty-state-description">Check back soon for new classes from artists.</p>
                </div>
              <?php endif; ?>
            </div>

            <div class="classes-subtab-panel" data-classes-panel="my" role="tabpanel">
              <h4 style="margin-bottom: 12px; color: #4f3a12;">My Enrolled Classes</h4>
              <?php if (!empty($data['my_classes'])): ?>
                <div class="dramas-grid">
                  <?php foreach ($data['my_classes'] as $class): ?>
                    <?php
                      $classTime = 'TBA';
                      if (!empty($class->start_time)) {
                        $startTs = strtotime($class->start_time);
                        if ($startTs !== false) {
                          $classTime = date('g:i A', $startTs);
                          $duration = (int)($class->duration_minutes ?? 0);
                          if ($duration > 0) {
                            $endTs = strtotime('+' . $duration . ' minutes', $startTs);
                            if ($endTs !== false) {
                              $classTime .= ' - ' . date('g:i A', $endTs);
                            }
                          }
                        }
                      }
                    ?>
                    <div class="drama-card">
                      <div class="drama-content">
                        <h3 class="drama-title"><?= htmlspecialchars($class->title ?? 'Class') ?></h3>
                        <p class="drama-description"><?= htmlspecialchars(substr((string)($class->description ?? ''), 0, 120)) ?><?= !empty($class->description) && strlen((string)$class->description) > 120 ? '...' : '' ?></p>
                        <div class="drama-info">
                          <div class="info-item">
                            <span class="material-symbols-rounded">person</span>
                            <span>By <?= htmlspecialchars($class->creator_name ?? 'Artist') ?></span>
                          </div>
                          <div class="info-item">
                            <span class="material-symbols-rounded">calendar_today</span>
                            <span><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                          </div>
                          <div class="info-item">
                            <span class="material-symbols-rounded">schedule</span>
                            <span><?= htmlspecialchars($classTime) ?></span>
                          </div>
                          <div class="info-item">
                            <span class="material-symbols-rounded">payments</span>
                            <span>LKR <?= number_format((float)($class->fee ?? 0), 2) ?></span>
                          </div>
                          <?php if (!empty($class->venue)): ?>
                            <div class="info-item">
                              <span class="material-symbols-rounded">location_on</span>
                              <span><?= htmlspecialchars($class->venue) ?></span>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="drama-footer">
                          <span class="status-badge success">Enrolled</span>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <span class="material-symbols-rounded">bookmarks</span>
                  </div>
                  <h3 class="empty-state-title">No Enrolled Classes Yet</h3>
                  <p class="empty-state-description">Join a class from the Available Classes tab to see it here.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Payment History -->
        <div class="dashboard-view" id="payments">
          <div class="dashboard-table-container">
            <div class="dashboard-table-header" style="margin-bottom: 18px;">
              <h3 class="dashboard-table-title">Payment History</h3>
            </div>

            <div class="payments-subtabs" role="tablist" aria-label="Payment history tabs">
              <button type="button" class="payments-subtab-btn active" data-payment-tab="showings" role="tab" aria-selected="true">
                <i class="bx bx-calendar"></i> Showings Payments (<?= count($data['showing_payments'] ?? []) ?>)
              </button>
              <button type="button" class="payments-subtab-btn" data-payment-tab="classes" role="tab" aria-selected="false">
                <i class="bx bx-book-reader"></i> Classes Payments (<?= count($data['class_payments'] ?? []) ?>)
              </button>
            </div>

            <div class="payments-subtab-panel active" data-payment-panel="showings" role="tabpanel">
              <h4 style="margin-bottom: 12px; color: #4f3a12;">Showing Payments</h4>
              <?php if (!empty($data['showing_payments'])): ?>
                <table class="dashboard-table">
                  <thead>
                    <tr>
                      <th>Drama</th>
                      <th>Order ID</th>
                      <th>Amount</th>
                      <th>Paid At</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data['showing_payments'] as $payment): ?>
                      <?php
                        $showingPriceText = trim((string)($payment->showing_prices ?? ''));
                        if ($showingPriceText === '' && !empty($payment->ticket_price)) {
                          $showingPriceText = 'LKR ' . number_format((float)$payment->ticket_price, 2);
                        }

                        $paidAtValue = !empty($payment->paid_at) ? date('M d, Y h:i A', strtotime($payment->paid_at)) : 'Pending verification';
                        $statusRaw = strtolower((string)($payment->booking_status ?? 'confirmed'));
                        $statusClass = in_array($statusRaw, ['confirmed', 'completed', 'watched', 'attended'], true) ? 'success' : 'warning';
                      ?>
                      <tr>
                        <td><?= htmlspecialchars($payment->title ?? 'Drama') ?></td>
                        <td><?= htmlspecialchars($payment->payhere_order_id ?? 'N/A') ?></td>
                        <td class="payment-amount"><?= htmlspecialchars($showingPriceText !== '' ? $showingPriceText : 'N/A') ?></td>
                        <td><?= htmlspecialchars($paidAtValue) ?></td>
                        <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($statusRaw)) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <span class="material-symbols-rounded">receipt_long</span>
                  </div>
                  <h3 class="empty-state-title">No Showing Payments Yet</h3>
                  <p class="empty-state-description">Payments for approved show requests will appear here.</p>
                </div>
              <?php endif; ?>
            </div>

            <div class="payments-subtab-panel" data-payment-panel="classes" role="tabpanel">
              <h4 style="margin-bottom: 12px; color: #4f3a12;">Class Payments</h4>
              <?php if (!empty($data['class_payments'])): ?>
                <table class="dashboard-table">
                  <thead>
                    <tr>
                      <th>Class</th>
                      <th>Order ID</th>
                      <th>Amount</th>
                      <th>Paid At</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($data['class_payments'] as $payment): ?>
                      <?php
                        $paidAtValue = !empty($payment->paid_at) ? date('M d, Y h:i A', strtotime($payment->paid_at)) : 'Pending verification';
                        $statusRaw = strtolower((string)($payment->status ?? 'completed'));
                        $statusClass = $statusRaw === 'completed' ? 'success' : 'warning';
                      ?>
                      <tr>
                        <td><?= htmlspecialchars($payment->class_title ?? 'Class') ?></td>
                        <td><?= htmlspecialchars($payment->order_id ?? 'N/A') ?></td>
                        <td class="payment-amount">LKR <?= number_format((float)($payment->amount ?? 0), 2) ?></td>
                        <td><?= htmlspecialchars($paidAtValue) ?></td>
                        <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($statusRaw)) ?></span></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <span class="material-symbols-rounded">receipt_long</span>
                  </div>
                  <h3 class="empty-state-title">No Class Payments Yet</h3>
                  <p class="empty-state-description">Completed class enrollment payments will appear here.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- My Showings -->
        <div class="dashboard-view" id="my-showings">
          <div class="dashboard-table-container">
            <div class="dashboard-table-header">
              <h3 class="dashboard-table-title">My Showings</h3>
            </div>

            <?php if (!empty($data['my_showings'])): ?>
              <table class="dashboard-table">
                <thead>
                  <tr>
                    <th>Drama</th>
                    <th>Show Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Showing Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data['my_showings'] as $booking): ?>
                    <?php
                      $statusRaw = strtolower((string)($booking->booking_status ?? 'pending'));
                      $requestDetails = [];
                      if (!empty($booking->request_details_json)) {
                        $decodedRequestDetails = json_decode((string)$booking->request_details_json, true);
                        if (is_array($decodedRequestDetails)) {
                          $requestDetails = $decodedRequestDetails;
                        }
                      }

                      $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
                      $requestedShowTime = trim((string)($requestDetails['show_time'] ?? ''));
                      $requestedVenue = trim((string)($requestDetails['request_venue'] ?? ''));

                      $displayShowDate = !empty($requestedShowDateRaw)
                        ? date('M d, Y', strtotime($requestedShowDateRaw))
                        : (!empty($booking->event_date) ? date('M d, Y', strtotime($booking->event_date)) : 'TBA');

                      $displayShowTime = $requestedShowTime !== ''
                        ? $requestedShowTime
                        : (!empty($booking->event_time) ? (string)$booking->event_time : 'TBA');

                      $displayVenue = $requestedVenue !== ''
                        ? $requestedVenue
                        : (string)($booking->venue ?? 'TBA');

                      $showingPriceText = trim((string)($booking->showing_prices ?? ''));

                      if (in_array($statusRaw, ['confirmed', 'completed', 'watched', 'attended', 'accepted'], true)) {
                        $statusClass = 'success';
                      } elseif ($statusRaw === 'rejected') {
                        $statusClass = 'danger';
                      } else {
                        $statusClass = 'warning';
                      }
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($booking->title ?? 'Drama') ?></td>
                      <td><?= htmlspecialchars($displayShowDate) ?></td>
                      <td><?= htmlspecialchars($displayShowTime) ?></td>
                      <td><?= htmlspecialchars($displayVenue) ?></td>
                      <td><?= htmlspecialchars($showingPriceText !== '' ? $showingPriceText : 'Not specified') ?></td>
                      <td>
                        <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($statusRaw)) ?></span>
                        <?php if ($statusRaw === 'rejected' && !empty($booking->rejection_reason)): ?>
                          <div style="margin-top: 6px; font-size: 12px; color: #8a1f1f;">
                            Reason: <?= htmlspecialchars($booking->rejection_reason) ?>
                          </div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a class="btn <?= $statusRaw === 'accepted' ? 'btn-pay-now' : 'btn-secondary' ?>" href="<?= ROOT ?>/BrowseDramas/bookShowings/<?= (int)$booking->drama_id ?>">
                          <?= $statusRaw === 'accepted' ? 'Pay Now' : 'View' ?>
                        </a>
                        <?php if ($statusRaw === 'accepted' && ($displayShowDate !== 'TBA' || $displayShowTime !== 'TBA')): ?>
                          <div style="margin-top: 6px; font-size: 12px; color: #6f5a2e;">
                            Requested: <?= htmlspecialchars($displayShowDate) ?><?= $displayShowTime !== 'TBA' ? ' | ' . htmlspecialchars($displayShowTime) : '' ?>
                          </div>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="empty-state">
                <div class="empty-state-icon">
                  <span class="material-symbols-rounded">event_busy</span>
                </div>
                <h3 class="empty-state-title">No Showings Yet</h3>
                <p class="empty-state-description">Book a drama from Browse Dramas to see your showings here.</p>
              </div>
            <?php endif; ?>
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
  <script>
    function initAudienceClassPayments() {
      const enrollForms = document.querySelectorAll('.class-enroll-payment-form');
      if (!enrollForms.length) {
        return;
      }

      enrollForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
          event.preventDefault();

          if (typeof payhere === 'undefined') {
            alert('PayHere is not available right now. Please refresh and try again.');
            return;
          }

          const submitBtn = form.querySelector('button[type="submit"]');
          const classIdInput = form.querySelector('input[name="class_id"]');
          if (!classIdInput || !classIdInput.value) {
            alert('Invalid class selected.');
            return;
          }

          if (submitBtn) {
            submitBtn.disabled = true;
          }

          fetch(form.getAttribute('action'), {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'class_id=' + encodeURIComponent(classIdInput.value)
          })
            .then(function (response) {
              return response.json();
            })
            .then(function (data) {
              if (!data.success) {
                alert(data.error || 'Unable to initialize class payment.');
                if (submitBtn) {
                  submitBtn.disabled = false;
                }
                return;
              }

              const payment = {
                sandbox: !!data.sandbox,
                merchant_id: data.merchant_id,
                return_url: data.return_url,
                cancel_url: data.cancel_url,
                notify_url: data.notify_url,
                order_id: data.order_id,
                items: data.title || 'Drama Class',
                amount: data.amount,
                currency: 'LKR',
                hash: data.hash,
                first_name: 'Audience',
                last_name: 'User',
                email: 'audience@example.com',
                phone: '0770000000',
                address: 'Sri Lanka',
                city: 'Colombo',
                country: 'Sri Lanka'
              };

              payhere.onCompleted = function () {
                window.location.href = data.return_url;
              };

              payhere.onDismissed = function () {
                if (submitBtn) {
                  submitBtn.disabled = false;
                }
              };

              payhere.onError = function (error) {
                alert('Payment error: ' + error);
                if (submitBtn) {
                  submitBtn.disabled = false;
                }
              };

              payhere.startPayment(payment);
            })
            .catch(function () {
              alert('Payment initialization failed. Please try again.');
              if (submitBtn) {
                submitBtn.disabled = false;
              }
            });
        });
      });
    }

    function initAudienceClassesTabs() {
      const classesView = document.getElementById('classes');
      if (!classesView) {
        return;
      }

      const buttons = classesView.querySelectorAll('.classes-subtab-btn');
      const panels = classesView.querySelectorAll('.classes-subtab-panel');

      if (!buttons.length || !panels.length) {
        return;
      }

            initAudienceClassPayments();
      buttons.forEach((button) => {
        button.addEventListener('click', function () {
          const target = button.getAttribute('data-classes-tab');

          buttons.forEach((btn) => {
            const isActive = btn === button;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
          });

          panels.forEach((panel) => {
            panel.classList.toggle('active', panel.getAttribute('data-classes-panel') === target);
          });
        });
      });
    }

    function initAudiencePaymentTabs() {
      const paymentsView = document.getElementById('payments');
      if (!paymentsView) {
        return;
      }

      const buttons = paymentsView.querySelectorAll('.payments-subtab-btn');
      const panels = paymentsView.querySelectorAll('.payments-subtab-panel');

      if (!buttons.length || !panels.length) {
        return;
      }

      buttons.forEach((button) => {
        button.addEventListener('click', function () {
          const target = button.getAttribute('data-payment-tab');

          buttons.forEach((btn) => {
            const isActive = btn === button;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
          });

          panels.forEach((panel) => {
            panel.classList.toggle('active', panel.getAttribute('data-payment-panel') === target);
          });
        });
      });
    }

    // Toast notification handler
    function closeToast() {
      const toast = document.getElementById('successToast');
      if (toast) {
        toast.style.animation = 'toastSlideOut 0.4s ease forwards';
        setTimeout(() => toast.remove(), 400);
      }
    }

    // Auto-hide toast after 4 seconds
    window.addEventListener('load', function() {
      initAudienceClassesTabs();
      initAudiencePaymentTabs();

      const toast = document.getElementById('successToast');
      if (toast) {
        setTimeout(() => {
          closeToast();
        }, 4000);
      }
    });
  </script>
</body>

</html>