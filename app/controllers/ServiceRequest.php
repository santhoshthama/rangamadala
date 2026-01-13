<?php

class ServiceRequest
{
    use Controller;

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/BrowseServiceProviders');
            exit;
        }

        // Collect form data
        $request = [
            'provider_id' => (int)($_POST['provider_id'] ?? 0),
            'requester_name' => trim($_POST['requester_name'] ?? ''),
            'requester_email' => trim($_POST['requester_email'] ?? ''),
            'requester_phone' => trim($_POST['requester_phone'] ?? ''),
            'drama_name' => trim($_POST['drama_name'] ?? ''),
            'service_required' => trim($_POST['service_required'] ?? ''),
            'start_date' => trim($_POST['start_date'] ?? ''),
            'end_date' => trim($_POST['end_date'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Basic validation
        $errors = [];
        if (empty($request['provider_id'])) $errors[] = 'Invalid provider.';
        if (empty($request['requester_name'])) $errors[] = 'Your name is required.';
        if (empty($request['requester_email']) || !filter_var($request['requester_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }
        if (empty($request['requester_phone'])) $errors[] = 'Phone number is required.';
        if (empty($request['drama_name'])) $errors[] = 'Drama/production name is required.';
        if (empty($request['service_required'])) $errors[] = 'Service selection is required.';
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
