<?php

class M_universal_profile extends M_signup {
    
    public function getUserById($user_id) {
        try {
            $this->db->query("SELECT * FROM users WHERE id = :user_id");
            $this->db->bind(':user_id', $user_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getUserById: " . $e->getMessage());
            return null;
        }
    }

    public function updateProfile($user_id, array $fields) {
        if (empty($fields)) {
            return false;
        }

        $allowed = ['full_name', 'phone', 'profile_image', 'years_experience', 'bio', 'location', 'website'];
        $setParts = [];
        $bindValues = [];

        foreach ($fields as $column => $value) {
            if (!in_array($column, $allowed, true)) {
                continue;
            }
            $setParts[] = "$column = :$column";
            $bindValues[":$column"] = $value;
        }

        if (empty($setParts)) {
            return false;
        }

        try {
            $sql = 'UPDATE users SET ' . implode(', ', $setParts) . " WHERE id = :user_id";
            
            $this->db->query($sql);

            foreach ($bindValues as $param => $value) {
                $this->db->bind($param, $value);
            }
            $this->db->bind(':user_id', $user_id);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in updateProfile: ' . $e->getMessage());
            return false;
        }
    }
}
