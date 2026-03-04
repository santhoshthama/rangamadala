<?php

class M_schedule {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    // ─── CREATE ─────────────────────────────────────────────────────────

    /**
     * Create a new schedule event
     */
    public function createEvent($data) {
        try {
            $this->db->query("INSERT INTO drama_schedules 
                (drama_id, event_type, event_title, event_description, scheduled_date, 
                 start_time, end_time, venue, role_id, status, participants, notes, created_by)
                VALUES 
                (:drama_id, :event_type, :event_title, :event_description, :scheduled_date,
                 :start_time, :end_time, :venue, :role_id, :status, :participants, :notes, :created_by)");

            $this->db->bind(':drama_id', $data['drama_id']);
            $this->db->bind(':event_type', $data['event_type']);
            $this->db->bind(':event_title', $data['event_title']);
            $this->db->bind(':event_description', $data['event_description'] ?? null);
            $this->db->bind(':scheduled_date', $data['scheduled_date']);
            $this->db->bind(':start_time', $data['start_time']);
            $this->db->bind(':end_time', $data['end_time']);
            $this->db->bind(':venue', $data['venue']);
            $this->db->bind(':role_id', $data['role_id'] ?? null);
            $this->db->bind(':status', $data['status'] ?? 'scheduled');
            $this->db->bind(':participants', $data['participants'] ?? null);
            $this->db->bind(':notes', $data['notes'] ?? null);
            $this->db->bind(':created_by', $data['created_by']);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in createEvent: " . $e->getMessage());
            return false;
        }
    }

    // ─── READ ───────────────────────────────────────────────────────────

    /**
     * Get a single event by ID
     */
    public function getEventById($event_id) {
        try {
            $this->db->query("SELECT s.*, d.drama_name, r.role_name,
                             u.full_name as creator_name
                             FROM drama_schedules s
                             LEFT JOIN dramas d ON s.drama_id = d.id
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             LEFT JOIN users u ON s.created_by = u.id
                             WHERE s.id = :id");
            $this->db->bind(':id', $event_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getEventById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all events for a drama
     */
    public function getEventsByDrama($drama_id) {
        try {
            $this->db->query("SELECT s.*, r.role_name
                             FROM drama_schedules s
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             WHERE s.drama_id = :drama_id
                             ORDER BY s.scheduled_date ASC, s.start_time ASC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getEventsByDrama: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get upcoming events for a drama (today or future, not cancelled)
     */
    public function getUpcomingEvents($drama_id) {
        try {
            $this->db->query("SELECT s.*, r.role_name
                             FROM drama_schedules s
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             WHERE s.drama_id = :drama_id
                               AND s.scheduled_date >= CURDATE()
                               AND s.status != 'cancelled'
                             ORDER BY s.scheduled_date ASC, s.start_time ASC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getUpcomingEvents: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get past events for a drama (before today, or completed/cancelled)
     */
    public function getPastEvents($drama_id) {
        try {
            $this->db->query("SELECT s.*, r.role_name
                             FROM drama_schedules s
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             WHERE s.drama_id = :drama_id
                               AND (s.scheduled_date < CURDATE() OR s.status IN ('completed', 'cancelled'))
                             ORDER BY s.scheduled_date DESC, s.start_time DESC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getPastEvents: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all events for a drama on a specific date (for date availability check)
     */
    public function getEventsByDate($drama_id, $date) {
        try {
            $this->db->query("SELECT s.*, r.role_name
                             FROM drama_schedules s
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             WHERE s.drama_id = :drama_id
                               AND s.scheduled_date = :scheduled_date
                               AND s.status NOT IN ('cancelled')
                             ORDER BY s.start_time ASC");
            $this->db->bind(':drama_id', $drama_id);
            $this->db->bind(':scheduled_date', $date);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getEventsByDate: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if a specific time slot is available on a date
     * Returns true if the time slot is free, false if there's a conflict
     */
    public function isTimeSlotAvailable($drama_id, $date, $start_time, $end_time, $exclude_id = null) {
        try {
            $sql = "SELECT COUNT(*) as conflict_count
                    FROM drama_schedules
                    WHERE drama_id = :drama_id
                      AND scheduled_date = :scheduled_date
                      AND status NOT IN ('cancelled')
                      AND (
                          (start_time < :end_time AND end_time > :start_time)
                      )";
            
            if ($exclude_id) {
                $sql .= " AND id != :exclude_id";
            }

            $this->db->query($sql);
            $this->db->bind(':drama_id', $drama_id);
            $this->db->bind(':scheduled_date', $date);
            $this->db->bind(':start_time', $start_time);
            $this->db->bind(':end_time', $end_time);

            if ($exclude_id) {
                $this->db->bind(':exclude_id', $exclude_id);
            }

            $result = $this->db->single();
            return $result && (int)$result->conflict_count === 0;
        } catch (Exception $e) {
            error_log("Error in isTimeSlotAvailable: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get events for a month (for calendar view)
     */
    public function getEventsByMonth($drama_id, $year, $month) {
        try {
            $startDate = sprintf('%04d-%02d-01', $year, $month);
            $endDate = date('Y-m-t', strtotime($startDate));

            $this->db->query("SELECT s.*, r.role_name
                             FROM drama_schedules s
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             WHERE s.drama_id = :drama_id
                               AND s.scheduled_date BETWEEN :start_date AND :end_date
                               AND s.status != 'cancelled'
                             ORDER BY s.scheduled_date ASC, s.start_time ASC");
            $this->db->bind(':drama_id', $drama_id);
            $this->db->bind(':start_date', $startDate);
            $this->db->bind(':end_date', $endDate);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getEventsByMonth: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get interview events from role_applications for calendar integration
     */
    public function getInterviewsFromApplications($drama_id) {
        try {
            $this->db->query("SELECT ra.id as application_id, ra.interview_at, ra.interview_status,
                                     ra.interview_notes, ra.interview_confirmation_status,
                                     r.role_name, r.id as role_id,
                                     u.full_name as artist_name
                             FROM role_applications ra
                             INNER JOIN drama_roles r ON ra.role_id = r.id
                             INNER JOIN users u ON ra.artist_id = u.id
                             WHERE r.drama_id = :drama_id
                               AND ra.interview_at IS NOT NULL
                               AND ra.status = 'pending'
                             ORDER BY ra.interview_at ASC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getInterviewsFromApplications: " . $e->getMessage());
            return [];
        }
    }

    // ─── UPDATE ─────────────────────────────────────────────────────────

    /**
     * Update an existing schedule event
     */
    public function updateEvent($event_id, $data) {
        try {
            $this->db->query("UPDATE drama_schedules SET
                event_type = :event_type,
                event_title = :event_title,
                event_description = :event_description,
                scheduled_date = :scheduled_date,
                start_time = :start_time,
                end_time = :end_time,
                venue = :venue,
                role_id = :role_id,
                participants = :participants,
                notes = :notes
                WHERE id = :id");

            $this->db->bind(':event_type', $data['event_type']);
            $this->db->bind(':event_title', $data['event_title']);
            $this->db->bind(':event_description', $data['event_description'] ?? null);
            $this->db->bind(':scheduled_date', $data['scheduled_date']);
            $this->db->bind(':start_time', $data['start_time']);
            $this->db->bind(':end_time', $data['end_time']);
            $this->db->bind(':venue', $data['venue']);
            $this->db->bind(':role_id', $data['role_id'] ?? null);
            $this->db->bind(':participants', $data['participants'] ?? null);
            $this->db->bind(':notes', $data['notes'] ?? null);
            $this->db->bind(':id', $event_id);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateEvent: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update event status
     */
    public function updateEventStatus($event_id, $status) {
        try {
            $allowed = ['scheduled', 'confirmed', 'completed', 'cancelled'];
            if (!in_array($status, $allowed, true)) {
                return false;
            }

            $this->db->query("UPDATE drama_schedules SET status = :status WHERE id = :id");
            $this->db->bind(':status', $status);
            $this->db->bind(':id', $event_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateEventStatus: " . $e->getMessage());
            return false;
        }
    }

    // ─── DELETE ─────────────────────────────────────────────────────────

    /**
     * Delete an event (hard delete)
     */
    public function deleteEvent($event_id) {
        try {
            $this->db->query("DELETE FROM drama_schedules WHERE id = :id");
            $this->db->bind(':id', $event_id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in deleteEvent: " . $e->getMessage());
            return false;
        }
    }

    // ─── ARTIST VIEW METHODS ────────────────────────────────────────────

    /**
     * Get upcoming rehearsals, performances & meetings for a drama (artist view)
     * Excludes interviews (private to director) and cancelled events
     */
    public function getUpcomingSchedulesForArtist($drama_id) {
        try {
            $this->db->query("SELECT s.*, r.role_name
                             FROM drama_schedules s
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             WHERE s.drama_id = :drama_id
                               AND s.scheduled_date >= CURDATE()
                               AND s.status NOT IN ('cancelled')
                               AND s.event_type IN ('rehearsal', 'performance', 'meeting')
                             ORDER BY s.scheduled_date ASC, s.start_time ASC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getUpcomingSchedulesForArtist: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get past rehearsals, performances & meetings for a drama (artist history)
     */
    public function getPastSchedulesForArtist($drama_id) {
        try {
            $this->db->query("SELECT s.*, r.role_name
                             FROM drama_schedules s
                             LEFT JOIN drama_roles r ON s.role_id = r.id
                             WHERE s.drama_id = :drama_id
                               AND (s.scheduled_date < CURDATE() OR s.status IN ('completed', 'cancelled'))
                               AND s.event_type IN ('rehearsal', 'performance', 'meeting')
                             ORDER BY s.scheduled_date DESC, s.start_time DESC");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getPastSchedulesForArtist: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get upcoming interview schedule for a specific artist in a drama
     */
    public function getArtistInterviews($artist_id, $drama_id) {
        try {
            $this->db->query("SELECT ra.id AS application_id, ra.interview_at, ra.interview_status,
                                     ra.interview_notes, r.role_name, r.id AS role_id
                             FROM role_applications ra
                             INNER JOIN drama_roles r ON ra.role_id = r.id
                             WHERE ra.artist_id = :artist_id
                               AND r.drama_id = :drama_id
                               AND ra.interview_at IS NOT NULL
                               AND ra.interview_at >= NOW()
                             ORDER BY ra.interview_at ASC");
            $this->db->bind(':artist_id', $artist_id);
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getArtistInterviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count upcoming events by type for artist stats display
     */
    public function getArtistScheduleStats($drama_id) {
        try {
            $this->db->query("SELECT 
                SUM(CASE WHEN event_type = 'rehearsal' THEN 1 ELSE 0 END) AS upcoming_rehearsals,
                SUM(CASE WHEN event_type = 'performance' THEN 1 ELSE 0 END) AS upcoming_performances,
                SUM(CASE WHEN event_type = 'meeting' THEN 1 ELSE 0 END) AS upcoming_meetings,
                COUNT(*) AS total_upcoming
                FROM drama_schedules
                WHERE drama_id = :drama_id
                  AND scheduled_date >= CURDATE()
                  AND status NOT IN ('cancelled')
                  AND event_type IN ('rehearsal', 'performance', 'meeting')");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getArtistScheduleStats: " . $e->getMessage());
            return null;
        }
    }

    // ─── STATISTICS ─────────────────────────────────────────────────────

    /**
     * Get schedule stats for a drama
     */
    public function getScheduleStats($drama_id) {
        try {
            $this->db->query("SELECT 
                COUNT(*) as total_events,
                SUM(CASE WHEN status = 'scheduled' AND scheduled_date >= CURDATE() THEN 1 ELSE 0 END) as upcoming,
                SUM(CASE WHEN status = 'confirmed' AND scheduled_date >= CURDATE() THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'completed' OR scheduled_date < CURDATE() THEN 1 ELSE 0 END) as past,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN event_type = 'rehearsal' AND status != 'cancelled' THEN 1 ELSE 0 END) as rehearsals,
                SUM(CASE WHEN event_type = 'interview' AND status != 'cancelled' THEN 1 ELSE 0 END) as interviews,
                SUM(CASE WHEN event_type = 'meeting' AND status != 'cancelled' THEN 1 ELSE 0 END) as meetings,
                SUM(CASE WHEN event_type = 'performance' AND status != 'cancelled' THEN 1 ELSE 0 END) as performances
                FROM drama_schedules
                WHERE drama_id = :drama_id");
            $this->db->bind(':drama_id', $drama_id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getScheduleStats: " . $e->getMessage());
            return null;
        }
    }
}

?>
