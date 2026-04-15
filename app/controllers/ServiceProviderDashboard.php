<?php

class ServiceProviderDashboard
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
        if (($_SESSION['user_role'] ?? '') !== 'service_provider') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        // Load models
        $providerModel = new M_service_provider();
        $requestModel = new M_service_request();
        $paymentModel = new M_payment();

        // Get provider details for profile image
        $provider = $providerModel->getProviderById($_SESSION['user_id']);
        $providerId = $_SESSION['user_id'];
        $trendRange = $_GET['trend'] ?? 'monthly';
        if (!in_array($trendRange, ['weekly', 'monthly', 'yearly'], true)) {
            $trendRange = 'monthly';
        }

        // Overview metrics
        $overallCounts = $requestModel->getDashboardCounts($providerId);
        $revenueSummary = $paymentModel->getRevenueSummary($providerId);

        $now = new DateTime();
        $thisMonthStart = (clone $now)->modify('first day of this month')->format('Y-m-d 00:00:00');
        $thisMonthEnd = (clone $now)->format('Y-m-d 23:59:59');
        $prevMonthStart = (clone $now)->modify('first day of last month')->format('Y-m-d 00:00:00');
        $prevMonthEnd = (clone $now)->modify('last day of last month')->format('Y-m-d 23:59:59');

        $thisMonthRevenue = $paymentModel->getRevenueSummary($providerId, $thisMonthStart, $thisMonthEnd);
        $prevMonthRevenue = $paymentModel->getRevenueSummary($providerId, $prevMonthStart, $prevMonthEnd);
        $thisMonthCounts = $requestModel->getDashboardCounts($providerId, $thisMonthStart, $thisMonthEnd);
        $prevMonthCounts = $requestModel->getDashboardCounts($providerId, $prevMonthStart, $prevMonthEnd);

        $totalBookings = (int)($overallCounts->total_bookings ?? 0);
        $completedServices = (int)($overallCounts->completed_services ?? 0);
        $completionRate = $totalBookings > 0 ? round(($completedServices / $totalBookings) * 100, 1) : 0;

        $revenueChange = $this->calculateChangePercent(
            (float)($thisMonthRevenue->total_revenue ?? 0),
            (float)($prevMonthRevenue->total_revenue ?? 0)
        );
        $bookingChange = $this->calculateChangePercent(
            (float)($thisMonthCounts->total_bookings ?? 0),
            (float)($prevMonthCounts->total_bookings ?? 0)
        );

        $revenueTrend = $this->buildRevenueTrend($paymentModel, $providerId, $trendRange);

        // Other dashboard sections
        $serviceDistribution = $requestModel->getServiceDistribution($providerId, 6);
        $distributionTotal = 0;
        foreach ($serviceDistribution as $item) {
            $distributionTotal += (int)($item->booking_count ?? 0);
        }
        foreach ($serviceDistribution as $item) {
            $count = (int)($item->booking_count ?? 0);
            $item->percentage = $distributionTotal > 0 ? round(($count / $distributionTotal) * 100) : 0;
            $item->service_label = ucwords(str_replace('_', ' ', $item->service_type ?? 'N/A'));
        }

        $ongoingServices = $requestModel->getOngoingServices($providerId, 6);
        $topClients = $requestModel->getTopClients($providerId, 3);

        foreach ($topClients as $client) {
            $name = trim((string)($client->requester_name ?? 'NA'));
            $parts = preg_split('/\s+/', $name);
            $initials = '';
            foreach (array_slice($parts, 0, 2) as $part) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
            $client->initials = $initials ?: 'NA';
        }
        
        $data = [
            'provider' => $provider,
            'pageTitle' => 'Dashboard',
            'total_revenue' => (float)($revenueSummary->total_revenue ?? 0),
            'total_bookings' => $totalBookings,
            'completed_services' => $completedServices,
            'completion_rate' => $completionRate,
            'revenue_change' => $revenueChange,
            'booking_change' => $bookingChange,
            'active_services' => (int)($overallCounts->active_services ?? 0),
            'trend_range' => $trendRange,
            'revenue_trend' => $revenueTrend,
            'service_distribution' => $serviceDistribution,
            'ongoing_services' => $ongoingServices,
            'top_clients' => $topClients
        ];

        $this->view('service_provider_dashboard', $data);
    }

    private function calculateChangePercent($current, $previous)
    {
        $current = (float)$current;
        $previous = (float)$previous;

        if ($previous == 0.0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function buildRevenueTrend($paymentModel, $providerId, $range)
    {
        $now = new DateTime();
        $points = [];
        $totals = [];

        if ($range === 'weekly') {
            $start = (clone $now)->modify('-6 days')->format('Y-m-d 00:00:00');
            $end = (clone $now)->format('Y-m-d 23:59:59');
            $rows = $paymentModel->getRevenueReport($providerId, $start, $end);

            foreach ($rows as $row) {
                $key = date('Y-m-d', strtotime($row->paid_at ?? $row->created_at));
                $totals[$key] = ($totals[$key] ?? 0) + (float)($row->amount ?? 0);
            }

            for ($i = 6; $i >= 0; $i--) {
                $d = (clone $now)->modify('-' . $i . ' days');
                $key = $d->format('Y-m-d');
                $points[] = [
                    'label' => $d->format('d M'),
                    'amount' => $totals[$key] ?? 0,
                ];
            }
        } elseif ($range === 'yearly') {
            $start = (clone $now)->modify('-4 years')->format('Y-01-01 00:00:00');
            $end = (clone $now)->format('Y-m-d 23:59:59');
            $rows = $paymentModel->getRevenueReport($providerId, $start, $end);

            foreach ($rows as $row) {
                $key = date('Y', strtotime($row->paid_at ?? $row->created_at));
                $totals[$key] = ($totals[$key] ?? 0) + (float)($row->amount ?? 0);
            }

            for ($i = 4; $i >= 0; $i--) {
                $y = (clone $now)->modify('-' . $i . ' years')->format('Y');
                $points[] = [
                    'label' => $y,
                    'amount' => $totals[$y] ?? 0,
                ];
            }
        } else {
            $start = (clone $now)->modify('first day of -5 months')->format('Y-m-d 00:00:00');
            $end = (clone $now)->format('Y-m-d 23:59:59');
            $rows = $paymentModel->getRevenueReport($providerId, $start, $end);

            foreach ($rows as $row) {
                $key = date('Y-m', strtotime($row->paid_at ?? $row->created_at));
                $totals[$key] = ($totals[$key] ?? 0) + (float)($row->amount ?? 0);
            }

            for ($i = 5; $i >= 0; $i--) {
                $m = (clone $now)->modify('-' . $i . ' months');
                $key = $m->format('Y-m');
                $points[] = [
                    'label' => $m->format('M'),
                    'amount' => $totals[$key] ?? 0,
                ];
            }
        }

        $maxAmount = 0;
        foreach ($points as $point) {
            $maxAmount = max($maxAmount, (float)$point['amount']);
        }

        foreach ($points as &$point) {
            $point['height'] = $maxAmount > 0 ? max(8, (int)round(($point['amount'] / $maxAmount) * 100)) : 8;
        }
        unset($point);

        return $points;
    }

    public function search()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        // Check if user has service_provider role
        if (($_SESSION['user_role'] ?? '') !== 'service_provider') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }

        // Get search query
        $query = trim($_GET['q'] ?? '');
        if (strlen($query) < 2) {
            echo json_encode(['results' => []]);
            exit;
        }

        $providerId = $_SESSION['user_id'];
        $requestModel = new M_service_request();
        
        // Search in ongoing services
        $ongoingServices = $requestModel->getOngoingServices($providerId, 100);
        $serviceResults = [];
        
        foreach ($ongoingServices as $service) {
            if (
                stripos($service->drama_name ?? '', $query) !== false ||
                stripos(str_replace('_', ' ', $service->service_type ?? ''), $query) !== false
            ) {
                $serviceResults[] = [
                    'type' => 'service',
                    'id' => $service->id ?? '',
                    'title' => htmlspecialchars($service->drama_name ?? 'N/A'),
                    'subtitle' => htmlspecialchars(ucwords(str_replace('_', ' ', $service->service_type ?? ''))),
                    'description' => 'Service | ' . htmlspecialchars(ucwords(str_replace('_', ' ', $service->status ?? '')))
                ];
                if (count($serviceResults) >= 5) break;
            }
        }

        // Search in top clients
        $topClients = $requestModel->getTopClients($providerId, 100);
        $clientResults = [];
        
        foreach ($topClients as $client) {
            if (stripos($client->requester_name ?? '', $query) !== false) {
                $clientResults[] = [
                    'type' => 'client',
                    'id' => $client->requester_id ?? '',
                    'title' => htmlspecialchars($client->requester_name ?? 'N/A'),
                    'subtitle' => (int)($client->booking_count ?? 0) . ' bookings',
                    'description' => 'Client | Total Spent: Rs. ' . number_format($client->total_spent ?? 0, 2)
                ];
                if (count($clientResults) >= 5) break;
            }
        }

        // Combine results
        $allResults = array_merge($serviceResults, $clientResults);
        
        header('Content-Type: application/json');
        echo json_encode(['results' => array_slice($allResults, 0, 10)]);
    }
}
?>
