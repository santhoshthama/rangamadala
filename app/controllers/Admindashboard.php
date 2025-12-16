<?php
class Admindashboard {
    use Controller;

    public function index(){
        $this->view('admindashboard');
    }
}
?>