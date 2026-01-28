<?php
class Admindashboard {
    use Controller;

    public function index(){
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has admin role
        if ($_SESSION['role'] !== 'admin') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $this->view('admindashboard');
    }

    /**
     * Get pending registrations (artists and service providers)
     */
    public function getPendingRegistrations()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $db = new Database();
        
        $query = "SELECT 
                    u.id,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.role,
                    u.nic_photo,
                    u.created_at
                FROM users u
                WHERE u.is_verified = 0 
                AND u.role IN ('artist', 'service_provider')
                ORDER BY u.created_at ASC";
        
        $result = $db->query($query);
        $registrations = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $registrations[] = $row;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($registrations);
        exit;
    }

    /**
     * Approve user registration
     */
    public function approveUser()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;

        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $db = new Database();
        $adminId = $_SESSION['user_id'];
        
        $query = "UPDATE users 
                SET 
                    is_verified = 1,
                    verified_by = ?,
                    verified_at = CURRENT_TIMESTAMP,
                    rejection_reason = NULL
                WHERE id = ? 
                AND role IN ('artist', 'service_provider')";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("ii", $adminId, $userId);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'User approved successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to approve user']);
        }
        exit;
    }

    /**
     * Reject user registration
     */
    public function rejectUser()
    {
        // Check admin access
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;
        $reason = $input['reason'] ?? 'No reason provided';

        if (!$userId) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            exit;
        }

        $db = new Database();
        $adminId = $_SESSION['user_id'];
        
        $query = "UPDATE users 
                SET 
                    is_verified = 0,
                    rejection_reason = ?,
                    verified_by = ?,
                    verified_at = CURRENT_TIMESTAMP
                WHERE id = ? 
                AND role IN ('artist', 'service_provider')";
        
        $stmt = $db->prepare($query);
        $stmt->bind_param("sii", $reason, $adminId, $userId);
        
        if ($stmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'User rejected successfully']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Failed to reject user']);
        }
        exit;
    }
}
?>