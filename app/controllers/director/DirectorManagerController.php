<?php

require_once __DIR__ . '/DirectorFeatureControllerTrait.php';

class DirectorManagerController
{
    use Controller, DirectorFeatureControllerTrait;

    protected $dramaModel;
    protected $pmModel;
    protected $artistModel;
    protected $profileModel;

    public function __construct()
    {
        $this->dramaModel = $this->getModel('M_drama');
        $this->pmModel = $this->getModel('M_production_manager');
        $this->artistModel = $this->getModel('M_artist');
        $this->profileModel = $this->getModel('M_universal_profile');
    }

    public function manager_profile()
    {
        $artistId = $this->sanitizeInt($this->getQueryParam('artist_id'));
        if (!$artistId) {
            $dramaId = $this->getQueryParam('drama_id');
            if ($dramaId) {
                header('Location: ' . ROOT . '/director/search_managers?drama_id=' . (int)$dramaId);
                exit;
            }
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $this->renderDramaView('artist_profile', [], function ($drama) use ($artistId) {
            if (!$this->artistModel) {
                $_SESSION['message'] = 'Artist profile service is currently unavailable.';
                $_SESSION['message_type'] = 'error';
                header('Location: ' . ROOT . '/director/search_managers?drama_id=' . (int)$drama->id);
                exit;
            }

            $artist = $this->artistModel->get_artist_by_id((int)$artistId);
            if (!$artist) {
                $_SESSION['message'] = 'Production Manager profile not found.';
                $_SESSION['message_type'] = 'error';
                header('Location: ' . ROOT . '/director/search_managers?drama_id=' . (int)$drama->id);
                exit;
            }

            return [
                'artist' => $artist,
                'role' => null,
                'roleId' => null,
                'profileContext' => 'manager_search',
            ];
        });
    }

    public function assign_managers()
    {
        $this->renderDramaView('assign_managers', [], function ($drama) {
            $currentManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;
            $pendingRequests = $this->pmModel ? $this->pmModel->getRequestsByDrama((int)$drama->id, 'pending') : [];

            return [
                'currentManager' => $currentManager,
                'pendingRequests' => $pendingRequests,
            ];
        });
    }

    public function search_managers()
    {
        $this->renderDramaView('search_managers', [], function ($drama) {
            $search = trim($_GET['search'] ?? '');
            $directorId = (int)($_SESSION['user_id'] ?? 0);

            $currentManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;
            if ($currentManager) {
                $_SESSION['message'] = 'Remove the current Production Manager before sending new requests.';
                $_SESSION['message_type'] = 'error';
                header('Location: ' . ROOT . '/director/assign_managers?drama_id=' . $drama->id);
                exit;
            }

            $availableManagers = $this->pmModel
                ? $this->pmModel->searchAvailableManagers((int)$drama->id, $directorId, $search)
                : [];

            return [
                'availableManagers' => $availableManagers,
                'searchTerm' => $search,
            ];
        });
    }

    public function send_manager_request()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->pmModel) {
            $_SESSION['message'] = 'Production Manager system is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/assign_managers?drama_id=' . $drama->id);
            exit;
        }

        $artistId = isset($_POST['artist_id']) ? (int)$_POST['artist_id'] : 0;
        $message = $_POST['message'] ?? null;
        $directorId = (int)$_SESSION['user_id'];

        if (!$artistId) {
            $_SESSION['message'] = 'Invalid artist selection.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/search_managers?drama_id=' . $drama->id);
            exit;
        }

        if ($artistId === $directorId) {
            $_SESSION['message'] = 'You cannot invite yourself as Production Manager.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/search_managers?drama_id=' . $drama->id);
            exit;
        }

        $currentManager = $this->pmModel ? $this->pmModel->getAssignedManager((int)$drama->id) : null;
        if ($currentManager) {
            $_SESSION['message'] = 'A Production Manager is already assigned. Remove them before sending a new request.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/assign_managers?drama_id=' . $drama->id);
            exit;
        }

        $result = $this->pmModel->createRequest((int)$drama->id, $artistId, $directorId, $message);

        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

        header('Location: ' . ROOT . '/director/assign_managers?drama_id=' . $drama->id);
        exit;
    }

    public function remove_manager()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ROOT . '/director/dashboard');
            exit;
        }

        $drama = $this->authorizeDrama();

        if (!$this->pmModel) {
            $_SESSION['message'] = 'Production Manager system is currently unavailable.';
            $_SESSION['message_type'] = 'error';
            header('Location: ' . ROOT . '/director/assign_managers?drama_id=' . $drama->id);
            exit;
        }

        $directorId = (int)$_SESSION['user_id'];
        $result = $this->pmModel->removeManager((int)$drama->id, $directorId);

        $_SESSION['message'] = $result['message'];
        $_SESSION['message_type'] = $result['success'] ? 'success' : 'error';

        header('Location: ' . ROOT . '/director/assign_managers?drama_id=' . $drama->id);
        exit;
    }

}
