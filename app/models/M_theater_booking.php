<?php

class M_theater_booking
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all theater bookings for a drama
     */
    public function getBookingsByDrama($drama_id)
    {
        $this->db->query("
            SELECT * FROM theater_bookings 
            WHERE drama_id = :drama_id 
            ORDER BY booking_date ASC, start_time ASC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Get a specific booking by ID
     */
    public function getBookingById($id)
    {
        $this->db->query("SELECT * FROM theater_bookings WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get confirmed bookings for a drama
     */
    public function getConfirmedBookings($drama_id)
    {
        $this->db->query("
            SELECT * FROM theater_bookings 
            WHERE drama_id = :drama_id AND status = 'confirmed'
            ORDER BY booking_date ASC, start_time ASC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet() ?: [];
    }

    /**
     * Create a new theater booking
     */
    public function createBooking($data)
    {
        $this->db->query("
            INSERT INTO theater_bookings (
                drama_id, theater_name, booking_date, start_time, end_time, 
                capacity, rental_cost, status, special_requests, booking_reference, created_by
            ) VALUES (
                :drama_id, :theater_name, :booking_date, :start_time, :end_time,
                :capacity, :rental_cost, :status, :special_requests, :booking_reference, :created_by
            )
        ");

        $this->db->bind(':drama_id', $data['drama_id']);
        $this->db->bind(':theater_name', $data['theater_name']);
        $this->db->bind(':booking_date', $data['booking_date']);
        $this->db->bind(':start_time', $data['start_time']);
        $this->db->bind(':end_time', $data['end_time']);
        $this->db->bind(':capacity', $data['capacity'] ?? null);
        $this->db->bind(':rental_cost', $data['rental_cost'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'pending');
        $this->db->bind(':special_requests', $data['special_requests'] ?? null);
        $this->db->bind(':booking_reference', $data['booking_reference'] ?? null);
        $this->db->bind(':created_by', $data['created_by']);

        return $this->db->execute();
    }

    /**
     * Update booking status
     */
    public function updateStatus($id, $status)
    {
        $this->db->query("
            UPDATE theater_bookings 
            SET status = :status, updated_at = CURRENT_TIMESTAMP 
            WHERE id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    /**
     * Delete a booking
     */
    public function deleteBooking($id)
    {
        $this->db->query("DELETE FROM theater_bookings WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Get total theater bookings count for a drama
     */
    public function getBookingCount($drama_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM theater_bookings WHERE drama_id = :drama_id");
        $this->db->bind(':drama_id', $drama_id);
        $result = $this->db->single();
        return $result->count ?? 0;
    }

    /**
     * Get total theater rental cost for a drama
     */
    public function getTotalCost($drama_id)
    {
        $this->db->query("
            SELECT SUM(rental_cost) as total FROM theater_bookings 
            WHERE drama_id = :drama_id AND status = 'confirmed'
        ");
        $this->db->bind(':drama_id', $drama_id);
        $result = $this->db->single();
        return $result->total ?? 0;
    }
}

?>
