<?php

class Production_manager{
    use Controller;

    protected $dramaModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
    }

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->renderDramaView('dashboard');
    }

    public function manage_services()
    {
        $this->renderDramaView('manage_services');
    }

    public function manage_budget()
    {
        $this->renderDramaView('manage_budget');
    }

    public function book_theater()
    {
        $this->renderDramaView('book_theater');
    }

    public function manage_schedule()
    {
        $this->renderDramaView('manage_schedule');
    }

    protected function renderDramaView($view, array $data = [])
    {
        $drama = $this->authorizeDrama();
        $payload = array_merge(['drama' => $drama], $data);
        $this->view('production_manager/' . $view, $payload);
    }

    protected function authorizeDrama()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if (!$this->dramaModel) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $dramaId = $this->getQueryParam('drama_id');
        if (!$dramaId) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        $drama = $this->dramaModel->getDramaById((int)$dramaId);
        
        // Check if user is the production manager for this drama
        // You may need to adjust this logic based on your database schema
        if (!$drama) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        return $drama;
    }
}

?>
