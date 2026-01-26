<?php

class M_service_request
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function createRequest($data)
    {
        // Prepare service details JSON
        $serviceDetailsJson = null;
        if (!empty($data['service_details'])) {
            $serviceDetailsJson = json_encode($this->filterServiceDetails($data['service_details']));
        }

        $this->db->query("INSERT INTO service_requests (
            provider_id, requested_by, requester_name, requester_email, requester_phone,
            drama_name, service_type, service_required, start_date, end_date, budget, description, notes, service_details_json, status, created_at
        ) VALUES (
            :provider_id, :requested_by, :requester_name, :requester_email, :requester_phone,
            :drama_name, :service_type, :service_required, :start_date, :end_date, :budget, :description, :notes, :service_details_json, :status, :created_at
        )");

        $this->db->bind(':provider_id', $data['provider_id']);
        $this->db->bind(':requested_by', $data['requested_by'] ?? null);
        $this->db->bind(':requester_name', $data['requester_name']);
        $this->db->bind(':requester_email', $data['requester_email']);
        $this->db->bind(':requester_phone', $data['requester_phone']);
        $this->db->bind(':drama_name', $data['drama_name']);
        $this->db->bind(':service_type', $data['service_type'] ?? null);
        $this->db->bind(':service_required', $data['service_required'] ?? null);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':budget', $data['budget'] ?? null);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':service_details_json', $serviceDetailsJson);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':created_at', $data['created_at']);

        return $this->db->execute();
    }

    /**
     * Filter and extract only valid service detail keys
     */
    private function filterServiceDetails($details_array)
    {
        $serviceDetails = [];
        $serviceDetailKeys = [
            // Theater Production
            'theater_venue_type', 'theater_stage_proscenium', 'theater_stage_black_box', 
            'theater_stage_open_floor', 'theater_stage_size', 'theater_num_days', 'theater_time', 
            'theater_budget_range', 'theater_reference',
            // Lighting
            'lighting_stage_lighting', 'lighting_spotlights', 'lighting_custom_programming', 
            'lighting_moving_heads', 'lighting_num_lights', 'lighting_effects', 'lighting_technician_needed',
            'lighting_budget_range', 'lighting_additional_requirements', 'lighting_reference',
            // Sound
            'sound_pa_system', 'sound_microphones', 'sound_sound_mixing', 'sound_background_music',
            'sound_special_effects', 'sound_additional_services', 'sound_budget_range', 'sound_reference',
            // Video
            'video_recording_type', 'video_duration', 'video_delivery_format', 'video_equipment',
            'video_budget_range', 'video_reference',
            // Set Design
            'set_design_type', 'set_materials', 'set_dimensions', 'set_budget_range', 'set_reference',
            // Costume
            'costume_count', 'costume_style', 'costume_rental_custom', 'costume_budget_range', 'costume_reference',
            // Makeup & Hair
            'makeup_artist_count', 'makeup_session_length', 'makeup_special_effects', 
            'makeup_budget_range', 'makeup_reference',
            // Uploaded files (metadata array)
            'uploaded_files'
        ];
        
        foreach ($serviceDetailKeys as $key) {
            if (isset($details_array[$key]) && $details_array[$key] !== '' && $details_array[$key] !== null) {
                $serviceDetails[$key] = $details_array[$key];
            }
        }
        
        return $serviceDetails;
    }

    public function getRequestsByProvider($provider_id)
    {
        $this->db->query("SELECT * FROM service_requests WHERE provider_id = :provider_id ORDER BY created_at DESC");
        $this->db->bind(':provider_id', $provider_id);
        $results = $this->db->resultSet();
        
        // Parse JSON details and merge into each request object
        foreach ($results as $result) {
            if (!empty($result->service_details_json)) {
                try {
                    $details = json_decode($result->service_details_json, true);
                    if (is_array($details)) {
                        // Merge service details properties into request object
                        foreach ($details as $key => $value) {
                            $result->$key = $value;
                        }
                    }
                } catch (Exception $e) {
                    // Log error but continue - invalid JSON won't break the display
                    error_log("Error parsing service details JSON for request {$result->id}: " . $e->getMessage());
                }
            }
        }
        
        return $results;
    }

    public function updateRequestStatus($request_id, $status)
    {
        $this->db->query("UPDATE service_requests SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $request_id);
        return $this->db->execute();
    }

    public function updateStatusDetailed($request_id, $status, $reason = null, $provider_id = null)
    {
        // Restrict updates to this provider if provided
        $whereProvider = $provider_id ? " AND provider_id = :provider_id" : "";

        if ($status === 'accepted') {
            $sql = "UPDATE service_requests SET status = 'accepted', accepted_at = NOW(), rejection_reason = NULL WHERE id = :id" . $whereProvider;
        } elseif ($status === 'completed') {
            $sql = "UPDATE service_requests SET status = 'completed', completed_at = NOW() WHERE id = :id" . $whereProvider;
        } elseif ($status === 'rejected') {
            $sql = "UPDATE service_requests SET status = 'rejected', rejection_reason = :reason WHERE id = :id" . $whereProvider;
        } else {
            $sql = "UPDATE service_requests SET status = :status WHERE id = :id" . $whereProvider;
        }

        $this->db->query($sql);
        $this->db->bind(':id', $request_id);
        if ($provider_id) {
            $this->db->bind(':provider_id', $provider_id);
        }
        if ($status === 'rejected') {
            $this->db->bind(':reason', $reason);
        } elseif (!in_array($status, ['accepted', 'completed'])) {
            $this->db->bind(':status', $status);
        }

        $result = $this->db->execute();

        // If accepted, mark the dates as booked in provider_availability
        if ($result && $status === 'accepted') {
            try {
                $this->markDatesAsBooked($request_id);
            } catch (Exception $e) {
                // Log error but don't fail the request - availability marking is optional
                error_log("Error marking dates as booked: " . $e->getMessage());
            }
        }

        // If rejected or cancelled, unmark the booked dates
        if ($result && in_array($status, ['rejected', 'cancelled'])) {
            try {
                $this->unmarkBookedDates($request_id);
            } catch (Exception $e) {
                // Log error but don't fail the request
                error_log("Error unmarking booked dates: " . $e->getMessage());
            }
        }

        return $result;
    }

    /**
     * Get service request details by ID
     */
    public function getRequestById($request_id)
    {
        $this->db->query("SELECT * FROM service_requests WHERE id = :id");
        $this->db->bind(':id', $request_id);
        return $this->db->single();
    }

    /**
     * Mark dates as booked when request is accepted
     */
    private function markDatesAsBooked($request_id)
    {
        try {
            // Get request details
            $request = $this->getRequestById($request_id);
            if (!$request || !isset($request->start_date) || !isset($request->end_date)) {
                return false;
            }

            // Use the unified addAvailableDate method
            $availabilityModel = new M_provider_availability();
            
            // Mark all dates from start_date to end_date as booked
            $start = strtotime($request->start_date);
            $end = strtotime($request->end_date);
            
            if ($start === false || $end === false) {
                return false;
            }

            $booked_for = (isset($request->requester_name) ? $request->requester_name : 'Unknown') . ' - ' . (isset($request->drama_name) ? $request->drama_name : '');
            $booking_details = 'Service: ' . (isset($request->service_required) ? $request->service_required : '') . ' | Notes: ' . (isset($request->notes) ? $request->notes : '');

            for ($current = $start; $current <= $end; $current += 86400) { // 86400 = 1 day in seconds
                $date = date('Y-m-d', $current);
                // Use the unified addAvailableDate method directly
                $availabilityModel->addAvailableDate(
                    $request->provider_id,
                    $date,
                    $booking_details,
                    'booked',
                    $booked_for,
                    $booking_details,
                    $request_id
                );
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in markDatesAsBooked: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Unmark dates as booked when request is rejected/cancelled
     */
    private function unmarkBookedDates($request_id)
    {
        try {
            // Get request details
            $request = $this->getRequestById($request_id);
            if (!$request || !isset($request->start_date) || !isset($request->end_date)) {
                return false;
            }

            // Load availability model and unmark dates
            $availabilityModel = new M_provider_availability();
            
            // Unmark all dates from start_date to end_date
            $start = strtotime($request->start_date);
            $end = strtotime($request->end_date);

            if ($start === false || $end === false) {
                return false;
            }

            for ($current = $start; $current <= $end; $current += 86400) {
                $date = date('Y-m-d', $current);
                $availabilityModel->unmarkBooked($request->provider_id, $date);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in unmarkBookedDates: " . $e->getMessage());
            return false;
        }
    }

    public function updatePaymentStatus($request_id, $payment_status, $provider_id = null)
    {
        $whereProvider = $provider_id ? " AND provider_id = :provider_id" : "";
        $this->db->query("UPDATE service_requests SET payment_status = :payment_status WHERE id = :id" . $whereProvider);
        $this->db->bind(':payment_status', $payment_status);
        $this->db->bind(':id', $request_id);
        if ($provider_id) {
            $this->db->bind(':provider_id', $provider_id);
        }
        return $this->db->execute();
    }
}
