<?php

class Director{
    use Controller;

    protected $directorRoleController;
    protected $directorScheduleController;
    protected $directorManagerController;
    protected $directorDramaController;

    public function index()
    {
        $this->dashboard();
    }

    public function dashboard()
    {
        $this->getDirectorDramaController()->dashboard();
    }

    public function drama_details()
    {
        $this->getDirectorDramaController()->drama_details();
    }

    public function manage_roles()
    {
        $this->getDirectorRoleController()->manage_roles();
    }

    public function view_role()
    {
        $this->getDirectorRoleController()->view_role();
    }

    public function create_role()
    {
        $this->getDirectorRoleController()->create_role();
    }

    public function update_role()
    {
        $this->getDirectorRoleController()->update_role();
    }

    public function delete_role()
    {
        $this->getDirectorRoleController()->delete_role();
    }

    public function assign_managers()
    {
        $this->getDirectorManagerController()->assign_managers();
    }

    public function search_managers()
    {
        $this->getDirectorManagerController()->search_managers();
    }

    public function send_manager_request()
    {
        $this->getDirectorManagerController()->send_manager_request();
    }

    public function remove_manager()
    {
        $this->getDirectorManagerController()->remove_manager();
    }

    public function schedule_management()
    {
        $this->getDirectorScheduleController()->schedule_management();
    }

    /**
     * Create a new schedule event (POST)
     */
    public function create_schedule()
    {
        $this->getDirectorScheduleController()->create_schedule();
    }

    /**
     * Update an existing schedule event (POST)
     */
    public function update_schedule()
    {
        $this->getDirectorScheduleController()->update_schedule();
    }

    /**
     * Delete a schedule event (POST)
     */
    public function delete_schedule()
    {
        $this->getDirectorScheduleController()->delete_schedule();
    }

    /**
     * Update schedule event status (POST) — confirm, complete, cancel
     */
    public function update_schedule_status()
    {
        $this->getDirectorScheduleController()->update_schedule_status();
    }

    /**
     * AJAX endpoint: check date availability
     */
    public function check_date_availability()
    {
        $this->getDirectorScheduleController()->check_date_availability();
    }

    public function view_services_budget()
    {
        $this->getDirectorDramaController()->view_services_budget();
    }

    public function search_artists()
    {
        $this->getDirectorRoleController()->search_artists();
    }

    public function artist_profile()
    {
        $this->getDirectorRoleController()->artist_profile();
    }

    public function manager_profile()
    {
        $this->getDirectorManagerController()->manager_profile();
    }

    public function send_role_request()
    {
        $this->getDirectorRoleController()->send_role_request();
    }

    public function remove_role_request()
    {
        $this->getDirectorRoleController()->remove_role_request();
    }

    public function publish_vacancy()
    {
        $this->getDirectorRoleController()->publish_vacancy();
    }

    public function unpublish_vacancy()
    {
        $this->getDirectorRoleController()->unpublish_vacancy();
    }

    public function accept_application()
    {
        $this->getDirectorRoleController()->accept_application();
    }

    public function reject_application()
    {
        $this->getDirectorRoleController()->reject_application();
    }

    public function application_profile()
    {
        $this->getDirectorRoleController()->application_profile();
    }

    public function schedule_application_interview()
    {
        $this->getDirectorRoleController()->schedule_application_interview();
    }

    public function create_drama()
    {
        $this->getDirectorDramaController()->create_drama();
    }

    public function manage_dramas()
    {
        $this->getDirectorDramaController()->manage_dramas();
    }

    public function role_management()
    {
        $this->getDirectorDramaController()->role_management();
    }

    public function update_drama()
    {
        $this->getDirectorDramaController()->update_drama();
    }

    public function publish_drama()
    {
        $this->getDirectorDramaController()->publish_drama();
    }

    protected function getDirectorRoleController(): DirectorRoleController
    {
        if (!$this->directorRoleController) {
            require_once __DIR__ . '/director/DirectorRoleController.php';
            $this->directorRoleController = new DirectorRoleController();
        }

        return $this->directorRoleController;
    }

    protected function getDirectorScheduleController(): DirectorScheduleController
    {
        if (!$this->directorScheduleController) {
            require_once __DIR__ . '/director/DirectorScheduleController.php';
            $this->directorScheduleController = new DirectorScheduleController();
        }

        return $this->directorScheduleController;
    }

    protected function getDirectorManagerController(): DirectorManagerController
    {
        if (!$this->directorManagerController) {
            require_once __DIR__ . '/director/DirectorManagerController.php';
            $this->directorManagerController = new DirectorManagerController();
        }

        return $this->directorManagerController;
    }

    protected function getDirectorDramaController(): DirectorDramaController
    {
        if (!$this->directorDramaController) {
            require_once __DIR__ . '/director/DirectorDramaController.php';
            $this->directorDramaController = new DirectorDramaController();
        }

        return $this->directorDramaController;
    }

    /**
     * Remove an artist assignment from a role
     */
    public function remove_assignment()
    {
        $this->getDirectorRoleController()->remove_assignment();
    }
}

?>