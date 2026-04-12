<?php

class ServicePayment
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

        $providerModel = new M_service_provider();
        $provider = $providerModel->getProviderById($_SESSION['user_id']);

        $requestModel = $this->getModel('M_service_request');
        $paymentModel = $this->getModel('M_payment');

        $requests = $requestModel ? $requestModel->getRequestsByProvider($_SESSION['user_id']) : [];
        $requestIds = array_map(function ($request) {
            return (int)$request->id;
        }, $requests);

        $paymentStatsMap = $paymentModel ? $paymentModel->getRequestPaymentStats($requestIds) : [];
        $paymentStepsMap = $paymentModel ? $paymentModel->getPaymentsByRequestIds($requestIds) : [];

        $servicePayments = [];
        $summary = [
            'total_quoted' => 0,
            'total_received' => 0,
            'total_outstanding' => 0,
            'all' => 0,
            'unpaid' => 0,
            'partially_paid' => 0,
            'fully_paid' => 0,
            'verification_pending' => 0,
        ];

        foreach ($requests as $request) {
            $providerResponse = [];
            if (!empty($request->provider_response) && is_array($request->provider_response)) {
                $providerResponse = $request->provider_response;
            } elseif (!empty($request->service_details_json)) {
                $details = json_decode($request->service_details_json, true);
                if (is_array($details) && isset($details['provider_response']) && is_array($details['provider_response'])) {
                    $providerResponse = $details['provider_response'];
                }
            }

            $quoteAmount = isset($providerResponse['quote_amount']) ? (float)$providerResponse['quote_amount'] : 0.0;
            $needsAdvance = !empty($providerResponse['needs_advance']);
            $advanceAmount = isset($providerResponse['advance_amount']) ? (float)$providerResponse['advance_amount'] : 0.0;
            $advanceDueDate = $providerResponse['advance_due_date'] ?? null;
            $finalPaymentDueDate = $providerResponse['final_payment_due_date'] ?? null;

            $stats = $paymentStatsMap[(int)$request->id] ?? [
                'total_paid' => 0.0,
                'advance_paid' => false,
                'remaining_paid' => false,
                'full_paid' => false,
            ];

            $totalPaid = (float)$stats['total_paid'];
            $remainingAmount = max($quoteAmount - $totalPaid, 0.0);

            $statusKey = 'unpaid';
            if ($quoteAmount > 0 && $remainingAmount <= 0.0001) {
                $statusKey = 'fully_paid';
            } elseif ($totalPaid > 0) {
                $statusKey = 'partially_paid';
            }

            $nextDueAmount = 0.0;
            if ($statusKey !== 'fully_paid') {
                if ($needsAdvance && empty($stats['advance_paid']) && $advanceAmount > 0) {
                    $nextDueAmount = min($advanceAmount, $remainingAmount > 0 ? $remainingAmount : $advanceAmount);
                } else {
                    $nextDueAmount = $remainingAmount;
                }
            }

            $verificationPending = in_array($request->payment_gateway ?? '', ['cash', 'bank_transfer'], true)
                && strtolower($request->advance_payment_status ?? '') === 'pending';

            $summary['all']++;
            $summary[$statusKey]++;
            if ($verificationPending) {
                $summary['verification_pending']++;
            }

            $summary['total_quoted'] += $quoteAmount;
            $summary['total_received'] += $totalPaid;
            $summary['total_outstanding'] += $remainingAmount;

            $paymentSteps = [];
            if (isset($paymentStepsMap[(int)$request->id]) && is_array($paymentStepsMap[(int)$request->id])) {
                foreach ($paymentStepsMap[(int)$request->id] as $step) {
                    $paymentSteps[] = [
                        'id' => (int)($step->id ?? 0),
                        'payment_type' => (string)($step->payment_type ?? ''),
                        'amount' => (float)($step->amount ?? 0),
                        'payment_gateway' => (string)($step->payment_gateway ?? ''),
                        'payment_status' => (string)($step->payment_status ?? ''),
                        'reference_number' => (string)($step->reference_number ?? ''),
                        'paid_at' => $step->paid_at ?? null,
                        'created_at' => $step->created_at ?? null,
                    ];
                }
            }

            $servicePayments[] = [
                'request_id' => (int)$request->id,
                'drama_name' => $request->drama_name ?? 'Unknown Drama',
                'service_type' => $request->service_type ?? 'Service',
                'requester_name' => $request->requester_name ?? 'Unknown',
                'quote_amount' => $quoteAmount,
                'total_paid' => $totalPaid,
                'remaining_amount' => $remainingAmount,
                'next_due_amount' => $nextDueAmount,
                'status_key' => $statusKey,
                'verification_pending' => $verificationPending,
                'latest_payment_method' => $request->payment_gateway ?? null,
                'latest_payment_status' => $request->advance_payment_status ?? null,
                'payment_type' => $request->payment_type ?? null,
                'reference_number' => $request->reference_number ?? null,
                'needs_advance' => $needsAdvance,
                'advance_amount' => $advanceAmount,
                'advance_due_date' => $advanceDueDate,
                'final_payment_due_date' => $finalPaymentDueDate,
                'request_status' => $request->status ?? null,
                'payment_steps' => $paymentSteps,
                'payment_count' => count($paymentSteps),
            ];
        }

        $data = [
            'provider' => $provider,
            'servicePayments' => $servicePayments,
            'summary' => $summary,
            'pageTitle' => 'Payments',
        ];

        $this->view('service_payment', $data);
    }
}
