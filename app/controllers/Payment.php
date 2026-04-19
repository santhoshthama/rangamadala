<?php

class Payment
{
    use Controller;
    
    private $paymentModel;
    private $serviceRequestModel;
    private $payHereHelper;
    
    public function __construct()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
        
        $this->paymentModel = $this->getModel('M_payment');
        $this->serviceRequestModel = $this->getModel('M_service_request');
        $this->payHereHelper = new PayHereHelper();
    }

    private function jsonResponse(array $payload, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        http_response_code($statusCode);
        echo json_encode($payload);
        exit;
    }

    private function getProviderResponse($request): array
    {
        $details = !empty($request->service_details_json) ? json_decode((string)$request->service_details_json, true) : [];
        return is_array($details['provider_response'] ?? null) ? $details['provider_response'] : [];
    }

    private function calculateExpectedPayableAmount($request, string $type): float
    {
        $providerResponse = $this->getProviderResponse($request);
        $quoteAmount = (float)($providerResponse['quote_amount'] ?? $request->budget ?? 0);
        $totalPaid = (float)$this->paymentModel->getTotalPaid((int)$request->id);
        $outstanding = max(0, $quoteAmount - $totalPaid);

        $needsAdvance = !empty($providerResponse['needs_advance']);
        $advanceAmount = max(0, (float)($providerResponse['advance_amount'] ?? 0));

        if ($type === 'advance') {
            if (!$needsAdvance) {
                return 0.0;
            }
            return min($advanceAmount, $outstanding);
        }

        if ($type === 'remaining' || $type === 'full') {
            return $outstanding;
        }

        return 0.0;
    }
    
    /**
     * Display checkout page
     */
    public function checkout()
    {
        $requestId = $_GET['request_id'] ?? null;
        $amount = $_GET['amount'] ?? null;
        $type = $_GET['type'] ?? 'advance';
        $forcedOverdue = isset($_GET['forced_overdue']) && (int)$_GET['forced_overdue'] === 1;

        if (!in_array($type, ['advance', 'remaining', 'full'], true)) {
            $type = 'advance';
        }
        
        if (!$requestId || !$amount) {
            $_SESSION['error'] = 'Invalid payment parameters';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }
        
        // Get service request details
        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = 'Service request not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }
        
        // Parse service details for provider response
        $providerResponse = $this->getProviderResponse($request);

        $expectedAmount = $this->calculateExpectedPayableAmount($request, $type);
        if ($expectedAmount <= 0) {
            $_SESSION['success'] = 'This request is already fully settled. No additional payment is required.';
            header('Location: ' . ROOT . '/Production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0));
            exit;
        }

        $requestedAmount = is_numeric($amount) ? (float)$amount : 0.0;
        $amount = number_format((abs($requestedAmount - $expectedAmount) <= 0.01) ? $requestedAmount : $expectedAmount, 2, '.', '');
        
        // Get user details for payment
        $userId = $_SESSION['user_id'];
        $userModel = $this->getModel('M_login');
        $user = $userModel ? $userModel->getUserById($userId) : null;
        
        if (!$user) {
            $_SESSION['error'] = 'User information not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }
        
        $data = [
            'request' => $request,
            'amount' => $amount,
            'type' => $type,
            'provider_response' => $providerResponse,
            'user' => $user,
            'forced_overdue' => $forcedOverdue,
            'forced_overdue_message' => $_SESSION['warning_message'] ?? '',
            'payhere_config' => [
                'merchant_id' => $this->payHereHelper->getConfig('merchant_id'),
                'sandbox' => $this->payHereHelper->getConfig('sandbox'),
                'return_url' => $this->payHereHelper->getConfig('return_url'),
                'cancel_url' => ROOT . '/Production_manager/manage_services',
                'notify_url' => $this->payHereHelper->getConfig('notify_url'),
            ]
        ];

        if (isset($_SESSION['warning_message'])) {
            unset($_SESSION['warning_message']);
        }
        
        $this->view('payment_checkout', $data);
    }

    /**
     * AJAX endpoint: Create PayHere payment and return order details
     */
    public function createPayHerePayment()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid request method'], 405);
            }

            if (!isset($_SESSION['user_id'])) {
                $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
            }

            $requestId = (int)($_POST['request_id'] ?? 0);
            $rawAmount = $_POST['amount'] ?? null;
            $amount = is_numeric($rawAmount) ? (float)$rawAmount : 0.0;
            $type = trim((string)($_POST['type'] ?? 'advance'));

            if ($requestId <= 0 || $amount <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'Invalid parameters'], 400);
            }

            if (!in_array($type, ['advance', 'remaining', 'full'], true)) {
                $type = 'advance';
            }

            $request = $this->serviceRequestModel->getRequestById($requestId);
            if (!$request) {
                $this->jsonResponse(['success' => false, 'error' => 'Service request not found'], 404);
            }

            $expectedAmount = $this->calculateExpectedPayableAmount($request, $type);
            if ($expectedAmount <= 0) {
                $this->jsonResponse(['success' => false, 'error' => 'No additional payment is required for this request'], 400);
            }
            $amount = $expectedAmount;

            $userId = (int)$_SESSION['user_id'];

            // Check if a pending PayHere payment already exists for this request and type
            $existingPayment = $this->paymentModel->getPaymentByType($requestId, $type);

            if ($existingPayment && $existingPayment->payment_status === 'pending' && $existingPayment->payment_gateway === 'payhere') {
                // Reuse existing pending payment
                $paymentId = (int)$existingPayment->id;
                $order_id = (string)$existingPayment->gateway_order_id;
            } else {
                // Generate order ID
                $order_id = 'REQ-' . $requestId . '-' . $type . '-' . time();

                // Create PayHere payment
                $paymentId = $this->paymentModel->createPayment([
                    'service_request_id' => $requestId,
                    'payment_type' => $type,
                    'amount' => $amount,
                    'payment_gateway' => 'payhere',
                    'payment_status' => 'pending',
                    'paid_by' => $userId,
                    'paid_to' => $request->provider_id ?? null,
                    'gateway_order_id' => $order_id,
                    'transaction_response' => json_encode(['source' => 'payhere_init'])
                ]);

                if (!$paymentId) {
                    $this->jsonResponse(['success' => false, 'error' => 'Unable to create payment'], 500);
                }
            }

            $formattedAmount = number_format($amount, 2, '.', '');

            // Generate hash for PayHere
            $hash = $this->payHereHelper->generateHash($order_id, $formattedAmount);

            $this->jsonResponse([
                'success' => true,
                'order_id' => $order_id,
                'hash' => $hash,
                'payment_id' => $paymentId,
                'amount' => $formattedAmount,
                'type' => $type
            ]);
        } catch (Throwable $e) {
            error_log('createPayHerePayment error: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'error' => 'Unable to initialize payment'], 500);
        }
    }

    /**
     * Display bank transfer upload form
     */
    public function bankForm()
    {
        $requestId = $_GET['request_id'] ?? null;
        $amount = $_GET['amount'] ?? null;
        $type = $_GET['type'] ?? 'advance';

        if (!in_array($type, ['advance', 'remaining', 'full'], true)) {
            $type = 'advance';
        }

        if (!$requestId || !$amount) {
            $_SESSION['error'] = 'Invalid bank payment parameters';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = 'Service request not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $expectedAmount = $this->calculateExpectedPayableAmount($request, $type);
        if ($expectedAmount <= 0) {
            $_SESSION['success'] = 'This request is already fully settled. No additional payment is required.';
            header('Location: ' . ROOT . '/Production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0));
            exit;
        }
        $amount = number_format($expectedAmount, 2, '.', '');

        $data = [
            'request' => $request,
            'amount' => $amount,
            'type' => $type,
        ];

        $this->view('payment_bank_upload', $data);
    }

    /**
     * Display cash payment form
     */
    public function cashForm()
    {
        $requestId = $_GET['request_id'] ?? null;
        $amount = $_GET['amount'] ?? null;
        $type = $_GET['type'] ?? 'advance';

        if (!in_array($type, ['advance', 'remaining', 'full'], true)) {
            $type = 'advance';
        }

        if (!$requestId || !$amount) {
            $_SESSION['error'] = 'Invalid cash payment parameters';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = 'Service request not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $expectedAmount = $this->calculateExpectedPayableAmount($request, $type);
        if ($expectedAmount <= 0) {
            $_SESSION['success'] = 'This request is already fully settled. No additional payment is required.';
            header('Location: ' . ROOT . '/Production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0));
            exit;
        }
        $amount = number_format($expectedAmount, 2, '.', '');

        $data = [
            'request' => $request,
            'amount' => $amount,
            'type' => $type,
        ];

        $this->view('payment_cash_form', $data);
    }

    /**
     * Save cash payment record (pending until provider confirms)
     */
    public function submitCashPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $type = $_POST['type'] ?? 'advance';
        $receivedDate = $_POST['received_date'] ?? null;
        $note = trim($_POST['note'] ?? '');

        if (!in_array($type, ['advance', 'remaining', 'full'], true)) {
            $type = 'advance';
        }

        if (!$requestId || !$amount || !$receivedDate) {
            $_SESSION['error'] = 'Missing required cash payment details';
            header('Location: ' . ROOT . '/Payment/cashForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = 'Service request not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $expectedAmount = $this->calculateExpectedPayableAmount($request, $type);
        if ($expectedAmount <= 0) {
            $_SESSION['success'] = 'This request is already fully settled. No additional payment is required.';
            header('Location: ' . ROOT . '/Production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0));
            exit;
        }
        $amount = number_format($expectedAmount, 2, '.', '');

        // Cancel any previous rejected payments for this request
        $this->cancelRejectedPayments($requestId);

        $paymentId = $this->paymentModel->createPayment([
            'service_request_id' => $requestId,
            'payment_type' => $type,
            'amount' => $amount,
            'payment_gateway' => 'cash',
            'payment_status' => 'pending',
            'paid_by' => $_SESSION['user_id'],
            'paid_to' => $request->provider_id ?? null,
            'transaction_response' => json_encode([
                'source' => 'cash_payment',
                'recorded_at' => date('Y-m-d H:i:s'),
                'received_date' => $receivedDate,
                'note' => $note !== '' ? $note : null
            ])
        ]);

        if (!$paymentId) {
            $_SESSION['error'] = 'Could not save cash payment record';
            header('Location: ' . ROOT . '/Payment/cashForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
            exit;
        }

        $this->notifyProviderAction(
            (int)($request->provider_id ?? 0),
            (int)($request->drama_id ?? 0),
            'payment_submitted_by_pm',
            'Cash Payment Submitted',
            'Production manager submitted a cash payment for your service request. Please verify the payment details.',
            ROOT . '/ServiceRequests'
        );

        $_SESSION['success'] = 'Cash payment recorded. Waiting for provider confirmation.';
        header('Location: ' . ROOT . '/Payment/receipt/' . $paymentId);
        exit;
    }

    /**
     * Save bank transfer evidence (slip upload)
     */
    public function submitBankSlip()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $type = $_POST['type'] ?? 'advance';

        if (!in_array($type, ['advance', 'remaining', 'full'], true)) {
            $type = 'advance';
        }

        if (!$requestId || !$amount || !isset($_FILES['bank_slip'])) {
            $_SESSION['error'] = 'Missing required bank payment details';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = 'Service request not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $expectedAmount = $this->calculateExpectedPayableAmount($request, $type);
        if ($expectedAmount <= 0) {
            $_SESSION['success'] = 'This request is already fully settled. No additional payment is required.';
            header('Location: ' . ROOT . '/Production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0));
            exit;
        }
        $amount = number_format($expectedAmount, 2, '.', '');

        // Cancel any previous rejected payments for this request
        $this->cancelRejectedPayments($requestId);

        $file = $_FILES['bank_slip'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Failed to upload bank slip';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode($amount) . '&type=' . urlencode($type));
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'Bank slip must be smaller than 5MB';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode($amount) . '&type=' . urlencode($type));
            exit;
        }

        $allowedMime = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf'
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowedMime[$mimeType])) {
            $_SESSION['error'] = 'Only JPG, PNG, or PDF files are allowed';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode($amount) . '&type=' . urlencode($type));
            exit;
        }

        $uploadDir = dirname(__DIR__, 2) . '/app/uploads/bank_slips/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $_SESSION['error'] = 'Could not create upload directory';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode($amount) . '&type=' . urlencode($type));
            exit;
        }

        $extension = $allowedMime[$mimeType];
        $filename = 'slip_' . (int)$requestId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $_SESSION['error'] = 'Could not save uploaded slip';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode($amount) . '&type=' . urlencode($type));
            exit;
        }

        $paymentId = $this->paymentModel->createBankPayment([
            'service_request_id' => $requestId,
            'payment_type' => $type,
            'amount' => $amount,
            'payment_status' => 'pending',
            'paid_by' => $_SESSION['user_id'],
            'paid_to' => $request->provider_id ?? null,
            'transaction_response' => json_encode([
                'source' => 'bank_slip_upload',
                'uploaded_at' => date('Y-m-d H:i:s'),
                'bank_slip_path' => $filename,
                'bank_submitted_at' => date('Y-m-d H:i:s')
            ])
        ]);

        if (!$paymentId) {
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }

            $_SESSION['error'] = 'Could not save bank payment';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode($amount) . '&type=' . urlencode($type));
            exit;
        }

        $this->notifyProviderAction(
            (int)($request->provider_id ?? 0),
            (int)($request->drama_id ?? 0),
            'payment_submitted_by_pm',
            'Bank Transfer Submitted',
            'Production manager uploaded bank transfer proof for your service request. Please verify the payment details.',
            ROOT . '/ServiceRequests'
        );

        $_SESSION['success'] = 'Bank slip uploaded successfully. Provider can now review it.';
        header('Location: ' . ROOT . '/Payment/receipt/' . $paymentId);
        exit;
    }

    /**
     * Securely view uploaded bank slip (only payer or provider)
     */
    public function viewBankSlip($payment_id = null)
    {
        if (!$payment_id) {
            http_response_code(404);
            exit;
        }

        $payment = $this->paymentModel->getPaymentById($payment_id);
        if (!$payment) {
            http_response_code(404);
            exit;
        }

        $transactionData = $payment->transaction_response ? json_decode($payment->transaction_response, true) : [];
        $bankSlipPath = $transactionData['bank_slip_path'] ?? null;
        if (empty($bankSlipPath)) {
            http_response_code(404);
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? 0;
        $isPayer = (int)$payment->paid_by === (int)$currentUserId;
        $isProvider = (int)$payment->paid_to === (int)$currentUserId;

        if (!$isPayer && !$isProvider) {
            http_response_code(403);
            exit;
        }

        $fileName = basename($bankSlipPath);
        $filePath = dirname(__DIR__, 2) . '/app/uploads/bank_slips/' . $fileName;

        if (!file_exists($filePath)) {
            http_response_code(404);
            exit;
        }

        $mimeType = mime_content_type($filePath);
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    /**
     * Provider confirms pending cash payment
     */
    public function confirmCashPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $paymentId = $_POST['payment_id'] ?? null;
        if (!$paymentId) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing payment id'], 400);
        }

        $payment = $this->paymentModel->getPaymentById($paymentId);
        if (!$payment) {
            $this->jsonResponse(['success' => false, 'error' => 'Payment not found'], 404);
        }

        if (($payment->payment_gateway ?? '') !== 'cash') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid payment type'], 400);
        }

        if ((int)$payment->paid_to !== (int)$_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'error' => 'Not allowed for this payment'], 403);
        }

        if (($payment->payment_status ?? '') !== 'pending') {
            $this->jsonResponse(['success' => true, 'message' => 'Payment already confirmed']);
        }

        $transactionData = $payment->transaction_response ? json_decode($payment->transaction_response, true) : [];
        if (!is_array($transactionData)) {
            $transactionData = [];
        }
        $transactionData['provider_confirmed_at'] = date('Y-m-d H:i:s');
        $transactionData['provider_confirmed_by'] = (int)$_SESSION['user_id'];

        $ok = $this->paymentModel->updatePaymentStatus(
            $payment->id,
            'completed',
            json_encode($transactionData)
        );

        if (!$ok) {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to confirm payment'], 500);
        }

        $this->updateServiceRequestPaymentStatus($payment->service_request_id);

        $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
        if ($request) {
            $this->notifyRequesterAction(
                (int)($request->requested_by ?? 0),
                (int)($request->drama_id ?? 0),
                'pm_provider_confirmed_manual_payment',
                'Provider Confirmed Cash Payment',
                ($request->provider_name ?? 'Provider') . ' confirmed receiving your cash payment for "' . ($request->service_type ?? 'service') . '".',
                ROOT . '/production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0)
            );
        }

        $this->jsonResponse(['success' => true]);
    }

    /**
     * Provider confirms pending bank transfer payment
     */
    public function confirmBankPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $paymentId = $_POST['payment_id'] ?? null;
        if (!$paymentId) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing payment id'], 400);
        }

        $payment = $this->paymentModel->getPaymentById($paymentId);
        if (!$payment) {
            $this->jsonResponse(['success' => false, 'error' => 'Payment not found'], 404);
        }

        if (($payment->payment_gateway ?? '') !== 'bank_transfer') {
            $this->jsonResponse(['success' => false, 'error' => 'Invalid payment type'], 400);
        }

        if ((int)$payment->paid_to !== (int)$_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'error' => 'Not allowed for this payment'], 403);
        }

        if (($payment->payment_status ?? '') !== 'pending') {
            $this->jsonResponse(['success' => true, 'message' => 'Payment already confirmed']);
        }

        $transactionData = $payment->transaction_response ? json_decode($payment->transaction_response, true) : [];
        if (!is_array($transactionData)) {
            $transactionData = [];
        }
        $transactionData['provider_confirmed_at'] = date('Y-m-d H:i:s');
        $transactionData['provider_confirmed_by'] = (int)$_SESSION['user_id'];

        $ok = $this->paymentModel->updatePaymentStatus(
            $payment->id,
            'completed',
            json_encode($transactionData)
        );

        if (!$ok) {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to confirm payment'], 500);
        }

        $this->updateServiceRequestPaymentStatus($payment->service_request_id);

        $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
        if ($request) {
            $this->notifyRequesterAction(
                (int)($request->requested_by ?? 0),
                (int)($request->drama_id ?? 0),
                'pm_provider_confirmed_manual_payment',
                'Provider Confirmed Bank Transfer',
                ($request->provider_name ?? 'Provider') . ' confirmed your bank transfer payment for "' . ($request->service_type ?? 'service') . '".',
                ROOT . '/production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0)
            );
        }

        $this->jsonResponse(['success' => true]);
    }

    /**
     * Provider rejects manual payment verification (cash/bank transfer)
     */
    public function rejectManualPayment()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            $this->jsonResponse(['success' => false, 'error' => 'Unauthorized'], 403);
        }

        $paymentId = $_POST['payment_id'] ?? null;
        $reason = trim((string)($_POST['reason'] ?? ''));

        if (!$paymentId) {
            $this->jsonResponse(['success' => false, 'error' => 'Missing payment id'], 400);
        }

        if ($reason === '') {
            $this->jsonResponse(['success' => false, 'error' => 'Reason is required'], 400);
        }

        $payment = $this->paymentModel->getPaymentById($paymentId);
        if (!$payment) {
            $this->jsonResponse(['success' => false, 'error' => 'Payment not found'], 404);
        }

        $gateway = $payment->payment_gateway ?? '';
        if (!in_array($gateway, ['cash', 'bank_transfer'], true)) {
            $this->jsonResponse(['success' => false, 'error' => 'Only cash or bank transfer can be rejected'], 400);
        }

        if ((int)$payment->paid_to !== (int)$_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'error' => 'Not allowed for this payment'], 403);
        }

        if (($payment->payment_status ?? '') !== 'pending') {
            $this->jsonResponse(['success' => true, 'message' => 'Payment is already finalized']);
        }

        $transactionData = $payment->transaction_response ? json_decode($payment->transaction_response, true) : [];
        if (!is_array($transactionData)) {
            $transactionData = [];
        }

        $transactionData['provider_verification_status'] = 'rejected';
        $transactionData['provider_verification_reason'] = $reason;
        $transactionData['provider_verification_at'] = date('Y-m-d H:i:s');
        $transactionData['provider_verification_by'] = (int)$_SESSION['user_id'];

        $ok = $this->paymentModel->updatePaymentStatus(
            $payment->id,
            'pending',
            json_encode($transactionData)
        );

        if (!$ok) {
            $this->jsonResponse(['success' => false, 'error' => 'Failed to update verification status'], 500);
        }

        $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
        if ($request) {
            $this->notifyRequesterAction(
                (int)($request->requested_by ?? 0),
                (int)($request->drama_id ?? 0),
                'pm_provider_rejected_manual_payment',
                'Provider Could Not Verify Payment',
                ($request->provider_name ?? 'Provider') . ' could not verify your ' . ($gateway === 'bank_transfer' ? 'bank transfer' : 'cash') . ' payment for "' . ($request->service_type ?? 'service') . '". Reason: ' . $reason,
                ROOT . '/production_manager/manage_services?drama_id=' . (int)($request->drama_id ?? 0)
            );
        }

        $this->jsonResponse(['success' => true]);
    }
    
    /**
     * Update service request payment status based on completed payments
     */
    private function updateServiceRequestPaymentStatus($request_id)
    {
        $fullPaid = $this->paymentModel->isFullyPaid($request_id);
        $advancePaid = $this->paymentModel->isAdvancePaid($request_id);
        $remainingPaid = $this->paymentModel->isRemainingPaid($request_id);
        
        if ($fullPaid) {
            $this->serviceRequestModel->updatePaymentStatus($request_id, 'paid');
            
            // Auto-upgrade to completed_paid if service is completed and fully paid
            $request = $this->serviceRequestModel->getRequestById($request_id);
            if ($request && strtolower($request->status) === 'completed') {
                $this->serviceRequestModel->updateRequestStatus($request_id, 'completed_paid');
            }
        } elseif ($advancePaid && $remainingPaid) {
            $this->serviceRequestModel->updatePaymentStatus($request_id, 'paid');
            
            // Auto-upgrade to completed_paid if service is completed and fully paid
            $request = $this->serviceRequestModel->getRequestById($request_id);
            if ($request && strtolower($request->status) === 'completed') {
                $this->serviceRequestModel->updateRequestStatus($request_id, 'completed_paid');
            }
        } elseif ($advancePaid || $remainingPaid) {
            $this->serviceRequestModel->updatePaymentStatus($request_id, 'partially_paid');
        }

        $this->syncBudgetWithServicePayment((int)$request_id);
    }

    private function syncBudgetWithServicePayment(int $requestId): void
    {
        try {
            if ($requestId <= 0) {
                return;
            }

            $request = $this->serviceRequestModel->getRequestById($requestId);
            if (!$request) {
                return;
            }

            $budgetModel = $this->getModel('M_budget');
            if (!$budgetModel) {
                return;
            }

            $totalPaid = (float)$this->paymentModel->getTotalPaid($requestId);
            $allocated = isset($request->budget) && $request->budget !== null ? (float)$request->budget : $totalPaid;
            if ($allocated < $totalPaid) {
                $allocated = $totalPaid;
            }

            $status = $this->mapBudgetStatusFromServiceAndPayment(
                strtolower((string)($request->status ?? 'pending')),
                strtolower((string)($request->payment_status ?? 'unpaid')),
                $totalPaid
            );

            $itemName = trim((string)($request->service_type ?? 'Service Request'));
            if ($itemName === '') {
                $itemName = 'Service Request';
            }
            $itemName .= ' #' . $requestId;

            $payload = [
                'drama_id' => (int)($request->drama_id ?? 0),
                'item_name' => $itemName,
                'category' => trim((string)($request->service_type ?? 'Other')) ?: 'Other',
                'allocated_amount' => $allocated,
                'spent_amount' => $totalPaid,
                'status' => $status,
                'notes' => 'Auto-synced from service payment flow',
                'created_by' => (int)($request->requested_by ?? $_SESSION['user_id'] ?? 0),
                'service_request_id' => $requestId,
                'source_type' => 'payment_sync',
                'last_synced_at' => date('Y-m-d H:i:s'),
            ];

            $existing = $budgetModel->getBudgetItemByServiceRequest($requestId);
            if ($existing && isset($existing->id)) {
                $payload['notes'] = $existing->notes ?? $payload['notes'];
                $budgetModel->updateBudgetItem((int)$existing->id, $payload);
                return;
            }

            $budgetModel->createBudgetItem($payload);
        } catch (Exception $e) {
            error_log('Payment::syncBudgetWithServicePayment failed: ' . $e->getMessage());
        }
    }

    private function mapBudgetStatusFromServiceAndPayment(string $requestStatus, string $paymentStatus, float $totalPaid): string
    {
        if (in_array($requestStatus, ['rejected', 'cancelled'], true)) {
            return 'cancelled';
        }

        if ($requestStatus === 'completed_paid' || $paymentStatus === 'paid') {
            return 'completed';
        }

        if (in_array($requestStatus, ['confirmed', 'accepted', 'completed'], true)) {
            return 'approved';
        }

        return $totalPaid > 0 ? 'approved' : 'pending';
    }
    
    /**
     * Display payment receipt/invoice
     */
    public function receipt($payment_id = null)
    {
        if (!$payment_id) {
            $_SESSION['error'] = 'Invalid payment reference';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }
        
        // Get payment details
        $payment = $this->paymentModel->getPaymentById($payment_id);
        if (!$payment) {
            $_SESSION['error'] = 'Payment not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? 0;
        $isPayer = (int)$payment->paid_by === (int)$currentUserId;
        $isProvider = (int)$payment->paid_to === (int)$currentUserId;
        if (!$isPayer && !$isProvider) {
            $_SESSION['error'] = 'You are not authorized to view this receipt';
            header('Location: ' . ROOT . '/Home');
            exit;
        }
        
        // Get service request details
        $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
        
        // Get payer and payee user details
        $userModel = $this->getModel('M_login');
        $paidBy = $payment->paid_by ? $userModel->getUserById($payment->paid_by) : null;
        $paidTo = $payment->paid_to ? $userModel->getUserById($payment->paid_to) : null;
        
        // Parse transaction response
        $transactionData = $payment->transaction_response ? json_decode($payment->transaction_response, true) : [];
        
        $data = [
            'payment' => $payment,
            'request' => $request,
            'paidBy' => $paidBy,
            'paidTo' => $paidTo,
            'transactionData' => $transactionData,
            'isProviderView' => $isProvider,
            'receipt_number' => 'RCP-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)
        ];
        
        $this->view('payment_receipt', $data);
    }
    
    /**
     * Return URL Handler - User redirected here after payment
     * PayHere only redirects here on completion, so we mark payment as successful
     */
    public function return()
    {
        // Check if user is logged in for this step
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }
        
        // Log all GET parameters from PayHere for debugging
        error_log('PayHere Return URL Parameters: ' . json_encode($_GET));
        
        $order_id = $_GET['order_id'] ?? null;
        
        if (!$order_id) {
            $_SESSION['error'] = 'Invalid payment reference';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }
        
        // Fetch payment by order_id
        $payment = $this->paymentModel->getPaymentByOrderId($order_id);
        
        if (!$payment) {
            $_SESSION['error'] = 'Payment record not found. Please contact support.';
            error_log('Payment not found for order_id: ' . $order_id);
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }
        
        // Update payment status only if it's pending (prevent duplicate updates)
        if ($payment->payment_status === 'pending') {
            $this->paymentModel->updatePaymentStatus(
                $payment->id, 
                'completed',
                json_encode([
                    'source' => 'return_url',
                    'order_id' => $order_id,
                    'timestamp' => time(),
                    'status' => 'completed',
                    'returned_at' => date('Y-m-d H:i:s')
                ])
            );
            
            // Update service request payment status
            $this->updateServiceRequestPaymentStatus($payment->service_request_id);

            $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
            if ($request) {
                $this->notifyProviderAction(
                    (int)($request->provider_id ?? 0),
                    (int)($request->drama_id ?? 0),
                    'payment_completed_by_pm',
                    'Online Payment Completed',
                    'Production manager completed an online payment for your service request.',
                    ROOT . '/ServiceRequests'
                );
            }
        }
        
        // Redirect directly to receipt page (no summary page)
        header('Location: ' . ROOT . '/Payment/receipt/' . $payment->id);
        exit;
    }

    /**
     * Cancel any rejected payments for a service request
     * Called before creating a new payment to prevent duplicates
     */
    private function cancelRejectedPayments($requestId)
    {
        // Get all pending payments for this request
        $payments = $this->paymentModel->getPaymentsByRequest($requestId);
        
        if (!$payments) {
            return;
        }

        foreach ($payments as $payment) {
            // Only process pending payments
            if (strtolower($payment->payment_status ?? '') !== 'pending') {
                continue;
            }

            // Check if payment was rejected by provider
            $transactionData = !empty($payment->transaction_response) 
                ? json_decode($payment->transaction_response, true) 
                : [];
            
            if (is_array($transactionData) && 
                isset($transactionData['provider_verification_status']) && 
                $transactionData['provider_verification_status'] === 'rejected') {
                
                // Mark this rejected payment as cancelled
                $this->paymentModel->updatePaymentStatus(
                    $payment->id,
                    'cancelled',
                    json_encode(array_merge($transactionData, [
                        'cancelled_at' => date('Y-m-d H:i:s'),
                        'cancelled_reason' => 'Replaced with new payment submission'
                    ]))
                );
            }
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
            error_log('Payment notification error: ' . $e->getMessage());
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
            error_log('Payment PM notification error: ' . $e->getMessage());
        }
    }
}
