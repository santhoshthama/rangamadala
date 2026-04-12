<?php

class M_drama {
    protected $db;
    protected $lastDramaRequestError = '';
    protected $dramaRequestTableReady = null;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllDramas() {
        try {
            $this->db->query("SELECT d.*, 
                                    d.drama_name AS title,
                                    d.poster_image AS image,
                                    d.public_description AS description,
                                    d.genre,
                                    d.language,
                                    d.duration_minutes,
                                    d.venue,
                                    d.event_date,
                                    TIME_FORMAT(d.event_time, '%h:%i %p') AS event_time,
                                    d.ticket_price,
                                    c.name AS category_name
                             FROM dramas d
                             LEFT JOIN categories c ON d.category_id = c.id
                             WHERE d.is_published = 1
                             ORDER BY d.published_at DESC, d.created_at DESC");
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
            $this->db->query("SELECT d.*, 
                                    d.drama_name AS title,
                                    d.poster_image AS image,
                                    d.public_description AS public_synopsis,
                                    d.public_description AS description,
                                    TIME_FORMAT(d.event_time, '%h:%i %p') AS event_time,
                                    c.name AS category_name
                             FROM dramas d
                             LEFT JOIN categories c ON d.category_id = c.id
                             WHERE d.id = :id
                             LIMIT 1");
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

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in createDrama: " . $e->getMessage());
            return false;
        }
    }

    private function ensureDramaRequestTableExists() {
        if ($this->dramaRequestTableReady !== null) {
            return $this->dramaRequestTableReady;
        }

        try {
            $this->db->query("SELECT COUNT(*) AS cnt
                             FROM information_schema.tables
                             WHERE table_schema = DATABASE()
                               AND table_name = 'drama_creation_requests'");
            $row = $this->db->single();
            $this->dramaRequestTableReady = $row && (int)$row->cnt > 0;
            if (!$this->dramaRequestTableReady) {
                error_log('M_drama: drama_creation_requests table not found. Run add_drama_creation_approval_workflow.sql.');
            }

            return $this->dramaRequestTableReady;
        } catch (Exception $e) {
            error_log("Error in ensureDramaRequestTableExists: " . $e->getMessage());
            $this->dramaRequestTableReady = false;
            return false;
        }
    }

    public function certificateNumberExistsInDramas($certificate_number) {
        try {
            $this->db->query("SELECT id FROM dramas WHERE certificate_number = :certificate_number LIMIT 1");
            $this->db->bind(':certificate_number', $certificate_number);
            return (bool)$this->db->single();
        } catch (Exception $e) {
            error_log("Error in certificateNumberExistsInDramas: " . $e->getMessage());
            return false;
        }
    }

    public function hasPendingDramaRequest($artist_id, $certificate_number) {
        try {
            if (!$this->ensureDramaRequestTableExists()) {
                return false;
            }
            $this->db->query("SELECT id FROM drama_creation_requests
                             WHERE certificate_number = :certificate_number
                             AND status = 'pending'
                             LIMIT 1");
            $this->db->bind(':certificate_number', $certificate_number);
            return (bool)$this->db->single();
        } catch (Exception $e) {
            error_log("Error in hasPendingDramaRequest: " . $e->getMessage());
            return false;
        }
    }

    public function createDramaRequest($data) {
        try {
            $this->lastDramaRequestError = '';
            if (!$this->ensureDramaRequestTableExists()) {
                return ['success' => false, 'message' => 'Drama request service is unavailable.'];
            }

            $this->db->query("INSERT INTO drama_creation_requests
                (drama_name, certificate_number, owner_name, description, certificate_image, requested_by)
                VALUES
                (:drama_name, :certificate_number, :owner_name, :description, :certificate_image, :requested_by)");

            $this->db->bind(':drama_name', $data['drama_name']);
            $this->db->bind(':certificate_number', $data['certificate_number']);
            $this->db->bind(':owner_name', $data['owner_name']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':certificate_image', $data['certificate_image']);
            $this->db->bind(':requested_by', (int)$data['requested_by']);

            $ok = $this->db->execute();
            if ($ok) {
                return ['success' => true, 'message' => 'Drama request submitted successfully.'];
            }

            $this->lastDramaRequestError = 'Failed to submit drama request.';
            return ['success' => false, 'message' => $this->lastDramaRequestError];
        } catch (Exception $e) {
            $message = $e->getMessage();
            $this->lastDramaRequestError = $message;
            error_log("Error in createDramaRequest: " . $e->getMessage());

            if (stripos($message, 'Duplicate entry') !== false) {
                return ['success' => false, 'message' => 'Certificate number is already used in another request or drama.'];
            }

            if (stripos($message, 'foreign key constraint fails') !== false) {
                return ['success' => false, 'message' => 'Invalid account reference for this request. Please log out and log in again.'];
            }

            return ['success' => false, 'message' => 'System error while submitting drama request.'];
        }
    }

    public function getPendingDramaRequests() {
        try {
            if (!$this->ensureDramaRequestTableExists()) {
                return [];
            }
            $this->db->query("SELECT r.*, u.full_name AS artist_name, u.email AS artist_email, u.phone AS artist_phone
                             FROM drama_creation_requests r
                             LEFT JOIN users u ON r.requested_by = u.id
                             WHERE r.status = 'pending'
                             ORDER BY r.created_at ASC");
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getPendingDramaRequests: " . $e->getMessage());
            return [];
        }
    }

    public function approveDramaRequest($request_id, $admin_id) {
        try {
            if (!$this->ensureDramaRequestTableExists()) {
                return ['success' => false, 'message' => 'Drama request table is unavailable.'];
            }
            $this->db->beginTransaction();

            $this->db->query("SELECT * FROM drama_creation_requests
                             WHERE id = :id AND status = 'pending'
                             LIMIT 1");
            $this->db->bind(':id', (int)$request_id);
            $request = $this->db->single();

            if (!$request) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Drama request not found or already processed.'];
            }

            $this->db->query("INSERT INTO dramas
                (drama_name, certificate_number, owner_name, description, public_description, certificate_image, created_by, creator_artist_id, is_published, published_at, published_by)
                VALUES
                (:drama_name, :certificate_number, :owner_name, :description, :public_description, :certificate_image, :created_by, :creator_artist_id, 0, NULL, NULL)");
            $this->db->bind(':drama_name', $request->drama_name);
            $this->db->bind(':certificate_number', $request->certificate_number);
            $this->db->bind(':owner_name', $request->owner_name);
            $this->db->bind(':description', $request->description);
            $this->db->bind(':public_description', $request->description);
            $this->db->bind(':certificate_image', $request->certificate_image);
            $this->db->bind(':created_by', (int)$request->requested_by);
            $this->db->bind(':creator_artist_id', (int)$request->requested_by);

            if (!$this->db->execute()) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Unable to create drama from request.'];
            }

            $newDramaId = (int)$this->db->lastInsertId();

            $this->db->query("UPDATE drama_creation_requests
                             SET status = 'approved',
                                 reviewed_by = :admin_id,
                                 reviewed_at = CURRENT_TIMESTAMP,
                                 rejection_reason = NULL,
                                 created_drama_id = :drama_id
                             WHERE id = :id");
            $this->db->bind(':admin_id', (int)$admin_id);
            $this->db->bind(':drama_id', $newDramaId);
            $this->db->bind(':id', (int)$request_id);

            if (!$this->db->execute()) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Unable to update request approval status.'];
            }

            $this->db->commit();
            return ['success' => true, 'message' => 'Drama approved successfully. Artist must publish it before it appears to audience.'];
        } catch (Exception $e) {
            if ($this->db) {
                try {
                    $this->db->rollBack();
                } catch (Exception $ignored) {
                }
            }
            if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['success' => false, 'message' => 'A request with this certificate is already approved/rejected earlier. Please refresh and review request history.'];
            }
            error_log("Error in approveDramaRequest: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to approve drama request.'];
        }
    }

    public function rejectDramaRequest($request_id, $admin_id, $reason = null) {
        try {
            if (!$this->ensureDramaRequestTableExists()) {
                return ['success' => false, 'message' => 'Drama request table is unavailable.'];
            }
            $this->db->query("UPDATE drama_creation_requests
                             SET status = 'rejected',
                                 reviewed_by = :admin_id,
                                 reviewed_at = CURRENT_TIMESTAMP,
                                 rejection_reason = :reason,
                                 created_drama_id = NULL
                             WHERE id = :id
                             AND status = 'pending'");
            $this->db->bind(':admin_id', (int)$admin_id);
            $this->db->bind(':reason', $reason !== null && trim($reason) !== '' ? trim($reason) : 'Rejected by admin');
            $this->db->bind(':id', (int)$request_id);
            $ok = $this->db->execute();

            if (!$ok || $this->db->rowCount() < 1) {
                return ['success' => false, 'message' => 'Drama request not found or already processed.'];
            }

            return ['success' => true, 'message' => 'Drama request rejected successfully.'];
        } catch (Exception $e) {
            if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
                return ['success' => false, 'message' => 'This request cannot be rejected due to duplicate certificate status history.'];
            }
            error_log("Error in rejectDramaRequest: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to reject drama request.'];
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

    public function countDramas($search = '', $category = '') {
        try {
            $sql = "SELECT COUNT(*) as cnt FROM dramas WHERE is_published = 1";
            if (!empty($search)) {
                $sql .= " AND (drama_name LIKE :search OR public_description LIKE :search OR owner_name LIKE :search OR genre LIKE :search OR venue LIKE :search)";
            }
            if (!empty($category)) {
                $sql .= " AND category_id = :category";
            }
            $this->db->query($sql);
            if (!empty($search)) {
                $this->db->bind(':search', '%' . $search . '%');
            }
            if (!empty($category)) {
                $this->db->bind(':category', (int)$category);
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
            $orderBy = 'published_at DESC, created_at DESC';
            $sortMap = [
                'latest' => 'published_at DESC, created_at DESC',
                'date_asc' => 'event_date ASC, event_time ASC',
                'date_desc' => 'event_date DESC, event_time DESC',
                'price_asc' => 'ticket_price ASC',
                'price_desc' => 'ticket_price DESC',
                'title_asc' => 'drama_name ASC',
                'title_desc' => 'drama_name DESC',
            ];
            if (isset($sortMap[$sort])) {
                $orderBy = $sortMap[$sort];
            }
            $sql = "SELECT d.*, 
                           d.drama_name AS title,
                           d.poster_image AS image,
                           d.public_description AS description,
                           TIME_FORMAT(d.event_time, '%h:%i %p') AS event_time,
                           c.name AS category_name
                    FROM dramas d
                    LEFT JOIN categories c ON d.category_id = c.id
                    WHERE d.is_published = 1";
            if (!empty($search)) {
                $sql .= " AND (d.drama_name LIKE :search OR d.public_description LIKE :search OR d.owner_name LIKE :search OR d.genre LIKE :search OR d.venue LIKE :search)";
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
                $this->db->bind(':category', (int)$category);
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
            $this->db->query("SELECT d.*, 
                                     'active' as status,
                                     d.is_published,
                                     d.published_at,
                                     d.poster_image
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

    public function publishDrama($dramaId, $directorId, array $data) {
        try {
            $this->db->query("UPDATE dramas
                             SET category_id = :category_id,
                                 public_description = :public_description,
                                 genre = :genre,
                                 language = :language,
                                 duration_minutes = :duration_minutes,
                                 venue = :venue,
                                 event_date = :event_date,
                                 event_time = :event_time,
                                 ticket_price = :ticket_price,
                                 showing_prices = :showing_prices,
                                 poster_image = :poster_image,
                                 is_published = 1,
                                 published_at = CURRENT_TIMESTAMP,
                                 published_by = :published_by,
                                 updated_at = CURRENT_TIMESTAMP
                             WHERE id = :id
                             AND creator_artist_id = :director_id");

            $this->db->bind(':category_id', (int)$data['category_id']);
            $this->db->bind(':public_description', $data['public_description']);
            $this->db->bind(':genre', $data['genre']);
            $this->db->bind(':language', $data['language']);
            $this->db->bind(':duration_minutes', (int)$data['duration_minutes']);
            $this->db->bind(':venue', $data['venue']);
            $this->db->bind(':event_date', $data['event_date']);
            $this->db->bind(':event_time', $data['event_time']);
            $this->db->bind(':ticket_price', $data['ticket_price']);
            $this->db->bind(':showing_prices', $data['showing_prices']);
            $this->db->bind(':poster_image', $data['poster_image']);
            $this->db->bind(':published_by', (int)$directorId);
            $this->db->bind(':id', (int)$dramaId);
            $this->db->bind(':director_id', (int)$directorId);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in publishDrama: ' . $e->getMessage());
            return false;
        }
    }

    public function queuePosterForAdminHome($dramaId) {
        try {
            $this->db->query("SELECT id, drama_name, poster_image FROM dramas WHERE id = :id LIMIT 1");
            $this->db->bind(':id', (int)$dramaId);
            $drama = $this->db->single();

            if (!$drama || empty($drama->poster_image)) {
                return false;
            }

            $posterPath = 'uploads/dramas/' . ltrim($drama->poster_image, '/');

            $this->db->query("SELECT id FROM swiper_slides WHERE drama_id = :drama_id LIMIT 1");
            $this->db->bind(':drama_id', (int)$dramaId);
            $existing = $this->db->single();

            if ($existing) {
                $this->db->query("UPDATE swiper_slides
                                 SET image_path = :image_path,
                                     title = :title,
                                     description = :description,
                                     is_active = 0,
                                     updated_at = CURRENT_TIMESTAMP
                                 WHERE id = :id");
                $this->db->bind(':image_path', $posterPath);
                $this->db->bind(':title', $drama->drama_name);
                $this->db->bind(':description', 'Submitted by director for home page approval');
                $this->db->bind(':id', (int)$existing->id);
                return $this->db->execute();
            }

            $this->db->query("INSERT INTO swiper_slides (id, image_path, title, description, drama_id, display_order, is_active)
                             VALUES (
                                (SELECT COALESCE(MAX(s.id), 0) + 1 FROM (SELECT id FROM swiper_slides) s),
                                :image_path,
                                :title,
                                :description,
                                :drama_id,
                                (SELECT COALESCE(MAX(display_order), 0) + 1 FROM swiper_slides),
                                0
                             )");
            $this->db->bind(':image_path', $posterPath);
            $this->db->bind(':title', $drama->drama_name);
            $this->db->bind(':description', 'Submitted by director for home page approval');
            $this->db->bind(':drama_id', (int)$dramaId);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in queuePosterForAdminHome: ' . $e->getMessage());
            return false;
        }
    }

    public function get_dramas_by_manager($user_id) {
        try {
            // Get dramas where user is assigned as Production Manager
            $this->db->query("SELECT d.*, dma.assigned_at, 'active' as status,
                             creator.full_name as creator_name
                             FROM drama_manager_assignments dma
                             INNER JOIN dramas d ON dma.drama_id = d.id
                             LEFT JOIN users creator ON d.creator_artist_id = creator.id
                             WHERE dma.manager_artist_id = :user_id 
                             AND dma.status = 'active'
                             ORDER BY dma.assigned_at DESC");
            $this->db->bind(':user_id', $user_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in get_dramas_by_manager: " . $e->getMessage());
            return [];
        }
    }

    public function get_dramas_by_actor($user_id) {
        // TODO: Implement roles and role_assignments tables and functionality
        // For now, return empty array as this feature is not yet implemented
        return [];
    }
}

?>
