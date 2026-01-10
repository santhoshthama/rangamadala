<?php

class ServiceProviderProfile
{
    use Controller;

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has service_provider role
        if (($_SESSION['role'] ?? '') !== 'service_provider') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        // Get provider ID from URL or use current user ID
        $provider_id = $_GET['id'] ?? $_SESSION['user_id'];

        // Instantiate model
        $model = new M_service_provider();

        // Fetch provider details
        $provider = $model->getProviderById($provider_id);

        if (!$provider) {
            header("Location: " . ROOT . "/ServiceProviderDashboard");
            exit;
        }

        // Fetch services
        $services = $model->getServicesByProviderId($provider_id);
        
        // Fetch service details for each service
        if (!empty($services)) {
            foreach ($services as $service) {
                $detail = $model->getServiceDetails($service->id, $service->service_type ?? '');
                if ($detail && empty($service->service_type) && !empty($detail->service_type)) {
                    $service->service_type = $detail->service_type; // backfill for legacy rows
                }
                $service->details = $detail;
            }
        }
        
        // Fetch projects
        $projects = $model->getProjectsByProviderId($provider_id);

        // Calculate statistics
        $total_projects = count($projects);

        // Pass data to view
        $data = [
            'provider' => $provider,
            'services' => $services ?? [],
            'projects' => $projects ?? [],
            'total_projects' => $total_projects,
            'provider_id' => $provider_id,
            'pageTitle' => 'Profile'
        ];

