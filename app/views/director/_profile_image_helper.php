<?php

if (isset($profileImageSrc) && is_string($profileImageSrc) && $profileImageSrc !== '') {
    $GLOBALS['directorProfileImageSrc'] = $profileImageSrc;
}

if (!function_exists('directorResolveProfileImageSrc')) {
    function directorResolveProfileImageSrc(): string
    {
        $fallback = ROOT . '/assets/images/default-avatar.jpg';

        if (isset($GLOBALS['directorProfileImageSrc']) && is_string($GLOBALS['directorProfileImageSrc']) && $GLOBALS['directorProfileImageSrc'] !== '') {
            return $GLOBALS['directorProfileImageSrc'];
        }

        return $fallback;
    }
}
