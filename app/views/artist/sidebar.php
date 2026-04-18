<?php
$artistSidebarActive = isset($artistSidebarActive) && is_string($artistSidebarActive)
    ? trim($artistSidebarActive)
    : '';

if ($artistSidebarActive === '' && isset($sidebarActive) && is_array($sidebarActive)) {
    foreach ($sidebarActive as $key => $isActive) {
        if ($isActive) {
            $artistSidebarActive = (string)$key;
            break;
        }
    }
}

if ($artistSidebarActive === '') {
    $artistSidebarActive = 'dashboard';
}

$artistSidebarLinks = [
    'dashboard' => [
        'href' => ROOT . '/artistdashboard',
        'icon' => 'bx bxs-home',
        'label' => 'Dashboard',
    ],
    'vacancies' => [
        'href' => ROOT . '/artistdashboard/browse_vacancies',
        'icon' => 'bx bxs-megaphone',
        'label' => 'View All Vacancies',
    ],
    'notifications' => [
        'href' => ROOT . '/artistdashboard/notifications',
        'icon' => 'bx bxs-bell',
        'label' => 'Notifications',
    ],
    'classes' => [
        'href' => ROOT . '/artistdashboard/classes',
        'icon' => 'bx bxs-graduation',
        'label' => 'Classes',
    ],
    'showings' => [
        'href' => ROOT . '/artistdashboard?tab=my-showings#my-showings',
        'icon' => 'bx bx-calendar-event',
        'label' => 'Showings',
    ],
    'calendar' => [
        'href' => ROOT . '/artistdashboard/calendar',
        'icon' => 'bx bx-calendar-week',
        'label' => 'Artist Calendar',
    ],
];
?>
<aside class="sidebar">
    <div class="logo">
        <a href="<?= ROOT ?>/artistdashboard" class="logo-link">
            <img src="<?= ROOT ?>/assets/images/Rangamadala logo.png" alt="Rangamadala Logo" class="logo-image">
        </a>
    </div>
    <ul class="menu">
        <?php foreach ($artistSidebarLinks as $key => $item): ?>
            <li class="<?= $artistSidebarActive === $key ? 'active' : '' ?>">
                <a href="<?= $item['href'] ?>">
                    <i class="<?= $item['icon'] ?>"></i>
                    <span><?= $item['label'] ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</aside>
