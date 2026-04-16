<?php

class Production_manager{
    use Controller;

    protected $dramaModel;
    protected $serviceRequestModel;
    protected $paymentModel;

    private function jsonResponse(array $payload, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code($statusCode);
        echo json_encode($payload);
        exit;
    }

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->serviceRequestModel = $this->getModel('M_service_request');
        $this->paymentModel = $this->getModel('M_payment');
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
        
        // Get service schedule for this drama
        $schedules = []; // TODO: Implement service schedule model
        
        // Calculate budget statistics
        $totalBudget = 0;
        $budgetUsed = 0;
        $budgetModel = $this->getModel('M_budget');
        if ($budgetModel) {
            $totalBudget = $budgetModel->getTotalBudget($drama->id);
            $budgetUsed = $budgetModel->getTotalSpent($drama->id);
        }
        
        $data = [
            'drama' => $drama,
            'services' => $allServices,
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
        $dramaServicesModel = $this->getModel('M_drama_services');
        $serviceModel = $this->serviceRequestModel ?: $this->getModel('M_service_request');
        $budgetItems = [];
        $totalBudget = 0;
        $totalSpent = 0;
        $categorySummary = [];
        $serviceTypes = $this->getAllowedBudgetCategories((int)$drama->id);
        $serviceRequests = $serviceModel ? $serviceModel->getServicesByDrama((int)$drama->id) : [];
        $dramaServices = $dramaServicesModel ? $dramaServicesModel->getServicesByDrama((int)$drama->id) : [];
        
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
            'serviceTypes' => $serviceTypes,
            'serviceRequests' => $serviceRequests,
            'dramaServices' => $dramaServices,
        ];
        
