<?php

class BrowseDramas
{
    use Controller;
    protected $model = null;

    public function __construct()
    {
        $this->model = $this->getModel("M_drama");
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
            'search' => '',
            'total_dramas' => 0,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => 1,
            'sort' => 'latest',
        ];

        $search = $_GET['search'] ?? '';
        $sort = $_GET['sort'] ?? 'latest';
        $data['search'] = $search;
        $data['sort'] = $sort;

        if ($this->model) {
            $total = $this->model->countDramas($search);
            $data['total_dramas'] = (int)$total;
            $data['total_pages'] = max(1, (int)ceil($total / $perPage));
            $data['dramas'] = $this->model->getDramasPaginated($search, $perPage, $offset, $sort);
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
            'error' => ''
        ];

        if (!$data['drama']) {
            $data['error'] = 'Drama not found';
        }

        $this->view('drama_details', $data);
    }
}

?>
