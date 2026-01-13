<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">
            <h2>🎭</h2>
        </div>
        <ul class="menu">
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="dashboard.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="drama_details.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-film"></i>
                    <span>Drama Details</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="manage_roles.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-users"></i>
                    <span>Artist Roles</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="assign_managers.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-user-tie"></i>
                    <span>Production Manager</span>
                </a>
            </li>
            <li class="active">
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="schedule_management.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-calendar-alt"></i>
                    <span>Schedule</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>">
=======
                <a href="view_services_budget.php?drama_id=1">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-dollar-sign"></i>
                    <span>Services & Budget</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/artistdashboard">
=======
                <a href="../artist/profile.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Profile</span>
                </a>
            </li>
            <li>
<<<<<<< HEAD
                <a href="<?= ROOT ?>/logout">
=======
                <a href="../../public/index.php">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
<<<<<<< HEAD
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= isset($drama->id) ? $drama->id : $_GET['drama_id'] ?? 1 ?>" class="back-button">
=======
        <a href="dashboard.php?drama_id=1" class="back-button">
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Sinhabahu</span>
                <h2>Schedule Management</h2>
            </div>
            <div class="header-controls">
                <button class="btn btn-primary" onclick="openScheduleModal('rehearsal')">
                    <i class="fas fa-theater-masks"></i>
                    Schedule Rehearsal
                </button>
                <button class="btn btn-success" onclick="openScheduleModal('interview')">
                    <i class="fas fa-user-check"></i>
                    Schedule Interview
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="search-section">
            <div class="search-filters">
                <div class="filter-group">
                    <label for="filterDrama">Filter by Drama</label>
                    <select id="filterDrama" onchange="filterSchedules()">
                        <option value="">All Dramas</option>
                        <option value="1">Maname</option>
                        <option value="2">Sinhabahu</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterType">Filter by Type</label>
                    <select id="filterType" onchange="filterSchedules()">
                        <option value="">All Types</option>
                        <option value="rehearsal">Rehearsal</option>
                        <option value="interview">Interview</option>
                        <option value="meeting">Meeting</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filterMonth">Filter by Month</label>
                    <input type="month" id="filterMonth" onchange="filterSchedules()">
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" onclick="showScheduleTab('upcoming')">
                <i class="fas fa-calendar-day"></i>
                Upcoming Events
            </button>
            <button class="tab-button" onclick="showScheduleTab('past')">
                <i class="fas fa-history"></i>
                Past Events
            </button>
            <button class="tab-button" onclick="showScheduleTab('calendar')">
                <i class="fas fa-calendar"></i>
                Calendar View
            </button>
        </div>

        <!-- Tab Content: Upcoming Events -->
        <div id="upcomingTab" class="tab-content active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <!-- Upcoming Schedule Items -->
                        <div class="card-section">
                            <h3>
                                <span>This Week</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong><i class="fas fa-theater-masks"></i> Rehearsal - Maname</strong>
                                        <div class="request-info">
                                            📅 December 22, 2024 | ⏰ 10:00 AM - 2:00 PM | 📍 NCPA Hall
                                        </div>
                                        <div class="request-info">
                                            <strong>Participants:</strong> 12 artists confirmed
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewScheduleDetails(1)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editSchedule(1)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="cancelSchedule(1)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong><i class="fas fa-user-check"></i> Interview - Sinhabahu</strong>
                                        <div class="request-info">
                                            📅 December 23, 2024 | ⏰ 2:00 PM - 4:00 PM | 📍 Online (Zoom)
                                        </div>
                                        <div class="request-info">
                                            <strong>Candidates:</strong> 5 artists scheduled
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewScheduleDetails(2)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editSchedule(2)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="cancelSchedule(2)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong><i class="fas fa-theater-masks"></i> Rehearsal - Sinhabahu</strong>
                                        <div class="request-info">
                                            📅 December 25, 2024 | ⏰ 3:00 PM - 6:00 PM | 📍 Tower Hall Theatre
                                        </div>
                                        <div class="request-info">
                                            <strong>Participants:</strong> Pending confirmations
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge pending">Pending</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewScheduleDetails(3)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editSchedule(3)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-danger" style="font-size: 11px; padding: 6px 12px;" onclick="cancelSchedule(3)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <div class="card-section">
                            <h3>
                                <span>Next Week</span>
                            </h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong><i class="fas fa-users"></i> Production Meeting - Maname</strong>
                                        <div class="request-info">
                                            📅 December 28, 2024 | ⏰ 9:00 AM - 11:00 AM | 📍 Office
                                        </div>
                                        <div class="request-info">
                                            <strong>Attendees:</strong> Directors, Production Managers
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewScheduleDetails(4)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editSchedule(4)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong><i class="fas fa-theater-masks"></i> Final Dress Rehearsal - Maname</strong>
                                        <div class="request-info">
                                            📅 December 30, 2024 | ⏰ 5:00 PM - 9:00 PM | 📍 Lionel Wendt Theatre
                                        </div>
                                        <div class="request-info">
                                            <strong>Participants:</strong> Full cast and crew
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge assigned">Confirmed</span>
                                        <button class="btn btn-primary" style="font-size: 11px; padding: 6px 12px;" onclick="viewScheduleDetails(5)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="editSchedule(5)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Past Events -->
        <div id="pastTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Past Events</h3>
                            <ul>
                                <li>
                                    <div>
                                        <strong><i class="fas fa-theater-masks"></i> Rehearsal - Maname</strong>
                                        <div class="request-info">
                                            📅 December 15, 2024 | ⏰ 10:00 AM - 1:00 PM | 📍 NCPA Hall
                                        </div>
                                        <div class="request-info">
                                            <strong>Attendance:</strong> 10/12 artists attended
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge" style="background: #6c757d; color: #fff;">Completed</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewScheduleDetails(6)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </li>
                                <li>
                                    <div>
                                        <strong><i class="fas fa-user-check"></i> Interview - Maname</strong>
                                        <div class="request-info">
                                            📅 December 10, 2024 | ⏰ 2:00 PM - 5:00 PM | 📍 Online
                                        </div>
                                        <div class="request-info">
                                            <strong>Candidates:</strong> 8 artists interviewed, 3 selected
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <span class="status-badge" style="background: #6c757d; color: #fff;">Completed</span>
                                        <button class="btn btn-secondary" style="font-size: 11px; padding: 6px 12px;" onclick="viewScheduleDetails(7)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content: Calendar View -->
        <div id="calendarTab" class="tab-content">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <button class="btn btn-secondary" onclick="previousMonth()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <h3 id="calendarMonthYear" style="margin: 0;">December 2024</h3>
                                <button class="btn btn-secondary" onclick="nextMonth()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                            
                            <!-- Calendar Grid -->
                            <div style="background: white; border-radius: 8px; overflow: hidden;">
                                <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background: var(--border);">
                                    <!-- Day Headers -->
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Sun</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Mon</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Tue</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Wed</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Thu</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Fri</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Sat</div>
                                    
                                    <!-- Calendar Days (will be populated by JavaScript) -->
                                    <div id="calendarDays" style="display: contents;"></div>
                                </div>
                            </div>
                            
                            <!-- Legend -->
                            <div style="margin-top: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 16px; height: 16px; background: #007bff; border-radius: 3px;"></div>
                                    <span style="font-size: 13px;">Rehearsal</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 16px; height: 16px; background: #28a745; border-radius: 3px;"></div>
                                    <span style="font-size: 13px;">Interview</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 16px; height: 16px; background: #ffc107; border-radius: 3px;"></div>
                                    <span style="font-size: 13px;">Meeting</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 16px; height: 16px; background: var(--brand); border-radius: 3px;"></div>
                                    <span style="font-size: 13px;">Today</span>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div style="margin-top: 30px; display: flex; gap: 10px;">
                                <button class="btn btn-primary" onclick="exportToCalendar()">
                                    <i class="fas fa-download"></i>
                                    Export to Google Calendar
                                </button>
                                <button class="btn btn-secondary" onclick="printSchedule()">
                                    <i class="fas fa-print"></i>
                                    Print Schedule
                                </button>
                                <button class="btn btn-success" onclick="openScheduleModal()">
                                    <i class="fas fa-plus"></i>
                                    Add Event
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Schedule Event Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeScheduleModal()">&times;</span>
            <h2 id="scheduleModalTitle"><i class="fas fa-calendar-plus"></i> Schedule Event</h2>
            <div class="modal-body">
                <form id="scheduleForm">
                    <div class="form-group">
                        <label for="scheduleDrama">Select Drama *</label>
                        <select id="scheduleDrama" required>
                            <option value="">Choose a drama...</option>
                            <option value="1">Maname</option>
                            <option value="2">Sinhabahu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="eventType">Event Type *</label>
                        <select id="eventType" required>
                            <option value="">Choose event type...</option>
                            <option value="rehearsal">Rehearsal</option>
                            <option value="interview">Interview</option>
                            <option value="meeting">Production Meeting</option>
                            <option value="performance">Performance</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="eventTitle">Event Title *</label>
                        <input type="text" id="eventTitle" required placeholder="e.g., Act 1 Rehearsal">
                    </div>

                    <div class="form-group">
                        <label for="eventDate">Date *</label>
                        <input type="date" id="eventDate" required>
                    </div>

                    <div class="form-group">
                        <label for="eventStartTime">Start Time *</label>
                        <input type="time" id="eventStartTime" required>
                    </div>

                    <div class="form-group">
                        <label for="eventEndTime">End Time *</label>
                        <input type="time" id="eventEndTime" required>
                    </div>

                    <div class="form-group">
                        <label for="eventVenue">Venue *</label>
                        <input type="text" id="eventVenue" required placeholder="e.g., NCPA Hall or Online (Zoom)">
                    </div>

                    <div class="form-group">
                        <label for="eventDescription">Description</label>
                        <textarea id="eventDescription" placeholder="Provide details about the event..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="participants">Participants</label>
                        <select id="participants" multiple style="height: 120px;">
                            <option value="all">All Cast Members</option>
                            <option value="1">Kasun Perera - Lead Actor</option>
                            <option value="2">Nimal Silva - Villain</option>
                            <option value="3">Samantha Fernando - Dancer</option>
                        </select>
                        <span class="info-text">Hold Ctrl (Cmd on Mac) to select multiple participants</span>
                    </div>

                    <div class="form-group">
                        <label for="sendNotifications">
                            <input type="checkbox" id="sendNotifications" checked>
                            Send notifications to participants
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="submitSchedule()">
                    <i class="fas fa-check"></i>
                    Create Schedule
                </button>
                <button class="btn btn-secondary" onclick="closeScheduleModal()">Cancel</button>
            </div>
        </div>
    </div>

    <!-- View Schedule Details Modal -->
    <div id="scheduleDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeScheduleDetailsModal()">&times;</span>
            <h2><i class="fas fa-calendar"></i> Schedule Details</h2>
            <div class="modal-body" id="scheduleDetailsBody">
                <!-- Schedule details will be loaded here -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="editScheduleFromDetails()">
                    <i class="fas fa-edit"></i>
                    Edit
                </button>
                <button class="btn btn-secondary" onclick="closeScheduleDetailsModal()">Close</button>
            </div>
        </div>
    </div>

    <script src="/Rangamadala/public/assets/JS/schedule-management.js"></script>
</body>
</html>
