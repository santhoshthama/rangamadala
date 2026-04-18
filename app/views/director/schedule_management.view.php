<?php
// Extract data
if (isset($data) && is_array($data)) {
    extract($data);
}

$dramaId = isset($dramaId) ? (int)$dramaId : (isset($drama->id) ? (int)$drama->id : 0);
$dramaName = isset($drama->drama_name) ? $drama->drama_name : 'Drama';
$flash = isset($flash) && is_array($flash) ? $flash : null;

$upcomingEvents = isset($upcomingEvents) && is_array($upcomingEvents) ? $upcomingEvents : [];
$pastEvents = isset($pastEvents) && is_array($pastEvents) ? $pastEvents : [];
$allEvents = isset($allEvents) && is_array($allEvents) ? $allEvents : [];
$interviewEvents = isset($interviewEvents) ? $interviewEvents : [];
$roles = isset($roles) ? $roles : [];
$scheduleStats = isset($scheduleStats) ? $scheduleStats : null;
$thisWeek = isset($thisWeek) && is_array($thisWeek) ? $thisWeek : [];
$nextWeek = isset($nextWeek) && is_array($nextWeek) ? $nextWeek : [];
$laterEvents = isset($laterEvents) && is_array($laterEvents) ? $laterEvents : [];
$calendarEvents = isset($calendarEvents) && is_array($calendarEvents) ? $calendarEvents : [];

