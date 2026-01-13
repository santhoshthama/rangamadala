<?php

class M_service_provider extends M_signup {
    private $db;

    public function __construct() {
        parent::__construct();
        $this->db = new Database();
    }

    public function register($full_name, $email, $password, $phone, $nic_photo_front = null, $nic_photo_back = null) {
        return $this->registerUser($full_name, $email, $password, $phone, 'service_provider', $nic_photo_front, $nic_photo_back);
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
        $this->db->query("SELECT s.*, st.service_type 
                          FROM services s 
                          LEFT JOIN service_types st ON s.service_type_id = st.service_type_id 
                          WHERE s.provider_id = :user_id");
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
                user_id, full_name, professional_title, email, phone, location, nic_number,
                social_media_link, years_experience, professional_summary,
                availability, availability_notes, nic_photo_front, nic_photo_back
            ) VALUES (
                :user_id, :full_name, :professional_title, :email, :phone, :location, :nic_number,
                :social_media_link, :years_experience, :professional_summary,
                :availability, :availability_notes, :nic_photo_front, :nic_photo_back
            )
            ON DUPLICATE KEY UPDATE
                full_name = VALUES(full_name),
                professional_title = VALUES(professional_title),
                email = VALUES(email),
                phone = VALUES(phone),
                location = VALUES(location),
                nic_number = VALUES(nic_number),
                social_media_link = VALUES(social_media_link),
                years_experience = VALUES(years_experience),
                professional_summary = VALUES(professional_summary),
                availability = VALUES(availability),
                availability_notes = VALUES(availability_notes),
                nic_photo_front = VALUES(nic_photo_front),
                nic_photo_back = VALUES(nic_photo_back)");

            $this->db->bind(':user_id', $providerId);
            $this->db->bind(':full_name', $provider['full_name'] ?? null);
            $this->db->bind(':professional_title', $provider['professional_title'] ?? null);
            $this->db->bind(':email', $provider['email'] ?? null);
            $this->db->bind(':phone', $provider['phone'] ?? null);
            $this->db->bind(':location', $provider['location'] ?? null);
            $this->db->bind(':nic_number', $provider['nic_number'] ?? null);
            $this->db->bind(':social_media_link', $provider['website'] ?? null);
            $this->db->bind(':years_experience', isset($provider['years_experience']) && $provider['years_experience'] !== '' ? (int)$provider['years_experience'] : null);
            $this->db->bind(':professional_summary', $provider['professional_summary'] ?? null);
            $this->db->bind(':availability', isset($provider['availability']) ? (int)$provider['availability'] : 1);
            $this->db->bind(':availability_notes', $provider['availability_notes'] ?? null);
            $this->db->bind(':nic_photo_front', $provider['nic_photo_front'] ?? null);
            $this->db->bind(':nic_photo_back', $provider['nic_photo_back'] ?? null);

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
                    if (!empty($svc['selected']) && !empty($svc['name'])) {
                        $typeId = $this->getServiceTypeIdByName($svc['name']);

                        $this->db->query("INSERT INTO services (provider_id, service_type_id)
                                          VALUES (:provider_id, :service_type_id)");
                        $this->db->bind(':provider_id', $providerId);
                        $this->db->bind(':service_type_id', $typeId);
                        $this->db->execute();

                        $serviceId = $this->db->lastInsertId();
                        $detail = $this->buildDetailPayload($svc['name'], $svc);
                        if ($serviceId && $detail) {
                            $this->insertServiceDetail($serviceId, $detail);
                        }
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
    public function insertService($provider_id, $service_type_name, $description = '', $extras = []) {
        $providerId = (int)$provider_id;
        $typeId = $this->getServiceTypeIdByName($service_type_name);

        $this->db->query("INSERT INTO services (provider_id, service_type_id)
                          VALUES (:provider_id, :service_type_id)");
        $this->db->bind(':provider_id', $providerId);
        $this->db->bind(':service_type_id', $typeId);
        $this->db->execute();

        $serviceId = $this->db->lastInsertId();
        $detail = $this->buildDetailPayload($service_type_name, $extras + ['description' => $description]);
        if ($serviceId && $detail) {
            $this->insertServiceDetail((int)$serviceId, $detail);
        }

        return (bool)$serviceId;
    }

    public function getServiceById($service_id) {
        $this->db->query("SELECT s.*, st.service_type 
                          FROM services s 
                          LEFT JOIN service_types st ON s.service_type_id = st.service_type_id 
                          WHERE s.id = :id");
        $this->db->bind(':id', $service_id);
        return $this->db->single();
    }

    public function getServiceDetails($service_id, $service_type = '') {
        $map = [
            'theater production' => 'service_theater_details',
            'lighting design'    => 'service_lighting_details',
            'sound systems'      => 'service_sound_details',
            'video production'   => 'service_video_details',
            'set design'         => 'service_set_details',
            'costume design'     => 'service_costume_details',
            'makeup & hair'      => 'service_makeup_details',
            'other'              => 'service_other_details',
        ];

        $key = strtolower(trim($service_type));

        // Prefer the declared service_type if present
        if (isset($map[$key])) {
            $table = $map[$key];
            $this->db->query("SELECT * FROM {$table} WHERE service_id = :service_id");
            $this->db->bind(':service_id', $service_id);
            $detail = $this->db->single();
            if ($detail) {
                // For 'other' service type, preserve the user-entered service_type from database
                // For other types, set the category name
                if ($key !== 'other') {
                    $detail->service_type = $service_type ?: ucfirst($key);
                }
                return $detail;
            }
        }

        // Fallback: detect by scanning all detail tables (handles older rows without service_type)
        foreach ($map as $label => $table) {
            $this->db->query("SELECT * FROM {$table} WHERE service_id = :service_id");
            $this->db->bind(':service_id', $service_id);
            $detail = $this->db->single();
            if ($detail) {
                // For 'other' service type, preserve the user-entered service_type from database
                // For other types, set the category name
                if ($label !== 'other') {
                    $detail->service_type = $label;
                }
                return $detail;
            }
        }

        return null;
    }

    public function updateService($service_id, $service_type_name, $description = '', $extras = []) {
        $typeId = $this->getServiceTypeIdByName($service_type_name);

        $this->db->query("UPDATE services SET 
            service_type_id = :service_type_id
            WHERE id = :id");

        $this->db->bind(':service_type_id', $typeId);
        $this->db->bind(':id', $service_id);
        $baseUpdated = $this->db->execute();

        $detail = $this->buildDetailPayload($service_type_name, $extras + ['description' => $description]);
        if ($detail) {
            $this->upsertServiceDetail((int)$service_id, $detail);
        }

        return $baseUpdated;
    }

    private function getServiceTypeIdByName(string $serviceTypeName): ?int {
        $name = trim($serviceTypeName);
        if ($name === '') {
            return null;
        }

        $this->db->query("SELECT service_type_id FROM service_types WHERE LOWER(service_type) = LOWER(:name) LIMIT 1");
        $this->db->bind(':name', $name);
        $row = $this->db->single();
        if (!empty($row->service_type_id)) {
            return (int)$row->service_type_id;
        }

        // Create service type if it doesn't exist yet
        $this->db->query("INSERT INTO service_types (service_type) VALUES (:name)");
        $this->db->bind(':name', $name);
        $this->db->execute();
        return (int)$this->db->lastInsertId();
    }

    private function buildDetailPayload(string $serviceName, array $svc): ?array {
        $key = strtolower(trim($serviceName));
        switch ($key) {
            case 'theater production':
                $af = (array)($svc['available_facilities'] ?? []);
                $tf = (array)($svc['technical_facilities'] ?? []);
                return [
                    'table' => 'service_theater_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'theatre_name' => $svc['theatre_name'] ?? null,
                        'seating_capacity' => $svc['seating_capacity'] ?? null,
                        'stage_dimensions' => $svc['stage_dimensions'] ?? null,
                        'stage_type' => $svc['stage_type'] ?? null,
                        'available_facilities' => !empty($af) ? implode(', ', $af) : null,
                        'technical_facilities' => !empty($tf) ? implode(', ', $tf) : null,
                        'equipment_rent' => $svc['equipment_rent'] ?? null,
                        'stage_crew_available' => $svc['stage_crew_available'] ?? null,
                        'location_address' => $svc['location_address'] ?? null,
                        'theatre_photos' => $svc['theatre_photos'] ?? null,
                    ],
                ];

            case 'lighting design':
                return [
                    'table' => 'service_lighting_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'lighting_equipment_provided' => $svc['lighting_equipment_provided'] ?? null,
                        'max_stage_size' => $svc['max_stage_size'] ?? null,
                        'lighting_design_service' => $svc['lighting_design_service'] ?? null,
                        'lighting_crew_available' => $svc['lighting_crew_available'] ?? null,
                    ],
                ];

            case 'sound systems':
                return [
                    'table' => 'service_sound_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'sound_equipment_provided' => $svc['sound_equipment_provided'] ?? null,
                        'max_audience_size' => $svc['max_audience_size'] ?? null,
                        'sound_effects_handling' => $svc['sound_effects_handling'] ?? null,
                        'sound_engineer_included' => $svc['sound_engineer_included'] ?? null,
                        'equipment_brands' => $svc['equipment_brands'] ?? null,
                    ],
                ];

            case 'video production':
                return [
                    'table' => 'service_video_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'services_offered' => $svc['services_offered'] ?? null,
                        'equipment_used' => $svc['equipment_used'] ?? null,
                        'num_crew_members' => $svc['num_crew_members'] ?? null,
                        'editing_software' => $svc['editing_software'] ?? null,
                        'drone_service_available' => $svc['drone_service_available'] ?? null,
                        'max_video_resolution' => $svc['max_video_resolution'] ?? null,
                        'photo_editing_included' => $svc['photo_editing_included'] ?? null,
                        'delivery_time' => $svc['delivery_time'] ?? null,
                        'raw_footage_provided' => $svc['raw_footage_provided'] ?? null,
                        'portfolio_links' => $svc['portfolio_links'] ?? null,
                        'sample_videos' => $svc['sample_videos'] ?? null,
                    ],
                ];

            case 'set design':
                return [
                    'table' => 'service_set_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'types_of_sets_designed' => $svc['types_of_sets_designed'] ?? null,
                        'set_construction_provided' => $svc['set_construction_provided'] ?? null,
                        'stage_installation_support' => $svc['stage_installation_support'] ?? null,
                        'max_stage_size_supported' => $svc['max_stage_size_supported'] ?? null,
                        'materials_used' => $svc['materials_used'] ?? null,
                        'sample_set_designs' => $svc['sample_set_designs'] ?? null,
                    ],
                ];

            case 'costume design':
                return [
                    'table' => 'service_costume_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'types_of_costumes_provided' => $svc['types_of_costumes_provided'] ?? null,
                        'custom_costume_design_available' => $svc['custom_costume_design_available'] ?? null,
                        'available_sizes' => $svc['available_sizes'] ?? null,
                        'alterations_provided' => $svc['alterations_provided'] ?? null,
                        'number_of_costumes_available' => $svc['number_of_costumes_available'] ?? null,
                    ],
                ];

            case 'makeup & hair':
                return [
                    'table' => 'service_makeup_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'type_of_makeup_services' => $svc['type_of_makeup_services'] ?? null,
                        'experience_stage_makeup_years' => $svc['experience_stage_makeup_years'] ?? null,
                        'character_based_makeup_available' => $svc['character_based_makeup_available'] ?? null,
                        'can_handle_full_cast' => $svc['can_handle_full_cast'] ?? null,
                        'maximum_actors_per_show' => $svc['maximum_actors_per_show'] ?? null,
                        'bring_own_makeup_kit' => $svc['bring_own_makeup_kit'] ?? null,
                        'onsite_service_available' => $svc['onsite_service_available'] ?? null,
                        'touchup_service_during_show' => $svc['touchup_service_during_show'] ?? null,
                        'traditional_cultural_makeup_expertise' => $svc['traditional_cultural_makeup_expertise'] ?? null,
                        'sample_makeup_photos' => $svc['sample_makeup_photos'] ?? null,
                    ],
                ];

            case 'other':
                return [
                    'table' => 'service_other_details',
                    'data' => [
                        'rate_per_hour' => isset($svc['rate']) ? (float)$svc['rate'] : null,
                        'rate_type' => $svc['rate_type'] ?? 'hourly',
                        'description' => $svc['description'] ?? null,
                        'service_type' => $svc['service_type'] ?? null,
                    ],
                ];

            default:
                return null;
        }
    }

    private function insertServiceDetail(int $serviceId, array $detail): void {
        if (empty($detail['table']) || !isset($detail['data'])) {
            return;
        }

        $columns = array_keys($detail['data']);
        $columnsSql = empty($columns) ? '' : ',' . implode(',', $columns);
        $placeholders = empty($columns) ? '' : ',:' . implode(',:', $columns);

        $sql = "INSERT INTO {$detail['table']} (service_id{$columnsSql}) VALUES (:service_id{$placeholders})";
        $this->db->query($sql);
        $this->db->bind(':service_id', $serviceId);
        foreach ($detail['data'] as $col => $val) {
            $this->db->bind(':' . $col, $val);
        }
        $this->db->execute();
    }

    private function upsertServiceDetail(int $serviceId, array $detail): void {
        if (empty($detail['table']) || !isset($detail['data'])) {
            return;
        }

        $columns = array_keys($detail['data']);
        $columnsSql = empty($columns) ? '' : ',' . implode(',', $columns);
        $placeholders = empty($columns) ? '' : ',:' . implode(',:', $columns);
        $updateParts = [];
        foreach ($columns as $col) {
            $updateParts[] = "$col = VALUES($col)";
        }
        $updateSql = empty($updateParts) ? '' : ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updateParts);

        $sql = "INSERT INTO {$detail['table']} (service_id{$columnsSql}) VALUES (:service_id{$placeholders}){$updateSql}";
        $this->db->query($sql);
        $this->db->bind(':service_id', $serviceId);
        foreach ($detail['data'] as $col => $val) {
            $this->db->bind(':' . $col, $val);
        }
        $this->db->execute();
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
                         social_media_link = :social_media_link, 
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
        $this->db->bind(':social_media_link', $website);
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
                GROUP_CONCAT(DISTINCT st.service_type SEPARATOR ', ') as services,
                GROUP_CONCAT(DISTINCT CONCAT(std.rate_per_hour, '|', std.rate_type) ORDER BY std.rate_per_hour SEPARATOR ', ') as rates
                FROM serviceprovider sp
                LEFT JOIN services s ON sp.user_id = s.provider_id
                LEFT JOIN service_types st ON s.service_type_id = st.service_type_id
                LEFT JOIN service_theater_details std ON s.id = std.service_id AND st.service_type = 'Theater Production'
                WHERE 1=1";

        // Apply filters
        if (!empty($filters['service_type'])) {
            $sql .= " AND st.service_type LIKE :service_type";
        }
        if (!empty($filters['location'])) {
            $sql .= " AND sp.location LIKE :location";
        }
        if (!empty($filters['availability'])) {
            $sql .= " AND sp.availability = :availability";
        }
        if (!empty($filters['min_rate']) || !empty($filters['max_rate'])) {
            $sql .= " AND std.rate_per_hour IS NOT NULL";
            if (!empty($filters['min_rate'])) {
                $sql .= " AND std.rate_per_hour >= :min_rate";
            }
            if (!empty($filters['max_rate'])) {
                $sql .= " AND std.rate_per_hour <= :max_rate";
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
        $this->db->query("SELECT s.*, st.service_type 
                          FROM services s 
                          LEFT JOIN service_types st ON s.service_type_id = st.service_type_id 
                          WHERE s.provider_id = :provider_id 
                          ORDER BY st.service_type ASC");
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }

    public function getProviderProjects($provider_id) {
        $this->db->query("SELECT * FROM projects WHERE provider_id = :provider_id ORDER BY year DESC");
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }}