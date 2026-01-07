<?php
class AudienceProfileEdit
{
    use Controller;
    protected $model = null;

    public function __construct()
    {
        $this->model = $this->getModel("M_audience");
    }

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has audience role
        if ($_SESSION['user_role'] !== 'audience') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $data = [
            'profile' => null,
            'bio' => null,
            'profile_image' => null,
            'success' => '',
            'error' => '',
            'errors' => []
        ];

        // Get current profile data
        $data['profile'] = $this->model->getProfile($_SESSION['user_id']);
        $data['bio'] = $this->model->getBio($_SESSION['user_id']);
        $data['profile_image'] = $this->model->getProfileImage($_SESSION['user_id']);

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];

            // Get form data
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $bio = trim($_POST['bio'] ?? '');

            // Validate inputs
            if (empty($full_name)) {
                $errors[] = "Full name is required";
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Valid email is required";
            } else {
                // Check if email is already taken by another user
                if ($this->model->checkEmailExists($email, $_SESSION['user_id'])) {
                    $errors[] = "Email is already taken";
                }
            }

            if (empty($phone)) {
                $errors[] = "Phone number is required";
            }

            // Handle profile image upload
            if (!empty($_FILES['profile_image']['name'])) {
                $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if (!in_array($_FILES['profile_image']['type'], $allowed_types)) {
                    $errors[] = "Only JPG, PNG, and GIF images are allowed";
                } elseif ($_FILES['profile_image']['size'] > $max_size) {
                    $errors[] = "Image size must be less than 5MB";
                } else {
                    $upload_dir = "../app/uploads/profile_images/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                    $new_filename = "profile_" . $_SESSION['user_id'] . "_" . time() . "." . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                        $this->model->updateProfileImage($_SESSION['user_id'], $new_filename);
                    } else {
                        $errors[] = "Failed to upload profile image";
                    }
                }
            }

            // If no errors, update profile
            if (empty($errors)) {
                $profile_data = [
                    'full_name' => $full_name,
                    'email' => $email,
                    'phone' => $phone
                ];

                $profile_updated = $this->model->updateProfile($_SESSION['user_id'], $profile_data);
                $bio_updated = $this->model->updateBio($_SESSION['user_id'], $bio);

                if ($profile_updated) {
                    $_SESSION['user_name'] = $full_name;
                    $data['success'] = "Profile updated successfully!";
                    // Refresh profile data
                    $data['profile'] = $this->model->getProfile($_SESSION['user_id']);
                    $data['bio'] = $this->model->getBio($_SESSION['user_id']);
                    $data['profile_image'] = $this->model->getProfileImage($_SESSION['user_id']);
                } else {
                    $data['error'] = "Failed to update profile";
                }
            } else {
                $data['errors'] = $errors;
            }
        }

        $this->view('audience_profile_edit', $data);
    }
}
?>