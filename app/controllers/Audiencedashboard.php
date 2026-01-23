<?php
class Audiencedashboard {
    use Controller;
    protected $dramaModel = null;

    public function __construct()
    {
        $this->dramaModel = $this->getModel("M_drama");
    }

    public function index(){
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has audience role
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'audience') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        // Fetch dramas and categories for the Browse Dramas tab
        $data = [
            'dramas' => $this->dramaModel->getAllDramas(),
            'categories' => $this->dramaModel->getAllCategories(),
            'total_dramas' => 0
        ];

        $data['total_dramas'] = count($data['dramas']);

        $this->view('audiencedashboard', $data);
    }
}
?>