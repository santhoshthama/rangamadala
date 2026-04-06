<?php
class M_user
{
    use Model;

    /**
     * Get all pending users for verification (only artists and service providers)
     * Returns users where verification_status is 'pending' or NULL
     */
    public function getPendingUsers()
    {
        $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.role, u.nic_photo, u.created_at,
                       sp.nic_photo_front, sp.nic_photo_back, sp.professional_title, sp.nic_number
                FROM users u
                LEFT JOIN serviceprovider sp ON u.id = sp.user_id
                WHERE (u.verification_status = 'pending' OR u.verification_status IS NULL)
                AND u.role IN ('artist', 'service_provider')
                ORDER BY u.created_at ASC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get user by ID with all verification details
     * Includes service provider details if applicable
     */
    public function getUserById($id)
    {
        $sql = "SELECT u.*, 
                       sp.professional_title, sp.nic_number, sp.location,
                       sp.nic_photo_front, sp.nic_photo_back, sp.years_experience,
                       sp.professional_summary
                FROM users u
                LEFT JOIN serviceprovider sp ON u.id = sp.user_id
                WHERE u.id = ?";
        
        return $this->db->query($sql, [$id])->fetch();
    }

    /**
     * Approve a user - sets is_verified=1 and verification_status='approved'
     * User can now log in to the system
     * 
     * @param int $user_id The user ID to approve
     * @param int $admin_id The admin ID performing the approval
     * @return bool Success or failure
     */
    public function approveUser($user_id, $admin_id)
    {
        $sql = "UPDATE users 
                SET is_verified = 1, 
                    verification_status = 'approved', 
                    verified_at = NOW(), 
                    verified_by_admin_id = ?,
                    rejection_reason = NULL
                WHERE id = ?
                AND role IN ('artist', 'service_provider')";
        
        return $this->db->query($sql, [$admin_id, $user_id]);
    }

    /**
     * Reject a user with reason - sets verification_status='rejected'
     * User cannot log in and will see the rejection reason
     * 
     * @param int $user_id The user ID to reject
     * @param string $reason The rejection reason
     * @param int $admin_id The admin ID performing the rejection
     * @return bool Success or failure
     */
    public function rejectUser($user_id, $reason, $admin_id)
    {
        $sql = "UPDATE users 
                SET is_verified = 0,
                    verification_status = 'rejected', 
                    rejection_reason = ?, 
                    verified_at = NOW(), 
                    verified_by_admin_id = ?
                WHERE id = ?
                AND role IN ('artist', 'service_provider')";
        
        return $this->db->query($sql, [$reason, $admin_id, $user_id]);
    }

    /**
     * Get all verified/approved users (only artists and service providers)
     */
    public function getVerifiedUsers()
    {
        $sql = "SELECT id, full_name, email, phone, role, verified_at, created_at FROM users 
                WHERE verification_status = 'approved' AND is_verified = 1
                AND role IN ('artist', 'service_provider')
                ORDER BY verified_at DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get all rejected users (only artists and service providers)
     */
    public function getRejectedUsers()
    {
        $sql = "SELECT id, full_name, email, phone, role, rejection_reason, verified_at, created_at FROM users 
                WHERE verification_status = 'rejected'
                AND role IN ('artist', 'service_provider')
                ORDER BY verified_at DESC";
        
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Get verification statistics for admin dashboard
     */
    public function getVerificationStats()
    {
        $stats = [];
        
        $pending = $this->db->query("SELECT COUNT(*) as count FROM users 
                                    WHERE (verification_status = 'pending' OR verification_status IS NULL)
                                    AND role IN ('artist', 'service_provider')")->fetch();
        $stats['pending'] = $pending->count ?? 0;
        
        $approved = $this->db->query("SELECT COUNT(*) as count FROM users 
                                     WHERE verification_status = 'approved'
                                     AND role IN ('artist', 'service_provider')")->fetch();
        $stats['approved'] = $approved->count ?? 0;
        
        $rejected = $this->db->query("SELECT COUNT(*) as count FROM users 
                                     WHERE verification_status = 'rejected'
                                     AND role IN ('artist', 'service_provider')")->fetch();
        $stats['rejected'] = $rejected->count ?? 0;
        
        $stats['total'] = $stats['pending'] + $stats['approved'] + $stats['rejected'];
        
        return $stats;
    }

    /**
     * Re-approve a previously rejected user
     * Allows admin to reconsider and approve after rejection
     */
    public function reapproveUser($user_id, $admin_id)
    {
        return $this->approveUser($user_id, $admin_id);
    }

    /**
     * Get admin who verified a specific user
     */
    public function getVerifiedByAdmin($user_id)
    {
        $sql = "SELECT a.full_name as admin_name, a.email as admin_email, u.verified_at
                FROM users u
                LEFT JOIN users a ON u.verified_by = a.id
                WHERE u.id = ?";
        
        return $this->db->query($sql, [$user_id])->fetch();
    }
}
?>
