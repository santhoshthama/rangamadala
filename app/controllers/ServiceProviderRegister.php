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
}
?>
