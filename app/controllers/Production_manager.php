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
        
        $data = [
            'drama' => $drama,
            'services' => $services,
            'confirmedCount' => $confirmedCount,
            'pendingCount' => $pendingCount,
            'totalCount' => count($services),
        ];
        
        $this->view('production_manager/manage_services', $data);
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
}

?>
