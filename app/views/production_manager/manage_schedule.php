<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Schedule Management - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .calendar-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 10px;
        }

        .calendar-controls button {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            background: var(--brand);
            color: white;
            cursor: pointer;
            font-weight: 500;
        }

        .calendar-controls button:hover {
            background: var(--brand-strong);
        }

        .month-year {
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            text-align: center;
            flex: 1;
        }

        .view-toggle {
            display: flex;
            gap: 8px;
        }

        .view-toggle button {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            color: var(--ink);
            cursor: pointer;
            border-radius: 4px;
            font-size: 12px;
        }

        .view-toggle button.active {
            background: var(--brand);
            color: white;
            border-color: var(--brand);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        .calendar-header {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 10px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: 700;
            color: var(--brand);
            padding: 10px;
            font-size: 12px;
        }

        .calendar-date {
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 10px;
            min-height: 100px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .calendar-date:hover {
            background: #f0f0f0;
            border-color: var(--brand);
        }

        .calendar-date.other-month {
            background: #fafafa;
            color: #999;
        }

        .calendar-date.today {
            background: #fff3cd;
            border: 2px solid var(--brand);
        }

        .date-number {
            font-weight: 700;
            margin-bottom: 4px;
            color: var(--ink);
        }

        .calendar-date.other-month .date-number {
            color: #ccc;
        }

        .date-events {
            font-size: 10px;
            line-height: 1.2;
        }

        .event-indicator {
            display: inline-block;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            margin-right: 2px;
        }

        .event-item {
            background: var(--brand);
            color: white;
            padding: 2px 4px;
            border-radius: 2px;
            font-size: 9px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .event-item.awaiting {
            background: #ffc107;
            color: #333;
        }

        .event-item.accepted {
            background: #28a745;
            color: white;
        }

        .event-item.paid {
            background: #007bff;
            color: white;
        }

        .event-item.theater {
            background: #6f42c1;
            color: white;
        }

        .timeline-view {
            display: none;
        }

        .timeline-view.active {
            display: block;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 16px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--brand);
        }

        .timeline-date {
            font-weight: 700;
            color: var(--brand);
            min-width: 80px;
            margin-right: 16px;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-title {
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 4px;
        }

        .timeline-status {
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-right: 8px;
        }

        .status-badge.awaiting {
            background: #ffc107;
            color: #333;
        }

        .status-badge.accepted {
            background: #28a745;
            color: white;
        }

        .status-badge.paid {
            background: #007bff;
            color: white;
        }

        .status-badge.completed {
            background: #6c757d;
            color: white;
        }

        .status-badge.theater {
            background: #6f42c1;
            color: white;
        }

        .filter-section {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 14px;
            border: 1px solid #ddd;
            background: white;
            color: var(--ink);
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s ease;
        }

        .filter-btn.active {
            background: var(--brand);
            color: white;
            border-color: var(--brand);
        }

        .filter-btn:hover {
            border-color: var(--brand);
        }

        .event-detail-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .event-detail-modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 28px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 12px;
        }

        .modal-header h3 {
            margin: 0;
            color: var(--ink);
        }

        .modal-close-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #999;
        }

        .modal-close-btn:hover {
            color: var(--ink);
        }

        .modal-section {
            margin-bottom: 16px;
        }

        .modal-section-title {
            font-weight: 700;
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .modal-section-content {
            color: var(--ink);
            line-height: 1.6;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .modal-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .btn-primary-modal {
            background: var(--brand);
            color: white;
        }

        .btn-primary-modal:hover {
            background: var(--brand-strong);
        }

        .btn-secondary-modal {
            background: #f0f0f0;
            color: var(--ink);
        }

        .btn-secondary-modal:hover {
            background: #e0e0e0;
        }

        .legend {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
        }

        .legend-dot {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
                <a href="dashboard.php?drama_id=1">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage_services.php?drama_id=1">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Services</span>
                </a>
            </li>
            <li>
                <a href="manage_budget.php?drama_id=1">
                    <i class="fas fa-chart-bar"></i>
                    <span>Budget Management</span>
                </a>
            </li>
            <li>
                <a href="book_theater.php?drama_id=1">
                    <i class="fas fa-theater-masks"></i>
                    <span>Theater Bookings</span>
                </a>
            </li>
            <li class="active">
                <a href="manage_schedule.php?drama_id=1">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Service Schedule</span>
                </a>
            </li>
            <li>
                <a href="../artist/profile.php">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
                <a href="../../public/index.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="dashboard.php?drama_id=1" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Sinhabahu</span>
                <h2>Service Schedule Management</h2>
            </div>
            <div class="user--info">
                <img src="../../assets/images/default-avatar.jpg" alt="Avatar">
                <span class="role-badge">
                    <i class="fas fa-user-tie"></i>
                    Production Manager
                </span>
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
                <div class="legend-item">
                    <div class="legend-dot" style="background: #6f42c1;"></div>
                    <span>Theater Booking</span>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <button class="filter-btn active" onclick="filterByStatus('')">All</button>
                <button class="filter-btn" onclick="filterByStatus('awaiting')">
                    <i class="fas fa-hourglass-half"></i>
                    Awaiting Response
                </button>
                <button class="filter-btn" onclick="filterByStatus('accepted')">
                    <i class="fas fa-check-circle"></i>
                    Accepted
                </button>
                <button class="filter-btn" onclick="filterByStatus('paid')">
                    <i class="fas fa-credit-card"></i>
                    Paid
                </button>
                <button class="filter-btn" onclick="filterByStatus('theater')">
                    <i class="fas fa-theater-masks"></i>
                    Theater Bookings
                </button>
            </div>

            <!-- Calendar Controls -->
            <div class="calendar-controls">
                <button onclick="previousMonth()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <div class="month-year" id="currentMonth">December 2025</div>
                <button onclick="nextMonth()">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="view-toggle">
                    <button class="active" onclick="switchView('calendar')">
                        <i class="fas fa-calendar-grid-3"></i>
                        Calendar
                    </button>
                    <button onclick="switchView('timeline')">
                        <i class="fas fa-list"></i>
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

    <script src="/Rangamadala/public/assets/JS/manage-schedule.js"></script>
</body>
</html>
