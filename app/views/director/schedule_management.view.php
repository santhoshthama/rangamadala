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

require_once __DIR__ . '/_profile_image_helper.php';
$profileImageSrc = directorResolveProfileImageSrc((int)($_SESSION['user_id'] ?? 0));
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
        .event-card { background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%); border-radius: 12px; padding: 16px 18px; margin-bottom: 14px; border: 1px solid #ead7a4; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(186,142,35,0.06); }
        .event-card:hover { box-shadow: 0 6px 16px rgba(186,142,35,0.12); transform: translateY(-2px); }
        .schedule-toolbar { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .schedule-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid #ead7a4;
            font-size: 13px;
            font-weight: 700;
            line-height: 1;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(186,142,35,0.10);
            white-space: nowrap;
        }
        .schedule-action-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(186,142,35,0.16); }
        .schedule-action-btn i { font-size: 16px; }
        .schedule-action-btn.rehearsal { background: linear-gradient(135deg, #d8b566 0%, #c59b3d 100%); color: #2f2410; border-color: #c9a14a; }
        .schedule-action-btn.interview { background: linear-gradient(180deg, #fffdf7 0%, #fff2d3 100%); color: #5a4300; border-color: #ead7a4; }
        .schedule-action-btn.secondary { background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%); color: #4a3a14; }
        .event-meta { font-size: 13px; color: #7a6121; margin-top: 6px; display: flex; align-items: center; gap: 8px; }
        .event-meta i { color: #ba8e23; font-size: 14px; }
        .event-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .event-header { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
        .event-card .status-badge { display: inline-flex; padding: 6px 14px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.4px; }
        .event-card .status-badge.pending { background: #fff3cd; color: #856404; }
        .event-card .status-badge.scheduled { background: linear-gradient(135deg, #ffd89b 0%, #ffcb70 100%); color: #5a4300; }
        .event-card .status-badge.confirmed { background: rgba(76,175,80,0.15); color: #256029; }
        .event-card .status-badge.assigned { background: rgba(76,175,80,0.15); color: #256029; }
        .event-card .status-badge.cancelled { background: #f8d7da; color: #721c24; }
        .event-card .btn { font-size: 12px; padding: 8px 12px; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s ease; }
        .event-card .btn:hover { transform: translateY(-1px); }
        .event-card .btn { min-width: 34px; min-height: 34px; }
        .event-card .btn-primary { background: linear-gradient(135deg, #d8b566 0%, #c59b3d 100%); border: 1px solid #c9a14a; color: #2f2410; box-shadow: 0 3px 8px rgba(186,142,35,0.12); }
        .event-card .btn-secondary { background: linear-gradient(180deg, #fffdf7 0%, #fff7e6 100%); border: 1px solid #f0dfb4; color: #4a3a14; box-shadow: 0 2px 6px rgba(186,142,35,0.08); }
        .event-card .btn-success { background: linear-gradient(135deg, #d8b566 0%, #c59b3d 100%); border: 1px solid #c9a14a; color: #2f2410; box-shadow: 0 3px 8px rgba(186,142,35,0.12); }
        .event-card .btn-danger { background: linear-gradient(135deg, #e7b0a9 0%, #d98d84 100%); border: 1px solid #d98d84; color: #4a1714; box-shadow: 0 3px 8px rgba(217,141,132,0.12); }
        .event-card .btn-delete-event { background: linear-gradient(135deg, #8f2d2d 0%, #b04444 100%); border: 1px solid #a63c3c; color: #fff; box-shadow: 0 3px 8px rgba(143,45,45,0.18); }
        .event-card .btn-delete-event:hover { background: linear-gradient(135deg, #a63c3c 0%, #c24f4f 100%); }
        .event-card .btn-primary i,
        .event-card .btn-secondary i,
        .event-card .btn-success i,
        .event-card .btn-danger i,
        .event-card .btn-delete-event i { font-size: 15px; }
        .stats-mini { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
        .stat-mini { padding: 12px 20px; border-radius: 10px; text-align: center; min-width: 100px; background: #fffdf7; border: 1px solid #ead7a4; color: #2f2410; }
        .stat-mini h4 { margin: 0; font-size: 22px; color: #ba8e23; }
        .stat-mini p { margin: 4px 0 0; font-size: 12px; color: #8a6a1f; }
    </style>
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

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box" style="background: <?= $_SESSION['message_type'] === 'success' ? '#d4edda' : '#f8d7da' ?>; color: <?= $_SESSION['message_type'] === 'success' ? '#155724' : '#721c24' ?>; padding: 14px 20px; border-radius: 8px; margin-bottom: 20px;">
                <?= esc($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid director-stats-grid schedule-stats-grid" style="margin-bottom: 24px;">
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
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <?php if (empty($upcomingEvents)): ?>
                            <div class="card-section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                    <h3 style="margin: 0;"><span>Upcoming Events</span></h3>
                                    <div class="schedule-toolbar">
                                        <button class="schedule-action-btn rehearsal" onclick="openCreateModal('rehearsal')">
                                            <i class="bx bx-theater-masks"></i> Schedule Rehearsal
                                        </button>
                                        <button class="schedule-action-btn interview" onclick="openCreateModal('interview')">
                                            <i class="bx bx-user-check"></i> Schedule Interview
                                        </button>
                                    </div>
                                </div>
                                <div style="text-align: center; padding: 40px; color: var(--muted); background: linear-gradient(180deg, #fffdfb 0%, #fff8f0 100%); border: 1px dashed #ead7a4; border-radius: 12px;">
                                    <i class="bx bx-calendar-plus" style="font-size: 40px; display: block; margin-bottom: 16px; color: #ba8e23;"></i>
                                    <h3 style="color: #2f2410;">No Upcoming Events</h3>
                                    <p>Schedule a rehearsal or interview to get started.</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if (!empty($thisWeek)): ?>
                            <div class="card-section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <h3 style="margin: 0;"><span>This Week</span></h3>
                                    <div class="schedule-toolbar">
                                        <button class="schedule-action-btn rehearsal" onclick="openCreateModal('rehearsal')">
                                            <i class="bx bx-theater-masks"></i> Schedule Rehearsal
                                        </button>
                                        <button class="schedule-action-btn interview" onclick="openCreateModal('interview')">
                                            <i class="bx bx-user-check"></i> Schedule Interview
                                        </button>
                                    </div>
                                </div>
                                <?php foreach ($thisWeek as $evt): ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($nextWeek)): ?>
                            <div class="card-section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <h3 style="margin: 0;"><span>Next Week</span></h3>
                                    <div class="schedule-toolbar">
                                        <button class="schedule-action-btn rehearsal" onclick="openCreateModal('rehearsal')">
                                            <i class="bx bx-theater-masks"></i> Schedule Rehearsal
                                        </button>
                                        <button class="schedule-action-btn interview" onclick="openCreateModal('interview')">
                                            <i class="bx bx-user-check"></i> Schedule Interview
                                        </button>
                                    </div>
                                </div>
                                <?php foreach ($nextWeek as $evt): ?>
                                    <?php include __DIR__ . '/../_partials/_schedule_event_card.php'; ?>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($laterEvents)): ?>
                            <div class="card-section">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                    <h3 style="margin: 0;"><span>Later</span></h3>
                                    <div class="schedule-toolbar">
                                        <button class="schedule-action-btn rehearsal" onclick="openCreateModal('rehearsal')">
                                            <i class="bx bx-theater-masks"></i> Schedule Rehearsal
                                        </button>
                                        <button class="schedule-action-btn interview" onclick="openCreateModal('interview')">
                                            <i class="bx bx-user-check"></i> Schedule Interview
                                        </button>
                                    </div>
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
                <div class="profile-container" style="grid-template-columns: 1fr;">
                    <div class="details">
                        <div class="card-section">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                <h3 style="margin: 0;">Past Events</h3>
                                <button class="schedule-action-btn rehearsal" onclick="openCreateModal()">
                                    <i class="bx bx-plus"></i> Create Event
                                </button>
                            </div>
                            <?php if (empty($pastEvents)): ?>
                                <div style="text-align: center; padding: 40px; color: #8a6a1f; background: linear-gradient(180deg, #fffdfb 0%, #fff8f0 100%); border: 1px dashed #ead7a4; border-radius: 12px;">
                                    <i class="bx bx-history" style="font-size: 40px; display: block; margin-bottom: 16px; color: #ba8e23;"></i>
                                    <h3 style="color: #2f2410;">No Past Events</h3>
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
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <button class="btn btn-secondary" onclick="previousMonth()" style="padding: 10px 14px;">
                                        <i class="bx bx-chevron-left"></i>
                                    </button>
                                    <h3 id="calendarMonthYear" style="margin: 0; min-width: 200px; text-align: center; color: #2f2410;"></h3>
                                    <button class="btn btn-secondary" onclick="nextMonth()" style="padding: 10px 14px;">
                                        <i class="bx bx-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="schedule-toolbar">
                                    <button class="schedule-action-btn rehearsal" onclick="openCreateModal('rehearsal')">
                                        <i class="bx bx-theater-masks"></i> Schedule Rehearsal
                                    </button>
                                    <button class="schedule-action-btn interview" onclick="openCreateModal('interview')">
                                        <i class="bx bx-user-check"></i> Schedule Interview
                                    </button>
                                </div>
                            </div>
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
    <script src="/Rangamadala/public/assets/JS/director-user-menu.js"></script>
    <script src="/Rangamadala/public/assets/JS/schedule-management.js"></script>
</body>
</html>
