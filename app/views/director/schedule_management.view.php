<?php
// Extract data
if (isset($data) && is_array($data)) {
    extract($data);
}

$dramaId = isset($drama->id) ? $drama->id : ($_GET['drama_id'] ?? 1);
$dramaName = isset($drama->drama_name) ? $drama->drama_name : 'Drama';

$upcomingEvents = isset($upcomingEvents) ? $upcomingEvents : [];
$pastEvents = isset($pastEvents) ? $pastEvents : [];
$allEvents = isset($allEvents) ? $allEvents : [];
$interviewEvents = isset($interviewEvents) ? $interviewEvents : [];
$roles = isset($roles) ? $roles : [];
$scheduleStats = isset($scheduleStats) ? $scheduleStats : null;

// Build JSON for JS calendar + details/edit modals
$calendarEvents = [];
foreach ($allEvents as $evt) {
    $calendarEvents[] = [
        'id' => $evt->id,
        'date' => $evt->scheduled_date,
        'title' => $evt->event_title,
        'type' => $evt->event_type,
        'start_time' => substr($evt->start_time, 0, 5),
        'end_time' => substr($evt->end_time, 0, 5),
        'venue' => $evt->venue,
        'status' => $evt->status,
        'description' => $evt->event_description ?? '',
        'notes' => $evt->notes ?? '',
        'role_id' => $evt->role_id ?? null,
        'role_name' => $evt->role_name ?? '',
        'editable' => true,
    ];
}
// Add interview events from role_applications
foreach ($interviewEvents as $iv) {
    if (!$iv->interview_at) continue;
    $calendarEvents[] = [
        'id' => 'interview_' . $iv->application_id,
        'date' => date('Y-m-d', strtotime($iv->interview_at)),
        'title' => 'Interview - ' . ($iv->role_name ?? 'Role') . ' (' . ($iv->artist_name ?? 'Artist') . ')',
        'type' => 'interview',
        'start_time' => date('H:i', strtotime($iv->interview_at)),
        'end_time' => '',
        'venue' => '',
        'status' => $iv->interview_status ?? 'pending',
        'description' => '',
        'notes' => '',
        'role_id' => null,
        'role_name' => $iv->role_name ?? '',
        'editable' => false,
    ];
}

// Group upcoming events by week
$thisWeek = [];
$nextWeek = [];
$laterEvents = [];
$now = new DateTime();
$endOfThisWeek = (clone $now)->modify('sunday this week');
$endOfNextWeek = (clone $endOfThisWeek)->modify('+7 days');

foreach ($upcomingEvents as $evt) {
    $evtDate = new DateTime($evt->scheduled_date);
    if ($evtDate <= $endOfThisWeek) {
        $thisWeek[] = $evt;
    } elseif ($evtDate <= $endOfNextWeek) {
        $nextWeek[] = $evt;
    } else {
        $laterEvents[] = $evt;
    }
}

function eventTypeIcon($type) {
    switch ($type) {
        case 'rehearsal': return 'bx-theater-masks';
        case 'interview': return 'bx-user-check';
        case 'meeting': return 'bx-users';
        case 'performance': return 'bx-star';
        default: return 'bx-calendar';
    }
}

function statusBadgeClass($status) {
    switch ($status) {
        case 'confirmed': return 'assigned';
        case 'scheduled': return 'pending';
        case 'completed': return '';
        case 'cancelled': return 'unassigned';
        default: return 'pending';
    }
}

function statusBadgeStyle($status) {
    if ($status === 'completed') return 'background: #6c757d; color: #fff;';
    if ($status === 'cancelled') return 'background: #dc3545; color: #fff;';
    return '';
}

