<?php

class M_service_request
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all service requests for a specific drama
     */
    public function getServicesByDrama($drama_id)
    {
        $this->db->query("SELECT * FROM service_requests 
                         WHERE drama_id = :drama_id 
                         ORDER BY created_at DESC");
        $this->db->bind(':drama_id', $drama_id);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    /**
     * Get service requests by status for a drama
     */
    public function getServicesByStatus($drama_id, $status)
    {
        $this->db->query("SELECT * FROM service_requests 
                         WHERE drama_id = :drama_id AND status = :status 
                         ORDER BY created_at DESC");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':status', $status);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    /**
     * Count services by status
     */
    public function countServicesByStatus($drama_id, $status)
    {
        $this->db->query("SELECT COUNT(*) as count FROM service_requests 
                         WHERE drama_id = :drama_id AND status = :status");
        $this->db->bind(':drama_id', $drama_id);
        $this->db->bind(':status', $status);
        $result = $this->db->single();
        return $result ? intval($result->count) : 0;
    }

    /**
     * Get all service requests for a provider
     */
    public function getRequestsByProvider($provider_id)
    {
        $this->db->query("SELECT sr.*, d.drama_name, d.image_url,
                         u.name as requester_name, u.email as requester_email 
                         FROM service_requests sr
                         JOIN dramas d ON sr.drama_id = d.id
                         JOIN users u ON sr.created_by = u.id
                         WHERE sr.service_provider_id = :provider_id 
                         ORDER BY sr.created_at DESC");
        $this->db->bind(':provider_id', $provider_id);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    /**
     * Create a new service request
     */
    public function createRequest($data)
    {
        $this->db->query("INSERT INTO service_requests (
            drama_id, service_provider_id, service_type, status, 
            request_date, required_date, budget_range, description, 
            special_requirements, created_by
        ) VALUES (
            :drama_id, :service_provider_id, :service_type, :status,
            :request_date, :required_date, :budget_range, :description,
            :special_requirements, :created_by
        )");

        $this->db->bind(':drama_id', $data['drama_id']);
        $this->db->bind(':service_provider_id', $data['service_provider_id']);
        $this->db->bind(':service_type', $data['service_type']);
        $this->db->bind(':status', $data['status'] ?? 'pending');
        $this->db->bind(':request_date', $data['request_date'] ?? date('Y-m-d'));
        $this->db->bind(':required_date', $data['required_date'] ?? null);
        $this->db->bind(':budget_range', $data['budget_range'] ?? null);
        $this->db->bind(':description', $data['description'] ?? null);
        $this->db->bind(':special_requirements', $data['special_requirements'] ?? null);
        $this->db->bind(':created_by', $data['created_by']);

        return $this->db->execute();
    }

    /**
     * Update service request status
     */
    public function updateRequestStatus($request_id, $status)
    {
        $this->db->query("UPDATE service_requests 
                         SET status = :status, updated_at = CURRENT_TIMESTAMP 
                         WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $request_id);
        return $this->db->execute();
    }

    /**
     * Get a single service request by ID
     */
    public function getRequestById($request_id)
    {
        $this->db->query("SELECT sr.*, d.drama_name, u.name as requester_name 
                         FROM service_requests sr
                         JOIN dramas d ON sr.drama_id = d.id
                         JOIN users u ON sr.created_by = u.id
                         WHERE sr.id = :id");
        $this->db->bind(':id', $request_id);
        return $this->db->single();
    }

    /**
     * Delete a service request
     */
    public function deleteRequest($request_id)
    {
        $this->db->query("DELETE FROM service_requests WHERE id = :id");
        $this->db->bind(':id', $request_id);
        return $this->db->execute();
    }

    /**
     * Get total count of service requests for a drama
     */
    public function getTotalCount($drama_id)
    {
        $this->db->query("SELECT COUNT(*) as count FROM service_requests 
                         WHERE drama_id = :drama_id");
        $this->db->bind(':drama_id', $drama_id);
        $result = $this->db->single();
        return $result ? intval($result->count) : 0;
    }
}
