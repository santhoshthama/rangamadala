<?php

class Artistdashboard
{
    use Controller;

    public function index()
    {
        // error_log("AWA AWA BADU AWA");
        error_log(print_r($_SESSION, true));    
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }


        $artist_model = $this->getModel('M_artist');
        $drama_model = $this->getModel('M_drama');
        
        $user_id = $_SESSION['user_id'];
        
        // Get artist profile data
        $data['user'] = $artist_model->get_artist_by_id($user_id);
        
        // Get dramas where user is the director (creator)
        $data['dramas_as_director'] = $drama_model->get_dramas_by_director($user_id);
        
        // Get dramas where user is a production manager
        $data['dramas_as_manager'] = $drama_model->get_dramas_by_manager($user_id);
        
        // Get dramas where user is cast in roles (as actor)
        $data['dramas_as_actor'] = $drama_model->get_dramas_by_actor($user_id);
        
        // Get pending role requests for this artist
        $data['role_requests'] = $artist_model->get_pending_role_requests($user_id);
        
        // Count statistics
        $data['stats'] = [
            'total_dramas' => count($data['dramas_as_director']) + count($data['dramas_as_manager']) + count($data['dramas_as_actor']),
            'as_director' => count($data['dramas_as_director']),
            'as_manager' => count($data['dramas_as_manager']),
            'as_actor' => count($data['dramas_as_actor']),
            'pending_requests' => count($data['role_requests'])
        ];
        
        $this->view('artistdashboard', $data);
    }
    
    public function respond_to_request()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = $_POST['request_id'] ?? null;
            $response = $_POST['response'] ?? null; // 'accept' or 'reject'
            
            if ($request_id && $response) {
                $artist_model = $this->getModel('M_artist');
                $result = $artist_model->respond_to_role_request($request_id, $response);
                
                if ($result) {
                    $_SESSION['message'] = $response === 'accept' ? 'Role request accepted successfully!' : 'Role request declined.';
                    $_SESSION['message_type'] = 'success';
                } else {
                    $_SESSION['message'] = 'Failed to process request.';
                    $_SESSION['message_type'] = 'error';
                }
            }
            
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }
    }
}
