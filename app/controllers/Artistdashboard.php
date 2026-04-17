<?php

class Artistdashboard
{
    use Controller;
    protected $payHereHelper = null;

    public function __construct()
    {
        try {
            $this->payHereHelper = new PayHereHelper();
        } catch (Throwable $e) {
            $this->payHereHelper = null;
            error_log('Artistdashboard PayHereHelper init failed: ' . $e->getMessage());
        }
    }

    public function index()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $artist_model = $this->getModel('M_artist');
        $drama_model = $this->getModel('M_drama');
        $pm_model = $this->getModel('M_production_manager');
        $role_model = $this->getModel('M_role');
        $class_model = $this->getModel('M_class');
        $show_booking_model = $this->getModel('M_audience_show_booking');
        
        $user_id = $_SESSION['user_id'];
        
        // Get artist profile data
        $data['user'] = $artist_model->get_artist_by_id($user_id);
        $data['profileImageSrc'] = $this->buildProfileImageSrc($data['user'] ?? null);
        
        // Get dramas where user is the director (creator)
        $data['dramas_as_director'] = $drama_model->get_dramas_by_director($user_id);
        
        // Get dramas where user is a production manager
        $data['dramas_as_manager'] = $drama_model->get_dramas_by_manager($user_id);
        
        // Get role assignments where user is cast as an actor (each role, not each drama)
        $data['roles_as_actor'] = $role_model ? $role_model->getAssignmentsByArtist($user_id) : [];

        $data['my_applications'] = $role_model ? $role_model->getArtistApplications($user_id) : [];
        $nowTs = time();
        $data['upcoming_interviews'] = array_values(array_filter($data['my_applications'], function ($app) use ($nowTs) {
            if (!isset($app->interview_at) || $app->interview_at === null || strtolower($app->status ?? '') !== 'pending') {
                return false;
            }

            $interviewTs = strtotime((string)$app->interview_at);
            if ($interviewTs === false) {
                return false;
            }

            // Keep only upcoming interviews (past dates are hidden from this tab)
            return $interviewTs >= $nowTs;
        }));
        
        // Get pending role requests for this artist
        $data['role_requests'] = $artist_model->get_pending_role_requests($user_id);
        
        // Get pending PM requests for this artist
        $data['pm_requests'] = $pm_model ? $pm_model->getPendingRequestsForArtist($user_id) : [];

        // Get audience show requests for dramas directed by this artist
        $data['show_requests'] = $show_booking_model ? $show_booking_model->getShowRequestsByArtist($user_id, ['pending', 'accepted', 'rejected']) : [];
        $data['show_requests_pending'] = [];
        $data['show_requests_accepted'] = [];
        $data['show_requests_rejected'] = [];

        foreach ($data['show_requests'] as $showRequest) {
            $status = strtolower((string)($showRequest->booking_status ?? 'pending'));
            if ($status === 'accepted') {
                $data['show_requests_accepted'][] = $showRequest;
            } elseif ($status === 'rejected') {
                $data['show_requests_rejected'][] = $showRequest;
            } else {
                $data['show_requests_pending'][] = $showRequest;
            }
        }

        $data = array_merge($data, $this->buildDashboardViewState($data['show_requests_accepted']));
        
        // Get total published vacancies count
        $data['total_published_vacancies'] = $role_model ? $role_model->countPublishedVacancies() : 0;

        $data['my_classes'] = $class_model ? $class_model->getClassesByCreator($user_id) : [];
        $data['available_classes'] = $class_model ? $class_model->getPublishedClasses($user_id) : [];
        $data['enrolled_classes'] = $class_model ? $class_model->getEnrolledClassesByUser($user_id) : [];
        
