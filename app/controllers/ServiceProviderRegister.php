<?php

class ServiceProviderRegister
{
    use Controller;

    public function index()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $nic_photo_front = $_FILES['nic_photo_front']['name'] ?? null;
            $nic_photo_back = $_FILES['nic_photo_back']['name'] ?? null;

            // 🔹 Basic validation
            if (empty($full_name)) {
                $errors[] = "Full name is required.";
            }
            if (empty($email)) {
                $errors[] = "Email is required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
            if (strlen($password) < 6) {
                $errors[] = "Password must be at least 6 characters.";
            }
            if (empty($phone)) {
                $errors[] = "Phone number is required.";
            }
            if (!$nic_photo_front) {
                $errors[] = "NIC front photo is required.";
            }
            if (!$nic_photo_back) {
                $errors[] = "NIC back photo is required.";
            }

            if (empty($errors)) {
                $model = new M_service_provider();

                // 🔹 Handle file uploads (save to public/uploads/nic_photos/)
                $uploadDir = __DIR__ . '/../../public/uploads/nic_photos/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true); // create folder if missing
                }

                $frontName = uniqid() . "_front_" . basename($nic_photo_front);
                $backName = uniqid() . "_back_" . basename($nic_photo_back);
                $frontPath = $uploadDir . $frontName;
                $backPath = $uploadDir . $backName;
                $frontDbPath = 'uploads/nic_photos/' . $frontName;
                $backDbPath = 'uploads/nic_photos/' . $backName;

                if (is_uploaded_file($_FILES['nic_photo_front']['tmp_name'])) {
                    if (!move_uploaded_file($_FILES['nic_photo_front']['tmp_name'], $frontPath)) {
                        $errors[] = "Failed to upload NIC front photo.";
                    }
                }

                if (is_uploaded_file($_FILES['nic_photo_back']['tmp_name'])) {
                    if (!move_uploaded_file($_FILES['nic_photo_back']['tmp_name'], $backPath)) {
                        $errors[] = "Failed to upload NIC back photo.";
                    }
                }

                if (empty($errors)) {
                    // 🔹 Save provider to DB
                    if ($model->register($full_name, $email, $password, $phone, $frontDbPath, $backDbPath)) {
                        echo "<script>
                                alert('Service Provider registered successfully!');
                                window.location = '" . ROOT . "/login';
                              </script>";
                        exit;
                    } else {
                        $errors[] = "Registration failed. Try again.";
                    }
                }
            }
        }

        // 🔹 Send errors to the view
        $this->view('service_provider_register', ['errors' => $errors]);
    }

    public function submit()
    {
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/ServiceProviderRegister');
            exit;
        }

        $model = new M_service_provider();

        // Collect fields
        $provider = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'professional_title' => trim($_POST['professional_title'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'location' => trim($_POST['location'] ?? ''),
            'nic_number' => trim($_POST['nic_number'] ?? ''),
            'website' => trim($_POST['website'] ?? ''),
            'years_experience' => $_POST['years_experience'] ?? '',
            'professional_summary' => trim($_POST['professional_summary'] ?? ''),
            'availability' => isset($_POST['availability']) ? (int)$_POST['availability'] : 1,
            'availability_notes' => trim($_POST['availability_notes'] ?? ''),
            'nic_photo_front' => null,
            'nic_photo_back' => null,
        ];

        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $existingCertFront = trim($_POST['existing_nic_photo_front'] ?? '');
        $existingCertBack = trim($_POST['existing_nic_photo_back'] ?? '');

        // Basic validations
        if ($provider['full_name'] === '') $errors[] = 'Full name is required.';
        if ($provider['professional_title'] === '') $errors[] = 'Professional title is required.';
        if ($provider['email'] === '' || !filter_var($provider['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if ($provider['phone'] === '') $errors[] = 'Phone number is required.';
        
        // NIC number validation
        if ($provider['nic_number'] === '') {
            $errors[] = 'NIC number is required.';
        } else {
            // Validate NIC format (Old: 9 digits + V/X, New: 12 digits)
            $nicPattern = '/^(?:\d{9}[VvXx]|\d{12})$/';
            if (!preg_match($nicPattern, $provider['nic_number'])) {
                $errors[] = 'Invalid NIC format. Use old format (e.g., 199512345V) or new format (e.g., 200012345678).';
            }
        }

        // Password validations
        if (empty($password) || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
        if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';

        // Handle NIC photo front upload
        if (!empty($existingCertFront) && empty($_FILES['nic_photo_front']['name'])) {
            $provider['nic_photo_front'] = $existingCertFront;
        } elseif (!empty($_FILES['nic_photo_front']['name'])) {
            $targetDir = __DIR__ . '/../../public/uploads/nic_photos/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = uniqid() . '_front_' . basename($_FILES['nic_photo_front']['name']);
            $targetFile = $targetDir . $fileName;

            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($_FILES['nic_photo_front']['size'] > $maxSize) {
                $errors[] = 'NIC front photo must be less than 5MB.';
            } elseif (!in_array($_FILES['nic_photo_front']['type'], ['image/jpeg', 'image/png', 'image/jpg'])) {
                $errors[] = 'NIC front photo must be JPG or PNG.';
            } elseif (is_uploaded_file($_FILES['nic_photo_front']['tmp_name'])) {
                if (move_uploaded_file($_FILES['nic_photo_front']['tmp_name'], $targetFile)) {
                    $provider['nic_photo_front'] = 'uploads/nic_photos/' . $fileName;
                } else {
                    $errors[] = 'Failed to upload NIC front photo.';
                }
            } else {
                $errors[] = 'Invalid file upload. Please try again.';
            }
        } else {
            $errors[] = 'NIC front photo is required.';
        }

        // Handle NIC photo back upload
        if (!empty($existingCertBack) && empty($_FILES['nic_photo_back']['name'])) {
            $provider['nic_photo_back'] = $existingCertBack;
        } elseif (!empty($_FILES['nic_photo_back']['name'])) {
            $targetDir = __DIR__ . '/../../public/uploads/nic_photos/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $fileName = uniqid() . '_back_' . basename($_FILES['nic_photo_back']['name']);
            $targetFile = $targetDir . $fileName;

            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($_FILES['nic_photo_back']['size'] > $maxSize) {
                $errors[] = 'NIC back photo must be less than 5MB.';
            } elseif (!in_array($_FILES['nic_photo_back']['type'], ['image/jpeg', 'image/png', 'image/jpg'])) {
                $errors[] = 'NIC back photo must be JPG or PNG.';
            } elseif (is_uploaded_file($_FILES['nic_photo_back']['tmp_name'])) {
                if (move_uploaded_file($_FILES['nic_photo_back']['tmp_name'], $targetFile)) {
                    $provider['nic_photo_back'] = 'uploads/nic_photos/' . $fileName;
                } else {
                    $errors[] = 'Failed to upload NIC back photo.';
                }
            } else {
                $errors[] = 'Invalid file upload. Please try again.';
            }
        } else {
            $errors[] = 'NIC back photo is required.';
        }

        // Duplicate checks
        if (empty($errors)) {
            // Check email in both serviceprovider and users tables
            if ($model->emailExists($provider['email']) || $model->emailExistsInUsers($provider['email'])) {
                $errors[] = 'This email is already registered. Please use a different email.';
            }
            if ($model->nameExists($provider['full_name'])) {
                $errors[] = 'A service provider with this full name is already registered.';
            }
        }

        // If there are errors, return form with data preserved
        if (!empty($errors)) {
            $this->view('service_provider_register', [
                'errors' => $errors,
                'formData' => $provider,
                'password' => $password,
                'confirm_password' => $confirm_password,
                'services' => $this->processServicesData($_POST['services'] ?? []),
                'projects' => $_POST['projects'] ?? [],
                'uploadedPhotoFront' => $provider['nic_photo_front'] ?? $existingCertFront,
                'uploadedPhotoBack' => $provider['nic_photo_back'] ?? $existingCertBack,
            ]);
            return;
        }

        // First, register user in users table
        $userRegistered = $model->registerUser($provider['full_name'], $provider['email'], $password, $provider['phone'], 'service_provider');
        
        if (!$userRegistered) {
            $this->view('service_provider_register', ['errors' => ['Email already exists or registration failed. Please try again.']]);
            return;
        }

        // Get the newly created user ID
        $user_id = $model->getUserIdByEmail($provider['email']);
        if (!$user_id) {
            $this->view('service_provider_register', ['errors' => ['Failed to retrieve user ID. Please try again.']]);
            return;
        }

        // Then save serviceprovider profile with user_id
        $services = $this->processServicesData($_POST['services'] ?? []);
        $projects = $_POST['projects'] ?? [];

        $savedId = $model->saveFullProfile($provider, $user_id, $services, $projects);

        if ($savedId) {
            header('Location: ' . ROOT . '/Login?registered=1');
            exit;
        } else {
            $this->view('service_provider_register', ['errors' => ['Failed to save your profile. Please try again.']]);
        }
    }

    /**
     * Process services data to ensure rate_type is included
     */
    private function processServicesData($services) {
        if (empty($services) || !is_array($services)) {
            return [];
        }

        $processed = [];
        foreach ($services as $idx => $svc) {
            $processed[$idx] = [
                'selected' => isset($svc['selected']) ? 1 : 0,
                'name' => $svc['name'] ?? '',
                'rate' => $svc['rate'] ?? '',
                'rate_type' => $svc['rate_type'] ?? 'hourly',
                'description' => $svc['description'] ?? '',
                // Theater Production fields
                'theatre_name' => $svc['theatre_name'] ?? null,
                'seating_capacity' => $svc['seating_capacity'] ?? null,
                'stage_dimensions' => $svc['stage_dimensions'] ?? null,
                'stage_type' => $svc['stage_type'] ?? null,
                'available_facilities' => $svc['available_facilities'] ?? [],
                'technical_facilities' => $svc['technical_facilities'] ?? [],
                'equipment_rent' => $svc['equipment_rent'] ?? null,
                'stage_crew_available' => $svc['stage_crew_available'] ?? null,
                'location_address' => $svc['location_address'] ?? null,
                'lighting_equipment_provided' => $svc['lighting_equipment_provided'] ?? null,
                'max_stage_size' => $svc['max_stage_size'] ?? null,
                'lighting_design_service' => $svc['lighting_design_service'] ?? null,
                'lighting_crew_available' => $svc['lighting_crew_available'] ?? null,
                'sound_equipment_provided' => $svc['sound_equipment_provided'] ?? null,
                'max_audience_size' => $svc['max_audience_size'] ?? null,
                'sound_effects_handling' => $svc['sound_effects_handling'] ?? null,
                'sound_engineer_included' => $svc['sound_engineer_included'] ?? null,
                'equipment_brands' => $svc['equipment_brands'] ?? null,
                // New video production fields
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
                // Set Design fields
                'types_of_sets_designed' => $svc['types_of_sets_designed'] ?? null,
                'set_construction_provided' => $svc['set_construction_provided'] ?? null,
                'stage_installation_support' => $svc['stage_installation_support'] ?? null,
                'max_stage_size_supported' => $svc['max_stage_size_supported'] ?? null,
                'materials_used' => $svc['materials_used'] ?? null,
                // New costume fields
                'types_of_costumes_provided' => $svc['types_of_costumes_provided'] ?? null,
                'custom_costume_design_available' => $svc['custom_costume_design_available'] ?? null,
                'available_sizes' => $svc['available_sizes'] ?? null,
                'alterations_provided' => $svc['alterations_provided'] ?? null,
                'number_of_costumes_available' => $svc['number_of_costumes_available'] ?? null,
                // New makeup fields
                'type_of_makeup_services' => $svc['type_of_makeup_services'] ?? null,
                'experience_stage_makeup_years' => $svc['experience_stage_makeup_years'] ?? null,
                'character_based_makeup_available' => $svc['character_based_makeup_available'] ?? null,
                'can_handle_full_cast' => $svc['can_handle_full_cast'] ?? null,
                'maximum_actors_per_show' => $svc['maximum_actors_per_show'] ?? null,
                'bring_own_makeup_kit' => $svc['bring_own_makeup_kit'] ?? null,
                'onsite_service_available' => $svc['onsite_service_available'] ?? null,
                'touchup_service_during_show' => $svc['touchup_service_during_show'] ?? null,
                'traditional_cultural_makeup_expertise' => $svc['traditional_cultural_makeup_expertise'] ?? null,
                // Other service type field
                'service_type' => $svc['service_type'] ?? null,
            ];

            // Handle theatre photos file upload if present for this index
            if (isset($_FILES['services']['name'][$idx]['theatre_photos']) && !empty($_FILES['services']['name'][$idx]['theatre_photos'][0])) {
                $fileName = $_FILES['services']['name'][$idx]['theatre_photos'][0];
                $tmpName = $_FILES['services']['tmp_name'][$idx]['theatre_photos'][0] ?? null;
                $size = $_FILES['services']['size'][$idx]['theatre_photos'][0] ?? 0;
                $type = $_FILES['services']['type'][$idx]['theatre_photos'][0] ?? '';
                if ($tmpName && is_uploaded_file($tmpName)) {
                    $targetDir = __DIR__ . '/../../public/uploads/theatre_photos/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('theatre_');
                    $targetFile = $targetDir . $unique . '_' . basename($fileName);
                    $allowed = ['image/jpeg','image/png','image/jpg'];
                    $maxSize = 10 * 1024 * 1024;
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmpName, $targetFile)) {
                            $processed[$idx]['theatre_photos'] = 'uploads/theatre_photos/' . $unique . '_' . basename($fileName);
                        }
                    }
                }
            }

            // Handle set design sample file upload if present for this index
            if (isset($_FILES['services']['name'][$idx]['sample_set_designs']) && !empty($_FILES['services']['name'][$idx]['sample_set_designs'])) {
                $fileName = $_FILES['services']['name'][$idx]['sample_set_designs'];
                $tmpName = $_FILES['services']['tmp_name'][$idx]['sample_set_designs'] ?? null;
                $size = $_FILES['services']['size'][$idx]['sample_set_designs'] ?? 0;
                $type = $_FILES['services']['type'][$idx]['sample_set_designs'] ?? '';
                if ($tmpName && is_uploaded_file($tmpName)) {
                    $targetDir = __DIR__ . '/../../public/uploads/set_designs/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('set_');
                    $targetFile = $targetDir . $unique . '_' . basename($fileName);
                    $allowed = ['image/jpeg','image/png','image/jpg','application/pdf'];
                    $maxSize = 10 * 1024 * 1024;
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmpName, $targetFile)) {
                            $processed[$idx]['sample_set_designs'] = 'uploads/set_designs/' . $unique . '_' . basename($fileName);
                        }
                    }
                }
            }

            // Handle makeup photos file upload if present for this index
            if (isset($_FILES['services']['name'][$idx]['sample_makeup_photos']) && !empty($_FILES['services']['name'][$idx]['sample_makeup_photos'])) {
                $fileName = $_FILES['services']['name'][$idx]['sample_makeup_photos'];
                $tmpName = $_FILES['services']['tmp_name'][$idx]['sample_makeup_photos'] ?? null;
                $size = $_FILES['services']['size'][$idx]['sample_makeup_photos'] ?? 0;
                $type = $_FILES['services']['type'][$idx]['sample_makeup_photos'] ?? '';
                if ($tmpName && is_uploaded_file($tmpName)) {
                    $targetDir = __DIR__ . '/../../public/uploads/makeup_photos/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('makeup_');
                    $targetFile = $targetDir . $unique . '_' . basename($fileName);
                    $allowed = ['image/jpeg','image/png','image/jpg'];
                    $maxSize = 10 * 1024 * 1024;
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmpName, $targetFile)) {
                            $processed[$idx]['sample_makeup_photos'] = 'uploads/makeup_photos/' . $unique . '_' . basename($fileName);
                        }
                    }
                }
            }

            // Handle sample videos file upload if present for this index
            if (isset($_FILES['services']['name'][$idx]['sample_videos']) && !empty($_FILES['services']['name'][$idx]['sample_videos'])) {
                $fileName = $_FILES['services']['name'][$idx]['sample_videos'];
                $tmpName = $_FILES['services']['tmp_name'][$idx]['sample_videos'] ?? null;
                $size = $_FILES['services']['size'][$idx]['sample_videos'] ?? 0;
                $type = $_FILES['services']['type'][$idx]['sample_videos'] ?? '';
                if ($tmpName && is_uploaded_file($tmpName)) {
                    $targetDir = __DIR__ . '/../../public/uploads/sample_videos/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('video_');
                    $targetFile = $targetDir . $unique . '_' . basename($fileName);
                    $allowed = ['image/jpeg','image/png','image/jpg','video/mp4','video/quicktime','application/x-mov'];
                    $maxSize = 500 * 1024 * 1024; // 500MB for videos
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmpName, $targetFile)) {
                            $processed[$idx]['sample_videos'] = 'uploads/sample_videos/' . $unique . '_' . basename($fileName);
                        }
                    }
                }
            }
        }
        return $processed;
    }
}
