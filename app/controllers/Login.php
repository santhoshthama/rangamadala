<?php
class Login
{
    use Controller;
    protected $model = null;

    public function __construct()
    {
        $this->model = $this->getModel("M_login");
    }

    public function index()
    {
        $data = [
            'email' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // pass the data to the model for authentication
            $user = $this->model->authenticate($email, $password);
            if ($user) {
                // set session and redirect to dashboard or home page
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->full_name;

                if ($user->role === 'audience') {
                    header("Location: " . ROOT . "/Audiencedashboard");
                    exit;
                }

                if ($user->role === 'service_provider') {
                    header("Location: " . ROOT . "/Audiencedashboard");
                    exit;
                }

                if ($user->role === 'artist') {
                    header("Location: " . ROOT . "/Audiencedashboard");
                    exit;
                }

                // fallback (optional)
                header("Location: " . ROOT . "/dashboard");
                exit();
            } else {
                $data['error'] = "Invalid email or password.";
                $data['email'] = $email;
            }
        }

        $this->view("login", $data);
    }
}

?>