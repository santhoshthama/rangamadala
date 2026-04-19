<?php

require_once __DIR__ . '/DirectorFeatureControllerTrait.php';

class DirectorRoleController
{
    use Controller, DirectorFeatureControllerTrait;

    protected $dramaModel;
    protected $roleModel;
    protected $artistModel;
    protected $notificationModel;
    protected $profileModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->roleModel = $this->getModel('M_role');
        $this->artistModel = $this->getModel('M_artist');
        $this->notificationModel = $this->getModel('M_notification');
        $this->profileModel = $this->getModel('M_universal_profile');
    }

    public function manage_roles()
    {
        $this->renderDramaView('manage_roles_overview', [], function ($drama) {
            $roles = $this->roleModel ? $this->roleModel->getRolesByDrama((int)$drama->id) : [];
            $stats = $this->roleModel ? $this->roleModel->getRoleStats((int)$drama->id) : null;
            $pendingApplications = $this->roleModel ? $this->roleModel->getApplicationsByDrama((int)$drama->id, 'pending') : [];
            $allRequests = $this->roleModel ? $this->roleModel->getRoleRequestsByDrama((int)$drama->id) : [];

            $pendingRequests = array_values(array_filter($allRequests, static function ($req) {
                $status = strtolower((string)($req->status ?? ''));
                return in_array($status, ['pending', 'interview'], true);
            }));

            $publishedRoles = $this->roleModel ? $this->roleModel->getPublishedRolesByDrama((int)$drama->id) : [];

            $publishableRoles = array_values(array_filter($roles, static function ($role) {
                $status = strtolower((string)($role->status ?? 'open'));
                return $status !== 'filled';
            }));

            $publishedRoleIds = array_values(array_map(static function ($role) {
                return (int)($role->id ?? 0);
            }, $publishedRoles));

            $roleTypes = [
                'lead' => 'Lead',
                'supporting' => 'Supporting',
                'other' => 'Other',
            ];

            $roleStatuses = [
                'open' => 'Open',
                'filled' => 'Filled',
                'closed' => 'Closed',
            ];

            return [
                'roles' => is_array($roles) ? $roles : [],
                'roleStats' => $stats,
                'pendingApplications' => is_array($pendingApplications) ? $pendingApplications : [],
                'pendingRequests' => is_array($pendingRequests) ? $pendingRequests : [],
                'publishedRoles' => is_array($publishedRoles) ? $publishedRoles : [],
                'publishableRoles' => is_array($publishableRoles) ? $publishableRoles : [],
                'publishedRoleIds' => is_array($publishedRoleIds) ? $publishedRoleIds : [],
                'roleTypes' => $roleTypes,
                'roleStatuses' => $roleStatuses,
                'dramaName' => (string)($drama->drama_name ?? 'Drama'),
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
            header('Location: ' . ROOT . '/director/dashboard?drama_id=' . (int)$dramaId);
            exit;
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
            $groupedApplications = $this->groupItemsByStatus($applications, 'status');
            $groupedRequests = $this->groupItemsByStatus($requests, 'status');

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
                'groupedApplications' => $groupedApplications,
                'groupedRequests' => $groupedRequests,
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
            header('Location: ' . ROOT . '/director/dashboard?drama_id=' . (int)$dramaId);
            exit;
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
            header('Location: ' . ROOT . '/director/dashboard?drama_id=' . (int)$dramaId);
            exit;
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

    public function search_artists()
    {
        $roleId = $this->getQueryParam('role_id');
        if (!$roleId) {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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
                'dramaId' => (int)$drama->id,
                'roleId' => (int)$role->id,
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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

    public function send_role_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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

    public function update_role_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $this->respondWithRedirect(false, 'Role management is currently unavailable.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $requestId = (int)($_POST['request_id'] ?? 0);
        $status = strtolower(trim((string)($_POST['status'] ?? 'pending')));
        $note = trim((string)($_POST['note'] ?? ''));
        $interviewRaw = trim((string)($_POST['interview_at'] ?? ''));

        if ($requestId <= 0) {
            $this->respondWithRedirect(false, 'Invalid request selected.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $allowedStatuses = ['pending', 'interview'];
        if (!in_array($status, $allowedStatuses, true)) {
            $this->respondWithRedirect(false, 'Invalid request status selected.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        if (strlen($note) > 1000) {
            $this->respondWithRedirect(false, 'Request note is too long. Please keep it under 1000 characters.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $interviewAt = null;
        if ($interviewRaw !== '') {
            $timestamp = strtotime($interviewRaw);
            if ($timestamp === false) {
                $this->respondWithRedirect(false, 'Invalid interview date/time provided.', (int)$drama->id, [
                    'route' => 'manage',
                ]);
            }

            if ($timestamp <= time()) {
                $this->respondWithRedirect(false, 'Interview date/time must be in the future.', (int)$drama->id, [
                    'route' => 'manage',
                ]);
            }

            $interviewAt = date('Y-m-d H:i:s', $timestamp);
        }

        if ($status === 'interview' && $interviewAt === null) {
            $this->respondWithRedirect(false, 'Provide an interview date/time when setting request status to interview.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        if ($status === 'pending') {
            $interviewAt = null;
        }

        $requestRecord = $this->roleModel->getRoleRequestById($requestId);
        if (!$requestRecord || (int)($requestRecord->drama_id ?? 0) !== (int)$drama->id) {
            $this->respondWithRedirect(false, 'Request not found or inaccessible.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $updated = $this->roleModel->updateRoleRequestByDirector(
            $requestId,
            (int)$_SESSION['user_id'],
            (int)$drama->id,
            $status,
            $note !== '' ? $note : null,
            $interviewAt
        );

        if ($updated) {
            if ($this->notificationModel && !empty($requestRecord->artist_id)) {
                $this->notificationModel->createNotification([
                    'user_id' => (int)$requestRecord->artist_id,
                    'drama_id' => (int)$drama->id,
                    'type' => 'role_assigned',
                    'title' => 'Role Request Updated: ' . ($requestRecord->role_name ?? 'Role'),
                    'message' => 'The director updated your request for "' . ($requestRecord->role_name ?? 'Role') . '" in "' . ($drama->drama_name ?? 'Drama') . '".',
                    'link' => ROOT . '/artistdashboard',
                ]);
            }

            $this->respondWithRedirect(true, 'Role request updated successfully.', (int)$drama->id, [
                'route' => 'manage',
            ]);
        }

        $this->respondWithRedirect(false, 'Unable to update request. It may already be handled.', (int)$drama->id, [
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
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

    public function remove_assignment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                $this->redirectToManageRoles((int)$dramaId);
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->roleModel) {
            $_SESSION['message'] = 'Role management is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $assignmentId = (int)($_POST['assignment_id'] ?? 0);
        $roleId = (int)($_POST['role_id'] ?? 0);
        $removeReason = trim((string)($_POST['remove_reason'] ?? ''));

        if (!$assignmentId || !$roleId) {
            $_SESSION['message'] = 'Invalid request. Missing assignment or role information.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        if ($removeReason === '') {
            $_SESSION['message'] = 'Please provide a reason for removing this artist from the role.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/view_role?drama_id=' . $drama->id . '&role_id=' . $roleId);
            exit;
        }

        if (strlen($removeReason) > 1000) {
            $_SESSION['message'] = 'Removal reason is too long. Please keep it under 1000 characters.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/view_role?drama_id=' . $drama->id . '&role_id=' . $roleId);
            exit;
        }

        $role = $this->findRoleForDrama($roleId, (int)$drama->id);
        if (!$role) {
            $_SESSION['message'] = 'Role not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            $this->redirectToManageRoles((int)$drama->id);
        }

        $assignment = $this->roleModel->getAssignmentById($assignmentId);
        if (!$assignment || (int)$assignment->role_id !== $roleId || (int)($assignment->drama_id ?? 0) !== (int)$drama->id) {
            $_SESSION['message'] = 'Assignment not found or inaccessible.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/view_role?drama_id=' . $drama->id . '&role_id=' . $roleId);
            exit;
        }

        $removed = $this->roleModel->removeAssignment($assignmentId);

        if ($removed) {
            if ($this->notificationModel) {
                $dramaLink = ROOT . '/artistdashboard/view_drama?drama_id=' . (int)$drama->id;

                if (!empty($assignment->artist_id)) {
                    $this->notificationModel->createNotification([
                        'user_id' => (int)$assignment->artist_id,
                        'drama_id' => (int)$drama->id,
                        'type' => 'role_removed',
                        'title' => 'Role Assignment Removed: ' . ($assignment->role_name ?? 'Role'),
                        'message' => 'Your assignment for the role "' . ($assignment->role_name ?? 'Role') . '" in "' . ($drama->drama_name ?? 'Drama') . '" has been removed by the director. Reason: ' . $removeReason,
                        'link' => $dramaLink,
                    ]);
                }

                $directorLogLink = ROOT . '/director/view_role?drama_id=' . (int)$drama->id . '&role_id=' . (int)$roleId;
                $this->notificationModel->createNotification([
                    'user_id' => (int)($_SESSION['user_id'] ?? 0),
                    'drama_id' => (int)$drama->id,
                    'type' => 'role_artist_removed',
                    'title' => 'Artist Removed from Role: ' . ($assignment->role_name ?? 'Role'),
                    'message' => 'You removed "' . ($assignment->artist_name ?? 'Artist') . '" from the role "' . ($assignment->role_name ?? 'Role') . '" in "' . ($drama->drama_name ?? 'Drama') . '". Reason: ' . $removeReason,
                    'link' => $directorLogLink,
                ]);
            }

            $_SESSION['message'] = 'Artist removed from role successfully.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Failed to remove artist assignment. Please try again.';
            $_SESSION['message_type'] = 'error';
        }

        $returnTo = $_POST['return_to'] ?? 'manage_roles';
        if ($returnTo === 'role_details') {
            header('Location: ' . ROOT . '/director/view_role?drama_id=' . $drama->id . '&role_id=' . $roleId);
        } else {
            $this->redirectToManageRoles((int)$drama->id);
        }
        exit;
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

    protected function expectsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';

        return stripos($accept, 'application/json') !== false
            || strtolower($requestedWith) === 'xmlhttprequest';
    }

    protected function groupItemsByStatus(array $items, string $statusKey = 'status'): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $status = 'pending';

            if (is_object($item) && isset($item->{$statusKey})) {
                $status = strtolower((string)$item->{$statusKey});
            } elseif (is_array($item) && array_key_exists($statusKey, $item)) {
                $status = strtolower((string)$item[$statusKey]);
            }

            if ($status === '') {
                $status = 'pending';
            }

            $grouped[$status][] = $item;
        }

        return $grouped;
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

}