// Get current user profile image
$userModel = new M_universal_profile();
$currentUser = $userModel->getUserById($_SESSION['user_id']);
$profileImageSrc = ROOT . '/assets/images/default-avatar.jpg';
if ($currentUser && !empty($currentUser->profile_image)) {
    $imageValue = str_replace('\\', '/', $currentUser->profile_image);
    if (strpos($imageValue, '/') !== false) {
        $profileImageSrc = ROOT . '/' . ltrim($imageValue, '/');
    } else {
        $profileImageSrc = ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
    }
} elseif ($currentUser && !empty($currentUser->nic_photo)) {
    $profileImageSrc = ROOT . '/' . ltrim(str_replace('\\', '/', $currentUser->nic_photo), '/');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Management - <?= esc($dramaName) ?> - Rangamadala</title>
    <link rel="stylesheet" href="/Rangamadala/public/assets/CSS/ui-theme.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
        .date-availability { margin-top: 8px; padding: 10px 14px; border-radius: 6px; font-size: 13px; display: none; }
        .date-availability.available { background: rgba(40, 167, 69, 0.12); color: #155724; border-left: 4px solid #28a745; }
        .date-availability.conflict { background: rgba(220, 53, 69, 0.12); color: #721c24; border-left: 4px solid #dc3545; }
        .date-availability.checking { background: rgba(255, 193, 7, 0.12); color: #856404; border-left: 4px solid #ffc107; }
        .event-card { background: var(--bg-card, #fff); border-radius: 8px; padding: 16px; margin-bottom: 12px; border: 1px solid var(--border, #e0e0e0); transition: box-shadow 0.15s; }
        .event-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .event-meta { font-size: 13px; color: var(--muted, #888); margin-top: 4px; }
        .event-actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .event-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; flex-wrap: wrap; }
        .stats-mini { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
        .stat-mini { padding: 12px 20px; border-radius: 8px; text-align: center; min-width: 100px; }
        .stat-mini h4 { margin: 0; font-size: 22px; }
        .stat-mini p { margin: 4px 0 0; font-size: 12px; opacity: 0.85; }
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
                <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= esc($dramaId) ?>">
                    <i class="bx bx-home"></i><span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/drama_details?drama_id=<?= esc($dramaId) ?>">
                    <i class="bx bx-film"></i><span>Drama Details</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/manage_roles?drama_id=<?= esc($dramaId) ?>">
                    <i class="bx bx-users"></i><span>Artist Roles</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/assign_managers?drama_id=<?= esc($dramaId) ?>">
                    <i class="bx bx-user-tie"></i><span>Production Manager</span>
                </a>
            </li>
            <li class="active">
                <a href="<?= ROOT ?>/director/schedule_management?drama_id=<?= esc($dramaId) ?>">
                    <i class="bx bx-calendar-alt"></i><span>Schedule</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/director/view_services_budget?drama_id=<?= esc($dramaId) ?>">
                    <i class="bx bx-dollar-sign"></i><span>Services & Budget</span>
                </a>
            </li>
            <li>
                <a href="<?= ROOT ?>/artistdashboard">
                    <i class="bx bx-arrow-left"></i><span>Back to Profile</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main--content">
        <a href="<?= ROOT ?>/director/dashboard?drama_id=<?= $dramaId ?>" class="back-button">
            <i class="bx bx-arrow-left"></i> Back to Dashboard
        </a>

        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span><?= esc($dramaName) ?></span>
                <h2>Schedule Management</h2>
            </div>
            <div class="user--info">
                <button class="btn btn-primary" onclick="openCreateModal('rehearsal')">
                    <i class="bx bx-theater-masks"></i> Schedule Rehearsal
                </button>
                <button class="btn btn-success" onclick="openCreateModal('interview')">
                    <i class="bx bx-user-check"></i> Schedule Interview
                </button>
                <div class="role-badge">
                    <i class="bx bx-video"></i> Director
                </div>
                <img src="<?= esc($profileImageSrc) ?>" alt="Director Avatar" onerror="this.src='<?= ROOT ?>/assets/images/default-avatar.jpg'">
                <a href="<?= ROOT ?>/logout" class="logout-btn" title="Logout">
                    <i class="bx bx-sign-out-alt"></i>
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-mini">
            <div class="stat-mini" style="background: linear-gradient(135deg, #007bff, #0056b3); color: #fff;">
                <h4><?= $scheduleStats ? (int)$scheduleStats->rehearsals : 0 ?></h4>
                <p>Rehearsals</p>
            </div>
            <div class="stat-mini" style="background: linear-gradient(135deg, #28a745, #1e7e34); color: #fff;">
                <h4><?= $scheduleStats ? (int)$scheduleStats->interviews : 0 ?></h4>
                <p>Interviews</p>
            </div>
            <div class="stat-mini" style="background: linear-gradient(135deg, #ffc107, #d39e00); color: #fff;">
                <h4><?= $scheduleStats ? (int)$scheduleStats->meetings : 0 ?></h4>
                <p>Meetings</p>
            </div>
            <div class="stat-mini" style="background: linear-gradient(135deg, #ba8e23, #a0781e); color: #fff;">
                <h4><?= $scheduleStats ? (int)$scheduleStats->upcoming : 0 ?></h4>
                <p>Upcoming</p>
            </div>
            <div class="stat-mini" style="background: linear-gradient(135deg, #6c757d, #545b62); color: #fff;">
                <h4><?= $scheduleStats ? (int)$scheduleStats->past : 0 ?></h4>
                <p>Past</p>
            </div>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-button active" onclick="showScheduleTab('upcoming', this)">
                <i class="bx bx-calendar-day"></i> Upcoming Events
            </button>
            <button class="tab-button" onclick="showScheduleTab('past', this)">
                <i class="bx bx-history"></i> Past Events
            </button>
            <button class="tab-button" onclick="showScheduleTab('calendar', this)">
                <i class="bx bx-calendar"></i> Calendar View
            </button>
        </div>

        <!-- ═══════════ TAB: UPCOMING ═══════════ -->
        <div id="upcomingTab" class="tab-content active">
            <div class="content">
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <?php if (empty($upcomingEvents)): ?>
                            <div class="card-section">
                                <div style="text-align: center; padding: 40px; color: var(--muted);">
                                    <i class="bx bx-calendar-plus" style="font-size: 40px; display: block; margin-bottom: 16px;"></i>
                                    <h3>No Upcoming Events</h3>
                                    <p>Schedule a rehearsal or interview to get started.</p>
                                    <button class="btn btn-primary" style="margin-top: 12px;" onclick="openCreateModal()">
                                        <i class="bx bx-plus"></i> Schedule Event
                                    </button>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($thisWeek)): ?>
                            <div class="card-section">
                                <h3><span>This Week</span></h3>
                                <?php foreach ($thisWeek as $evt): ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($nextWeek)): ?>
                            <div class="card-section">
                                <h3><span>Next Week</span></h3>
                                <?php foreach ($nextWeek as $evt): ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($laterEvents)): ?>
                            <div class="card-section">
                                <h3><span>Later</span></h3>
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
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <h3>Past Events</h3>
                            <?php if (empty($pastEvents)): ?>
                                <div style="text-align: center; padding: 40px; color: var(--muted);">
                                    <i class="bx bx-history" style="font-size: 40px; display: block; margin-bottom: 16px;"></i>
                                    <h3>No Past Events</h3>
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
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <button class="btn btn-secondary" onclick="previousMonth()">
                                    <i class="bx bx-chevron-left"></i>
                                </button>
                                <h3 id="calendarMonthYear" style="margin: 0;"></h3>
                                <button class="btn btn-secondary" onclick="nextMonth()">
                                    <i class="bx bx-chevron-right"></i>
                                </button>
                            </div>

                            <!-- Calendar Grid -->
                            <div style="background: white; border-radius: 8px; overflow: hidden;">
                                <div id="calendarGrid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1px; background: var(--border);">
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Sun</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Mon</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Tue</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Wed</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Thu</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Fri</div>
                                    <div style="background: var(--brand); color: white; padding: 12px; text-align: center; font-weight: bold;">Sat</div>
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
                                    <div style="width: 16px; height: 16px; background: #6f42c1; border-radius: 3px;"></div>
                                    <span style="font-size: 13px;">Performance</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <div style="width: 16px; height: 16px; background: var(--brand); border-radius: 3px;"></div>
                                    <span style="font-size: 13px;">Today</span>
                                </div>
                            </div>

                            <div style="margin-top: 20px;">
                                <button class="btn btn-success" onclick="openCreateModal()">
                                    <i class="bx bx-plus"></i> Add Event
                                </button>
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

                    <div class="form-group" id="roleSelectGroup" style="display: none;">
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

                    <div class="modal-actions" style="margin-top: 16px;">
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
    <script src="/Rangamadala/public/assets/JS/schedule-management.js"></script>
</body>
</html>
