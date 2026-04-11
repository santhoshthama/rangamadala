<?php

class M_service_schedule
{
    private $db;
    private $tableExistsCache = [];

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all service schedules for a drama
     */
    public function getSchedulesByDrama($drama_id)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return [];
        }

        $selectColumns = $this->getSelectColumns($table);
        $this->db->query("
            SELECT {$selectColumns} FROM {$table} 
            WHERE drama_id = :drama_id 
            ORDER BY scheduled_date ASC, start_time ASC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get a specific schedule by ID
     */
    public function getScheduleById($id)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return null;
        }

        $selectColumns = $this->getSelectColumns($table);
        $this->db->query("SELECT {$selectColumns} FROM {$table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get upcoming schedules for a drama (from today onwards)
     */
    public function getUpcomingSchedules($drama_id)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return [];
        }

        $selectColumns = $this->getSelectColumns($table);
        $this->db->query("
            SELECT {$selectColumns} FROM {$table} 
            WHERE drama_id = :drama_id AND scheduled_date >= CURDATE()
            ORDER BY scheduled_date ASC, start_time ASC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Create a new service schedule
     */
    public function createSchedule($data)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return false;
        }

        if ($table === 'service_schedules') {
            $this->db->query("
                INSERT INTO service_schedules (
                    drama_id, service_request_id, service_name, scheduled_date, start_time, end_time,
                    venue, assigned_to, status, notes, created_by
                ) VALUES (
                    :drama_id, :service_request_id, :service_name, :scheduled_date, :start_time, :end_time,
                    :venue, :assigned_to, :status, :notes, :created_by
                )
            ");

            $this->db->bind(':service_request_id', $data['service_request_id'] ?? null);
            $this->db->bind(':service_name', $data['service_name']);
            $this->db->bind(':assigned_to', $data['assigned_to'] ?? null);
        } else {
            // Backward compatibility for legacy drama_schedules table
            $this->db->query("
                INSERT INTO drama_schedules (
                    drama_id, event_type, event_title, event_description, scheduled_date, start_time, end_time,
                    venue, status, notes, created_by
                ) VALUES (
                    :drama_id, :event_type, :event_title, :event_description, :scheduled_date, :start_time, :end_time,
                    :venue, :status, :notes, :created_by
                )
            ");

            $this->db->bind(':event_type', 'meeting');
            $this->db->bind(':event_title', $data['service_name']);
            $this->db->bind(':event_description', $data['notes'] ?? null);
        }

        $this->db->bind(':drama_id', $data['drama_id']);
        $this->db->bind(':scheduled_date', $data['scheduled_date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        $this->db->bind(':venue', $data['venue'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'scheduled');
        $this->db->bind(':notes', $data['notes'] ?? null);
        $this->db->bind(':created_by', $data['created_by']);

        return $this->db->execute();
    }

    /**
     * Update schedule status
     */
    public function updateStatus($id, $status)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return false;
        }

        $this->db->query("
            UPDATE {$table} 
            SET status = :status, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    /**
     * Update schedule details
     */
    public function updateSchedule($id, $data)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return false;
        }

        $this->db->query("
            UPDATE {$table} SET
                scheduled_date = :scheduled_date,
                start_time = :start_time,
                end_time = :end_time,
                venue = :venue,
                status = :status,
                notes = :notes,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");

        $this->db->bind(':id', $id);
        $this->db->bind(':scheduled_date', $data['scheduled_date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        $this->db->bind(':venue', $data['venue'] ?? null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes'] ?? null);

        return $this->db->execute();
    }

    /**
     * Delete a schedule
     */
    public function deleteSchedule($id)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return false;
        }

        $this->db->query("DELETE FROM {$table} WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get schedule count for a drama
     */
    public function getScheduleCount($drama_id)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return 0;
        }

        $this->db->query("
            SELECT COUNT(*) as count FROM {$table} WHERE drama_id = :drama_id
        ");
        $this->db->bind(':drama_id', $drama_id);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    /**
     * Get schedules by status
     */
    public function getSchedulesByStatus($drama_id, $status)
    {
        $table = $this->getScheduleTable();
        if (!$table) {
            return [];
        }

        $selectColumns = $this->getSelectColumns($table);
        $this->db->query("
            SELECT {$selectColumns} FROM {$table} 
            WHERE drama_id = :drama_id AND status = :status
            ORDER BY scheduled_date ASC, start_time ASC
        ");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':status', $status);
        return $this->db->resultSet() ?: [];
    }

    private function getScheduleTable(): ?string
    {
        if ($this->tableExists('service_schedules')) {
            return 'service_schedules';
        }

        if ($this->tableExists('drama_schedules')) {
            return 'drama_schedules';
        }

        return null;
    }

    private function getSelectColumns(string $table): string
    {
        if ($table === 'service_schedules') {
            return '*';
        }

        return "
            id,
            drama_id,
            NULL AS service_request_id,
            event_title AS service_name,
            scheduled_date,
            start_time,
            end_time,
            venue,
            NULL AS assigned_to,
            status,
            notes,
            created_by,
            created_at,
            updated_at
        ";
    }

    private function tableExists(string $tableName): bool
    {
        if (isset($this->tableExistsCache[$tableName])) {
            return $this->tableExistsCache[$tableName];
        }

        $this->db->query("SELECT COUNT(*) AS cnt
                          FROM information_schema.tables
                          WHERE table_schema = DATABASE() AND table_name = :table_name");
        $this->db->bind(':table_name', $tableName);
        $row = $this->db->single();

        $exists = $row && isset($row->cnt) && (int)$row->cnt > 0;
        $this->tableExistsCache[$tableName] = $exists;
        return $exists;
    }
}

?>
