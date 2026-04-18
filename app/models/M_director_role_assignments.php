<?php

class M_director_role_assignments
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getAssignmentsByRole($role_id)
    {
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
            error_log("Error in M_director_role_assignments::getAssignmentsByRole: " . $e->getMessage());
            return [];
        }
    }

    public function getAssignmentById($assignment_id)
    {
        try {
            $this->db->query("SELECT a.*,
                             r.drama_id,
                             r.role_name,
                             u.full_name as artist_name,
                             u.email as artist_email
                             FROM role_assignments a
                             INNER JOIN drama_roles r ON a.role_id = r.id
                             INNER JOIN users u ON a.artist_id = u.id
                             WHERE a.id = :assignment_id
                             LIMIT 1");
            $this->db->bind(':assignment_id', $assignment_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in M_director_role_assignments::getAssignmentById: " . $e->getMessage());
            return null;
        }
    }

    public function getAssignmentsByArtist($artist_id)
    {
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
            error_log("Error in M_director_role_assignments::getAssignmentsByArtist: " . $e->getMessage());
            return [];
        }
    }

    public function getArtistRoleInDrama($artist_id, $drama_id)
    {
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
            error_log("Error in M_director_role_assignments::getArtistRoleInDrama: " . $e->getMessage());
            return null;
        }
    }

    public function removeAssignment($assignment_id)
    {
        try {
            $this->db->beginTransaction();

            $this->db->query("SELECT role_id FROM role_assignments WHERE id = :id");
            $this->db->bind(':id', $assignment_id);
            $assignment = $this->db->single();

            if (!$assignment) {
                $this->db->rollBack();
                return false;
            }

            $this->db->query("DELETE FROM role_assignments WHERE id = :id");
            $this->db->bind(':id', $assignment_id);
            $this->db->execute();

            $this->db->query("UPDATE drama_roles
                             SET positions_filled = positions_filled - 1,
                                 status = CASE
                                     WHEN status = 'filled' AND (positions_filled - 1) < positions_available THEN 'open'
                                     ELSE status
                                 END
                             WHERE id = :role_id AND positions_filled > 0");
            $this->db->bind(':role_id', $assignment->role_id);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error in M_director_role_assignments::removeAssignment: " . $e->getMessage());
            return false;
        }
    }
}
