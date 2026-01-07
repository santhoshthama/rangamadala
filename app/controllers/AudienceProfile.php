<?php
 class AudienceProfile
{   
    use Controller;
    protected $model = null;
    public function __construct()
    {
        $this->model = $this->getModel("M_audience");
    }

    public function index()
    {
        $data = [];
        $data['profile'] = $this->model->getProfile($_SESSION['user_id']);
        $data['bio'] = $this->model->getBio($_SESSION['user_id']);
        $data['signup_details'] = $this->model->getSignupDetails($_SESSION['user_id']);
        $data['profile_image'] = $this->model->getProfileImage($_SESSION['user_id']);
        // Profile viewing► logic can be implemented here
        $this->view('audience_profile',$data);
    }
}
?>