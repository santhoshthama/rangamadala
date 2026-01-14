<?php

class M_artist extends M_signup {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = new Database();
    }

    public function register($full_name, $email, $password, $phone, $nic_photo = null) {
        return $this->registerUser($full_name, $email, $password, $phone, 'artist', $nic_photo);
    }

    public function get_artist_by_id($user_id) {
        try {
            $this->db->query("SELECT * FROM users WHERE id = :user_id AND role = 'artist'");
            $this->db->bind(':user_id', $user_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in get_artist_by_id: " . $e->getMessage());
            return null;
        }
    }

    public function update_artist_profile($user_id, array $fields) {
        if (empty($fields)) {
            return false;
        }

        $allowed = ['full_name', 'phone', 'profile_image', 'years_experience'];
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
            $sql = 'UPDATE users SET ' . implode(', ', $setParts) . " WHERE id = :user_id AND role = 'artist'";
            $this->db->query($sql);

            foreach ($bindValues as $param => $value) {
                $this->db->bind($param, $value);
            }
            $this->db->bind(':user_id', $user_id);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in update_artist_profile: ' . $e->getMessage());
            return false;
        }
    }

    public function get_pending_role_requests($user_id) {
        try {
            $this->db->query("SELECT ra.*, r.role_name, r.role_description, r.salary,
                             d.title as drama_title, d.id as drama_id,
                             u.name as director_name
                             FROM role_assignments ra
                             INNER JOIN roles r ON ra.role_id = r.id
                             INNER JOIN dramas d ON r.drama_id = d.id
                             INNER JOIN users u ON d.creator_artist_id = u.id
                             WHERE ra.artist_id = :user_id 
                             AND ra.status = 'requested'
                             AND ra.requested_by = 'director'
                             ORDER BY ra.request_date DESC");
            $this->db->bind(':user_id', $user_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in get_pending_role_requests: " . $e->getMessage());
            return [];
        }
    }

    public function respond_to_role_request($request_id, $response) {
        try {
            $status = ($response === 'accept') ? 'accepted' : 'rejected';
            
            $this->db->query("UPDATE role_assignments 
                             SET status = :status, response_date = NOW() 
                             WHERE id = :request_id");
            $this->db->bind(':status', $status);
            $this->db->bind(':request_id', $request_id);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in respond_to_role_request: " . $e->getMessage());
            return false;
        }
    }
}

