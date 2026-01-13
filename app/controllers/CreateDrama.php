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
        $drama_model = $this->getModel('M_drama');
        
        // Get categories for dropdown
        $data['categories'] = $drama_model->getAllCategories();
        
        // Initialize empty data for form
        $data['form_data'] = [
            'title' => '',
            'description' => '',
            'category_id' => '',
            'venue' => '',
            'event_date' => '',
            'event_time' => '',
            'duration' => '',
            'ticket_price' => '',
            'language' => 'Sinhala',
            'genre' => ''
        ];
        
        $this->view('create_drama', $data);
    }

    private function createDrama()
    {
        $drama_model = $this->getModel('M_drama');
        
        // Validate required fields
        $errors = [];
        
        if (empty($_POST['title'])) {
            $errors[] = "Drama title is required";
        }
        
        if (empty($_POST['description'])) {
            $errors[] = "Description is required";
        }
        
        if (empty($_POST['category_id'])) {
            $errors[] = "Category is required";
        }
        
        if (empty($_POST['venue'])) {
            $errors[] = "Venue is required";
        }
        
        if (empty($_POST['event_date'])) {
            $errors[] = "Event date is required";
        }
        
        if (empty($_POST['event_time'])) {
            $errors[] = "Event time is required";
        }
        
        if (!empty($errors)) {
            $_SESSION['message'] = implode(", ", $errors);
            $_SESSION['message_type'] = 'error';
            
            // Get categories and repopulate form
            $data['categories'] = $drama_model->getAllCategories();
            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
            return;
        }
        
        // Handle image upload
        $image_name = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = $this->handleImageUpload($_FILES['image']);
            if ($image_name === false) {
                $_SESSION['message'] = "Error uploading image";
                $_SESSION['message_type'] = 'error';
                
                $data['categories'] = $drama_model->getAllCategories();
                $data['form_data'] = $_POST;
                $this->view('create_drama', $data);
                return;
            }
        }
        
        // Prepare data for database
        $drama_data = [
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description']),
            'category_id' => (int)$_POST['category_id'],
            'venue' => trim($_POST['venue']),
            'event_date' => $_POST['event_date'],
            'event_time' => $_POST['event_time'],
            'duration' => !empty($_POST['duration']) ? (int)$_POST['duration'] : null,
            'ticket_price' => !empty($_POST['ticket_price']) ? (float)$_POST['ticket_price'] : null,
            'image' => $image_name,
            'created_by' => $_SESSION['user_id']
        ];
        
        // Create the drama
        if ($drama_model->createDrama($drama_data)) {
            $_SESSION['message'] = "Drama created successfully! You are now the director of this drama.";
            $_SESSION['message_type'] = 'success';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        } else {
            $_SESSION['message'] = "Error creating drama. Please try again.";
            $_SESSION['message_type'] = 'error';
            
            $data['categories'] = $drama_model->getAllCategories();
            $data['form_data'] = $_POST;
            $this->view('create_drama', $data);
        }
    }
    
    private function handleImageUpload($file)
    {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
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
        $upload_dir = 'app/uploads/drama_images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'drama_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return $filename;
        }
        
        return false;
    }
}
