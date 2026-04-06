<?php

class M_signup {

    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Register a new user with verification status based on role
     * 
     * @param string $full_name User's full name
     * @param string $email User's email address
     * @param string $password Plain text password (will be hashed)
     * @param string $phone User's phone number
     * @param string $role User role: 'audience', 'artist', 'service_provider', 'admin'
     * @param string|null $nic_photo NIC photo path (for artists - single photo)
     * @param string|null $nic_photo_back NIC back photo path (for service providers)
     * @return bool True on success, false on failure
     * 
     * VERIFICATION RULES:
     * - 'audience' and 'admin' roles: Auto-approved (is_verified=1, verification_status='approved')
     * - 'artist' and 'service_provider' roles: Require admin approval (is_verified=0, verification_status='pending')
     * - Pending users CANNOT log in until approved by admin
     * - Rejected users CANNOT log in and will see rejection reason
     */
    public function registerUser($full_name, $email, $password, $phone, $role, $nic_photo = null, $nic_photo_back = null) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Determine initial verification state based on role
        // Artists and Service Providers require admin approval
        // Audience and Admin users are auto-approved
        $is_verified = 1;
        $verification_status = 'approved';

        if (in_array($role, ['artist', 'service_provider'], true)) {
            // Artists and service providers require admin approval
            $is_verified = 0;
            $verification_status = 'pending';
        }

        $this->db->query("INSERT INTO users 
            (full_name, email, password, phone, nic_photo, role, is_verified, verification_status, created_at) 
            VALUES 
            (:full_name, :email, :password, :phone, :nic_photo, :role, :is_verified, :verification_status, NOW())");

        $this->db->bind(':full_name', $full_name);
        $this->db->bind(':email', $email);
        $this->db->bind(':password', $hashedPassword);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':nic_photo', $nic_photo);
        $this->db->bind(':role', $role);
        $this->db->bind(':is_verified', $is_verified);
        $this->db->bind(':verification_status', $verification_status);

        return $this->db->execute();
    }

    /**
     * Check if an email already exists in the users table
     * 
     * @param string $email Email to check
     * @return bool True if email exists, false otherwise
     */
    public function emailExists($email) {
        $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row && isset($row->cnt) ? ((int)$row->cnt > 0) : false;
    }

    /**
     * Get user's verification status by email
     * 
     * @param string $email User's email
     * @return object|null User verification info or null if not found
     */
    public function getVerificationStatus($email) {
        $this->db->query("SELECT id, is_verified, verification_status, rejection_reason 
                          FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        return $this->db->single();
    }
}
?>
