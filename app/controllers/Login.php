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
            'error' => '',
            'success' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            // Validate input
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $data['error'] = "Please enter both email and password.";
                $data['email'] = $email;
                $this->view("login", $data);
                return;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = "Please enter a valid email address.";
                $data['email'] = $email;
                $this->view("login", $data);
                return;
            }

            // pass the data to the model for authentication
            $user = $this->model->authenticate($email, $password);

            if ($user) {
                // Check verification status for artists and service providers
                if (in_array($user->role, ['artist', 'service_provider'])) {
                    // Check if user is verified
                    if (isset($user->is_verified) && $user->is_verified == 0) {
                        // Check if registration was rejected
                        if (!empty($user->rejection_reason)) {
                            $data['error'] = "Your registration was rejected. Reason: " . $user->rejection_reason . " Please contact admin support.";
                        } else {
                            // Account is pending approval
                            $data['error'] = "Your account is pending admin approval. Please wait for verification. You will receive an email once approved.";
                        }
                        $data['email'] = $email;
                        $this->view("login", $data);
                        return;
                    }
                }

                // set session and redirect to dashboard or home page
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->full_name;
                $_SESSION['full_name'] = $user->full_name;
                $_SESSION['email'] = $user->email;
                $_SESSION['phone'] = $user->phone;
                $_SESSION['role'] = $user->role;

                // Set success message
                $_SESSION['success_message'] = "Welcome back, " . $user->full_name . "! Login successful.";

                // Redirect based on user role
                if ($user->role === 'admin') {
                    header("Location: " . ROOT . "/Admindashboard");
                    exit;
                } elseif ($user->role === 'artist') {
                    header("Location: " . ROOT . "/artistdashboard");
                    exit;
                } elseif ($user->role === 'service_provider') {
                    header("Location: " . ROOT . "/ServiceProviderDashboard");
                    exit;
                } elseif ($user->role === 'audience') {
                    header("Location: " . ROOT . "/Audiencedashboard");
                    exit;
                }

                // fallback - redirect to home if role is unknown
                header("Location: " . ROOT . "/Home");
                exit();
            } else {
                $data['error'] = "Invalid email or password. Please check your credentials and try again.";
                $data['email'] = $email;
            }
        }

        $this->view("login", $data);
    }
}

?>