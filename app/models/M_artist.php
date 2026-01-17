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
            $this->db->query("SELECT rr.*, r.role_name, r.role_description, r.role_type, r.salary,
                             r.drama_id, r.requirements, r.is_published,
                             d.drama_name, d.description as drama_description,
                             u.full_name as director_name, u.email as director_email, u.phone as director_phone
                             FROM role_requests rr
                             INNER JOIN drama_roles r ON rr.role_id = r.id
                             INNER JOIN dramas d ON r.drama_id = d.id
                             INNER JOIN users u ON rr.director_id = u.id
                             WHERE rr.artist_id = :user_id 
                               AND rr.status IN ('pending','interview')
                             ORDER BY rr.requested_at DESC");
            $this->db->bind(':user_id', $user_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in get_pending_role_requests: " . $e->getMessage());
            return [];
        }
    }

    public function respond_to_role_request($request_id, $artist_id, $response) {
        try {
            $response = strtolower($response);
            if (!in_array($response, ['accept','reject'], true)) {
                return false;
            }

            $this->db->beginTransaction();

            $this->db->query("SELECT rr.*, r.positions_available, r.positions_filled
                              FROM role_requests rr
                              INNER JOIN drama_roles r ON rr.role_id = r.id
                              WHERE rr.id = :request_id AND rr.artist_id = :artist_id FOR UPDATE");
            $this->db->bind(':request_id', $request_id);
            $this->db->bind(':artist_id', $artist_id);
            $request = $this->db->single();

            if (!$request || !in_array($request->status, ['pending','interview'], true)) {
                $this->db->rollBack();
                return false;
            }

            if ($response === 'reject') {
                $this->db->query("UPDATE role_requests 
                                  SET status = 'rejected', responded_at = NOW()
                                  WHERE id = :request_id");
                $this->db->bind(':request_id', $request_id);
                $this->db->execute();
                $this->db->commit();
                return true;
            }

            if ((int)$request->positions_filled >= (int)$request->positions_available) {
                $this->db->rollBack();
                return false;
            }

            $this->db->query("INSERT INTO role_assignments (role_id, artist_id, assigned_by)
                              VALUES (:role_id, :artist_id, :assigned_by)");
            $this->db->bind(':role_id', $request->role_id);
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':assigned_by', $request->director_id);
            $this->db->execute();

            $this->db->query("UPDATE drama_roles 
                              SET positions_filled = positions_filled + 1,
                                  status = CASE WHEN positions_filled + 1 >= positions_available THEN 'filled' ELSE status END
                              WHERE id = :role_id");
            $this->db->bind(':role_id', $request->role_id);
            $this->db->execute();

            $this->db->query("UPDATE role_requests 
                              SET status = 'accepted', responded_at = NOW()
                              WHERE id = :request_id");
            $this->db->bind(':request_id', $request_id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in respond_to_role_request: " . $e->getMessage());
            return false;
        }
    }

    public function get_artists_for_role($role_id, array $filters = []) {
        try {
            $baseQuery = "SELECT u.id, u.full_name, u.email, u.phone, u.profile_image, u.years_experience,
                                 rr.status as request_status, rr.id as request_id,
                                 ra.status as assignment_status
                          FROM users u
                          LEFT JOIN role_requests rr 
                            ON rr.artist_id = u.id AND rr.role_id = :role_id AND rr.status IN ('pending','interview')
                          LEFT JOIN role_assignments ra
                            ON ra.artist_id = u.id AND ra.role_id = :role_id AND ra.status = 'active'
                          WHERE u.role = 'artist'";

            $conditions = [];
            $bindings = [':role_id' => $role_id];

            if (!empty($filters['search'])) {
                $conditions[] = "u.full_name LIKE :search";
                $bindings[':search'] = '%' . $filters['search'] . '%';
            }

            if (!empty($filters['min_experience'])) {
                $conditions[] = "(u.years_experience IS NULL OR u.years_experience >= :min_exp)";
                $bindings[':min_exp'] = (int)$filters['min_experience'];
            }

            if (!empty($filters['max_experience'])) {
                $conditions[] = "(u.years_experience IS NULL OR u.years_experience <= :max_exp)";
                $bindings[':max_exp'] = (int)$filters['max_experience'];
            }

            if (!empty($conditions)) {
                $baseQuery .= ' AND ' . implode(' AND ', $conditions);
            }

            $baseQuery .= ' ORDER BY u.full_name ASC';

            $this->db->query($baseQuery);
            foreach ($bindings as $param => $value) {
                $this->db->bind($param, $value);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error in get_artists_for_role: ' . $e->getMessage());
            return [];
        }
    }
}

