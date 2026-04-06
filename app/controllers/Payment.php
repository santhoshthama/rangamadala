<?php

class Payment
{
    use Controller;

    private $paymentModel;
    private $serviceRequestModel;
    private $payHereHelper;

    public function __construct()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $this->paymentModel = $this->getModel('M_payment');
        $this->serviceRequestModel = $this->getModel('M_service_request');
        $this->payHereHelper = new PayHereHelper();
    }

    public function checkout()
    {
        $requestId = $_GET['request_id'] ?? null;
        $amount = $_GET['amount'] ?? null;
        $type = $_GET['type'] ?? 'advance';

        if (!$requestId || !$amount) {
            $_SESSION['error'] = 'Invalid payment parameters';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = 'Service request not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $serviceDetails = $request->service_details_json ? json_decode($request->service_details_json, true) : [];
        $providerResponse = is_array($serviceDetails) ? ($serviceDetails['provider_response'] ?? []) : [];

        $userModel = $this->getModel('M_login');
        $user = $userModel ? $userModel->getUserById($_SESSION['user_id']) : null;

        $data = [
            'request' => $request,
            'amount' => $amount,
            'type' => $type,
            'provider_response' => $providerResponse,
            'user' => $user,
            'payhere_config' => [
                'merchant_id' => $this->payHereHelper->getConfig('merchant_id'),
                'sandbox' => $this->payHereHelper->getConfig('sandbox', true),
                'return_url' => $this->payHereHelper->getConfig('return_url'),
                'cancel_url' => $this->payHereHelper->getConfig('cancel_url', ROOT . '/Production_manager/manage_services'),
                'notify_url' => $this->payHereHelper->getConfig('notify_url', ROOT . '/Payment/notify')
            ]
        ];

        $this->view('payment_checkout', $data);
    }

    public function createPayHerePayment()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $type = $_POST['type'] ?? 'advance';

        if (!$requestId || !$amount) {
            echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            echo json_encode(['success' => false, 'error' => 'Service request not found']);
            exit;
        }

        $existingPayment = $this->paymentModel->getPaymentByType($requestId, $type);
        if ($existingPayment && ($existingPayment->payment_status ?? '') === 'pending' && ($existingPayment->payment_gateway ?? '') === 'payhere') {
            $paymentId = $existingPayment->id;
            $orderId = $existingPayment->gateway_order_id;
        } else {
            $orderId = 'REQ-' . $requestId . '-' . $type . '-' . time();
            $paymentId = $this->paymentModel->createPayment([
                'service_request_id' => $requestId,
                'payment_type' => $type,
                'amount' => $amount,
                'payment_gateway' => 'payhere',
                'payment_status' => 'pending',
                'paid_by' => $_SESSION['user_id'],
                'paid_to' => $request->provider_id ?? null,
                'gateway_order_id' => $orderId,
                'transaction_response' => json_encode(['source' => 'payhere_init'])
            ]);

            if (!$paymentId) {
                echo json_encode(['success' => false, 'error' => 'Unable to create payment']);
                exit;
            }
        }

        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'hash' => $this->payHereHelper->generateHash($orderId, $amount),
            'payment_id' => $paymentId
        ]);
        exit;
    }

    public function bankForm()
    {
        $this->renderManualPaymentForm('payment_bank_upload', 'Bank Transfer Payment');
    }

    public function cashForm()
    {
        $this->renderManualPaymentForm('payment_cash_form', 'Cash Payment');
    }

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

        $_SESSION['success'] = 'Cash payment recorded. Waiting for provider confirmation.';
        header('Location: ' . ROOT . '/Payment/receipt/' . $paymentId);
        exit;
    }

    public function submitBankSlip()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $requestId = $_POST['request_id'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $type = $_POST['type'] ?? 'advance';

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

        $this->cancelRejectedPayments($requestId);

        $file = $_FILES['bank_slip'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Failed to upload bank slip';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = 'Bank slip must be smaller than 5MB';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
            exit;
        }

        $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'application/pdf' => 'pdf'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!isset($allowedMime[$mimeType])) {
            $_SESSION['error'] = 'Only JPG, PNG, or PDF files are allowed';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
            exit;
        }

        $uploadDir = dirname(__DIR__, 2) . '/app/uploads/bank_slips/';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            $_SESSION['error'] = 'Could not create upload directory';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
            exit;
        }

        $extension = $allowedMime[$mimeType];
        $filename = 'slip_' . (int)$requestId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $_SESSION['error'] = 'Could not save uploaded slip';
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
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
            header('Location: ' . ROOT . '/Payment/bankForm?request_id=' . (int)$requestId . '&amount=' . urlencode((string)$amount) . '&type=' . urlencode($type));
            exit;
        }

        $_SESSION['success'] = 'Bank slip uploaded successfully. Provider can now review it.';
        header('Location: ' . ROOT . '/Payment/receipt/' . $paymentId);
        exit;
    }

    public function viewBankSlip($paymentId = null)
    {
        if (!$paymentId) {
            http_response_code(404);
            exit;
        }

        $payment = $this->paymentModel->getPaymentById($paymentId);
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
        if ((int)$payment->paid_by !== (int)$currentUserId && (int)$payment->paid_to !== (int)$currentUserId) {
            http_response_code(403);
            exit;
        }

        $fileName = basename($bankSlipPath);
        $filePath = dirname(__DIR__, 2) . '/app/uploads/bank_slips/' . $fileName;
        if (!file_exists($filePath)) {
            http_response_code(404);
            exit;
        }

        header('Content-Type: ' . mime_content_type($filePath));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    public function confirmCashPayment()
    {
        $this->handleProviderVerification('cash');
    }

    public function confirmBankPayment()
    {
        $this->handleProviderVerification('bank_transfer');
    }

    public function rejectManualPayment()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $paymentId = $_POST['payment_id'] ?? null;
        $reason = trim((string)($_POST['reason'] ?? ''));

        if (!$paymentId || $reason === '') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing payment id or reason']);
            return;
        }

        $payment = $this->paymentModel->getPaymentById($paymentId);
        if (!$payment || !in_array(($payment->payment_gateway ?? ''), ['cash', 'bank_transfer'], true)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Payment not found']);
            return;
        }

        if ((int)$payment->paid_to !== (int)$_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Not allowed for this payment']);
            return;
        }

        $transactionData = $payment->transaction_response ? json_decode($payment->transaction_response, true) : [];
        if (!is_array($transactionData)) {
            $transactionData = [];
        }

        $transactionData['provider_verification_status'] = 'rejected';
        $transactionData['provider_verification_reason'] = $reason;
        $transactionData['provider_verification_at'] = date('Y-m-d H:i:s');
        $transactionData['provider_verification_by'] = (int)$_SESSION['user_id'];

        $this->paymentModel->updatePaymentStatus($payment->id, 'pending', json_encode($transactionData));
        echo json_encode(['success' => true]);
    }

    public function receipt($paymentId = null)
    {
        if (!$paymentId) {
            $_SESSION['error'] = 'Invalid payment reference';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $payment = $this->paymentModel->getPaymentById($paymentId);
        if (!$payment) {
            $_SESSION['error'] = 'Payment not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $currentUserId = $_SESSION['user_id'] ?? 0;
        if ((int)$payment->paid_by !== (int)$currentUserId && (int)$payment->paid_to !== (int)$currentUserId) {
            $_SESSION['error'] = 'You are not authorized to view this receipt';
            header('Location: ' . ROOT . '/Home');
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
        $userModel = $this->getModel('M_login');

        $data = [
            'payment' => $payment,
            'request' => $request,
            'paidBy' => $payment->paid_by ? $userModel->getUserById($payment->paid_by) : null,
            'paidTo' => $payment->paid_to ? $userModel->getUserById($payment->paid_to) : null,
            'transactionData' => $payment->transaction_response ? json_decode($payment->transaction_response, true) : [],
            'isProviderView' => ((int)$payment->paid_to === (int)$currentUserId),
            'receipt_number' => 'RCP-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)
        ];

        $this->view('payment_receipt', $data);
    }

    public function paymentReturn()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $orderId = $_GET['order_id'] ?? null;
        if (!$orderId) {
            $_SESSION['error'] = 'Invalid payment reference';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $payment = $this->paymentModel->getPaymentByOrderId($orderId);
        if (!$payment) {
            $_SESSION['error'] = 'Payment record not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        if (($payment->payment_status ?? '') === 'pending') {
            $this->paymentModel->updatePaymentStatus($payment->id, 'completed', json_encode([
                'source' => 'return_url',
                'order_id' => $orderId,
                'status' => 'completed',
                'returned_at' => date('Y-m-d H:i:s')
            ]));
            $this->updateServiceRequestPaymentStatus($payment->service_request_id);
        }

        header('Location: ' . ROOT . '/Payment/receipt/' . $payment->id);
        exit;
    }

    public function notify()
    {
        http_response_code(200);
        echo 'OK';
    }

    private function renderManualPaymentForm($viewName, $pageTitle)
    {
        $requestId = $_GET['request_id'] ?? null;
        $amount = $_GET['amount'] ?? null;
        $type = $_GET['type'] ?? 'advance';

        if (!$requestId || !$amount) {
            $_SESSION['error'] = 'Invalid payment parameters';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            $_SESSION['error'] = 'Service request not found';
            header('Location: ' . ROOT . '/Production_manager/manage_services');
            exit;
        }

        $this->view($viewName, [
            'request' => $request,
            'amount' => $amount,
            'type' => $type,
            'pageTitle' => $pageTitle
        ]);
    }

    private function handleProviderVerification($gateway)
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'service_provider') {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        $paymentId = $_POST['payment_id'] ?? null;
        if (!$paymentId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Missing payment id']);
            return;
        }

        $payment = $this->paymentModel->getPaymentById($paymentId);
        if (!$payment || ($payment->payment_gateway ?? '') !== $gateway) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Payment not found']);
            return;
        }

        if ((int)$payment->paid_to !== (int)$_SESSION['user_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Not allowed for this payment']);
            return;
        }

        if (($payment->payment_status ?? '') !== 'pending') {
            echo json_encode(['success' => true, 'message' => 'Payment already confirmed']);
            return;
        }

        $transactionData = $payment->transaction_response ? json_decode($payment->transaction_response, true) : [];
        if (!is_array($transactionData)) {
            $transactionData = [];
        }
        $transactionData['provider_confirmed_at'] = date('Y-m-d H:i:s');
        $transactionData['provider_confirmed_by'] = (int)$_SESSION['user_id'];

        if (!$this->paymentModel->updatePaymentStatus($payment->id, 'completed', json_encode($transactionData))) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to confirm payment']);
            return;
        }

        $this->updateServiceRequestPaymentStatus($payment->service_request_id);
        echo json_encode(['success' => true]);
    }

    private function updateServiceRequestPaymentStatus($requestId)
    {
        $fullPaid = $this->paymentModel->isFullyPaid($requestId);
        $advancePaid = $this->paymentModel->isAdvancePaid($requestId);
        $remainingPaid = $this->paymentModel->isRemainingPaid($requestId);

        if ($fullPaid || ($advancePaid && $remainingPaid)) {
            $this->serviceRequestModel->updatePaymentStatus($requestId, 'paid');
            $request = $this->serviceRequestModel->getRequestById($requestId);
            if ($request && strtolower((string)$request->status) === 'completed') {
                $this->serviceRequestModel->updateRequestStatus($requestId, 'completed_paid');
            }
        } elseif ($advancePaid || $remainingPaid) {
            $this->serviceRequestModel->updatePaymentStatus($requestId, 'partially_paid');
        }
    }

    private function cancelRejectedPayments($requestId)
    {
        $payments = $this->paymentModel->getPaymentsByRequest($requestId);
        if (!$payments) {
            return;
        }

        foreach ($payments as $payment) {
            if (strtolower($payment->payment_status ?? '') !== 'pending') {
                continue;
            }

            $transactionData = !empty($payment->transaction_response) ? json_decode($payment->transaction_response, true) : [];
            if (is_array($transactionData) && ($transactionData['provider_verification_status'] ?? '') === 'rejected') {
                $this->paymentModel->updatePaymentStatus($payment->id, 'cancelled', json_encode(array_merge($transactionData, [
                    'cancelled_at' => date('Y-m-d H:i:s'),
                    'cancelled_reason' => 'Replaced with new payment submission'
                ])));
            }
        }
    }
}
