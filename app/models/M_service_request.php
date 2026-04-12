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
            drama_id, provider_id, requested_by, requester_name, requester_email, requester_phone,
            drama_name, service_type, service_required, start_date, end_date, budget, description, notes, service_details_json, status, created_at
        ) VALUES (
            :drama_id, :provider_id, :requested_by, :requester_name, :requester_email, :requester_phone,
            :drama_name, :service_type, :service_required, :start_date, :end_date, :budget, :description, :notes, :service_details_json, :status, :created_at
        )");

        // Bind drama_id if provided (nullable)
        $this->db->bind(':drama_id', isset($data['drama_id']) && $data['drama_id'] !== '' ? (int)$data['drama_id'] : null);
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
        $this->db->query("
            SELECT sr.*, 
                   p.id as payment_id,
                   p.payment_type,
                   p.amount as payment_amount,
                   p.payment_gateway,
                   p.payment_status as advance_payment_status,
                   p.transaction_response,
                   p.paid_at,
                   p.reference_number,
                   CASE 
                       WHEN pf.id IS NOT NULL THEN 'paid'
                       WHEN (pa.id IS NOT NULL OR pr.id IS NOT NULL) THEN 'partially_paid'
                       WHEN p.id IS NOT NULL THEN 'pending'
                       ELSE 'unpaid'
                   END as calculated_payment_status
            FROM service_requests sr
            LEFT JOIN payments p ON sr.id = p.service_request_id 
                AND p.payment_status != 'canceled'
                AND p.id = (
                    SELECT p2.id FROM payments p2 
                    WHERE p2.service_request_id = sr.id 
                    AND p2.payment_status != 'canceled'
                    ORDER BY p2.created_at DESC 
                    LIMIT 1
                )
            LEFT JOIN payments pf ON sr.id = pf.service_request_id AND pf.payment_type = 'full' AND pf.payment_status IN ('completed', 'success')
            LEFT JOIN payments pa ON sr.id = pa.service_request_id AND pa.payment_type = 'advance' AND pa.payment_status IN ('completed', 'success')
            LEFT JOIN payments pr ON sr.id = pr.service_request_id AND pr.payment_type = 'remaining' AND pr.payment_status IN ('completed', 'success')
            WHERE sr.provider_id = :provider_id 
            GROUP BY sr.id
            ORDER BY sr.created_at DESC
        ");
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

    /**
     * Get existing accepted/completed bookings that overlap a given date range.
     */
    public function getOverlappingBookingsForProvider($provider_id, $start_date, $end_date, $exclude_request_id = null)
    {
        $sql = "SELECT id, requester_name, requester_email, requester_phone, drama_name,
                   service_type, service_required, start_date, end_date, budget,
                   notes, service_details_json, status
                FROM service_requests
                WHERE provider_id = :provider_id
                  AND status IN ('accepted', 'completed', 'completed_paid')
                  AND NOT (end_date < :start_date OR start_date > :end_date)";

        if (!empty($exclude_request_id)) {
            $sql .= " AND id != :exclude_request_id";
        }

        $sql .= " ORDER BY start_date ASC, id ASC";

        $this->db->query($sql);
        $this->db->bind(':provider_id', $provider_id);
        $this->db->bind(':start_date', $start_date);
        $this->db->bind(':end_date', $end_date);

        if (!empty($exclude_request_id)) {
            $this->db->bind(':exclude_request_id', $exclude_request_id);
        }

        return $this->db->resultSet();
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
        $this->db->query("
            SELECT sr.*, 
                   u.full_name as provider_name, 
                   u.email as provider_email,
                   d.drama_name
            FROM service_requests sr
            LEFT JOIN users u ON sr.provider_id = u.id
            LEFT JOIN dramas d ON sr.drama_id = d.id
            WHERE sr.id = :id
        ");
        $this->db->bind(':id', $request_id);
        return $this->db->single();
    }

    /**
     * Get all service requests for a specific drama
     */
    public function getServicesByDrama($drama_id)
    {
        $this->db->query("
            SELECT sr.*, 
                   p.id as payment_id,
                   p.payment_type,
                   p.amount as payment_amount,
                   p.payment_gateway,
                   p.payment_status as advance_payment_status,
                   p.transaction_response,
                   p.paid_at,
                   p.reference_number,
                   CASE 
                       WHEN pf.id IS NOT NULL THEN 'paid'
                       WHEN (pa.id IS NOT NULL OR pr.id IS NOT NULL) THEN 'partially_paid'
                       WHEN p.id IS NOT NULL THEN 'pending'
                       ELSE 'unpaid'
                   END as calculated_payment_status
            FROM service_requests sr
            LEFT JOIN payments p ON sr.id = p.service_request_id 
                AND p.payment_status != 'canceled'
                AND p.id = (
                    SELECT p2.id FROM payments p2 
                    WHERE p2.service_request_id = sr.id 
                    AND p2.payment_status != 'canceled'
                    ORDER BY p2.created_at DESC 
                    LIMIT 1
                )
            LEFT JOIN payments pf ON sr.id = pf.service_request_id AND pf.payment_type = 'full' AND pf.payment_status IN ('completed', 'success')
            LEFT JOIN payments pa ON sr.id = pa.service_request_id AND pa.payment_type = 'advance' AND pa.payment_status IN ('completed', 'success')
            LEFT JOIN payments pr ON sr.id = pr.service_request_id AND pr.payment_type = 'remaining' AND pr.payment_status IN ('completed', 'success')
            WHERE sr.drama_id = :drama_id 
            GROUP BY sr.id
            ORDER BY sr.created_at DESC
        ");
        $this->db->bind(':drama_id', $drama_id);
        return $this->db->resultSet();
    }

    /**
     * Mark dates as booked with provider decision.
     * allow_more: 1 = still available to others, 0 = fully blocked.
     */
    private function markDatesAsBooked($request_id, $allow_more = 1)
    {
        try {
            // Get request details
            $request = $this->getRequestById($request_id);
            if (!$request || !isset($request->start_date) || !isset($request->end_date)) {
                return false;
            }

            $availabilityModel = new M_provider_availability();
            
            // Mark all dates from start_date to end_date with the provider's decision
            $start = strtotime($request->start_date);
            $end = strtotime($request->end_date);
            
            if ($start === false || $end === false) {
                return false;
            }

            for ($current = $start; $current <= $end; $current += 86400) { // 86400 = 1 day in seconds
                $date = date('Y-m-d', $current);
                $availabilityModel->addAvailableDate(
                    $request->provider_id,
                    $date,
                    'Service request booking',
                    'booked',
                    $request->requester_name ?? 'Production Manager Request',
                    'Booked from accepted request #' . $request_id,
                    $request_id,
                    $allow_more ? 1 : 0
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

            // Load availability model and unmark booking dates
            $availabilityModel = new M_provider_availability();
            
            // Remove booking from all dates for this request
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

    /**
     * Provider submits a response/proposal to an existing request
     */
    public function submitProviderResponse($request_id, $provider_id, $payload)
    {
        try {
            // Verify the request belongs to this provider
            $request = $this->getRequestById($request_id);
            if (!$request || $request->provider_id != $provider_id) {
                return ['success' => false, 'error' => 'Request not found or access denied'];
            }

            // Can only respond to pending requests
            if ($request->status !== 'pending') {
                return ['success' => false, 'error' => 'Cannot respond to this request'];
            }

            // Merge provider response into service_details_json
            $existingDetails = !empty($request->service_details_json) 
                ? json_decode($request->service_details_json, true) 
                : [];
            
            $existingDetails['provider_response'] = [
                'quote_amount' => $payload['quote_amount'] ?? null,
                'needs_advance' => $payload['needs_advance'] ?? false,
                'advance_amount' => $payload['advance_amount'] ?? null,
                'advance_due_date' => $payload['advance_due_date'] ?? null,
                'final_payment_due_date' => $payload['final_payment_due_date'] ?? null,
                'note' => $payload['note'] ?? null,
                'responded_at' => date('Y-m-d H:i:s'),
            ];

            $this->db->query("UPDATE service_requests 
                SET status = 'provider_responded', 
                    service_details_json = :details,
                    provider_notes = :notes
                WHERE id = :id AND provider_id = :provider_id");
            
            $this->db->bind(':details', json_encode($existingDetails));
            $this->db->bind(':notes', $payload['note'] ?? null);
            $this->db->bind(':id', $request_id);
            $this->db->bind(':provider_id', $provider_id);
            
            if ($this->db->execute()) {
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'Database update failed'];
        } catch (Exception $e) {
            error_log("submitProviderResponse error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Server error'];
        }
    }

    /**
     * PM confirms the provider's response
     */
    public function confirmByPM($request_id, $pm_user_id, $payload)
    {
        try {
            // Verify the request exists and is awaiting PM confirmation
            $request = $this->getRequestById($request_id);
            if (!$request) {
                return ['success' => false, 'error' => 'Request not found'];
            }

            // Verify PM has access to this drama
            $pmModel = new M_production_manager();
            if (!$pmModel->isManagerForDrama($pm_user_id, $request->drama_id)) {
                return ['success' => false, 'error' => 'Unauthorized'];
            }

            // Can only confirm provider_responded requests
            if ($request->status !== 'provider_responded') {
                return ['success' => false, 'error' => 'Cannot confirm this request'];
            }

            // Merge PM confirmation into service_details_json
            $existingDetails = !empty($request->service_details_json) 
                ? json_decode($request->service_details_json, true) 
                : [];
            
            $existingDetails['pm_confirmation'] = [
                'final_quote' => $payload['final_quote'] ?? null,
                'final_start_date' => $payload['final_start_date'] ?? null,
                'final_end_date' => $payload['final_end_date'] ?? null,
                'note' => $payload['note'] ?? null,
                'confirmed_at' => date('Y-m-d H:i:s'),
                'confirmed_by' => $pm_user_id,
            ];

            $this->db->query("UPDATE service_requests 
                SET status = 'confirmed', 
                    service_details_json = :details,
                    notes = :notes
                WHERE id = :id");
            
            $this->db->bind(':details', json_encode($existingDetails));
            $this->db->bind(':notes', $payload['note'] ?? null);
            $this->db->bind(':id', $request_id);
            
            if ($this->db->execute()) {
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'Database update failed'];
        } catch (Exception $e) {
            error_log("confirmByPM error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Server error'];
        }
    }

    /**
     * Provider accepts the PM-confirmed terms (legacy - keep for backward compatibility)
     */
    public function acceptConfirmed($request_id, $provider_id)
    {
        return $this->acceptConfirmedWithDecision($request_id, $provider_id, 0);
    }

    /**
     * Provider accepts the PM-confirmed terms with allow/block decision.
     */
    public function acceptConfirmedWithDecision($request_id, $provider_id, $allow_more = 0)
    {
        try {
            // Verify the request belongs to this provider
            $request = $this->getRequestById($request_id);
            if (!$request || $request->provider_id != $provider_id) {
                return ['success' => false, 'error' => 'Request not found or access denied'];
            }

            // Can only accept confirmed requests
            if ($request->status !== 'confirmed') {
                return ['success' => false, 'error' => 'Cannot accept this request'];
            }

            // Update status to accepted 
            $this->db->query("UPDATE service_requests SET status = 'accepted', accepted_at = NOW(), rejection_reason = NULL WHERE id = :id AND provider_id = :provider_id");
            $this->db->bind(':id', $request_id);
            $this->db->bind(':provider_id', $provider_id);
            
            $result = $this->db->execute();
            
            // If accepted, mark the dates with the allow_more decision
            if ($result) {
                try {
                    $this->markDatesAsBooked($request_id, $allow_more ? 1 : 0);
                } catch (Exception $e) {
                    error_log("Error marking dates as booked: " . $e->getMessage());
                }
            }
            
            if ($result) {
                return ['success' => true];
            }
            return ['success' => false, 'error' => 'Database update failed'];
        } catch (Exception $e) {
            error_log("acceptConfirmedWithDecision error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Server error'];
        }
    }

    /**
     * Simple method to update request status
     * Used by Payment controller to auto-upgrade to completed_paid
     */
    public function updateStatus($request_id, $status)
    {
        return $this->updateRequestStatus($request_id, $status);
    }

    /**
     * Bookings Report - Detailed list of all bookings
     */
    public function getBookingsReport($provider_id, $startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                sr.id,
                sr.drama_name,
                sr.service_type,
                sr.budget,
                sr.status,
                sr.start_date,
                sr.end_date,
                sr.requester_name,
                sr.created_at,
                COALESCE(SUM(CASE WHEN p.payment_status IN ('completed', 'success') THEN p.amount ELSE 0 END), 0) as amount_paid,
                CASE 
                    WHEN pf.id IS NOT NULL THEN 'fully_paid'
                    WHEN (pa.id IS NOT NULL OR pr.id IS NOT NULL) THEN 'partially_paid'
                    WHEN p.id IS NOT NULL THEN 'pending'
                    ELSE 'unpaid'
                END as payment_status
            FROM service_requests sr
            LEFT JOIN payments p ON sr.id = p.service_request_id
            LEFT JOIN payments pf ON sr.id = pf.service_request_id AND pf.payment_type = 'full' AND pf.payment_status IN ('completed', 'success')
            LEFT JOIN payments pa ON sr.id = pa.service_request_id AND pa.payment_type = 'advance' AND pa.payment_status IN ('completed', 'success')
            LEFT JOIN payments pr ON sr.id = pr.service_request_id AND pr.payment_type = 'remaining' AND pr.payment_status IN ('completed', 'success')
            WHERE sr.provider_id = :provider_id";
        
        if ($startDate && $endDate) {
            $sql .= " AND sr.created_at BETWEEN :startDate AND :endDate";
        }
        
        $sql .= " GROUP BY sr.id ORDER BY sr.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':provider_id', $provider_id);
        if ($startDate && $endDate) {
            $this->db->bind(':startDate', $startDate);
            $this->db->bind(':endDate', $endDate);
        }
        return $this->db->resultSet();
    }

    /**
     * Service Performance Report - Detailed service requests
     */
    public function getServicePerformance($provider_id, $startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                sr.id,
                sr.drama_name,
                sr.service_type,
                sr.budget,
                sr.status,
                sr.start_date,
                sr.end_date,
                sr.requester_name,
                sr.created_at,
                sr.completed_at,
                CASE WHEN sr.status = 'completed' THEN 1 ELSE 0 END as is_completed,
                COALESCE(SUM(CASE WHEN p.payment_status IN ('completed', 'success') THEN p.amount ELSE 0 END), 0) as amount,
                COALESCE(SUM(CASE WHEN p.payment_status IN ('completed', 'success') THEN p.amount ELSE 0 END), 0) as amount_paid
            FROM service_requests sr
            LEFT JOIN payments p ON sr.id = p.service_request_id
            WHERE sr.provider_id = :provider_id AND sr.service_type IS NOT NULL";
        
        if ($startDate && $endDate) {
            $sql .= " AND sr.created_at BETWEEN :startDate AND :endDate";
        }
        
        $sql .= " GROUP BY sr.id ORDER BY sr.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':provider_id', $provider_id);
        if ($startDate && $endDate) {
            $this->db->bind(':startDate', $startDate);
            $this->db->bind(':endDate', $endDate);
        }
        return $this->db->resultSet();
    }

    /**
     * Cancellation / Rejection Report - Detailed cancelled/rejected requests
     */
    public function getCancellationReport($provider_id, $startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                sr.id,
                sr.drama_name,
                sr.service_type,
                sr.budget,
                sr.status,
                sr.rejection_reason,
                sr.requester_name,
                sr.created_at,
                sr.start_date,
                sr.end_date,
                COALESCE(SUM(CASE WHEN p.payment_status IN ('completed', 'success') THEN p.amount ELSE 0 END), 0) as amount_paid
            FROM service_requests sr
            LEFT JOIN payments p ON sr.id = p.service_request_id
            WHERE sr.provider_id = :provider_id 
            AND sr.status IN ('rejected', 'cancelled')";
        
        if ($startDate && $endDate) {
            $sql .= " AND sr.created_at BETWEEN :startDate AND :endDate";
        }
        
        $sql .= " GROUP BY sr.id ORDER BY sr.created_at DESC";
        
        $this->db->query($sql);
        $this->db->bind(':provider_id', $provider_id);
        if ($startDate && $endDate) {
            $this->db->bind(':startDate', $startDate);
            $this->db->bind(':endDate', $endDate);
        }
        return $this->db->resultSet();
    }

    /**
     * Dashboard overview counts for a provider
     */
    public function getDashboardCounts($provider_id, $startDate = null, $endDate = null)
    {
        $sql = "SELECT
                    COUNT(*) AS total_bookings,
                    SUM(CASE WHEN status IN ('completed', 'completed_paid') THEN 1 ELSE 0 END) AS completed_services,
                    SUM(CASE WHEN status IN ('pending', 'provider_responded', 'confirmed', 'accepted') THEN 1 ELSE 0 END) AS active_services
                FROM service_requests
                WHERE provider_id = :provider_id";

        if ($startDate && $endDate) {
            $sql .= " AND created_at BETWEEN :startDate AND :endDate";
        }

        $this->db->query($sql);
        $this->db->bind(':provider_id', $provider_id);
        if ($startDate && $endDate) {
            $this->db->bind(':startDate', $startDate);
            $this->db->bind(':endDate', $endDate);
        }

        return $this->db->single();
    }

    /**
     * Service distribution by booking count
     */
    public function getServiceDistribution($provider_id, $limit = 6)
    {
        $limit = max(1, (int)$limit);

        $this->db->query("SELECT
                            service_type,
                            COUNT(*) AS booking_count
                          FROM service_requests
                          WHERE provider_id = :provider_id
                            AND service_type IS NOT NULL
                            AND service_type != ''
                          GROUP BY service_type
                          ORDER BY booking_count DESC
                          LIMIT " . $limit);
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }

    /**
     * Latest ongoing services for dashboard list
     */
    public function getOngoingServices($provider_id, $limit = 6)
    {
        $limit = max(1, (int)$limit);

        $this->db->query("SELECT
                            sr.id,
                            sr.drama_name,
                            sr.service_type,
                            sr.status,
                            sr.updated_at,
                            sr.requester_name,
                            COALESCE(SUM(CASE WHEN p.payment_status IN ('completed', 'success') THEN p.amount ELSE 0 END), 0) AS amount_paid
                          FROM service_requests sr
                          LEFT JOIN payments p ON sr.id = p.service_request_id
                          WHERE sr.provider_id = :provider_id
                            AND sr.status IN ('pending', 'provider_responded', 'confirmed', 'accepted')
                          GROUP BY sr.id
                          ORDER BY sr.updated_at DESC
                          LIMIT " . $limit);
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }

    /**
     * Top clients by total paid amount
     */
    public function getTopClients($provider_id, $limit = 3)
    {
        $limit = max(1, (int)$limit);

        $this->db->query("SELECT
                            sr.requester_name,
                            COUNT(DISTINCT sr.id) AS booking_count,
                            COALESCE(SUM(CASE WHEN p.payment_status IN ('completed', 'success') THEN p.amount ELSE 0 END), 0) AS total_spent,
                            MAX(sr.created_at) AS last_booking
                          FROM service_requests sr
                          LEFT JOIN payments p ON sr.id = p.service_request_id
                          WHERE sr.provider_id = :provider_id
                          GROUP BY sr.requester_name
                          ORDER BY total_spent DESC, booking_count DESC
                          LIMIT " . $limit);
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }
}
