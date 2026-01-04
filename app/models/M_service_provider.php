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

    public function getProviderById($user_id) {
        $this->db->query("SELECT * FROM serviceprovider WHERE user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    public function getServicesByProviderId($user_id) {
        $this->db->query("SELECT * FROM services WHERE provider_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    public function getProjectsByProviderId($user_id) {
        $this->db->query("SELECT * FROM projects WHERE provider_id = :user_id ORDER BY year DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
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

    // Service CRUD Methods
    public function insertService($provider_id, $service_name, $rate_per_hour, $description = '') {
        $this->db->query("INSERT INTO services (provider_id, service_name, rate_per_hour, description) 
                         VALUES (:provider_id, :service_name, :rate_per_hour, :description)");
        $this->db->bind(':provider_id', $provider_id);
        $this->db->bind(':service_name', $service_name);
        $this->db->bind(':rate_per_hour', $rate_per_hour);
        $this->db->bind(':description', $description);
        return $this->db->execute();
    }

    public function getServiceById($service_id) {
        $this->db->query("SELECT * FROM services WHERE id = :id");
        $this->db->bind(':id', $service_id);
        return $this->db->single();
    }

    public function updateService($service_id, $service_name, $rate_per_hour, $description = '') {
        $this->db->query("UPDATE services SET 
                         service_name = :service_name, 
                         rate_per_hour = :rate_per_hour, 
                         description = :description
                         WHERE id = :id");
        $this->db->bind(':service_name', $service_name);
        $this->db->bind(':rate_per_hour', $rate_per_hour);
        $this->db->bind(':description', $description);
        $this->db->bind(':id', $service_id);
        return $this->db->execute();
    }

    public function deleteService($service_id) {
        $this->db->query("DELETE FROM services WHERE id = :id");
        $this->db->bind(':id', $service_id);
        return $this->db->execute();
    }

    // Project CRUD Methods
    public function insertProject($provider_id, $year, $project_name, $services_provided, $description = '') {
        $this->db->query("INSERT INTO projects (provider_id, year, project_name, services_provided, description) 
                         VALUES (:provider_id, :year, :project_name, :services_provided, :description)");
        $this->db->bind(':provider_id', $provider_id);
        $this->db->bind(':year', $year);
        $this->db->bind(':project_name', $project_name);
        $this->db->bind(':services_provided', $services_provided);
        $this->db->bind(':description', $description);
        return $this->db->execute();
    }

    public function getProjectById($project_id) {
        $this->db->query("SELECT * FROM projects WHERE id = :id");
        $this->db->bind(':id', $project_id);
        return $this->db->single();
    }

    public function updateProject($project_id, $year, $project_name, $services_provided, $description = '') {
        $this->db->query("UPDATE projects SET 
                         year = :year, 
                         project_name = :project_name, 
                         services_provided = :services_provided,
                         description = :description
                         WHERE id = :id");
        $this->db->bind(':year', $year);
        $this->db->bind(':project_name', $project_name);
        $this->db->bind(':services_provided', $services_provided);
        $this->db->bind(':description', $description);
        $this->db->bind(':id', $project_id);
        return $this->db->execute();
    }

    public function deleteProject($project_id) {
        $this->db->query("DELETE FROM projects WHERE id = :id");
        $this->db->bind(':id', $project_id);
        return $this->db->execute();
    }

    // Provider Basic Info Update
    public function updateBasicInfo($provider_id, $full_name, $professional_title, $email, $phone, 
                                    $location, $website, $years_experience, $professional_summary, 
                                    $availability, $availability_notes) {
        $this->db->query("UPDATE serviceprovider SET 
                         full_name = :full_name, 
                         professional_title = :professional_title, 
                         email = :email, 
                         phone = :phone, 
                         location = :location, 
                         website = :website, 
                         years_experience = :years_experience,
                         professional_summary = :professional_summary,
                         availability = :availability,
                         availability_notes = :availability_notes
                         WHERE user_id = :user_id");
        $this->db->bind(':full_name', $full_name);
        $this->db->bind(':professional_title', $professional_title);
        $this->db->bind(':email', $email);
        $this->db->bind(':phone', $phone);
        $this->db->bind(':location', $location);
        $this->db->bind(':website', $website);
        $this->db->bind(':years_experience', $years_experience);
        $this->db->bind(':professional_summary', $professional_summary);
        $this->db->bind(':availability', $availability);
        $this->db->bind(':availability_notes', $availability_notes);
        $this->db->bind(':user_id', $provider_id);
        return $this->db->execute();
    }

    // Update password with current password verification
    public function updatePasswordWithVerification($user_id, $current_password, $new_password) {
        // Get current password hash from users table
        $this->db->query("SELECT password FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $user_id);
        $user = $this->db->single();
        
        if (!$user) {
            return false;
        }
        
        // Verify current password
        if (!password_verify($current_password, $user->password)) {
            return false;
        }
        
        // Update to new password
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = :password WHERE id = :user_id");
        $this->db->bind(':password', $new_password_hash);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Delete Provider Profile (with cascade)
    public function deleteProvider($provider_id) {
        try {
            $this->db->beginTransaction();
            
            // Delete services
            $this->db->query("DELETE FROM services WHERE provider_id = :provider_id");
            $this->db->bind(':provider_id', $provider_id);
            $this->db->execute();
            
            // Delete projects
            $this->db->query("DELETE FROM projects WHERE provider_id = :provider_id");
            $this->db->bind(':provider_id', $provider_id);
            $this->db->execute();
            
            // Delete provider profile
            $this->db->query("DELETE FROM serviceprovider WHERE user_id = :user_id");
            $this->db->bind(':user_id', $provider_id);
            $this->db->execute();
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('deleteProvider failed: ' . $e->getMessage());
            return false;
        }
    }

    // Update Profile Image (SEPARATE from business certificate)
    public function updateProfileImage($user_id, $filename) {
        $this->db->query("UPDATE serviceprovider SET profile_image = :profile_image WHERE user_id = :user_id");
        $this->db->bind(':profile_image', $filename);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Browse/Search Methods for Production Managers
    public function getAllProvidersWithServices($filters = []) {
        $sql = "SELECT DISTINCT sp.*, 
                GROUP_CONCAT(DISTINCT s.service_name SEPARATOR ', ') as services,
                GROUP_CONCAT(DISTINCT s.rate_per_hour ORDER BY s.rate_per_hour SEPARATOR ', ') as rates
                FROM serviceprovider sp
                LEFT JOIN services s ON sp.user_id = s.provider_id
                WHERE 1=1";

        // Apply filters
        if (!empty($filters['service_type'])) {
            $sql .= " AND s.service_name LIKE :service_type";
        }
        if (!empty($filters['location'])) {
            $sql .= " AND sp.location LIKE :location";
        }
        if (!empty($filters['availability'])) {
            $sql .= " AND sp.availability = :availability";
        }
        if (!empty($filters['min_rate']) || !empty($filters['max_rate'])) {
            $sql .= " AND s.rate_per_hour IS NOT NULL";
            if (!empty($filters['min_rate'])) {
                $sql .= " AND s.rate_per_hour >= :min_rate";
            }
            if (!empty($filters['max_rate'])) {
                $sql .= " AND s.rate_per_hour <= :max_rate";
            }
        }

        $sql .= " GROUP BY sp.user_id ORDER BY sp.full_name ASC";

        $this->db->query($sql);

        if (!empty($filters['service_type'])) {
            $this->db->bind(':service_type', '%' . $filters['service_type'] . '%');
        }
        if (!empty($filters['location'])) {
            $this->db->bind(':location', '%' . $filters['location'] . '%');
        }
        if (!empty($filters['availability'])) {
            $this->db->bind(':availability', (int)$filters['availability']);
        }
        if (!empty($filters['min_rate'])) {
            $this->db->bind(':min_rate', (float)$filters['min_rate']);
        }
        if (!empty($filters['max_rate'])) {
            $this->db->bind(':max_rate', (float)$filters['max_rate']);
        }

        return $this->db->resultSet();
    }

    public function getAllLocations() {
        $this->db->query("SELECT DISTINCT location FROM serviceprovider WHERE location IS NOT NULL ORDER BY location ASC");
        return $this->db->resultSet();
    }

    public function getProviderServices($provider_id) {
        $this->db->query("SELECT * FROM services WHERE provider_id = :provider_id ORDER BY service_name ASC");
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }

    public function getProviderProjects($provider_id) {
        $this->db->query("SELECT * FROM projects WHERE provider_id = :provider_id ORDER BY year DESC");
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }}