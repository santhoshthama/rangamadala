<?php

class Director{
    use Controller;

    protected $dramaModel;
    protected $roleModel;
    protected $artistModel;
    protected $pmModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->roleModel = $this->getModel('M_role');
        $this->artistModel = $this->getModel('M_artist');
        $this->pmModel = $this->getModel('M_production_manager');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->renderDramaView('dashboard');
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
            $pendingRequests = $this->roleModel ? $this->roleModel->getRoleRequestsByDrama((int)$drama->id, 'pending') : [];
            $publishedRoles = $this->roleModel ? $this->roleModel->getPublishedRolesByDrama((int)$drama->id) : [];

            return [
                'roles' => $roles,
                'roleStats' => $stats,
                'pendingApplications' => $pendingApplications,
                'pendingRequests' => $pendingRequests,
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
        $this->renderDramaView('schedule_management');
    }

    public function view_services_budget()
    {
        $this->renderDramaView('view_services_budget');
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
            $this->respondWithRedirect(true, 'Application rejected.', (int)$drama->id, [
                'route' => 'manage',
            ], 'info');
        }

        $this->respondWithRedirect(false, 'Unable to reject the application.', (int)$drama->id, [
            'route' => 'manage',
        ]);
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

    protected function renderDramaView($view, array $data = [], ?callable $dataBuilder = null)
    {
        $drama = $this->authorizeDrama();
        if ($dataBuilder) {
            $additional = $dataBuilder($drama);
            if (is_array($additional)) {
                $data = array_merge($data, $additional);
            }
        }

        $payload = array_merge(['drama' => $drama], $data);
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

        $query = ['drama_id' => $dramaId];
        if ($roleId) {
            $query['role_id'] = $roleId;
        }

        return ROOT . '/director/manage_roles?' . http_build_query($query);
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
        $allowedTypes = ['lead', 'supporting', 'ensemble', 'dancer', 'musician', 'other'];
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
}

?>