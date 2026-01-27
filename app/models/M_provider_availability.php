<?php

class M_provider_availability
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Unified method to add a date (handles both manual and automatic additions)
     */
    public function addAvailableDate($provider_id, $available_date, $description, $status = 'available', $booked_for = null, $booking_details = null, $service_request_id = null)
    {
        try {
            $this->db->query("INSERT INTO provider_availability (provider_id, available_date, description, status, booked_for, booking_details, service_request_id, added_on)
                             VALUES (:provider_id, :available_date, :description, :status, :booked_for, :booking_details, :service_request_id, NOW())
                             ON DUPLICATE KEY UPDATE 
                             description = VALUES(description), status = VALUES(status), booked_for = VALUES(booked_for), 
                             booking_details = VALUES(booking_details), service_request_id = VALUES(service_request_id), updated_at = NOW()");
            
            $this->db->bind(':provider_id', $provider_id);
            $this->db->bind(':available_date', $available_date);
            $this->db->bind(':description', $description);
            $this->db->bind(':status', $status);
            $this->db->bind(':booked_for', $booked_for);
            $this->db->bind(':booking_details', $booking_details);
            $this->db->bind(':service_request_id', $service_request_id);
            
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in addAvailableDate: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark a date as booked when a service request is accepted
     * Now uses the unified addAvailableDate method
     */
    public function markAsBooked($provider_id, $available_date, $booked_for, $booking_details, $service_request_id)
    {
        try {
            return $this->addAvailableDate($provider_id, $available_date, $booking_details, 'booked', $booked_for, $booking_details, $service_request_id);
        } catch (Exception $e) {
            error_log("Error in markAsBooked: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all availability for a provider with request details
     */
    public function getAvailability($provider_id, $from_date = null, $to_date = null)
    {
        try {
            $sql = "SELECT pa.*, 
                           sr.requester_name, sr.requester_email, sr.requester_phone,
                           sr.drama_name, sr.service_type, sr.service_required,
                           sr.start_date, sr.end_date, sr.budget, sr.description as request_description,
                           sr.notes, sr.status as request_status, sr.service_details_json
                    FROM provider_availability pa
                    LEFT JOIN service_requests sr ON pa.service_request_id = sr.id
                    WHERE pa.provider_id = :provider_id";
            
            if ($from_date && $to_date) {
                $sql .= " AND pa.available_date BETWEEN :from_date AND :to_date";
            } elseif ($from_date) {
                $sql .= " AND pa.available_date >= :from_date";
            }
            
            $sql .= " ORDER BY pa.available_date ASC";
            
            $this->db->query($sql);
            $this->db->bind(':provider_id', $provider_id);
            
            if ($from_date && $to_date) {
                $this->db->bind(':from_date', $from_date);
                $this->db->bind(':to_date', $to_date);
            } elseif ($from_date) {
                $this->db->bind(':from_date', $from_date);
            }
            
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAvailability: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get availability for a specific date
     */
    public function getAvailabilityByDate($provider_id, $available_date)
    {
        $this->db->query("SELECT * FROM provider_availability WHERE provider_id = :provider_id AND available_date = :available_date");
        $this->db->bind(':provider_id', $provider_id);
        $this->db->bind(':available_date', $available_date);
        
        return $this->db->resultSet();
    }

    /**
     * Remove an available date
     */
    public function removeAvailableDate($provider_id, $available_date)
    {
        $this->db->query("DELETE FROM provider_availability WHERE provider_id = :provider_id AND available_date = :available_date");
        $this->db->bind(':provider_id', $provider_id);
        $this->db->bind(':available_date', $available_date);
        
        return $this->db->execute();
    }

    /**
     * Update availability date description
     */
    public function updateAvailableDate($provider_id, $available_date, $description, $status = 'available')
    {
        $this->db->query("UPDATE provider_availability SET description = :description, status = :status, updated_at = NOW() 
                         WHERE provider_id = :provider_id AND available_date = :available_date");
        
        $this->db->bind(':provider_id', $provider_id);
        $this->db->bind(':available_date', $available_date);
        $this->db->bind(':description', $description);
        $this->db->bind(':status', $status);
        
        return $this->db->execute();
    }

    /**
     * Get booked dates for a provider
     */
    public function getBookedDates($provider_id)
    {
        $this->db->query("SELECT * FROM provider_availability WHERE provider_id = :provider_id AND status = 'booked' ORDER BY available_date ASC");
        $this->db->bind(':provider_id', $provider_id);
        
        return $this->db->resultSet();
    }

    /**
     * Unmark a date as booked (when request is rejected or cancelled)
     */
    public function unmarkBooked($provider_id, $available_date)
    {
        $this->db->query("UPDATE provider_availability SET status = 'available', booked_for = NULL, booking_details = NULL, 
                         service_request_id = NULL, booked_on = NULL, updated_at = NOW() 
                         WHERE provider_id = :provider_id AND available_date = :available_date");
        
        $this->db->bind(':provider_id', $provider_id);
        $this->db->bind(':available_date', $available_date);
        
        return $this->db->execute();
    }
}
