<?php

class M_drama_services
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Add or update a required service for a drama
     */
    public function addService($drama_id, $service_type, $budget = null, $description = null)
    {
        $this->db->query("INSERT INTO drama_services (drama_id, service_type, budget, description)
            VALUES (:drama_id, :service_type, :budget, :description)
            ON DUPLICATE KEY UPDATE budget = :budget, description = :description, updated_at = CURRENT_TIMESTAMP");

        $this->db->bind(':drama_id', (int)$drama_id);
        $this->db->bind(':service_type', $service_type);
        $this->db->bind(':budget', $budget);
        $this->db->bind(':description', $description);

        return $this->db->execute();
    }

    /**
     * Remove a service type from drama
     */
    public function removeService($drama_id, $service_type)
    {
        $this->db->query("DELETE FROM drama_services WHERE drama_id = :drama_id AND service_type = :service_type");
        $this->db->bind(':drama_id', (int)$drama_id);
        $this->db->bind(':service_type', $service_type);
        return $this->db->execute();
    }

    /**
     * Get all required services for a drama
     */
    public function getServicesByDrama($drama_id)
    {
        $this->db->query("SELECT * FROM drama_services WHERE drama_id = :drama_id ORDER BY created_at ASC");
        $this->db->bind(':drama_id', (int)$drama_id);
        return $this->db->resultSet();
    }

    /**
     * Get a single service
     */
    public function getService($drama_id, $service_type)
    {
        $this->db->query("SELECT * FROM drama_services WHERE drama_id = :drama_id AND service_type = :service_type");
        $this->db->bind(':drama_id', (int)$drama_id);
        $this->db->bind(':service_type', $service_type);
        return $this->db->single();
    }

    /**
     * Get service types list for a drama
     */
    public function getServiceTypes($drama_id)
    {
        $services = $this->getServicesByDrama($drama_id);
        return array_map(function($s) { return $s->service_type; }, $services ?? []);
    }
}
