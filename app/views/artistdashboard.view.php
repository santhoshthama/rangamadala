<?php 
// Extract data array for easier access
if(isset($data) && is_array($data)) {
    extract($data);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard - Rangamadala</title>
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/ui-theme.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/toast.css">
    <link rel="stylesheet" href="<?=ROOT?>/assets/CSS/artistdashboard-page.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="shortcut icon" href="<?= ROOT ?>/assets/images/Rangamadala logo.png" type="image/x-icon">
</head>
<body class="<?= ($activeTab ?? '') === 'showings' ? 'showings-only' : '' ?>">
        <!-- Toast Notification Script -->
        <script src="<?= ROOT ?>/assets/JS/toast.js"></script>
        <?php if ($toastSuccessMessage !== ''): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastSuccess('<?= addslashes($toastSuccessMessage); ?>');
            });
        </script>
        <?php endif; ?>
        <?php if ($toastErrorMessage !== ''): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                toastError('<?= addslashes($toastErrorMessage); ?>');
            });
        </script>
        <?php endif; ?>

    <!-- Sidebar -->
    <?php
    $artistSidebarActive = 'dashboard';
    if (!empty($sidebarActive['notifications'])) {
        $artistSidebarActive = 'notifications';
    } elseif (!empty($sidebarActive['vacancies'])) {
        $artistSidebarActive = 'vacancies';
    } elseif (!empty($sidebarActive['classes'])) {
        $artistSidebarActive = 'classes';
    } elseif (!empty($sidebarActive['showings'])) {
        $artistSidebarActive = 'showings';
    }
    include __DIR__ . '/artist/_partials/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="main--content">
        <!-- Header -->
        <div class="header--wrapper">
            <div class="header--title">
                <span>Artist Dashboard</span>
                <h2><?= isset($user->full_name) ? 'Hi, ' . esc($user->full_name) : 'Hi, Artist' ?></h2>
            </div>
            <div class="user--info">
                <div class="role-badge">
                    <i class="bx bx-star"></i> Artist
                </div>
                <details class="user-menu">
                    <summary class="user-menu-trigger">
                        <div class="user-avatar-small">
                            <img src="<?= esc($profileImageSrc ?? (ROOT . '/uploads/profile_images/user_profile.png')) ?>" alt="Profile" onerror="this.src='<?= ROOT ?>/uploads/profile_images/user_profile.png'">
                        </div>
                    </summary>
                    <div class="user-menu-dropdown">
                        <a href="<?= ROOT ?>/profile" class="user-menu-item">
                            <i class='bx bx-user icon'></i>
                            <span>Profile</span>
                        </a>
                        <a href="<?= ROOT ?>/logout" class="user-menu-item">
                            <i class='bx bx-log-out icon'></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </details>
            </div>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="info-box <?= $_SESSION['message_type'] === 'success' ? 'artist-flash-success' : 'artist-flash-error' ?>">
                <?= esc($_SESSION['message']) ?>
            </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="stats-grid artist-stats-grid">
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">Total Dramas</div>
                    <div class="stat-card-icon primary">
                        <i class="bx bx-mask"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['total_dramas']) ? $stats['total_dramas'] : 0 ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">As Director</div>
                    <div class="stat-card-icon info">
                        <i class="bx bx-film"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['as_director']) ? $stats['as_director'] : 0 ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">As Production Manager</div>
                    <div class="stat-card-icon success">
                        <i class="bx bx-briefcase"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['as_manager']) ? $stats['as_manager'] : 0 ?></div>
            </div>
            <div class="stat-card artist-stat-card">
                <div class="stat-card-header">
                    <div class="stat-card-title">As Actor</div>
                    <div class="stat-card-icon warning">
                        <i class="bx bx-award"></i>
                    </div>
                </div>
                <div class="stat-card-value"><?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?></div>
            </div>
        </div>

        <!-- Drama Role Vacancies Banner -->
        <div class="card-section vacancies-banner">
            <div class="artist-vacancies-layout">
                <div class="artist-vacancies-content">
                    <h2 class="artist-vacancies-title">
                        Drama Role Vacancies Now Open!
                    </h2>
                    <p class="artist-vacancies-copy">
                        Discover available roles and apply to be part of our upcoming drama productions.
                    </p>
                </div>
                <a href="<?=ROOT?>/artistdashboard/browse_vacancies" class="btn btn-primary btn-compact artist-btn-semibold">
                    <i class="bx bx-search"></i> Search Vacancies
                </a>
            </div>
        </div>

        <!-- Navigation Tab Bar -->
        <div class="nav-tabs-bar">
            <a href="<?=ROOT?>/artistdashboard?tab=director" class="nav-tab-btn <?= $activeTab === 'director' ? 'active' : '' ?>">
                <i class="bx bx-film"></i> As Director (<?= isset($stats['as_director']) ? $stats['as_director'] : 0 ?>)
            </a>
            <a href="<?=ROOT?>/artistdashboard?tab=manager" class="nav-tab-btn <?= $activeTab === 'manager' ? 'active' : '' ?>">
                <i class="bx bx-briefcase"></i> As Production Manager (<?= isset($stats['as_manager']) ? $stats['as_manager'] : 0 ?>)
            </a>
            <a href="<?=ROOT?>/artistdashboard?tab=actor" class="nav-tab-btn <?= $activeTab === 'actor' ? 'active' : '' ?>">
                <i class="bx bx-user-tie"></i> As Actor (<?= isset($stats['as_actor']) ? $stats['as_actor'] : 0 ?>)
            </a>
            <a href="<?=ROOT?>/artistdashboard?tab=interviews" class="nav-tab-btn <?= $activeTab === 'interviews' ? 'active' : '' ?>">
                <i class="bx bx-calendar-check"></i> View Interview Schedules (<?= isset($stats['upcoming_interviews']) ? $stats['upcoming_interviews'] : 0 ?>)
            </a>
            <a href="<?=ROOT?>/artistdashboard?tab=requests" class="nav-tab-btn <?= $activeTab === 'requests' ? 'active' : '' ?>">
                <i class="bx bx-envelope"></i> Requests 
                (<?= (isset($stats['pending_requests']) ? $stats['pending_requests'] : 0) + (isset($stats['pending_pm_requests']) ? $stats['pending_pm_requests'] : 0) ?>)
            </a>
        </div>

        <!-- Tabs for Drama Categories -->
        <div class="content">
            <div class="profile-container artist-profile-single-column">
                <div class="details">

                <!-- As Director Tab -->
                <div id="director-tab" class="tab-content <?= $activeTab === 'director' ? 'active' : '' ?>">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-film"></i> Dramas You're Directing</span>
                            <?php if (isset($dramas_as_director) && !empty($dramas_as_director)): ?>
                                <a href="<?=ROOT?>/createDrama" class="btn btn-primary btn-compact">
                                    <i class="bx bx-plus"></i> Create New Drama
                                </a>
                            <?php endif; ?>
                        </h3>
                    <?php if (!isset($dramas_as_director) || empty($dramas_as_director)): ?>
                        <div class="no-results">
                            <i class="bx bx-film"></i>
                            <h3>No Dramas Yet</h3>
                            <p>You haven't created any dramas. Start your journey as a director!</p>
                            <a class="btn btn-primary btn-compact artist-mt-16" href="<?=ROOT?>/createDrama">
                                <i class="bx bx-plus"></i> Create Drama
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_director as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header">
                                        <h3 class="artist-name"><?= esc($drama->drama_name ?? 'Registered Drama') ?></h3>
                                        <p class="artist-experience">Certificate <?= esc($drama->certificate_number ?? 'N/A') ?></p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Owner:</span>
                                            <span class="info-value"><?= esc($drama->owner_name ?? 'Not recorded') ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Created:</span>
                                            <span class="info-value"><?= isset($drama->created_at) ? date('M d, Y', strtotime($drama->created_at)) : 'N/A' ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Public:</span>
                                            <span class="info-value">
                                                <?php if (!empty($drama->is_published)): ?>
                                                    <span class="status-badge assigned">Published</span>
                                                <?php else: ?>
                                                    <span class="status-badge pending">Not Published</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Certificate Image:</span>
                                            <span class="info-value">
                                                <?php if (!empty($drama->certificate_image)): ?>
                                                    <a href="<?= ROOT ?>/uploads/certificates/<?= esc(rawurlencode($drama->certificate_image)) ?>" target="_blank" class="director-cert-link">
                                                        View
                                                    </a>
                                                <?php else: ?>
                                                    <span class="status-badge pending">Pending</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <a class="btn btn-primary artist-fill-link" href="<?=ROOT?>/director/dashboard?drama_id=<?= (int)$drama->id ?>">
                                            <i class="bx bx-tachometer-alt"></i> Manage
                                        </a>
                                        <a class="btn btn-director-publish artist-fill-link" href="<?= ROOT ?>/director/drama_details?drama_id=<?= (int)$drama->id ?>#publish-section">
                                            <i class="bx bx-bullhorn"></i> <?= !empty($drama->is_published) ? 'Update Publish' : 'Publish' ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- As Production Manager Tab -->
                <div id="manager-tab" class="tab-content <?= $activeTab === 'manager' ? 'active' : '' ?>">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-briefcase"></i> Dramas You're Managing as Production Manager</span>
                        </h3>
                    <?php if (!isset($dramas_as_manager) || empty($dramas_as_manager)): ?>
                        <div class="no-results">
                            <i class="bx bx-briefcase"></i>
                            <h3>No Production Manager Roles</h3>
                            <p>You haven't been assigned as a production manager for any dramas yet.</p>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($dramas_as_manager as $drama): ?>
                                <div class="artist-card">
                                    <div class="artist-header artist-manager-header">
                                        <h3 class="artist-name"><?= esc($drama->drama_name ?? 'Drama') ?></h3>
                                        <p class="artist-experience"><?= esc($drama->description ?? 'Production Manager') ?></p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Director:</span>
                                            <span class="info-value"><?= esc($drama->creator_name ?? 'Unknown') ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Language:</span>
                                            <span class="info-value"><?= esc($drama->language ?? 'Sinhala') ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Status:</span>
                                            <span class="info-value">
                                                <span class="status-badge <?= $drama->status === 'active' ? 'assigned' : 'pending' ?>">
                                                    <?= esc(ucfirst($drama->status)) ?>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <a class="btn btn-primary artist-fill-link" href="<?=ROOT?>/Production_manager/dashboard?drama_id=<?= (int)$drama->id ?>">
                                            <i class="bx bx-tasks"></i> Manage
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- As Actor Tab -->
                <div id="actor-tab" class="tab-content <?= $activeTab === 'actor' ? 'active' : '' ?>">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-user-tie"></i> Your Acting Roles</span>
                        </h3>
                    <?php if (!isset($roles_as_actor) || empty($roles_as_actor)): ?>
                        <div class="no-results">
                            <i class="bx bx-user-tie"></i>
                            <h3>No Acting Roles</h3>
                            <p>You haven't been cast in any roles yet. Browse available vacancies!</p>
                            <a class="btn btn-primary btn-compact artist-mt-16" href="<?=ROOT?>/artistdashboard/browse_vacancies">
                                <i class="bx bx-search"></i> Browse Vacancies
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="artists-grid">
                            <?php foreach ($roles_as_actor as $role): ?>
                                <div class="artist-card">
                                    <div class="artist-header artist-actor-header">
                                        <h3 class="artist-name artist-actor-name"><?= esc($role->role_name) ?></h3>
                                        <p class="artist-experience"><?= esc(ucfirst($role->role_type)) ?> Role</p>
                                    </div>
                                    <div class="artist-body">
                                        <div class="info-row">
                                            <span class="info-label">Drama:</span>
                                            <span class="info-value artist-brand-text">
                                                <strong><?= esc($role->drama_name) ?></strong>
                                            </span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Director:</span>
                                            <span class="info-value"><?= esc($role->director_name ?? 'Unknown') ?></span>
                                        </div>
                                        <?php if (!empty($role->salary)): ?>
                                        <div class="info-row">
                                            <span class="info-label">Salary:</span>
                                            <span class="info-value">LKR <?= number_format($role->salary) ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="info-row">
                                            <span class="info-label">Assigned:</span>
                                            <span class="info-value"><?= date('M d, Y', strtotime($role->assigned_at)) ?></span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">Status:</span>
                                            <span class="info-value">
                                                <span class="status-badge assigned">
                                                    <i class="bx bx-check-circle"></i> Active
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="artist-footer">
                                        <a class="btn btn-primary artist-fill-link" href="<?=ROOT?>/artistdashboard/view_drama?drama_id=<?= (int)$role->drama_id ?>">
                                            <i class="bx bx-eye"></i> View Drama
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- View Interview Schedules Tab -->
                <div id="interviews-tab" class="tab-content <?= $activeTab === 'interviews' ? 'active' : '' ?>">
                    <div class="card-section">
                        <h3>
                            <span><i class="bx bx-calendar-check"></i> View Interview Schedules</span>
                        </h3>
                        <?php if (isset($upcoming_interviews) && !empty($upcoming_interviews)): ?>
                            <p class="artist-interview-intro">Confirm your participation so the director knows you are joining.</p>
                            <div class="artist-grid-gap-16">
                                <?php foreach ($upcoming_interviews as $application): ?>
                                    <?php
                                        $interviewTime = date('M d, Y g:i A', strtotime($application->interview_at));
                                        $confirmationStatus = strtolower($application->interview_confirmation_status ?? 'pending');
                                        $statusClassMap = [
                                            'confirmed' => 'artist-status-confirmed',
                                            'declined' => 'artist-status-declined',
                                            'pending' => 'artist-status-pending',
                                        ];
                                        $badgeClass = $statusClassMap[$confirmationStatus] ?? $statusClassMap['pending'];
                                    ?>
                                    <div class="role-info-card artist-interview-card">
                                        <div class="artist-flex-between-wrap-12">
                                            <div>
                                                <h4 class="artist-m0"><?= esc($application->role_name ?? 'Role') ?> <small class="artist-muted artist-normal-weight">in <?= esc($application->drama_name ?? 'Drama') ?></small></h4>
                                                <div class="artist-muted-small">
                                                    Directed by <?= esc($application->director_name ?? 'Director') ?>
                                                </div>
                                            </div>
                                            <span class="status-badge <?= esc($badgeClass) ?>">
                                                <?= esc($confirmationStatus) ?>
                                            </span>
                                        </div>
                                        <div class="role-info-item artist-role-item-mt-12">
                                            <span class="role-info-label"><i class="bx bx-calendar"></i> Interview:</span>
                                            <span class="role-info-value"><?= esc($interviewTime) ?></span>
                                        </div>
                                        <?php if (!empty($application->interview_notes)): ?>
                                            <div class="artist-note-block">
                                                <strong>Director notes:</strong>
                                                <p class="artist-note-copy"><?= nl2br(esc($application->interview_notes)) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($confirmationStatus === 'pending'): ?>
                                            <form method="POST" action="<?= ROOT ?>/artistdashboard/confirm_interview" class="interview-response artist-interview-form">
                                                <input type="hidden" name="application_id" value="<?= (int)$application->id ?>">
                                                <label class="artist-label-muted-small">Send an optional note to the director</label>
                                                <textarea name="note" rows="2" class="form-control" placeholder="Add details about your availability (optional)"></textarea>
                                                <div class="artist-actions-wrap-12">
                                                    <button type="submit" name="response" value="confirm" class="btn btn-success artist-btn-flex-min140">
                                                        <i class="bx bx-check"></i> Confirm Attendance
                                                    </button>
                                                    <button type="submit" name="response" value="decline" class="btn btn-danger artist-btn-flex-min120">
                                                        <i class="bx bx-times"></i> Decline
                                                    </button>
                                                </div>
                                            </form>
                                        <?php else: ?>
                                            <div class="artist-response-meta">
                                                Response sent <?= !empty($application->interview_confirmed_at) ? esc(date('M d, Y g:i A', strtotime($application->interview_confirmed_at))) : 'recently' ?>
                                                <?php if (!empty($application->interview_confirmation_note)): ?>
                                                    <div class="artist-response-note">"<?= esc($application->interview_confirmation_note) ?>"</div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="bx bx-calendar-check"></i>
                                <h3>No Interview Schedules</h3>
                                <p>You don't have any upcoming interview schedules at the moment.</p>
                                <a class="btn btn-primary btn-compact artist-mt-16" href="<?=ROOT?>/artistdashboard/browse_vacancies">
                                    <i class="bx bx-search"></i> Browse Vacancies
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- My Showings Tab -->
                <div id="my-showings-tab" class="tab-content <?= $activeTab === 'showings' ? 'active' : '' ?>">
                    <h3 class="artist-tab-title">
                        <i class="bx bx-calendar-event"></i> My Showings
                    </h3>

                    <div class="classes-subtabs" role="tablist" aria-label="My showings tabs">
                        <a href="<?=ROOT?>/artistdashboard?tab=showings&showings_tab=requests" class="classes-subtab-btn <?= $activeShowingsTab === 'requests' ? 'active' : '' ?>" role="tab" aria-selected="<?= $activeShowingsTab === 'requests' ? 'true' : 'false' ?>">
                            <i class="bx bx-time-five"></i> Showings Requests (<?= isset($show_requests_pending) ? count($show_requests_pending) : 0 ?>)
                        </a>
                        <a href="<?=ROOT?>/artistdashboard?tab=showings&showings_tab=accepted" class="classes-subtab-btn <?= $activeShowingsTab === 'accepted' ? 'active' : '' ?>" role="tab" aria-selected="<?= $activeShowingsTab === 'accepted' ? 'true' : 'false' ?>">
                            <i class="bx bx-check-circle"></i> Accepted Showings (<?= isset($show_requests_accepted) ? count($show_requests_accepted) : 0 ?>)
                        </a>
                        <a href="<?=ROOT?>/artistdashboard?tab=showings&showings_tab=rejected" class="classes-subtab-btn <?= $activeShowingsTab === 'rejected' ? 'active' : '' ?>" role="tab" aria-selected="<?= $activeShowingsTab === 'rejected' ? 'true' : 'false' ?>">
                            <i class="bx bx-x-circle"></i> Rejected Showings (<?= isset($show_requests_rejected) ? count($show_requests_rejected) : 0 ?>)
                        </a>
                    </div>

                    <div class="classes-subtab-panel <?= $activeShowingsTab === 'requests' ? 'active' : '' ?>" data-showings-panel="requests" role="tabpanel">
                    <?php if (!empty($show_requests_pending)): ?>
                        <div class="artist-grid-gap-16-mb18">
                            <?php foreach ($show_requests_pending as $show_request): ?>
                                <?php
                                    $requestDetails = [];
                                    if (!empty($show_request->request_details_json)) {
                                        $decoded = json_decode((string)$show_request->request_details_json, true);
                                        if (is_array($decoded)) {
                                            $requestDetails = $decoded;
                                        }
                                    }
                                    $requestedVenue = trim((string)($requestDetails['request_venue'] ?? 'Not specified'));
                                    $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
                                    $requestedShowTime = trim((string)($requestDetails['show_time'] ?? ''));
                                    $requestedSchedule = trim((string)($requestDetails['show_datetime'] ?? ''));
                                    $requestedStartRaw = trim((string)($requestDetails['request_start_at'] ?? ''));
                                    $requestedEndRaw = trim((string)($requestDetails['request_end_at'] ?? ''));
                                    $requestSenderName = trim((string)($requestDetails['request_sender_name'] ?? ($show_request->audience_name ?? 'Audience User')));
                                    $requestContactPhone = trim((string)($requestDetails['request_contact_phone'] ?? ($show_request->audience_phone ?? 'Not provided')));
                                    $requestContactEmail = trim((string)($requestDetails['request_contact_email'] ?? ($show_request->audience_email ?? 'Not provided')));
                                    $requestedStart = $requestedStartRaw !== '' ? date('M d, Y g:i A', strtotime($requestedStartRaw)) : 'Not specified';
                                    $requestedEnd = $requestedEndRaw !== '' ? date('M d, Y g:i A', strtotime($requestedEndRaw)) : 'Not specified';
                                    $requestedShowDate = $requestedShowDateRaw !== '' ? date('M d, Y', strtotime($requestedShowDateRaw)) : 'Not specified';
                                    if ($requestedShowTime === '' && $requestedSchedule !== '') {
                                        $timeParts = preg_split('/\s+/', $requestedSchedule, 2);
                                        $requestedShowTime = trim((string)($timeParts[1] ?? ''));
                                    }
                                    if ($requestedShowTime === '') {
                                        $requestedShowTime = 'Not specified';
                                    }
                                    if ($requestedSchedule === '' && ($requestedStartRaw !== '' || $requestedEndRaw !== '')) {
                                        $requestedSchedule = trim($requestedStart . ' to ' . $requestedEnd);
                                    }
                                    if ($requestedSchedule === '') {
                                        $requestedSchedule = 'Not specified';
                                    }
                                    $pendingShowDateForMatch = $requestedShowDateRaw !== '' ? date('Y-m-d', strtotime($requestedShowDateRaw)) : '';
                                    $pendingShowTimeForMatch = trim((string)($requestDetails['show_time_start'] ?? ($requestDetails['show_time'] ?? '')));
                                    $pendingShowTimeEndForMatch = trim((string)($requestDetails['show_time_end'] ?? ''));
                                    $presentCount = (int)($requestDetails['present_count'] ?? 0);
                                    $requestNotes = trim((string)($requestDetails['request_notes'] ?? ''));
                                ?>
                                <?php
                                    $pendingSlotKey = $pendingShowDateForMatch . '|' . $pendingShowTimeForMatch . '|' . $pendingShowTimeEndForMatch;
                                    $pendingConflictCount = ($pendingShowDateForMatch !== '' && $pendingShowTimeForMatch !== '' && isset($acceptedSlotCounts[$pendingSlotKey]))
                                        ? (int)$acceptedSlotCounts[$pendingSlotKey]
                                        : 0;
                                ?>
                                <div class="role-info-card pending-showing-card">
                                    <div class="showing-card-header">
                                        <div>
                                            <h3 class="showing-card-title"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p class="showing-card-audience"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge requested"><i class="bx bx-time"></i> Pending</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-map"></i> Requested Place:</span><span class="role-info-value"><?= esc($requestedVenue) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-user"></i> Sender Name:</span><span class="role-info-value"><?= esc($requestSenderName !== '' ? $requestSenderName : 'Audience User') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-phone"></i> Contact Phone:</span><span class="role-info-value"><?= esc($requestContactPhone !== '' ? $requestContactPhone : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-envelope"></i> Contact Email:</span><span class="role-info-value"><?= esc($requestContactEmail !== '' ? $requestContactEmail : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate !== 'Not specified' ? $requestedShowDate : $requestedSchedule) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime) ?></span></div>
                                    <?php if ($pendingConflictCount > 0): ?>
                                        <div class="pending-slot-conflict artist-accepted-empty-visible">
                                            <i class="bx bx-error-circle"></i> This requested slot matches <?= (int)$pendingConflictCount ?> accepted showing(s).
                                        </div>
                                    <?php endif; ?>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-group"></i> Expected Present Count:</span><span class="role-info-value"><?= $presentCount > 0 ? (int)$presentCount : 'Not specified' ?></span></div>
                                    <?php if ($requestNotes !== ''): ?>
                                        <div class="artist-note-panel">
                                            <strong class="artist-note-title"><i class="bx bx-note"></i> Additional Notes:</strong>
                                            <p class="artist-note-text"><?= esc($requestNotes) ?></p>
                                        </div>
                                    <?php endif; ?>
                                    <div class="artist-actions-wrap-10">
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_show_request" class="artist-form-flex-min180">
                                            <input type="hidden" name="request_id" value="<?= (int)$show_request->id ?>">
                                            <input type="hidden" name="response" value="accept">
                                            <button type="submit" class="btn btn-success artist-btn-full"><i class="bx bx-check"></i> Accept Show</button>
                                        </form>
                                        <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_show_request" class="artist-form-flex-min220-grid">
                                            <input type="hidden" name="request_id" value="<?= (int)$show_request->id ?>">
                                            <input type="hidden" name="response" value="reject">
                                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Add reason for rejection" required></textarea>
                                            <button type="submit" class="btn btn-danger artist-btn-full"><i class="bx bx-x"></i> Reject With Reason</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results artist-no-results-mb18">
                            <i class="bx bx-inbox"></i>
                            <h3>No Pending Show Requests</h3>
                            <p>No audience show requests are waiting for your decision.</p>
                        </div>
                    <?php endif; ?>
                    </div>

                    <div class="classes-subtab-panel <?= $activeShowingsTab === 'accepted' ? 'active' : '' ?>" data-showings-panel="accepted" role="tabpanel">
                    <?php if (!empty($showRequestsAcceptedList)): ?>
                        <form class="accepted-showings-toolbar" method="GET" action="<?=ROOT?>/artistdashboard">
                            <input type="hidden" name="tab" value="showings">
                            <input type="hidden" name="showings_tab" value="accepted">
                            <div>
                                <label for="accepted-filter-date">Filter by Date</label>
                                <input type="date" id="accepted-filter-date" name="accepted_date" value="<?= esc($acceptedFilterDate) ?>" />
                            </div>
                            <div>
                                <label for="accepted-filter-start-time">Filter by Start Time</label>
                                <input type="time" id="accepted-filter-start-time" name="accepted_start_time" value="<?= esc($acceptedFilterStartTime) ?>" />
                            </div>
                            <div>
                                <label for="accepted-filter-end-time">Filter by End Time</label>
                                <input type="time" id="accepted-filter-end-time" name="accepted_end_time" value="<?= esc($acceptedFilterEndTime) ?>" />
                            </div>
                            <button type="submit" class="btn btn-secondary">
                                <i class="bx bx-filter-alt"></i> Apply Filter
                            </button>
                            <a href="<?=ROOT?>/artistdashboard?tab=showings&showings_tab=accepted" class="btn btn-secondary artist-text-center">
                                <i class="bx bx-reset"></i> Clear Filter
                            </a>
                        </form>
                        <?php if (empty($filteredAcceptedShowRequests)): ?>
                            <div class="accepted-showings-empty artist-accepted-empty-visible">
                                No accepted showings found for the selected date/time filter.
                            </div>
                        <?php endif; ?>
                        <div class="artist-grid-gap-16-mb18">
                            <?php foreach ($filteredAcceptedShowRequests as $show_request): ?>
                                <?php
                                    $requestDetails = [];
                                    if (!empty($show_request->request_details_json)) {
                                        $decoded = json_decode((string)$show_request->request_details_json, true);
                                        if (is_array($decoded)) {
                                            $requestDetails = $decoded;
                                        }
                                    }
                                    $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
                                    $requestedShowDate = $requestedShowDateRaw !== '' ? date('M d, Y', strtotime($requestedShowDateRaw)) : 'Not specified';
                                    $requestedShowTime = trim((string)($requestDetails['show_time'] ?? 'Not specified'));
                                    $requestSenderName = trim((string)($requestDetails['request_sender_name'] ?? ($show_request->audience_name ?? 'Audience User')));
                                    $requestContactPhone = trim((string)($requestDetails['request_contact_phone'] ?? ($show_request->audience_phone ?? 'Not provided')));
                                    $requestContactEmail = trim((string)($requestDetails['request_contact_email'] ?? ($show_request->audience_email ?? 'Not provided')));
                                ?>
                                <div class="role-info-card accepted-showing-card">
                                    <div class="showing-card-header">
                                        <div>
                                            <h3 class="showing-card-title"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p class="showing-card-audience"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge success"><i class="bx bx-check-circle"></i> Accepted</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-user"></i> Sender Name:</span><span class="role-info-value"><?= esc($requestSenderName !== '' ? $requestSenderName : 'Audience User') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-phone"></i> Contact Phone:</span><span class="role-info-value"><?= esc($requestContactPhone !== '' ? $requestContactPhone : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-envelope"></i> Contact Email:</span><span class="role-info-value"><?= esc($requestContactEmail !== '' ? $requestContactEmail : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime !== '' ? $requestedShowTime : 'Not specified') ?></span></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results artist-no-results-mb18"><i class="bx bx-check-shield"></i><h3>No Accepted Showings</h3><p>You have not accepted any audience showings yet.</p></div>
                    <?php endif; ?>
                    </div>

                    <div class="classes-subtab-panel <?= $activeShowingsTab === 'rejected' ? 'active' : '' ?>" data-showings-panel="rejected" role="tabpanel">
                    <?php if (!empty($show_requests_rejected)): ?>
                        <div class="artist-grid-gap-16-mb10">
                            <?php foreach ($show_requests_rejected as $show_request): ?>
                                <?php
                                    $requestDetails = [];
                                    if (!empty($show_request->request_details_json)) {
                                        $decoded = json_decode((string)$show_request->request_details_json, true);
                                        if (is_array($decoded)) {
                                            $requestDetails = $decoded;
                                        }
                                    }
                                    $requestedShowDateRaw = trim((string)($requestDetails['show_date'] ?? ''));
                                    $requestedShowDate = $requestedShowDateRaw !== '' ? date('M d, Y', strtotime($requestedShowDateRaw)) : 'Not specified';
                                    $requestedShowTime = trim((string)($requestDetails['show_time'] ?? 'Not specified'));
                                    $requestSenderName = trim((string)($requestDetails['request_sender_name'] ?? ($show_request->audience_name ?? 'Audience User')));
                                    $requestContactPhone = trim((string)($requestDetails['request_contact_phone'] ?? ($show_request->audience_phone ?? 'Not provided')));
                                    $requestContactEmail = trim((string)($requestDetails['request_contact_email'] ?? ($show_request->audience_email ?? 'Not provided')));
                                    $rejectionReason = trim((string)($show_request->rejection_reason ?? ''));
                                ?>
                                <div class="role-info-card rejected-showing-card">
                                    <div class="showing-card-header">
                                        <div>
                                            <h3 class="showing-card-title"><i class="bx bx-film"></i> <?= esc($show_request->drama_title ?? 'Drama') ?></h3>
                                            <p class="showing-card-audience"><strong>Audience:</strong> <?= esc($show_request->audience_name ?? 'Audience User') ?></p>
                                        </div>
                                        <span class="status-badge danger"><i class="bx bx-x-circle"></i> Rejected</span>
                                    </div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-user"></i> Sender Name:</span><span class="role-info-value"><?= esc($requestSenderName !== '' ? $requestSenderName : 'Audience User') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-phone"></i> Contact Phone:</span><span class="role-info-value"><?= esc($requestContactPhone !== '' ? $requestContactPhone : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-envelope"></i> Contact Email:</span><span class="role-info-value"><?= esc($requestContactEmail !== '' ? $requestContactEmail : 'Not provided') ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-calendar"></i> Show Date:</span><span class="role-info-value"><?= esc($requestedShowDate) ?></span></div>
                                    <div class="role-info-item"><span class="role-info-label"><i class="bx bx-time-five"></i> Show Time:</span><span class="role-info-value"><?= esc($requestedShowTime !== '' ? $requestedShowTime : 'Not specified') ?></span></div>
                                    <div class="artist-rejection-block">
                                        <strong class="artist-rejection-title"><i class="bx bx-error-circle"></i> Rejection Reason:</strong>
                                        <p class="artist-rejection-text"><?= esc($rejectionReason !== '' ? $rejectionReason : 'No reason provided.') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results artist-no-results-mb10"><i class="bx bx-smile"></i><h3>No Rejected Showings</h3><p>No rejected audience show requests found.</p></div>
                    <?php endif; ?>
                    </div>
                </div>

                <!-- Requests Tab -->
                <div id="requests-tab" class="tab-content <?= $activeTab === 'requests' ? 'active' : '' ?>">
                    <?php
                        $hasPmRequests = isset($pm_requests) && !empty($pm_requests);
                        $hasActorRequests = isset($role_requests) && !empty($role_requests);
                    ?>

                    <!-- Category: PM Requests -->
                    <div class="card-section artist-card-section-mb24">
                        <h3 class="artist-heading-ink-mb20">
                            <i class="bx bx-user-tie"></i> PM Requests
                            <span class="artist-count-label">
                                (<?= isset($pm_requests) ? count($pm_requests) : 0 ?>)
                            </span>
                        </h3>

                        <?php if ($hasPmRequests): ?>
                            <div class="artist-grid-gap-16">
                                <?php foreach ($pm_requests as $pm_request): ?>
                                    <div class="role-info-card">
                                        <div class="artist-flex-between-start-mb16">
                                            <div>
                                                <h3 class="artist-brand-heading-mb8">
                                                    <i class="bx bx-film"></i> <?= esc($pm_request->drama_name) ?>
                                                </h3>
                                                <p class="artist-muted-small">
                                                    <strong>Director:</strong> <?= esc($pm_request->director_name) ?>
                                                </p>
                                                <p class="artist-muted-small">
                                                    <strong>Certificate:</strong> <?= esc($pm_request->certificate_number) ?>
                                                </p>
                                            </div>
                                            <span class="status-badge requested">
                                                <i class="bx bx-clock"></i> Pending
                                            </span>
                                        </div>
                                        
                                        <div class="role-info-item">
                                            <span class="role-info-label">
                                                <i class="bx bx-briefcase"></i> Position:
                                            </span>
                                            <span class="role-info-value">Production Manager</span>
                                        </div>
                                        
                                        <?php if (!empty($pm_request->message)): ?>
                                            <div class="artist-note-panel">
                                                <strong class="artist-note-title"><i class="bx bx-comment"></i> Message from Director:</strong>
                                                <p class="artist-note-text"><?= esc($pm_request->message) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="role-info-item">
                                            <span class="role-info-label">
                                                <i class="bx bx-calendar"></i> Requested:
                                            </span>
                                            <span class="role-info-value"><?= date('M d, Y g:i A', strtotime($pm_request->requested_at)) ?></span>
                                        </div>
                                        
                                        <div class="artist-info-note">
                                            <p class="artist-info-note-copy">
                                                <i class="bx bx-info-circle"></i> <strong>About this role:</strong> 
                                                As Production Manager, you'll oversee services, budget management, and theater bookings for this drama.
                                            </p>
                                        </div>
                                        
                                        <div class="artist-actions-row">
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" class="artist-form-flex">
                                                <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                                <input type="hidden" name="response" value="accept">
                                                <button type="submit" class="btn btn-success artist-btn-full">
                                                    <i class="bx bx-check"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_manager_request" class="artist-form-flex">
                                                <input type="hidden" name="request_id" value="<?= $pm_request->id ?>">
                                                <input type="hidden" name="response" value="reject">
                                                <button type="submit" class="btn btn-danger artist-btn-full">
                                                    <i class="bx bx-times"></i> Decline
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-results">
                                <i class="bx bx-inbox"></i>
                                <h3>No Pending PM Requests</h3>
                                <p>You don't have any production manager requests right now.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Category: Actor Requests -->
                    <div class="card-section">
                        <h3 class="artist-heading-ink-mb20">
                            <i class="bx bx-theater-masks"></i> Actor Requests
                            <span class="artist-count-label">
                                (<?= isset($role_requests) ? count($role_requests) : 0 ?>)
                            </span>
                        </h3>

                        <?php if (!$hasActorRequests): ?>
                            <div class="no-results">
                                <i class="bx bx-inbox"></i>
                                <h3>No Pending Actor Requests</h3>
                                <p>You don't have any actor role requests at the moment.</p>
                            </div>
                        <?php else: ?>
                            <div class="artist-grid-gap-16">
                                <?php foreach ($role_requests as $request): ?>
                                    <div class="role-info-card">
                                        <div class="artist-flex-between-start-mb16">
                                            <div>
                                                <h3 class="artist-heading-ink-mb20">
                                                    <i class="bx bx-theater-masks"></i> <?= esc($request->drama_name) ?>
                                                </h3>
                                                <p class="artist-muted-small">
                                                    <strong>Director:</strong> <?= esc($request->director_name) ?>
                                                </p>
                                            </div>
                                            <span class="status-badge requested">
                                                <i class="bx bx-clock"></i> Pending
                                            </span>
                                        </div>
                                        
                                        <div class="role-info-item">
                                            <span class="role-info-label">
                                                <i class="bx bx-user-tag"></i> Role:
                                            </span>
                                            <span class="role-info-value"><?= esc($request->role_name) ?></span>
                                        </div>
                                        
                                        <?php if (!empty($request->role_description)): ?>
                                            <div class="artist-description-panel">
                                                <strong class="artist-note-title">Description:</strong>
                                                <p class="artist-note-text"><?= esc($request->role_description) ?></p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($request->salary)): ?>
                                            <div class="role-info-item">
                                                <span class="role-info-label">
                                                    <i class="bx bx-money-bill-wave"></i> Salary:
                                                </span>
                                                <span class="role-info-value">LKR <?= number_format($request->salary) ?></span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="role-info-item">
                                            <span class="role-info-label">
                                                <i class="bx bx-calendar"></i> Requested:
                                            </span>
                                            <span class="role-info-value"><?= isset($request->requested_at) && $request->requested_at ? date('M d, Y', strtotime($request->requested_at)) : 'N/A' ?></span>
                                        </div>
                                        
                                        <div class="artist-actions-row">
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" class="artist-form-flex">
                                                <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                                <input type="hidden" name="response" value="accept">
                                                <button type="submit" class="btn btn-success artist-btn-full">
                                                    <i class="bx bx-check"></i> Accept Role
                                                </button>
                                            </form>
                                            <form method="POST" action="<?=ROOT?>/artistdashboard/respond_to_request" class="artist-form-flex">
                                                <input type="hidden" name="request_id" value="<?= $request->id ?>">
                                                <input type="hidden" name="response" value="reject">
                                                <button type="submit" class="btn btn-danger artist-btn-full">
                                                    <i class="bx bx-times"></i> Decline
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                </div>
            </div>
        </div>
    </main>

</body>
</html>
