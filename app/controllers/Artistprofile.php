<?php

class Artistprofile
{
    use Controller;

    public function index()
    {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $artist_model = $this->getModel('M_artist');
        $user_id = $_SESSION['user_id'];

        $data = [
            'user' => $artist_model->get_artist_by_id($user_id),
            'errors' => [],
            'success' => '',
            'form' => [
                'full_name' => '',
                'phone' => '',
                'years_experience' => ''
            ]
        ];

        if (!$data['user']) {
            $data['errors'][] = 'Unable to load artist profile.';
            $this->view('artistprofile', $data);
            return;
        }

        $data['form'] = [
            'full_name' => $data['user']->full_name ?? '',
            'phone' => $data['user']->phone ?? '',
            'years_experience' => isset($data['user']->years_experience) && $data['user']->years_experience !== null
                ? (string)$data['user']->years_experience
                : ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $years_input = trim($_POST['years_experience'] ?? '');

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
                        $profileImageName = 'artist_' . $user_id . '_' . time() . '.' . $extension;
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
                'years_experience' => $years_input
            ];

            if (empty($errors)) {
                $updateFields = [
                    'full_name' => $full_name,
                    'phone' => $phone,
                    'years_experience' => $years_input === '' ? null : $yearsValue
                ];

                if ($profileImageName !== null) {
                    $updateFields['profile_image'] = $profileImageName;
                }

                $updated = $artist_model->update_artist_profile($user_id, $updateFields);

                if ($updated) {
                    if ($profileImageName !== null && $oldImageName && $oldImageName !== $profileImageName) {
                        $oldPath = $uploadDir . $oldImageName;
                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $_SESSION['user_name'] = $full_name;
                    $data['success'] = 'Profile updated successfully.';
                    $data['user'] = $artist_model->get_artist_by_id($user_id);
                    $data['form'] = [
                        'full_name' => $data['user']->full_name ?? '',
                        'phone' => $data['user']->phone ?? '',
                        'years_experience' => isset($data['user']->years_experience) && $data['user']->years_experience !== null
                            ? (string)$data['user']->years_experience
                            : ''
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

        $this->view('artistprofile', $data);
    }
}