        $this->view('production_manager/manage_budget', $data);
    }

    public function get_budget_items()
    {
        $drama = $this->authorizeDrama();

        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            $this->jsonResponse(['success' => false, 'error' => 'Budget model not found'], 500);
        }

        $items = $budgetModel->getBudgetByDrama($drama->id);
        $totalBudget = $budgetModel->getTotalBudget($drama->id);
        $totalSpent = $budgetModel->getTotalSpent($drama->id);
        $remainingBudget = $totalBudget - $totalSpent;
        $categorySummary = $budgetModel->getBudgetSummaryByCategory($drama->id);

        $this->jsonResponse([
            'success' => true,
            'items' => $items,
            'summary' => [
                'totalBudget' => $totalBudget,
                'totalSpent' => $totalSpent,
                'remainingBudget' => $remainingBudget,
                'percentSpent' => $totalBudget > 0 ? round(($totalSpent / $totalBudget) * 100) : 0,
                'categorySummary' => $categorySummary,
            ],
        ]);
    }

    public function get_budget_item()
    {
        $drama = $this->authorizeDrama();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid budget item id'], 400);
        }

        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            $this->jsonResponse(['success' => false, 'error' => 'Budget model not found'], 500);
        }

        $item = $budgetModel->getBudgetItemById($id);
        if (!$item || (int)$item->drama_id !== (int)$drama->id) {
            $this->jsonResponse(['success' => false, 'error' => 'Budget item not found'], 404);
        }

        $this->jsonResponse(['success' => true, 'item' => $item]);
    }

    public function save_budget_item()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        $drama = $this->authorizeDrama();
        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            $this->jsonResponse(['success' => false, 'error' => 'Budget model not found'], 500);
        }

        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $itemName = trim((string)($_POST['item_name'] ?? ''));
        $category = trim((string)($_POST['category'] ?? ''));
        $serviceRequestId = isset($_POST['service_request_id']) && $_POST['service_request_id'] !== '' ? (int)$_POST['service_request_id'] : null;
        $allocatedAmount = isset($_POST['allocated_amount']) ? (float)$_POST['allocated_amount'] : null;
        $spentAmount = isset($_POST['spent_amount']) && $_POST['spent_amount'] !== '' ? (float)$_POST['spent_amount'] : 0.0;
        $status = trim((string)($_POST['status'] ?? 'pending'));
        $notes = trim((string)($_POST['notes'] ?? ''));

        $allowedCategories = $this->getAllowedBudgetCategories((int)$drama->id);
        $allowedStatuses = ['pending', 'approved', 'completed', 'cancelled'];
        $linkedRequest = null;

        if ($itemName === '') {
            $this->jsonResponse(['success' => false, 'error' => 'Item name is required'], 400);
        }

        if ($serviceRequestId !== null) {
            $serviceModel = $this->serviceRequestModel ?: $this->getModel('M_service_request');
            if (!$serviceModel) {
                $this->jsonResponse(['success' => false, 'error' => 'Service request model not found'], 500);
            }

            $linkedRequest = $serviceModel->getRequestById($serviceRequestId);
            if (!$linkedRequest || (int)($linkedRequest->drama_id ?? 0) !== (int)$drama->id) {
                $this->jsonResponse(['success' => false, 'error' => 'Selected service request is invalid for this drama'], 400);
            }

            $requestType = trim((string)($linkedRequest->service_type ?? ''));
            if ($requestType !== '' && !in_array($requestType, $allowedCategories, true)) {
                $allowedCategories[] = $requestType;
            }

            if ($requestType !== '') {
                $category = $requestType;
            }

            $linkedStatus = $this->mapBudgetStatusFromService($linkedRequest);
            if ($linkedStatus !== null) {
                $status = $linkedStatus;
            }

            if ($this->paymentModel) {
                $spentAmount = (float)$this->paymentModel->getTotalPaid($serviceRequestId);
            }
        }

        if (!in_array($category, $allowedCategories, true)) {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid category'], 400);
        }

        if ($allocatedAmount === null || $allocatedAmount < 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Allocated amount must be a valid non-negative number'], 400);
        }

        if ($spentAmount < 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Spent amount must be non-negative'], 400);
        }

        if ($spentAmount > $allocatedAmount) {
            $this->jsonResponse(['success' => false, 'error' => 'Spent amount cannot exceed allocated amount'], 400);
        }

        if (!in_array($status, $allowedStatuses, true)) {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid status'], 400);
        }

        if ($serviceRequestId !== null && $linkedRequest && $status === 'completed') {
            $calcPaymentStatus = strtolower((string)($linkedRequest->calculated_payment_status ?? 'unpaid'));
            $requestStatus = strtolower((string)($linkedRequest->status ?? 'pending'));
            if (!($requestStatus === 'completed_paid' || $calcPaymentStatus === 'paid')) {
                $this->jsonResponse(['success' => false, 'error' => 'Linked request is not fully paid. Budget status cannot be completed yet.'], 400);
            }
        }

        $payload = [
            'drama_id' => (int)$drama->id,
            'item_name' => $itemName,
            'category' => $category,
            'allocated_amount' => $allocatedAmount,
            'spent_amount' => $spentAmount,
            'status' => $status,
            'notes' => $notes !== '' ? $notes : null,
            'created_by' => $_SESSION['user_id'] ?? null,
            'service_request_id' => $serviceRequestId,
            'source_type' => $serviceRequestId !== null ? 'service_request' : 'manual',
        ];

        if ($id) {
            $existing = $budgetModel->getBudgetItemById($id);
            if (!$existing || (int)$existing->drama_id !== (int)$drama->id) {
                $this->jsonResponse(['success' => false, 'error' => 'Budget item not found'], 404);
            }

            $ok = $budgetModel->updateBudgetItem($id, $payload);
            $this->jsonResponse([
                'success' => (bool)$ok,
                'message' => $ok ? 'Budget item updated successfully' : 'Failed to update budget item'
            ]);
        }

        $ok = $budgetModel->createBudgetItem($payload);
        $this->jsonResponse([
            'success' => (bool)$ok,
            'message' => $ok ? 'Budget item created successfully' : 'Failed to create budget item'
        ]);
    }

    protected function getAllowedBudgetCategories(int $dramaId): array
    {
        $fallback = [
            'Theater Production',
            'Lighting Design',
            'Sound Systems',
            'Video Production',
            'Set Design',
            'Costume Design',
            'Makeup & Hair',
            'Other',
        ];

        $dramaServicesModel = $this->getModel('M_drama_services');
        if (!$dramaServicesModel) {
            return $fallback;
        }

        $services = $dramaServicesModel->getServicesByDrama($dramaId);
        if (!is_array($services) || empty($services)) {
            return $fallback;
        }

        $types = [];
        foreach ($services as $svc) {
            $type = trim((string)($svc->service_type ?? ''));
            if ($type !== '' && !in_array($type, $types, true)) {
                $types[] = $type;
            }
        }

        return !empty($types) ? $types : $fallback;
    }

    protected function mapBudgetStatusFromService($serviceRequest): ?string
    {
        if (!$serviceRequest) {
            return null;
        }

        $requestStatus = strtolower((string)($serviceRequest->status ?? 'pending'));
        $paymentStatus = strtolower((string)($serviceRequest->calculated_payment_status ?? 'unpaid'));

        if (in_array($requestStatus, ['rejected', 'cancelled'], true)) {
            return 'cancelled';
        }

        if ($requestStatus === 'completed_paid' || $paymentStatus === 'paid') {
            return 'completed';
        }

        if (in_array($requestStatus, ['confirmed', 'accepted', 'completed'], true)) {
            return 'approved';
        }

        return 'pending';
    }

    public function delete_budget_item()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        $drama = $this->authorizeDrama();
        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            $this->jsonResponse(['success' => false, 'error' => 'Budget model not found'], 500);
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid budget item id'], 400);
        }

        $existing = $budgetModel->getBudgetItemById($id);
        if (!$existing || (int)$existing->drama_id !== (int)$drama->id) {
            $this->jsonResponse(['success' => false, 'error' => 'Budget item not found'], 404);
        }

        $ok = $budgetModel->deleteBudgetItem($id);
        $this->jsonResponse([
            'success' => (bool)$ok,
            'message' => $ok ? 'Budget item deleted successfully' : 'Failed to delete budget item'
        ]);
    }

    public function export_budget_report()
    {
        $drama = $this->authorizeDrama();
        $budgetModel = $this->getModel('M_budget');

        if (!$budgetModel) {
            http_response_code(500);
            echo 'Budget model not found';
            return;
        }

        $items = $budgetModel->getBudgetByDrama($drama->id);
        $filename = 'budget_report_drama_' . (int)$drama->id . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Item Name', 'Category', 'Allocated Amount', 'Spent Amount', 'Status', 'Notes', 'Created At']);

        foreach ($items as $item) {
            fputcsv($output, [
                $item->item_name ?? '',
                $item->category ?? '',
                $item->allocated_amount ?? 0,
                $item->spent_amount ?? 0,
                $item->status ?? '',
                $item->notes ?? '',
                $item->created_at ?? '',
            ]);
        }

        fclose($output);
        exit;
    }

    public function book_theater()
    {
        $drama = $this->authorizeDrama();
        $_SESSION['message'] = 'Theater booking page has been removed from Production Manager module.';
        $_SESSION['message_type'] = 'info';
        header('Location: ' . ROOT . '/production_manager/dashboard?drama_id=' . (int)$drama->id);
        exit;
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
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        $id = $_POST['id']  ?? null;
        $status = $_POST['status'] ?? null;

        if (!$id || !$status) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing id or status'], 400);
        }

        try {
            // Verify the request exists and belongs to a drama managed by this user
            $serviceModel = $this->getModel('M_service_request');
            $request = $serviceModel->getRequestById((int)$id);
            
            if (!$request) {
                $this->jsonResponse(['success' => false, 'error' => 'Request not found'], 404);
            }

            // Verify user is manager for this drama
            $pmModel = $this->getModel('M_production_manager');
            if (!$pmModel->isManagerForDrama($_SESSION['user_id'], (int)$request->drama_id)) {
                $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            // Simplest rule: only pending requests can be cancelled
            if (strtolower((string)$request->status) !== 'pending') {
                $this->jsonResponse(['success' => false, 'error' => 'Only pending requests can be cancelled'], 400);
            }

            // Update status to cancelled (no payment logic here)
            $ok = $serviceModel->updateStatusDetailed((int)$id, 'cancelled');
            if ($ok) {
                $this->notifyProviderAction(
                    (int)$request->provider_id,
                    (int)$request->drama_id,
                    'service_request_cancelled_by_pm',
                    'Service Request Cancelled',
                    'Production manager cancelled the service request for "' . ($request->service_type ?? 'service') . '" in "' . ($request->drama_name ?? 'your drama') . '".',
                    ROOT . '/ServiceRequests'
                );

                $this->jsonResponse(['success' => true, 'status' => 'cancelled']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to update status'], 500);
            }
        } catch (Exception $e) {
            error_log("Error in cancelServiceRequest: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    /**
     * PM confirms a provider-responded request
     */
    public function confirmProviderResponse()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        $request_id = $_POST['request_id'] ?? null;
        $final_quote = $_POST['final_quote'] ?? null;
        $final_start_date = $_POST['final_start_date'] ?? null;
        $final_end_date = $_POST['final_end_date'] ?? null;
        $note = $_POST['note'] ?? '';

        if (!$request_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing request ID'], 400);
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $request = $serviceModel->getRequestById((int)$request_id);

            if (!$request) {
                $this->jsonResponse(['success' => false, 'error' => 'Request not found'], 404);
            }

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
                $this->notifyProviderAction(
                    (int)$request->provider_id,
                    (int)$request->drama_id,
                    'provider_quote_confirmed_by_pm',
                    'Quotation Confirmed by PM',
                    'Production manager confirmed your quotation for "' . ($request->service_type ?? 'service') . '" in "' . ($request->drama_name ?? 'the drama') . '". Please review and accept to continue.',
                    ROOT . '/ServiceRequests'
                );

                $this->jsonResponse(['success' => true, 'message' => 'Provider response confirmed']);
            } else {
                $this->jsonResponse(is_array($result) ? $result : ['success' => false, 'error' => 'Invalid response'], 400);
            }
        } catch (Exception $e) {
            error_log("Error in confirmProviderResponse: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    /**
     * PM rejects a provider-responded request
     */
    public function rejectProviderResponse()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 400);
        }

        $request_id = $_POST['request_id'] ?? null;
        $reason = $_POST['reason'] ?? 'Terms not acceptable';

        if (!$request_id) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing request ID'], 400);
        }

        try {
            $serviceModel = $this->getModel('M_service_request');
            $request = $serviceModel->getRequestById((int)$request_id);
            
            if (!$request) {
                $this->jsonResponse(['success' => false, 'error' => 'Request not found'], 404);
            }

            // Verify user is manager for this drama
            $pmModel = $this->getModel('M_production_manager');
            if (!$pmModel->isManagerForDrama($_SESSION['user_id'], (int)$request->drama_id)) {
                $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $ok = $serviceModel->updateStatusDetailed((int)$request_id, 'rejected', $reason);
            if ($ok) {
                $this->notifyProviderAction(
                    (int)$request->provider_id,
                    (int)$request->drama_id,
                    'provider_quote_rejected_by_pm',
                    'Quotation Rejected by PM',
                    'Production manager rejected your quotation for "' . ($request->service_type ?? 'service') . '". Reason: ' . $reason,
                    ROOT . '/ServiceRequests'
                );

                $this->jsonResponse(['success' => true, 'message' => 'Provider response rejected']);
            } else {
                $this->jsonResponse(['success' => false, 'error' => 'Failed to reject'], 500);
            }
        } catch (Exception $e) {
            error_log("Error in rejectProviderResponse: " . $e->getMessage());
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
            error_log('Production_manager notification error: ' . $e->getMessage());
        }
    }
}
