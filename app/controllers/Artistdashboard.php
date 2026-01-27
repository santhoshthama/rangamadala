<?php

class Artistdashboard
{
    use Controller;

    public function index()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }


        $artist_model = $this->getModel('M_artist');
        $drama_model = $this->getModel('M_drama');
        $pm_model = $this->getModel('M_production_manager');
        $role_model = $this->getModel('M_role');
        
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
        
        // Get pending PM requests for this artist
        $data['pm_requests'] = $pm_model ? $pm_model->getPendingRequestsForArtist($user_id) : [];
        
        // Get total published vacancies count
        $data['total_published_vacancies'] = $role_model ? $role_model->countPublishedVacancies() : 0;
        
        // Count statistics
        $data['stats'] = [
            'total_dramas' => count($data['dramas_as_director']) + count($data['dramas_as_manager']) + count($data['dramas_as_actor']),
            'as_director' => count($data['dramas_as_director']),
            'as_manager' => count($data['dramas_as_manager']),
            'as_actor' => count($data['dramas_as_actor']),
            'pending_requests' => count($data['role_requests']),
            'pending_pm_requests' => count($data['pm_requests'])
        ];
        
        $this->view('artistdashboard', $data);
    }

    public function browse_vacancies()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $role_model = $this->getModel('M_role');
        $user_id = $_SESSION['user_id'];

        // Get filter parameters
        $filters = [
            'role_type' => $_GET['role_type'] ?? '',
            'search' => $_GET['search'] ?? '',
            'sort' => $_GET['sort'] ?? 'latest',
            'artist_id' => $user_id  // Exclude roles where artist is already assigned
        ];

        // Get all published vacancies with filters
        $data['vacancies'] = $role_model ? $role_model->getAllPublishedVacancies($filters) : [];
        
        // Get artist's existing applications to check which roles they've applied to
        $data['my_applications'] = $role_model ? $role_model->getArtistApplications($user_id) : [];
        
        // Create a map of role IDs the artist has applied to
        $data['applied_role_ids'] = [];
        foreach ($data['my_applications'] as $app) {
            $data['applied_role_ids'][] = $app->role_id;
        }
        
        $data['filters'] = $filters;
        $data['total_vacancies'] = count($data['vacancies']);
        
        $this->view('artist/browse_vacancies', $data);
    }

    public function apply_for_role()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $role_id = isset($input['role_id']) ? (int)$input['role_id'] : 0;
        $cover_letter = $input['cover_letter'] ?? '';

        if (!$role_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Role ID is required']);
            exit;
        }

        $role_model = $this->getModel('M_role');
        $result = $role_model->applyForRole($role_id, $_SESSION['user_id'], $cover_letter);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function my_applications()
    {
        // Check if user is logged in and is an artist
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'artist') {
            header("Location: " . ROOT . "/login");
            exit;
        }

        $role_model = $this->getModel('M_role');
        $user_id = $_SESSION['user_id'];

        // Get all applications by this artist
        $data['applications'] = $role_model ? $role_model->getArtistApplications($user_id) : [];
        
        // Group by status
        $data['pending'] = array_filter($data['applications'], function($app) {
            return $app->status === 'pending';
        });
        
        $data['accepted'] = array_filter($data['applications'], function($app) {
            return $app->status === 'accepted';
        });
        
        $data['rejected'] = array_filter($data['applications'], function($app) {
            return $app->status === 'rejected';
        });
        
        $this->view('artist/my_applications', $data);
    }
    

    public function respond_to_request()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = $_POST['request_id'] ?? null;
            $response = $_POST['response'] ?? null; // 'accept' or 'reject'
            
            if ($request_id && $response) {
                $artist_model = $this->getModel('M_artist');
                $user_id = $_SESSION['user_id'];
                $result = $artist_model->respond_to_role_request($request_id, $user_id, $response);
                
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

    public function respond_to_manager_request()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request_id = isset($_POST['request_id']) ? (int)$_POST['request_id'] : 0;
            $response = $_POST['response'] ?? null; // 'accept' or 'reject'
            
            if ($request_id && $response) {
                $pm_model = $this->getModel('M_production_manager');
                $user_id = $_SESSION['user_id'];
                
                if ($response === 'accept') {
                    $result = $pm_model->acceptRequest($request_id, $user_id);
                } else {
                    $response_note = $_POST['response_note'] ?? null;
                    $result = $pm_model->rejectRequest($request_id, $user_id, $response_note);
                }
                
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';
            } else {
                $_SESSION['message'] = 'Invalid request.';
                $_SESSION['message_type'] = 'error';
            }
            
            header("Location: " . ROOT . "/artistdashboard");
            exit;
        }
    }
}
