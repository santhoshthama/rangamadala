<?php
if (isset($data) && is_array($data)) {
    extract($data);
}

$profileImageSrc = isset($profileImageSrc) && is_string($profileImageSrc) && $profileImageSrc !== ''
    ? $profileImageSrc
    : ROOT . '/uploads/profile_images/user_profile.png';

$sidebarActiveDefaults = [
    'dashboard' => false,
    'notifications' => false,
    'vacancies' => false,
    'classes' => false,
    'showings' => false,
    'calendar' => false,
];

$sidebarActive = (isset($sidebarActive) && is_array($sidebarActive))
    ? array_merge($sidebarActiveDefaults, $sidebarActive)
    : $sidebarActiveDefaults;

$calendarEvents = isset($calendarEvents) && is_array($calendarEvents) ? $calendarEvents : [];
$calendarDramas = isset($calendarDramas) && is_array($calendarDramas) ? $calendarDramas : [];
$calendarFilters = isset($calendarFilters) && is_array($calendarFilters) ? $calendarFilters : [];

$initialView = isset($calendarFilters['view']) ? (string)$calendarFilters['view'] : 'month';
$initialDrama = isset($calendarFilters['drama_id']) ? (int)$calendarFilters['drama_id'] : 0;
$initialParticipation = isset($calendarFilters['participation']) ? (string)$calendarFilters['participation'] : 'all';
$initialStartDate = isset($calendarFilters['start_date']) ? (string)$calendarFilters['start_date'] : date('Y-m-01');
$initialEndDate = isset($calendarFilters['end_date']) ? (string)$calendarFilters['end_date'] : date('Y-m-t', strtotime('+2 months'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Calendar - Rangamadala</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/artistdashboard-page.css">
    <link rel="stylesheet" href="<?= ROOT ?>/assets/CSS/artist-calendar.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <aside class="sidebar">
        <div class="logo">
            <a href="<?=ROOT?>/artistdashboard" class="logo-link">
                <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" class="logo-image">
            </a>
        </div>
        <ul class="menu">
            <li class="<?= $sidebarActive['dashboard'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard">
                    <i class='bx bx-home'></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['vacancies'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies">
                    <i class='bx bx-volume-full'></i>
                    <span>View All Vacancies</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['notifications'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/notifications">
                    <i class='bx bx-bell'></i>
                    <span>Notifications</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['classes'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/classes">
                    <i class='bx bx-microphone'></i>
                    <span>Classes</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['showings'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard?tab=my-showings#my-showings">
                    <i class='bx bx-calendar-event'></i>
                    <span>Showings</span>
                </a>
            </li>
            <li class="<?= $sidebarActive['calendar'] ? 'active' : '' ?>">
                <a href="<?=ROOT?>/artistdashboard/calendar">
                    <i class='bx bx-calendar-week'></i>
                    <span>Artist Calendar</span>
                </a>
            </li>
        </ul>
    </aside>

    <main class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Artist Calendar</span>
                <h2><?= isset($user->full_name) ? esc($user->full_name) : 'Artist' ?></h2>
                <p class="artist-calendar-subtitle">Unified schedule across all your assigned dramas.</p>
            </div>
            <div class="user--info">
                <a href="<?= ROOT ?>/profile" class="btn btn-secondary">
                    <i class='bx bx-user'></i> Profile
                </a>
            </div>
        </div>

        <div class="card-section artist-calendar-controls">
            <div class="artist-calendar-view-switch" role="tablist" aria-label="Calendar view mode">
                <button type="button" class="artist-calendar-view-btn" data-view="day">Day</button>
                <button type="button" class="artist-calendar-view-btn" data-view="week">Week</button>
                <button type="button" class="artist-calendar-view-btn" data-view="month">Month</button>
            </div>

            <form id="artistCalendarFilters" class="artist-calendar-filter-grid">
                <div>
                    <label for="filterDramaId">Drama</label>
                    <select id="filterDramaId" name="drama_id">
                        <option value="0">All Dramas</option>
                        <?php foreach ($calendarDramas as $drama): ?>
                            <option value="<?= (int)$drama->id ?>" <?= $initialDrama === (int)$drama->id ? 'selected' : '' ?>>
                                <?= esc($drama->drama_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="filterParticipation">Participation</label>
                    <select id="filterParticipation" name="participation">
                        <option value="all" <?= $initialParticipation === 'all' ? 'selected' : '' ?>>All Roles</option>
                        <option value="director" <?= $initialParticipation === 'director' ? 'selected' : '' ?>>Director Dramas</option>
                        <option value="actor" <?= $initialParticipation === 'actor' ? 'selected' : '' ?>>Actor Dramas</option>
                        <option value="pm" <?= $initialParticipation === 'pm' ? 'selected' : '' ?>>PM Dramas</option>
                    </select>
                </div>
                <div>
                    <label for="filterStartDate">Start date</label>
                    <input type="date" id="filterStartDate" name="start_date" value="<?= esc($initialStartDate) ?>">
                </div>
                <div>
                    <label for="filterEndDate">End date</label>
                    <input type="date" id="filterEndDate" name="end_date" value="<?= esc($initialEndDate) ?>">
                </div>
                <div class="artist-calendar-filter-actions">
                    <button type="submit" class="btn btn-primary"><i class='bx bx-filter-alt'></i> Apply</button>
                    <button type="button" id="calendarTodayBtn" class="btn btn-secondary"><i class='bx bx-calendar-check'></i> Today</button>
                </div>
            </form>
        </div>

        <div class="card-section artist-calendar-board">
            <div class="artist-calendar-toolbar">
                <button type="button" class="btn btn-secondary" id="calendarPrevBtn"><i class='bx bx-chevron-left'></i></button>
                <h3 id="artistCalendarTitle">Calendar</h3>
                <button type="button" class="btn btn-secondary" id="calendarNextBtn"><i class='bx bx-chevron-right'></i></button>
            </div>
            <div id="artistCalendarContainer"></div>
            <div id="artistCalendarSyncInfo" class="artist-calendar-sync"></div>
        </div>
    </main>

    <script>
        const ROOT = '<?= ROOT ?>';
        const ARTIST_CALENDAR_INITIAL_EVENTS = <?= json_encode($calendarEvents, JSON_UNESCAPED_UNICODE) ?>;
        const ARTIST_CALENDAR_INITIAL_FILTERS = {
            drama_id: <?= (int)$initialDrama ?>,
            participation: '<?= esc($initialParticipation) ?>',
            start_date: '<?= esc($initialStartDate) ?>',
            end_date: '<?= esc($initialEndDate) ?>',
            view: '<?= esc($initialView) ?>'
        };
    </script>
    <script src="<?= ROOT ?>/assets/JS/artist-calendar.js"></script>
</body>
</html>
