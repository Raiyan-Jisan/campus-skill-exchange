<?php
/**
 * Authentication Middleware / Guard
 * Ensures the student is logged in before accessing protected pages.
 */

require_once __DIR__ . '/functions.php';

if (!is_logged_in()) {
    set_flash('danger', 'Please log in to your student account to access this page.');
    redirect('auth.php');
}
?>
