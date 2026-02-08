<?php

class Payment extends Controller
{
    private $paymentModel;
    private $serviceRequestModel;
    
    public function __construct()
    {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            redirect('login');
        }
        
        $this->paymentModel = $this->model('M_payment');
        $this->serviceRequestModel = $this->model('M_service_request');
    }
    
    /**
     * Initiate payment process
     */
    public function initiate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            return;
        }
        
        $requestId = $_POST['request_id'] ?? null;
        $amount = $_POST['amount'] ?? null;
        $type = $_POST['type'] ?? 'advance'; // 'advance', 'final', or 'full'
        $stage = $_POST['stage'] ?? 'after_completion'; // NEW: when payment happens
        
        if (!$requestId || !$amount) {
            echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
            return;
        }
        
        // Validate amount
        if (!is_numeric($amount) || $amount <= 0) {
            echo json_encode(['success' => false, 'error' => 'Invalid amount']);
            return;
        }
        
        // Get service request details
        $request = $this->serviceRequestModel->getRequestById($requestId);
        if (!$request) {
            echo json_encode(['success' => false, 'error' => 'Service request not found']);
            return;
        }
        
        // Verify user has permission (must be PM for this drama)
        // For now, checking if user is logged in
        $userId = $_SESSION['user_id'];
        
        // Check if payment already exists
        $existingPayment = $this->paymentModel->getPaymentByType($requestId, $type);
        if ($existingPayment && $existingPayment->payment_status === 'completed') {
            echo json_encode(['success' => false, 'error' => ucfirst($type) . ' payment already completed']);
            return;
        }
        
        // Create pending payment record
        $paymentId = $this->paymentModel->createPayment([
            'service_request_id' => $requestId,
            'payment_type' => $type,
            'amount' => $amount,
            'payment_gateway' => 'paypal',
            'payment_status' => 'pending',
            'paid_by' => $userId,
            'payment_stage' => $stage,
            'paid_to' => $request->provider_id
        ]);
        
        if (!$paymentId) {
            echo json_encode(['success' => false, 'error' => 'Failed to create payment record']);
            return;
        }
        
        // Store payment info in session for callback
        $_SESSION['pending_payment'] = [
            'payment_id' => $paymentId,
            'request_id' => $requestId,
            'type' => $type,
            'amount' => $amount
        ];
        
        // For now, return a mock approval URL (will be replaced with PayPal integration)
        echo json_encode([
            'success' => true,
            'payment_id' => $paymentId,
            'message' => 'Payment initiated. Ready for PayPal integration.',
            'redirect_url' => ROOT . '/Payment/process/' . $paymentId
        ]);
    }
    
    /**
     * Process payment (placeholder for PayPal integration)
     */
    public function process($payment_id = null)
    {
        if (!$payment_id) {
            die('Invalid payment ID');
        }
        
        $payment = $this->paymentModel->getPaymentById($payment_id);
        if (!$payment) {
            die('Payment not found');
        }
        
        $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
        
        // Display payment confirmation page
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Process Payment - Rangamadala</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                .payment-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                h2 { color: #333; margin-top: 0; }
                .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; }
                .label { font-weight: 600; color: #666; }
                .value { color: #333; }
                .amount { font-size: 24px; font-weight: bold; color: #d4af37; margin: 20px 0; text-align: center; }
                .btn { display: block; width: 100%; padding: 15px; margin: 10px 0; border: none; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; }
                .btn-pay { background: #0070ba; color: white; }
                .btn-pay:hover { background: #005ea6; }
                .btn-cancel { background: #e5e5e5; color: #333; }
                .btn-cancel:hover { background: #d5d5d5; }
            </style>
        </head>
        <body>
            <div class="payment-card">
                <h2>💳 Confirm Payment</h2>
                
                <div class="detail-row">
                    <span class="label">Drama:</span>
                    <span class="value"><?= htmlspecialchars($request->drama_name) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="label">Service:</span>
                    <span class="value"><?= htmlspecialchars($request->service_type) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="label">Payment Type:</span>
                    <span class="value"><?= ucfirst($payment->payment_type) ?> Payment</span>
                </div>
                
                <div class="amount">
                    Rs <?= number_format($payment->amount, 2) ?>
                </div>
                
                <form method="POST" action="<?= ROOT ?>/Payment/simulate/<?= $payment->id ?>">
                    <button type="submit" name="action" value="complete" class="btn btn-pay">
                        Pay with PayPal (Simulation)
                    </button>
                </form>
                
                <button onclick="window.location.href='<?= ROOT ?>/Payment/cancel/<?= $payment->id ?>'" class="btn btn-cancel">
                    Cancel Payment
                </button>
                
                <p style="text-align: center; color: #999; font-size: 12px; margin-top: 20px;">
                    🔒 This is a simulation. PayPal integration will be added next.
                </p>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Simulate payment completion (for testing before PayPal integration)
     */
    public function simulate($payment_id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('Payment/process/' . $payment_id);
            return;
        }
        
        $action = $_POST['action'] ?? null;
        
        if ($action === 'complete' && $payment_id) {
            $payment = $this->paymentModel->getPaymentById($payment_id);
            if ($payment) {
                // Update payment status
                $transactionId = 'SIM-' . time() . '-' . rand(1000, 9999);
                $this->paymentModel->updatePaymentStatus(
                    $payment_id,
                    'completed',
                    $transactionId,
                    json_encode(['simulated' => true, 'timestamp' => date('Y-m-d H:i:s')])
                );
                
                // Update service request payment status
                $this->updateServiceRequestPaymentStatus($payment->service_request_id);
                
                // Clear session
                unset($_SESSION['pending_payment']);
                
                // Redirect to success page
                redirect('Payment/success/' . $payment_id);
            }
        }
    }
    
    /**
     * Payment success page
     */
    public function success($payment_id = null)
    {
        $payment = $payment_id ? $this->paymentModel->getPaymentById($payment_id) : null;
        
        if ($payment) {
            $request = $this->serviceRequestModel->getRequestById($payment->service_request_id);
        }
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Successful - Rangamadala</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                .success-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
                .success-icon { font-size: 64px; color: #28a745; margin-bottom: 20px; }
                h2 { color: #333; margin: 10px 0; }
                .transaction { background: #f8f9fa; padding: 15px; border-radius: 6px; margin: 20px 0; font-family: monospace; }
                .btn { display: inline-block; padding: 12px 30px; margin: 10px 5px; border-radius: 6px; text-decoration: none; font-weight: 600; }
                .btn-primary { background: #d4af37; color: white; }
                .btn-secondary { background: #e5e5e5; color: #333; }
            </style>
        </head>
        <body>
            <div class="success-card">
                <div class="success-icon">✓</div>
                <h2>Payment Successful!</h2>
                <p>Your payment has been processed successfully.</p>
                
                <?php if ($payment): ?>
                <div class="transaction">
                    <strong>Transaction ID:</strong> <?= htmlspecialchars($payment->transaction_id) ?><br>
                    <strong>Amount:</strong> Rs <?= number_format($payment->amount, 2) ?><br>
                    <strong>Type:</strong> <?= ucfirst($payment->payment_type) ?> Payment
                </div>
                <?php endif; ?>
                
                <a href="<?= ROOT ?>/production_manager/manage_services" class="btn btn-primary">Return to Services</a>
                <a href="<?= ROOT ?>/production_manager" class="btn btn-secondary">Go to Dashboard</a>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Payment cancellation page
     */
    public function cancel($payment_id = null)
    {
        if ($payment_id) {
            $this->paymentModel->updatePaymentStatus($payment_id, 'failed', null, 'Payment cancelled by user');
        }
        
        unset($_SESSION['pending_payment']);
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Cancelled - Rangamadala</title>
            <style>
                body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
                .cancel-card { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
                .cancel-icon { font-size: 64px; color: #dc3545; margin-bottom: 20px; }
                h2 { color: #333; margin: 10px 0; }
                .btn { display: inline-block; padding: 12px 30px; margin: 10px 5px; border-radius: 6px; text-decoration: none; font-weight: 600; }
                .btn-primary { background: #d4af37; color: white; }
            </style>
        </head>
        <body>
            <div class="cancel-card">
                <div class="cancel-icon">✕</div>
                <h2>Payment Cancelled</h2>
                <p>You have cancelled the payment process.</p>
                <a href="<?= ROOT ?>/production_manager/manage_services" class="btn btn-primary">Return to Services</a>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Update service request payment status based on completed payments
     */
    private function updateServiceRequestPaymentStatus($request_id)
    {
        $advancePaid = $this->paymentModel->isAdvancePaid($request_id);
        $finalPaid = $this->paymentModel->isFinalPaid($request_id);
        
        if ($advancePaid && $finalPaid) {
            $this->serviceRequestModel->updatePaymentStatus($request_id, 'paid');
        } elseif ($advancePaid || $finalPaid) {
            $this->serviceRequestModel->updatePaymentStatus($request_id, 'partially_paid');
        }
    }
    
    /**
     * View payment history for a request
     */
    public function history($request_id = null)
    {
        if (!$request_id) {
            die('Invalid request ID');
        }
        
        $payments = $this->paymentModel->getPaymentsByRequest($request_id);
        $request = $this->serviceRequestModel->getRequestById($request_id);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'request' => $request,
            'payments' => $payments
        ]);
    }
}
