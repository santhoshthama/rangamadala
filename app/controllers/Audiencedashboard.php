<?php
class Audiencedashboard {
    use Controller;
    protected $dramaModel = null;
    protected $bookingModel = null;
    protected $classModel = null;
    protected $audienceModel = null;
    protected $payHereHelper = null;

    public function __construct()
    {
        $this->dramaModel = $this->getModel("M_drama");
        $this->bookingModel = $this->getModel("M_audience_show_booking");
        $this->classModel = $this->getModel("M_class");
        $this->audienceModel = $this->getModel("M_audience");
        try {
            $this->payHereHelper = new PayHereHelper();
        } catch (Throwable $e) {
            $this->payHereHelper = null;
            error_log('Audiencedashboard PayHereHelper init failed: ' . $e->getMessage());
        }
    }

    public function index(){
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has audience role
        $sessionRole = $_SESSION['role'] ?? ($_SESSION['user_role'] ?? '');
        if ($sessionRole !== 'audience') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        // Fetch dramas and categories for the Browse Dramas tab
        $data = [
            'dramas' => $this->dramaModel->getAllDramas(),
            'categories' => $this->dramaModel->getAllCategories(),
            'total_dramas' => 0,
            'my_showings' => [],
            'classes' => [],
            'my_classes' => [],
            'showing_payments' => [],
            'class_payments' => [],
            'dashboard_profile_image' => ROOT . '/uploads/profile_images/user_profile.png'
        ];

        $data['total_dramas'] = count($data['dramas']);
        if ($this->bookingModel) {
            $data['my_showings'] = $this->bookingModel->getBookingsByAudience((int)$_SESSION['user_id']);
            $data['showing_payments'] = $this->bookingModel->getShowingPaymentsByAudience((int)$_SESSION['user_id']);
        }

        if ($this->classModel) {
            $data['classes'] = $this->classModel->getPublishedClasses();
            $data['my_classes'] = $this->classModel->getEnrolledClassesByUser((int)$_SESSION['user_id']);
            $data['class_payments'] = $this->classModel->getEnrollmentPaymentsByUser((int)$_SESSION['user_id'], 'audience');
        }

        if ($this->audienceModel) {
            $profileImage = $this->audienceModel->getProfileImage((int)$_SESSION['user_id']);
            if (!empty($profileImage)) {
                $data['dashboard_profile_image'] = ROOT . '/uploads/profile_images/' . rawurlencode($profileImage);
            }
        }

        $this->view('audiencedashboard', $data);
    }

    public function enroll_class()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        if (($_SESSION['role'] ?? '') !== 'audience') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/audiencedashboard#classes');
            exit;
        }

        $_SESSION['error_message'] = 'Complete class payment first to enroll.';
        header('Location: ' . ROOT . '/audiencedashboard#classes');
        exit;
    }

    public function start_class_payment()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'audience')) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Invalid request method']);
            exit;
        }

        $classId = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        if ($classId <= 0 || !$this->classModel || !$this->payHereHelper) {
            echo json_encode(['success' => false, 'error' => 'Invalid class payment request']);
            exit;
        }

        $paymentOrder = $this->classModel->createEnrollmentPaymentOrder($classId, (int)$_SESSION['user_id'], 'audience', true);
        if (!$paymentOrder['success']) {
            echo json_encode(['success' => false, 'error' => $paymentOrder['message'] ?? 'Unable to initialize class payment']);
            exit;
        }

        $orderId = (string)$paymentOrder['order_id'];
        $amount = number_format((float)($paymentOrder['amount'] ?? 0), 2, '.', '');
        $hash = $this->payHereHelper->generateHash($orderId, $amount);
        $class = $paymentOrder['class'] ?? null;

        echo json_encode([
            'success' => true,
            'order_id' => $orderId,
            'amount' => $amount,
            'hash' => $hash,
            'title' => $class->title ?? 'Drama Class',
            'merchant_id' => $this->payHereHelper->getConfig('merchant_id'),
            'sandbox' => (bool)$this->payHereHelper->getConfig('sandbox', false),
            'return_url' => ROOT . '/audiencedashboard/class_payment_return?order_id=' . rawurlencode($orderId),
            'cancel_url' => ROOT . '/audiencedashboard#classes',
            'notify_url' => ROOT . '/audiencedashboard/class_payment_notify',
        ]);
        exit;
    }

    public function class_payment_return()
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'audience')) {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        $orderId = trim((string)($_GET['order_id'] ?? ''));
        if ($orderId === '' || !$this->classModel) {
            $_SESSION['error_message'] = 'Invalid class payment return details.';
            header('Location: ' . ROOT . '/audiencedashboard#classes');
            exit;
        }

        $result = $this->classModel->completeEnrollmentPayment($orderId, (int)$_SESSION['user_id'], 'audience', true);
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }

        header('Location: ' . ROOT . '/audiencedashboard#classes');
        exit;
    }

    public function class_payment_notify()
    {
        http_response_code(200);
        echo 'OK';
        exit;
    }

    public function payment_receipt($type = null, $paymentId = null)
    {
        if (!isset($_SESSION['user_id']) || (($_SESSION['role'] ?? '') !== 'audience')) {
            header('Location: ' . ROOT . '/Login');
            exit;
        }

        $type = strtolower(trim((string)$type));
        $paymentId = (int)$paymentId;

        if ($paymentId <= 0 || !in_array($type, ['showing', 'class'], true)) {
            $_SESSION['error_message'] = 'Invalid receipt request.';
            header('Location: ' . ROOT . '/audiencedashboard#payments');
            exit;
        }

        $receipt = null;
        $receiptNumber = '';

        if ($type === 'showing') {
            if (!$this->bookingModel) {
                $_SESSION['error_message'] = 'Receipt data is unavailable.';
                header('Location: ' . ROOT . '/audiencedashboard#payments');
                exit;
            }

            $receipt = $this->bookingModel->getBookingByIdForAudience($paymentId, (int)$_SESSION['user_id']);
            if (!$receipt || empty($receipt->paid_at) || empty($receipt->payhere_order_id)) {
                $_SESSION['error_message'] = 'Receipt not found.';
                header('Location: ' . ROOT . '/audiencedashboard#payments');
                exit;
            }

            $receiptNumber = 'RCP-SHOW-' . str_pad((string)$receipt->id, 6, '0', STR_PAD_LEFT);
        } else {
            if (!$this->classModel) {
                $_SESSION['error_message'] = 'Receipt data is unavailable.';
                header('Location: ' . ROOT . '/audiencedashboard#payments');
                exit;
            }

            $receipt = $this->classModel->getEnrollmentPaymentByIdForUser($paymentId, (int)$_SESSION['user_id'], 'audience');
            if (!$receipt || strtolower((string)($receipt->status ?? '')) !== 'completed') {
                $_SESSION['error_message'] = 'Receipt not found.';
                header('Location: ' . ROOT . '/audiencedashboard#payments');
                exit;
            }

            $receiptNumber = 'RCP-CLASS-' . str_pad((string)$receipt->id, 6, '0', STR_PAD_LEFT);
        }

        $data = [
            'receipt_type' => $type,
            'receipt_number' => $receiptNumber,
            'receipt' => $receipt,
        ];

        $this->view('audience_payment_receipt', $data);
    }
}
?>