<?php 

class M_login {
    private $db = null;
    
    public function __construct()
    {
        $this->db = new Database();
    }

    private function ensurePasswordResetTableExists(): bool
    {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS password_reset_tokens (
                id INT NOT NULL AUTO_INCREMENT,
                user_id INT NOT NULL,
                token_hash CHAR(64) NOT NULL,
                expires_at DATETIME NOT NULL,
                used_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY unique_user_token_reset (user_id),
                UNIQUE KEY unique_token_hash (token_hash),
                KEY idx_password_reset_expires_at (expires_at),
                CONSTRAINT fk_password_reset_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
            return $this->db->execute();
        } catch (Throwable $e) {
            error_log('M_login::ensurePasswordResetTableExists error: ' . $e->getMessage());
            return false;
        }
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

    public function createPasswordResetToken($email)
    {
        if (!$this->ensurePasswordResetTableExists()) {
            return ['success' => false, 'message' => 'Password reset service is unavailable.'];
        }

        $this->db->query("SELECT id, full_name, email FROM users WHERE email = :email LIMIT 1");
        $this->db->bind(':email', $email);
        $user = $this->db->single();

        if (!$user) {
            return ['success' => true, 'message' => 'If the email exists, a reset link will be available.'];
        }

        $plainToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = date('Y-m-d H:i:s', time() + (60 * 30));

        $this->db->query("INSERT INTO password_reset_tokens (user_id, token_hash, expires_at, used_at, created_at)
                          VALUES (:user_id, :token_hash, :expires_at, NULL, NOW())
                          ON DUPLICATE KEY UPDATE token_hash = VALUES(token_hash), expires_at = VALUES(expires_at), used_at = NULL, created_at = NOW()");
        $this->db->bind(':user_id', (int)$user->id);
        $this->db->bind(':token_hash', $tokenHash);
        $this->db->bind(':expires_at', $expiresAt);

        if (!$this->db->execute()) {
            return ['success' => false, 'message' => 'Failed to create reset token.'];
        }

        return [
            'success' => true,
            'message' => 'If the email exists, a reset link will be available.',
            'token' => $plainToken,
            'user' => $user,
        ];
    }

    public function getPasswordResetUserByToken($token)
    {
        if (!$this->ensurePasswordResetTableExists()) {
            return false;
        }

        $tokenHash = hash('sha256', trim((string)$token));
        $this->db->query("SELECT u.id, u.full_name, u.email, t.expires_at, t.used_at
                          FROM password_reset_tokens t
                          INNER JOIN users u ON u.id = t.user_id
                          WHERE t.token_hash = :token_hash
                          LIMIT 1");
        $this->db->bind(':token_hash', $tokenHash);
        $record = $this->db->single();

        if (!$record) {
            return false;
        }

        if (!empty($record->used_at)) {
            return false;
        }

        if (!empty($record->expires_at) && strtotime($record->expires_at) < time()) {
            return false;
        }

        return $record;
    }

    public function resetPasswordWithToken($token, $newPassword)
    {
        $resetUser = $this->getPasswordResetUserByToken($token);
        if (!$resetUser) {
            return ['success' => false, 'message' => 'Reset link is invalid or has expired.'];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $tokenHash = hash('sha256', trim((string)$token));

        try {
            $this->db->query("UPDATE users SET password = :password, updated_at = NOW() WHERE id = :id");
            $this->db->bind(':password', $hashedPassword);
            $this->db->bind(':id', (int)$resetUser->id);
            $userUpdated = $this->db->execute();

            $this->db->query("UPDATE password_reset_tokens SET used_at = NOW() WHERE token_hash = :token_hash");
            $this->db->bind(':token_hash', $tokenHash);
            $tokenUpdated = $this->db->execute();

            if ($userUpdated && $tokenUpdated) {
                return ['success' => true, 'message' => 'Password updated successfully.'];
            }

            return ['success' => false, 'message' => 'Failed to update password.'];
        } catch (Throwable $e) {
            error_log('M_login::resetPasswordWithToken error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update password.'];
        }
    }
}

?>