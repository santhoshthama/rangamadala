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
        $this->db->query("INSERT INTO service_requests (
            provider_id, requester_name, requester_email, requester_phone,
            drama_name, service_required, start_date, end_date, notes, status, created_at
        ) VALUES (
            :provider_id, :requester_name, :requester_email, :requester_phone,
            :drama_name, :service_required, :start_date, :end_date, :notes, :status, :created_at
        )");

        $this->db->bind(':provider_id', $data['provider_id']);
        $this->db->bind(':requester_name', $data['requester_name']);
        $this->db->bind(':requester_email', $data['requester_email']);
        $this->db->bind(':requester_phone', $data['requester_phone']);
        $this->db->bind(':drama_name', $data['drama_name']);
        $this->db->bind(':service_required', $data['service_required']);
        $this->db->bind(':start_date', $data['start_date']);
        $this->db->bind(':end_date', $data['end_date']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':created_at', $data['created_at']);

        return $this->db->execute();
    }

    public function getRequestsByProvider($provider_id)
    {
        $this->db->query("SELECT * FROM service_requests WHERE provider_id = :provider_id ORDER BY created_at DESC");
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }

    public function updateRequestStatus($request_id, $status)
    {
        $this->db->query("UPDATE service_requests SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $request_id);
        return $this->db->execute();
    }
}