        // Count statistics
        $data['stats'] = [
            'total_dramas' => count($data['dramas_as_director']) + count($data['dramas_as_manager']) + count($data['roles_as_actor']),
            'as_director' => count($data['dramas_as_director']),
            'as_manager' => count($data['dramas_as_manager']),
            'as_actor' => count($data['roles_as_actor']),
            'upcoming_interviews' => count($data['upcoming_interviews']),
            'pending_requests' => count($data['role_requests']),
            'pending_pm_requests' => count($data['pm_requests']),
            'pending_show_requests' => count($data['show_requests_pending']),
            'accepted_show_requests' => count($data['show_requests_accepted']),
            'rejected_show_requests' => count($data['show_requests_rejected']),
            'classes_created' => count($data['my_classes']),
            'classes_enrolled' => count($data['enrolled_classes'])
        ];
        
        $this->view('artistdashboard', $data);
    }

    private function buildDashboardViewState(array $showRequestsAccepted): array
    {
        $requestPath = strtolower((string)(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? ''));
        $requestedTab = strtolower(trim((string)($_GET['tab'] ?? '')));
        $tabAliases = ['my-showings' => 'showings'];
        $activeTab = $tabAliases[$requestedTab] ?? $requestedTab;
        $allowedTabs = ['director', 'manager', 'actor', 'interviews', 'requests', 'showings'];
        if (!in_array($activeTab, $allowedTabs, true)) {
            $activeTab = 'director';
        }

        $requestedShowingsTab = strtolower(trim((string)($_GET['showings_tab'] ?? 'requests')));
        $allowedShowingsTabs = ['requests', 'accepted', 'rejected'];
        $activeShowingsTab = in_array($requestedShowingsTab, $allowedShowingsTabs, true) ? $requestedShowingsTab : 'requests';

        $acceptedFilterDate = trim((string)($_GET['accepted_date'] ?? ''));
        $acceptedFilterStartTime = trim((string)($_GET['accepted_start_time'] ?? ''));
        $acceptedFilterEndTime = trim((string)($_GET['accepted_end_time'] ?? ''));
        $selectedStartMins = $this->parseTimeToMinutes($acceptedFilterStartTime);
        $selectedEndMins = $this->parseTimeToMinutes($acceptedFilterEndTime);

        $filteredAcceptedShowRequests = [];
        $acceptedSlotCounts = [];

        foreach ($showRequestsAccepted as $acceptedShowRequest) {
            $requestDetails = [];
            if (!empty($acceptedShowRequest->request_details_json)) {
                $decoded = json_decode((string)$acceptedShowRequest->request_details_json, true);
                if (is_array($decoded)) {
                    $requestDetails = $decoded;
                }
            }

            $slotDate = trim((string)($requestDetails['show_date'] ?? ''));
            $slotStart = trim((string)($requestDetails['show_time_start'] ?? ($requestDetails['show_time'] ?? '')));
            $slotEnd = trim((string)($requestDetails['show_time_end'] ?? ''));

            if ($slotDate !== '' && $slotStart !== '') {
                $slotKey = $slotDate . '|' . $slotStart . '|' . $slotEnd;
                if (!isset($acceptedSlotCounts[$slotKey])) {
                    $acceptedSlotCounts[$slotKey] = 0;
                }
                $acceptedSlotCounts[$slotKey]++;
            }

            $dateMatches = ($acceptedFilterDate === '') || ($slotDate !== '' && $slotDate === $acceptedFilterDate);

            $cardStartMins = $this->parseTimeToMinutes($slotStart);
            $startMatches = ($selectedStartMins === null) || ($cardStartMins !== null && $cardStartMins === $selectedStartMins);

            $cardEndMins = $this->parseTimeToMinutes($slotEnd);
            $endMatches = ($selectedEndMins === null) || ($cardEndMins !== null && $cardEndMins === $selectedEndMins);

            if ($dateMatches && $startMatches && $endMatches) {
                $filteredAcceptedShowRequests[] = $acceptedShowRequest;
            }
        }

        $sidebarActive = [
            'dashboard' => false,
            'notifications' => false,
            'vacancies' => false,
            'classes' => false,
            'showings' => false,
        ];

        if (strpos($requestPath, '/artistdashboard/notifications') !== false) {
            $sidebarActive['notifications'] = true;
        } elseif (strpos($requestPath, '/artistdashboard/browse_vacancies') !== false) {
            $sidebarActive['vacancies'] = true;
        } elseif (strpos($requestPath, '/artistdashboard/classes') !== false) {
            $sidebarActive['classes'] = true;
        } elseif ($activeTab === 'showings') {
            $sidebarActive['showings'] = true;
        } else {
            $sidebarActive['dashboard'] = true;
        }

        return [
            'activeTab' => $activeTab,
            'activeShowingsTab' => $activeShowingsTab,
            'acceptedFilterDate' => $acceptedFilterDate,
            'acceptedFilterStartTime' => $acceptedFilterStartTime,
            'acceptedFilterEndTime' => $acceptedFilterEndTime,
            'showRequestsAcceptedList' => $showRequestsAccepted,
            'filteredAcceptedShowRequests' => $filteredAcceptedShowRequests,
            'acceptedSlotCounts' => $acceptedSlotCounts,
            'sidebarActive' => $sidebarActive,
        ];
    }

    private function buildProfileImageSrc($user): string
    {
        $default = ROOT . '/uploads/profile_images/user_profile.png';
        if (!is_object($user) || empty($user->profile_image)) {
            return $default;
        }

        $storedValue = str_replace('\\', '/', (string)$user->profile_image);
        if (strpos($storedValue, '/') !== false) {
            return ROOT . '/' . ltrim($storedValue, '/');
        }

        return ROOT . '/uploads/profile_images/' . rawurlencode($storedValue);
    }

    private function parseTimeToMinutes($value): ?int
    {
        $input = trim((string)$value);
        if ($input === '') {
            return null;
        }

        if (preg_match('/^(\d{1,2}):(\d{2})$/', $input, $matches)) {
            $hh = (int)$matches[1];
            $mm = (int)$matches[2];
            if ($hh >= 0 && $hh <= 23 && $mm >= 0 && $mm <= 59) {
                return ($hh * 60) + $mm;
            }
        }

        if (preg_match('/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i', $input, $matches)) {
            $hh = (int)$matches[1];
            $mm = (int)$matches[2];
            $meridiem = strtoupper($matches[3]);

            if ($hh === 12) {
                $hh = 0;
            }
            if ($meridiem === 'PM') {
                $hh += 12;
            }

            if ($hh >= 0 && $hh <= 23 && $mm >= 0 && $mm <= 59) {
                return ($hh * 60) + $mm;
            }
        }

        return null;
    }

    public function browse_vacancies()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $role_model = $this->getModel('M_role');
        $artist_model = $this->getModel('M_artist');
        $user_id = $_SESSION['user_id'];

        // Get filter parameters
        $filters = [
            'role_type' => $_GET['role_type'] ?? '',
            'search' => $_GET['search'] ?? '',
            'sort' => $_GET['sort'] ?? 'latest',
            'artist_id' => $user_id  // Exclude roles where artist is already assigned
        ];

        // Get all published vacancies with filters
        $data['vacancies'] = $role_model ? $role_model->getAllPublishedVacancies($filters) : [];
        
        // Get artist's existing applications to check which roles they've applied to
        $data['my_applications'] = $role_model ? $role_model->getArtistApplications($user_id) : [];
        
        // Create a map of role IDs the artist has applied to
        $data['applied_role_ids'] = [];
        foreach ($data['my_applications'] as $app) {
            $data['applied_role_ids'][] = $app->role_id;
        }
        
        $data['filters'] = $filters;
        $data['total_vacancies'] = count($data['vacancies']);
        $data['user'] = $artist_model ? $artist_model->get_artist_by_id($user_id) : null;
        
        $this->view('artist/browse_vacancies', $data);
    }

    public function apply_for_role()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        // Get role ID from query parameter
        $role_id = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;

        if (!$role_id) {
            $_SESSION['message'] = 'Invalid role ID';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard/browse_vacancies");
            exit;
        }

        $role_model = $this->getModel('M_role');
        $artist_model = $this->getModel('M_artist');

        // Get role details
        $data['role'] = $role_model->getRoleDetailsForApplication($role_id);
        
        if (!$data['role']) {
            $_SESSION['message'] = 'Role not found';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard/browse_vacancies");
            exit;
        }

        // Get artist details
        $data['artist'] = $artist_model->get_artist_by_id($_SESSION['user_id']);

        // Show the application form
        $this->view('artist/apply_for_role_form', $data);
    }

    public function submit_application()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . ROOT . "/artistdashboard/browse_vacancies");
            exit;
        }

        $role_id = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
        $cover_letter = trim($_POST['cover_letter'] ?? '');
        $media_links = trim($_POST['media_links'] ?? '');

        $errors = [];

        if (!$role_id) {
            $errors[] = 'Role ID is required';
        }

        if (empty($cover_letter)) {
            $errors[] = 'Cover letter is required';
        }

        if (!empty($errors)) {
            $role_model = $this->getModel('M_role');
            $artist_model = $this->getModel('M_artist');
            
            $data['role'] = $role_model->getRoleDetailsForApplication($role_id);
            $data['artist'] = $artist_model->get_artist_by_id($_SESSION['user_id']);
            $data['errors'] = $errors;
            
            $this->view('artist/apply_for_role_form', $data);
            return;
        }

        $role_model = $this->getModel('M_role');
        $result = $role_model->applyForRole($role_id, $_SESSION['user_id'], $cover_letter, $media_links);

        if ($result['success']) {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = 'success';
            header("Location: " . ROOT . "/artistdashboard/browse_vacancies");
            exit;
        } else {
            $artist_model = $this->getModel('M_artist');
            
            $data['role'] = $role_model->getRoleDetailsForApplication($role_id);
            $data['artist'] = $artist_model->get_artist_by_id($_SESSION['user_id']);
            $data['errors'] = [$result['message']];
            
            $this->view('artist/apply_for_role_form', $data);
        }
    }

    public function my_applications()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $role_model = $this->getModel('M_role');
        $user_id = $_SESSION['user_id'];

        // Get all applications by this artist
        $data['applications'] = $role_model ? $role_model->getArtistApplications($user_id) : [];
        
        // Group by status
        $data['pending'] = array_filter($data['applications'], function($app) {
            return $app->status === 'pending';
        });
        
        $data['accepted'] = array_filter($data['applications'], function($app) {
            return $app->status === 'accepted';
        });
        
        $data['rejected'] = array_filter($data['applications'], function($app) {
            return $app->status === 'rejected';
        });
        
        $this->view('artist/my_applications', $data);
    }

    public function confirm_interview()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/artistdashboard?tab=interviews');
            exit;
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? null) !== 'artist') {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $applicationId = (int)($_POST['application_id'] ?? 0);
        $response = strtolower(trim($_POST['response'] ?? ''));
        $note = trim($_POST['note'] ?? '');

        if ($applicationId <= 0 || !in_array($response, ['confirm', 'decline'], true)) {
            $_SESSION['message'] = 'Invalid interview confirmation request.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard?tab=interviews');
            exit;
        }

        $role_model = $this->getModel('M_role');
        if (!$role_model) {
            $_SESSION['message'] = 'Interview system is unavailable right now.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard?tab=interviews');
            exit;
        }

        $success = $role_model->artistRespondInterview(
            $applicationId,
            (int)$_SESSION['user_id'],
            $response,
            $note !== '' ? $note : null
        );

        if ($success) {
            $message = $response === 'confirm' ? 'Interview confirmed. See you there!' : 'Interview declined.';
            $_SESSION['message'] = $message;
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Unable to update your interview response.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/artistdashboard?tab=interviews');
        exit;
    }
    

    public function respond_to_request()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = $_POST['request_id'] ?? null;
            $response = $_POST['response'] ?? null; // 'accept' or 'reject'
            
            if ($request_id && $response) {
                $artist_model = $this->getModel('M_artist');
                $user_id = $_SESSION['user_id'];
                $result = $artist_model->respond_to_role_request($request_id, $user_id, $response);
                
                if ($result) {
                    $_SESSION['message'] = $response === 'accept' ? 'Role request accepted successfully!' : 'Role request declined.';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Failed to process request.';
                    $_SESSION['message_type'] = 'error';
                }
            }
            
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }
    }

    public function respond_to_manager_request()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
            $response = $_POST['response'] ?? null; // 'accept' or 'reject'
            
            if ($request_id && $response) {
                $pm_model = $this->getModel('M_production_manager');
                $user_id = $_SESSION['user_id'];
                
                if ($response === 'accept') {
                    $result = $pm_model->acceptRequest($request_id, $user_id);
                } else {
                    $response_note = $_POST['response_note'] ?? null;
                    $result = $pm_model->rejectRequest($request_id, $user_id, $response_note);
                }
                
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['message'] = 'Invalid request.';
                $_SESSION['message_type'] = 'error';
            }
            
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }
    }

    public function respond_to_show_request()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/artistdashboard?tab=showings&showings_tab=requests');
            exit;
        }

        $request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
        $response = trim((string)($_POST['response'] ?? ''));
        $reason = trim((string)($_POST['rejection_reason'] ?? ''));

        if ($request_id <= 0 || !in_array($response, ['accept', 'reject'], true)) {
            $_SESSION['message'] = 'Invalid show request response.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard?tab=showings&showings_tab=requests');
            exit;
        }

        $show_booking_model = $this->getModel('M_audience_show_booking');
        if (!$show_booking_model) {
            $_SESSION['message'] = 'Show request system is unavailable right now.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard?tab=showings&showings_tab=requests');
            exit;
        }

        $result = $show_booking_model->respondToShowRequest(
            $request_id,
            (int)$_SESSION['user_id'],
            $response,
            $reason
        );

        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
        header('Location: ' . ROOT . '/artistdashboard?tab=showings&showings_tab=requests');
        exit;
    }

    /**
     * View drama details as an actor (read-only view with drama info, roles, and schedule)
     */
    public function view_drama()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $drama_id = $_GET['drama_id'] ?? null;
        if (!$drama_id) {
            $_SESSION['message'] = 'Drama not found.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $drama_model = $this->getModel('M_drama');
        $role_model = $this->getModel('M_role');
        $user_id = $_SESSION['user_id'];

        // Get drama details
        $data['drama'] = $drama_model->getDramaById($drama_id);
        
        if (!$data['drama']) {
            $_SESSION['message'] = 'Drama not found.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        // Get all roles for this drama
        $data['roles'] = $role_model ? $role_model->getRolesByDrama($drama_id) : [];
        
        // Get artist's role in this drama
        $data['my_role'] = $role_model ? $role_model->getArtistRoleInDrama($user_id, $drama_id) : null;
        
        // Get schedule/rehearsal data from drama_schedules table
        $schedule_model = $this->getModel('M_schedule');
        if ($schedule_model) {
            // Upcoming rehearsals, performances, meetings
            $data['schedules'] = $schedule_model->getUpcomingSchedulesForArtist($drama_id);
            // Past events
            $data['past_schedules'] = $schedule_model->getPastSchedulesForArtist($drama_id);
            // Artist's interview schedules
            $data['my_interviews'] = $schedule_model->getArtistInterviews($user_id, $drama_id);
            // Stats
            $data['schedule_stats'] = $schedule_model->getArtistScheduleStats($drama_id);
        } else {
            $data['schedules'] = [];
            $data['past_schedules'] = [];
            $data['my_interviews'] = [];
            $data['schedule_stats'] = null;
        }

        $this->view('artist_drama_view', $data);
    }

    /**
     * View individual event detail page (artist read-only)
     * URL: /artistdashboard/event_detail?event_id=X&drama_id=Y
     */
    public function event_detail()
    {
        // Auth check
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $event_id = $_GET['event_id'] ?? null;
        $drama_id = $_GET['drama_id'] ?? null;

        if (!$event_id || !$drama_id) {
            $_SESSION['message'] = 'Event not found.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $schedule_model = $this->getModel('M_schedule');
        $drama_model = $this->getModel('M_drama');
        $role_model = $this->getModel('M_role');
        $user_id = $_SESSION['user_id'];

        // Get event details (includes creator_name, role_name, drama_name)
        $data['event'] = $schedule_model ? $schedule_model->getEventById($event_id) : null;

        // Verify the event belongs to the requested drama
        if (!$data['event'] || (int)$data['event']->drama_id !== (int)$drama_id) {
            $_SESSION['message'] = 'Event not found or access denied.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard/view_drama?drama_id=" . (int)$drama_id);
            exit;
        }

        // Get drama info
        $data['drama'] = $drama_model->getDramaById($drama_id);

        // Get artist's own role in this drama
        $data['my_role'] = $role_model ? $role_model->getArtistRoleInDrama($user_id, $drama_id) : null;

        $this->view('artist_event_detail', $data);
    }

    /**
     * Notifications page - shows all notifications grouped by drama
     */
    public function notifications()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $notification_model = $this->getModel('M_notification');
        $artist_model = $this->getModel('M_artist');
        $user_id = $_SESSION['user_id'];

        $data['user'] = $artist_model ? $artist_model->get_artist_by_id($user_id) : null;
        $data['grouped_notifications'] = $notification_model ? $notification_model->getNotificationsGroupedByDrama($user_id) : [];
        $data['unread_count'] = $notification_model ? $notification_model->getUnreadCount($user_id) : 0;
        $data['all_notifications'] = $notification_model ? $notification_model->getNotificationsByUser($user_id) : [];

        $this->view('artist/notifications', $data);
    }

    /**
     * Mark a single notification as read (AJAX or regular request)
     */
    public function mark_notification_read()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $notification_id = $_GET['id'] ?? $_POST['notification_id'] ?? null;
        $redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? null;

        if ($notification_id) {
            $notification_model = $this->getModel('M_notification');
            $notification_model->markAsRead((int)$notification_id, (int)$_SESSION['user_id']);
        }

        $safeRedirect = $this->sanitizeArtistRedirect($redirect, '/artistdashboard/notifications');
        header('Location: ' . $safeRedirect);
        exit;
    }

    /**
     * Show a single notification in detail (marks as read)
     */
    public function notification_detail()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $notificationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($notificationId <= 0) {
            $_SESSION['message'] = 'Invalid notification.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/notifications');
            exit;
        }

        $notificationModel = $this->getModel('M_notification');
        $artist_model = $this->getModel('M_artist');
        if (!$notificationModel) {
            $_SESSION['message'] = 'Notification service unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/notifications');
            exit;
        }

        $userId = (int)$_SESSION['user_id'];
        $notification = $notificationModel->getNotificationByIdForUser($notificationId, $userId);
        if (!$notification) {
            $_SESSION['message'] = 'Notification not found.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/notifications');
            exit;
        }

        if (!(int)($notification->is_read ?? 0)) {
            $notificationModel->markAsRead($notificationId, $userId);
            $notification->is_read = 1;
        }

        $data = [
            'user' => $artist_model ? $artist_model->get_artist_by_id($userId) : null,
            'notification' => $notification,
            'back_url' => ROOT . '/artistdashboard/notifications',
        ];

        $this->view('artist/notification_detail', $data);
    }

    /**
     * Allow only safe in-app artist dashboard redirects and prevent mark-route loops.
     */
    private function sanitizeArtistRedirect($redirect, $fallbackPath = '/artistdashboard/notifications')
    {
        $fallback = ROOT . $fallbackPath;

        if (!is_string($redirect)) {
            return $fallback;
        }

        $candidate = trim($redirect);
        if ($candidate === '') {
            return $fallback;
        }

        if (strpos($candidate, ROOT) === 0) {
            $candidate = substr($candidate, strlen(ROOT));
        }

        if ($candidate === '') {
            return $fallback;
        }

        if ($candidate[0] !== '/') {
            $candidate = '/' . ltrim($candidate, '/');
        }

        $parts = parse_url($candidate);
        if ($parts === false || !empty($parts['host']) || !empty($parts['scheme'])) {
            return $fallback;
        }

        $path = (string)($parts['path'] ?? '');
        if ($path === '') {
            return $fallback;
        }

        $rootPath = (string)(parse_url(ROOT, PHP_URL_PATH) ?? '');
        if ($rootPath !== '' && strpos($path, $rootPath) === 0) {
            $path = substr($path, strlen($rootPath));
            if ($path === '') {
                $path = '/';
            }
        }

        if (strpos($path, '/artistdashboard') !== 0) {
            return $fallback;
        }

        if (
            strpos($path, '/artistdashboard/mark_notification_read') === 0 ||
            strpos($path, '/artistdashboard/mark_all_notifications_read') === 0
        ) {
            return $fallback;
        }

        $query = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';
        return ROOT . $path . $query . $fragment;
    }

    /**
     * Mark all notifications as read
     */
    public function mark_all_notifications_read()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $notification_model = $this->getModel('M_notification');
        if ($notification_model) {
            $notification_model->markAllAsRead((int)$_SESSION['user_id']);
        }

        $_SESSION['message'] = 'All notifications marked as read.';
        $_SESSION['message_type'] = 'success';
        header("Location: " . ROOT . "/artistdashboard/notifications");
        exit;
    }

    public function classes()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $artist_model = $this->getModel('M_artist');
        $class_model = $this->getModel('M_class');
        $user_id = (int)$_SESSION['user_id'];

        $data['user'] = $artist_model ? $artist_model->get_artist_by_id($user_id) : null;
        $data['my_classes'] = $class_model ? $class_model->getClassesByCreator($user_id) : [];
        $data['available_classes'] = $class_model ? $class_model->getPublishedClasses($user_id) : [];
        $data['enrolled_classes'] = $class_model ? $class_model->getEnrolledClassesByUser($user_id) : [];

        $this->view('artist/classes', $data);
    }

    public function create_class()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $class_model = $this->getModel('M_class');
        if (!$class_model) {
            $_SESSION['message'] = 'Class system is unavailable right now.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $start_time = trim((string)($_POST['start_time'] ?? ''));
        $end_time = trim((string)($_POST['end_time'] ?? ''));
        $duration_minutes = 120;

        if ($start_time !== '' || $end_time !== '') {
            if ($start_time === '' || $end_time === '') {
                $_SESSION['message'] = 'Please select both start time and end time.';
                $_SESSION['message_type'] = 'error';
                header('Location: ' . ROOT . '/artistdashboard/classes');
                exit;
            }

            $duration_minutes = $this->calculateDurationMinutes($start_time, $end_time);
            if ($duration_minutes === null || $duration_minutes < 30) {
                $_SESSION['message'] = 'Invalid class time range. End time must be after start time, with at least 30 minutes.';
                $_SESSION['message_type'] = 'error';
                header('Location: ' . ROOT . '/artistdashboard/classes');
                exit;
            }
        }

        $payload = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'class_level' => $_POST['class_level'] ?? 'all_levels',
            'fee' => $_POST['fee'] ?? 0,
            'capacity' => $_POST['capacity'] ?? 30,
            'class_date' => $_POST['class_date'] ?? null,
            'start_time' => $start_time !== '' ? $start_time : null,
            'duration_minutes' => $duration_minutes,
            'venue' => trim($_POST['venue'] ?? ''),
            'is_published' => isset($_POST['is_published']) ? 1 : 0,
        ];

        $result = $class_model->createClass((int)$_SESSION['user_id'], $payload);
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

        header('Location: ' . ROOT . '/artistdashboard/classes');
        exit;
    }

    private function calculateDurationMinutes($start_time, $end_time)
    {
        $start = DateTime::createFromFormat('H:i', $start_time);
        $end = DateTime::createFromFormat('H:i', $end_time);

        if (!$start || !$end) {
            return null;
        }

        $start_minutes = ((int)$start->format('H') * 60) + (int)$start->format('i');
        $end_minutes = ((int)$end->format('H') * 60) + (int)$end->format('i');

        if ($end_minutes <= $start_minutes) {
            return null;
        }

        return $end_minutes - $start_minutes;
    }

    public function enroll_class()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $_SESSION['message'] = 'Complete class payment first to enroll.';
        $_SESSION['message_type'] = 'error';

        header('Location: ' . ROOT . '/artistdashboard/classes');
        exit;
    }

    public function start_class_payment()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $classId = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        $classModel = $this->getModel('M_class');
        if ($classId <= 0 || !$classModel || !$this->payHereHelper) {
            echo json_encode(['success' => false, 'error' => 'Invalid class payment request']);
            exit;
        }

        $paymentOrder = $classModel->createEnrollmentPaymentOrder($classId, (int)$_SESSION['user_id'], 'artist', false);
        if (!$paymentOrder['success']) {
            echo json_encode(['success' => false, 'error' => $paymentOrder['message'] ?? 'Unable to initialize class payment']);
            exit;
        }

        $orderId = (string)$paymentOrder['order_id'];
        $amount = number_format((float)($paymentOrder['amount'] ?? 0), 2, '.', '');
        $hash = $this->payHereHelper->generateHash($orderId, $amount);
        $class = $paymentOrder['class'] ?? null;

        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'amount' => $amount,
            'hash' => $hash,
            'title' => $class->title ?? 'Drama Class',
            'merchant_id' => $this->payHereHelper->getConfig('merchant_id'),
            'sandbox' => (bool)$this->payHereHelper->getConfig('sandbox', false),
            'return_url' => ROOT . '/artistdashboard/class_payment_return?order_id=' . rawurlencode($orderId),
            'cancel_url' => ROOT . '/artistdashboard/classes',
            'notify_url' => ROOT . '/artistdashboard/class_payment_notify',
        ]);
        exit;
    }

    public function class_payment_return()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $orderId = trim((string)($_GET['order_id'] ?? ''));
        $classModel = $this->getModel('M_class');
        if ($orderId === '' || !$classModel) {
            $_SESSION['message'] = 'Invalid class payment return details.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $result = $classModel->completeEnrollmentPayment($orderId, (int)$_SESSION['user_id'], 'artist', false);
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

        header('Location: ' . ROOT . '/artistdashboard/classes');
        exit;
    }

    public function class_payment_notify()
    {
        http_response_code(200);
        echo 'OK';
        exit;
    }

    public function toggle_class_publish()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        if ($class_id <= 0) {
            $_SESSION['message'] = 'Invalid class selected.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $class_model = $this->getModel('M_class');
        if (!$class_model) {
            $_SESSION['message'] = 'Class system is unavailable right now.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $result = $class_model->togglePublishByOwner($class_id, (int)$_SESSION['user_id']);
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

        header('Location: ' . ROOT . '/artistdashboard/classes');
        exit;
    }

    public function delete_class()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $class_id = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        if ($class_id <= 0) {
            $_SESSION['message'] = 'Invalid class selected.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $class_model = $this->getModel('M_class');
        if (!$class_model) {
            $_SESSION['message'] = 'Class system is unavailable right now.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/artistdashboard/classes');
            exit;
        }

        $result = $class_model->deleteByOwner($class_id, (int)$_SESSION['user_id']);
        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

        header('Location: ' . ROOT . '/artistdashboard/classes');
        exit;
    }
}