require_once __DIR__ . '/_profile_image_helper.php';
$profileImageSrc = directorResolveProfileImageSrc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management - <?= esc($dramaName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/director-schedule.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="director-dashboard-page">
    <!-- Sidebar -->
    <?php
    $directorSidebarDramaId = (int)$dramaId;
    $directorSidebarActive = 'schedule';
    include __DIR__ . '/_partials/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="main--content">
    
        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($dramaName) ?></span>
                <h2>Schedule Management</h2>
            </div>
            <div class="user--info">
                <?php
                $directorProfileImageSrc = $profileImageSrc;
                $directorRoleLabel = 'Director';
                include __DIR__ . '/_partials/user_menu.php';
                ?>
            </div>
        </div>

        <?php if (!empty($flash)): ?>
            <?php include APPROOT . '/views/_partials/flash.php'; ?>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid director-stats-grid schedule-stats-grid schedule-stats-spacing">
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Rehearsals</div>
                    <div class="stat-card-icon primary">
                        <i class="bx bx-movie-play"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $scheduleStats ? (int)$scheduleStats->rehearsals : 0 ?></div>
            </div>
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Interviews</div>
                    <div class="stat-card-icon success">
                        <i class="bx bx-user-check"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $scheduleStats ? (int)$scheduleStats->interviews : 0 ?></div>
            </div>
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Meetings</div>
                    <div class="stat-card-icon info">
                        <i class="bx bx-group"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $scheduleStats ? (int)$scheduleStats->meetings : 0 ?></div>
            </div>
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Upcoming</div>
                    <div class="stat-card-icon warning">
                        <i class="bx bx-calendar-event"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $scheduleStats ? (int)$scheduleStats->upcoming : 0 ?></div>
            </div>
            <div class="stat-card director-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Past</div>
                    <div class="stat-card-icon info">
                        <i class="bx bx-history"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= $scheduleStats ? (int)$scheduleStats->past : 0 ?></div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="nav-tabs-bar tabs">
            <button class="nav-tab-btn tab-button active" onclick="showScheduleTab('upcoming', this)">
                <i class="bx bx-calendar-day"></i> Upcoming Events
            </button>
            <button class="nav-tab-btn tab-button" onclick="showScheduleTab('past', this)">
                <i class="bx bx-history"></i> Past Events
            </button>
            <button class="nav-tab-btn tab-button" onclick="showScheduleTab('calendar', this)">
                <i class="bx bx-calendar"></i> Calendar View
            </button>
        </div>

        <!-- ═══════════ TAB: UPCOMING ═══════════ -->
        <div id="upcomingTab" class="tab-content active">
            <div class="content">
                <div class="profile-container schedule-single-column">
                    <div class="details">
                        <?php if (empty($upcomingEvents)): ?>
                            <div class="card-section">
                                <div class="schedule-section-header schedule-section-header-lg">
                                    <h3 class="schedule-section-title"><span>Upcoming Events</span></h3>
                                    <?php include __DIR__ . '/_partials/schedule_toolbar.php'; ?>
                                </div>
                                <div class="schedule-empty-state">
                                    <i class="bx bx-calendar-plus schedule-empty-icon"></i>
                                    <h3 class="schedule-empty-title">No Upcoming Events</h3>
                                    <p>Schedule a rehearsal or interview to get started.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($thisWeek)): ?>
                            <div class="card-section">
                                <div class="schedule-section-header">
                                    <h3 class="schedule-section-title"><span>This Week</span></h3>
                                    <?php include __DIR__ . '/_partials/schedule_toolbar.php'; ?>
                                </div>
                                <?php foreach ($thisWeek as $evt): ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($nextWeek)): ?>
                            <div class="card-section">
                                <div class="schedule-section-header">
                                    <h3 class="schedule-section-title"><span>Next Week</span></h3>
                                    <?php include __DIR__ . '/_partials/schedule_toolbar.php'; ?>
                                </div>
                                <?php foreach ($nextWeek as $evt): ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($laterEvents)): ?>
                            <div class="card-section">
                                <div class="schedule-section-header">
                                    <h3 class="schedule-section-title"><span>Later</span></h3>
                                    <?php include __DIR__ . '/_partials/schedule_toolbar.php'; ?>
                                </div>
                                <?php foreach ($laterEvents as $evt): ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════ TAB: PAST ═══════════ -->
        <div id="pastTab" class="tab-content">
            <div class="content">
                <div class="profile-container schedule-single-column">
                    <div class="details">
                        <div class="card-section">
                            <div class="schedule-section-header">
                                <h3 class="schedule-section-title">Past Events</h3>
                                <?php
                                $scheduleToolbarConfig = [
                                    'showRehearsal' => false,
                                    'showInterview' => false,
                                    'showCreate' => true,
                                    'createLabel' => 'Create Event',
                                    'createIcon' => 'bx-plus',
                                    'createType' => null,
                                ];
                                include __DIR__ . '/_partials/schedule_toolbar.php';
                                unset($scheduleToolbarConfig);
                                ?>
                            </div>
                            <?php if (empty($pastEvents)): ?>
                                <div class="schedule-empty-state schedule-empty-state-muted">
                                    <i class="bx bx-history schedule-empty-icon"></i>
                                    <h3 class="schedule-empty-title">No Past Events</h3>
                                    <p>Completed events will appear here.</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($pastEvents as $evt): ?>
                                    <?php $isPast = true; ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                    <?php $isPast = false; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════ TAB: CALENDAR ═══════════ -->
        <div id="calendarTab" class="tab-content">
            <div class="content">
                <div class="profile-container schedule-single-column">
                    <div class="details">
                        <div class="card-section">
                            <div class="calendar-toolbar">
                                <div class="calendar-nav">
                                    <button class="btn btn-secondary calendar-nav-btn" onclick="previousMonth()">
                                        <i class="bx bx-chevron-left"></i>
                                    </button>
                                    <h3 id="calendarMonthYear" class="calendar-month-title"></h3>
                                    <button class="btn btn-secondary calendar-nav-btn" onclick="nextMonth()">
                                        <i class="bx bx-chevron-right"></i>
                                    </button>
                                </div>
                                <?php include __DIR__ . '/_partials/schedule_toolbar.php'; ?>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="calendar-shell">
                                <div id="calendarGrid" class="calendar-grid">
                                    <div class="calendar-weekday">Sun</div>
                                    <div class="calendar-weekday">Mon</div>
                                    <div class="calendar-weekday">Tue</div>
                                    <div class="calendar-weekday">Wed</div>
                                    <div class="calendar-weekday">Thu</div>
                                    <div class="calendar-weekday">Fri</div>
                                    <div class="calendar-weekday">Sat</div>
                                    <div id="calendarDays" class="calendar-days"></div>
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="calendar-legend">
                                <div class="calendar-legend-item">
                                    <div class="legend-dot legend-dot-rehearsal"></div>
                                    <span class="calendar-legend-label">Rehearsal</span>
                                </div>
                                <div class="calendar-legend-item">
                                    <div class="legend-dot legend-dot-interview"></div>
                                    <span class="calendar-legend-label">Interview</span>
                                </div>
                                <div class="calendar-legend-item">
                                    <div class="legend-dot legend-dot-meeting"></div>
                                    <span class="calendar-legend-label">Meeting</span>
                                </div>
                                <div class="calendar-legend-item">
                                    <div class="legend-dot legend-dot-performance"></div>
                                    <span class="calendar-legend-label">Performance</span>
                                </div>
                                <div class="calendar-legend-item">
                                    <div class="legend-dot legend-dot-today"></div>
                                    <span class="calendar-legend-label">Today</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- ═══════════ CREATE / EDIT MODAL ═══════════ -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2 id="modalTitle"><i class="bx bx-calendar-plus"></i> Schedule Event</h2>
            <div class="modal-body">
                <form id="scheduleForm" method="POST" action="">
                    <input type="hidden" name="event_id" id="formEventId" value="">

                    <div class="form-group">
                        <label for="formEventType">Event Type *</label>
                        <select id="formEventType" name="event_type" required>
                            <option value="">Choose event type...</option>
                            <option value="rehearsal">Rehearsal</option>
                            <option value="interview">Interview</option>
                            <option value="meeting">Production Meeting</option>
                            <option value="performance">Performance</option>
                        </select>
                    </div>

                    <div class="form-group is-hidden" id="roleSelectGroup">
                        <label for="formRoleId">Select Role (for interview)</label>
                        <select id="formRoleId" name="role_id">
                            <option value="">-- Select Role --</option>
                            <?php foreach ($roles as $role): ?>
                                <option value="<?= (int)$role->id ?>"><?= esc($role->role_name) ?> (<?= esc(ucfirst($role->role_type)) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="formEventTitle">Event Title *</label>
                        <input type="text" id="formEventTitle" name="event_title" required placeholder="e.g., Act 1 Rehearsal">
                    </div>

                    <div class="form-group">
                        <label for="formScheduledDate">Date *</label>
                        <input type="date" id="formScheduledDate" name="scheduled_date" required>
                        <div id="dateAvailability" class="date-availability"></div>
                    </div>

                    <div class="form-group">
                        <label for="formStartTime">Start Time *</label>
                        <input type="time" id="formStartTime" name="start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="formEndTime">End Time *</label>
                        <input type="time" id="formEndTime" name="end_time" required>
                    </div>

                    <div class="form-group">
                        <label for="formVenue">Venue *</label>
                        <input type="text" id="formVenue" name="venue" required placeholder="e.g., NCPA Hall or Online (Zoom)">
                    </div>

                    <div class="form-group">
                        <label for="formDescription">Description</label>
                        <textarea id="formDescription" name="event_description" rows="3" placeholder="Provide details about the event..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="formNotes">Notes</label>
                        <textarea id="formNotes" name="notes" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>

                    <div class="modal-actions modal-actions-spaced">
                        <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                            <i class="bx bx-check"></i> Create Schedule
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ═══════════ VIEW DETAILS MODAL ═══════════ -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetailsModal()">&times;</span>
            <h2><i class="bx bx-calendar"></i> Event Details</h2>
            <div class="modal-body" id="detailsBody">
                <!-- Populated by JS -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeDetailsModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        // ── Configuration ──
        const ROOT = '<?= ROOT ?>';
        const DRAMA_ID = <?= (int)$dramaId ?>;
        const CALENDAR_EVENTS = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
    <script src="/Rangamadala/public/assets/JS/schedule-management.js"></script>
</body>
</html>
