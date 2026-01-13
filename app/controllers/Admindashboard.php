<?php
class Admindashboard {
    use Controller;

    public function index(){
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Check if user has admin role
        if ($_SESSION['user_role'] !== 'admin') {
            header("Location: " . ROOT . "/Home");
            exit;
        }

        $this->view('admindashboard');
    }
}
?>