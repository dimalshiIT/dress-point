<?php
/**
 * Dress Point - Admin Authentication Guard
 * Included in all admin pages to protect against unauthorized access
 */
require_once __DIR__ . '/../config.php';

// Check if admin is authenticated
if (!is_admin_logged_in()) {
    header('Location: ' . BASE_URL . '/admin/login.php');
    exit;
}
?>
