<?php

class AudienceRegister
{
    use Controller;

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $phone = $_POST['phone'] ?? '';

            $model = new M_audience();

            if ($model->register($full_name, $email, $password, $confirm_password, $phone)) {
                $_SESSION['success_message'] = "Audience registered successfully! Please login.";
                header("Location: " . ROOT . "/Login");
                exit;
            } else {
                echo "<script>alert('Registration failed!');</script>";
            }

        }

        $this->view('audience_register');
    }
}

?>