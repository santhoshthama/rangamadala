<?php

class AudienceRegister
{
    use Controller;

    public function index()
    {
        $errors = [];
        $old = [
            'full_name' => '',
            'email' => '',
            'phone' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');

            $old = [
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
            ];

            $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/';
            $phonePattern = '/^(?:\+94|94|0)7\d{8}$/';

            if ($full_name === '') {
                $errors[] = 'Full name is required.';
            }
            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'A valid email is required.';
            }
            if (!preg_match($passwordPattern, $password)) {
                $errors[] = 'Password must be at least 6 characters and include uppercase, lowercase, number, and symbol.';
            }
            if ($password !== $confirm_password) {
                $errors[] = 'Password confirmation does not match.';
            }
            if ($phone === '') {
                $errors[] = 'Phone number is required.';
            } elseif (!preg_match($phonePattern, $phone)) {
                $errors[] = 'Enter a valid Sri Lankan mobile number (e.g. 07X XXX XXXX or +94 XXX XXX XXX).';
            }

            $model = new M_audience();

            if (empty($errors) && $model->register($full_name, $email, $password, $confirm_password, $phone)) {
                $_SESSION['success_message'] = "Audience registered successfully! Please login.";
                header("Location: " . ROOT . "/Login");
                exit;
            } else {
                if (empty($errors)) {
                    $errors[] = 'Registration failed. Email may already exist.';
                }
            }

        }

        $this->view('audience/register', [
            'errors' => $errors,
            'old' => $old,
        ]);
    }
}

?>