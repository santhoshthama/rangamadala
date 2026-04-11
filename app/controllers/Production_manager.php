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

    public function get_budget_items()
    {
        header('Content-Type: application/json');
        $drama = $this->authorizeDrama();

        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Budget model not found']);
            return;
        }

        $items = $budgetModel->getBudgetByDrama($drama->id);
        $totalBudget = $budgetModel->getTotalBudget($drama->id);
        $totalSpent = $budgetModel->getTotalSpent($drama->id);
        $remainingBudget = $totalBudget - $totalSpent;
        $categorySummary = $budgetModel->getBudgetSummaryByCategory($drama->id);

        echo json_encode([
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
        header('Content-Type: application/json');
        $drama = $this->authorizeDrama();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid budget item id']);
            return;
        }

        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Budget model not found']);
            return;
        }

        $item = $budgetModel->getBudgetItemById($id);
        if (!$item || (int)$item->drama_id !== (int)$drama->id) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Budget item not found']);
            return;
        }

        echo json_encode(['success' => true, 'item' => $item]);
    }

    public function save_budget_item()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $drama = $this->authorizeDrama();
        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Budget model not found']);
            return;
        }

        $id = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;
        $itemName = trim((string)($_POST['item_name'] ?? ''));
        $category = trim((string)($_POST['category'] ?? ''));
        $allocatedAmount = isset($_POST['allocated_amount']) ? (float)$_POST['allocated_amount'] : null;
        $spentAmount = isset($_POST['spent_amount']) && $_POST['spent_amount'] !== '' ? (float)$_POST['spent_amount'] : 0.0;
        $status = trim((string)($_POST['status'] ?? 'pending'));
        $notes = trim((string)($_POST['notes'] ?? ''));

        $allowedCategories = ['venue', 'technical', 'costume', 'marketing', 'other'];
        $allowedStatuses = ['pending', 'approved', 'completed', 'cancelled'];

        if ($itemName === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Item name is required']);
            return;
        }

        if (!in_array($category, $allowedCategories, true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid category']);
            return;
        }

        if ($allocatedAmount === null || $allocatedAmount < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Allocated amount must be a valid non-negative number']);
            return;
        }

        if ($spentAmount < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Spent amount must be non-negative']);
            return;
        }

        if ($spentAmount > $allocatedAmount) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Spent amount cannot exceed allocated amount']);
            return;
        }

        if (!in_array($status, $allowedStatuses, true)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid status']);
            return;
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
        ];

        if ($id) {
            $existing = $budgetModel->getBudgetItemById($id);
            if (!$existing || (int)$existing->drama_id !== (int)$drama->id) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Budget item not found']);
                return;
            }

            $ok = $budgetModel->updateBudgetItem($id, $payload);
            echo json_encode([
                'success' => (bool)$ok,
                'message' => $ok ? 'Budget item updated successfully' : 'Failed to update budget item'
            ]);
            return;
        }

        $ok = $budgetModel->createBudgetItem($payload);
        echo json_encode([
            'success' => (bool)$ok,
            'message' => $ok ? 'Budget item created successfully' : 'Failed to create budget item'
        ]);
    }

    public function delete_budget_item()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $drama = $this->authorizeDrama();
        $budgetModel = $this->getModel('M_budget');
        if (!$budgetModel) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Budget model not found']);
            return;
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid budget item id']);
            return;
        }

        $existing = $budgetModel->getBudgetItemById($id);
        if (!$existing || (int)$existing->drama_id !== (int)$drama->id) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Budget item not found']);
            return;
        }

        $ok = $budgetModel->deleteBudgetItem($id);
        echo json_encode([
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
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }

        $id = $_POST['id']  ?? null;
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

            // Simplest rule: only pending requests can be cancelled
            if (strtolower((string)$request->status) !== 'pending') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Only pending requests can be cancelled']);
                return;
            }

            // Update status to cancelled (no payment logic here)
            $ok = $serviceModel->updateStatusDetailed((int)$id, 'cancelled');
            if ($ok) {
                echo json_encode(['success' => true, 'status' => 'cancelled']);
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
