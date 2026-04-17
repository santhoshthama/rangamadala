<?php

if (!function_exists('directorResolveProfileImageSrc')) {
    function directorResolveProfileImageSrc(int $userId): string
    {
        $fallback = ROOT . '/assets/images/default-avatar.jpg';
        if ($userId <= 0) {
            return $fallback;
        }

        $userModel = new M_universal_profile();
        $currentUser = $userModel->getUserById($userId);
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
}
