<?php 

class M_login {
    private $db = null;
    
    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Authenticate user by email and password
     * Returns user object with verification fields if credentials are valid
     * 
     * VERIFICATION FIELDS RETURNED:
     * - is_verified: 0 = not verified, 1 = verified
     * - verification_status: 'pending', 'approved', 'rejected'
     * - rejection_reason: Reason if rejected (null otherwise)
     * 
     * @param string $email User's email
     * @param string $password Plain text password
     * @return object|false User object on success, false on failure
     */
    public function authenticate($email, $password) {
        // Select all user fields including verification status for login checks
        $this->db->query("SELECT id, full_name, email, password, phone, role, 
                                 is_verified, verification_status, rejection_reason,
                                 profile_image, created_at
                          FROM users 
                          WHERE email = :email");
        $this->db->bind(":email", $email);
        $user = $this->db->single();
        
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    /**
     * Get minimal verification status by email (used before password auth)
     *
     * @param string $email
     * @return object|false
     */
    public function getVerificationSnapshotByEmail($email) {
        $this->db->query("SELECT id, role, is_verified, verification_status, rejection_reason
                          FROM users
                          WHERE email = :email
                          LIMIT 1");
        $this->db->bind(":email", $email);
        return $this->db->single();
    }

    /**
     * Check if email exists in database
     * 
     * @param string $email User's email
     * @return bool True if email exists, false otherwise
     */
    public function checkEmailExists($email) {
        $this->db->query("SELECT id FROM users WHERE email = :email");
        $this->db->bind(":email", $email);
        $user = $this->db->single();
        return $user ? true : false;
    }

    /**
     * Get user by ID (for session refresh)
     * 
     * @param int $userId User's ID
     * @return object|false User object or false if not found
     */
    public function getUserById($userId) {
        $this->db->query("SELECT id, full_name, email, phone, role, 
                                 is_verified, verification_status, profile_image
                          FROM users 
                          WHERE id = :id");
        $this->db->bind(":id", $userId);
        return $this->db->single();
    }

    /**
     * Check if user is verified (can log in)
     * 
     * @param int $userId User's ID
     * @return bool True if user is verified and approved
     */
    public function isUserVerified($userId) {
        $this->db->query("SELECT is_verified, verification_status, role 
                          FROM users WHERE id = :id");
        $this->db->bind(":id", $userId);
        $user = $this->db->single();
        
        if (!$user) {
            return false;
        }
        
        // Audience and admin users don't need verification
        if (in_array($user->role, ['audience', 'admin'])) {
            return true;
        }
        
        // Artists and service providers need is_verified = 1 AND verification_status = 'approved'
        return $user->is_verified == 1 && $user->verification_status === 'approved';
    }
}

?>