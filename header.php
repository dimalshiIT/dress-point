<?php
/**
 * Dress Point Basic - Header Component
 * Includes top bar, store logo, navigation links, and cart badge
 */
require_once __DIR__ . '/config.php';

// Fetch categories from the database for the navigation menu
$nav_categories = [];
if ($db_connected) {
    $cat_query = $conn->query("SELECT id, name, slug FROM categories ORDER BY id ASC");
    if ($cat_query) {
        while ($row = $cat_query->fetch_assoc()) {
            $nav_categories[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? safe($page_title) . ' | ' . SITE_NAME : SITE_NAME . ' | Online Clothing Store'; ?></title>
  
  <!-- Custom Plain CSS (Zero Frameworks) -->
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/shop.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/cart.css">
</head>
<body>

  <!-- Top Announcement Bar -->
  <div class="top-bar">
    <div class="container top-bar-inner">
      <div class="top-bar-left">
        <span><b>Dress Point</b> — Premium Sri Lankan Fashion | Island-wide Delivery &amp; Cash on Delivery Available</span>
      </div>
      <div class="top-bar-right">
        <a href="<?php echo BASE_URL; ?>/contact.php">Need Help? Contact Us</a>
        <span>|</span>
        <a href="<?php echo BASE_URL; ?>/admin/login.php">Admin Portal</a>
      </div>
    </div>
  </div>

  <!-- Main Store Header -->
  <header class="site-header">
    <div class="container header-inner">
      
      <!-- Brand Logo -->
      <a href="<?php echo BASE_URL; ?>/index.php" class="logo">
        <span class="logo-accent">&#9830;</span> DRESS<span class="logo-accent">POINT</span>
        <span class="logo-tag">Fashion Store</span>
      </a>

      <!-- Navigation Links -->
      <nav class="main-nav" id="mainNav">
        <ul class="nav-links">
          <li><a href="<?php echo BASE_URL; ?>/index.php" class="nav-link">Home</a></li>
          <li><a href="<?php echo BASE_URL; ?>/shop.php" class="nav-link">All Products</a></li>
          
          <!-- Categories Dropdown Menu -->
          <li class="nav-dropdown">
            <a href="<?php echo BASE_URL; ?>/shop.php" class="nav-link">Categories &#9662;</a>
            <div class="dropdown-menu">
              <?php foreach ($nav_categories as $c): ?>
                <a href="<?php echo BASE_URL; ?>/shop.php?category=<?php echo urlencode($c['slug']); ?>" class="dropdown-item">
                  <?php echo safe($c['name']); ?>
                </a>
              <?php endforeach; ?>
            </div>
          </li>

          <li><a href="<?php echo BASE_URL; ?>/contact.php" class="nav-link">Contact Us</a></li>
        </ul>
      </nav>

      <!-- Cart Button & Mobile Hamburger Toggle -->
      <div class="header-actions">
        <a href="<?php echo BASE_URL; ?>/cart.php" class="cart-btn">
          <span>&#128722;</span>
          <span>Bag</span>
          <span class="cart-badge"><?php echo get_cart_count(); ?></span>
        </a>
        <button class="mobile-toggle" id="mobileNavToggle" aria-label="Toggle navigation">&#9776;</button>
      </div>

    </div>
  </header>

  <!-- Database Connection Notice (Shown only if database.sql is not yet imported in phpMyAdmin) -->
  <?php if (!$db_connected): ?>
    <div style="background: #FEE2E2; border-bottom: 2px solid #DC2626; padding: 14px 20px; text-align: center; color: #991B1B; font-size: 0.95rem;">
      <strong>Notice:</strong> Database <code>dresspoint_basic_db</code> is not connected yet. 
      Please open <strong>phpMyAdmin</strong> (<a href="http://localhost/phpmyadmin" target="_blank" style="text-decoration: underline; font-weight: bold;">http://localhost/phpmyadmin</a>) and import <strong>database.sql</strong>.
    </div>
  <?php endif; ?>
