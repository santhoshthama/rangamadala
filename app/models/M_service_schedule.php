<?php

class M_service_schedule
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all service schedules for a drama
     */
    public function getSchedulesByDrama($drama_id)
    {
        $this->db->query("
            SELECT * FROM service_schedules 
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
        $this->db->query("SELECT * FROM service_schedules WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get upcoming schedules for a drama (from today onwards)
     */
    public function getUpcomingSchedules($drama_id)
    {
        $this->db->query("
            SELECT * FROM service_schedules 
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
        $this->db->query("
            INSERT INTO service_schedules (
                drama_id, service_request_id, service_name, scheduled_date, start_time, end_time,
                venue, assigned_to, status, notes, created_by
            ) VALUES (
                :drama_id, :service_request_id, :service_name, :scheduled_date, :start_time, :end_time,
                :venue, :assigned_to, :status, :notes, :created_by
            )
        ");

        $this->db->bind(':drama_id', $data['drama_id']);
        $this->db->bind(':service_request_id', $data['service_request_id'] ?? null);
        $this->db->bind(':service_name', $data['service_name']);
        $this->db->bind(':scheduled_date', $data['scheduled_date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        $this->db->bind(':venue', $data['venue'] ?? null);
        $this->db->bind(':assigned_to', $data['assigned_to'] ?? null);
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
        $this->db->query("
            UPDATE service_schedules 
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
        $this->db->query("
            UPDATE service_schedules SET
                scheduled_date = :scheduled_date,
                start_time = :start_time,
                end_time = :end_time,
                venue = :venue,
                assigned_to = :assigned_to,
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
        $this->db->bind(':assigned_to', $data['assigned_to'] ?? null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':notes', $data['notes'] ?? null);

        return $this->db->execute();
    }

    /**
     * Delete a schedule
     */
    public function deleteSchedule($id)
    {
        $this->db->query("DELETE FROM service_schedules WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get schedule count for a drama
     */
    public function getScheduleCount($drama_id)
    {
        $this->db->query("
            SELECT COUNT(*) as count FROM service_schedules WHERE drama_id = :drama_id
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
        $this->db->query("
            SELECT * FROM service_schedules 
            WHERE drama_id = :drama_id AND status = :status
            ORDER BY scheduled_date ASC, start_time ASC
        ");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':status', $status);
        return $this->db->resultSet() ?: [];
    }
}

?>