        $this->view('service_provider_profile', $data);
    }

    // Add Service
    public function addService()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'service_provider') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $provider_id = $_GET['provider_id'] ?? $_SESSION['user_id'];
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new M_service_provider();
            $services = $_POST['services'] ?? [];
            $addedCount = 0;
            $errors = [];

            // Fetch existing service types for this provider
            $existingServices = $model->getServicesByProviderId($provider_id);
            $existingTypes = array_map(function($s) { return strtolower(trim($s->service_type ?? '')); }, $existingServices);

            // Process each service from the registration-style form
            foreach ($services as $idx => $svc) {
                // Only add if selected
                if (empty($svc['selected'])) {
                    continue;
                }

                $serviceName = $svc['name'] ?? null;
                $description = $svc['description'] ?? '';
                
                if (!$serviceName) {
                    $errors[] = "Service #{$idx} missing name";
                    continue;
                }

                // Check if this service type already exists
                if (in_array(strtolower(trim($serviceName)), $existingTypes)) {
                    $errors[] = "$serviceName already added (one per type allowed)";
                    continue;
                }

                // Extract service-specific fields from the svc array
                $extras = $svc;
                unset($extras['selected'], $extras['name'], $extras['description']);

                // Handle file uploads for this service
                if (isset($_FILES['services']['name'][$idx])) {
                    // Theater photos
                    if (isset($_FILES['services']['name'][$idx]['theatre_photos']) && !empty($_FILES['services']['name'][$idx]['theatre_photos'][0])) {
                        $tmp = $_FILES['services']['tmp_name'][$idx]['theatre_photos'][0] ?? null;
                        $type = $_FILES['services']['type'][$idx]['theatre_photos'][0] ?? '';
                        $size = $_FILES['services']['size'][$idx]['theatre_photos'][0] ?? 0;
                        if ($tmp && is_uploaded_file($tmp)) {
                            $targetDir = __DIR__ . '/../../public/uploads/theatre_photos/';
                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0777, true);
                            }
                            $unique = uniqid('theatre_');
                            $targetFile = $targetDir . $unique . '_' . basename($_FILES['services']['name'][$idx]['theatre_photos'][0]);
                            $allowed = ['image/jpeg','image/png','image/jpg'];
                            $maxSize = 10 * 1024 * 1024;
                            if ($size <= $maxSize && in_array($type, $allowed)) {
                                if (move_uploaded_file($tmp, $targetFile)) {
                                    $extras['theatre_photos'] = 'uploads/theatre_photos/' . $unique . '_' . basename($_FILES['services']['name'][$idx]['theatre_photos'][0]);
                                }
                            }
                        }
                    }
                    // Set designs
                    if (isset($_FILES['services']['name'][$idx]['sample_set_designs']) && !empty($_FILES['services']['name'][$idx]['sample_set_designs'])) {
                        $tmp = $_FILES['services']['tmp_name'][$idx]['sample_set_designs'] ?? null;
                        $type = $_FILES['services']['type'][$idx]['sample_set_designs'] ?? '';
                        $size = $_FILES['services']['size'][$idx]['sample_set_designs'] ?? 0;
                        if ($tmp && is_uploaded_file($tmp)) {
                            $targetDir = __DIR__ . '/../../public/uploads/set_designs/';
                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0777, true);
                            }
                            $unique = uniqid('set_');
                            $targetFile = $targetDir . $unique . '_' . basename($_FILES['services']['name'][$idx]['sample_set_designs']);
                            $allowed = ['image/jpeg','image/png','image/jpg','application/pdf'];
                            $maxSize = 10 * 1024 * 1024;
                            if ($size <= $maxSize && in_array($type, $allowed)) {
                                if (move_uploaded_file($tmp, $targetFile)) {
                                    $extras['sample_set_designs'] = 'uploads/set_designs/' . $unique . '_' . basename($_FILES['services']['name'][$idx]['sample_set_designs']);
                                }
                            }
                        }
                    }
                    // Makeup photos
                    if (isset($_FILES['services']['name'][$idx]['sample_makeup_photos']) && !empty($_FILES['services']['name'][$idx]['sample_makeup_photos'])) {
                        $tmp = $_FILES['services']['tmp_name'][$idx]['sample_makeup_photos'] ?? null;
                        $type = $_FILES['services']['type'][$idx]['sample_makeup_photos'] ?? '';
                        $size = $_FILES['services']['size'][$idx]['sample_makeup_photos'] ?? 0;
                        if ($tmp && is_uploaded_file($tmp)) {
                            $targetDir = __DIR__ . '/../../public/uploads/makeup_photos/';
                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0777, true);
                            }
                            $unique = uniqid('makeup_');
                            $targetFile = $targetDir . $unique . '_' . basename($_FILES['services']['name'][$idx]['sample_makeup_photos']);
                            $allowed = ['image/jpeg','image/png','image/jpg'];
                            $maxSize = 10 * 1024 * 1024;
                            if ($size <= $maxSize && in_array($type, $allowed)) {
                                if (move_uploaded_file($tmp, $targetFile)) {
                                    $extras['sample_makeup_photos'] = 'uploads/makeup_photos/' . $unique . '_' . basename($_FILES['services']['name'][$idx]['sample_makeup_photos']);
                                }
                            }
                        }
                    }
                    // Sample videos
                    if (isset($_FILES['services']['name'][$idx]['sample_videos']) && !empty($_FILES['services']['name'][$idx]['sample_videos'])) {
                        $tmp = $_FILES['services']['tmp_name'][$idx]['sample_videos'] ?? null;
                        $type = $_FILES['services']['type'][$idx]['sample_videos'] ?? '';
                        $size = $_FILES['services']['size'][$idx]['sample_videos'] ?? 0;
                        if ($tmp && is_uploaded_file($tmp)) {
                            $targetDir = __DIR__ . '/../../public/uploads/sample_videos/';
                            if (!is_dir($targetDir)) {
                                mkdir($targetDir, 0777, true);
                            }
                            $unique = uniqid('video_');
                            $targetFile = $targetDir . $unique . '_' . basename($_FILES['services']['name'][$idx]['sample_videos']);
                            $allowed = ['image/jpeg','image/png','image/jpg','video/mp4','video/quicktime','application/x-mov'];
                            $maxSize = 500 * 1024 * 1024;
                            if ($size <= $maxSize && in_array($type, $allowed)) {
                                if (move_uploaded_file($tmp, $targetFile)) {
                                    $extras['sample_videos'] = 'uploads/sample_videos/' . $unique . '_' . basename($_FILES['services']['name'][$idx]['sample_videos']);
                                }
                            }
                        }
                    }
                }

                $result = $model->insertService($provider_id, $serviceName, $description, $extras);
                if ($result) {
                    $addedCount++;
                } else {
                    $errors[] = "Failed to add $serviceName";
                }
            }

            if ($addedCount > 0) {
                $_SESSION['success'] = "Added $addedCount service(s) successfully!";
                header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $provider_id);
                exit;
            } else {
                $_SESSION['error'] = empty($errors) ? "No services selected" : implode(', ', $errors);
            }
        }

        // Fetch existing services to disable them in the form
        $model = new M_service_provider();
        $existingServices = $model->getServicesByProviderId($provider_id);
        $existingServiceTypes = array_map(function($s) { return $s->service_type ?? ''; }, $existingServices);

        $data = [
            'provider_id' => $provider_id,
            'existingServiceTypes' => $existingServiceTypes
        ];
        $this->view('service_add_service', $data);
    }

    // Edit Service
    public function editService()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'service_provider')) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $service_id = $_GET['id'] ?? null;
        if (!$service_id) {
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $model = new M_service_provider();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect all POST data as extras
            $extras = $_POST;
            unset($extras['service_name'], $extras['description']);

            // Handle optional sample set designs upload for set design service edit
            if (isset($_FILES['sample_set_designs']) && !empty($_FILES['sample_set_designs']['name'])) {
                $tmp = $_FILES['sample_set_designs']['tmp_name'] ?? null;
                $type = $_FILES['sample_set_designs']['type'] ?? '';
                $size = $_FILES['sample_set_designs']['size'] ?? 0;
                if ($tmp && is_uploaded_file($tmp)) {
                    $targetDir = __DIR__ . '/../../public/uploads/set_designs/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('set_');
                    $targetFile = $targetDir . $unique . '_' . basename($_FILES['sample_set_designs']['name']);
                    $allowed = ['image/jpeg','image/png','image/jpg','application/pdf'];
                    $maxSize = 10 * 1024 * 1024;
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmp, $targetFile)) {
                            $extras['sample_set_designs'] = 'uploads/set_designs/' . $unique . '_' . basename($_FILES['sample_set_designs']['name']);
                        }
                    }
                }
            }

            // Handle optional theatre photos upload for theater production service edit
            if (isset($_FILES['theatre_photos']) && !empty($_FILES['theatre_photos']['name'])) {
                $tmp = $_FILES['theatre_photos']['tmp_name'] ?? null;
                $type = $_FILES['theatre_photos']['type'] ?? '';
                $size = $_FILES['theatre_photos']['size'] ?? 0;
                if ($tmp && is_uploaded_file($tmp)) {
                    $targetDir = __DIR__ . '/../../public/uploads/theatre_photos/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('theatre_');
                    $targetFile = $targetDir . $unique . '_' . basename($_FILES['theatre_photos']['name']);
                    $allowed = ['image/jpeg','image/png','image/jpg'];
                    $maxSize = 10 * 1024 * 1024;
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmp, $targetFile)) {
                            $extras['theatre_photos'] = 'uploads/theatre_photos/' . $unique . '_' . basename($_FILES['theatre_photos']['name']);
                        }
                    }
                }
            }

            // Handle optional sample makeup photos upload for makeup service edit
            if (isset($_FILES['sample_makeup_photos']) && !empty($_FILES['sample_makeup_photos']['name'])) {
                $tmp = $_FILES['sample_makeup_photos']['tmp_name'] ?? null;
                $type = $_FILES['sample_makeup_photos']['type'] ?? '';
                $size = $_FILES['sample_makeup_photos']['size'] ?? 0;
                if ($tmp && is_uploaded_file($tmp)) {
                    $targetDir = __DIR__ . '/../../public/uploads/makeup_photos/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('makeup_');
                    $targetFile = $targetDir . $unique . '_' . basename($_FILES['sample_makeup_photos']['name']);
                    $allowed = ['image/jpeg','image/png','image/jpg'];
                    $maxSize = 10 * 1024 * 1024;
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmp, $targetFile)) {
                            $extras['sample_makeup_photos'] = 'uploads/makeup_photos/' . $unique . '_' . basename($_FILES['sample_makeup_photos']['name']);
                        }
                    }
                }
            }

            // Handle optional sample videos upload for video production service edit
            if (isset($_FILES['sample_videos']) && !empty($_FILES['sample_videos']['name'])) {
                $tmp = $_FILES['sample_videos']['tmp_name'] ?? null;
                $type = $_FILES['sample_videos']['type'] ?? '';
                $size = $_FILES['sample_videos']['size'] ?? 0;
                if ($tmp && is_uploaded_file($tmp)) {
                    $targetDir = __DIR__ . '/../../public/uploads/sample_videos/';
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }
                    $unique = uniqid('video_');
                    $targetFile = $targetDir . $unique . '_' . basename($_FILES['sample_videos']['name']);
                    $allowed = ['image/jpeg','image/png','image/jpg','video/mp4','video/quicktime','application/x-mov'];
                    $maxSize = 500 * 1024 * 1024; // 500MB for videos
                    if ($size <= $maxSize && in_array($type, $allowed)) {
                        if (move_uploaded_file($tmp, $targetFile)) {
                            $extras['sample_videos'] = 'uploads/sample_videos/' . $unique . '_' . basename($_FILES['sample_videos']['name']);
                        }
                    }
                }
            }

            // Ensure we have a service name; fall back to existing service type
            $existingService = $model->getServiceById($service_id);
            $serviceName = $_POST['service_name'] ?? ($existingService->service_type ?? null);
            if (empty($serviceName)) {
                $_SESSION['error'] = "Service type is required";
                header("Location: " . ROOT . "/ServiceProviderProfile/editService?id=" . $service_id);
                exit;
            }

            $result = $model->updateService(
                $service_id, 
                $serviceName,
                $_POST['description'] ?? '', 
                $extras
            );
            
            if ($result) {
                $service = $model->getServiceById($service_id);
                $_SESSION['success'] = "Service updated successfully!";
                header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $service->provider_id);
                exit;
            } else {
                $_SESSION['error'] = "Error updating service";
            }
        }

        // Fetch service data
        $service = $model->getServiceById($service_id);
        if (!$service) {
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        // Fetch service-specific details
        $details = $model->getServiceDetails($service_id, $service->service_type ?? '');

        $data = [
            'service' => $service,
            'details' => $details
        ];
        $this->view('service_edit_service', $data);
    }

    // Delete Service
    public function deleteService()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'service_provider')) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $service_id = $_GET['id'] ?? null;
        if (!$service_id) {
            $_SESSION['error'] = "Invalid service ID";
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $model = new M_service_provider();
        $service = $model->getServiceById($service_id);
        
        if (!$service) {
            $_SESSION['error'] = "Service not found";
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $result = $model->deleteService($service_id);
        
        if ($result) {
            $_SESSION['success'] = "Service deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting service";
        }

        header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $service->provider_id);
        exit;
    }

    // Add Project
    public function addProject()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'service_provider')) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $provider_id = $_GET['provider_id'] ?? $_SESSION['user_id'];
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new M_service_provider();
            $result = $model->insertProject(
                $provider_id,
                $_POST['year'],
                $_POST['project_name'],
                $_POST['services_provided'],
                $_POST['description'] ?? ''
            );
            
            if ($result) {
                $_SESSION['success'] = "Project added successfully!";
                header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $provider_id);
                exit;
            } else {
                $_SESSION['error'] = "Error adding project";
            }
        }

        $data = ['provider_id' => $provider_id];
        $this->view('service_add_project', $data);
    }

    // Edit Project
    public function editProject()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'service_provider')) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $project_id = $_GET['id'] ?? null;
        if (!$project_id) {
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $model = new M_service_provider();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $model->updateProject(
                $project_id,
                $_POST['year'],
                $_POST['project_name'],
                $_POST['services_provided'],
                $_POST['description'] ?? ''
            );
            
            if ($result) {
                $project = $model->getProjectById($project_id);
                $_SESSION['success'] = "Project updated successfully!";
                header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $project->provider_id);
                exit;
            } else {
                $_SESSION['error'] = "Error updating project";
            }
        }

        // Fetch project data
        $project = $model->getProjectById($project_id);
        if (!$project) {
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $data = ['project' => $project];
        $this->view('service_edit_project', $data);
    }

    // Delete Project
    public function deleteProject()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'service_provider')) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $project_id = $_GET['id'] ?? null;
        if (!$project_id) {
            $_SESSION['error'] = "Invalid project ID";
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $model = new M_service_provider();
        $project = $model->getProjectById($project_id);
        
        if (!$project) {
            $_SESSION['error'] = "Project not found";
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $result = $model->deleteProject($project_id);
        
        if ($result) {
            $_SESSION['success'] = "Project deleted successfully!";
        } else {
            $_SESSION['error'] = "Error deleting project";
        }

        header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $project->provider_id);
        exit;
    }

    // Edit Basic Info
    public function editBasicInfo()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'service_provider')) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $provider_id = $_GET['id'] ?? $_SESSION['user_id'];
        $model = new M_service_provider();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $model->updateBasicInfo(
                $provider_id,
                $_POST['full_name'],
                $_POST['professional_title'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['location'],
                $_POST['website'] ?? '',
                $_POST['years_experience'],
                $_POST['professional_summary'] ?? '',
                $_POST['availability'] ?? 0,
                $_POST['availability_notes'] ?? ''
            );
            
            if ($result) {
                $_SESSION['success'] = "Information updated successfully!";
                header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $provider_id);
                exit;
            } else {
                $_SESSION['error'] = "Error updating information";
            }
        }

        // Fetch provider data
        $provider = $model->getProviderById($provider_id);
        if (!$provider) {
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $data = ['provider' => $provider];
        $this->view('service_edit_basic_info', $data);
    }

    // Change Password
    public function changePassword()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'service_provider') {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $provider_id = $_SESSION['user_id'];
        $model = new M_service_provider();

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate password fields
            if (empty($_POST['current_password'])) {
                $_SESSION['error'] = "Current password is required";
            } elseif (empty($_POST['new_password'])) {
                $_SESSION['error'] = "New password cannot be empty";
            } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
                $_SESSION['error'] = "New passwords do not match";
            } else {
                // Verify current password and update
                $result = $model->updatePasswordWithVerification(
                    $provider_id,
                    $_POST['current_password'],
                    $_POST['new_password']
                );
                
                if ($result) {
                    $_SESSION['success'] = "Password changed successfully!";
                    header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $provider_id);
                    exit;
                } else {
                    $_SESSION['error'] = "Current password is incorrect";
                }
            }
        }

        // Fetch provider data
        $provider = $model->getProviderById($provider_id);
        if (!$provider) {
            header("Location: " . ROOT . "/ServiceProviderProfile");
            exit;
        }

        $data = ['provider' => $provider];
        $this->view('service_change_password', $data);
    }

    // Delete Profile
    public function deleteProfile()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'service_provider') {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        $provider_id = $_GET['id'] ?? null;
        if (!$provider_id) {
            $_SESSION['error'] = "Invalid provider ID";
            header("Location: " . ROOT . "/ServiceProviderDashboard");
            exit;
        }

        $model = new M_service_provider();
        $result = $model->deleteProvider($provider_id);
        
        if ($result) {
            $_SESSION['success'] = "Profile deleted successfully!";
            unset($_SESSION['user_id']);
            unset($_SESSION['role']);
            session_destroy();
            header("Location: " . ROOT . "/Home");
            exit;
        } else {
            $_SESSION['error'] = "Error deleting profile";
            header("Location: " . ROOT . "/ServiceProviderProfile/index?id=" . $provider_id);
            exit;
        }
    }

    // Upload Profile Image (SEPARATE from business certificate)
    public function uploadProfileImage()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'service_provider')) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
            $provider_id = $_SESSION['user_id'];
            $file = $_FILES['profile_image'];

            // Validate file type
            $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowed_types)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, PNG and GIF allowed.";
                header("Location: " . ROOT . "/ServiceProviderProfile");
                exit;
            }

            if ($file['size'] > $max_size) {
                $_SESSION['error'] = "File too large. Maximum size is 5MB.";
                header("Location: " . ROOT . "/ServiceProviderProfile");
                exit;
            }

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['error'] = "Upload error occurred.";
                header("Location: " . ROOT . "/ServiceProviderProfile");
                exit;
            }

            // Validate file extension
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($extension, $allowed_extensions)) {
                $_SESSION['error'] = "Invalid file extension. Only JPG, PNG and GIF allowed.";
                header("Location: " . ROOT . "/ServiceProviderProfile");
                exit;
            }

            // Generate unique filename
            $filename = 'profile_' . $provider_id . '_' . time() . '.' . $extension;
            
            // Upload directory (FIXED PATH - matches view path)
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/Rangamadala/public/uploads/profile_images/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $destination = $upload_dir . $filename;

            // Move uploaded file
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Update database
                $model = new M_service_provider();
                
                // Get old profile image to delete it (NOT business certificate)
                $provider = $model->getProviderById($provider_id);
                if ($provider && !empty($provider->profile_image)) {
                    $old_image = $upload_dir . $provider->profile_image;
                    if (file_exists($old_image)) {
                        unlink($old_image);
                    }
                }
                
                // Update with new profile image
                $result = $model->updateProfileImage($provider_id, $filename);
                
                if ($result) {
                    $_SESSION['success'] = "Profile picture updated successfully!";
                } else {
                    $_SESSION['error'] = "Failed to update profile picture in database.";
                }
            } else {
                $_SESSION['error'] = "Failed to upload image.";
            }
        }

        header("Location: " . ROOT . "/ServiceProviderProfile");
        exit;
    }
}
