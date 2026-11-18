<?php
/**
 * Dress Point - Admin Shared Header & Navigation
 */
require_once __DIR__ . '/auth_check.php';

// Detect active page to highlight nav items
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? safe($page_title) . ' | Admin | ' . SITE_NAME : 'Admin Portal | ' . SITE_NAME; ?></title>
  
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/admin.css">
</head>
<body>

<div class="admin-wrapper">

  <!-- Left Sidebar Navigation -->
  <aside class="admin-sidebar">
    <div class="admin-brand">
      <h2>&#9830; DRESS<span>POINT</span></h2>
      <small>Administration Portal</small>
    </div>

    <ul class="admin-nav">
      <li>
        <a href="<?php echo BASE_URL; ?>/admin/index.php" class="<?php echo ($current_page === 'index.php') ? 'active' : ''; ?>">
          <span>&#128202;</span> Dashboard
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/admin/products.php" class="<?php echo ($current_page === 'products.php') ? 'active' : ''; ?>">
          <span>&#128087;</span> Products Catalog
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="<?php echo ($current_page === 'orders.php') ? 'active' : ''; ?>">
          <span>&#128230;</span> Customer Orders
        </a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/admin/messages.php" class="<?php echo ($current_page === 'messages.php') ? 'active' : ''; ?>">
          <span>&#9993;</span> Inquiries / Messages
        </a>
      </li>
      <li style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 15px;">
        <a href="<?php echo BASE_URL; ?>/index.php" target="_blank" style="color: #9CA3AF;">
          <span>&#127760;</span> View Live Website &#8599;
        </a>
      </li>
    </ul>

    <div class="admin-user-footer">
      <small style="color: #9CA3AF;">Signed in as:</small>
      <b style="color: var(--champagne); font-size: 0.95rem; margin-top: 2px;">
        &#128100; <?php echo safe($_SESSION['admin_username'] ?? 'admin'); ?>
      </b>
      <div style="margin-top: 10px;">
        <a href="<?php echo BASE_URL; ?>/admin/logout.php" style="color: #EF4444; font-size: 0.82rem; font-weight: 600; text-decoration: none;">
          &#128682; Log Out
        </a>
      </div>
    </div>
  </aside>

  <!-- Main Content Layout -->
  <div class="admin-main">
    
    <!-- Top Action Bar -->
    <header class="admin-top-nav">
      <h1 class="admin-page-title"><?php echo isset($page_title) ? safe($page_title) : 'Dashboard'; ?></h1>
      <div style="display: flex; align-items: center; gap: 14px;">
        <span style="font-size: 0.85rem; color: var(--text-muted);">
          &#128197; <?php echo date('l, F j, Y'); ?>
        </span>
        <a href="<?php echo BASE_URL; ?>/admin/products.php#addNew" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.85rem;">
          + Add New Product
        </a>
      </div>
    </header>

    <!-- Main Container -->
    <div class="admin-container">

