<?php

class Director{
    use Controller;

    protected $dramaModel;
    protected $roleModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->roleModel = $this->getModel('M_role');
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
        $this->renderDramaView('manage_roles', [], function ($drama) {
            $roles = $this->roleModel ? $this->roleModel->getRolesByDrama((int)$drama->id) : [];
            $stats = $this->roleModel ? $this->roleModel->getRoleStats((int)$drama->id) : null;

            $formData = $_SESSION['role_form_data'] ?? null;
            $formErrors = $_SESSION['role_form_errors'] ?? [];
            $formMode = $_SESSION['role_form_mode'] ?? 'create';
            $formRoleId = $_SESSION['role_form_role_id'] ?? null;

            unset($_SESSION['role_form_data'], $_SESSION['role_form_errors'], $_SESSION['role_form_mode'], $_SESSION['role_form_role_id']);

            $requestedRoleId = $this->getQueryParam('role_id');
            if ($requestedRoleId !== null) {
                $requestedRoleId = (int)$requestedRoleId;
            }

            if ($formMode === 'update' && $formRoleId) {
                $formRoleId = (int)$formRoleId;
                if (!$requestedRoleId) {
                    $requestedRoleId = $formRoleId;
                }
            }

            $editingRole = null;
            if ($requestedRoleId) {
                $editingRole = $this->findRoleForDrama($requestedRoleId, (int)$drama->id);
            }

            return [
                'roles' => $roles,
                'roleStats' => $stats,
                'editing_role' => $editingRole,
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
            $_SESSION['role_form_mode'] = 'create';
            $this->redirectToManageRoles((int)$drama->id);
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
        $this->redirectToManageRoles((int)$drama->id);
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
            $this->redirectToManageRoles((int)$drama->id, ['role_id' => $role->id]);
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
            $this->redirectToManageRoles((int)$drama->id, ['role_id' => $role->id]);
        }

        $_SESSION['message'] = 'Failed to update role. Please try again.';
        $_SESSION['message_type'] = 'error';
        $this->redirectToManageRoles((int)$drama->id, ['role_id' => $role->id]);
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
        $this->renderDramaView('assign_managers');
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
        $this->renderDramaView('search_artists');
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