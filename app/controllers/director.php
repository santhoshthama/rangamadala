<?php

class Director{
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

    public function drama_details()
    {
        // error_log("IN DRAMA DETAILS CONTROLLER");
        $this->renderDramaView('drama_details');
    }

    public function manage_roles()
    {
        $this->renderDramaView('manage_roles');
    }

    public function assign_managers()
    {
        $this->renderDramaView('assign_managers');
    }

    public function schedule_management()
    {
        $this->renderDramaView('schedule_management');
    }

    public function view_services_budget()
    {
        $this->renderDramaView('view_services_budget');
    }

    public function search_artists()
    {
        $this->renderDramaView('search_artists');
    }

<<<<<<< HEAD
    public function create_drama()
    {
        $this->renderDramaView('create_drama');
    }

    public function manage_dramas()
    {
        $this->renderDramaView('manage_dramas');
    }

    public function role_management()
    {
        $this->renderDramaView('role_management');
    }

=======
>>>>>>> 4a7cd2f6e0c168dac5fa3f8773e569dc3f8aba4b
    protected function renderDramaView($view, array $data = [])
    {
        $drama = $this->authorizeDrama();
        $payload = array_merge(['drama' => $drama], $data);
        $this->view('director/' . $view, $payload);
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
        $ownerId = $drama ? (int)($drama->creator_artist_id ?? $drama->created_by ?? 0) : 0;

        if (!$drama || $ownerId !== (int)$_SESSION['user_id']) {
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }

        return $drama;
    }
}

?>