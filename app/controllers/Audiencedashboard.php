<?php
class Audiencedashboard {
    use Controller;
    protected $dramaModel = null;
    protected $bookingModel = null;
    protected $classModel = null;

    public function __construct()
    {
        $this->dramaModel = $this->getModel("M_drama");
        $this->bookingModel = $this->getModel("M_audience_show_booking");
        $this->classModel = $this->getModel("M_class");
    }

    public function index(){
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has audience role
        if ($_SESSION['role'] !== 'audience') {
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
            'my_classes' => []
        ];

        $data['total_dramas'] = count($data['dramas']);
        if ($this->bookingModel) {
            $data['my_showings'] = $this->bookingModel->getBookingsByAudience((int)$_SESSION['user_id']);
        }

        if ($this->classModel) {
            $data['classes'] = $this->classModel->getPublishedClasses();
            $data['my_classes'] = $this->classModel->getEnrolledClassesByUser((int)$_SESSION['user_id']);
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

        $classId = isset($_POST['class_id']) ? (int)$_POST['class_id'] : 0;
        if ($classId <= 0 || !$this->classModel) {
            $_SESSION['error_message'] = 'Invalid class selected.';
            header('Location: ' . ROOT . '/audiencedashboard#classes');
            exit;
        }

        $result = $this->classModel->enrollUser($classId, (int)$_SESSION['user_id'], true);
        if ($result['success']) {
            $_SESSION['success_message'] = $result['message'];
        } else {
            $_SESSION['error_message'] = $result['message'];
        }

        header('Location: ' . ROOT . '/audiencedashboard#classes');
        exit;
    }
}
?>