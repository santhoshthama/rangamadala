-- =============================================
-- UPDATE LOGIN CONTROLLER LOGIC
-- =============================================
-- Add this verification check to your Login controller

/*
In Login.php controller, update the login method to check verification status:

public function index() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        // Query to check user and verification status
        $query = "SELECT id, full_name, email, password, role, is_verified, rejection_reason
                  FROM users 
                  WHERE email = ?";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                
                // Check if user requires verification (artist or service_provider)
                if (in_array($user['role'], ['artist', 'service_provider'])) {
                    if ($user['is_verified'] == 0) {
                        if ($user['rejection_reason']) {
                            // Account was rejected
                            $_SESSION['error'] = "Your registration was rejected. Reason: " . $user['rejection_reason'];
                        } else {
                            // Account pending approval
                            $_SESSION['error'] = "Your account is pending admin approval. Please wait for verification.";
                        }
                        header("Location: " . ROOT . "/login");
                        exit;
                    }
                }
                
                // Login successful - set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: " . ROOT . "/admindashboard");
                        break;
                    case 'artist':
                        header("Location: " . ROOT . "/artistdashboard");
                        break;
                    case 'service_provider':
                        header("Location: " . ROOT . "/serviceproviderdashboard");
                        break;
                    case 'audience':
                    default:
                        header("Location: " . ROOT . "/audiencedashboard");
                        break;
                }
                exit;
            } else {
                $_SESSION['error'] = "Invalid email or password";
            }
        } else {
            $_SESSION['error'] = "Invalid email or password";
        }
        
        header("Location: " . ROOT . "/login");
        exit;
    }
    
    $this->view('login');
}
*/

-- =============================================
-- AUTO-APPROVE AUDIENCE MEMBERS ON REGISTRATION
-- =============================================
-- Add this to your registration controller for audience members

/*
In your registration controller (AudienceRegister.php or similar):

if ($role === 'audience') {
    // Auto-approve audience members
    $is_verified = 1;
} else {
    // Artists and service providers need manual approval
    $is_verified = 0;
}

$query = "INSERT INTO users (full_name, email, password, phone, role, is_verified) 
          VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $db->prepare($query);
$stmt->bind_param("sssssi", $full_name, $email, $hashed_password, $phone, $role, $is_verified);
*/

-- =============================================
-- DISPLAY VERIFICATION STATUS IN LOGIN VIEW
-- =============================================
-- Add this to your login.view.php

/*
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success'] ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>
*/
