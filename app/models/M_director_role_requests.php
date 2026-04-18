<?php

class M_director_role_requests
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createRoleRequest($role_id, $artist_id, $director_id, ?string $note = null, ?string $interviewAt = null)
    {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT id, status FROM role_requests
                              WHERE role_id = :role_id AND artist_id = :artist_id AND status IN ('pending','interview')");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $existing = $this->db->single();

            if ($existing) {
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
                return $existing->id;
            }

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
            return $requestId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in M_director_role_requests::createRoleRequest: " . $e->getMessage());
            return false;
        }
    }

    public function getRoleRequestsByDrama($drama_id, ?string $status = null)
    {
        try {
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

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in M_director_role_requests::getRoleRequestsByDrama: " . $e->getMessage());
            return [];
        }
    }

    public function getRoleRequestsByRole($role_id, ?string $status = null)
    {
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
            error_log("Error in M_director_role_requests::getRoleRequestsByRole: " . $e->getMessage());
            return [];
        }
    }

    public function getRoleRequestById($request_id)
    {
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
            error_log("Error in M_director_role_requests::getRoleRequestById: " . $e->getMessage());
            return null;
        }
    }

    public function updateRoleRequestStatus($request_id, string $status, ?string $note = null, ?string $interviewAt = null)
    {
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
            error_log("Error in M_director_role_requests::updateRoleRequestStatus: " . $e->getMessage());
            return false;
        }
    }

    public function cancelRoleRequestByDirector(int $request_id, int $director_id, int $drama_id): bool
    {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT rr.id, rr.status
                              FROM role_requests rr
                              INNER JOIN drama_roles r ON rr.role_id = r.id
                              WHERE rr.id = :request_id
                                AND rr.director_id = :director_id
                                AND r.drama_id = :drama_id
                              FOR UPDATE");
            $this->db->bind(':request_id', $request_id);
            $this->db->bind(':director_id', $director_id);
            $this->db->bind(':drama_id', $drama_id);
            $request = $this->db->single();

            if (!$request) {
                $this->db->rollBack();
                return false;
            }

            $status = strtolower((string)($request->status ?? ''));
            if (!in_array($status, ['pending', 'interview'], true)) {
                $this->db->rollBack();
                return false;
            }

            $this->db->query("UPDATE role_requests
                              SET status = 'cancelled', responded_at = NOW()
                              WHERE id = :request_id");
            $this->db->bind(':request_id', $request_id);
            $ok = $this->db->execute();

            if (!$ok) {
                $this->db->rollBack();
                return false;
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in M_director_role_requests::cancelRoleRequestByDirector: " . $e->getMessage());
            return false;
        }
    }

    public function assignArtistFromRequest($request_id, int $director_id)
    {
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

            $this->db->query("INSERT INTO role_assignments (role_id, artist_id, assigned_by)
                              VALUES (:role_id, :artist_id, :assigned_by)");
            $this->db->bind(':role_id', $request->role_id);
            $this->db->bind(':artist_id', $request->artist_id);
            $this->db->bind(':assigned_by', $director_id);
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

            $newPositionsFilled = (int)$request->positions_filled + 1;
            $positionsAvailable = (int)$request->positions_available;

            if ($newPositionsFilled >= $positionsAvailable) {
                $this->db->query("UPDATE role_requests
                                  SET status = 'cancelled', responded_at = NOW()
                                  WHERE role_id = :role_id
                                  AND status IN ('pending', 'interview')
                                  AND id != :current_request_id");
                $this->db->bind(':role_id', $request->role_id);
                $this->db->bind(':current_request_id', $request_id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in M_director_role_requests::assignArtistFromRequest: " . $e->getMessage());
            return false;
        }
    }
}
