<?php
class M_user
{
    use Model;

    /**
     * Get all pending users for verification
     */
    public function getPendingUsers()
    {
        $sql = "SELECT id, full_name, email, phone, role, nic_photo, created_at FROM users 
                WHERE verification_status = 'pending' OR verification_status IS NULL
                ORDER BY created_at DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get user by ID with all verification details
     */
    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        
        return $this->db->query($sql, [$id])->fetch();
    }

    /**
     * Approve a user
     */
    public function approveUser($user_id, $admin_id)
    {
        $sql = "UPDATE users 
                SET is_verified = 1, 
                    verification_status = 'approved', 
                    verified_at = NOW(), 
                    verified_by_admin_id = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [$admin_id, $user_id]);
    }

    /**
     * Reject a user with reason
     */
    public function rejectUser($user_id, $reason, $admin_id)
    {
        $sql = "UPDATE users 
                SET verification_status = 'rejected', 
                    rejection_reason = ?, 
                    verified_at = NOW(), 
                    verified_by_admin_id = ? 
                WHERE id = ?";
        
        return $this->db->query($sql, [$reason, $admin_id, $user_id]);
    }

    /**
     * Get all verified users
     */
    public function getVerifiedUsers()
    {
        $sql = "SELECT id, full_name, email, phone, role, verified_at FROM users 
                WHERE verification_status = 'approved' AND is_verified = 1
                ORDER BY verified_at DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get all rejected users
     */
    public function getRejectedUsers()
    {
        $sql = "SELECT id, full_name, email, phone, role, rejection_reason, verified_at FROM users 
                WHERE verification_status = 'rejected'
                ORDER BY verified_at DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get verification statistics
     */
    public function getVerificationStats()
    {
        $stats = [];
        
        $pending = $this->db->query("SELECT COUNT(*) as count FROM users 
                                    WHERE verification_status = 'pending' OR verification_status IS NULL")->fetch();
        $stats['pending'] = $pending->count ?? 0;
        
        $approved = $this->db->query("SELECT COUNT(*) as count FROM users 
                                     WHERE verification_status = 'approved'")->fetch();
        $stats['approved'] = $approved->count ?? 0;
        
        $rejected = $this->db->query("SELECT COUNT(*) as count FROM users 
                                     WHERE verification_status = 'rejected'")->fetch();
        $stats['rejected'] = $rejected->count ?? 0;
        
        return $stats;
    }
}
?>
