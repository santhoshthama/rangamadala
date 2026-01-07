<?php

class M_drama {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllDramas() {
        try {
            $this->db->query("SELECT d.*, c.name as category_name 
                             FROM dramas d 
                             LEFT JOIN categories c ON d.category_id = c.id 
                             ORDER BY d.created_at DESC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAllDramas: " . $e->getMessage());
            return [];
        }
    }

    public function searchDramas($search = '', $category = '') {
        try {
            $sql = "SELECT d.*, c.name as category_name 
                    FROM dramas d 
                    LEFT JOIN categories c ON d.category_id = c.id 
                    WHERE 1=1";
            
            if (!empty($search)) {
                $sql .= " AND (d.title LIKE :search OR d.description LIKE :search)";
            }
            
            if (!empty($category)) {
                $sql .= " AND d.category_id = :category";
            }
            
            $sql .= " ORDER BY d.created_at DESC";
            
            $this->db->query($sql);
            
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            
            if (!empty($category)) {
                $this->db->bind(':category', $category);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in searchDramas: " . $e->getMessage());
            return [];
        }
    }

    public function getDramaById($drama_id) {
        try {
            $this->db->query("SELECT d.*, c.name as category_name, 
                             u.full_name as creator_name
                             FROM dramas d 
                             LEFT JOIN categories c ON d.category_id = c.id
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
                (title, description, category_id, venue, event_date, event_time, duration, ticket_price, image, created_by, creator_artist_id) 
                VALUES 
                (:title, :description, :category_id, :venue, :event_date, :event_time, :duration, :ticket_price, :image, :created_by, :creator_artist_id)");

            $this->db->bind(':title', $data['title']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':venue', $data['venue']);
            $this->db->bind(':event_date', $data['event_date']);
            $this->db->bind(':event_time', $data['event_time']);
            $this->db->bind(':duration', $data['duration']);
            $this->db->bind(':ticket_price', $data['ticket_price']);
            $this->db->bind(':image', $data['image']);
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

    public function countDramas($search = '', $category = '') {
        try {
            $sql = "SELECT COUNT(*) as cnt FROM dramas d WHERE 1=1";
            if (!empty($search)) {
                $sql .= " AND (d.title LIKE :search OR d.description LIKE :search)";
            }
            if (!empty($category)) {
                $sql .= " AND d.category_id = :category";
            }
            $this->db->query($sql);
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            if (!empty($category)) {
                $this->db->bind(':category', $category);
            }
            $row = $this->db->single();
            return $row ? (int)$row->cnt : 0;
        } catch (Exception $e) {
            error_log("Error in countDramas: " . $e->getMessage());
            return 0;
        }
    }

    public function getDramasPaginated($search = '', $category = '', $limit = 12, $offset = 0, $sort = 'latest') {
        try {
            $orderBy = 'd.created_at DESC';
            $sortMap = [
                'latest' => 'd.created_at DESC',
                'date_asc' => 'd.event_date ASC',
                'date_desc' => 'd.event_date DESC',
                'price_asc' => 'd.ticket_price ASC',
                'price_desc' => 'd.ticket_price DESC',
                'title_asc' => 'd.title ASC',
                'title_desc' => 'd.title DESC',
            ];
            if (isset($sortMap[$sort])) {
                $orderBy = $sortMap[$sort];
            }
            $sql = "SELECT d.*, c.name as category_name 
                    FROM dramas d 
                    LEFT JOIN categories c ON d.category_id = c.id 
                    WHERE 1=1";
            if (!empty($search)) {
                $sql .= " AND (d.title LIKE :search OR d.description LIKE :search)";
            }
            if (!empty($category)) {
                $sql .= " AND d.category_id = :category";
            }
            $sql .= " ORDER BY $orderBy LIMIT :limit OFFSET :offset";
            $this->db->query($sql);
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            if (!empty($category)) {
                $this->db->bind(':category', $category);
            }
            // Bind limit/offset as integers
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
            $this->db->query("SELECT d.*, c.name as category_name,
                             'active' as status
                             FROM dramas d
                             LEFT JOIN categories c ON d.category_id = c.id
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
