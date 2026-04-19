<?php

class M_service_provider extends M_signup {

    private $tableExistsCache = [];

    /**
     * Initializes this model and inherited DB access from the signup base model.
     * Used automatically whenever service provider controllers instantiate this class.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Registers a new service provider account through the shared user registration flow.
     * Used during service provider signup submission.
     */
    public function register($full_name, $email, $password, $phone, $nic_photo = null, $nic_photo_back = null) {
        return $this->registerUser($full_name, $email, $password, $phone, 'service_provider', $nic_photo, $nic_photo_back);
    }

    /**
     * Checks whether a service provider already exists with the given email.
     * Used for duplicate-email validation in registration/update paths.
     */
    public function emailExists($email) {
        // Check duplicate email only among service providers.
        $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE email = :email AND role = 'service_provider'");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row && isset($row->cnt) ? ((int)$row->cnt > 0) : false;
    }

    /**
     * Checks whether a service provider already exists with the given full name.
     * Used when enforcing uniqueness/business constraints for profile identity.
     */
    public function nameExists($full_name) {
        // Count records with the same full name for this role.
        $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE full_name = :full_name AND role = 'service_provider'");
        $this->db->bind(':full_name', $full_name);
        $row = $this->db->single();
        return $row && isset($row->cnt) ? ((int)$row->cnt > 0) : false;
    }

