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

echo $output;
