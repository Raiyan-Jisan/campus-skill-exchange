<?php
/**
 * Logout Handler
 * Course: CSE 472 Web and Internet Programming Lab
 * Reference: Secure session destruction
 */

require_once __DIR__ . '/includes/functions.php';

// Clear session variables
$_SESSION = [];

// Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session completely
session_destroy();

// Start clean session for flash notification
session_start();
set_flash('info', 'You have been successfully logged out. See you soon!');
redirect('index.php');
?>
