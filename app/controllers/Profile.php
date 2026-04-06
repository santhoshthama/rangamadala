<?php

class Profile
{
    use Controller;

    public function index()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];
        $userModel = $this->getUserModel($userRole);

        $data = [
            'user' => $userModel->getUserById($userId),
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
            $fullName = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $yearsInput = trim($_POST['years_experience'] ?? '');
            $bio = trim($_POST['bio'] ?? '');
            $location = trim($_POST['location'] ?? '');
            $website = trim($_POST['website'] ?? '');

            $errors = [];
            $profileImageName = null;
            $uploadedFilePath = null;
            $uploadDir = dirname(__DIR__, 2) . '/public/uploads/profile_images/';
            $oldImageStored = $data['user']->profile_image ?? null;
            $oldImageName = $oldImageStored ? basename($oldImageStored) : null;

            if ($fullName === '') {
                $errors[] = 'Full name is required.';
            }

            if ($phone === '') {
                $errors[] = 'Phone number is required.';
            }

            $yearsValue = null;
            if ($yearsInput !== '') {
                if (!ctype_digit($yearsInput)) {
                    $errors[] = 'Years of experience must be a whole number.';
                } else {
                    $yearsValue = (int)$yearsInput;
                }
            }

            if ($website !== '' && !filter_var($website, FILTER_VALIDATE_URL)) {
                $errors[] = 'Please enter a valid website URL.';
            }

            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['profile_image'];

                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $errors[] = 'Error uploading profile image.';
                } elseif ($file['size'] > 5 * 1024 * 1024) {
                    $errors[] = 'Profile image must be smaller than 5MB.';
                } elseif (@getimagesize($file['tmp_name']) === false) {
                    $errors[] = 'Uploaded file is not a valid image.';
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
                        $profileImageName = $userRole . '_' . $userId . '_' . time() . '.' . $extension;
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
                'full_name' => $fullName,
                'phone' => $phone,
                'years_experience' => $yearsInput,
                'bio' => $bio,
                'location' => $location,
                'website' => $website
            ];

            if (empty($errors)) {
                $updateFields = [
                    'full_name' => $fullName,
                    'phone' => $phone,
                    'years_experience' => $yearsInput === '' ? null : $yearsValue
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

                $updated = $userModel->updateProfile($userId, $updateFields);

                if ($updated) {
                    if ($profileImageName !== null && $oldImageName && $oldImageName !== $profileImageName) {
                        $oldPath = $uploadDir . $oldImageName;
                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $_SESSION['user_name'] = $fullName;
                    $data['success'] = 'Profile updated successfully.';
                    $data['user'] = $userModel->getUserById($userId);
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
        return new M_universal_profile();
    }
}
