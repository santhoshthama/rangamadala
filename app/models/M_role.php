<?php

class M_role {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    // CREATE - Add a new role
    public function createRole($data) {
        try {
            error_log("M_role::createRole called with data: " . print_r($data, true));
            
            $this->db->query("INSERT INTO drama_roles 
                (drama_id, role_name, role_description, role_type, salary, 
                 positions_available, requirements, created_by) 
                VALUES 
                (:drama_id, :role_name, :role_description, :role_type, :salary, 
                 :positions_available, :requirements, :created_by)");

            $this->db->bind(':drama_id', $data['drama_id']);
            $this->db->bind(':role_name', $data['role_name']);
            $this->db->bind(':role_description', $data['role_description'] ?? null);
            $this->db->bind(':role_type', $data['role_type'] ?? 'supporting');
            $this->db->bind(':salary', $data['salary'] ?? null);
            $this->db->bind(':positions_available', $data['positions_available'] ?? 1);
            $this->db->bind(':requirements', $data['requirements'] ?? null);
            $this->db->bind(':created_by', $data['created_by']);

            error_log("Executing insert query...");
            if ($this->db->execute()) {
                $lastId = $this->db->lastInsertId();
                error_log("Role created successfully with ID: " . $lastId);
                return $lastId;
            }
            error_log("Execute failed - no exception but execute returned false");
            return false;
        } catch (Exception $e) {
            error_log("Error in createRole: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    // READ - Get all roles for a drama
    public function getRolesByDrama($drama_id) {
        try {
            $this->db->query("SELECT r.*, 
                             (r.positions_available - r.positions_filled) as available_positions,
                             u.full_name as created_by_name,
                             CASE WHEN ra.id IS NOT NULL THEN 1 ELSE 0 END as is_filled,
                             artist_user.full_name as assigned_artist_name
                             FROM drama_roles r
                             LEFT JOIN users u ON r.created_by = u.id
                             LEFT JOIN role_assignments ra ON r.id = ra.role_id AND ra.status = 'active'
                             LEFT JOIN users artist_user ON ra.artist_id = artist_user.id
                             WHERE r.drama_id = :drama_id
                             ORDER BY r.created_at DESC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getRolesByDrama: " . $e->getMessage());
            return [];
        }
    }

    // READ - Get single role by ID
    public function getRoleById($role_id) {
        try {
            $this->db->query("SELECT r.*, 
                             (r.positions_available - r.positions_filled) as available_positions,
                             u.full_name as created_by_name,
                             d.drama_name
                             FROM drama_roles r
                             LEFT JOIN users u ON r.created_by = u.id
                             LEFT JOIN dramas d ON r.drama_id = d.id
                             WHERE r.id = :role_id");
            $this->db->bind(':role_id', $role_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getRoleById: " . $e->getMessage());
            return null;
        }
    }

    // UPDATE - Update role
    public function updateRole($role_id, $data) {
        try {
            $this->db->query("UPDATE drama_roles SET
                role_name = :role_name,
                role_description = :role_description,
                role_type = :role_type,
                salary = :salary,
                positions_available = :positions_available,
                requirements = :requirements,
                status = :status
                WHERE id = :role_id");

            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':role_name', $data['role_name']);
            $this->db->bind(':role_description', $data['role_description'] ?? null);
            $this->db->bind(':role_type', $data['role_type'] ?? 'supporting');
            $this->db->bind(':salary', $data['salary'] ?? null);
            $this->db->bind(':positions_available', $data['positions_available'] ?? 1);
            $this->db->bind(':requirements', $data['requirements'] ?? null);
            $this->db->bind(':status', $data['status'] ?? 'open');

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateRole: " . $e->getMessage());
            return false;
        }
    }

    // DELETE - Delete role
    public function deleteRole($role_id) {
        try {
            // Check if role has assignments
            $this->db->query("SELECT COUNT(*) as count FROM role_assignments WHERE role_id = :role_id");
            $this->db->bind(':role_id', $role_id);
            $result = $this->db->single();
            
            if ($result && $result->count > 0) {
                // Can't delete role with assignments - mark as closed instead
                $this->db->query("UPDATE drama_roles SET status = 'closed' WHERE id = :role_id");
                $this->db->bind(':role_id', $role_id);
                return $this->db->execute();
            }

            // No assignments, safe to delete
            $this->db->query("DELETE FROM drama_roles WHERE id = :role_id");
            $this->db->bind(':role_id', $role_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in deleteRole: " . $e->getMessage());
            return false;
        }
    }

    // Get role statistics for a drama
    public function getRoleStats($drama_id) {
        try {
            $this->db->query("SELECT 
                COUNT(*) as total_roles,
                SUM(positions_available) as total_positions,
                SUM(positions_filled) as filled_positions,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) as open_roles,
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_roles,
                SUM(salary) as total_salary_budget
                FROM drama_roles 
                WHERE drama_id = :drama_id");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getRoleStats: " . $e->getMessage());
            return null;
        }
    }

    // Get applications for a role
    public function getApplicationsByRole($role_id) {
        try {
            $this->db->query("SELECT a.*, 
                             u.full_name as artist_name,
                             u.email as artist_email
                             FROM role_applications a
                             INNER JOIN users u ON a.artist_id = u.id
                             WHERE a.role_id = :role_id
                             ORDER BY a.applied_at DESC");
            $this->db->bind(':role_id', $role_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getApplicationsByRole: " . $e->getMessage());
            return [];
        }
    }

    public function getApplicationById($application_id) {
        try {
            $this->db->query("SELECT a.*, r.drama_id, r.role_name, r.status as role_status
                              FROM role_applications a
                              INNER JOIN drama_roles r ON a.role_id = r.id
                              WHERE a.id = :application_id");
            $this->db->bind(':application_id', $application_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('Error in getApplicationById: ' . $e->getMessage());
            return null;
        }
    }

    // Get all pending applications for a drama
    public function getPendingApplications($drama_id) {
        try {
            $this->db->query("SELECT a.*, 
                             r.role_name,
                             u.full_name as artist_name,
                             u.email as artist_email
                             FROM role_applications a
                             INNER JOIN drama_roles r ON a.role_id = r.id
                             INNER JOIN users u ON a.artist_id = u.id
                             WHERE r.drama_id = :drama_id AND a.status = 'pending'
                             ORDER BY a.applied_at DESC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getPendingApplications: " . $e->getMessage());
            return [];
        }
    }

    // Get assigned artists for a role
    public function getAssignmentsByRole($role_id) {
        try {
            $this->db->query("SELECT a.*, 
                             u.full_name as artist_name,
                             u.email as artist_email,
                             u.phone as artist_phone
                             FROM role_assignments a
                             INNER JOIN users u ON a.artist_id = u.id
                             WHERE a.role_id = :role_id AND a.status = 'active'
                             ORDER BY a.assigned_at DESC");
            $this->db->bind(':role_id', $role_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAssignmentsByRole: " . $e->getMessage());
            return [];
        }
    }

    // Accept application and assign role
    public function acceptApplication($application_id, $reviewed_by) {
        try {
            $this->db->beginTransaction();

            // Get application details with role info
            $this->db->query("SELECT a.*, r.positions_available, r.positions_filled 
                              FROM role_applications a
                              INNER JOIN drama_roles r ON a.role_id = r.id
                              WHERE a.id = :id FOR UPDATE");
            $this->db->bind(':id', $application_id);
            $app = $this->db->single();

            if (!$app) {
                $this->db->rollBack();
                return false;
            }

            // Check if role is full
            if ((int)$app->positions_filled >= (int)$app->positions_available) {
                $this->db->rollBack();
                return false;
            }

            // Update application status
            $this->db->query("UPDATE role_applications 
                SET status = 'accepted', reviewed_at = NOW(), reviewed_by = :reviewed_by 
                WHERE id = :id");
            $this->db->bind(':id', $application_id);
            $this->db->bind(':reviewed_by', $reviewed_by);
            $this->db->execute();

            // Create assignment
            $this->db->query("INSERT INTO role_assignments 
                (role_id, artist_id, assigned_by) 
                VALUES (:role_id, :artist_id, :assigned_by)");
            $this->db->bind(':role_id', $app->role_id);
            $this->db->bind(':artist_id', $app->artist_id);
            $this->db->bind(':assigned_by', $reviewed_by);
            $this->db->execute();

            // Update positions filled
            $this->db->query("UPDATE drama_roles 
                SET positions_filled = positions_filled + 1 
                WHERE id = :role_id");
            $this->db->bind(':role_id', $app->role_id);
            $this->db->execute();

            // Check if role is now filled and update status
            $this->db->query("UPDATE drama_roles 
                SET status = 'filled' 
                WHERE id = :role_id AND positions_filled >= positions_available");
            $this->db->bind(':role_id', $app->role_id);
            $this->db->execute();

            // Auto-cancel pending requests if role is now full
            $newPositionsFilled = (int)$app->positions_filled + 1;
            $positionsAvailable = (int)$app->positions_available;
            
            if ($newPositionsFilled >= $positionsAvailable) {
                error_log("Role {$app->role_id} is now full from application acceptance. Auto-cancelling pending requests.");
                
                // Cancel pending role requests
                $this->db->query("UPDATE role_requests 
                                  SET status = 'cancelled', responded_at = NOW()
                                  WHERE role_id = :role_id 
                                  AND status IN ('pending', 'interview')");
                $this->db->bind(':role_id', $app->role_id);
                $requestsAffected = $this->db->execute();
                
                // Reject other pending applications
                $this->db->query("UPDATE role_applications 
                                  SET status = 'rejected', reviewed_at = NOW(), reviewed_by = :reviewed_by
                                  WHERE role_id = :role_id 
                                  AND status = 'pending'
                                  AND id != :current_app_id");
                $this->db->bind(':role_id', $app->role_id);
                $this->db->bind(':reviewed_by', $reviewed_by);
                $this->db->bind(':current_app_id', $application_id);
                $appsAffected = $this->db->execute();
                
                if ($requestsAffected || $appsAffected) {
                    error_log("Auto-cancelled {$requestsAffected} request(s) and rejected {$appsAffected} application(s) for filled role {$app->role_id}");
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in acceptApplication: " . $e->getMessage());
            return false;
        }
    }

    // Reject application
    public function rejectApplication($application_id, $reviewed_by) {
        try {
            $this->db->query("UPDATE role_applications 
                SET status = 'rejected', reviewed_at = NOW(), reviewed_by = :reviewed_by 
                WHERE id = :id");
            $this->db->bind(':id', $application_id);
            $this->db->bind(':reviewed_by', $reviewed_by);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in rejectApplication: " . $e->getMessage());
            return false;
        }
    }

    public function getApplicationsByDrama($drama_id, ?string $status = null) {
        try {
            $query = "SELECT a.*, r.role_name, r.role_type, r.status as role_status, r.drama_id,
                             u.full_name as artist_name, u.email as artist_email, u.phone as artist_phone
                      FROM role_applications a
                      INNER JOIN drama_roles r ON a.role_id = r.id
                      INNER JOIN users u ON a.artist_id = u.id
                      WHERE r.drama_id = :drama_id";

            if ($status !== null) {
                $query .= " AND a.status = :status";
            }

            $query .= " ORDER BY a.applied_at DESC";

            $this->db->query($query);
            $this->db->bind(':drama_id', $drama_id);
            if ($status !== null) {
                $this->db->bind(':status', $status);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getApplicationsByDrama: " . $e->getMessage());
            return [];
        }
    }

    public function publishVacancy($role_id, ?string $message, int $director_id) {
        try {
            $this->db->query("UPDATE drama_roles SET 
                is_published = 1,
                published_at = NOW(),
                published_message = :message,
                published_by = :director_id
                WHERE id = :role_id");

            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':message', $message);
            $this->db->bind(':director_id', $director_id);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in publishVacancy: " . $e->getMessage());
            return false;
        }
    }

    public function unpublishVacancy($role_id) {
        try {
            $this->db->query("UPDATE drama_roles SET 
                is_published = 0,
                published_at = NULL,
                published_message = NULL,
                published_by = NULL
                WHERE id = :role_id");

            $this->db->bind(':role_id', $role_id);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in unpublishVacancy: " . $e->getMessage());
            return false;
        }
    }

    public function getPublishedRolesByDrama($drama_id) {
        try {
            $this->db->query("SELECT r.*, u.full_name as director_name
                             FROM drama_roles r
                             LEFT JOIN users u ON r.published_by = u.id
                             WHERE r.drama_id = :drama_id AND r.is_published = 1
                             ORDER BY r.published_at DESC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getPublishedRolesByDrama: " . $e->getMessage());
            return [];
        }
    }

    public function createRoleRequest($role_id, $artist_id, $director_id, ?string $note = null, ?string $interviewAt = null) {
        try {
            error_log("M_role::createRoleRequest - role_id: $role_id, artist_id: $artist_id, director_id: $director_id");
            $this->db->beginTransaction();

            $this->db->query("SELECT id, status FROM role_requests 
                              WHERE role_id = :role_id AND artist_id = :artist_id AND status IN ('pending','interview')");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $existing = $this->db->single();

            if ($existing) {
                error_log("M_role::createRoleRequest - Updating existing request ID: {$existing->id}");
                $this->db->query("UPDATE role_requests SET 
                    status = 'pending',
                    note = :note,
                    interview_at = :interview_at,
                    requested_at = NOW(),
                    responded_at = NULL
                    WHERE id = :id");

                $this->db->bind(':note', $note);
                $this->db->bind(':interview_at', $interviewAt);
                $this->db->bind(':id', $existing->id);
                $this->db->execute();

                $this->db->commit();
                error_log("M_role::createRoleRequest - Successfully updated request ID: {$existing->id}");
                return $existing->id;
            }

            error_log("M_role::createRoleRequest - Creating new request");
            $this->db->query("INSERT INTO role_requests 
                (role_id, artist_id, director_id, status, note, interview_at)
                VALUES (:role_id, :artist_id, :director_id, 'pending', :note, :interview_at)");

            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':director_id', $director_id);
            $this->db->bind(':note', $note);
            $this->db->bind(':interview_at', $interviewAt);
            $this->db->execute();

            $requestId = $this->db->lastInsertId();
            $this->db->commit();
            error_log("M_role::createRoleRequest - Successfully created request ID: $requestId");
            return $requestId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in createRoleRequest: " . $e->getMessage());
            return false;
        }
    }

    public function getRoleRequestsByDrama($drama_id, ?string $status = null) {
        try {
            error_log("M_role::getRoleRequestsByDrama - drama_id: $drama_id, status filter: " . ($status ?? 'none'));
            
            $query = "SELECT rr.*, r.role_name, r.role_type, r.status as role_status,
                             u.full_name as artist_name, u.email as artist_email, u.phone as artist_phone,
                             du.full_name as director_name
                      FROM role_requests rr
                      INNER JOIN drama_roles r ON rr.role_id = r.id
                      INNER JOIN users u ON rr.artist_id = u.id
                      INNER JOIN users du ON rr.director_id = du.id
                      WHERE r.drama_id = :drama_id";

            if ($status !== null) {
                $query .= " AND rr.status = :status";
            }

            $query .= " ORDER BY rr.requested_at DESC";

            $this->db->query($query);
            $this->db->bind(':drama_id', $drama_id);
            if ($status !== null) {
                $this->db->bind(':status', $status);
            }

            $results = $this->db->resultSet();
            error_log("M_role::getRoleRequestsByDrama - Found " . count($results) . " requests");
            
            if (!empty($results)) {
                foreach ($results as $req) {
                    error_log("  Request ID: {$req->id}, Status: {$req->status}, Artist: {$req->artist_name}, Role: {$req->role_name}");
                }
            }
            
            return $results;
        } catch (Exception $e) {
            error_log("Error in getRoleRequestsByDrama: " . $e->getMessage());
            return [];
        }
    }

    public function getRoleRequestsByRole($role_id, ?string $status = null) {
        try {
            $query = "SELECT rr.*, u.full_name as artist_name, u.email as artist_email, u.phone as artist_phone,
                             du.full_name as director_name
                      FROM role_requests rr
                      INNER JOIN users u ON rr.artist_id = u.id
                      INNER JOIN users du ON rr.director_id = du.id
                      WHERE rr.role_id = :role_id";

            if ($status !== null) {
                $query .= " AND rr.status = :status";
            }

            $query .= " ORDER BY rr.requested_at DESC";

            $this->db->query($query);
            $this->db->bind(':role_id', $role_id);
            if ($status !== null) {
                $this->db->bind(':status', $status);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getRoleRequestsByRole: " . $e->getMessage());
            return [];
        }
    }

    public function getRoleRequestById($request_id) {
        try {
            $this->db->query("SELECT rr.*, r.drama_id, r.role_name, r.positions_available, r.positions_filled,
                                     r.status as role_status, r.drama_id,
                                     u.full_name as artist_name
                              FROM role_requests rr
                              INNER JOIN drama_roles r ON rr.role_id = r.id
                              INNER JOIN users u ON rr.artist_id = u.id
                              WHERE rr.id = :request_id");
            $this->db->bind(':request_id', $request_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getRoleRequestById: " . $e->getMessage());
            return null;
        }
    }

    public function updateRoleRequestStatus($request_id, string $status, ?string $note = null, ?string $interviewAt = null) {
        try {
            $this->db->query("UPDATE role_requests SET 
                status = :status,
                note = COALESCE(:note, note),
                interview_at = :interview_at,
                responded_at = CASE WHEN :status IN ('accepted','rejected','cancelled') THEN NOW() ELSE responded_at END
                WHERE id = :request_id");

            $this->db->bind(':status', $status);
            $this->db->bind(':note', $note);
            $this->db->bind(':interview_at', $interviewAt);
            $this->db->bind(':request_id', $request_id);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateRoleRequestStatus: " . $e->getMessage());
            return false;
        }
    }

    public function assignArtistFromRequest($request_id, int $director_id) {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT rr.*, r.positions_available, r.positions_filled
                              FROM role_requests rr
                              INNER JOIN drama_roles r ON rr.role_id = r.id
                              WHERE rr.id = :request_id FOR UPDATE");
            $this->db->bind(':request_id', $request_id);
            $request = $this->db->single();

            if (!$request || !in_array($request->status, ['pending','interview'], true)) {
                $this->db->rollBack();
                return false;
            }

            if ((int)$request->positions_filled >= (int)$request->positions_available) {
                $this->db->rollBack();
                return false;
            }

            // Create assignment
            $this->db->query("INSERT INTO role_assignments (role_id, artist_id, assigned_by)
                              VALUES (:role_id, :artist_id, :assigned_by)");
            $this->db->bind(':role_id', $request->role_id);
            $this->db->bind(':artist_id', $request->artist_id);
            $this->db->bind(':assigned_by', $director_id);
            $this->db->execute();

            // Update role positions
            $this->db->query("UPDATE drama_roles 
                              SET positions_filled = positions_filled + 1,
                                  status = CASE WHEN positions_filled + 1 >= positions_available THEN 'filled' ELSE status END
                              WHERE id = :role_id");
            $this->db->bind(':role_id', $request->role_id);
            $this->db->execute();

            // Accept this request
            $this->db->query("UPDATE role_requests 
                              SET status = 'accepted', responded_at = NOW()
                              WHERE id = :request_id");
            $this->db->bind(':request_id', $request_id);
            $this->db->execute();

            // Auto-reject other pending requests if role is now full
            $newPositionsFilled = (int)$request->positions_filled + 1;
            $positionsAvailable = (int)$request->positions_available;
            
            if ($newPositionsFilled >= $positionsAvailable) {
                error_log("Role {$request->role_id} is now full. Auto-rejecting other pending requests.");
                
                $this->db->query("UPDATE role_requests 
                                  SET status = 'cancelled', responded_at = NOW()
                                  WHERE role_id = :role_id 
                                  AND status IN ('pending', 'interview')
                                  AND id != :current_request_id");
                $this->db->bind(':role_id', $request->role_id);
                $this->db->bind(':current_request_id', $request_id);
                $affectedRows = $this->db->execute();
                
                if ($affectedRows) {
                    error_log("Auto-cancelled {$affectedRows} pending request(s) for filled role {$request->role_id}");
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in assignArtistFromRequest: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count all published vacancies across all dramas
     */
    public function countPublishedVacancies() {
        try {
            $this->db->query("SELECT COUNT(*) as count 
                             FROM drama_roles 
                             WHERE is_published = 1 
                             AND status != 'filled'");
            $result = $this->db->single();
            return $result ? (int)$result->count : 0;
        } catch (Exception $e) {
            error_log("Error in countPublishedVacancies: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all published vacancies with optional filters
     */
    public function getAllPublishedVacancies($filters = []) {
        try {
            $query = "SELECT r.*, d.drama_name, d.description as drama_description,
                             u.full_name as director_name,
                             (r.positions_available - r.positions_filled) as positions_remaining
                      FROM drama_roles r
                      INNER JOIN dramas d ON r.drama_id = d.id
                      INNER JOIN users u ON d.creator_artist_id = u.id
                      WHERE r.is_published = 1 AND r.status != 'filled'";

            // Exclude roles where the artist is already assigned
            if (!empty($filters['artist_id'])) {
                $query .= " AND r.id NOT IN (
                                SELECT role_id FROM role_assignments WHERE artist_id = :artist_id
                            )";
            }

            if (!empty($filters['role_type'])) {
                $query .= " AND r.role_type = :role_type";
            }

            if (!empty($filters['search'])) {
                $query .= " AND (r.role_name LIKE :search OR r.role_description LIKE :search OR d.drama_name LIKE :search)";
            }

            // Sorting
            $sort = $filters['sort'] ?? 'latest';
            switch ($sort) {
                case 'latest':
                    $query .= " ORDER BY r.published_at DESC";
                    break;
                case 'oldest':
                    $query .= " ORDER BY r.published_at ASC";
                    break;
                case 'salary_high':
                    $query .= " ORDER BY r.salary DESC";
                    break;
                case 'salary_low':
                    $query .= " ORDER BY r.salary ASC";
                    break;
                default:
                    $query .= " ORDER BY r.published_at DESC";
            }

            $this->db->query($query);

            if (!empty($filters['artist_id'])) {
                $this->db->bind(':artist_id', $filters['artist_id']);
            }

            if (!empty($filters['role_type'])) {
                $this->db->bind(':role_type', $filters['role_type']);
            }

            if (!empty($filters['search'])) {
                $this->db->bind(':search', '%' . $filters['search'] . '%');
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAllPublishedVacancies: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Apply for a role (artist submits application)
     */
    public function applyForRole($role_id, $artist_id, $cover_letter = '', $media_links = '') {
        try {
            // Check if role exists and is published
            $this->db->query("SELECT * FROM drama_roles WHERE id = :role_id AND is_published = 1");
            $this->db->bind(':role_id', $role_id);
            $role = $this->db->single();

            if (!$role) {
                return ['success' => false, 'message' => 'This vacancy is no longer available'];
            }

            // Check if role is filled
            if ((int)$role->positions_filled >= (int)$role->positions_available) {
                return ['success' => false, 'message' => 'This role is already filled'];
            }

            // Check if artist already applied
            $this->db->query("SELECT id FROM role_applications 
                             WHERE role_id = :role_id AND artist_id = :artist_id");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $existing = $this->db->single();

            if ($existing) {
                return ['success' => false, 'message' => 'You have already applied for this role'];
            }

            // Check if artist is already assigned to this role
            $this->db->query("SELECT id FROM role_assignments 
                             WHERE role_id = :role_id AND artist_id = :artist_id");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $assigned = $this->db->single();

            if ($assigned) {
                return ['success' => false, 'message' => 'You are already assigned to this role'];
            }

            // Create application
            $this->db->query("INSERT INTO role_applications 
                             (role_id, artist_id, application_message, media_links, status, applied_at)
                             VALUES (:role_id, :artist_id, :application_message, :media_links, 'pending', NOW())");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':application_message', $cover_letter);
            $this->db->bind(':media_links', $media_links);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Application submitted successfully!'];
            } else {
                return ['success' => false, 'message' => 'Failed to submit application'];
            }
        } catch (Exception $e) {
            error_log("Error in applyForRole: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while submitting your application'];
        }
    }

    /**
     * Get role details for application form
     */
    public function getRoleDetailsForApplication($role_id) {
        try {
            $this->db->query("SELECT r.*, d.drama_name 
                             FROM drama_roles r
                             INNER JOIN dramas d ON r.drama_id = d.id
                             WHERE r.id = :role_id AND r.is_published = 1");
            $this->db->bind(':role_id', $role_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getRoleDetailsForApplication: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all applications submitted by an artist
     */
    public function getArtistApplications($artist_id) {
        try {
            $this->db->query("SELECT a.*, r.role_name, r.role_type, r.salary, r.status as role_status,
                             d.drama_name, u.full_name as director_name
                             FROM role_applications a
                             INNER JOIN drama_roles r ON a.role_id = r.id
                             INNER JOIN dramas d ON r.drama_id = d.id
                             INNER JOIN users u ON d.creator_artist_id = u.id
                             WHERE a.artist_id = :artist_id
                             ORDER BY a.applied_at DESC");
            $this->db->bind(':artist_id', $artist_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getArtistApplications: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all role assignments for an artist (roles they are currently cast in)
     */
    public function getAssignmentsByArtist($artist_id) {
        try {
            $this->db->query("SELECT ra.*, 
                             r.role_name, r.role_type, r.role_description, r.salary,
                             d.id as drama_id, d.drama_name, d.description as drama_description,
                             u.full_name as director_name
                             FROM role_assignments ra
                             INNER JOIN drama_roles r ON ra.role_id = r.id
                             INNER JOIN dramas d ON r.drama_id = d.id
                             INNER JOIN users u ON d.creator_artist_id = u.id
                             WHERE ra.artist_id = :artist_id AND ra.status = 'active'
                             ORDER BY ra.assigned_at DESC");
            $this->db->bind(':artist_id', $artist_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAssignmentsByArtist: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get artist's specific role in a drama
     */
    public function getArtistRoleInDrama($artist_id, $drama_id) {
        try {
            $this->db->query("SELECT ra.*, r.role_name, r.role_type, r.role_description, r.salary
                             FROM role_assignments ra
                             INNER JOIN drama_roles r ON ra.role_id = r.id
                             WHERE ra.artist_id = :artist_id 
                             AND r.drama_id = :drama_id 
                             AND ra.status = 'active'
                             LIMIT 1");
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getArtistRoleInDrama: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Remove artist assignment from role
     * @param int $assignment_id Assignment ID to remove
     * @return bool Success status
     */
    public function removeAssignment($assignment_id) {
        try {
            $this->db->beginTransaction();
            
            // First, get the role_id before deleting
            $this->db->query("SELECT role_id FROM role_assignments WHERE id = :id");
            $this->db->bind(':id', $assignment_id);
            $assignment = $this->db->single();
            
            if (!$assignment) {
                $this->db->rollBack();
                return false;
            }
            
            // Delete the assignment
            $this->db->query("DELETE FROM role_assignments WHERE id = :id");
            $this->db->bind(':id', $assignment_id);
            $this->db->execute();
            
            // Decrement positions_filled counter
            $this->db->query("UPDATE drama_roles SET positions_filled = positions_filled - 1 
                             WHERE id = :role_id AND positions_filled > 0");
            $this->db->bind(':role_id', $assignment->role_id);
            $this->db->execute();
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in removeAssignment: " . $e->getMessage());
            return false;
        }
    }
}

?>