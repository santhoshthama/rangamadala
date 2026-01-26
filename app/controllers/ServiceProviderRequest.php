<?php

class ServiceProviderRequest
{
    use Controller;

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/BrowseServiceProviders');
            exit;
        }

        // Handle uploads (script/reference files)
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/request_references/';
        $allowedExt = ['pdf','doc','docx','jpg','jpeg','png','gif','zip'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $uploadedFiles = [];

        // Make sure destination exists
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $fileFields = [
            'theater_reference',
            'lighting_reference',
            'sound_reference',
            'video_reference',
            'set_reference',
            'costume_reference',
            'makeup_reference',
        ];

        foreach ($fileFields as $field) {
            if (!isset($_FILES[$field]) || empty($_FILES[$field]['name'])) {
                continue; // nothing uploaded for this field
            }

            $file = $_FILES[$field];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['request_errors'] = ['File upload failed for ' . $field . ' (error code ' . $file['error'] . ').'];
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders'));
                exit;
            }

            // Validate size
            if ($file['size'] > $maxSize) {
                $_SESSION['request_errors'] = ['File too large for ' . $field . ' (max 5MB).'];
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders'));
                exit;
            }

            // Validate extension
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExt, true)) {
                $_SESSION['request_errors'] = ['Invalid file type for ' . $field . '. Allowed: ' . implode(', ', $allowedExt) . '.'];
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders'));
                exit;
            }

            // Build safe filename
            $baseName = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
            $newName = uniqid('req_', true) . '_' . $baseName . '.' . $ext;
            $destPath = $uploadDir . $newName;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                $_SESSION['request_errors'] = ['Could not save uploaded file for ' . $field . '.'];
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders'));
                exit;
            }

            // Store relative path for public access
            $relativePath = 'uploads/request_references/' . $newName;
            $uploadedFiles[$field] = [
                'original_name' => $file['name'],
                'relative_path' => $relativePath,
                'size' => (int)$file['size'],
                'mime' => $file['type'] ?? '',
            ];
        }

        // Collect form data
        $request = [
            'provider_id' => (int)($_POST['provider_id'] ?? 0),
            'requested_by' => $_SESSION['user_id'] ?? null,
            'requester_name' => trim($_POST['requester_name'] ?? ''),
            'requester_email' => trim($_POST['requester_email'] ?? ''),
            'requester_phone' => trim($_POST['requester_phone'] ?? ''),
            'drama_name' => trim($_POST['drama_name'] ?? ''),
            'service_type' => trim($_POST['service_type'] ?? ''),
            'service_required' => trim($_POST['service_required'] ?? ''),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'budget' => isset($_POST['budget']) && !empty(trim($_POST['budget'])) ? (float)($_POST['budget']) : null,
            'description' => trim($_POST['description'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'service_details' => $_POST // Start with all POST data for service-specific details
        ];

        // Attach uploaded file metadata
        if (!empty($uploadedFiles)) {
            $request['service_details']['uploaded_files'] = $uploadedFiles;
        }

        // Basic validation
        $errors = [];
        if (empty($request['provider_id'])) $errors[] = 'Invalid provider.';
        if (empty($request['requester_name'])) $errors[] = 'Your name is required.';
        if (empty($request['requester_email']) || !filter_var($request['requester_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if (empty($request['requester_phone'])) $errors[] = 'Phone number is required.';
        if (empty($request['drama_name'])) $errors[] = 'Drama/production name is required.';
        if (empty($request['service_type'])) $errors[] = 'Service selection is required.';
        if (empty($request['start_date'])) $errors[] = 'Start date is required.';
        if (empty($request['end_date'])) $errors[] = 'End date is required.';

        if (!empty($errors)) {
            // Return to previous page with errors
            $_SESSION['request_errors'] = $errors;
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders');
            exit;
        }

        // Save to database
        $model = new M_service_request();
        $saved = $model->createRequest($request);

        if ($saved) {
            // Success - redirect with success message
            $_SESSION['request_success'] = 'Service request submitted successfully! The provider will contact you soon.';
            header('Location: ' . ROOT . '/BrowseServiceProviders/viewProfile/' . $request['provider_id']);
        } else {
            // Failed - redirect with error
            $_SESSION['request_errors'] = ['Failed to submit request. Please try again.'];
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders');
        }
        exit;
    }
}
