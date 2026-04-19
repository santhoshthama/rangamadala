<?php

trait DirectorFeatureControllerTrait
{
    protected function renderDramaView($view, array $data = [], ?callable $dataBuilder = null)
    {
        $drama = $this->authorizeDrama();

        $categories = [];
        if ($this->dramaModel && method_exists($this->dramaModel, 'getAllCategories')) {
            $categories = $this->dramaModel->getAllCategories() ?? [];
        }

        if ($dataBuilder) {
            $additional = $dataBuilder($drama);
            if (is_array($additional)) {
                $data = array_merge($data, $additional);
            }
        }

        $payload = array_merge([
            'drama' => $drama,
            'categories' => $categories,
            'profileImageSrc' => $this->resolveCurrentDirectorProfileImageSrc(),
            'flash' => $this->consumeFlash(),
            'currentDirectorId' => (int)($_SESSION['user_id'] ?? 0),
            'dramaId' => (int)($drama->id ?? 0),
            'dramaName' => (string)($drama->drama_name ?? 'Drama'),
        ], $data);

        $this->view('director/' . $view, $payload);
    }

    protected function resolveCurrentDirectorProfileImageSrc(): string
    {
        $fallback = ROOT . '/assets/images/default-avatar.jpg';
        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        if ($userId <= 0 || !$this->profileModel || !method_exists($this->profileModel, 'getUserById')) {
            return $fallback;
        }

        $currentUser = $this->profileModel->getUserById($userId);
        if (!$currentUser) {
            return $fallback;
        }

        if (!empty($currentUser->profile_image)) {
            $imageValue = str_replace('\\', '/', (string)$currentUser->profile_image);
            if (strpos($imageValue, '/') !== false) {
                return ROOT . '/' . ltrim($imageValue, '/');
            }
            return ROOT . '/uploads/profile_images/' . rawurlencode($imageValue);
        }

        if (!empty($currentUser->nic_photo)) {
            return ROOT . '/' . ltrim(str_replace('\\', '/', (string)$currentUser->nic_photo), '/');
        }

        return $fallback;
    }

    protected function consumeFlash(): ?array
    {
        if (!isset($_SESSION['message'])) {
            return null;
        }

        $flash = [
            'message' => (string)$_SESSION['message'],
            'type' => (string)($_SESSION['message_type'] ?? 'info'),
        ];

        unset($_SESSION['message'], $_SESSION['message_type']);
        return $flash;
    }

    protected function authorizeDrama()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        if (!$this->dramaModel) {
            header('Location: ' . ROOT . '/artistdashboard');
            exit;
        }

        $dramaId = $this->getQueryParam('drama_id');
        if (!$dramaId) {
            header('Location: ' . ROOT . '/artistdashboard');
            exit;
        }

        $drama = $this->dramaModel->getDramaById((int)$dramaId);
        $ownerId = $drama ? (int)($drama->creator_artist_id ?? $drama->created_by ?? 0) : 0;

        if (!$drama || $ownerId !== (int)$_SESSION['user_id']) {
            header('Location: ' . ROOT . '/artistdashboard');
            exit;
        }

        return $drama;
    }

    protected function sanitizeInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $int = filter_var($value, FILTER_VALIDATE_INT);
        return $int === false ? null : (int)$int;
    }
}
