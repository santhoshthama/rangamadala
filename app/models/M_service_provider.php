<?php

class M_service_provider extends M_signup {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = new Database();
    }

    public function register($full_name, $email, $password, $phone, $nic_photo = null) {
        return $this->registerUser($full_name, $email, $password, $phone, 'service_provider', $nic_photo);
    }

    public function emailExists($email) {
        $this->db->query("SELECT COUNT(*) AS cnt FROM serviceprovider WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row && isset($row->cnt) ? ((int)$row->cnt > 0) : false;
    }

    public function nameExists($full_name) {
        $this->db->query("SELECT COUNT(*) AS cnt FROM serviceprovider WHERE full_name = :full_name");
        $this->db->bind(':full_name', $full_name);
        $row = $this->db->single();
        return $row && isset($row->cnt) ? ((int)$row->cnt > 0) : false;
    }

    public function emailExistsInUsers($email) {
        $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row && isset($row->cnt) ? ((int)$row->cnt > 0) : false;
    }

    public function getUserIdByEmail($email) {
        $this->db->query("SELECT id FROM users WHERE email = :email LIMIT 1");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row ? (int)$row->id : false;
    }

    public function saveFullProfile($provider, $user_id, $services = [], $projects = []) {
        $providerId = (int)$user_id;

        // Phase 1: Insert the provider profile (transactional)
        try {
            $this->db->beginTransaction();

            $this->db->query("INSERT INTO serviceprovider (
                user_id, full_name, professional_title, email, phone, location,
                website, years_experience, professional_summary,
                availability, availability_notes, business_cert_photo
            ) VALUES (
                :user_id, :full_name, :professional_title, :email, :phone, :location,
                :website, :years_experience, :professional_summary,
                :availability, :availability_notes, :business_cert_photo
            )
            ON DUPLICATE KEY UPDATE
                full_name = VALUES(full_name),
                professional_title = VALUES(professional_title),
                email = VALUES(email),
                phone = VALUES(phone),
                location = VALUES(location),
                website = VALUES(website),
                years_experience = VALUES(years_experience),
                professional_summary = VALUES(professional_summary),
                availability = VALUES(availability),
                availability_notes = VALUES(availability_notes),
                business_cert_photo = VALUES(business_cert_photo)");

            $this->db->bind(':user_id', $providerId);
            $this->db->bind(':full_name', $provider['full_name'] ?? null);
            $this->db->bind(':professional_title', $provider['professional_title'] ?? null);
            $this->db->bind(':email', $provider['email'] ?? null);
            $this->db->bind(':phone', $provider['phone'] ?? null);
            $this->db->bind(':location', $provider['location'] ?? null);
            $this->db->bind(':website', $provider['website'] ?? null);
            $this->db->bind(':years_experience', isset($provider['years_experience']) && $provider['years_experience'] !== '' ? (int)$provider['years_experience'] : null);
            $this->db->bind(':professional_summary', $provider['professional_summary'] ?? null);
            $this->db->bind(':availability', isset($provider['availability']) ? (int)$provider['availability'] : 1);
            $this->db->bind(':availability_notes', $provider['availability_notes'] ?? null);
            $this->db->bind(':business_cert_photo', $provider['business_cert_photo'] ?? null);

            $this->db->execute();
            $this->db->commit();
        } catch (Exception $e) {
            // Provider insert failed; nothing else to do
            $this->db->rollBack();
            // Optional: log error for debugging
            error_log('saveFullProfile provider insert failed: ' . $e->getMessage());
            return false;
        }

        // Phase 2: Insert services (best-effort, do not rollback provider)
        try {
            if (!empty($services) && is_array($services)) {
                foreach ($services as $svc) {
                    if (isset($svc['selected']) && !empty($svc['name'])) {
                        $this->db->query("INSERT INTO services (provider_id, service_name, rate_per_hour, description)
                                          VALUES (:provider_id, :service_name, :rate_per_hour, :description)");
                        $this->db->bind(':provider_id', $providerId);
                        $this->db->bind(':service_name', $svc['name']);
                        $this->db->bind(':rate_per_hour', isset($svc['rate']) && $svc['rate'] !== '' ? (float)$svc['rate'] : 0);
                        $this->db->bind(':description', isset($svc['description']) ? trim($svc['description']) : null);
                        $this->db->execute();
                    }
                }
            }
        } catch (Exception $e) {
            // Optional: log but don't fail the registration
            error_log('saveFullProfile services insert failed: ' . $e->getMessage());
        }

        // Phase 3: Insert projects (best-effort, do not rollback provider)
        try {
            if (!empty($projects) && is_array($projects)) {
                foreach ($projects as $proj) {
                    $this->db->query("INSERT INTO projects (provider_id, year, project_name, services_provided, description)
                                      VALUES (:provider_id, :year, :project_name, :services_provided, :description)");
                    $this->db->bind(':provider_id', $providerId);
                    $this->db->bind(':year', isset($proj['year']) && $proj['year'] !== '' ? (int)$proj['year'] : null);
                    $this->db->bind(':project_name', $proj['project_name'] ?? null);
                    $this->db->bind(':services_provided', $proj['services_provided'] ?? null);
                    $this->db->bind(':description', $proj['description'] ?? null);
                    $this->db->execute();
                }
            }
        } catch (Exception $e) {
            // Optional: log but don't fail the registration
            error_log('saveFullProfile projects insert failed: ' . $e->getMessage());
        }

        return $providerId;
    }
}
