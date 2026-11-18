<?php
/**
 * Dress Point Basic - Admin Dashboard
 * Shows a simple summary: Total Products, Total Orders, and Total Messages
 */
$page_title = "Admin Dashboard";
require_once __DIR__ . '/header.php';  //not rewrite header file. 

// Default KPI counts
$total_products = 0;
$total_orders = 0;
$total_messages = 0;

if ($db_connected) {
    // 1. Total Products Count
    $prod_res = $conn->query("SELECT COUNT(*) AS total FROM products");
    if ($prod_res && $row = $prod_res->fetch_assoc()) {
        $total_products = (int)($row['total'] ?? 0);
    }

    // 2. Total Orders Count
    $ord_res = $conn->query("SELECT COUNT(*) AS total FROM orders");
    if ($ord_res && $row = $ord_res->fetch_assoc()) {
        $total_orders = (int)($row['total'] ?? 0);
    }

    // 3. Total Messages Count
    $msg_res = $conn->query("SELECT COUNT(*) AS total FROM messages");
    if ($msg_res && $row = $msg_res->fetch_assoc()) {
        $total_messages = (int)($row['total'] ?? 0);
    }
}
?>

<!-- Simple Dashboard 3-Card Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
  
  <!-- Total Products Card -->
  <div class="stat-card">
    <div class="stat-info">
      <p>Total Products</p>
      <h3><?php echo number_format($total_products); ?></h3>
      <a href="<?php echo BASE_URL; ?>/admin/products.php" style="font-size: 0.85rem; color: var(--champagne); text-decoration: none; font-weight: 600; margin-top: 6px; display: inline-block;">
        Manage Products &rarr;
      </a>
    </div>
    <div class="stat-icon">&#128087;</div>
  </div>

  <!-- Total Orders Card -->
  <div class="stat-card">
    <div class="stat-info">
      <p>Total Orders</p>
      <h3><?php echo number_format($total_orders); ?></h3>
      <a href="<?php echo BASE_URL; ?>/admin/orders.php" style="font-size: 0.85rem; color: var(--champagne); text-decoration: none; font-weight: 600; margin-top: 6px; display: inline-block;">
        View Orders &rarr;
      </a>
    </div>
    <div class="stat-icon">&#128230;</div>
  </div>

  <!-- Total Messages Card -->
  <div class="stat-card">
    <div class="stat-info">
      <p>Total Messages</p>
      <h3><?php echo number_format($total_messages); ?></h3>
      <a href="<?php echo BASE_URL; ?>/admin/messages.php" style="font-size: 0.85rem; color: var(--champagne); text-decoration: none; font-weight: 600; margin-top: 6px; display: inline-block;">
        View Messages &rarr;
      </a>
    </div>
    <div class="stat-icon">&#9993;</div>
  </div>

</div>

<!-- Quick Navigation & Management Card -->
<div class="admin-card" style="margin-top: 30px;">
  <div class="admin-card-header">
    <h3>Quick Management Actions</h3>
  </div>
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; padding: 10px 0;">
    <div style="background: #F8FAFC; border: 1px solid var(--border-light); border-radius: var(--radius); padding: 20px;">
      <h4 style="margin: 0 0 8px 0; color: var(--primary);">&#128087; Products Management</h4>
      <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0 0 14px 0;">Add new clothes, upload images, update pricing or remove out-of-stock items.</p>
      <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn-primary btn-sm">Go to Products</a>
    </div>
    <div style="background: #F8FAFC; border: 1px solid var(--border-light); border-radius: var(--radius); padding: 20px;">
      <h4 style="margin: 0 0 8px 0; color: var(--primary);">&#128230; Customer Orders</h4>
      <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0 0 14px 0;">Review incoming Cash on Delivery orders, view customer info, and update order status.</p>
      <a href="<?php echo BASE_URL; ?>/admin/orders.php" class="btn btn-primary btn-sm">Go to Orders</a>
    </div>
    <div style="background: #F8FAFC; border: 1px solid var(--border-light); border-radius: var(--radius); padding: 20px;">
      <h4 style="margin: 0 0 8px 0; color: var(--primary);">&#9993; Customer Messages</h4>
      <p style="color: var(--text-muted); font-size: 0.88rem; margin: 0 0 14px 0;">Read questions and inquiries sent by customers via the contact form.</p>
      <a href="<?php echo BASE_URL; ?>/admin/messages.php" class="btn btn-primary btn-sm">Go to Messages</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
