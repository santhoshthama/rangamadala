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
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $nic_number = trim($_POST['nic_number'] ?? '');
            $nic_photo_front = $_FILES['nic_photo']['name'] ?? ($_FILES['nic_photo_front']['name'] ?? null);
            $nic_photo_back = $_FILES['nic_photo_back']['name'] ?? null;

            $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/';
            $phonePattern = '/^(?:\+94|94|0)7\d{8}$/';
            $nicPattern = '/^(?:\d{12}|\d{9}[Vv])$/';

            // 🔹 Basic validation
            if (empty($full_name)) {
                $errors[] = "Full name is required.";
            }
            if (empty($email)) {
                $errors[] = "Email is required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format.";
            }
            if (!preg_match($passwordPattern, $password)) {
                $errors[] = "Password must be at least 6 characters and include uppercase, lowercase, number, and symbol.";
            }
            if ($password !== $confirm_password) {
                $errors[] = "Password confirmation does not match.";
            }
            if (empty($phone)) {
                $errors[] = "Phone number is required.";
            } elseif (!preg_match($phonePattern, $phone)) {
                $errors[] = "Enter a valid Sri Lankan mobile number (e.g. 07X XXX XXXX or +94 XXX XXX XXX).";
            }
            if (empty($nic_number)) {
                $errors[] = "NIC number is required.";
            } elseif (!preg_match($nicPattern, $nic_number)) {
                $errors[] = "Enter a valid Sri Lankan NIC (12 digits or old format ending with V).";
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

                $frontUpload = $_FILES['nic_photo'] ?? $_FILES['nic_photo_front'] ?? null;

                if (!empty($frontUpload['tmp_name']) && is_uploaded_file($frontUpload['tmp_name'])) {
                    if (!move_uploaded_file($frontUpload['tmp_name'], $frontPath)) {
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

        // Send errors to the view
        $this->view('service_provider_register', ['errors' => $errors]);
    }

    public function submit()
    {
        $errors = [];
        $fieldErrors = [];

        $addFieldError = function ($field, $message) use (&$fieldErrors, &$errors) {
            if (!isset($fieldErrors[$field])) {
                $fieldErrors[$field] = $message;
            }
            $errors[] = $message;
        };

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
        $existingCertFront = trim($_POST['existing_nic_photo'] ?? ($_POST['existing_nic_photo_front'] ?? ''));
        $existingCertBack = trim($_POST['existing_nic_photo_back'] ?? '');
        $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{6,}$/';
        $phonePattern = '/^(?:\+94|94|0)7\d{8}$/';
        $nicPattern = '/^(?:\d{12}|\d{9}[Vv])$/';

        // Basic validations
        if ($provider['full_name'] === '') {
            $addFieldError('full_name', 'Full name is required.');
        }
        if ($provider['email'] === '') {
            $addFieldError('email', 'Email is required.');
        } elseif (!filter_var($provider['email'], FILTER_VALIDATE_EMAIL)) {
            $addFieldError('email', 'Valid email is required.');
        }
        if ($provider['phone'] === '') {
            $addFieldError('phone', 'Phone number is required.');
        } elseif (!preg_match($phonePattern, $provider['phone'])) {
            $addFieldError('phone', 'Enter a valid Sri Lankan mobile number (e.g. 07X XXX XXXX or +94 XXX XXX XXX).');
        }
        if ($provider['years_experience'] === '') {
            $addFieldError('years_experience', 'Years of experience is required.');
        } elseif (!is_numeric($provider['years_experience']) || (int)$provider['years_experience'] < 0) {
            $addFieldError('years_experience', 'Years of experience must be a valid non-negative number.');
        }
        
        if ($provider['nic_number'] === '') {
            $addFieldError('nic_number', 'NIC number is required.');
        } elseif (!preg_match($nicPattern, $provider['nic_number'])) {
            $addFieldError('nic_number', 'Enter a valid Sri Lankan NIC (12 digits or old format ending with V).');
        }

        if (!preg_match($passwordPattern, $password)) {
            $addFieldError('password', 'Password must be at least 6 characters and include uppercase, lowercase, number, and symbol.');
        }
        if ($password !== $confirm_password) {
            $addFieldError('confirm_password', 'Password confirmation does not match.');
        }

        // Handle NIC photo front upload
        if (!empty($existingCertFront) && empty($_FILES['nic_photo']['name'] ?? $_FILES['nic_photo_front']['name'] ?? '')) {
            $provider['nic_photo'] = $existingCertFront;
        } elseif (!empty($_FILES['nic_photo']['name'] ?? $_FILES['nic_photo_front']['name'] ?? '')) {
            $targetDir = __DIR__ . '/../../public/uploads/nic_photos/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $frontUpload = $_FILES['nic_photo'] ?? $_FILES['nic_photo_front'];
            $fileName = uniqid() . '_front_' . basename($frontUpload['name']);
            $targetFile = $targetDir . $fileName;

            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($frontUpload['size'] > $maxSize) {
                $addFieldError('nic_photo', 'NIC front photo must be less than 5MB.');
            } elseif (!in_array($frontUpload['type'], ['image/jpeg', 'image/png', 'image/jpg'])) {
                $addFieldError('nic_photo', 'NIC front photo must be JPG or PNG.');
            } elseif (is_uploaded_file($frontUpload['tmp_name'])) {
                if (move_uploaded_file($frontUpload['tmp_name'], $targetFile)) {
                    $provider['nic_photo'] = 'uploads/nic_photos/' . $fileName;
                } else {
                    $addFieldError('nic_photo', 'Failed to upload NIC front photo.');
                }
            } else {
                $addFieldError('nic_photo', 'Invalid NIC front file upload. Please try again.');
            }
        } else {
            $addFieldError('nic_photo', 'NIC front photo is required.');
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
                $addFieldError('nic_photo_back', 'NIC back photo must be less than 5MB.');
            } elseif (!in_array($_FILES['nic_photo_back']['type'], ['image/jpeg', 'image/png', 'image/jpg'])) {
                $addFieldError('nic_photo_back', 'NIC back photo must be JPG or PNG.');
            } elseif (is_uploaded_file($_FILES['nic_photo_back']['tmp_name'])) {
                if (move_uploaded_file($_FILES['nic_photo_back']['tmp_name'], $targetFile)) {
                    $provider['nic_photo_back'] = 'uploads/nic_photos/' . $fileName;
                } else {
                    $addFieldError('nic_photo_back', 'Failed to upload NIC back photo.');
                }
            } else {
                $addFieldError('nic_photo_back', 'Invalid NIC back file upload. Please try again.');
            }
        } else {
            $addFieldError('nic_photo_back', 'NIC back photo is required.');
        }

        $services = $this->processServicesData($_POST['services'] ?? []);
        $hasAtLeastOneService = false;
        foreach ($services as $service) {
            if (!empty($service['selected'])) {
                $hasAtLeastOneService = true;
                break;
            }
        }
        if (!$hasAtLeastOneService) {
            $addFieldError('services', 'Please select at least one service.');
        }

        // Duplicate email check across users table
        if (empty($errors) && $model->emailExistsInUsers($provider['email'])) {
            $addFieldError('email', 'This email is already registered. Please use a different email.');
        }

        // If there are errors, return form with data preserved
        if (!empty($errors)) {
            $this->view('service_provider_register', [
                'errors' => $errors,
                'fieldErrors' => $fieldErrors,
                'firstErrorField' => array_key_first($fieldErrors),
                'formData' => $provider,
                'password' => $password,
                'confirm_password' => $confirm_password,
                'services' => $services,
                'projects' => $_POST['projects'] ?? [],
                'uploadedPhotoFront' => $provider['nic_photo'] ?? ($provider['nic_photo_front'] ?? $existingCertFront),
                'uploadedPhotoBack' => $provider['nic_photo_back'] ?? $existingCertBack,
            ]);
            return;
        }

        // Insert into users table
        $userRegistered = $model->registerUser($provider['full_name'], $provider['email'], $password, $provider['phone'], 'service_provider');

        // Retrieve created account id
        $user_id = $model->getUserIdByEmail($provider['email'], 'service_provider');

        if (!$userRegistered || !$user_id) {
            $this->view('service_provider_register', [
                'errors' => ['Registration failed while creating user account. Please try again.'],
                'fieldErrors' => ['email' => 'Unable to create account with this email.'],
                'firstErrorField' => 'email',
                'formData' => $provider,
                'services' => $services,
                'projects' => $_POST['projects'] ?? [],
                'uploadedPhotoFront' => $provider['nic_photo'] ?? ($provider['nic_photo_front'] ?? $existingCertFront),
                'uploadedPhotoBack' => $provider['nic_photo_back'] ?? $existingCertBack,
            ]);
            return;
        }

        // Get the user ID for profile save
        if (!$user_id) {
            $this->view('service_provider_register', ['errors' => ['Failed to retrieve user ID. Please try again.']]);
            return;
        }

        // Then save serviceprovider profile with user_id
        $projects = $_POST['projects'] ?? [];

        $savedId = $model->saveFullProfile($provider, $user_id, $services, $projects);

        if ($savedId) {
            $_SESSION['registration_success'] = true;
            $_SESSION['registration_message'] = 'Your service provider registration has been submitted successfully! Your account is pending admin verification. You will be able to login once your NIC and details are verified.';
            header('Location: ' . ROOT . '/Login');
            exit;
        } else {
            // Prevent an orphan pending user row from blocking retries with "email already exists".
            $model->cleanupIncompleteRegistration($user_id);

            $this->view('service_provider_register', [
                'errors' => ['Failed to save your profile. Please try again.'],
                'fieldErrors' => ['services' => 'Could not complete registration. Please review and submit again.'],
                'firstErrorField' => 'services',
                'formData' => $provider,
                'services' => $services,
                'projects' => $projects,
                'uploadedPhotoFront' => $provider['nic_photo'] ?? ($provider['nic_photo_front'] ?? $existingCertFront),
                'uploadedPhotoBack' => $provider['nic_photo_back'] ?? $existingCertBack,
            ]);
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
