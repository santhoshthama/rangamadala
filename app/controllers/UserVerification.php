<?php
class UserVerification
{
    use Controller;
    protected $model = null;

    public function __construct()
    {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: " . ROOT . "/Login");
            exit;
        }
        
        $this->model = $this->getModel("M_user");
    }

    // View pending users for verification
    public function pending()
    {
        $data = [
            'pending_users' => $this->model->getPendingUsers(),
            'page_title' => 'Pending User Verification'
        ];
        
        $this->view("user_verification", $data);
    }

    // Approve a user
    public function approve()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id'] ?? 0);
            
            if ($user_id > 0) {
                $result = $this->model->approveUser($user_id, $_SESSION['user_id']);
                
                if ($result) {
                    $_SESSION['success_message'] = "User approved successfully!";
                    header("Location: " . ROOT . "/UserVerification/pending");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Failed to approve user.";
                }
            }
        }
        
        header("Location: " . ROOT . "/UserVerification/pending");
        exit;
    }

    // Reject a user
    public function reject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = intval($_POST['user_id'] ?? 0);
            $reason = trim($_POST['rejection_reason'] ?? '');
            
            if ($user_id > 0 && !empty($reason)) {
                $result = $this->model->rejectUser($user_id, $reason, $_SESSION['user_id']);
                
                if ($result) {
                    $_SESSION['success_message'] = "User rejected with reason provided.";
                    header("Location: " . ROOT . "/UserVerification/pending");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Failed to reject user.";
                }
            } else {
                $_SESSION['error_message'] = "Please provide a rejection reason.";
            }
        }
        
        header("Location: " . ROOT . "/UserVerification/pending");
        exit;
    }

    // View user details with NIC image
    public function viewUser()
    {
        $user_id = intval($_GET['id'] ?? 0);
        
        if ($user_id > 0) {
            $data = [
                'user' => $this->model->getUserById($user_id),
                'page_title' => 'User Verification Details'
            ];
            
            $this->view("user_verification_detail", $data);
        } else {
            header("Location: " . ROOT . "/UserVerification/pending");
            exit;
        }
    }

    // View all verified users
    public function verified()
    {
        $data = [
            'verified_users' => $this->model->getVerifiedUsers(),
            'page_title' => 'Verified Users'
        ];
        
        $this->view("verified_users", $data);
    }

    // View all rejected users
    public function rejected()
    {
        $data = [
            'rejected_users' => $this->model->getRejectedUsers(),
            'page_title' => 'Rejected Users'
        ];
        
        $this->view("rejected_users", $data);
    }
}
?>
