<?php

class Profile
{
    use Controller;

    public function index()
    {
        // Universal profile - works for all roles
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $user_role = $_SESSION['user_role'];
        
        // Get user model based on role
        $user_model = $this->getUserModel($user_role);
        
        $data = [
            'user' => $user_model->getUserById($user_id),
            'errors' => [],
            'success' => '',
            'form' => [
                'full_name' => '',
                'phone' => '',
                'years_experience' => '',
                'bio' => '',
                'location' => '',
                'website' => ''
            ]
        ];

        if (!$data['user']) {
            $data['errors'][] = 'Unable to load profile.';
            $this->view('profile', $data);
            return;
        }

        $data['form'] = [
            'full_name' => $data['user']->full_name ?? '',
            'phone' => $data['user']->phone ?? '',
            'years_experience' => isset($data['user']->years_experience) && $data['user']->years_experience !== null
                ? (string)$data['user']->years_experience
                : '',
            'bio' => $data['user']->bio ?? '',
            'location' => $data['user']->location ?? '',
            'website' => $data['user']->website ?? ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $years_input = trim($_POST['years_experience'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $website = trim($_POST['website'] ?? '');

            $errors = [];
            $profileImageName = null;
            $uploadedFilePath = null;
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/profile_images/';
            $oldImageStored = $data['user']->profile_image ?? null;
            $oldImageName = $oldImageStored ? basename($oldImageStored) : null;

            if ($full_name === '') {
                $errors[] = 'Full name is required.';
            }

            if ($phone === '') {
                $errors[] = 'Phone number is required.';
            }

            $yearsValue = null;
            if ($years_input !== '') {
                if (!ctype_digit($years_input)) {
                    $errors[] = 'Years of experience must be a whole number.';
                } else {
                    $yearsValue = (int)$years_input;
                }
            }

            if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid website URL (e.g., https://yourwebsite.com).';
            }

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['profile_image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = 'Error uploading profile image.';
                } else {
                    if ($file['size'] > 5 * 1024 * 1024) {
                        $errors[] = 'Profile image must be smaller than 5MB.';
                    } else {
                        $imageInfo = @getimagesize($file['tmp_name']);
                        if ($imageInfo === false) {
                            $errors[] = 'Uploaded file is not a valid image.';
                        }
                    }
                }

                if (empty($errors)) {
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }

                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                    if (!in_array($extension, $allowedExtensions, true)) {
                        $errors[] = 'Only JPG, JPEG, PNG, GIF, or WEBP images are allowed.';
                    } else {
                        $profileImageName = $user_role . '_' . $user_id . '_' . time() . '.' . $extension;
                        $uploadedFilePath = $uploadDir . $profileImageName;

                        if (!move_uploaded_file($file['tmp_name'], $uploadedFilePath)) {
                            $errors[] = 'Failed to save the uploaded profile image.';
                            $profileImageName = null;
                            $uploadedFilePath = null;
                        }
                    }
                }
            }

            $data['form'] = [
                'full_name' => $full_name,
                'phone' => $phone,
                'years_experience' => $years_input,
                'bio' => $bio,
                'location' => $location,
                'website' => $website
            ];

            if (empty($errors)) {
                $updateFields = [
                    'full_name' => $full_name,
                    'phone' => $phone,
                    'years_experience' => $years_input === '' ? null : $yearsValue
                ];

                if ($bio !== '') {
                    $updateFields['bio'] = $bio;
                }
                if ($location !== '') {
                    $updateFields['location'] = $location;
                }
                if ($website !== '') {
                    $updateFields['website'] = $website;
                }

                if ($profileImageName !== null) {
                    $updateFields['profile_image'] = $profileImageName;
                }

                $updated = $user_model->updateProfile($user_id, $updateFields);

                if ($updated) {
                    if ($profileImageName !== null && $oldImageName && $oldImageName !== $profileImageName) {
                        $oldPath = $uploadDir . $oldImageName;
                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $_SESSION['user_name'] = $full_name;
                    $data['success'] = 'Profile updated successfully.';
                    $data['user'] = $user_model->getUserById($user_id);
                    $data['form'] = [
                        'full_name' => $data['user']->full_name ?? '',
                        'phone' => $data['user']->phone ?? '',
                        'years_experience' => isset($data['user']->years_experience) && $data['user']->years_experience !== null
                            ? (string)$data['user']->years_experience
                            : '',
                        'bio' => $data['user']->bio ?? '',
                        'location' => $data['user']->location ?? '',
                        'website' => $data['user']->website ?? ''
                    ];
                } else {
                    if ($profileImageName !== null && $uploadedFilePath && is_file($uploadedFilePath)) {
                        unlink($uploadedFilePath);
                    }
                    $errors[] = 'Failed to update profile. Please try again.';
                }
            }

            $data['errors'] = $errors;
        }

        $this->view('profile', $data);
    }

    private function getUserModel($role)
    {
        // All roles use the same universal profile model
        return new M_universal_profile();
    }
}
