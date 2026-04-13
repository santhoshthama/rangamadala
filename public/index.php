<?php
// Configure session settings BEFORE session_start()
ini_set('session.cookie_lifetime', 0);  // Session ends when browser closes
ini_set('session.gc_maxlifetime', 86400);  // 24 hours server-side
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access
ini_set('session.cookie_samesite', 'Lax');  // Prevent CSRF
ini_set('session.use_strict_mode', 1);  // Prevent session fixation

// Ensure session directory exists
$sessionPath = "C:/xampp/tmp";
if (!is_dir($sessionPath)) {
    mkdir($sessionPath, 0777, true);
}
session_save_path($sessionPath);

session_start();

// Capture HTML output so we can enforce consistent tab branding site-wide.
ob_start();

// Include initialization file
require '../app/core/init.php';

// Enable debug mode errors
if (defined('DEBUG') && DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// Optional: force errors to show in HTML
set_error_handler(function($errno, $errstr, $errfile, $errline){
    echo "<b>Error:</b> [$errno] $errstr in $errfile on line $errline<br>";
});

// Start the app
$app = new App();
$app->loadController();

$output = ob_get_clean();

if (is_string($output) && preg_match('/<html\b/i', $output) && preg_match('/<head\b/i', $output) && preg_match('/<\/head>/i', $output)) {
    $headPattern = '/<head\b[^>]*>(.*?)<\/head>/is';
    $output = preg_replace_callback($headPattern, function ($matches) {
        $headContent = $matches[1];

        $hasTitle = preg_match('/<title\b[^>]*>.*?<\/title>/is', $headContent) === 1;
        $hasIcon = preg_match('/rel\s*=\s*["\'](?:shortcut\s+icon|icon)["\']/is', $headContent) === 1;

        if ($hasTitle) {
            $headContent = preg_replace_callback('/<title\b[^>]*>(.*?)<\/title>/is', function ($titleMatch) {
                $rawTitle = strip_tags($titleMatch[1]);
                $trimmedTitle = trim(html_entity_decode($rawTitle, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                if ($trimmedTitle === '') {
                    return '<title>Rangamadala</title>';
                }

                if (stripos($trimmedTitle, 'Rangamadala') !== false) {
                    return $titleMatch[0];
                }

                return '<title>' . htmlspecialchars($trimmedTitle . ' - Rangamadala', ENT_QUOTES | ENT_HTML5, 'UTF-8') . '</title>';
            }, $headContent, 1);
        } else {
            $headContent .= "\n    <title>Rangamadala</title>";
        }

        if (!$hasIcon) {
            $headContent .= "\n    <link rel=\"shortcut icon\" href=\"" . ROOT . "/assets/images/Rangamadala logo.png\" type=\"image/x-icon\">";
        }

        return '<head>' . $headContent . '</head>';
    }, $output, 1);
}

if (is_string($output)) {
    $iconMap = [
        'dashboard' => 'bx bx-grid-alt',
        'people' => 'bx bx-user',
        'app_registration' => 'bx bx-edit-alt',
        'approval' => 'bx bx-shield-quarter',
        'article' => 'bx bx-file',
        'home' => 'bx bx-home',
        'menu' => 'bx bx-menu',
        'search' => 'bx bx-search',
        'close' => 'bx bx-x',
        'notifications' => 'bx bx-bell',
        'person' => 'bx bx-user',
        'palette' => 'bx bx-palette',
        'logout' => 'bx bx-log-out',
        'groups' => 'bx bx-group',
        'theater_comedy' => 'bx bx-mask',
        'event_available' => 'bx bx-calendar-check',
        'pending_actions' => 'bx bx-time',
        'schedule' => 'bx bx-calendar',
        'hourglass_top' => 'bx bx-hourglass',
        'add' => 'bx bx-plus',
        'progress_activity' => 'bx bx-loader-alt',
        'task_alt' => 'bx bx-check-circle',
        'view_carousel' => 'bx bx-carousel',
        'photo_library' => 'bx bx-photo-album',
        'reviews' => 'bx bx-message-square-detail',
        'add_photo_alternate' => 'bx bx-image-add',
        'rate_review' => 'bx bx-message-edit',
        'confirmation_number' => 'bx bx-purchase-tag',
        'trending_up' => 'bx bx-trending-up',
        'event' => 'bx bx-calendar',
        'done_all' => 'bx bx-check-double',
        'filter_list' => 'bx bx-filter-alt',
        'movie' => 'bx bx-camera-movie',
        'call' => 'bx bx-phone',
        'category' => 'bx bx-category',
        'language' => 'bx bx-world',
        'timer' => 'bx bx-timer',
        'visibility' => 'bx bx-show-alt',
        'visibility_off' => 'bx bx-hide',
        'theaters' => 'bx bx-camera-movie',
        'calendar_today' => 'bx bx-calendar',
        'group' => 'bx bx-group',
        'payments' => 'bx bx-money',
        'location_on' => 'bx bx-map',
        'school' => 'bx bx-book-open',
        'bookmarks' => 'bx bx-bookmark',
        'receipt_long' => 'bx bx-receipt',
        'event_busy' => 'bx bx-calendar-x',
        'settings' => 'bx bx-cog',
        'star' => 'bx bx-star',
        'info' => 'bx bx-info-circle',
        'check_circle' => 'bx bx-check-circle',
        'title' => 'bx bx-text',
        'cloud_upload' => 'bx bx-cloud-upload',
        'badge' => 'bx bx-id-card',
        'chat' => 'bx bx-chat',
        'error' => 'bx bx-error-circle',
        'delete' => 'bx bx-trash',
    ];

    // Remove Material Symbols stylesheet links globally.
    $output = preg_replace('/\s*<link[^>]*Material\+Symbols[^>]*>\s*/i', "\n", $output);

    // Ensure Boxicons stylesheet is always available.
    if (preg_match('/<head\b[^>]*>/i', $output) && !preg_match('/boxicons\.min\.css/i', $output)) {
        $output = preg_replace(
            '/<head\b[^>]*>/i',
            "$0\n    <link href=\"https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css\" rel=\"stylesheet\">",
            $output,
            1
        );
    }

    // Convert static Material icon tags in server-rendered HTML to Boxicons.
    $output = preg_replace_callback(
        '/<(span|i)([^>]*?)class=("|\')([^"\']*?)material-symbols-rounded([^"\']*?)("|\')([^>]*)>(.*?)<\/\1>/is',
        function ($matches) use ($iconMap) {
            $before = trim((string)$matches[4]);
            $after = trim((string)$matches[5]);
            $attrs = (string)$matches[7];
            $name = trim(strip_tags((string)$matches[8]));
            $mapped = $iconMap[$name] ?? 'bx bx-circle';

            $classParts = [];
            if ($before !== '') {
                $classParts[] = $before;
            }
            // Keep material-symbols-rounded class for compatibility with existing CSS selectors.
            $classParts[] = 'material-symbols-rounded';
            if ($after !== '') {
                $classParts[] = $after;
            }
            $classParts[] = $mapped;

            $classString = preg_replace('/\s+/', ' ', trim(implode(' ', $classParts)));
            return '<i class="' . htmlspecialchars($classString, ENT_QUOTES | ENT_HTML5, 'UTF-8') . '"' . $attrs . '></i>';
        },
        $output
    );

    // Convert dynamically injected Material icon elements (e.g., JS-rendered modals/cards).
    $iconMapJson = json_encode($iconMap, JSON_UNESCAPED_SLASHES);
    $boxiconRuntimeScript = "\n<script>(function(){\n  const ICON_MAP = " . $iconMapJson . ";\n  function convert(node){\n    if(!node || !node.querySelectorAll){return;}\n    const all = [];\n    if(node.classList && node.classList.contains('material-symbols-rounded')){ all.push(node); }\n    node.querySelectorAll('.material-symbols-rounded').forEach(function(el){ all.push(el); });\n    all.forEach(function(el){\n      const name = (el.textContent || '').trim();\n      const mapped = ICON_MAP[name] || 'bx bx-circle';\n      const parts = (el.className || '').split(/\\s+/).filter(Boolean);\n      mapped.split(/\\s+/).forEach(function(c){ if(parts.indexOf(c) === -1){ parts.push(c); } });\n      if(el.tagName.toLowerCase() !== 'i'){\n        const i = document.createElement('i');\n        i.className = parts.join(' ');\n        for (let j = 0; j < el.attributes.length; j++) {\n          const attr = el.attributes[j];\n          if(attr && attr.name !== 'class'){ i.setAttribute(attr.name, attr.value); }\n        }\n        el.replaceWith(i);\n      } else {\n        el.className = parts.join(' ');\n        el.textContent = '';\n      }\n    });\n  }\n  if(document.readyState === 'loading'){\n    document.addEventListener('DOMContentLoaded', function(){ convert(document); });\n  } else {\n    convert(document);\n  }\n  const observer = new MutationObserver(function(mutations){\n    mutations.forEach(function(m){\n      m.addedNodes.forEach(function(n){ if(n && n.nodeType === 1){ convert(n); } });\n    });\n  });\n  observer.observe(document.documentElement, { childList: true, subtree: true });\n})();</script>\n";

    if (preg_match('/<\/body>/i', $output)) {
        $output = preg_replace('/<\/body>/i', $boxiconRuntimeScript . '</body>', $output, 1);
    } else {
        $output .= $boxiconRuntimeScript;
    }
}

echo $output;
