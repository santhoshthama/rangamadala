<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Schedule Management - Rangamadala</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/production_manager/manage_schedule.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="director-dashboard-page">
    <?php $dramaId = isset($dramaId) ? (int)$dramaId : (int)($drama->id ?? 0); ?>
    <?php $currentPage = 'manage_schedule'; require __DIR__ . '/_partials/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main--content">


        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= isset($drama->drama_name) ? esc($drama->drama_name) : 'Drama' ?></span>
                <h2>Service Schedule Management</h2>
            </div>
            <div class="user--info">
                <?php
                    $pmProfileImageSrc = isset($profileImageSrc) && is_string($profileImageSrc) && $profileImageSrc !== ''
                        ? $profileImageSrc
                        : (ROOT . '/assets/images/default-avatar.jpg');
                    $pmRoleLabel = 'Production Manager';
                    $pmProfileUrl = ROOT . '/profile';
                    $pmLogoutUrl = ROOT . '/logout';
                    require __DIR__ . '/_partials/user_menu.php';
                ?>
            </div>
        </div>

        <!-- Content -->
        <div class="content" style="padding: 28px;">
            <!-- Legend -->
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-dot" style="background: #ffc107;"></div>
                    <span>Awaiting Response</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background: #28a745;"></div>
                    <span>Accepted</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background: #007bff;"></div>
                    <span>Paid</span>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <button class="filter-btn active" onclick="filterByStatus('', this)">All</button>
                <button class="filter-btn" onclick="filterByStatus('awaiting', this)">
                    <i class="bx bx-hourglass-half"></i>
                    Awaiting Response
                </button>
                <button class="filter-btn" onclick="filterByStatus('accepted', this)">
                    <i class="bx bx-check-circle"></i>
                    Accepted
                </button>
                <button class="filter-btn" onclick="filterByStatus('paid', this)">
                    <i class="bx bx-credit-card"></i>
                    Paid
                </button>
            </div>

            <!-- Calendar Controls -->
            <div class="calendar-controls">
                <button onclick="previousMonth()">
                    <i class="bx bx-chevron-left"></i>
                </button>
                <div class="month-year" id="currentMonth">December 2025</div>
                <button onclick="nextMonth()">
                    <i class="bx bx-chevron-right"></i>
                </button>
                <div class="view-toggle">
                    <button class="active" onclick="switchView('calendar', this)">
                        <i class="bx bx-calendar-grid-3"></i>
                        Calendar
                    </button>
                    <button onclick="switchView('timeline', this)">
                        <i class="bx bx-list"></i>
                        Timeline
                    </button>
                </div>
            </div>

            <!-- Calendar View -->
            <div id="calendarView" class="calendar-view active">
                <div class="calendar-header">
                    <div class="calendar-day-header">Sun</div>
                    <div class="calendar-day-header">Mon</div>
                    <div class="calendar-day-header">Tue</div>
                    <div class="calendar-day-header">Wed</div>
                    <div class="calendar-day-header">Thu</div>
                    <div class="calendar-day-header">Fri</div>
                    <div class="calendar-day-header">Sat</div>
                </div>
                <div class="calendar-grid" id="calendarGrid"></div>
            </div>

            <!-- Timeline View -->
            <div id="timelineView" class="timeline-view">
                <div id="timelineContent"></div>
            </div>
        </div>
    </main>

    <!-- Event Detail Modal -->
    <div id="eventModal" class="event-detail-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="eventTitle">Service Details</h3>
                <button class="modal-close-btn" onclick="closeEventModal()">×</button>
            </div>
            <div id="eventDetails"></div>
            <div class="modal-actions" id="modalActions"></div>
        </div>
    </div>

    <script>
        window.pmScheduleConfig = {
            root: <?= json_encode(ROOT) ?>,
            dramaId: <?= (int)$dramaId ?>
        };
        window.schedules = <?= isset($schedules) && is_array($schedules) ? json_encode($schedules) : '[]' ?>;
    </script>
    <script src="/Rangamadala/public/assets/JS/manage-schedule.js"></script>
    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
</body>
</html>