    /**
     * Checks whether an email already exists in users regardless of role.
     * Used by flows that require global email uniqueness.
     */
    public function emailExistsInUsers($email) {
        // Global uniqueness check across all roles.
        $this->db->query("SELECT COUNT(*) AS cnt FROM users WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row && isset($row->cnt) ? ((int)$row->cnt > 0) : false;
    }

    /**
     * Returns user ID by email, optionally scoped to a role.
     * Used after signup or verification when only email is available.
     */
    public function getUserIdByEmail($email, $role = null) {
        // Use role-scoped lookup when role is provided.
        if ($role) {
            $this->db->query("SELECT id FROM users WHERE email = :email AND role = :role LIMIT 1");
            $this->db->bind(':role', $role);
        } else {
            $this->db->query("SELECT id FROM users WHERE email = :email LIMIT 1");
        }
        $this->db->bind(':email', $email);
        $row = $this->db->single();
        return $row ? (int)$row->id : false;
    }

    /**
     * Loads a complete provider profile by joining serviceprovider and users data.
     * Used by profile pages/edit forms to render current provider information.
     */
    public function getProviderById($user_id) {
        // Detect optional columns to keep this query backward-compatible with older schemas.
        $usersHasYears = $this->columnExists('users', 'years_experience');
        $serviceProviderHasYears = $this->columnExists('serviceprovider', 'years_experience');
        $usersHasBio = $this->columnExists('users', 'bio');
        $serviceProviderHasSummary = $this->columnExists('serviceprovider', 'professional_summary');

        // Build years expression dynamically based on available source columns.
        if ($usersHasYears && $serviceProviderHasYears) {
            $yearsExpr = 'COALESCE(sp.years_experience, u.years_experience)';
        } elseif ($serviceProviderHasYears) {
            $yearsExpr = 'sp.years_experience';
        } elseif ($usersHasYears) {
            $yearsExpr = 'u.years_experience';
        } else {
            $yearsExpr = 'NULL';
        }

        // Build professional summary expression dynamically for mixed DB versions.
        if ($usersHasBio && $serviceProviderHasSummary) {
            $summaryExpr = "COALESCE(NULLIF(u.bio, ''), sp.professional_summary)";
        } elseif ($usersHasBio) {
            $summaryExpr = 'u.bio';
        } elseif ($serviceProviderHasSummary) {
            $summaryExpr = 'sp.professional_summary';
        } else {
            $summaryExpr = 'NULL';
        }

        $this->db->query("SELECT
                            sp.user_id,
                            sp.professional_title,
                            sp.location,
                            sp.birthday,
                            sp.social_media_link,
                            sp.birthday,
                            {$yearsExpr} AS years_experience,
                            sp.availability,
                            sp.availability_notes,
                            sp.created_at,
                            sp.updated_at,
                            u.full_name,
                            u.email,
                            u.phone,
                            u.wphone,
                            u.nic_number,
                            u.nic_photo,
                            u.nic_photo_back,
                            u.profile_image,
                            {$summaryExpr} AS professional_summary
                          FROM serviceprovider sp
                          INNER JOIN users u ON u.id = sp.user_id
                          WHERE sp.user_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->single();
    }

    /**
     * Returns all service rows linked to a provider, including service type names.
     * Used on provider profile page and service management screens.
     */
    public function getServicesByProviderId($user_id) {
        // Join service type text so UI can render human-readable category names.
        $this->db->query("SELECT s.*, st.service_type 
                          FROM services s 
                          LEFT JOIN service_types st ON s.service_type_id = st.service_type_id 
                          WHERE s.provider_id = :user_id");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    /**
     * Returns all projects for a provider ordered by year.
     * Used in provider profile and project CRUD views.
     */
    public function getProjectsByProviderId($user_id) {
        // Show latest projects first on profile screens.
        $this->db->query("SELECT * FROM projects WHERE provider_id = :user_id ORDER BY year DESC");
        $this->db->bind(':user_id', $user_id);
        return $this->db->resultSet();
    }

    /**
     * Checks whether a serviceprovider profile row exists for a user.
     * Used to detect partial signups or missing profile records.
     */
    public function providerProfileExists($user_id) {
        $this->db->query("SELECT 1 FROM serviceprovider WHERE user_id = :user_id LIMIT 1");
        $this->db->bind(':user_id', (int)$user_id);
        return (bool)$this->db->single();
    }

    /**
     * Creates a minimal provider profile row when one is missing.
     * Used as a self-healing step before rendering provider profile pages.
     */
    public function bootstrapProviderProfile($user_id): bool {
        $providerId = (int)$user_id;
        if ($providerId <= 0) {
            return false;
        }

        // Skip insert if profile already exists.
        if ($this->providerProfileExists($providerId)) {
            return true;
        }

        $hasYears = $this->columnExists('serviceprovider', 'years_experience');

        $sql = "INSERT INTO serviceprovider (user_id, professional_title, location, social_media_link, availability, availability_notes";
        if ($hasYears) {
            $sql .= ", years_experience";
        }
        $sql .= ")
                 SELECT u.id, '', '', '', 1, ''";
        if ($hasYears) {
            $sql .= ", COALESCE(u.years_experience, 0)";
        }
        $sql .= "
                 FROM users u
                 WHERE u.id = :user_id AND u.role = 'service_provider'
                 ON DUPLICATE KEY UPDATE user_id = user_id";

        $this->db->query($sql);
        $this->db->bind(':user_id', $providerId);

        try {
            // Execute minimal profile bootstrap insert.
            return $this->db->execute();
        } catch (Exception $e) {
            error_log('bootstrapProviderProfile failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Removes partially created provider data across related tables.
     * Used when registration fails mid-process and cleanup is required.
     */
    public function cleanupIncompleteRegistration($user_id) {
        $providerId = (int)$user_id;

        try {
            // Remove dependent rows first, then base rows, inside one transaction.
            $this->db->beginTransaction();

            $this->db->query("DELETE FROM projects WHERE provider_id = :provider_id");
            $this->db->bind(':provider_id', $providerId);
            $this->db->execute();

            $this->db->query("DELETE FROM services WHERE provider_id = :provider_id");
            $this->db->bind(':provider_id', $providerId);
            $this->db->execute();

            $this->db->query("DELETE FROM serviceprovider WHERE user_id = :user_id");
            $this->db->bind(':user_id', $providerId);
            $this->db->execute();

            $this->db->query("DELETE FROM users WHERE id = :user_id AND role = 'service_provider'");
            $this->db->bind(':user_id', $providerId);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Roll back all partial deletions on failure.
            $this->db->rollBack();
            error_log('cleanupIncompleteRegistration failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Saves full provider profile plus selected services and projects.
     * Used by registration/profile completion flow as a multi-phase write.
     */
    public function saveFullProfile($provider, $user_id, $services = [], $projects = []) {
        $providerId = (int)$user_id;
        $serviceProviderHasYears = $this->columnExists('serviceprovider', 'years_experience');
        $usersHasYears = $this->columnExists('users', 'years_experience');
        $yearsExperience = isset($provider['years_experience']) && $provider['years_experience'] !== '' ? (int)$provider['years_experience'] : null;

        // Phase 1: Insert the provider profile (transactional)
        try {
            $this->db->beginTransaction();

            // Update users base identity/contact fields first.
            $usersUpdateSql = "UPDATE users SET
                                full_name = :full_name,
                                email = :email,
                                phone = :phone,
                                wphone = :phone,
                                nic_number = :nic_number,
                                nic_photo = :nic_photo,
                                nic_photo_back = :nic_photo_back,
                                bio = :bio";

            if ($usersHasYears) {
                $usersUpdateSql .= ", years_experience = :years_experience";
            }

            $usersUpdateSql .= " WHERE id = :user_id AND role = 'service_provider'";

            $this->db->query($usersUpdateSql);

            $this->db->bind(':user_id', $providerId);
            $this->db->bind(':full_name', $provider['full_name'] ?? null);
            $this->db->bind(':email', $provider['email'] ?? null);
            $this->db->bind(':phone', $provider['phone'] ?? null);
            $this->db->bind(':wphone', $provider['wphone'] ?? null);
            $this->db->bind(':nic_number', $provider['nic_number'] ?? null);
            $this->db->bind(':nic_photo', $provider['nic_photo'] ?? ($provider['nic_photo_front'] ?? null));
            $this->db->bind(':nic_photo_back', $provider['nic_photo_back'] ?? null);
            $this->db->bind(':bio', $provider['professional_summary'] ?? null);
            if ($usersHasYears) {
                $this->db->bind(':years_experience', $yearsExperience);
            }
            $this->db->execute();

            // Build provider upsert SQL using optional years_experience column.
            $serviceProviderColumns = ['user_id', 'professional_title', 'location', 'social_media_link', 'birthday','availability', 'availability_notes'];
            if ($serviceProviderHasYears) {
                $serviceProviderColumns[] = 'years_experience';
            }

            $serviceProviderSql = "INSERT INTO serviceprovider (" . implode(', ', $serviceProviderColumns) . ")
            VALUES (:user_id, :professional_title, :location, :social_media_link, :birthday, :availability, :availability_notes";

            if ($serviceProviderHasYears) {
                $serviceProviderSql .= ", :years_experience";
            }

            $serviceProviderSql .= ")
            ON DUPLICATE KEY UPDATE
                professional_title = VALUES(professional_title),
                location = VALUES(location),
                social_media_link = VALUES(social_media_link),
                birthday = VALUES(birthday),
                availability = VALUES(availability),
                availability_notes = VALUES(availability_notes)";

            if ($serviceProviderHasYears) {
                $serviceProviderSql .= ", years_experience = VALUES(years_experience)";
            }

            $this->db->query($serviceProviderSql);

            $this->db->bind(':user_id', $providerId);
            $this->db->bind(':professional_title', $provider['professional_title'] ?? null);
            $this->db->bind(':location', $provider['location'] ?? null);
            $this->db->bind(':social_media_link', $provider['website'] ?? null);
            $this->db->bind(':birthday', $provider['birthday'] ?? null);
                        $this->db->bind(':availability', isset($provider['availability']) ? (int)$provider['availability'] : 1);
            $this->db->bind(':availability_notes', $provider['availability_notes'] ?? null);
            if ($serviceProviderHasYears) {
                $this->db->bind(':years_experience', $yearsExperience);
            }

            // Commit provider base/profile write before best-effort child writes.
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
            // Replace existing services with submitted set.
            $this->db->query("DELETE FROM services WHERE provider_id = :provider_id");
            $this->db->bind(':provider_id', $providerId);
            $this->db->execute();

            if (!empty($services) && is_array($services)) {
                foreach ($services as $svc) {
                    if (!empty($svc['selected']) && !empty($svc['name'])) {
                        $typeId = $this->getServiceTypeIdByName($svc['name']);

                        // Insert base service row first.
                        $this->db->query("INSERT INTO services (provider_id, service_type_id)
                                          VALUES (:provider_id, :service_type_id)");
                        $this->db->bind(':provider_id', $providerId);
                        $this->db->bind(':service_type_id', $typeId);
                        $this->db->execute();

                        // Then insert matching detail row into the correct detail table.
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
            // Replace existing projects with submitted project list.
            $this->db->query("DELETE FROM projects WHERE provider_id = :provider_id");
            $this->db->bind(':provider_id', $providerId);
            $this->db->execute();

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
    /**
     * Creates a service row and writes service-type-specific detail data.
     * Used when provider adds a new offered service.
     */
    public function insertService($provider_id, $service_type_name, $description = '', $extras = []) {
        $providerId = (int)$provider_id;
        // Resolve service type FK (create type if it does not exist).
        $typeId = $this->getServiceTypeIdByName($service_type_name);

        // Insert service base row.
        $this->db->query("INSERT INTO services (provider_id, service_type_id)
                          VALUES (:provider_id, :service_type_id)");
        $this->db->bind(':provider_id', $providerId);
        $this->db->bind(':service_type_id', $typeId);
        $this->db->execute();

        // Build and insert detail payload if this service type has a detail table.
        $serviceId = $this->db->lastInsertId();
        $detail = $this->buildDetailPayload($service_type_name, $extras + ['description' => $description]);
        if ($serviceId && $detail) {
            $this->insertServiceDetail((int)$serviceId, $detail);
        }

        return (bool)$serviceId;
    }

    /**
     * Returns a single service record with resolved service type.
     * Used by service edit/delete actions to load target service.
     */
    public function getServiceById($service_id) {
        $this->db->query("SELECT s.*, st.service_type 
                          FROM services s 
                          LEFT JOIN service_types st ON s.service_type_id = st.service_type_id 
                          WHERE s.id = :id");
        $this->db->bind(':id', $service_id);
        return $this->db->single();
    }

    /**
     * Fetches detail row from the correct service detail table by type.
     * Used for rendering rich service-specific fields in profile/edit pages.
     */
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
            if ($this->tableExists($table)) {
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
        }

        // Fallback: detect by scanning all detail tables for legacy rows.
        foreach ($map as $label => $table) {
            if (!$this->tableExists($table)) {
                continue;
            }
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

    /**
     * Updates service base type and upserts matching service detail payload.
     * Used when a provider edits an existing service entry.
     */
    public function updateService($service_id, $service_type_name, $description = '', $extras = []) {
        // Update base service type FK.
        $typeId = $this->getServiceTypeIdByName($service_type_name);

        $this->db->query("UPDATE services SET 
            service_type_id = :service_type_id
            WHERE id = :id");

        $this->db->bind(':service_type_id', $typeId);
        $this->db->bind(':id', $service_id);
        $baseUpdated = $this->db->execute();

        // Upsert matching detail data for the selected service type.
        $detail = $this->buildDetailPayload($service_type_name, $extras + ['description' => $description]);
        if ($detail) {
            $this->upsertServiceDetail((int)$service_id, $detail);
        }

        return $baseUpdated;
    }

    /**
     * Resolves service type name to ID, creating the type when missing.
     * Used by insert/update service flows to keep foreign key valid.
     */
    private function getServiceTypeIdByName(string $serviceTypeName): ?int {
        $name = trim($serviceTypeName);
        if ($name === '') {
            return null;
        }

        // Reuse existing service type when present.
        $this->db->query("SELECT service_type_id FROM service_types WHERE LOWER(service_type) = LOWER(:name) LIMIT 1");
        $this->db->bind(':name', $name);
        $row = $this->db->single();
        if (!empty($row->service_type_id)) {
            return (int)$row->service_type_id;
        }

        // Create service type if it doesn't exist yet.
        $this->db->query("INSERT INTO service_types (service_type) VALUES (:name)");
        $this->db->bind(':name', $name);
        $this->db->execute();
        return (int)$this->db->lastInsertId();
    }

    /**
     * Maps generic form data into a normalized table/data payload per service type.
     * Used before inserting or updating any service detail row.
     */
    private function buildDetailPayload(string $serviceName, array $svc): ?array {
        $key = strtolower(trim($serviceName));
        // Convert generic form payload into service-specific table columns.
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

    /**
     * Inserts a new service detail row into the resolved detail table.
     * Used right after a new service base record is created.
     */
    private function insertServiceDetail(int $serviceId, array $detail): void {
        if (empty($detail['table']) || !isset($detail['data'])) {
            return;
        }

        // Guard against missing detail tables in partial migrations.
        if (!$this->tableExists($detail['table'])) {
            error_log('insertServiceDetail skipped. Missing table: ' . $detail['table']);
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

    /**
     * Inserts or updates a service detail row using ON DUPLICATE KEY logic.
     * Used during service edits to keep detail rows in sync.
     */
    private function upsertServiceDetail(int $serviceId, array $detail): void {
        if (empty($detail['table']) || !isset($detail['data'])) {
            return;
        }

        // Guard against missing detail tables in partial migrations.
        if (!$this->tableExists($detail['table'])) {
            error_log('upsertServiceDetail skipped. Missing table: ' . $detail['table']);
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

    /**
     * Deletes a service base record by ID.
     * Used by provider service delete action (detail tables rely on DB relations/rules).
     */
    public function deleteService($service_id) {
        $this->db->query("DELETE FROM services WHERE id = :id");
        $this->db->bind(':id', $service_id);
        return $this->db->execute();
    }

    // Project CRUD Methods
    /**
     * Inserts a provider project record.
     * Used when adding portfolio/history projects in provider profile.
     */
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

    /**
     * Returns a single project by ID.
     * Used by project edit and delete confirmation flows.
     */
    public function getProjectById($project_id) {
        $this->db->query("SELECT * FROM projects WHERE id = :id");
        $this->db->bind(':id', $project_id);
        return $this->db->single();
    }

    /**
     * Updates a provider project record.
     * Used when provider edits a previously added project.
     */
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

    /**
     * Deletes a project record by ID.
     * Used by provider project delete action.
     */
    public function deleteProject($project_id) {
        $this->db->query("DELETE FROM projects WHERE id = :id");
        $this->db->bind(':id', $project_id);
        return $this->db->execute();
    }

    // Provider Basic Info Update
    /**
     * Updates user and serviceprovider basic profile fields in one transaction.
     * Used by ServiceProviderProfile::editBasicInfo submit handler.
     */
    public function updateBasicInfo($provider_id, $full_name, $professional_title, $email, $phone, $wphone,
                                    $location, $website, $birthday, $years_experience, $professional_summary, 
                                    $availability, $availability_notes) {
        try {
            // Keep users + serviceprovider updates atomic.
            $this->db->beginTransaction();

            // Detect optional columns for compatibility across DB versions.
            $usersHasBio = $this->columnExists('users', 'bio');
            $serviceProviderHasYears = $this->columnExists('serviceprovider', 'years_experience');
            $serviceProviderHasSummary = $this->columnExists('serviceprovider', 'professional_summary');

            $usersUpdateSql = "UPDATE users SET
                             full_name = :full_name,
                             email = :email,
                             phone = :phone,
                             wphone = :wphone";
            if ($usersHasBio) {
                $usersUpdateSql .= ", bio = :bio";
            }
            $usersUpdateSql .= "
                             WHERE id = :user_id AND role = 'service_provider'";

            $this->db->query($usersUpdateSql);
            $this->db->bind(':full_name', $full_name);
            $this->db->bind(':email', $email);
            $this->db->bind(':phone', $phone);
            $this->db->bind(':wphone', $wphone);
            if ($usersHasBio) {
                $this->db->bind(':bio', $professional_summary);
            }
            $this->db->bind(':user_id', $provider_id);
            $this->db->execute();

            // Update provider-only fields in serviceprovider table.
            $serviceProviderUpdateSql = "UPDATE serviceprovider SET
                             professional_title = :professional_title,
                             location = :location,
                             social_media_link = :social_media_link,
                             birthday = :birthday,
                             availability = :availability,
                             availability_notes = :availability_notes";
            if ($serviceProviderHasYears) {
                $serviceProviderUpdateSql .= ", years_experience = :years_experience";
            }
            if ($serviceProviderHasSummary) {
                $serviceProviderUpdateSql .= ", professional_summary = :professional_summary";
            }
            $serviceProviderUpdateSql .= "
                             WHERE user_id = :user_id";

            $this->db->query($serviceProviderUpdateSql);
            $this->db->bind(':professional_title', $professional_title);
            $this->db->bind(':location', $location);
            $this->db->bind(':social_media_link', $website);
            $this->db->bind(':birthday', $birthday);
            if ($serviceProviderHasYears) {
                $this->db->bind(':years_experience', $years_experience);
            }
            if ($serviceProviderHasSummary) {
                $this->db->bind(':professional_summary', $professional_summary);
            }
            $this->db->bind(':availability', (int)$availability);
            $this->db->bind(':availability_notes', $availability_notes);
            $this->db->bind(':user_id', $provider_id);
            $this->db->execute();

            // Commit both table updates together.
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            // Roll back both updates if any step fails.
            $this->db->rollBack();
            error_log('updateBasicInfo failed: ' . $e->getMessage());
            return false;
        }
    }

    // Update password with current password verification
    /**
     * Verifies current password and updates to a new hashed password.
     * Used by service provider change-password flow.
     */
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
        
        // Hash and store new password only after current password is verified.
        $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $this->db->query("UPDATE users SET password = :password WHERE id = :user_id");
        $this->db->bind(':password', $new_password_hash);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Delete Provider Profile (with cascade)
    /**
     * Deletes provider-owned records and profile row inside a transaction.
     * Used when service provider chooses to delete their profile.
     */
    public function deleteProvider($provider_id) {
        try {
            // Delete in transaction so partial account removal does not occur.
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
            // Restore state if any delete fails.
            $this->db->rollBack();
            error_log('deleteProvider failed: ' . $e->getMessage());
            return false;
        }
    }

    // Update Profile Image (SEPARATE from business certificate)
    /**
     *Stores the profile image filename in users table.
     * Used after successful upload in profile image update action.
     */
    public function updateProfileImage($user_id, $filename) {
        $this->db->query("UPDATE users SET profile_image = :profile_image WHERE id = :user_id");
        $this->db->bind(':profile_image', $filename);
        $this->db->bind(':user_id', $user_id);
        return $this->db->execute();
    }

    // Browse/Search Methods for Production Managers
    /**
     * Returns searchable provider list with service/rate aggregates and filters.
     * Used by production manager browse service providers pages.
     */
    public function getAllProvidersWithServices($filters = []) {
        // Map detail tables so rate/rate_type can be resolved per service category.
        $detailTableMap = [
            ['table' => 'service_theater_details', 'alias' => 'std', 'service_type' => 'Theater Production'],
            ['table' => 'service_lighting_details', 'alias' => 'sld', 'service_type' => 'Lighting Design'],
            ['table' => 'service_sound_details', 'alias' => 'ssd', 'service_type' => 'Sound Systems'],
            ['table' => 'service_video_details', 'alias' => 'svd', 'service_type' => 'Video Production'],
            ['table' => 'service_set_details', 'alias' => 'ssetd', 'service_type' => 'Set Design'],
            ['table' => 'service_costume_details', 'alias' => 'scd', 'service_type' => 'Costume Design'],
            ['table' => 'service_makeup_details', 'alias' => 'smd', 'service_type' => 'Makeup & Hair'],
            ['table' => 'service_other_details', 'alias' => 'sod', 'service_type' => 'Other'],
        ];

        $ratePerHourParts = [];
        $rateTypeParts = [];
        $dynamicJoins = '';

        // Build joins/expressions only for tables that actually exist.
        foreach ($detailTableMap as $def) {
            if (!$this->tableExists($def['table'])) {
                continue;
            }
            $alias = $def['alias'];
            $serviceType = addslashes($def['service_type']);
            $dynamicJoins .= "\n                LEFT JOIN {$def['table']} {$alias} ON s.id = {$alias}.service_id AND st.service_type = '{$serviceType}'";
            $ratePerHourParts[] = "{$alias}.rate_per_hour";
            $rateTypeParts[] = "{$alias}.rate_type";
        }

        // Coalesce rates across all possible detail table aliases.
        $rateExpr = !empty($ratePerHourParts) ? ('COALESCE(' . implode(', ', $ratePerHourParts) . ')') : 'NULL';
        $rateTypeExpr = !empty($rateTypeParts) ? ('COALESCE(' . implode(', ', $rateTypeParts) . ')') : "'hourly'";

        $sql = "SELECT DISTINCT
            sp.user_id,
            sp.professional_title,
            sp.location,
            sp.social_media_link,
            sp.availability,
            sp.availability_notes,
            u.full_name,
            u.email,
            u.phone,
            u.bio AS professional_summary,
            u.years_experience,
            u.nic_number,
            u.nic_photo AS nic_photo_front,
            u.nic_photo_back,
            u.profile_image,
                GROUP_CONCAT(DISTINCT st.service_type SEPARATOR ', ') as services,
                GROUP_CONCAT(DISTINCT CONCAT({$rateExpr}, '|', {$rateTypeExpr}) ORDER BY {$rateExpr} SEPARATOR ', ') as rates
                FROM serviceprovider sp
            INNER JOIN users u ON u.id = sp.user_id
                LEFT JOIN services s ON sp.user_id = s.provider_id
                LEFT JOIN service_types st ON s.service_type_id = st.service_type_id
                {$dynamicJoins}
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
            $sql .= " AND {$rateExpr} IS NOT NULL";
            if (!empty($filters['min_rate'])) {
                $sql .= " AND {$rateExpr} >= :min_rate";
            }
            if (!empty($filters['max_rate'])) {
                $sql .= " AND {$rateExpr} <= :max_rate";
            }
        }

        $sql .= " GROUP BY sp.user_id ORDER BY u.full_name ASC";

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

        // Return final provider list for browse/search UI.
        return $this->db->resultSet();
    }

    /**
     * Checks information_schema to see whether a table exists, with in-memory caching.
     * Used by dynamic detail-table logic and backward-compatible queries.
     */
    private function tableExists(string $tableName): bool {
        // Return cached result if previously checked in this request lifecycle.
        if (isset($this->tableExistsCache[$tableName])) {
            return $this->tableExistsCache[$tableName];
        }

        $this->db->query("SELECT COUNT(*) AS cnt
                          FROM information_schema.tables
                          WHERE table_schema = DATABASE() AND table_name = :table_name");
        $this->db->bind(':table_name', $tableName);
        $row = $this->db->single();

        $exists = $row && isset($row->cnt) && (int)$row->cnt > 0;
        // Cache to avoid repeated information_schema queries.
        $this->tableExistsCache[$tableName] = $exists;
        return $exists;
    }

    /**
     * Checks information_schema for a column and caches the result.
     * Used to support schema differences across deployments/migrations.
     */
    private function columnExists(string $tableName, string $columnName): bool {
        $cacheKey = $tableName . '.' . $columnName;
        // Return cached result if available.
        if (isset($this->tableExistsCache[$cacheKey])) {
            return $this->tableExistsCache[$cacheKey];
        }

        $this->db->query("SELECT COUNT(*) AS cnt
                          FROM information_schema.columns
                          WHERE table_schema = DATABASE()
                            AND table_name = :table_name
                            AND column_name = :column_name");
        $this->db->bind(':table_name', $tableName);
        $this->db->bind(':column_name', $columnName);
        $row = $this->db->single();

        $exists = $row && isset($row->cnt) && (int)$row->cnt > 0;
        // Cache to reduce repeated schema lookups.
        $this->tableExistsCache[$cacheKey] = $exists;
        return $exists;
    }

    /**
     * Returns distinct provider locations for browse filter dropdowns.
     * Used in provider search/filter UI.
     */
    public function getAllLocations() {
        $this->db->query("SELECT DISTINCT location FROM serviceprovider WHERE location IS NOT NULL ORDER BY location ASC");
        return $this->db->resultSet();
    }

    /**
     * Returns all services for a provider ordered by service type.
     * Used by profile and detail pages that list provider service offerings.
     */
    public function getProviderServices($provider_id) {
        $this->db->query("SELECT s.*, st.service_type 
                          FROM services s 
                          LEFT JOIN service_types st ON s.service_type_id = st.service_type_id 
                          WHERE s.provider_id = :provider_id 
                          ORDER BY st.service_type ASC");
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }

    /**
     * Returns all projects for a provider sorted by most recent year.
     * Used by provider public/profile project history sections.
     */
    public function getProviderProjects($provider_id) {
        $this->db->query("SELECT * FROM projects WHERE provider_id = :provider_id ORDER BY year DESC");
        $this->db->bind(':provider_id', $provider_id);
        return $this->db->resultSet();
    }
}