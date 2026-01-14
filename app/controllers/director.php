<?php

class Director{
    use Controller;

    protected $dramaModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
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
        $this->renderDramaView('manage_roles');
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

    protected function renderDramaView($view, array $data = [])
    {
        $drama = $this->authorizeDrama();
        $payload = array_merge(['drama' => $drama], $data);
        $this->view('director/' . $view, $payload);
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