<?php

class M_production_manager {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get the currently assigned Production Manager for a drama
     */
    public function getAssignedManager($drama_id) {
        try {
            error_log("[getAssignedManager] Fetching PM for drama_id: $drama_id");
            
            $this->db->query("SELECT dma.*, u.full_name as manager_name, u.email, u.phone,
                             d.drama_name
                             FROM drama_manager_assignments dma
                             INNER JOIN users u ON dma.manager_artist_id = u.id
                             INNER JOIN dramas d ON dma.drama_id = d.id
                             WHERE dma.drama_id = :drama_id 
                             AND dma.status = 'active'
                             ORDER BY dma.assigned_at DESC
                             LIMIT 1");
            $this->db->bind(':drama_id', $drama_id);
            $result = $this->db->single();
            
            if ($result) {
                error_log("[getAssignedManager] Found: " . $result->manager_name . " (ID: " . $result->manager_artist_id . ")");
            } else {
                error_log("[getAssignedManager] No active manager found for drama_id: $drama_id");
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error in getAssignedManager: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new PM request (invitation)
     */
    public function createRequest($drama_id, $artist_id, $director_id, $message = null) {
        try {
            // Check if there's already a pending request for this artist and drama
            $this->db->query("SELECT id FROM drama_manager_requests 
                             WHERE drama_id = :drama_id 
                             AND artist_id = :artist_id 
                             AND status = 'pending'");
            $this->db->bind(':drama_id', $drama_id);
            $this->db->bind(':artist_id', $artist_id);
            
            if ($this->db->single()) {
                return ['success' => false, 'message' => 'A pending request already exists for this artist.'];
            }

            // Create new request
            $this->db->query("INSERT INTO drama_manager_requests 
                             (drama_id, artist_id, director_id, message, requested_at)
                             VALUES (:drama_id, :artist_id, :director_id, :message, NOW())");
            $this->db->bind(':drama_id', $drama_id);
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':director_id', $director_id);
            $this->db->bind(':message', $message);
            
            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Production Manager request sent successfully.'];
            }
            
            return ['success' => false, 'message' => 'Failed to send request.'];
        } catch (Exception $e) {
            error_log("Error in createRequest: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while sending the request.'];
        }
    }

    /**
     * Accept a PM request and assign the manager
     */
    public function acceptRequest($request_id, $artist_id) {
        try {
            $this->db->beginTransaction();

            // Get request details
            $this->db->query("SELECT * FROM drama_manager_requests 
                             WHERE id = :request_id 
                             AND artist_id = :artist_id 
                             AND status = 'pending'");
            $this->db->bind(':request_id', $request_id);
            $this->db->bind(':artist_id', $artist_id);
            $request = $this->db->single();

            if (!$request) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Request not found or already processed.'];
            }

            // Update request status
            $this->db->query("UPDATE drama_manager_requests 
                             SET status = 'accepted', 
                             responded_at = NOW()
                             WHERE id = :request_id");
            $this->db->bind(':request_id', $request_id);
            $this->db->execute();

            // Remove any existing active manager for this drama
            // Note: status is suffixed with the row id to avoid hitting the
            // unique constraint on (drama_id, status) when multiple removed
            // rows exist for the same drama.
            $this->db->query("UPDATE drama_manager_assignments 
                             SET status = CONCAT('removed_', id), 
                             removed_at = NOW()
                             WHERE drama_id = :drama_id 
                             AND status = 'active'");
            $this->db->bind(':drama_id', $request->drama_id);
            $this->db->execute();

            // Create new assignment
            $this->db->query("INSERT INTO drama_manager_assignments 
                             (drama_id, manager_artist_id, assigned_by, assigned_at, status)
                             VALUES (:drama_id, :artist_id, :assigned_by, NOW(), 'active')");
            $this->db->bind(':drama_id', $request->drama_id);
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':assigned_by', $request->director_id);
            $this->db->execute();

            // Cancel any other pending requests for this drama
            $this->db->query("UPDATE drama_manager_requests 
                             SET status = 'cancelled'
                             WHERE drama_id = :drama_id 
                             AND status = 'pending' 
                             AND id != :request_id");
            $this->db->bind(':drama_id', $request->drama_id);
            $this->db->bind(':request_id', $request_id);
            $this->db->execute();

            $this->db->commit();
            return ['success' => true, 'message' => 'You are now the Production Manager for this drama.'];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in acceptRequest: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while accepting the request.'];
        }
    }

    /**
     * Reject a PM request
     */
    public function rejectRequest($request_id, $artist_id, $response_note = null) {
        try {
            $this->db->query("UPDATE drama_manager_requests 
                             SET status = 'rejected', 
                             responded_at = NOW(),
                             response_note = :response_note
                             WHERE id = :request_id 
                             AND artist_id = :artist_id 
                             AND status = 'pending'");
            $this->db->bind(':request_id', $request_id);
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':response_note', $response_note);
            
            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Request declined.'];
            }
            
            return ['success' => false, 'message' => 'Request not found or already processed.'];
        } catch (Exception $e) {
            error_log("Error in rejectRequest: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while declining the request.'];
        }
    }

    /**
     * Get pending PM requests for an artist
     */
    public function getPendingRequestsForArtist($artist_id) {
        try {
            $this->db->query("SELECT dmr.*, d.drama_name, d.certificate_number,
                             u.full_name as director_name, u.email as director_email
                             FROM drama_manager_requests dmr
                             INNER JOIN dramas d ON dmr.drama_id = d.id
                             INNER JOIN users u ON dmr.director_id = u.id
                             WHERE dmr.artist_id = :artist_id 
                             AND dmr.status = 'pending'
                             ORDER BY dmr.requested_at DESC");
            $this->db->bind(':artist_id', $artist_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getPendingRequestsForArtist: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all PM requests for a drama
     */
    public function getRequestsByDrama($drama_id, $status = null) {
        try {
            $sql = "SELECT dmr.*, u.full_name as artist_name, u.email as artist_email,
                    u.phone as artist_phone, NULL AS years_experience
                    FROM drama_manager_requests dmr
                    INNER JOIN users u ON dmr.artist_id = u.id
                    WHERE dmr.drama_id = :drama_id";
            
            if ($status) {
                $sql .= " AND dmr.status = :status";
            }
            
            $sql .= " ORDER BY dmr.requested_at DESC";
            
            $this->db->query($sql);
            $this->db->bind(':drama_id', $drama_id);
            
            if ($status) {
                $this->db->bind(':status', $status);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getRequestsByDrama: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Remove current Production Manager
     */
    public function removeManager($drama_id, $director_id) {
        try {
            // Suffix status to keep rows unique per drama when multiple
            // removals occur; this avoids duplicate-key errors on
            // (drama_id, status) unique constraint.
            $this->db->query("UPDATE drama_manager_assignments 
                             SET status = CONCAT('removed_', id), 
                             removed_at = NOW()
                             WHERE drama_id = :drama_id 
                             AND status = 'active'");
            $this->db->bind(':drama_id', $drama_id);
            
            $this->db->execute();
            $affected = $this->db->rowCount();

            if ($affected > 0) {
                return ['success' => true, 'message' => 'Production Manager removed successfully.'];
            }

            return ['success' => false, 'message' => 'No active manager found.'];
        } catch (Exception $e) {
            error_log("Error in removeManager: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while removing the manager.'];
        }
    }

    /**
     * Get dramas where user is the Production Manager
     */
    public function getDramasByManager($artist_id) {
        try {
            $this->db->query("SELECT d.*, dma.assigned_at, 'active' as status,
                             creator.full_name as creator_name
                             FROM drama_manager_assignments dma
                             INNER JOIN dramas d ON dma.drama_id = d.id
                             LEFT JOIN users creator ON d.creator_artist_id = creator.id
                             WHERE dma.manager_artist_id = :artist_id 
                             AND dma.status = 'active'
                             ORDER BY dma.assigned_at DESC");
            $this->db->bind(':artist_id', $artist_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getDramasByManager: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search for artists who can be Production Managers
     * Excludes the drama director and already assigned PM
     */
    public function searchAvailableManagers($drama_id, $director_id, $search = '') {
        try {
            // Simple query matching M_artist::get_artists_for_role()
            // Uses nic_photo for profile_image, returns NULL for years_experience and status fields
            $sql = "SELECT u.id, u.full_name, u.email, u.phone,
                    u.nic_photo AS profile_image,
                    NULL AS years_experience,
                    NULL AS request_status,
                    NULL AS request_id,
                    NULL AS assignment_status
                    FROM users u
                    WHERE u.role IS NOT NULL 
                    AND LOWER(TRIM(u.role)) = 'artist'
                    AND u.id != :director_id
                    AND NOT EXISTS (
                        SELECT 1 FROM drama_manager_assignments dma
                        WHERE dma.drama_id = :drama_id
                          AND dma.status = 'active'
                          AND dma.manager_artist_id = u.id
                    )";
            
            // Search filter
            if (!empty($search)) {
                $sql .= " AND LOWER(u.full_name) LIKE :search";
            }
            
            $sql .= " ORDER BY u.full_name ASC";
            
            $this->db->query($sql);
            $this->db->bind(':director_id', $director_id);
            $this->db->bind(':drama_id', $drama_id);
            
            if (!empty($search)) {
                $this->db->bind(':search', '%' . strtolower($search) . '%');
            }
            
            $results = $this->db->resultSet();
            
            return is_array($results) ? $results : [];
        } catch (Exception $e) {
            error_log("Error in searchAvailableManagers: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if artist is assigned as PM for a drama
     */
    public function isManagerForDrama($artist_id, $drama_id) {
        try {
            $this->db->query("SELECT id FROM drama_manager_assignments 
                             WHERE drama_id = :drama_id 
                             AND manager_artist_id = :artist_id 
                             AND status = 'active'");
            $this->db->bind(':drama_id', $drama_id);
            $this->db->bind(':artist_id', $artist_id);
            
            return $this->db->single() !== false;
        } catch (Exception $e) {
            error_log("Error in isManagerForDrama: " . $e->getMessage());
            return false;
        }
    }
}

?>
