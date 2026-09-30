<?php
/**
 * Reusable Helper Functions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitizes input to avoid XSS and trim unnecessary spaces
 * Reference: Lab 06 Manual, Section 8
 */
function clean_input($data) {
    if ($data === null) {
        return '';
    }
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Checks if a student is currently authenticated
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Returns the currently logged in student ID (PK)
 */
function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Stores a temporary flash message in the session
 */
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'info'
        'message' => $message
    ];
}

/**
 * Renders and clears any flash message
 */
function display_flash() {
    if (isset($_SESSION['flash'])) {
        $type = htmlspecialchars($_SESSION['flash']['type'], ENT_QUOTES, 'UTF-8');
        $msg  = $_SESSION['flash']['message']; // message can include safe strong tags or text
        unset($_SESSION['flash']);

        echo "<div class='alert alert-{$type}' role='alert'>
                <span>{$msg}</span>
                <button type='button' class='alert-close' onclick='this.parentElement.remove();'>&times;</button>
              </div>";
    }
}

/**
 * Safe redirect helper
 */
function redirect($url) {
    header("Location: {$url}");
    exit();
}

/**
 * Formats a database timestamp into a clean academic date string
 */
function format_date($timestamp) {
    if (!$timestamp) return 'N/A';
    return date('M d, Y', strtotime($timestamp));
}
?>
