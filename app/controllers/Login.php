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
            'success' => '',
            'approval_notice' => '',
            'already_logged_in' => false,
            'current_user_name' => '',
            'cancel_url' => ROOT . '/Home'
        ];

        // If user is already logged in and opens login page, show confirmation prompt
        if ($_SERVER['REQUEST_METHOD'] !== "POST" && isset($_SESSION['user_id'])) {
            $role = $_SESSION['user_role'] ?? $_SESSION['role'] ?? '';
            $cancelUrl = ROOT . '/Home';

            if ($role === 'admin') {
                $cancelUrl = ROOT . '/Admindashboard';
            } elseif ($role === 'artist') {
                $cancelUrl = ROOT . '/artistdashboard';
            } elseif ($role === 'service_provider') {
                $cancelUrl = ROOT . '/ServiceProviderDashboard';
            } elseif ($role === 'audience') {
                $cancelUrl = ROOT . '/Audiencedashboard';
            }

            $data['already_logged_in'] = true;
            $data['current_user_name'] = $_SESSION['full_name'] ?? $_SESSION['user_name'] ?? 'User';
            $data['cancel_url'] = $cancelUrl;

            $this->view("login", $data);
            return;
        }

        if (isset($_GET['logged_out']) && $_GET['logged_out'] === '1') {
            $data['success'] = 'You have been signed out successfully.';
        }

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

            // First check if email exists
            $emailExists = $this->model->checkEmailExists($email);
            
            if (!$emailExists) {
                $data['error'] = "No account found with this email address. Please check your email or sign up for a new account.";
                $data['email'] = $email;
                $this->view("login", $data);
                return;
            }

            // Pre-auth verification status check so users can see status messages immediately
            $verification = $this->model->getVerificationSnapshotByEmail($email);
            if ($verification && in_array($verification->role, ['artist', 'service_provider'])) {
                $verificationStatus = $verification->verification_status ?? 'pending';
                $isVerified = isset($verification->is_verified) ? (int)$verification->is_verified : 0;

                if ($verificationStatus === 'rejected' || $isVerified === 0) {
                    if ($verificationStatus === 'rejected') {
                        $rejectionReason = $verification->rejection_reason ?? 'No reason provided';
                        $data['error'] = "Your registration was rejected.<br><br><strong>Reason:</strong> " . htmlspecialchars($rejectionReason) . "<br><br>Please contact admin support for more information.";
                    } else {
                        $data['error'] = "Your account is pending admin approval.<br><br>Our team is reviewing your submitted documents. This usually takes 1-2 business days.<br><br>You will be able to login once your account is verified.";
                    }

                    $data['email'] = $email;
                    $this->view("login", $data);
                    return;
                }

                if ($verificationStatus === 'approved' && $isVerified === 1) {
                    $data['approval_notice'] = 'Your account has been approved. You can now log in.';
                }
            }

            // pass the data to the model for authentication
            $user = $this->model->authenticate($email, $password);

            if ($user) {
                // Check verification status for artists and service providers
                if (in_array($user->role, ['artist', 'service_provider'])) {
                    // Check if user is verified
                    if (isset($user->is_verified) && $user->is_verified == 0) {
                        // Check verification status
                        $verificationStatus = $user->verification_status ?? 'pending';
                        
                        if ($verificationStatus === 'rejected') {
                            $rejectionReason = $user->rejection_reason ?? 'No reason provided';
                            $data['error'] = "Your registration was rejected.<br><br><strong>Reason:</strong> " . htmlspecialchars($rejectionReason) . "<br><br>Please contact admin support for more information.";
                        } else {
                            // Account is pending approval
                            $data['error'] = "Your account is pending admin approval.<br><br>Our team is reviewing your submitted documents. This usually takes 1-2 business days.<br><br>You will be able to login once your account is verified.";
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
                $_SESSION['user_role'] = $user->role;

                // Set success message
                if (in_array($user->role, ['artist', 'service_provider'])
                    && ($user->verification_status ?? '') === 'approved'
                    && (int)($user->is_verified ?? 0) === 1) {
                    $_SESSION['success_message'] = "Welcome back, " . $user->full_name . "! Your account is approved and login was successful.";
                } else {
                    $_SESSION['success_message'] = "Welcome back, " . $user->full_name . "! Login successful.";
                }

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
                $data['error'] = "Incorrect password. Please try again or use 'Forgot password' to reset it.";
                $data['email'] = $email;
            }
        }

        $this->view("login", $data);
    }
}

?>