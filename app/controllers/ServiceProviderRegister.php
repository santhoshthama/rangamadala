<?php

class ServiceProviderRegister
{
    use Controller;

    public function index()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $nic_photo = $_FILES['nic_photo']['name'] ?? null;

            // 🔹 Basic validation
            if (empty($full_name)) {
                $errors[] = "Full name is required.";
            }
            if (empty($email)) {
                $errors[] = "Email is required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            }
            if (empty($phone)) {
                $errors[] = "Phone number is required.";
            }
            if (!$nic_photo) {
                $errors[] = "NIC photo is required.";
            }

            if (empty($errors)) {
                $model = new M_service_provider();

                // 🔹 Handle file upload (save to app/uploads/)
                $uploadDir = __DIR__ . '/../uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // create folder if missing
                }

                $uniqueName = uniqid() . "_" . basename($nic_photo);
                $uploadPath = $uploadDir . $uniqueName;
                $dbPath = 'app/uploads/' . $uniqueName; // for database storage

                if (is_uploaded_file($_FILES['nic_photo']['tmp_name'])) {
                    if (!move_uploaded_file($_FILES['nic_photo']['tmp_name'], $uploadPath)) {
                        $errors[] = "Failed to upload NIC photo.";
                    }
                }

                if (empty($errors)) {
                    // 🔹 Save artist to DB
                    if ($model->register($full_name, $email, $password, $phone, $dbPath)) {
                        echo "<script>
                                alert('Artist registered successfully!');
                                window.location = '" . ROOT . "/login';
                              </script>";
                        exit;
                    } else {
                        $errors[] = "Registration failed. Try again.";
                    }
                }
            }
        }

        // 🔹 Send errors to the view
        $this->view('service_provider_register', ['errors' => $errors]);
    }

    public function submit()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/ServiceProviderRegister');
            exit;
        }

        $model = new M_service_provider();

        // Collect fields
        $provider = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'professional_title' => trim($_POST['professional_title'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'website' => trim($_POST['website'] ?? ''),
            'years_experience' => $_POST['years_experience'] ?? '',
            'professional_summary' => trim($_POST['professional_summary'] ?? ''),
            'availability' => isset($_POST['availability']) ? (int)$_POST['availability'] : 1,
            'availability_notes' => trim($_POST['availability_notes'] ?? ''),
            'business_cert_photo' => null,
        ];

        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $existingCert = trim($_POST['existing_business_cert_photo'] ?? '');

        // Basic validations
        if ($provider['full_name'] === '') $errors[] = 'Full name is required.';
        if ($provider['professional_title'] === '') $errors[] = 'Professional title is required.';
        if ($provider['email'] === '' || !filter_var($provider['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if ($provider['phone'] === '') $errors[] = 'Phone number is required.';
        if ($provider['location'] === '') $errors[] = 'Location is required.';
        if ($provider['years_experience'] === '') $errors[] = 'Years of experience is required.';
        if ($provider['professional_summary'] === '') $errors[] = 'Professional summary is required.';

        // Password validations
        if (empty($password) || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';

        // Handle business certificate photo upload or reuse existing before any early return
        if (!empty($existingCert) && empty($_FILES['business_cert_photo']['name'])) {
            $provider['business_cert_photo'] = $existingCert;
        } elseif (!empty($_FILES['business_cert_photo']['name'])) {
            $targetDir = __DIR__ . '/../../public/uploads/business_certificates/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = uniqid() . '_' . basename($_FILES['business_cert_photo']['name']);
            $targetFile = $targetDir . $fileName;

            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($_FILES['business_cert_photo']['size'] > $maxSize) {
                $errors[] = 'Business certificate photo must be less than 5MB.';
            } elseif (!in_array($_FILES['business_cert_photo']['type'], ['image/jpeg', 'image/png', 'image/jpg'])) {
                $errors[] = 'Business certificate photo must be JPG or PNG.';
            } elseif (is_uploaded_file($_FILES['business_cert_photo']['tmp_name'])) {
                if (move_uploaded_file($_FILES['business_cert_photo']['tmp_name'], $targetFile)) {
                    $provider['business_cert_photo'] = 'uploads/business_certificates/' . $fileName;
                } else {
                    $errors[] = 'Failed to upload business certificate photo.';
                }
            } else {
                $errors[] = 'Invalid file upload. Please try again.';
            }
        } else {
            $errors[] = 'Business certificate photo is required.';
        }

        // Duplicate checks
        if (empty($errors)) {
            // Check email in both serviceprovider and users tables
            if ($model->emailExists($provider['email']) || $model->emailExistsInUsers($provider['email'])) {
                $errors[] = 'This email is already registered. Please use a different email.';
            }
            if ($model->nameExists($provider['full_name'])) {
                $errors[] = 'A service provider with this full name is already registered.';
            }
        }

        // If there are errors, return form with data preserved
        if (!empty($errors)) {
            $this->view('service_provider_register', [
                'errors' => $errors,
                'formData' => $provider,
                'password' => $password,
                'confirm_password' => $confirm_password,
                'services' => $_POST['services'] ?? [],
                'projects' => $_POST['projects'] ?? [],
                'uploadedPhoto' => $provider['business_cert_photo'] ?? $existingCert,
            ]);
            return;
        }

        // First, register user in users table
        $userRegistered = $model->registerUser($provider['full_name'], $provider['email'], $password, $provider['phone'], 'service_provider');
        
        if (!$userRegistered) {
            $this->view('service_provider_register', ['errors' => ['Email already exists or registration failed. Please try again.']]);
            return;
        }

        // Get the newly created user ID
        $user_id = $model->getUserIdByEmail($provider['email']);
        if (!$user_id) {
            $this->view('service_provider_register', ['errors' => ['Failed to retrieve user ID. Please try again.']]);
            return;
        }

        // Then save serviceprovider profile with user_id
        $services = $_POST['services'] ?? [];
        $projects = $_POST['projects'] ?? [];

        $savedId = $model->saveFullProfile($provider, $user_id, $services, $projects);

        if ($savedId) {
            header('Location: ' . ROOT . '/Login?registered=1');
            exit;
        } else {
            $this->view('service_provider_register', ['errors' => ['Failed to save your profile. Please try again.']]);
        }
    }
}
?>
