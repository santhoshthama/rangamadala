<?php

class ServiceProviderRequest
{
    use Controller;

    private function jsonResponse(array $payload, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code($statusCode);
        echo json_encode($payload);
        exit;
    }

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
            'drama_id' => !empty($_POST['drama_id']) ? (int)$_POST['drama_id'] : null,
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

        // If drama_id is provided, ensure the service type exists in drama_services
        if (!empty($request['drama_id']) && !empty($request['service_type'])) {
            $dramaServicesModel = new M_drama_services();
            $existingService = $dramaServicesModel->getService($request['drama_id'], $request['service_type']);
            
            // If service doesn't exist, add it automatically
            if (!$existingService) {
                $dramaServicesModel->addService(
                    $request['drama_id'],
                    $request['service_type'],
                    $request['budget'],
                    $request['description']
                );
            }
        }

        // Save to database
        $model = new M_service_request();
        $saved = $model->createRequest($request);

        if ($saved) {
            if (!empty($request['drama_id']) && !empty($request['provider_id'])) {
                $requesterName = trim((string)($request['requester_name'] ?? 'Production Manager'));
                $this->notifyProviderAction(
                    (int)$request['provider_id'],
                    (int)$request['drama_id'],
                    'service_request_created_pm',
                    'Service Request from ' . $requesterName,
                    $requesterName . ' sent a new service request for "' . $request['service_type'] . '" in "' . $request['drama_name'] . '".',
                    ROOT . '/ServiceRequests'
                );
            }

            // Success - redirect with success message
            $_SESSION['request_success'] = 'Service request submitted successfully! The provider will contact you soon.';
            // If drama_id provided (production manager flow), take user to the manage_services page so counts update
            if (!empty($request['drama_id'])) {
                header('Location: ' . ROOT . '/production_manager/manage_services?drama_id=' . (int)$request['drama_id']);
            } else {
                header('Location: ' . ROOT . '/BrowseServiceProviders/viewProfile/' . $request['provider_id']);
            }
        } else {
            // Failed - redirect with error
            $_SESSION['request_errors'] = ['Failed to submit request. Please try again.'];
            header('Location: ' . $_SERVER['HTTP_REFERER'] ?? ROOT . '/BrowseServiceProviders');
        }
        exit;
    }

    /**
     * Provider responds to a pending request with quote/dates/note
     */
    public function respond()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        $request_id = $_POST['request_id'] ?? null;
        $quote_amount = $_POST['quote_amount'] ?? null;
        $needs_advance = isset($_POST['needs_advance']) && $_POST['needs_advance'] == '1' ? true : false;
        $advance_amount = $_POST['advance_amount'] ?? null;
        $advance_due_date = $_POST['advance_due_date'] ?? null;
        $final_payment_due_date = $_POST['final_payment_due_date'] ?? null;
        $note = $_POST['note'] ?? '';

        if (!$request_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing request ID'], 400);
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $result = $serviceModel->submitProviderResponse(
                (int)$request_id,
                $_SESSION['user_id'],
                [
                    'quote_amount' => $quote_amount,
                    'needs_advance' => $needs_advance,
                    'advance_amount' => $advance_amount,
                    'advance_due_date' => $advance_due_date,
                    'final_payment_due_date' => $final_payment_due_date,
                    'note' => $note,
                ]
            );

            if ($result['success']) {
                $request = $serviceModel->getRequestById((int)$request_id);
                if ($request) {
                    $this->notifyRequesterAction(
                        (int)($request->requested_by ?? 0),
                        (int)($request->drama_id ?? 0),
                        'pm_provider_responded_quote',
                        'Provider Responded with Quotation',
                        ($request->provider_name ?? 'Provider') . ' sent a quotation response for "' . ($request->service_type ?? 'service') . '" in "' . ($request->drama_name ?? 'your drama') . '".',
                        ROOT . '/production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0)
                    );
                }

                $this->jsonResponse(['success' => true, 'message' => 'Response submitted successfully']);
            } else {
                $this->jsonResponse(is_array($result) ? $result : ['success' => false, 'error' => 'Invalid response'], 400);
            }
        } catch (Exception $e) {
            error_log("Error in respond: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    /**
     * Provider accepts PM-confirmed terms
     */
    public function acceptConfirmed()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        $request_id = $_POST['request_id'] ?? null;
        $allow_more = isset($_POST['allow_more']) ? (int)$_POST['allow_more'] : 0;

        if (!$request_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing request ID'], 400);
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $result = $serviceModel->acceptConfirmedWithDecision((int)$request_id, $_SESSION['user_id'], $allow_more);

            if ($result['success']) {
                $request = $serviceModel->getRequestById((int)$request_id);
                if ($request) {
                    $this->notifyRequesterAction(
                        (int)($request->requested_by ?? 0),
                        (int)($request->drama_id ?? 0),
                        'pm_provider_accepted_terms',
                        'Provider Accepted Confirmed Terms',
                        ($request->provider_name ?? 'Provider') . ' accepted your confirmed terms for "' . ($request->service_type ?? 'service') . '". Service is now in progress.',
                        ROOT . '/production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0)
                    );
                }

                $this->jsonResponse(['success' => true, 'message' => 'Request accepted successfully']);
            } else {
                $this->jsonResponse(is_array($result) ? $result : ['success' => false, 'error' => 'Invalid response'], 400);
            }
        } catch (Exception $e) {
            error_log("Error in acceptConfirmed: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    /**
     * Get overlapping bookings for the provider when deciding whether to allow more bookings.
     */
    public function getOverlappingBookings()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;
        $exclude_request_id = $_POST['request_id'] ?? null;

        if (!$start_date || !$end_date) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing date range'], 400);
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $rows = $serviceModel->getOverlappingBookingsForProvider(
                (int)$_SESSION['user_id'],
                $start_date,
                $end_date,
                $exclude_request_id ? (int)$exclude_request_id : null
            );

            $bookings = [];
            foreach ($rows as $row) {
                $serviceDetails = [];
                if (!empty($row->service_details_json)) {
                    $decoded = json_decode($row->service_details_json, true);
                    if (is_array($decoded)) {
                        $serviceDetails = $decoded;
                    }
                }

                $bookings[] = [
                    'id' => (int)$row->id,
                    'requester_name' => $row->requester_name ?? 'Unknown',
                    'requester_email' => $row->requester_email ?? 'N/A',
                    'requester_phone' => $row->requester_phone ?? 'N/A',
                    'drama_name' => $row->drama_name ?? '-',
                    'service_type' => $row->service_type ?? '-',
                    'service_required' => $row->service_required ?? null,
                    'start_date' => $row->start_date ?? '-',
                    'end_date' => $row->end_date ?? '-',
                    'budget' => $row->budget ?? null,
                    'notes' => $row->notes ?? null,
                    'service_details' => $serviceDetails,
                    'status' => $row->status ?? '-',
                ];
            }

            $this->jsonResponse(['success' => true, 'bookings' => $bookings]);
        } catch (Exception $e) {
            error_log('Error in getOverlappingBookings: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    /**
     * Provider rejects PM-confirmed terms
     */
    public function rejectConfirmed()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
        }

        $request_id = $_POST['request_id'] ?? null;
        $reason = $_POST['reason'] ?? 'Terms not acceptable';

        if (!$request_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing request ID'], 400);
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $request = $serviceModel->getRequestById((int)$request_id);
            $ok = $serviceModel->updateStatusDetailed((int)$request_id, 'rejected', $reason, $_SESSION['user_id']);
            
            if ($ok) {
                if ($request) {
                    $this->notifyRequesterAction(
                        (int)($request->requested_by ?? 0),
                        (int)($request->drama_id ?? 0),
                        'pm_provider_rejected_terms',
                        'Provider Rejected Confirmed Terms',
                        ($request->provider_name ?? 'Provider') . ' rejected confirmed terms for "' . ($request->service_type ?? 'service') . '". Reason: ' . $reason,
                        ROOT . '/production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0)
                    );
                }

                $this->jsonResponse(['success' => true, 'message' => 'Request rejected']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to reject'], 500);
            }
        } catch (Exception $e) {
            error_log("Error in rejectConfirmed: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    private function notifyProviderAction($providerId, $dramaId, $type, $title, $message, $link = null)
    {
        try {
            $notificationModel = $this->getModel('M_notification');
            if (!$notificationModel || !$providerId) {
                return;
            }

            $notificationModel->createNotification([
                'user_id' => (int)$providerId,
                'drama_id' => $dramaId ? (int)$dramaId : null,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
            ]);
        } catch (Exception $e) {
            error_log('ServiceProviderRequest notification error: ' . $e->getMessage());
        }
    }

    private function notifyRequesterAction($requesterId, $dramaId, $type, $title, $message, $link = null)
    {
        try {
            $notificationModel = $this->getModel('M_notification');
            if (!$notificationModel || !$requesterId) {
                return;
            }

            $notificationModel->createNotification([
                'user_id' => (int)$requesterId,
                'drama_id' => $dramaId ? (int)$dramaId : null,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
            ]);
        } catch (Exception $e) {
            error_log('ServiceProviderRequest PM notification error: ' . $e->getMessage());
        }
    }
}
