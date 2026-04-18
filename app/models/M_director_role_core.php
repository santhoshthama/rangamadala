<?php

class M_director_role_core
{
    protected $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createRole($data)
    {
        try {
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

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (Exception $e) {
            error_log("Error in M_director_role_core::createRole: " . $e->getMessage());
            return false;
        }
    }

    public function getRolesByDrama($drama_id)
    {
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
            error_log("Error in M_director_role_core::getRolesByDrama: " . $e->getMessage());
            return [];
        }
    }

    public function getRoleById($role_id)
    {
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
            error_log("Error in M_director_role_core::getRoleById: " . $e->getMessage());
            return null;
        }
    }

    public function updateRole($role_id, $data)
    {
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
            error_log("Error in M_director_role_core::updateRole: " . $e->getMessage());
            return false;
        }
    }

    public function deleteRole($role_id)
    {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM role_assignments WHERE role_id = :role_id");
            $this->db->bind(':role_id', $role_id);
            $result = $this->db->single();

            if ($result && $result->count > 0) {
                $this->db->query("UPDATE drama_roles SET status = 'closed' WHERE id = :role_id");
                $this->db->bind(':role_id', $role_id);
                return $this->db->execute();
            }

            $this->db->query("DELETE FROM drama_roles WHERE id = :role_id");
            $this->db->bind(':role_id', $role_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in M_director_role_core::deleteRole: " . $e->getMessage());
            return false;
        }
    }

    public function getRoleStats($drama_id)
    {
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
            error_log("Error in M_director_role_core::getRoleStats: " . $e->getMessage());
            return null;
        }
    }

    public function publishVacancy($role_id, ?string $message, int $director_id)
    {
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
            error_log("Error in M_director_role_core::publishVacancy: " . $e->getMessage());
            return false;
        }
    }

    public function unpublishVacancy($role_id)
    {
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
            error_log("Error in M_director_role_core::unpublishVacancy: " . $e->getMessage());
            return false;
        }
    }

    public function getPublishedRolesByDrama($drama_id)
    {
        try {
            $this->db->query("SELECT r.*, u.full_name as director_name
                             FROM drama_roles r
                             LEFT JOIN users u ON r.published_by = u.id
                             WHERE r.drama_id = :drama_id AND r.is_published = 1
                             ORDER BY r.published_at DESC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in M_director_role_core::getPublishedRolesByDrama: " . $e->getMessage());
            return [];
        }
    }

    public function countPublishedVacancies()
    {
        try {
            $this->db->query("SELECT COUNT(*) as count
                             FROM drama_roles
                             WHERE is_published = 1
                             AND status != 'filled'");
            $result = $this->db->single();
            return $result ? (int)$result->count : 0;
        } catch (Exception $e) {
            error_log("Error in M_director_role_core::countPublishedVacancies: " . $e->getMessage());
            return 0;
        }
    }

    public function getAllPublishedVacancies($filters = [])
    {
        try {
            $query = "SELECT r.*, d.drama_name, d.description as drama_description,
                             u.full_name as director_name,
                             (r.positions_available - r.positions_filled) as positions_remaining
                      FROM drama_roles r
                      INNER JOIN dramas d ON r.drama_id = d.id
                      INNER JOIN users u ON d.creator_artist_id = u.id
                      WHERE r.is_published = 1 AND r.status != 'filled'";

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
            error_log("Error in M_director_role_core::getAllPublishedVacancies: " . $e->getMessage());
            return [];
        }
    }
}
