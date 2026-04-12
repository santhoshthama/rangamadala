<?php

class CreateDrama
{
    use Controller;

    public function index()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->createDrama();
        } else {
            $this->showForm();
        }
    }

    private function showForm()
    {
        // Initialize empty data for form
        $data['form_data'] = [
            'drama_name' => '',
            'certificate_number' => '',
            'owner_name' => '',
            'description' => ''
        ];
        
        $this->view('create_drama', $data);
    }

    private function createDrama()
    {
        $drama_model = $this->getModel('M_drama');
        
        // Validate required fields
        $errors = [];
        
        if (empty($_POST['drama_name'])) {
            $errors[] = "Drama name is required";
        }
        
        if (empty($_POST['certificate_number'])) {
            $errors[] = "Certificate number is required";
        }
        
        if (empty($_POST['owner_name'])) {
            $errors[] = "Owner name is required";
        }

        if (empty($_POST['description'])) {
            $errors[] = "Drama description is required";
        }
        
        if (!isset($_FILES['certificate_image']) || $_FILES['certificate_image']['error'] === UPLOAD_ERR_NO_FILE) {
            $errors[] = "Certificate image is required";
        }
        
        if (!empty($errors)) {
            $_SESSION['message'] = implode(", ", $errors);
            $_SESSION['message_type'] = 'error';
            
            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
            return;
        }
        
        // Handle certificate image upload
        $image_name = null;
        if (isset($_FILES['certificate_image']) && $_FILES['certificate_image']['error'] === UPLOAD_ERR_OK) {
            $image_name = $this->handleImageUpload($_FILES['certificate_image']);
            if ($image_name === false) {
                $_SESSION['message'] = "Error uploading certificate image";
                $_SESSION['message_type'] = 'error';
                
                $data['form_data'] = $_POST;
                $this->view('create_drama', $data);
                return;
            }
        }
        
        $certificateNumber = trim($_POST['certificate_number']);

        if ($drama_model->certificateNumberExistsInDramas($certificateNumber)) {
            $_SESSION['message'] = "This certificate number is already registered to an existing drama.";
            $_SESSION['message_type'] = 'error';

            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
            return;
        }

        if ($drama_model->hasPendingDramaRequest($_SESSION['user_id'], $certificateNumber)) {
            $_SESSION['message'] = "There is already a pending request with this certificate number.";
            $_SESSION['message_type'] = 'error';

            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
            return;
        }

        // Prepare data for pending review
        $drama_data = [
            'drama_name' => trim($_POST['drama_name']),
            'certificate_number' => $certificateNumber,
            'owner_name' => trim($_POST['owner_name']),
            'description' => trim($_POST['description']),
            'certificate_image' => $image_name,
            'requested_by' => $_SESSION['user_id']
        ];
        
        // Submit request for admin approval
        $result = $drama_model->createDramaRequest($drama_data);

        if (($result['success'] ?? false) === true) {
            $_SESSION['message'] = "Drama request submitted successfully. The drama will be created automatically after admin approval.";
            $_SESSION['message_type'] = 'success';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        } else {
            $_SESSION['message'] = $result['message'] ?? "Error submitting drama request. Please try again.";
            $_SESSION['message_type'] = 'error';
            
            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
        }
    }
    
    private function handleImageUpload($file)
    {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Validate file type
        if (!in_array($file['type'], $allowed_types)) {
            return false;
        }
        
        // Validate file size
        if ($file['size'] > $max_size) {
            return false;
        }
        
        // Create upload directory if it doesn't exist
        $upload_dir = dirname(__DIR__, 2) . '/public/uploads/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'certificate_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }
        
        return false;
    }
}
