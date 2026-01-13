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
            
            
            
            // error_log(print_r($user, true)); // Debugging line
            
            
            
            
            if ($user) {
                // set session and redirect to dashboard or home page
                $_SESSION['user_id'] = $user->id;
                $_SESSION['user_name'] = $user->full_name;
                $_SESSION['full_name'] = $user->full_name;
                $_SESSION['email'] = $user->email;
                $_SESSION['phone'] = $user->phone;
                $_SESSION['role'] = $user->role;

                // Redirect based on user role
                if ($user->role === 'admin') {
                    header("Location: " . ROOT . "/Admindashboard");
                    exit;
                } elseif ($user->role === 'artist') {
                    header("Location: " . ROOT . "/ArtistDashboard");
                    // Temporary redirect for testing
                    // header("Location: " . ROOT . "/BrowseServiceProviders");
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
                $data['error'] = "Invalid email or password.";
                $data['email'] = $email;
            }
        }

        // error_log("RENDERING LOGIN VIEW"); // Debugging line
        $this->view("login", $data);
    }
}

?>