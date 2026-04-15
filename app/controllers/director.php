<?php

class Director{
    use Controller;

    protected $dramaModel;
    protected $roleModel;
    protected $artistModel;
    protected $pmModel;
    protected $scheduleModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->roleModel = $this->getModel('M_role');
        $this->artistModel = $this->getModel('M_artist');
        $this->pmModel = $this->getModel('M_production_manager');
        $this->scheduleModel = $this->getModel('M_schedule');
        $this->notificationModel = $this->getModel('M_notification');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->renderDramaView('dashboard', [], function ($drama) {
            // Get production manager
            $productionManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;
            
            // Get all roles for this drama
            $roles = $this->roleModel ? $this->roleModel->getRolesByDrama((int)$drama->id) : [];
            $roleStats = $this->roleModel ? $this->roleModel->getRoleStats((int)$drama->id) : null;
            $pendingApplications = $this->roleModel ? $this->roleModel->getPendingApplications((int)$drama->id) : [];
            
            // Get assigned artists for each role
            $assignedArtists = [];
            if ($this->roleModel && !empty($roles)) {
                foreach ($roles as $role) {
                    $assignments = $this->roleModel->getAssignmentsByRole((int)$role->id);
                    foreach ($assignments as $assignment) {
                        $assignedArtists[] = (object)[
                            'artist_name' => $assignment->artist_name ?? 'Unknown',
                            'role_name' => $role->role_name ?? 'Unknown Role',
                            'role_type' => $role->role_type ?? 'supporting',
                            'assigned_at' => $assignment->assigned_at ?? null,
                        ];
                    }
                }
            }

            $totalRoles = (int)($roleStats->total_roles ?? 0);
            $totalPositions = (int)($roleStats->total_positions ?? 0);
            $filledPositions = (int)($roleStats->filled_positions ?? 0);
            $hasProductionManager = $productionManager ? 1 : 0;
            $pendingApplicationsCount = is_array($pendingApplications) ? count($pendingApplications) : 0;
            
            return [
                'productionManager' => $productionManager,
                'assignedArtists' => $assignedArtists,
                'dashboardStats' => [
                    'total_roles' => $totalRoles,
                    'total_positions' => $totalPositions,
                    'filled_positions' => $filledPositions,
                    'production_managers' => $hasProductionManager,
                    'pending_applications' => $pendingApplicationsCount,
                ],
            ];
        });
    }

    public function drama_details()
    {
        // error_log("IN DRAMA DETAILS CONTROLLER");
        $this->renderDramaView('drama_details');
    }

    public function manage_roles()
    {
        $this->renderDramaView('manage_roles_overview', [], function ($drama) {
            $roles = $this->roleModel ? $this->roleModel->getRolesByDrama((int)$drama->id) : [];
            $stats = $this->roleModel ? $this->roleModel->getRoleStats((int)$drama->id) : null;
            $pendingApplications = $this->roleModel ? $this->roleModel->getApplicationsByDrama((int)$drama->id, 'pending') : [];
            
            // Get all requests without status filter to see everything
            $allRequests = $this->roleModel ? $this->roleModel->getRoleRequestsByDrama((int)$drama->id) : [];
            
            // Filter to pending and interview status
            $pendingRequests = array_filter($allRequests, function($req) {
                $status = strtolower($req->status ?? '');
                error_log("Request ID {$req->id} - Status: {$status}, Artist: {$req->artist_name}, Role: {$req->role_name}");
                return in_array($status, ['pending', 'interview']);
            });
            error_log("Filtered pending requests: " . count($pendingRequests));
            
            $publishedRoles = $this->roleModel ? $this->roleModel->getPublishedRolesByDrama((int)$drama->id) : [];

            return [
                'roles' => $roles,
                'roleStats' => $stats,
                'pendingApplications' => $pendingApplications,
                'pendingRequests' => array_values($pendingRequests), // Re-index array
                'publishedRoles' => $publishedRoles,
            ];
        });
    }

    public function view_role()
    {
        $roleId = $this->getQueryParam('role_id');
        if (!$roleId) {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $this->renderDramaView('role_details', [], function ($drama) use ($roleId) {
            $role = $this->findRoleForDrama((int)$roleId, (int)$drama->id);
            if (!$role) {
                $_SESSION['message'] = 'Role not found or inaccessible.';
                $_SESSION['message_type'] = 'error';
                $this->redirectToManageRoles((int)$drama->id);
            }

            $applications = $this->roleModel ? $this->roleModel->getApplicationsByRole((int)$role->id) : [];
            $requests = $this->roleModel ? $this->roleModel->getRoleRequestsByRole((int)$role->id) : [];
            $assignments = $this->roleModel ? $this->roleModel->getAssignmentsByRole((int)$role->id) : [];

            $formData = $_SESSION['role_form_data'] ?? null;
            $formErrors = $_SESSION['role_form_errors'] ?? [];
            $formMode = $_SESSION['role_form_mode'] ?? null;
            $formRoleId = $_SESSION['role_form_role_id'] ?? null;

            unset($_SESSION['role_form_data'], $_SESSION['role_form_errors'], $_SESSION['role_form_mode'], $_SESSION['role_form_role_id']);

            return [
                'role' => $role,
                'roleApplications' => $applications,
                'roleRequests' => $requests,
                'assignments' => $assignments,
                'role_form_data' => $formData,
                'role_form_errors' => $formErrors,
                'role_form_mode' => $formMode,
                'role_form_role_id' => $formRoleId,
            ];
        });
    }

    public function create_role()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $drama = $this->authorizeDrama();

            $defaults = [
                'role_name' => '',
                'role_description' => '',
                'role_type' => 'supporting',
                'salary' => '',
                'positions_available' => '1',
                'requirements' => '',
            ];

            $formData = $_SESSION['role_form_data'] ?? $defaults;
            $formErrors = $_SESSION['role_form_errors'] ?? [];

            unset($_SESSION['role_form_data'], $_SESSION['role_form_errors']);

            $this->renderDramaView('create_role', [
                'formData' => array_merge($defaults, $formData),
                'formErrors' => $formErrors,
            ]);
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $_SESSION['message'] = 'Role management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $formData = [
            'role_name' => trim($_POST['role_name'] ?? ''),
            'role_description' => trim($_POST['role_description'] ?? ''),
            'role_type' => trim($_POST['role_type'] ?? 'supporting'),
            'salary' => trim($_POST['salary'] ?? ''),
            'positions_available' => trim($_POST['positions_available'] ?? '1'),
            'requirements' => trim($_POST['requirements'] ?? ''),
        ];

        [$errors, $normalized] = $this->validateRoleInput($formData, 'create');

        if (!empty($errors)) {
            $_SESSION['role_form_data'] = $formData;
            $_SESSION['role_form_errors'] = $errors;
            $this->redirectToCreateRole((int)$drama->id);
        }

        $createData = [
            'drama_id' => (int)$drama->id,
            'role_name' => $normalized['role_name'],
            'role_description' => $normalized['role_description'],
            'role_type' => $normalized['role_type'],
            'salary' => $normalized['salary'],
            'positions_available' => $normalized['positions_available'],
            'requirements' => $normalized['requirements'],
            'created_by' => (int)$_SESSION['user_id'],
        ];

        $roleId = $this->roleModel->createRole($createData);

        if ($roleId) {
            $_SESSION['message'] = 'Role created successfully.';
            $_SESSION['message_type'] = 'success';
            $this->redirectToManageRoles((int)$drama->id, ['role_id' => $roleId]);
        }

        $_SESSION['message'] = 'Failed to create role. Please try again.';
        $_SESSION['message_type'] = 'error';
        $this->redirectToCreateRole((int)$drama->id);
    }

    public function update_role()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $_SESSION['message'] = 'Role management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $roleId = $this->getQueryParam('role_id');
        $roleId = $roleId !== null ? (int)$roleId : (int)($_POST['role_id'] ?? 0);

        $role = $this->findRoleForDrama($roleId, (int)$drama->id);
        if (!$role) {
            $_SESSION['message'] = 'Role not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $formData = [
            'role_name' => trim($_POST['role_name'] ?? ''),
            'role_description' => trim($_POST['role_description'] ?? ''),
            'role_type' => trim($_POST['role_type'] ?? 'supporting'),
            'salary' => trim($_POST['salary'] ?? ''),
            'positions_available' => trim($_POST['positions_available'] ?? '1'),
            'requirements' => trim($_POST['requirements'] ?? ''),
            'status' => trim($_POST['status'] ?? 'open'),
        ];

        [$errors, $normalized] = $this->validateRoleInput($formData, 'update');

        if (!empty($errors)) {
            $_SESSION['role_form_data'] = $formData;
            $_SESSION['role_form_errors'] = $errors;
            $_SESSION['role_form_mode'] = 'update';
            $_SESSION['role_form_role_id'] = $role->id;
            $this->redirectToRoleDetails((int)$drama->id, (int)$role->id);
        }

        $updateData = [
            'role_name' => $normalized['role_name'],
            'role_description' => $normalized['role_description'],
            'role_type' => $normalized['role_type'],
            'salary' => $normalized['salary'],
            'positions_available' => $normalized['positions_available'],
            'requirements' => $normalized['requirements'],
            'status' => $normalized['status'],
        ];

        $updated = $this->roleModel->updateRole((int)$role->id, $updateData);

        if ($updated) {
            $_SESSION['message'] = 'Role updated successfully.';
            $_SESSION['message_type'] = 'success';
            $this->redirectToRoleDetails((int)$drama->id, (int)$role->id);
        }

        $_SESSION['message'] = 'Failed to update role. Please try again.';
        $_SESSION['message_type'] = 'error';
        $this->redirectToRoleDetails((int)$drama->id, (int)$role->id);
    }

    public function delete_role()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $_SESSION['message'] = 'Role management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $roleId = $this->getQueryParam('role_id');
        $roleId = $roleId !== null ? (int)$roleId : (int)($_POST['role_id'] ?? 0);

        $role = $this->findRoleForDrama($roleId, (int)$drama->id);
        if (!$role) {
            $_SESSION['message'] = 'Role not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $hadAssignments = isset($role->positions_filled) && (int)$role->positions_filled > 0;

        $deleted = $this->roleModel->deleteRole((int)$role->id);

        if ($deleted) {
            if ($hadAssignments) {
                $_SESSION['message'] = 'Role has active assignments and was marked as closed.';
                $_SESSION['message_type'] = 'info';
            } else {
                $_SESSION['message'] = 'Role deleted successfully.';
                $_SESSION['message_type'] = 'success';
            }
            $this->redirectToManageRoles((int)$drama->id);
        }

        $_SESSION['message'] = 'Failed to delete role. Please try again.';
        $_SESSION['message_type'] = 'error';
        $this->redirectToManageRoles((int)$drama->id, ['role_id' => $role->id]);
    }

    public function assign_managers()
    {
        $this->renderDramaView('assign_managers', [], function ($drama) {
            // Fetch current manager and pending requests for this drama
            $currentManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;
            $pendingRequests = $this->pmModel ? $this->pmModel->getRequestsByDrama((int)$drama->id, 'pending') : [];

            return [
                'currentManager' => $currentManager,
                'pendingRequests' => $pendingRequests,
            ];
        });
    }

    public function search_managers()
    {
        $this->renderDramaView('search_managers', [], function ($drama) {
            $search = trim($_GET['search'] ?? '');
            $director_id = $_SESSION['user_id'];

            // Block searching/requests if a manager is already assigned
            $currentManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;
            if ($currentManager) {
                $_SESSION['message'] = 'Remove the current Production Manager before sending new requests.';
                $_SESSION['message_type'] = 'error';
                header("Location: " . ROOT . "/director/assign_managers?drama_id=" . $drama->id);
                exit;
            }
            
            // Search for available managers (excluding drama director and current PM)
            // This always fetches from database - with or without search term
            $availableManagers = $this->pmModel ? 
                $this->pmModel->searchAvailableManagers((int)$drama->id, (int)$director_id, $search) : [];
            
            return [
                'availableManagers' => $availableManagers,
                'searchTerm' => $search,
            ];
        });
    }

    public function send_manager_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();
        
        if (!$this->pmModel) {
            $_SESSION['message'] = 'Production Manager system is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/director/assign_managers?drama_id=" . $drama->id);
            exit;
        }

        $artist_id = isset($_POST['artist_id']) ? (int)$_POST['artist_id'] : 0;
        $message = $_POST['message'] ?? null;
        $director_id = $_SESSION['user_id'];

        if (!$artist_id) {
            $_SESSION['message'] = 'Invalid artist selection.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/director/search_managers?drama_id=" . $drama->id);
            exit;
        }

        // Ensure director is not inviting themselves
        if ($artist_id === $director_id) {
            $_SESSION['message'] = 'You cannot invite yourself as Production Manager.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/director/search_managers?drama_id=" . $drama->id);
            exit;
        }

        // Prevent new requests if a PM is already assigned
        $currentManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;
        if ($currentManager) {
            $_SESSION['message'] = 'A Production Manager is already assigned. Remove them before sending a new request.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/director/assign_managers?drama_id=" . $drama->id);
            exit;
        }

        $result = $this->pmModel->createRequest((int)$drama->id, $artist_id, $director_id, $message);

        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
        
        header("Location: " . ROOT . "/director/assign_managers?drama_id=" . $drama->id);
        exit;
    }

    public function remove_manager()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();
        
        if (!$this->pmModel) {
            $_SESSION['message'] = 'Production Manager system is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/director/assign_managers?drama_id=" . $drama->id);
            exit;
        }

        $director_id = $_SESSION['user_id'];
        $result = $this->pmModel->removeManager((int)$drama->id, $director_id);

        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
        
        header("Location: " . ROOT . "/director/assign_managers?drama_id=" . $drama->id);
        exit;
    }

    public function schedule_management()
    {
        $this->renderDramaView('schedule_management', [], function ($drama) {
            $dramaId = (int)$drama->id;

            // Get all events
            $upcomingEvents = $this->scheduleModel ? $this->scheduleModel->getUpcomingEvents($dramaId) : [];
            $pastEvents = $this->scheduleModel ? $this->scheduleModel->getPastEvents($dramaId) : [];
            $allEvents = $this->scheduleModel ? $this->scheduleModel->getEventsByDrama($dramaId) : [];
            $stats = $this->scheduleModel ? $this->scheduleModel->getScheduleStats($dramaId) : null;

            // Get interview events from role_applications for calendar integration
            $interviewEvents = $this->scheduleModel ? $this->scheduleModel->getInterviewsFromApplications($dramaId) : [];

            // Get roles for the interview schedule dropdown
            $roles = $this->roleModel ? $this->roleModel->getRolesByDrama($dramaId) : [];

            return [
                'upcomingEvents' => $upcomingEvents,
                'pastEvents' => $pastEvents,
                'allEvents' => $allEvents,
                'interviewEvents' => $interviewEvents,
                'scheduleStats' => $stats,
                'roles' => $roles,
            ];
        });
    }

    /**
     * Create a new schedule event (POST)
     */
    public function create_schedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        // Validate inputs
        $eventType = trim($_POST['event_type'] ?? '');
        $eventTitle = trim($_POST['event_title'] ?? '');
        $eventDescription = trim($_POST['event_description'] ?? '');
        $scheduledDate = trim($_POST['scheduled_date'] ?? '');
        $startTime = trim($_POST['start_time'] ?? '');
        $endTime = trim($_POST['end_time'] ?? '');
        $venue = trim($_POST['venue'] ?? '');
        $roleId = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
        $notes = trim($_POST['notes'] ?? '');

        $errors = [];
        if (!in_array($eventType, ['rehearsal', 'interview', 'meeting', 'performance'])) {
            $errors[] = 'Invalid event type.';
        }
        if (empty($eventTitle)) {
            $errors[] = 'Event title is required.';
        }
        if (empty($scheduledDate)) {
            $errors[] = 'Date is required.';
        }
        if (empty($startTime) || empty($endTime)) {
            $errors[] = 'Start and end times are required.';
        }
        if ($startTime >= $endTime) {
            $errors[] = 'End time must be after start time.';
        }
        if (empty($venue)) {
            $errors[] = 'Venue is required.';
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        // Check time slot availability
        $isAvailable = $this->scheduleModel->isTimeSlotAvailable((int)$drama->id, $scheduledDate, $startTime, $endTime);
        if (!$isAvailable) {
            $_SESSION['message'] = 'This time slot conflicts with an existing schedule on ' . $scheduledDate . '. Please choose a different time.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $data = [
            'drama_id' => (int)$drama->id,
            'event_type' => $eventType,
            'event_title' => $eventTitle,
            'event_description' => $eventDescription ?: null,
            'scheduled_date' => $scheduledDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'venue' => $venue,
            'role_id' => $roleId,
            'notes' => $notes ?: null,
            'created_by' => (int)$_SESSION['user_id'],
        ];

        $eventId = $this->scheduleModel->createEvent($data);

        if ($eventId) {
            $_SESSION['message'] = 'Schedule event created successfully.';
            $_SESSION['message_type'] = 'success';

            // Notify all artists in this drama about the new event
            if ($this->notificationModel) {
                $eventLink = ROOT . '/artistdashboard/event_detail?event_id=' . $eventId . '&drama_id=' . $drama->id;
                $this->notificationModel->notifyDramaArtists(
                    (int)$drama->id,
                    'event_scheduled',
                    'New ' . ucfirst($eventType) . ' Scheduled: ' . $eventTitle,
                    ucfirst($eventType) . ' "' . $eventTitle . '" has been scheduled for ' . date('M d, Y', strtotime($scheduledDate)) . ' at ' . $venue . ' (' . date('h:i A', strtotime($startTime)) . ' - ' . date('h:i A', strtotime($endTime)) . ').',
                    $eventLink,
                    (int)$_SESSION['user_id']
                );
            }
        } else {
            $_SESSION['message'] = 'Failed to create schedule event.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    /**
     * Update an existing schedule event (POST)
     */
    public function update_schedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        if (!$eventId) {
            $_SESSION['message'] = 'Invalid event.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        // Verify event belongs to this drama
        $event = $this->scheduleModel->getEventById($eventId);
        if (!$event || (int)$event->drama_id !== (int)$drama->id) {
            $_SESSION['message'] = 'Event not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        // Validate inputs
        $eventType = trim($_POST['event_type'] ?? '');
        $eventTitle = trim($_POST['event_title'] ?? '');
        $eventDescription = trim($_POST['event_description'] ?? '');
        $scheduledDate = trim($_POST['scheduled_date'] ?? '');
        $startTime = trim($_POST['start_time'] ?? '');
        $endTime = trim($_POST['end_time'] ?? '');
        $venue = trim($_POST['venue'] ?? '');
        $roleId = !empty($_POST['role_id']) ? (int)$_POST['role_id'] : null;
        $notes = trim($_POST['notes'] ?? '');

        $errors = [];
        if (!in_array($eventType, ['rehearsal', 'interview', 'meeting', 'performance'])) {
            $errors[] = 'Invalid event type.';
        }
        if (empty($eventTitle)) {
            $errors[] = 'Event title is required.';
        }
        if (empty($scheduledDate)) {
            $errors[] = 'Date is required.';
        }
        if (empty($startTime) || empty($endTime)) {
            $errors[] = 'Start and end times are required.';
        }
        if ($startTime >= $endTime) {
            $errors[] = 'End time must be after start time.';
        }
        if (empty($venue)) {
            $errors[] = 'Venue is required.';
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        // Check time slot availability (exclude current event)
        $isAvailable = $this->scheduleModel->isTimeSlotAvailable((int)$drama->id, $scheduledDate, $startTime, $endTime, $eventId);
        if (!$isAvailable) {
            $_SESSION['message'] = 'This time slot conflicts with an existing schedule on ' . $scheduledDate . '. Please choose a different time.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $data = [
            'event_type' => $eventType,
            'event_title' => $eventTitle,
            'event_description' => $eventDescription ?: null,
            'scheduled_date' => $scheduledDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'venue' => $venue,
            'role_id' => $roleId,
            'notes' => $notes ?: null,
        ];

        $updated = $this->scheduleModel->updateEvent($eventId, $data);

        if ($updated) {
            $_SESSION['message'] = 'Schedule event updated successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to update schedule event.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    /**
     * Delete a schedule event (POST)
     */
    public function delete_schedule()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);

        // Verify event belongs to this drama
        $event = $this->scheduleModel->getEventById($eventId);
        if (!$event || (int)$event->drama_id !== (int)$drama->id) {
            $_SESSION['message'] = 'Event not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $deleted = $this->scheduleModel->deleteEvent($eventId);

        if ($deleted) {
            $_SESSION['message'] = 'Schedule event deleted successfully.';
            $_SESSION['message_type'] = 'success';

            // Notify all artists that the event was cancelled/deleted
            if ($this->notificationModel) {
                $dramaLink = ROOT . '/artistdashboard/view_drama?drama_id=' . $drama->id;
                $this->notificationModel->notifyDramaArtists(
                    (int)$drama->id,
                    'event_cancelled',
                    'Event Cancelled: ' . ($event->event_title ?? 'Event'),
                    'The ' . ($event->event_type ?? 'event') . ' "' . ($event->event_title ?? 'Event') . '" on ' . date('M d, Y', strtotime($event->scheduled_date)) . ' has been removed from the schedule.',
                    $dramaLink,
                    (int)$_SESSION['user_id']
                );
            }
        } else {
            $_SESSION['message'] = 'Failed to delete schedule event.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    /**
     * Update schedule event status (POST) — confirm, complete, cancel
     */
    public function update_schedule_status()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $dramaId);
                exit;
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->scheduleModel) {
            $_SESSION['message'] = 'Schedule management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $eventId = (int)($_POST['event_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        // Verify event belongs to this drama
        $event = $this->scheduleModel->getEventById($eventId);
        if (!$event || (int)$event->drama_id !== (int)$drama->id) {
            $_SESSION['message'] = 'Event not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
            exit;
        }

        $updated = $this->scheduleModel->updateEventStatus($eventId, $status);

        if ($updated) {
            $_SESSION['message'] = 'Event status updated to ' . ucfirst($status) . '.';
            $_SESSION['message_type'] = 'success';

            // Notify artists about status change (cancelled or confirmed)
            if ($this->notificationModel && in_array($status, ['cancelled', 'confirmed'])) {
                $notifType = $status === 'cancelled' ? 'event_cancelled' : 'event_updated';
                $eventLink = ROOT . '/artistdashboard/event_detail?event_id=' . $eventId . '&drama_id=' . $drama->id;
                $this->notificationModel->notifyDramaArtists(
                    (int)$drama->id,
                    $notifType,
                    'Event ' . ucfirst($status) . ': ' . ($event->event_title ?? 'Event'),
                    'The ' . ($event->event_type ?? 'event') . ' "' . ($event->event_title ?? 'Event') . '" on ' . date('M d, Y', strtotime($event->scheduled_date)) . ' has been ' . $status . '.',
                    $eventLink,
                    (int)$_SESSION['user_id']
                );
            }
        } else {
            $_SESSION['message'] = 'Failed to update event status.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/schedule_management?drama_id=' . $drama->id);
        exit;
    }

    /**
     * AJAX endpoint: check date availability
     */
    public function check_date_availability()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['available' => false, 'message' => 'Not authenticated.']);
            exit;
        }

        $dramaId = (int)($this->getQueryParam('drama_id') ?? 0);
        $date = $this->getQueryParam('date') ?? '';
        $startTime = $this->getQueryParam('start_time') ?? '';
        $endTime = $this->getQueryParam('end_time') ?? '';
        $excludeId = (int)($this->getQueryParam('exclude_id') ?? 0);

        if (!$dramaId || !$date) {
            echo json_encode(['available' => false, 'message' => 'Missing parameters.']);
            exit;
        }

        if (!$this->scheduleModel) {
            echo json_encode(['available' => false, 'message' => 'Schedule service unavailable.']);
            exit;
        }

        // Get events on this date
        $eventsOnDate = $this->scheduleModel->getEventsByDate($dramaId, $date);

        // Check specific time slot if provided
        $timeAvailable = true;
        if ($startTime && $endTime) {
            $timeAvailable = $this->scheduleModel->isTimeSlotAvailable($dramaId, $date, $startTime, $endTime, $excludeId ?: null);
        }

        $eventList = [];
        foreach ($eventsOnDate as $evt) {
            $eventList[] = [
                'id' => $evt->id,
                'title' => $evt->event_title,
                'type' => $evt->event_type,
                'start_time' => $evt->start_time,
                'end_time' => $evt->end_time,
                'venue' => $evt->venue,
                'status' => $evt->status,
            ];
        }

        echo json_encode([
            'available' => $timeAvailable,
            'date' => $date,
            'events_count' => count($eventsOnDate),
            'events' => $eventList,
            'message' => $timeAvailable
                ? (count($eventsOnDate) > 0
                    ? 'Date has ' . count($eventsOnDate) . ' event(s) but the selected time slot is available.'
                    : 'Date is completely free. You can schedule your event.')
                : 'Time slot conflicts with an existing event. Please choose a different time.',
        ]);
        exit;
    }

    public function view_services_budget()
    {
        $this->renderDramaView('view_services_budget', [], function ($drama) {
            $dramaId = (int)$drama->id;

            $serviceModel = $this->getModel('M_service_request');
            $budgetModel = $this->getModel('M_budget');
            $paymentModel = $this->getModel('M_payment');

            $servicesRaw = $serviceModel ? ($serviceModel->getServicesByDrama($dramaId) ?? []) : [];
            $budgetItemsRaw = $budgetModel ? ($budgetModel->getBudgetByDrama($dramaId) ?? []) : [];
            $budgetCategoriesRaw = $budgetModel ? ($budgetModel->getBudgetSummaryByCategory($dramaId) ?? []) : [];

            $totalBudget = $budgetModel ? (float)$budgetModel->getTotalBudget($dramaId) : 0.0;
            $usedBudget = $budgetModel ? (float)$budgetModel->getTotalSpent($dramaId) : 0.0;
            $remainingBudget = max(0, $totalBudget - $usedBudget);
            $usedPct = $totalBudget > 0 ? round(($usedBudget / $totalBudget) * 100, 2) : 0;
            $remainingPct = max(0, round(100 - $usedPct, 2));

            $pendingPayments = 0.0;
            if ($paymentModel && is_array($servicesRaw) && !empty($servicesRaw)) {
                $requestIds = [];
                foreach ($servicesRaw as $service) {
                    $rid = (int)($service->id ?? 0);
                    if ($rid > 0) {
                        $requestIds[] = $rid;
                    }
                }

                if (!empty($requestIds)) {
                    $paymentStats = $paymentModel->getRequestPaymentStats($requestIds);
                    foreach ($servicesRaw as $service) {
                        $rid = (int)($service->id ?? 0);
                        $serviceBudget = (float)($service->budget ?? 0);
                        $paid = isset($paymentStats[$rid]) ? (float)($paymentStats[$rid]['total_paid'] ?? 0) : 0.0;
                        if ($serviceBudget > $paid) {
                            $pendingPayments += ($serviceBudget - $paid);
                        }
                    }
                }
            }

            $services = [];
            foreach ($servicesRaw as $service) {
                $services[] = [
                    'title' => $service->service_type ?? 'Service',
                    'managed_by' => $service->provider_name ?? null,
                    'details' => $service->description ?? $service->service_required ?? null,
                    'status' => ucfirst((string)($service->status ?? 'pending')),
                    'payment_status' => ucfirst((string)($service->calculated_payment_status ?? $service->payment_status ?? 'unpaid')),
                ];
            }

            $budgetItems = [];
            foreach ($budgetItemsRaw as $item) {
                $budgetItems[] = [
                    'title' => $item->item_name ?? 'Budget Item',
                    'details' => $item->notes ?? null,
                    'amount' => isset($item->allocated_amount) ? (float)$item->allocated_amount : 0,
                    'status' => ucfirst((string)($item->status ?? 'pending')),
                ];
            }

            $budgetCategories = [];
            foreach ($budgetCategoriesRaw as $category) {
                $allocated = isset($category->total_allocated) ? (float)$category->total_allocated : 0.0;
                $pct = $totalBudget > 0 ? round(($allocated / $totalBudget) * 100, 2) : 0.0;
                $budgetCategories[] = [
                    'name' => ucfirst((string)($category->category ?? 'Other')),
                    'amount' => $allocated,
                    'percentage' => $pct,
                ];
            }

            return [
                'services' => $services,
                'budgetItems' => $budgetItems,
                'budgetCategories' => $budgetCategories,
                'theaterBookings' => [],
                'budgetSummary' => [
                    'total_budget' => $totalBudget,
                    'used_budget' => $usedBudget,
                    'remaining_budget' => $remainingBudget,
                    'used_percentage' => $usedPct,
                    'remaining_percentage' => $remainingPct,
                    'pending_payments' => $pendingPayments,
                ],
            ];
        });
    }

    public function search_artists()
    {
        $roleId = $this->getQueryParam('role_id');
        if (!$roleId) {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $searchTerm = trim((string)$this->getQueryParam('search', ''));

        $this->renderDramaView('search_artists', [], function ($drama) use ($roleId, $searchTerm) {
            $role = $this->findRoleForDrama((int)$roleId, (int)$drama->id);
            if (!$role) {
                $_SESSION['message'] = 'Role not found or inaccessible.';
                $_SESSION['message_type'] = 'error';
                $this->redirectToManageRoles((int)$drama->id);
            }

            $filters = [];
            if ($searchTerm !== '') {
                $filters['search'] = $searchTerm;
            }

            $artists = $this->artistModel ? $this->artistModel->get_artists_for_role((int)$role->id, $filters) : [];

            return [
                'role' => $role,
                'artists' => $artists,
                'searchTerm' => $searchTerm,
            ];
        });
    }

    public function artist_profile()
    {
        $artistId = $this->sanitizeInt($this->getQueryParam('artist_id'));
        if (!$artistId) {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $roleId = $this->sanitizeInt($this->getQueryParam('role_id'));

        $this->renderDramaView('artist_profile', [], function ($drama) use ($artistId, $roleId) {
            if (!$this->artistModel) {
                $this->respondWithRedirect(false, 'Artist profile service is currently unavailable.', (int)$drama->id, [
                    'route' => 'manage',
                    'role_id' => $roleId,
                ]);
            }

            $artist = $this->artistModel->get_artist_by_id((int)$artistId);
            if (!$artist) {
                $this->respondWithRedirect(false, 'Artist profile not found.', (int)$drama->id, [
                    'route' => 'search',
                    'role_id' => $roleId,
                ]);
            }

            $role = null;
            if ($roleId) {
                $role = $this->findRoleForDrama((int)$roleId, (int)$drama->id);
            }

            return [
                'artist' => $artist,
                'role' => $role,
                'roleId' => $roleId,
                'profileContext' => 'role_artist',
            ];
        });
    }

    public function manager_profile()
    {
        $artistId = $this->sanitizeInt($this->getQueryParam('artist_id'));
        if (!$artistId) {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/search_managers?drama_id=' . (int)$dramaId);
                exit;
            }
            $this->dashboard();
            return;
        }

        $this->renderDramaView('artist_profile', [], function ($drama) use ($artistId) {
            if (!$this->artistModel) {
                $_SESSION['message'] = 'Artist profile service is currently unavailable.';
                $_SESSION['message_type'] = 'error';
                header('Location: ' . ROOT . '/director/search_managers?drama_id=' . (int)$drama->id);
                exit;
            }

            $artist = $this->artistModel->get_artist_by_id((int)$artistId);
            if (!$artist) {
                $_SESSION['message'] = 'Production Manager profile not found.';
                $_SESSION['message_type'] = 'error';
                header('Location: ' . ROOT . '/director/search_managers?drama_id=' . (int)$drama->id);
                exit;
            }

            return [
                'artist' => $artist,
                'role' => null,
                'roleId' => null,
                'profileContext' => 'manager_search',
            ];
        });
    }

    public function send_role_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id);
        }

        $roleId = (int)($_POST['role_id'] ?? 0);
        $artistId = (int)($_POST['artist_id'] ?? 0);
        $note = trim((string)($_POST['note'] ?? ''));
        $interviewRaw = trim((string)($_POST['interview_at'] ?? ''));

        $errors = [];

        $role = $this->findRoleForDrama($roleId, (int)$drama->id);
        if (!$role) {
            $errors[] = 'Role not found or inaccessible.';
        }

        if ($artistId <= 0) {
            $errors[] = 'Select a valid artist to request.';
        }

        $interviewAt = null;
        if ($interviewRaw !== '') {
            $timestamp = strtotime($interviewRaw);
            if ($timestamp === false) {
                $errors[] = 'Invalid interview schedule provided.';
            } else {
                $interviewAt = date('Y-m-d H:i:s', $timestamp);
            }
        }

        if ($role && (int)$role->positions_filled >= (int)$role->positions_available) {
            $errors[] = 'All positions for this role have already been filled.';
        }

        if (!empty($errors)) {
            $this->respondWithRedirect(false, implode(' ', $errors), (int)$drama->id, [
                'route' => 'search',
                'role_id' => $roleId,
            ]);
        }

        $requestId = $this->roleModel->createRoleRequest(
            $roleId,
            $artistId,
            (int)$_SESSION['user_id'],
            $note !== '' ? $note : null,
            $interviewAt
        );

        if ($requestId) {
            // Notify the artist about the role request/invitation
            if ($this->notificationModel && $role) {
                $dramaLink = ROOT . '/artistdashboard';
                $this->notificationModel->createNotification([
                    'user_id' => $artistId,
                    'drama_id' => (int)$drama->id,
                    'type' => 'role_assigned',
                    'title' => 'Role Invitation: ' . ($role->role_name ?? 'Role'),
                    'message' => 'You have been invited for the role "' . ($role->role_name ?? 'Role') . '" in "' . ($drama->drama_name ?? 'Drama') . '". Please check your dashboard to respond.',
                    'link' => $dramaLink,
                ]);
            }

            $this->respondWithRedirect(true, 'Artist request sent successfully.', (int)$drama->id, [
                'route' => 'search',
                'role_id' => $roleId,
            ]);
        }

        $this->respondWithRedirect(false, 'Unable to send artist request. Please try again.', (int)$drama->id, [
            'route' => 'search',
            'role_id' => $roleId,
        ]);
    }

    public function remove_role_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        if ($requestId <= 0) {
            $this->respondWithRedirect(false, 'Invalid request selected.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $removed = $this->roleModel->cancelRoleRequestByDirector(
            $requestId,
            (int)$_SESSION['user_id'],
            (int)$drama->id
        );

        if ($removed) {
            $this->respondWithRedirect(true, 'Role request removed successfully.', (int)$drama->id, [
                'route' => 'manage',
            ], 'success');
        }

        $this->respondWithRedirect(false, 'Unable to remove request. It may already be handled.', (int)$drama->id, [
            'route' => 'manage',
        ]);
    }

    public function publish_vacancy()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id);
        }

        $roleId = (int)($_POST['role_id'] ?? 0);
        $message = trim((string)($_POST['message'] ?? ''));

        $role = $this->findRoleForDrama($roleId, (int)$drama->id);
        if (!$role) {
            $this->respondWithRedirect(false, 'Role not found or inaccessible.', (int)$drama->id);
        }

        $published = $this->roleModel->publishVacancy($roleId, $message !== '' ? $message : null, (int)$_SESSION['user_id']);

        if ($published) {
            $this->respondWithRedirect(true, 'Vacancy published successfully.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $this->respondWithRedirect(false, 'Unable to publish vacancy. Please try again.', (int)$drama->id, [
            'route' => 'manage',
        ]);
    }

    public function unpublish_vacancy()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id);
        }

        $roleId = (int)($_POST['role_id'] ?? 0);
        $role = $this->findRoleForDrama($roleId, (int)$drama->id);
        if (!$role) {
            $this->respondWithRedirect(false, 'Role not found or inaccessible.', (int)$drama->id);
        }

        $unpublished = $this->roleModel->unpublishVacancy($roleId);

        if ($unpublished) {
            $this->respondWithRedirect(true, 'Vacancy unpublished.', (int)$drama->id, [
                'route' => 'manage',
            ], 'info');
        }

        $this->respondWithRedirect(false, 'Unable to update vacancy status.', (int)$drama->id, [
            'route' => 'manage',
        ]);
    }

    public function accept_application()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id);
        }

        $applicationId = (int)($_POST['application_id'] ?? 0);
        $application = $this->roleModel->getApplicationById($applicationId);

        if (!$application || (int)$application->drama_id !== (int)$drama->id) {
            $this->respondWithRedirect(false, 'Application not found or inaccessible.', (int)$drama->id);
        }

        $accepted = $this->roleModel->acceptApplication($applicationId, (int)$_SESSION['user_id']);

        if ($accepted) {
            // Notify the artist that their application was accepted
            if ($this->notificationModel && $application) {
                $dramaLink = ROOT . '/artistdashboard/view_drama?drama_id=' . $drama->id;
                $this->notificationModel->createNotification([
                    'user_id' => (int)$application->artist_id,
                    'drama_id' => (int)$drama->id,
                    'type' => 'application_accepted',
                    'title' => 'Role Assigned: ' . ($application->role_name ?? 'Role'),
                    'message' => 'Congratulations! Your application for the role "' . ($application->role_name ?? 'Role') . '" in "' . ($drama->drama_name ?? 'Drama') . '" has been accepted. You are now assigned to this role.',
                    'link' => $dramaLink,
                ]);
            }

            $this->respondWithRedirect(true, 'Application accepted and artist assigned.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $this->respondWithRedirect(false, 'Unable to accept the application.', (int)$drama->id, [
            'route' => 'manage',
        ]);
    }

    public function reject_application()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id);
        }

        $applicationId = (int)($_POST['application_id'] ?? 0);
        $application = $this->roleModel->getApplicationById($applicationId);

        if (!$application || (int)$application->drama_id !== (int)$drama->id) {
            $this->respondWithRedirect(false, 'Application not found or inaccessible.', (int)$drama->id);
        }

        $rejected = $this->roleModel->rejectApplication($applicationId, (int)$_SESSION['user_id']);

        if ($rejected) {
            // Notify the artist that their application was rejected
            if ($this->notificationModel && $application) {
                $this->notificationModel->createNotification([
                    'user_id' => (int)$application->artist_id,
                    'drama_id' => (int)$drama->id,
                    'type' => 'application_rejected',
                    'title' => 'Application Update: ' . ($application->role_name ?? 'Role'),
                    'message' => 'Your application for the role "' . ($application->role_name ?? 'Role') . '" in "' . ($drama->drama_name ?? 'Drama') . '" was not selected. Keep exploring other opportunities!',
                    'link' => ROOT . '/artistdashboard/browse_vacancies',
                ]);
            }

            $this->respondWithRedirect(true, 'Application rejected.', (int)$drama->id, [
                'route' => 'manage',
            ], 'info');
        }

        $this->respondWithRedirect(false, 'Unable to reject the application.', (int)$drama->id, [
            'route' => 'manage',
        ]);
    }

    public function application_profile()
    {
        $applicationId = $this->sanitizeInt($this->getQueryParam('application_id'));
        if (!$applicationId) {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel || !$this->artistModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id);
        }

        $application = $this->loadApplicationForDrama($applicationId, $drama);
        if (!$application) {
            $this->respondWithRedirect(false, 'Application not found or inaccessible.', (int)$drama->id);
        }

        $artist = $this->artistModel->get_artist_by_id((int)$application->artist_id);
        if (!$artist) {
            $this->respondWithRedirect(false, 'Unable to load artist profile.', (int)$drama->id);
        }

        $directorId = (int)($_SESSION['user_id'] ?? 0);
        if ($directorId > 0 && strtolower($application->status ?? '') === 'pending') {
            $this->roleModel->markApplicationProfileViewed($applicationId, $directorId);
            $application = $this->roleModel->getApplicationById($applicationId);
        }

        $confirmationStatus = strtolower($application->interview_confirmation_status ?? 'pending');
        if (
            $directorId > 0 &&
            $confirmationStatus !== 'pending' &&
            empty($application->interview_confirmation_seen_at ?? null)
        ) {
            $this->roleModel->markInterviewConfirmationSeen($applicationId, $directorId);
            $application = $this->roleModel->getApplicationById($applicationId);
        }

        $this->renderDramaView('application_artist_profile', [
            'application' => $application,
            'artist' => $artist,
            'directorId' => $directorId,
        ]);
    }

    public function schedule_application_interview()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id);
        }

        $applicationId = (int)($_POST['application_id'] ?? 0);
        $application = $this->loadApplicationForDrama($applicationId, $drama);
        if (!$application) {
            $this->respondWithRedirect(false, 'Application not found or inaccessible.', (int)$drama->id);
        }

        if (strtolower($application->status ?? '') !== 'pending') {
            $this->respondWithRedirect(false, 'Only pending applications can be scheduled for interviews.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $directorId = (int)($_SESSION['user_id'] ?? 0);
        $redirectTarget = $_POST['redirect_to'] ?? 'manage';
        $redirectOptions = [
            'route' => $redirectTarget === 'profile' ? 'application_profile' : 'manage',
            'application_id' => $applicationId,
        ];

        $interviewRaw = trim((string)($_POST['interview_at'] ?? ''));
        $notes = trim((string)($_POST['interview_notes'] ?? ''));

        $errors = [];
        if (empty($application->profile_viewed_at) || (int)($application->profile_viewed_by ?? 0) !== $directorId) {
            $errors[] = 'View the artist profile before scheduling the interview.';
        }

        $interviewAt = null;
        if ($interviewRaw === '') {
            $errors[] = 'Provide a date and time for the interview.';
        } else {
            $timestamp = strtotime($interviewRaw);
            if ($timestamp === false) {
                $errors[] = 'Enter a valid interview date and time.';
            } elseif ($timestamp <= time()) {
                $errors[] = 'Schedule the interview for a future time.';
            } else {
                $interviewAt = date('Y-m-d H:i:s', $timestamp);
            }
        }

        if (!empty($errors)) {
            $this->respondWithRedirect(false, implode(' ', $errors), (int)$drama->id, $redirectOptions);
        }

        $scheduled = $this->roleModel->scheduleApplicationInterview(
            $applicationId,
            $interviewAt,
            $directorId,
            $notes !== '' ? $notes : null
        );

        if ($scheduled) {
            // Notify the artist about their scheduled interview
            if ($this->notificationModel && $application) {
                $dramaLink = ROOT . '/artistdashboard/view_drama?drama_id=' . $drama->id;
                $this->notificationModel->createNotification([
                    'user_id' => (int)$application->artist_id,
                    'drama_id' => (int)$drama->id,
                    'type' => 'interview_scheduled',
                    'title' => 'Interview Scheduled: ' . ($application->role_name ?? 'Role'),
                    'message' => 'An interview has been scheduled for the role "' . ($application->role_name ?? 'Role') . '" in "' . ($drama->drama_name ?? 'Drama') . '" on ' . date('M d, Y \a\t h:i A', strtotime($interviewAt)) . '.',
                    'link' => $dramaLink,
                ]);
            }

            $this->respondWithRedirect(true, 'Interview scheduled successfully.', (int)$drama->id, $redirectOptions);
        }

        $this->respondWithRedirect(false, 'Unable to schedule the interview. Please try again.', (int)$drama->id, $redirectOptions);
    }

    public function create_drama()
    {
        $this->renderDramaView('create_drama');
    }

    public function manage_dramas()
    {
        $this->renderDramaView('manage_dramas');
    }

    public function role_management()
    {
        $this->renderDramaView('role_management');
    }

    public function update_drama()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            header("Location: " . ROOT . "/director/drama_details" . ($dramaId ? "?drama_id={$dramaId}" : ''));
            exit;
        }

        $drama = $this->authorizeDrama();

        $formData = [
            'drama_name' => trim($_POST['drama_name'] ?? ''),
            'certificate_number' => trim($_POST['certificate_number'] ?? ''),
            'owner_name' => trim($_POST['owner_name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];

        $errors = [];

        if ($formData['drama_name'] === '') {
            $errors[] = 'Drama name is required';
        }

        if ($formData['certificate_number'] === '') {
            $errors[] = 'Certificate number is required';
        }

        if ($formData['owner_name'] === '') {
            $errors[] = 'Owner name is required';
        }

        if ($formData['description'] === '') {
            $errors[] = 'Drama description is required';
        }

        $newImageName = null;
        if (isset($_FILES['certificate_image']) && $_FILES['certificate_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newImageName = $this->handleImageUpload($_FILES['certificate_image']);
            if ($newImageName === false) {
                $errors[] = 'Invalid certificate image. Allowed types: JPG, PNG, GIF, WEBP, PDF up to 5MB.';
            }
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            $this->renderDramaView('drama_details', ['form_data' => $formData]);
            return;
        }

        $updateData = $formData;
        if ($newImageName !== null) {
            $updateData['certificate_image'] = $newImageName;
        }

        $updated = $this->dramaModel->updateDrama((int)$drama->id, $updateData);

        if ($updated) {
            if ($newImageName !== null && !empty($drama->certificate_image)) {
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/certificates/';
                $oldPath = $uploadDir . $drama->certificate_image;
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $_SESSION['message'] = 'Drama details updated successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            if ($newImageName !== null) {
                $uploadDir = dirname(__DIR__, 2) . '/public/uploads/certificates/';
                $newPath = $uploadDir . $newImageName;
                if (file_exists($newPath)) {
                    @unlink($newPath);
                }
            }

            $_SESSION['message'] = 'Failed to update drama. Certificate number might already exist.';
            $_SESSION['message_type'] = 'error';
        }

        header("Location: " . ROOT . "/director/drama_details?drama_id=" . $drama->id);
        exit;
    }

    public function publish_drama()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            header('Location: ' . ROOT . '/director/drama_details' . ($dramaId ? '?drama_id=' . (int)$dramaId : ''));
            exit;
        }

        $drama = $this->authorizeDrama();

        $formData = [
            'category_id' => trim($_POST['category_id'] ?? ''),
            'public_description' => trim($_POST['public_description'] ?? ''),
            'language' => trim($_POST['language'] ?? ''),
            'duration_minutes' => trim($_POST['duration_minutes'] ?? ''),
            'showing_prices' => trim($_POST['showing_prices'] ?? ''),
        ];

        $errors = [];

        $categoryId = filter_var($formData['category_id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($categoryId === false) {
            $errors[] = 'Category is required.';
        }

        if ($formData['public_description'] === '') {
            $errors[] = 'Public drama description is required.';
        }

        if ($formData['language'] === '') {
            $errors[] = 'Language is required.';
        }

        $duration = filter_var($formData['duration_minutes'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($duration === false) {
            $errors[] = 'Duration must be a positive whole number.';
        }

        if ($formData['showing_prices'] === '') {
            $errors[] = 'Showing prices are required.';
        } elseif (strlen($formData['showing_prices']) > 500) {
            $errors[] = 'Showing prices cannot exceed 500 characters.';
        }

        $posterName = $drama->poster_image ?? null;
        if (isset($_FILES['poster_image']) && $_FILES['poster_image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $newPoster = $this->handlePosterUpload($_FILES['poster_image']);
            if ($newPoster === false) {
                $errors[] = 'Invalid poster image. Allowed types: JPG, PNG, GIF, WEBP up to 8MB.';
            } else {
                $posterName = $newPoster;
            }
        }

        if (empty($posterName)) {
            $errors[] = 'Drama poster image is required to publish.';
        }

        if (!empty($errors)) {
            $_SESSION['message'] = implode(' ', $errors);
            $_SESSION['message_type'] = 'error';
            $this->renderDramaView('drama_details', ['publish_form_data' => $formData]);
            return;
        }

        $publishData = [
            'category_id' => (int)$categoryId,
            'public_description' => $formData['public_description'],
            'genre' => $drama->genre ?? '',
            'language' => $formData['language'],
            'duration_minutes' => (int)$duration,
            'venue' => $drama->venue ?? '',
            'event_date' => $drama->event_date ?? null,
            'event_time' => $drama->event_time ?? null,
            'ticket_price' => isset($drama->ticket_price) ? number_format((float)$drama->ticket_price, 2, '.', '') : '0.00',
            'showing_prices' => $formData['showing_prices'],
            'poster_image' => $posterName,
        ];

        $ok = $this->dramaModel->publishDrama((int)$drama->id, (int)$_SESSION['user_id'], $publishData);

        if ($ok) {
            $queued = $this->dramaModel->queuePosterForAdminHome((int)$drama->id);
            $_SESSION['message'] = $queued
                ? 'Drama published successfully. Poster sent to admin for home page review.'
                : 'Drama published successfully. Poster queue could not be updated for admin.';
            $_SESSION['message_type'] = $queued ? 'success' : 'info';
        } else {
            $_SESSION['message'] = 'Failed to publish drama. Please try again.';
            $_SESSION['message_type'] = 'error';
        }

        header('Location: ' . ROOT . '/director/drama_details?drama_id=' . (int)$drama->id);
        exit;
    }

    protected function renderDramaView($view, array $data = [], ?callable $dataBuilder = null)
    {
        $drama = $this->authorizeDrama();

        $categories = [];
        if ($this->dramaModel && method_exists($this->dramaModel, 'getAllCategories')) {
            $categories = $this->dramaModel->getAllCategories() ?? [];
        }

        if ($dataBuilder) {
            $additional = $dataBuilder($drama);
            if (is_array($additional)) {
                $data = array_merge($data, $additional);
            }
        }

        $payload = array_merge(['drama' => $drama, 'categories' => $categories], $data);
        $this->view('director/' . $view, $payload);
    }

    protected function redirectToManageRoles(int $dramaId, array $params = [])
    {
        $query = array_merge(['drama_id' => $dramaId], $params);
        $url = ROOT . '/director/manage_roles';
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }
        header('Location: ' . $url);
        exit;
    }

    protected function redirectToCreateRole(int $dramaId)
    {
        $url = ROOT . '/director/create_role?drama_id=' . $dramaId;
        header('Location: ' . $url);
        exit;
    }

    protected function redirectToRoleDetails(int $dramaId, int $roleId, array $params = [])
    {
        $query = array_merge(['drama_id' => $dramaId, 'role_id' => $roleId], $params);
        $url = ROOT . '/director/view_role?' . http_build_query($query);
        header('Location: ' . $url);
        exit;
    }

    protected function respondWithRedirect(bool $success, string $message, int $dramaId, array $options = [], ?string $flashType = null)
    {
        $redirectUrl = $this->buildRedirectUrl($dramaId, $options);

        if ($this->expectsJson()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $success,
                'message' => $message,
                'redirect' => $redirectUrl,
            ]);
            exit;
        }

        $_SESSION['message'] = $message;
        $_SESSION['message_type'] = $flashType ?? ($success ? 'success' : 'error');

        header('Location: ' . $redirectUrl);
        exit;
    }

    protected function buildRedirectUrl(int $dramaId, array $options = []): string
    {
        $route = $options['route'] ?? null;
        $roleId = isset($options['role_id']) ? (int)$options['role_id'] : null;
        $applicationId = isset($options['application_id']) ? (int)$options['application_id'] : null;

        if ($route === 'search') {
            $url = ROOT . '/director/search_artists?drama_id=' . $dramaId;
            if ($roleId) {
                $url .= '&role_id=' . $roleId;
            }
            return $url;
        }

        if ($route === 'view' && $roleId) {
            return ROOT . '/director/view_role?drama_id=' . $dramaId . '&role_id=' . $roleId;
        }

        if ($route === 'application_profile' && $applicationId) {
            return ROOT . '/director/application_profile?drama_id=' . $dramaId . '&application_id=' . $applicationId;
        }

        $query = ['drama_id' => $dramaId];
        if ($roleId) {
            $query['role_id'] = $roleId;
        }

        return ROOT . '/director/manage_roles?' . http_build_query($query);
    }

    protected function loadApplicationForDrama(int $applicationId, $drama)
    {
        if ($applicationId <= 0 || !$this->roleModel) {
            return null;
        }

        $application = $this->roleModel->getApplicationById($applicationId);
        if (!$application || (int)$application->drama_id !== (int)$drama->id) {
            return null;
        }

        return $application;
    }

    protected function sanitizeInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $int = filter_var($value, FILTER_VALIDATE_INT);
        return $int === false ? null : (int)$int;
    }

    protected function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return stripos($accept, 'application/json') !== false
            || strtolower($requestedWith) === 'xmlhttprequest';
    }

    protected function validateRoleInput(array $data, string $mode = 'create'): array
    {
        $errors = [];
        $allowedTypes = ['lead', 'supporting', 'other'];
        $allowedStatuses = ['open', 'filled', 'closed'];

        $roleName = trim($data['role_name'] ?? '');
        if ($roleName === '') {
            $errors['role_name'] = 'Role name is required.';
        }

        $roleDescription = trim($data['role_description'] ?? '');
        if ($roleDescription === '') {
            $errors['role_description'] = 'Role description is required.';
        }

        $roleType = strtolower(trim($data['role_type'] ?? 'supporting'));
        if (!in_array($roleType, $allowedTypes, true)) {
            $errors['role_type'] = 'Select a valid role type.';
        }

        $salaryRaw = trim($data['salary'] ?? '');
        $salaryValue = null;
        if ($salaryRaw !== '') {
            $salaryNumeric = filter_var($salaryRaw, FILTER_VALIDATE_FLOAT);
            if ($salaryNumeric === false || $salaryNumeric < 0) {
                $errors['salary'] = 'Salary must be a non-negative number.';
            } else {
                $salaryValue = number_format($salaryNumeric, 2, '.', '');
            }
        }

        $positionsRaw = trim((string)($data['positions_available'] ?? ''));
        $positionsValue = filter_var($positionsRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($positionsValue === false) {
            $errors['positions_available'] = 'Positions must be a positive integer.';
        }

        $requirements = trim($data['requirements'] ?? '');

        $status = strtolower(trim($data['status'] ?? 'open'));
        if ($mode === 'update') {
            if (!in_array($status, $allowedStatuses, true)) {
                $errors['status'] = 'Select a valid status.';
            }
        } else {
            $status = 'open';
        }

        $normalized = [
            'role_name' => $roleName,
            'role_description' => $roleDescription,
            'role_type' => $roleType,
            'salary' => $salaryValue,
            'positions_available' => $positionsValue !== false ? $positionsValue : null,
            'requirements' => $requirements !== '' ? $requirements : null,
            'status' => $status,
        ];

        return [$errors, $normalized];
    }

    protected function findRoleForDrama($roleId, int $dramaId)
    {
        if (!$this->roleModel || !$roleId) {
            return null;
        }

        $role = $this->roleModel->getRoleById((int)$roleId);
        if (!$role) {
            return null;
        }

        if ((int)$role->drama_id !== (int)$dramaId) {
            return null;
        }

        return $role;
    }

    protected function authorizeDrama()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if (!$this->dramaModel) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $dramaId = $this->getQueryParam('drama_id');
        if (!$dramaId) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $drama = $this->dramaModel->getDramaById((int)$dramaId);
        $ownerId = $drama ? (int)($drama->creator_artist_id ?? $drama->created_by ?? 0) : 0;

        if (!$drama || $ownerId !== (int)$_SESSION['user_id']) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        return $drama;
    }

    protected function handleImageUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/certificates/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'certificate_' . time() . '_' . uniqid('', true) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }

    protected function handlePosterUpload($file)
    {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 8 * 1024 * 1024; // 8MB

        if (!in_array($file['type'], $allowedTypes)) {
            return false;
        }

        if ($file['size'] > $maxSize) {
            return false;
        }

        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/dramas/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'drama_poster_' . time() . '_' . uniqid('', true) . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }

        return false;
    }

    /**
     * Remove an artist assignment from a role
     */
    public function remove_assignment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            $this->dashboard();
            return;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $_SESSION['message'] = 'Role management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $assignmentId = (int)($_POST['assignment_id'] ?? 0);
        $roleId = (int)($_POST['role_id'] ?? 0);

        if (!$assignmentId || !$roleId) {
            $_SESSION['message'] = 'Invalid request. Missing assignment or role information.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        // Verify the role belongs to this drama
        $role = $this->findRoleForDrama($roleId, (int)$drama->id);
        if (!$role) {
            $_SESSION['message'] = 'Role not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        // Remove the assignment
        $removed = $this->roleModel->removeAssignment($assignmentId);

        if ($removed) {
            $_SESSION['message'] = 'Artist removed from role successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to remove artist assignment. Please try again.';
            $_SESSION['message_type'] = 'error';
        }

        // Redirect back to role details or manage roles
        $returnTo = $_POST['return_to'] ?? 'manage_roles';
        if ($returnTo === 'role_details') {
            header("Location: " . ROOT . "/director/view_role?drama_id=" . $drama->id . "&role_id=" . $roleId);
        } else {
            $this->redirectToManageRoles((int)$drama->id);
        }
        exit;
    }
}

?>