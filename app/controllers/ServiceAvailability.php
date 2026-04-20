<?php

class ServiceAvailability
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

    public function index()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has service_provider role
        if (($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        // Get provider details for profile image
        $model = new M_service_provider();
        $provider = $model->getProviderById($_SESSION['user_id']);
        
        // Load availability data from database
        $formattedAvailability = [];
        try {
            $availabilityModel = new M_provider_availability();
            $availabilityData = $availabilityModel->getAvailability($_SESSION['user_id']);
            
            // Format availability data for the view
            if ($availabilityData) {
                foreach ($availabilityData as $availability) {
                    $dateKey = $this->formatDateForJS($availability->available_date);
                    
                    // Parse service details JSON if present
                    $serviceDetails = [];
                    if (!empty($availability->service_details_json)) {
                        $serviceDetails = json_decode($availability->service_details_json, true) ?? [];
                    }
                    
                    $formattedAvailability[$dateKey] = [
                        'date' => $availability->available_date,
                        'description' => $availability->description ?? '',
                        'status' => $availability->status ?? 'available',
                        'allow_more_bookings' => $availability->allow_more_bookings ?? 1,
                        'booked_for' => $availability->booked_for ?? null,
                        'booking_details' => $availability->booking_details ?? null,
                        'service_request_id' => $availability->service_request_id ?? null,
                        'added_on' => $availability->added_on ?? null,
                        'booked_on' => $availability->booked_on ?? null,
                        // Request details
                        'requester_name' => $availability->requester_name ?? null,
                        'requester_email' => $availability->requester_email ?? null,
                        'requester_phone' => $availability->requester_phone ?? null,
                        'drama_name' => $availability->drama_name ?? null,
                        'service_type' => $availability->service_type ?? null,
                        'service_required' => $availability->service_required ?? null,
                        'start_date' => $availability->start_date ?? null,
                        'end_date' => $availability->end_date ?? null,
                        'budget' => $availability->budget ?? null,
                        'request_description' => $availability->request_description ?? null,
                        'notes' => $availability->notes ?? null,
                        'request_status' => $availability->request_status ?? null,
                        'service_details' => $serviceDetails
                    ];
                }
            }
        } catch (Exception $e) {
            error_log("Error loading availability data: " . $e->getMessage());
            // Continue without availability data - table might not exist yet
            $formattedAvailability = [];
        }
        
        $data = [
            'provider' => $provider,
            'pageTitle' => 'Availability Calendar',
            'availability_data' => json_encode($formattedAvailability)
        ];

        $this->view('service_availability', $data);
    }

    //Format date from database (Y-m-d) to JavaScript format (M/D/YYYY)
    
    private function formatDateForJS($dbDate)
    {
        $timestamp = strtotime($dbDate);
        return date('n/j/Y', $timestamp);
    }

    //Add a new available date via AJAX
    public function addDate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }

            $date = $_POST['date'] ?? null;
            $description = $_POST['description'] ?? null;
            $title = $_POST['title'] ?? null;

            if (!$date || !$description || !$title) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing date or description'], 400);
        }

        // Convert JS date format (M/D/YYYY) to database format (Y-m-d)
        $dbDate = $this->formatDateForDB($date);

        $availabilityModel = new M_provider_availability();
            $result = $availabilityModel->addAvailableDate(
                $_SESSION['user_id'],
                $dbDate,
                $description,
                'booked',
                $title,
                $description,
                null
            );

        if ($result) {
            $this->jsonResponse(['success' => true, 'date' => $date, 'description' => $description]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to add date'], 500);
        }
    }

    //Remove an available date via AJAX

    public function removeDate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $date = $_POST['date'] ?? null;

        if (!$date) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing date'], 400);
        }

        // Convert JS date format to database format
        $dbDate = $this->formatDateForDB($date);

        $availabilityModel = new M_provider_availability();
        $result = $availabilityModel->removeAvailableDate($_SESSION['user_id'], $dbDate);

        if ($result) {
            $this->jsonResponse(['success' => true, 'date' => $date]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to remove date'], 500);
        }
    }

    //Update an available date via AJAX

    public function updateDate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $date = $_POST['date'] ?? null;
        $description = $_POST['description'] ?? null;

        if (!$date || !$description) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing date or description'], 400);
        }

        // Convert JS date format to database format
        $dbDate = $this->formatDateForDB($date);

        $availabilityModel = new M_provider_availability();
        $result = $availabilityModel->updateAvailableDate($_SESSION['user_id'], $dbDate, $description, 'available');

        if ($result) {
            $this->jsonResponse(['success' => true, 'date' => $date, 'description' => $description]);
        } else {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to update date'], 500);
        }
    }

    //Format date from JavaScript format (M/D/YYYY) to database format (Y-m-d)
    private function formatDateForDB($jsDate)
    {
        $timestamp = strtotime($jsDate);
        return date('Y-m-d', $timestamp);
    }
}

