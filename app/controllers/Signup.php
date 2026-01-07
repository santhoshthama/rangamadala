<?php

class Signup {
    use Controller;

    protected $model;

    public function __construct() {
        $this->model = new M_signup();
    }

    public function index() {

        $data = [
            'full_name' => '',
            'email' => '',
            'phone' => '',
            'error' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {

            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            $errors = [];

            // Validate
            if (empty($full_name)) $errors[] = "Full name is required.";
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";
            if (empty($password) || strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
            if ($password !== $confirm_password) $errors[] = "Passwords do not match.";
            if (empty($phone)) $errors[] = "Phone number required.";

            // Handle NIC upload
            $nic_photo = null;
            if (!empty($_FILES['nic_photo']['name'])) {
                $targetDir = "uploads/nic/";
                if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

                $fileName = uniqid() . "_" . basename($_FILES["nic_photo"]["name"]);
                $targetFile = $targetDir . $fileName;

                if (move_uploaded_file($_FILES["nic_photo"]["tmp_name"], $targetFile)) {
                    $nic_photo = $fileName;
                } else {
                    $errors[] = "NIC upload failed.";
                }
            }

            // Preserve data
            $data['full_name'] = $full_name;
            $data['email'] = $email;
            $data['phone'] = $phone;

            if (!empty($errors)) {
                $data['error'] = implode("<br>", $errors);
                $this->view("signup", $data);
                return;
            }

            // Save user
            $role = "audience"; // default role for this signup page

            $result = $this->model->registerUser($full_name, $email, $password, $phone, $role, $nic_photo);

            if ($result) {
                header("Location: " . ROOT . "/login");
                exit;
            } else {
                $data['error'] = "Signup failed. Email may already exist.";
            }
        }

        $this->view("signup", $data);
    }
}

?>
