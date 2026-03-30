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

        // Get provider details for profile image
        $model = new M_service_provider();
        $provider = $model->getProviderById($_SESSION['user_id']);
        $paymentModel = $this->getModel('M_payment');
        $payments = $paymentModel ? $paymentModel->getPaymentsReceived($_SESSION['user_id']) : [];

        $pendingCount = 0;
        $receivedCount = 0;
        foreach ($payments as $payment) {
            if (in_array($payment->payment_status, ['completed', 'success'])) {
                $receivedCount++;
            } else {
                $pendingCount++;
            }
        }
        
        $data = [
            'provider' => $provider,
            'payments' => $payments,
            'pendingCount' => $pendingCount,
            'receivedCount' => $receivedCount,
            'pageTitle' => 'Payments'
        ];

        $this->view('service_payment', $data);
    }
}
