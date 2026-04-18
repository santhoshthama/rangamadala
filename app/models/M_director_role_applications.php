<?php

class M_director_role_applications
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getApplicationsByRole($role_id)
    {
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
            error_log("Error in M_director_role_applications::getApplicationsByRole: " . $e->getMessage());
            return [];
        }
    }

    public function getApplicationById($application_id)
    {
        try {
            $this->db->query("SELECT a.*, r.drama_id, r.role_name, r.status as role_status
                              FROM role_applications a
                              INNER JOIN drama_roles r ON a.role_id = r.id
                              WHERE a.id = :application_id");
            $this->db->bind(':application_id', $application_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('Error in M_director_role_applications::getApplicationById: ' . $e->getMessage());
            return null;
        }
    }

    public function markApplicationProfileViewed(int $application_id, int $director_id)
    {
        try {
            $this->db->query("UPDATE role_applications
                              SET profile_viewed_at = NOW(), profile_viewed_by = :director_id
                              WHERE id = :application_id AND status = 'pending'");
            $this->db->bind(':director_id', $director_id);
            $this->db->bind(':application_id', $application_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_director_role_applications::markApplicationProfileViewed: ' . $e->getMessage());
            return false;
        }
    }

    public function scheduleApplicationInterview(int $application_id, string $interviewAt, int $director_id, ?string $notes = null)
    {
        try {
            $this->db->query("SELECT status, profile_viewed_by FROM role_applications WHERE id = :application_id");
            $this->db->bind(':application_id', $application_id);
            $application = $this->db->single();

            if (!$application || strtolower($application->status) !== 'pending') {
                return false;
            }

            if ((int)($application->profile_viewed_by ?? 0) !== $director_id) {
                return false;
            }

            $this->db->query("UPDATE role_applications SET
                                interview_at = :interview_at,
                                interview_notes = :notes,
                                interview_status = 'pending',
                                interview_scheduled_at = NOW(),
                                interview_scheduled_by = :director_id,
                                interview_confirmation_status = 'pending',
                                interview_confirmation_note = NULL,
                                interview_confirmed_at = NULL,
                                interview_confirmation_seen_at = NULL
                              WHERE id = :application_id");
            $this->db->bind(':interview_at', $interviewAt);
            $this->db->bind(':notes', $notes);
            $this->db->bind(':director_id', $director_id);
            $this->db->bind(':application_id', $application_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_director_role_applications::scheduleApplicationInterview: ' . $e->getMessage());
            return false;
        }
    }

    public function getPendingApplications($drama_id)
    {
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
            error_log("Error in M_director_role_applications::getPendingApplications: " . $e->getMessage());
            return [];
        }
    }

    public function acceptApplication($application_id, $reviewed_by)
    {
        try {
            $this->db->beginTransaction();

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

            if (strtolower($app->status ?? '') !== 'pending') {
                $this->db->rollBack();
                return false;
            }

            if ((int)$app->positions_filled >= (int)$app->positions_available) {
                $this->db->rollBack();
                return false;
            }

            $this->db->query("UPDATE role_applications
                SET status = 'accepted', reviewed_at = NOW(), reviewed_by = :reviewed_by, interview_status = 'completed'
                WHERE id = :id");
            $this->db->bind(':id', $application_id);
            $this->db->bind(':reviewed_by', $reviewed_by);
            $this->db->execute();

            $this->db->query("INSERT INTO role_assignments
                (role_id, artist_id, assigned_by)
                VALUES (:role_id, :artist_id, :assigned_by)");
            $this->db->bind(':role_id', $app->role_id);
            $this->db->bind(':artist_id', $app->artist_id);
            $this->db->bind(':assigned_by', $reviewed_by);
            $this->db->execute();

            $this->db->query("UPDATE drama_roles
                SET positions_filled = positions_filled + 1
                WHERE id = :role_id");
            $this->db->bind(':role_id', $app->role_id);
            $this->db->execute();

            $this->db->query("UPDATE drama_roles
                SET status = 'filled'
                WHERE id = :role_id AND positions_filled >= positions_available");
            $this->db->bind(':role_id', $app->role_id);
            $this->db->execute();

            $newPositionsFilled = (int)$app->positions_filled + 1;
            $positionsAvailable = (int)$app->positions_available;

            if ($newPositionsFilled >= $positionsAvailable) {
                $this->db->query("UPDATE role_requests
                                  SET status = 'cancelled', responded_at = NOW()
                                  WHERE role_id = :role_id
                                  AND status IN ('pending', 'interview')");
                $this->db->bind(':role_id', $app->role_id);
                $this->db->execute();

                $this->db->query("UPDATE role_applications
                                  SET status = 'rejected', reviewed_at = NOW(), reviewed_by = :reviewed_by
                                  WHERE role_id = :role_id
                                  AND status = 'pending'
                                  AND id != :current_app_id");
                $this->db->bind(':role_id', $app->role_id);
                $this->db->bind(':reviewed_by', $reviewed_by);
                $this->db->bind(':current_app_id', $application_id);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in M_director_role_applications::acceptApplication: " . $e->getMessage());
            return false;
        }
    }

    public function rejectApplication($application_id, $reviewed_by)
    {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT status FROM role_applications WHERE id = :id FOR UPDATE");
            $this->db->bind(':id', $application_id);
            $application = $this->db->single();

            if (!$application || strtolower($application->status ?? '') !== 'pending') {
                $this->db->rollBack();
                return false;
            }

            $this->db->query("UPDATE role_applications
                SET status = 'rejected', reviewed_at = NOW(), reviewed_by = :reviewed_by
                WHERE id = :id");
            $this->db->bind(':id', $application_id);
            $this->db->bind(':reviewed_by', $reviewed_by);
            $result = $this->db->execute();
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in M_director_role_applications::rejectApplication: " . $e->getMessage());
            return false;
        }
    }

    public function getApplicationForArtist(int $application_id, int $artist_id)
    {
        try {
            $this->db->query("SELECT * FROM role_applications WHERE id = :application_id AND artist_id = :artist_id");
            $this->db->bind(':application_id', $application_id);
            $this->db->bind(':artist_id', $artist_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log('Error in M_director_role_applications::getApplicationForArtist: ' . $e->getMessage());
            return null;
        }
    }

    public function artistRespondInterview(int $application_id, int $artist_id, string $response, ?string $note = null)
    {
        $response = strtolower($response);
        if (!in_array($response, ['confirm', 'decline'], true)) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT status, interview_at, interview_confirmation_status
                              FROM role_applications
                              WHERE id = :application_id AND artist_id = :artist_id FOR UPDATE");
            $this->db->bind(':application_id', $application_id);
            $this->db->bind(':artist_id', $artist_id);
            $application = $this->db->single();

            if (!$application) {
                $this->db->rollBack();
                return false;
            }

            if (empty($application->interview_at) || strtolower($application->status ?? '') !== 'pending') {
                $this->db->rollBack();
                return false;
            }

            $newStatus = $response === 'confirm' ? 'confirmed' : 'declined';

            $this->db->query("UPDATE role_applications SET
                                interview_confirmation_status = :status,
                                interview_confirmation_note = :note,
                                interview_confirmed_at = NOW(),
                                interview_confirmation_seen_at = NULL
                              WHERE id = :application_id");
            $this->db->bind(':status', $newStatus);
            $this->db->bind(':note', $note);
            $this->db->bind(':application_id', $application_id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Error in M_director_role_applications::artistRespondInterview: ' . $e->getMessage());
            return false;
        }
    }

    public function markInterviewConfirmationSeen(int $application_id, int $director_id)
    {
        try {
            $this->db->query("UPDATE role_applications
                              SET interview_confirmation_seen_at = NOW()
                              WHERE id = :application_id");
            $this->db->bind(':application_id', $application_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Error in M_director_role_applications::markInterviewConfirmationSeen: ' . $e->getMessage());
            return false;
        }
    }

    public function getApplicationsByDrama($drama_id, ?string $status = null)
    {
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
            error_log("Error in M_director_role_applications::getApplicationsByDrama: " . $e->getMessage());
            return [];
        }
    }

    public function applyForRole($role_id, $artist_id, $cover_letter = '', $media_links = '')
    {
        try {
            $this->db->query("SELECT * FROM drama_roles WHERE id = :role_id AND is_published = 1");
            $this->db->bind(':role_id', $role_id);
            $role = $this->db->single();

            if (!$role) {
                return ['success' => false, 'message' => 'This vacancy is no longer available'];
            }

            if ((int)$role->positions_filled >= (int)$role->positions_available) {
                return ['success' => false, 'message' => 'This role is already filled'];
            }

            $this->db->query("SELECT id FROM role_applications
                             WHERE role_id = :role_id AND artist_id = :artist_id");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $existing = $this->db->single();

            if ($existing) {
                return ['success' => false, 'message' => 'You have already applied for this role'];
            }

            $this->db->query("SELECT id FROM role_assignments
                             WHERE role_id = :role_id AND artist_id = :artist_id");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $assigned = $this->db->single();

            if ($assigned) {
                return ['success' => false, 'message' => 'You are already assigned to this role'];
            }

            $this->db->query("INSERT INTO role_applications
                             (role_id, artist_id, application_message, media_links, status, applied_at)
                             VALUES (:role_id, :artist_id, :application_message, :media_links, 'pending', NOW())");
            $this->db->bind(':role_id', $role_id);
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':application_message', $cover_letter);
            $this->db->bind(':media_links', $media_links);

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Application submitted successfully!'];
            }

            return ['success' => false, 'message' => 'Failed to submit application'];
        } catch (Exception $e) {
            error_log("Error in M_director_role_applications::applyForRole: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while submitting your application'];
        }
    }

    public function getRoleDetailsForApplication($role_id)
    {
        try {
            $this->db->query("SELECT r.*, d.drama_name
                             FROM drama_roles r
                             INNER JOIN dramas d ON r.drama_id = d.id
                             WHERE r.id = :role_id AND r.is_published = 1");
            $this->db->bind(':role_id', $role_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in M_director_role_applications::getRoleDetailsForApplication: " . $e->getMessage());
            return null;
        }
    }

    public function getArtistApplications($artist_id)
    {
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
            error_log("Error in M_director_role_applications::getArtistApplications: " . $e->getMessage());
            return [];
        }
    }
}
