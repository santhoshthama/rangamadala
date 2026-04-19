<?php
class ForgotPassword
{
    use Controller;

    protected $model = null;

    public function __construct()
    {
        $this->model = $this->getModel('M_login');
    }

    public function index()
    {
        $data = [
            'email' => '',
            'message' => '',
            'error' => '',
            'reset_link' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = 'Please enter a valid email address.';
                $data['email'] = $email;
                $this->view('forgot_password', $data);
                return;
            }

            $result = $this->model ? $this->model->createPasswordResetToken($email) : ['success' => false, 'message' => 'Password reset is unavailable.'];

            if (!empty($result['success'])) {
                $token = $result['token'] ?? '';
                $resetLink = $token !== '' ? ROOT . '/ForgotPassword/reset?token=' . urlencode($token) : '';

                if ($resetLink !== '') {
                    $data['reset_link'] = $resetLink;
                }

                $data['message'] = 'If an account exists for that email, a password reset link has been generated below.';
                $data['email'] = $email;
            } else {
                $data['error'] = $result['message'] ?? 'Unable to process your request right now.';
                $data['email'] = $email;
            }
        }

        $this->view('forgot_password', $data);
    }

    public function reset()
    {
        $token = trim($_GET['token'] ?? ($_POST['token'] ?? ''));
        $data = [
            'token' => $token,
            'error' => '',
            'success' => '',
            'email' => '',
            'full_name' => '',
        ];

        if ($token === '') {
            $data['error'] = 'Reset link is missing or invalid.';
            $data['token'] = '';
            $this->view('reset_password', $data);
            return;
        }

        $resetUser = $this->model ? $this->model->getPasswordResetUserByToken($token) : false;
        if (!$resetUser) {
            $data['error'] = 'Reset link is invalid or has expired.';
            $data['token'] = '';
            $this->view('reset_password', $data);
            return;
        }

        $data['email'] = $resetUser->email ?? '';
        $data['full_name'] = $resetUser->full_name ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = (string)($_POST['password'] ?? '');
            $confirmPassword = (string)($_POST['confirm_password'] ?? '');

            if (strlen($password) < 8) {
                $data['error'] = 'Password must be at least 8 characters long.';
                $this->view('reset_password', $data);
                return;
            }

            if ($password !== $confirmPassword) {
                $data['error'] = 'Passwords do not match.';
                $this->view('reset_password', $data);
                return;
            }

            $result = $this->model ? $this->model->resetPasswordWithToken($token, $password) : ['success' => false, 'message' => 'Password reset is unavailable.'];

            if (!empty($result['success'])) {
                $_SESSION['success_message'] = 'Your password has been updated. You can log in now.';
                header('Location: ' . ROOT . '/Login');
                exit;
            }

            $data['error'] = $result['message'] ?? 'Failed to reset password.';
        }

        $this->view('reset_password', $data);
    }
}
