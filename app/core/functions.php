<?php
function show($stuff)
{
    echo "<pre>";
    print_r($stuff);
    echo "</pre>";
}

function esc($string)
{
    return htmlspecialchars((string)$string);
}

/**
 * Set a success message to be displayed as a toast notification
 * @param string $message The success message to display
 */
function setSuccessMessage($message)
{
    $_SESSION['success_message'] = $message;
}

/**
 * Set an error message to be displayed as a toast notification
 * @param string $message The error message to display
 */
function setErrorMessage($message)
{
    $_SESSION['error_message'] = $message;
}

/**
 * Set a warning message to be displayed as a toast notification
 * @param string $message The warning message to display
 */
function setWarningMessage($message)
{
    $_SESSION['warning_message'] = $message;
}

/**
 * Set an info message to be displayed as a toast notification
 * @param string $message The info message to display
 */
function setInfoMessage($message)
{
    $_SESSION['info_message'] = $message;
}

/**
 * Redirect with a success message
 * @param string $url The URL to redirect to
 * @param string $message The success message to display
 */
function redirectWithSuccess($url, $message)
{
    setSuccessMessage($message);
    header("Location: " . $url);
    exit;
}

/**
 * Redirect with an error message
 * @param string $url The URL to redirect to
 * @param string $message The error message to display
 */
function redirectWithError($url, $message)
{
    setErrorMessage($message);
    header("Location: " . $url);
    exit;
}