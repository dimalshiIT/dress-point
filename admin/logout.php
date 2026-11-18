<?php
/**
 * Dress Point - Admin Logout
 */
require_once __DIR__ . '/../config.php';

// Unset admin session variables (remove login session details)
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_username']);

// Redirect to login page (after log out)
header('Location: ' . BASE_URL . '/admin/login.php');
exit;
?>
