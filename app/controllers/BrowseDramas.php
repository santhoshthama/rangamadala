<?php

class BrowseDramas
{
    use Controller;
    protected $model = null;
    protected $ratingModel = null;

    public function __construct()
    {
        $this->model = $this->getModel("M_drama");
        $this->ratingModel = $this->getModel("M_rating");
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        // Pagination params
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? min(50, max(5, (int)$_GET['per_page'])) : 12;
        $offset = ($page - 1) * $perPage;

        $data = [
            'dramas' => [],
            'categories' => [],
            'search' => '',
            'category_filter' => '',
            'total_dramas' => 0,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => 1,
        ];

        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $sort = $_GET['sort'] ?? 'latest';
        $data['search'] = $search;
        $data['category_filter'] = $category;
        $data['sort'] = $sort;

        // Categories for dropdown
        $data['categories'] = $this->model ? $this->model->getAllCategories() : [];

        if ($this->model) {
            $total = $this->model->countDramas($search, $category);
            $data['total_dramas'] = (int)$total;
            $data['total_pages'] = max(1, (int)ceil($total / $perPage));
            $data['dramas'] = $this->model->getDramasPaginated($search, $category, $perPage, $offset, $sort);
        }

        $this->view('browse_dramas', $data);
    }

    public function view($drama_id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/Login");
            exit;
        }

        if (!$drama_id || !$this->model) {
            header("Location: " . ROOT . "/BrowseDramas");
            exit;
        }

        $data = [
            'drama' => $this->model->getDramaById($drama_id),
            'error' => '',
            'rating_summary' => null,
            'user_rating' => null,
            'ratings' => [],
            'has_rated' => false
        ];

        if (!$data['drama']) {
            $data['error'] = 'Drama not found';
        } else if ($this->ratingModel) {
            // Get rating summary
            $data['rating_summary'] = $this->ratingModel->getDramaRatingSummary($drama_id);
            
            // Get user's existing rating (if any)
            $data['user_rating'] = $this->ratingModel->getUserDramaRating($drama_id, $_SESSION['user_id']);
            $data['has_rated'] = $data['user_rating'] !== null;
            
            // Get all ratings for display
            $data['ratings'] = $this->ratingModel->getDramaRatings($drama_id, 5, 0);
        }

        $this->view('drama_details', $data);
    }

    /**
     * Submit or update a drama rating via AJAX
     * POST request with: drama_id, rating (1-5), comment (optional)
     */
    public function submitRating()
    {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        // Check if request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        // Get JSON data
        $input = json_decode(file_get_contents('php://input'), true);

        // Validate input
        if (empty($input['drama_id']) || empty($input['rating'])) {
            echo json_encode(['success' => false, 'message' => 'Drama ID and rating are required']);
            exit;
        }

        $drama_id = (int)$input['drama_id'];
        $rating = (int)$input['rating'];
        $comment = $input['comment'] ?? null;
        $user_id = $_SESSION['user_id'];

        // Validate rating range
        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
            exit;
        }

        // Verify drama exists
        if (!$this->model || !$this->model->getDramaById($drama_id)) {
            echo json_encode(['success' => false, 'message' => 'Drama not found']);
            exit;
        }

        // Submit/update rating
        if (!$this->ratingModel) {
            echo json_encode(['success' => false, 'message' => 'Rating service unavailable']);
            exit;
        }

        $success = $this->ratingModel->submitRating($drama_id, $user_id, $rating, $comment);

        if ($success) {
            // Get updated summary
            $summary = $this->ratingModel->getDramaRatingSummary($drama_id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Rating submitted successfully',
                'summary' => $summary
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit rating']);
        }
        exit;
    }

    /**
     * Get ratings for a drama with pagination via AJAX
     */
    public function getRatings($drama_id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (!$drama_id) {
            echo json_encode(['success' => false, 'message' => 'Drama ID is required']);
            exit;
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

        if (!$this->ratingModel) {
            echo json_encode(['success' => false, 'message' => 'Rating service unavailable']);
            exit;
        }

        $ratings = $this->ratingModel->getDramaRatings($drama_id, $limit, $offset);
        $total = $this->ratingModel->countDramaRatings($drama_id);

        echo json_encode([
            'success' => true,
            'ratings' => $ratings,
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset
        ]);
        exit;
    }

    /**
     * Mark a rating as helpful via AJAX
     */
    public function markHelpful($rating_id = null)
    {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            exit;
        }

        if (!$rating_id) {
            echo json_encode(['success' => false, 'message' => 'Rating ID is required']);
            exit;
        }

        if (!$this->ratingModel) {
            echo json_encode(['success' => false, 'message' => 'Rating service unavailable']);
            exit;
        }

        $success = $this->ratingModel->markAsHelpful($rating_id);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Rating marked as helpful'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to mark as helpful']);
        }
        exit;
    }


?>
