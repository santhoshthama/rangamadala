<?php
$flash = isset($flash) && is_array($flash) ? $flash : null;
if (!$flash || empty($flash['message'])) {
    return;
}

$type = strtolower((string)($flash['type'] ?? 'info'));
$allowed = ['success', 'error', 'info', 'warning'];
if (!in_array($type, $allowed, true)) {
    $type = 'info';
}

$iconMap = [
    'success' => 'check-circle',
    'error' => 'x-circle',
    'warning' => 'error',
    'info' => 'info-circle',
];
$icon = $iconMap[$type] ?? 'info-circle';
?>

<div class="message <?= esc($type) ?>">
    <i class="bx bx-<?= esc($icon) ?>"></i>
    <?= esc((string)$flash['message']) ?>
</div>
