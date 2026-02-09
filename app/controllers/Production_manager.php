<?php

class Production_manager{
    use Controller;

    protected $dramaModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        // Authorize the drama first
        $drama = $this->authorizeDrama();
        
        // Get all service requests for this drama
        $serviceModel = $this->getModel('M_service_request');
        $allServices = $serviceModel ? $serviceModel->getServicesByDrama($drama->id) : [];
        
        // Get theater bookings for this drama
        $theaterBookings = []; // TODO: Implement theater bookings model
        
        // Get service schedule for this drama
        $schedules = []; // TODO: Implement service schedule model
        
        // Calculate budget statistics
        $totalBudget = 0;
        $budgetUsed = 0;
        // TODO: Fetch from budget tracking model
        
        $data = [
            'drama' => $drama,
            'services' => $allServices,
            'theaterBookings' => $theaterBookings,
            'schedules' => $schedules,
            'totalBudget' => $totalBudget,
            'budgetUsed' => $budgetUsed,
        ];
        
        $this->view('production_manager/dashboard', $data);
    }

    public function manage_services()
    {
        $drama = $this->authorizeDrama();
        
        // Get service requests for this drama
        $serviceModel = $this->getModel('M_service_request');
        $services = $serviceModel ? $serviceModel->getServicesByDrama($drama->id) : [];
        
        // Attach provider details for display
        $providerModel = $this->getModel('M_service_provider');
        $providerCache = [];
        if (is_array($services) && $providerModel) {
            foreach ($services as $s) {
                $pid = $s->provider_id ?? null;
                if ($pid) {
                    if (!isset($providerCache[$pid])) {
                        $prov = $providerModel->getProviderById((int)$pid);
                        $providerCache[$pid] = $prov;
                    }
                    $providerData = $providerCache[$pid];
                    if ($providerData) {
                        $s->provider_name = $providerData->full_name ?? ($providerData->name ?? 'Provider');
                        $s->provider_email = $providerData->email ?? 'N/A';
                        $s->provider_phone = $providerData->phone ?? 'N/A';
                    } else {
                        $s->provider_name = 'Provider';
                        $s->provider_email = 'N/A';
                        $s->provider_phone = 'N/A';
                    }
                } else {
                    $s->provider_name = '—';
                    $s->provider_email = 'N/A';
                    $s->provider_phone = 'N/A';
                }
            }
        }
        
        // Count services by status
        $confirmedCount = 0;
        $pendingCount = 0;
        
        if (is_array($services)) {
            foreach ($services as $service) {
                if (isset($service->status)) {
                    if ($service->status === 'accepted') {
                        $confirmedCount++;
                    } elseif ($service->status === 'pending') {
                        $pendingCount++;
                    }
                }
            }
        }

        // Get drama services configuration from DB
        $dramaServicesModel = $this->getModel('M_drama_services');
        $dramaServices = $dramaServicesModel ? $dramaServicesModel->getServicesByDrama($drama->id) : [];
        
        $data = [
            'drama' => $drama,
            'services' => $services,
            'dramaServices' => $dramaServices,
            'confirmedCount' => $confirmedCount,
            'pendingCount' => $pendingCount,
            'totalCount' => count($services),
        ];
        
        $this->view('production_manager/manage_services', $data);
    }

    public function browse_services()
    {
        $drama = $this->authorizeDrama();

        $providerModel = $this->getModel('M_service_provider');
        $filters = [
            'service_type' => $this->getQueryParam('service_type') ?? null,
            'location'     => $this->getQueryParam('location') ?? null,
            'availability' => $this->getQueryParam('availability') ?? null,
            'min_rate'     => $this->getQueryParam('min_rate') ?? null,
            'max_rate'     => $this->getQueryParam('max_rate') ?? null,
        ];

        $providers = $providerModel ? $providerModel->getAllProvidersWithServices($filters) : [];
        $locations = $providerModel ? $providerModel->getAllLocations() : [];

        $data = [
            'drama'     => $drama,
            'providers' => $providers,
            'filters'   => $filters,
            'locations' => $locations,
        ];

        $this->view('production_manager/browse_services', $data);
    }

    public function manage_budget()
    {
        $drama = $this->authorizeDrama();
        
        // Get budget model and fetch budget data
        $budgetModel = $this->getModel('M_budget');
        $budgetItems = [];
        $totalBudget = 0;
        $totalSpent = 0;
        $categorySummary = [];
        
        if ($budgetModel) {
            $budgetItems = $budgetModel->getBudgetByDrama($drama->id);
            $totalBudget = $budgetModel->getTotalBudget($drama->id);
            $totalSpent = $budgetModel->getTotalSpent($drama->id);
            $categorySummary = $budgetModel->getBudgetSummaryByCategory($drama->id);
        }
        
        $remainingBudget = $totalBudget - $totalSpent;
        $percentSpent = $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100) : 0;
        
        $data = [
            'drama' => $drama,
            'budgetItems' => $budgetItems,
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingBudget' => $remainingBudget,
            'percentSpent' => $percentSpent,
            'categorySummary' => $categorySummary,
        ];
        
        $this->view('production_manager/manage_budget', $data);
    }

    public function book_theater()
    {
        $drama = $this->authorizeDrama();
        
        // Get theater bookings for this drama
        $theaterModel = $this->getModel('M_theater_booking');
        $bookings = [];
        $confirmedCount = 0;
        $pendingCount = 0;
        $totalCost = 0;
        
        if ($theaterModel) {
            $bookings = $theaterModel->getBookingsByDrama($drama->id);
            $totalCost = $theaterModel->getTotalCost($drama->id);
            
            if (is_array($bookings)) {
                foreach ($bookings as $booking) {
                    if (isset($booking->status)) {
                        if ($booking->status === 'confirmed') {
                            $confirmedCount++;
                        } elseif ($booking->status === 'pending') {
                            $pendingCount++;
                        }
                    }
                }
            }
        }
        
        $data = [
            'drama' => $drama,
            'theaterBookings' => $bookings,
            'confirmedCount' => $confirmedCount,
            'pendingCount' => $pendingCount,
            'totalBookings' => count($bookings),
            'totalCost' => $totalCost,
        ];
        
        $this->view('production_manager/book_theater', $data);
    }

    public function manage_schedule()
    {
        $drama = $this->authorizeDrama();
        
        // Get service schedules for this drama
        $scheduleModel = $this->getModel('M_service_schedule');
        $schedules = [];
        $upcomingCount = 0;
        
        if ($scheduleModel) {
            $schedules = $scheduleModel->getSchedulesByDrama($drama->id);
            
            // Count upcoming schedules
            if (is_array($schedules)) {
                $today = date('Y-m-d');
                foreach ($schedules as $schedule) {
                    if (isset($schedule->scheduled_date) && $schedule->scheduled_date >= $today) {
                        $upcomingCount++;
                    }
                }
            }
        }
        
        $data = [
            'drama' => $drama,
            'schedules' => $schedules,
            'upcomingCount' => $upcomingCount,
            'totalSchedules' => count($schedules),
        ];
        
        $this->view('production_manager/manage_schedule', $data);
    }

    public function save_required_services()
    {
        $drama = $this->authorizeDrama();

        $defaultRedirect = ROOT . '/production_manager/manage_services?drama_id=' . (int)$drama->id;
        $returnUrl = isset($_POST['return_url']) ? trim((string)$_POST['return_url']) : (isset($_GET['return_url']) ? trim((string)$_GET['return_url']) : '');
        $isSafeReturn = $returnUrl && ((strpos($returnUrl, ROOT) === 0) || (isset($returnUrl[0]) && $returnUrl[0] === '/'));
        $redirectTo = $isSafeReturn ? $returnUrl : $defaultRedirect;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectTo);
            exit;
        }

        $dramaServicesModel = $this->getModel('M_drama_services');
        if (!$dramaServicesModel) {
            $_SESSION['message'] = 'Error: Drama services model not found.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . $redirectTo);
            exit;
        }

        // Support adding multiple types or removing a single type
        $addedTypes = isset($_POST['required_services']) && is_array($_POST['required_services']) ? array_filter($_POST['required_services']) : [];
        $removeType = isset($_POST['remove_service_type']) ? trim((string)$_POST['remove_service_type']) : '';
        $budget = isset($_POST['service_budget']) ? trim((string)$_POST['service_budget']) : null;
        $description = isset($_POST['service_description']) ? trim((string)$_POST['service_description']) : null;

        if ($removeType !== '') {
            // Remove service type from DB
            $ok = $dramaServicesModel->removeService($drama->id, $removeType);
            $_SESSION['message'] = 'Service type removed.';
            $_SESSION['message_type'] = 'success';
        } elseif (!empty($addedTypes)) {
            // Add multiple service types to DB
            $addedCount = 0;
            foreach ($addedTypes as $t) {
                $t = trim((string)$t);
                if ($t !== '') {
                    if ($dramaServicesModel->addService($drama->id, $t, $budget, $description)) {
                        $addedCount++;
                    }
                }
            }
            if ($addedCount > 0) {
                $_SESSION['message'] = $addedCount . ' service type(s) added.';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'No service types added.';
                $_SESSION['message_type'] = 'info';
            }
        }

        header('Location: ' . $redirectTo);
        exit;
    }

    protected function renderDramaView($view, array $data = [])
    {
        $drama = $this->authorizeDrama();
        $payload = array_merge(['drama' => $drama], $data);
        $this->view('production_manager/' . $view, $payload);
    }

    protected function authorizeDrama()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if (!$this->dramaModel) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $dramaId = $this->getQueryParam('drama_id');
        if (!$dramaId) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $drama = $this->dramaModel->getDramaById((int)$dramaId);
        
        if (!$drama) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        // Check if user is the production manager for this drama
        $pmModel = $this->getModel('M_production_manager');
        $user_id = $_SESSION['user_id'];
        
        if (!$pmModel || !$pmModel->isManagerForDrama($user_id, (int)$dramaId)) {
            $_SESSION['message'] = 'You are not authorized to access this drama.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        return $drama;
    }

    public function cancelServiceRequest()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing id or status']);
            return;
        }

        try {
            // Verify the request exists and belongs to a drama managed by this user
            $serviceModel = $this->getModel('M_service_request');
            $request = $serviceModel->getRequestById((int)$id);
            
            if (!$request) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Request not found']);
                return;
            }

            // Verify user is manager for this drama
            $pmModel = $this->getModel('M_production_manager');
            if (!$pmModel->isManagerForDrama($_SESSION['user_id'], (int)$request->drama_id)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                return;
            }

            // Update status
            $ok = $serviceModel->updateStatusDetailed((int)$id, (string)$status);
            if ($ok) {
                echo json_encode(['success' => true, 'status' => $status]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to update status']);
            }
        } catch (Exception $e) {
            error_log("Error in cancelServiceRequest: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * PM confirms a provider-responded request
     */
    public function confirmProviderResponse()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $request_id = $_POST['request_id'] ?? null;
        $final_quote = $_POST['final_quote'] ?? null;
        $final_start_date = $_POST['final_start_date'] ?? null;
        $final_end_date = $_POST['final_end_date'] ?? null;
        $note = $_POST['note'] ?? '';

        if (!$request_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing request ID']);
            return;
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $result = $serviceModel->confirmByPM(
                (int)$request_id,
                $_SESSION['user_id'],
                [
                    'final_quote' => $final_quote,
                    'final_start_date' => $final_start_date,
                    'final_end_date' => $final_end_date,
                    'note' => $note,
                ]
            );

            if ($result['success']) {
                echo json_encode(['success' => true, 'message' => 'Provider response confirmed']);
            } else {
                http_response_code(400);
                echo json_encode($result);
            }
        } catch (Exception $e) {
            error_log("Error in confirmProviderResponse: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }

    /**
     * PM rejects a provider-responded request
     */
    public function rejectProviderResponse()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $request_id = $_POST['request_id'] ?? null;
        $reason = $_POST['reason'] ?? 'Terms not acceptable';

        if (!$request_id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing request ID']);
            return;
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $request = $serviceModel->getRequestById((int)$request_id);
            
            if (!$request) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Request not found']);
                return;
            }

            // Verify user is manager for this drama
            $pmModel = $this->getModel('M_production_manager');
            if (!$pmModel->isManagerForDrama($_SESSION['user_id'], (int)$request->drama_id)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                return;
            }

            $ok = $serviceModel->updateStatusDetailed((int)$request_id, 'rejected', $reason);
            if ($ok) {
                echo json_encode(['success' => true, 'message' => 'Provider response rejected']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Failed to reject']);
            }
        } catch (Exception $e) {
            error_log("Error in rejectProviderResponse: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }
}
