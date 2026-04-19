<?php
$dashboardProfileImage = $data['dashboard_profile_image'] ?? (ROOT . '/uploads/profile_images/user_profile.png');

$allShowings = $data['my_showings'] ?? [];
$activeShowings = [];
$watchedShowings = [];
$todayYmd = date('Y-m-d');

foreach ($allShowings as $bookingItem) {
  $requestDetails = [];
  if (!empty($bookingItem->request_details_json)) {
    $decodedRequestDetails = json_decode((string)$bookingItem->request_details_json, true);
    if (is_array($decodedRequestDetails)) {
      $requestDetails = $decodedRequestDetails;
    }
  }

  $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
  $fallbackEventDateRaw = trim((string)($bookingItem->event_date ?? ''));
  $resolvedShowDateRaw = $requestedShowDateRaw !== '' ? $requestedShowDateRaw : $fallbackEventDateRaw;
  $resolvedShowDateYmd = '';
  if ($resolvedShowDateRaw !== '' && strtotime($resolvedShowDateRaw) !== false) {
    $resolvedShowDateYmd = date('Y-m-d', strtotime($resolvedShowDateRaw));
  }

  $isPastShowing = $resolvedShowDateYmd !== '' && $resolvedShowDateYmd < $todayYmd;
  $hasPaymentRecord = !empty($bookingItem->paid_at) || !empty($bookingItem->payhere_order_id);
  $isWatchedByPaymentAndDate = $hasPaymentRecord && $isPastShowing;

  if ($isWatchedByPaymentAndDate) {
    $watchedShowings[] = $bookingItem;
  } else {
    $activeShowings[] = $bookingItem;
  }
}
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

  <!-- Dashboard CSS -->
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/admindashboard.css" />
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/audiencedashboard-page.css" />
  <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/toast.css" />

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

          <a href="#" class="dashboard-nav-item" data-view="watched-dramas">
            <i class='bx bx-mask nav-icon'></i>
            <span class="nav-label">Bought Dramas</span>
          </a>

          <!-- Payment History -->
          <a href="#" class="dashboard-nav-item" data-view="payments">
            <i class='bx bx-receipt nav-icon'></i>
            <span class="nav-label">Payment History</span>
          </a>

        </div>
      </nav>

      <div class="sidebar-footer">

        </a>
      </div>
    </aside>

    <div class="dashboard-sidebar-overlay" id="dashboardSidebarOverlay"></div>

    <!-- MAIN CONTENT -->
    <main class="dashboard-main">

      <header class="dashboard-header">
        <div class="dashboard-header-content">
          <button class="dashboard-sidebar-toggle">
            <i class='bx bx-menu'></i>
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
                  onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'"
                  alt="User Avatar" />
              </div>
            </div>

            <div class="user-menu-dropdown">
              <a href="<?= ROOT ?>/AudienceProfile" class="user-menu-item">
                <i class='icon bx bx-user'></i>
                <span>Profile</span>
              </a>

              <a href="<?= ROOT ?>/Logout" class="user-menu-item">
                <i class='icon bx bx-log-out'></i>
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
          <?php
            $today = date('Y-m-d');
            $upcomingDramasCount = 0;
            $pendingShowRequestsCount = 0;

            foreach (($data['my_showings'] ?? []) as $booking) {
              $status = strtolower((string)($booking->booking_status ?? 'pending'));

              if ($status === 'pending') {
                $pendingShowRequestsCount++;
              }

              if (!in_array($status, ['accepted', 'confirmed', 'completed', 'watched', 'attended'], true)) {
                continue;
              }

              $requestDetails = [];
              if (!empty($booking->request_details_json)) {
                $decoded = json_decode((string)$booking->request_details_json, true);
                if (is_array($decoded)) {
                  $requestDetails = $decoded;
                }
              }

              $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
              $eventDateRaw = trim((string)($booking->event_date ?? ''));
              $checkDate = $requestedShowDateRaw !== '' ? $requestedShowDateRaw : $eventDateRaw;

              if ($checkDate !== '' && strtotime($checkDate) !== false && date('Y-m-d', strtotime($checkDate)) >= $today) {
                $upcomingDramasCount++;
              }
            }

            $availableClassesCount = count($data['classes'] ?? []);
            $paidBookingShowsCount = count($data['showing_payments'] ?? []);
          ?>
          <div class="stats-grid">
            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Upcoming Dramas</div>
                <div class="stat-card-icon primary">
                  <i class='bx bx-calendar-event'></i>
                </div>
              </div>
              <div class="stat-card-value"><?= (int)$upcomingDramasCount ?></div>
              <div class="stat-card-change positive">
             
              </div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Available Classes</div>
                <div class="stat-card-icon info">
                  <i class='bx bx-book-reader'></i>
                </div>
              </div>
              <div class="stat-card-value"><?= (int)$availableClassesCount ?></div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Booking Shows Paid</div>
                <div class="stat-card-icon success">
                  <i class='bx bx-credit-card'></i>
                </div>
              </div>
              <div class="stat-card-value"><?= (int)$paidBookingShowsCount ?></div>
            </div>

            <div class="stat-card">
              <div class="stat-card-header">
                <div class="stat-card-title">Pending Show Requests</div>
                <div class="stat-card-icon warning">
                  <i class='bx bx-time-five'></i>
                </div>
              </div>
              <div class="stat-card-value"><?= (int)$pendingShowRequestsCount ?></div>
            </div>
          </div>

          <div class="dashboard-table-container dashboard-bookings-section">
            <div class="dashboard-table-header">
              <h3 class="dashboard-table-title">Booking Status</h3>
            </div>

            <?php if (!empty($activeShowings)): ?>
              <div class="my-showings-filters">
                <div class="my-showings-filter-box">
                  <i class='bx bx-filter-alt'></i>
                  <select id="overviewShowingsStatusFilter" aria-label="Filter dashboard bookings by status">
                    <option value="all">All Bookings</option>
                    <option value="paid">Payment Done</option>
                    <option value="pending">Request Pending</option>
                    <option value="rejected">Rejected Requests</option>
                  </select>
                </div>
                <div class="my-showings-search-box">
                  <i class='bx bx-search'></i>
                  <input type="text" id="overviewShowingsSearchInput" placeholder="Search drama, venue, date or status" aria-label="Search dashboard bookings" />
                </div>
              </div>

              <table class="dashboard-table">
                <thead>
                  <tr>
                    <th>Drama</th>
                    <th>Show Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($activeShowings as $booking): ?>
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

                      if (in_array($statusRaw, ['confirmed', 'completed', 'watched', 'attended', 'accepted'], true)) {
                        $statusClass = 'success';
                      } elseif ($statusRaw === 'rejected') {
                        $statusClass = 'danger';
                      } else {
                        $statusClass = 'warning';
                      }

                      $paymentDone = in_array($statusRaw, ['confirmed', 'completed', 'watched', 'attended'], true);

                      $searchBlob = strtolower(trim(
                        ($booking->title ?? '') . ' ' .
                        $displayShowDate . ' ' .
                        $displayShowTime . ' ' .
                        $displayVenue . ' ' .
                        $statusRaw
                      ));
                    ?>
                    <tr class="overview-showings-row" data-status="<?= htmlspecialchars($statusRaw) ?>" data-payment="<?= $paymentDone ? 'paid' : 'unpaid' ?>" data-search="<?= htmlspecialchars($searchBlob) ?>">
                      <td><?= htmlspecialchars($booking->title ?? 'Drama') ?></td>
                      <td><?= htmlspecialchars($displayShowDate) ?></td>
                      <td><?= htmlspecialchars($displayShowTime) ?></td>
                      <td><?= htmlspecialchars($displayVenue) ?></td>
                      <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($statusRaw)) ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
              <div id="overviewShowingsNoResults" class="my-showings-no-results" style="display: none;">
                No bookings match your filter.
              </div>
            <?php else: ?>
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class='bx bx-calendar-x'></i>
                </div>
                <h3 class="empty-state-title">No Showings Yet</h3>
                <p class="empty-state-description">Book a drama from Browse Dramas to see your booking status here.</p>
              </div>
            <?php endif; ?>
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
                <i class='bx bx-search'></i>
                <input type="text" id="dramaSearch" placeholder="Search dramas..." />
              </div>
              <div class="filter-box">
                <i class='bx bx-filter-alt'></i>
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
                          <i class='bx bx-camera-movie'></i>
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
                          <i class='bx bx-user'></i>
                          <span>Producer: <?= htmlspecialchars($drama->owner_name ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item">
                          <i class='bx bx-phone'></i>
                          <span>Producer Contact: <?= htmlspecialchars(!empty($drama->producer_phone) ? $drama->producer_phone : 'Not available') ?></span>
                        </div>
                        <div class="info-item">
                          <i class='bx bx-category'></i>
                          <span>Category: <?= htmlspecialchars($drama->category_name ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item">
                          <i class='bx bx-world'></i>
                          <span>Language: <?= htmlspecialchars($drama->language ?? 'N/A') ?></span>
                        </div>
                        <div class="info-item">
                          <i class='bx bx-time-five'></i>
                          <span>Duration: <?= !empty($drama->duration_minutes) ? (int)$drama->duration_minutes . ' min' : 'N/A' ?></span>
                        </div>
                      </div>

                      <div class="drama-footer">
                        <div class="form-hint" style="margin: 6px 0 0; width: 100%; color: #666;">
                          Showing prices: <?= !empty($drama->showing_prices) ? htmlspecialchars($drama->showing_prices) : 'Not specified' ?>
                        </div>
                        <a class="btn btn-secondary btn-book" href="<?= ROOT ?>/BrowseDramas/bookShowings/<?= $drama->id ?>" data-drama-id="<?= $drama->id ?>">
                          <i class='bx bx-show'></i>
                          <span>Book Showings</span>
                        </a>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <i class='bx bx-theater'></i>
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
                      $levelRaw = strtolower(trim((string)($class->class_level ?? 'all_levels')));
                      $levelLabel = $levelRaw !== '' ? ucwords(str_replace('_', ' ', $levelRaw)) : 'All Levels';
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
                        <p class="class-level-text"><?= htmlspecialchars($levelLabel) ?></p>
                        <p class="drama-description"><?= htmlspecialchars(substr((string)($class->description ?? ''), 0, 120)) ?><?= !empty($class->description) && strlen((string)$class->description) > 120 ? '...' : '' ?></p>
                        <div class="drama-info">
                          <div class="info-item">
                            <i class='bx bx-user'></i>
                            <span>By <?= htmlspecialchars($class->creator_name ?? 'Artist') ?></span>
                          </div>
                          <div class="info-item">
                            <i class='bx bx-calendar'></i>
                            <span><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                          </div>
                          <div class="info-item">
                            <i class='bx bx-time-five'></i>
                            <span><?= htmlspecialchars($classTime) ?></span>
                          </div>
                          <div class="info-item">
                            <i class='bx bx-group'></i>
                            <span><?= (int)($class->enrolled_count ?? 0) ?> / <?= (int)($class->capacity ?? 0) ?> enrolled</span>
                          </div>
                          <div class="info-item">
                            <i class='bx bx-credit-card'></i>
                            <span>LKR <?= number_format((float)($class->fee ?? 0), 2) ?></span>
                          </div>
                          <?php if (!empty($class->venue)): ?>
                            <div class="info-item">
                              <i class='bx bx-map'></i>
                              <span><?= htmlspecialchars($class->venue) ?></span>
                            </div>
                          <?php endif; ?>
                        </div>
                        <div class="drama-footer">
                          <form method="POST" action="<?= ROOT ?>/audiencedashboard/start_class_payment" class="class-enroll-payment-form" style="width: 100%;">
                            <input type="hidden" name="class_id" value="<?= (int)$class->id ?>">
                            <button type="submit" class="btn btn-book" style="width: 100%; display: inline-flex; justify-content: center; align-items: center;">
                              <i class='bx bx-book-reader'></i>
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
                    <i class='bx bx-book-reader'></i>
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
                      $levelRaw = strtolower(trim((string)($class->class_level ?? 'all_levels')));
                      $levelLabel = $levelRaw !== '' ? ucwords(str_replace('_', ' ', $levelRaw)) : 'All Levels';
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
                        <p class="class-level-text"><?= htmlspecialchars($levelLabel) ?></p>
                        <p class="drama-description"><?= htmlspecialchars(substr((string)($class->description ?? ''), 0, 120)) ?><?= !empty($class->description) && strlen((string)$class->description) > 120 ? '...' : '' ?></p>
                        <div class="drama-info">
                          <div class="info-item">
                            <i class='bx bx-user'></i>
                            <span>By <?= htmlspecialchars($class->creator_name ?? 'Artist') ?></span>
                          </div>
                          <div class="info-item">
                            <i class='bx bx-calendar'></i>
                            <span><?= !empty($class->class_date) ? date('M d, Y', strtotime($class->class_date)) : 'TBA' ?></span>
                          </div>
                          <div class="info-item">
                            <i class='bx bx-time-five'></i>
                            <span><?= htmlspecialchars($classTime) ?></span>
                          </div>
                          <div class="info-item">
                            <i class='bx bx-credit-card'></i>
                            <span>LKR <?= number_format((float)($class->fee ?? 0), 2) ?></span>
                          </div>
                          <?php if (!empty($class->venue)): ?>
                            <div class="info-item">
                              <i class='bx bx-map'></i>
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
                    <i class='bx bx-bookmark'></i>
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
                      <th>Receipt</th>
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
                        <td>
                          <a href="<?= ROOT ?>/audiencedashboard/payment_receipt/showing/<?= (int)$payment->id ?>" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px;background:rgba(186,142,35,0.12);color:#8f6717;text-decoration:none;font-weight:600;font-size:13px;">
                            <i class="bx bx-download"></i>
                            Receipt
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <i class='bx bx-receipt'></i>
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
                      <th>Receipt</th>
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
                        <td>
                          <a href="<?= ROOT ?>/audiencedashboard/payment_receipt/class/<?= (int)$payment->id ?>" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;padding:8px 12px;border-radius:8px;background:rgba(186,142,35,0.12);color:#8f6717;text-decoration:none;font-weight:600;font-size:13px;">
                            <i class="bx bx-download"></i>
                            Receipt
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              <?php else: ?>
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <i class='bx bx-receipt'></i>
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

            <?php if (!empty($activeShowings)): ?>
              <div class="my-showings-filters">
                <div class="my-showings-filter-box">
                  <i class='bx bx-filter-alt'></i>
                  <select id="myShowingsStatusFilter" aria-label="Filter showings by status">
                    <option value="all">All Bookings</option>
                    <option value="paid">Payment Done</option>
                    <option value="pending">Request Pending</option>
                    <option value="rejected">Rejected Requests</option>
                  </select>
                </div>
                <div class="my-showings-search-box">
                  <i class='bx bx-search'></i>
                  <input type="text" id="myShowingsSearchInput" placeholder="Search drama, venue, date or status" aria-label="Search my showings" />
                </div>
              </div>

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
                  <?php foreach ($activeShowings as $booking): ?>
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

                      $paymentDone = in_array($statusRaw, ['confirmed', 'completed', 'watched', 'attended'], true);

                      $searchBlob = strtolower(trim(
                        ($booking->title ?? '') . ' ' .
                        $displayShowDate . ' ' .
                        $displayShowTime . ' ' .
                        $displayVenue . ' ' .
                        $statusRaw
                      ));
                    ?>
                    <tr class="my-showings-row" data-status="<?= htmlspecialchars($statusRaw) ?>" data-payment="<?= $paymentDone ? 'paid' : 'unpaid' ?>" data-search="<?= htmlspecialchars($searchBlob) ?>">
                      <td><?= htmlspecialchars($booking->title ?? 'Drama') ?></td>
                      <td><?= htmlspecialchars($displayShowDate) ?></td>
                      <td><?= htmlspecialchars($displayShowTime) ?></td>
                      <td><?= htmlspecialchars($displayVenue) ?></td>
                      <td><?= htmlspecialchars($showingPriceText !== '' ? $showingPriceText : 'Not specified') ?></td>
                      <td>
                        <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars(ucfirst($statusRaw)) ?></span>
                        <?php if ($statusRaw === 'rejected' && !empty($booking->rejection_reason)): ?>
                          <div style="margin-top: 6px; font-size: 12px; color: #a3202c;">
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
              <div id="myShowingsNoResults" class="my-showings-no-results" style="display: none;">
                No bookings match your filter.
              </div>
            <?php else: ?>
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class='bx bx-calendar-x'></i>
                </div>
                <h3 class="empty-state-title">No Showings Yet</h3>
                <p class="empty-state-description">Book a drama from Browse Dramas to see your showings here.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Watched Dramas -->
        <div class="dashboard-view" id="watched-dramas">
          <div class="dashboard-table-container">
            <div class="dashboard-table-header">
              <h3 class="dashboard-table-title">Bought Dramas</h3>
            </div>

            <?php if (!empty($watchedShowings)): ?>
              <table class="dashboard-table">
                <thead>
                  <tr>
                    <th>Drama</th>
                    <th>Show Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($watchedShowings as $booking): ?>
                    <?php
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
                    ?>
                    <tr>
                      <td><?= htmlspecialchars($booking->title ?? 'Drama') ?></td>
                      <td><?= htmlspecialchars($displayShowDate) ?></td>
                      <td><?= htmlspecialchars($displayShowTime) ?></td>
                      <td><?= htmlspecialchars($displayVenue) ?></td>
                      <td>
                        <div class="watched-actions">
                            <a class="btn btn-secondary" href="<?= ROOT ?>/BrowseDramas/watchedDetails/<?= (int)$booking->id ?>">View Details</a>
                          <a class="btn btn-pay-now" href="<?= ROOT ?>/BrowseDramas/rateReview/<?= (int)$booking->drama_id ?>">Rate &amp; Review</a>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php else: ?>
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class='bx bx-mask'></i>
                </div>
                <h3 class="empty-state-title">No Bought Dramas Yet</h3>
                <p class="empty-state-description">Only paid bookings with a passed show date will appear here with details and review options.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Settings -->
        <div class="dashboard-view" id="settings">
          <div class="empty-state">
            <div class="empty-state-icon">
              <i class='bx bx-cog'></i>
            </div>
            <h3 class="empty-state-title">Account Settings</h3>
            <p class="empty-state-description">Change your password, update profile, and manage preferences.</p>
          </div>
        </div>

      </div>
    </main>
  </div>

  <script src="<?= ROOT ?>/assets/JS/audience/audiencedashboard.js"></script>
  <script src="<?= ROOT ?>/assets/JS/audience/audiencedashboard-page.js"></script>
</body>

</html>
