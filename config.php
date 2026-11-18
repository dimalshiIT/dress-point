<?php
/**
 * Dress Point Basic - Configuration and Database Connection
 * Connects to MySQL using mysqli with prepared statements
 */

// Start session if not started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic store settings
define('SITE_NAME', 'Dress Point');
define('CURRENCY', 'Rs.');
define('BASE_URL', '/dresspoint_basic');

// Database credentials (default XAMPP setup)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'dresspoint_basic_db';

// Connect to MySQL database
mysqli_report(MYSQLI_REPORT_OFF);
$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check if database connected successfully
$db_connected = true;
$db_error_message = '';

if ($conn->connect_error) {
    $db_connected = false;
    $db_error_message = $conn->connect_error;
} else {
    // Set character encoding to utf8mb4
    $conn->set_charset("utf8mb4");
}

/**
 * Format a number into currency display (e.g. 4500 -> Rs. 4,500.00)
 */
function format_price($amount) {
    return CURRENCY . ' ' . number_format((float)$amount, 2);
}

/**
 * Clean user text before printing in HTML to prevent XSS attacks
 */
function safe($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Check if the admin user is logged in
 */
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Calculate the total quantity of items currently in the cart
 */
function get_cart_count() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += isset($item['quantity']) ? (int)$item['quantity'] : 1;
        }
    }
    return $count;
}
?>
