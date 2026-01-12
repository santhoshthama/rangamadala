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
            provider_id, requested_by, requester_name, requester_email, requester_phone,
            drama_name, service_type, service_required, start_date, end_date, notes, status, created_at
        ) VALUES (
            :provider_id, :requested_by, :requester_name, :requester_email, :requester_phone,
            :drama_name, :service_type, :service_required, :start_date, :end_date, :notes, :status, :created_at
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

        return $this->db->execute();
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
