<?php

class M_drama {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllDramas() {
        try {
            $this->db->query("SELECT * FROM dramas ORDER BY created_at DESC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAllDramas: " . $e->getMessage());
            return [];
        }
    }

    public function searchDramas($search = '') {
        try {
            $sql = "SELECT * FROM dramas WHERE 1=1";

            if (!empty($search)) {
                $sql .= " AND (drama_name LIKE :search OR certificate_number LIKE :search OR owner_name LIKE :search)";
            }

            $sql .= " ORDER BY created_at DESC";

            $this->db->query($sql);

            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in searchDramas: " . $e->getMessage());
            return [];
        }
    }

    public function getDramaById($drama_id) {
        try {
            $this->db->query("SELECT d.*, u.full_name as creator_name
                             FROM dramas d 
                             LEFT JOIN users u ON d.created_by = u.id
                             WHERE d.id = :id");
            $this->db->bind(':id', $drama_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getDramaById: " . $e->getMessage());
            return null;
        }
    }

    public function getAllCategories() {
        try {
            $this->db->query("SELECT * FROM categories ORDER BY name ASC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAllCategories: " . $e->getMessage());
            return [];
        }
    }

    public function createDrama($data) {
        try {
            $this->db->query("INSERT INTO dramas 
                (drama_name, certificate_number, owner_name, description, certificate_image, created_by, creator_artist_id) 
                VALUES 
                (:drama_name, :certificate_number, :owner_name, :description, :certificate_image, :created_by, :creator_artist_id)");

            $this->db->bind(':drama_name', $data['drama_name']);
            $this->db->bind(':certificate_number', $data['certificate_number']);
            $this->db->bind(':owner_name', $data['owner_name']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':certificate_image', $data['certificate_image']);
            $this->db->bind(':created_by', $data['created_by']);
            $this->db->bind(':creator_artist_id', $data['created_by']); // Artist becomes director

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in createDrama: " . $e->getMessage());
            return false;
        }
    }

    public function updateDrama($drama_id, $data) {
        try {
            $fields = [
                'drama_name' => ':drama_name',
                'certificate_number' => ':certificate_number',
                'owner_name' => ':owner_name',
                'description' => ':description',
            ];

            if (array_key_exists('certificate_image', $data)) {
                $fields['certificate_image'] = ':certificate_image';
            }

            $setParts = [];
            foreach ($fields as $column => $placeholder) {
                $setParts[] = "{$column} = {$placeholder}";
            }
            $setParts[] = "updated_at = NOW()";

            $sql = "UPDATE dramas SET " . implode(', ', $setParts) . " WHERE id = :id";
            $this->db->query($sql);

            $this->db->bind(':drama_name', $data['drama_name']);
            $this->db->bind(':certificate_number', $data['certificate_number']);
            $this->db->bind(':owner_name', $data['owner_name']);
            $this->db->bind(':description', $data['description']);

            if (array_key_exists('certificate_image', $data)) {
                $this->db->bind(':certificate_image', $data['certificate_image']);
            }

            $this->db->bind(':id', $drama_id);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateDrama: " . $e->getMessage());
            return false;
        }
    }

    public function countDramas($search = '') {
        try {
            $sql = "SELECT COUNT(*) as cnt FROM dramas WHERE 1=1";
            if (!empty($search)) {
                $sql .= " AND (drama_name LIKE :search OR certificate_number LIKE :search OR owner_name LIKE :search)";
            }
            $this->db->query($sql);
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            $row = $this->db->single();
            return $row ? (int)$row->cnt : 0;
        } catch (Exception $e) {
            error_log("Error in countDramas: " . $e->getMessage());
            return 0;
        }
    }

    public function getDramasPaginated($search = '', $limit = 12, $offset = 0, $sort = 'latest') {
        try {
            $orderBy = 'created_at DESC';
            $sortMap = [
                'latest' => 'created_at DESC',
                'name_asc' => 'drama_name ASC',
                'name_desc' => 'drama_name DESC',
            ];
            if (isset($sortMap[$sort])) {
                $orderBy = $sortMap[$sort];
            }
            $sql = "SELECT * FROM dramas WHERE 1=1";
            if (!empty($search)) {
                $sql .= " AND (drama_name LIKE :search OR certificate_number LIKE :search OR owner_name LIKE :search)";
            }
            $sql .= " ORDER BY $orderBy LIMIT :limit OFFSET :offset";
            $this->db->query($sql);
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
            $this->db->bind(':offset', (int)$offset, PDO::PARAM_INT);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getDramasPaginated: " . $e->getMessage());
            return [];
        }
    }

    public function get_dramas_by_director($user_id) {
        try {
            $this->db->query("SELECT d.*, 'active' as status
                             FROM dramas d
                             WHERE d.creator_artist_id = :user_id
                             ORDER BY d.created_at DESC");
            $this->db->bind(':user_id', $user_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in get_dramas_by_director: " . $e->getMessage());
            return [];
        }
    }

    public function get_dramas_by_manager($user_id) {
        // TODO: Implement drama_managers table and functionality
        // For now, return empty array as this feature is not yet implemented
        return [];
    }

    public function get_dramas_by_actor($user_id) {
        // TODO: Implement roles and role_assignments tables and functionality
        // For now, return empty array as this feature is not yet implemented
        return [];
    }
}

?>
