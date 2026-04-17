<?php

class ArtistRegister
{
    use Controller;

    public function index()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $nic_number = trim($_POST['nic_number'] ?? '');
            $nic_photo_front = $_FILES['nic_photo_front']['name'] ?? null;
            $nic_photo_back = $_FILES['nic_photo_back']['name'] ?? null;

            $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/';
            $phonePattern = '/^(?:\+94|94|0)7\d{8}$/';
            $nicPattern = '/^(?:\d{12}|\d{9}[Vv])$/';

            // 🔹 Basic validation
            if (empty($full_name)) {
                $errors[] = "Full name is required.";
            }
            if (empty($email)) {
                $errors[] = "Email is required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
            if (!preg_match($passwordPattern, $password)) {
                $errors[] = "Password must be at least 6 characters and include uppercase, lowercase, number, and symbol.";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Password confirmation does not match.";
            }
            if (empty($phone)) {
                $errors[] = "Phone number is required.";
            } elseif (!preg_match($phonePattern, $phone)) {
                $errors[] = "Enter a valid Sri Lankan mobile number (e.g. 07X XXX XXXX or +94 XXX XXX XXX).";
            }
            if (empty($nic_number)) {
                $errors[] = "NIC number is required.";
            } elseif (!preg_match($nicPattern, $nic_number)) {
                $errors[] = "Enter a valid Sri Lankan NIC (12 digits or old format ending with V).";
            }
            if (!$nic_photo_front) {
                $errors[] = "NIC front photo is required.";
            }
            if (!$nic_photo_back) {
                $errors[] = "NIC back photo is required.";
            }

            if (empty($errors)) {
                $model = new M_artist();

                // 🔹 Handle file uploads (save to public/uploads/nic/)
                $uploadDir = __DIR__ . '/../../public/uploads/nic/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // create folder if missing
                }

                $frontUniqueName = uniqid() . "_front_" . basename($nic_photo_front);
                $backUniqueName = uniqid() . "_back_" . basename($nic_photo_back);

                $frontUploadPath = $uploadDir . $frontUniqueName;
                $backUploadPath = $uploadDir . $backUniqueName;

                $frontDbPath = 'uploads/nic/' . $frontUniqueName;
                $backDbPath = 'uploads/nic/' . $backUniqueName;

                $maxSize = 5 * 1024 * 1024;
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

                if (!empty($_FILES['nic_photo_front']['size']) && $_FILES['nic_photo_front']['size'] > $maxSize) {
                    $errors[] = "NIC front photo must be less than 5MB.";
                } elseif (!empty($_FILES['nic_photo_front']['type']) && !in_array($_FILES['nic_photo_front']['type'], $allowedTypes, true)) {
                    $errors[] = "NIC front photo must be JPG or PNG.";
                }

                if (!empty($_FILES['nic_photo_back']['size']) && $_FILES['nic_photo_back']['size'] > $maxSize) {
                    $errors[] = "NIC back photo must be less than 5MB.";
                } elseif (!empty($_FILES['nic_photo_back']['type']) && !in_array($_FILES['nic_photo_back']['type'], $allowedTypes, true)) {
                    $errors[] = "NIC back photo must be JPG or PNG.";
                }

                if (empty($errors) && is_uploaded_file($_FILES['nic_photo_front']['tmp_name'])) {
                    if (!move_uploaded_file($_FILES['nic_photo_front']['tmp_name'], $frontUploadPath)) {
                        $errors[] = "Failed to upload NIC front photo.";
                    }
                }

                if (empty($errors) && is_uploaded_file($_FILES['nic_photo_back']['tmp_name'])) {
                    if (!move_uploaded_file($_FILES['nic_photo_back']['tmp_name'], $backUploadPath)) {
                        $errors[] = "Failed to upload NIC back photo.";
                    }
                }

                if (empty($errors)) {
                    // 🔹 Save artist to DB
                    if ($model->register($full_name, $email, $password, $phone, $frontDbPath, $nic_number, $backDbPath)) {
                        $_SESSION['registration_success'] = true;
                        $_SESSION['registration_message'] = 'Your artist registration has been submitted successfully! Your account is pending admin verification. You will be able to login once your NIC and details are verified.';
                        header("Location: " . ROOT . "/Login");
                        exit;
                    } else {
                        $errors[] = "Registration failed. Email may already exist.";
                    }
                }
            }
        }

        // 🔹 Send errors to the view
        $this->view('artist_register', [
            'errors' => $errors,
            'old' => [
                'full_name' => trim($_POST['full_name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'nic_number' => trim($_POST['nic_number'] ?? '')
            ]
        ]);
    }
}
?>
