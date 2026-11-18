<?php
/**
 * Dress Point Basic - Admin Orders Management
 * View customer orders and update status (Pending, Processing, Shipped, Delivered)
 * Uses mysqli prepared statements for all operations
 */
require_once __DIR__ . '/../config.php';

$success_msg = '';
$error_msg = '';

// 1. Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id   = (int)($_POST['order_id'] ?? 0);
    $new_status = trim($_POST['new_status'] ?? '');
    $allowed_statuses = ['Pending', 'Processing', 'Shipped', 'Delivered'];

    if ($order_id > 0 && in_array($new_status, $allowed_statuses) && $db_connected) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $new_status, $order_id);
            if ($stmt->execute()) {
                $success_msg = 'Order #' . $order_id . ' status updated to "' . safe($new_status) . '".';
            } else {
                $error_msg = 'Failed to update order status: ' . $conn->error;
            }
            $stmt->close();
        }
    }
}

// 2. Fetch all orders (Direct List)
$orders = [];
if ($db_connected) {
    $res = $conn->query("SELECT * FROM orders ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $orders[] = $row;
        }
    }
}

// 3. Preload order items for each order
$order_items_by_id = [];
if (!empty($orders) && $db_connected) {
    $order_ids = array_column($orders, 'id');
    $in_clause = implode(',', array_map('intval', $order_ids));
    $items_res = $conn->query("SELECT * FROM order_items WHERE order_id IN ($in_clause) ORDER BY id ASC");
    if ($items_res) {
        while ($item = $items_res->fetch_assoc()) {
            $order_items_by_id[$item['order_id']][] = $item;
        }
    }
}

$page_title = "Customer Orders";
require_once __DIR__ . '/header.php';
?>

<?php if (!empty($success_msg)): ?>
  <div style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; padding: 14px 18px; border-radius: var(--radius); margin-bottom: 24px; font-weight: 500;">
    &#10003; <?php echo safe($success_msg); ?>
  </div>
<?php endif; ?>

<?php if (!empty($error_msg)): ?>
  <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 14px 18px; border-radius: var(--radius); margin-bottom: 24px; font-weight: 500;">
    &#9888; <?php echo safe($error_msg); ?>
  </div>
<?php endif; ?>

<div class="admin-card-header" style="margin-bottom: 20px; padding: 0;">
  <h3>All Customer Orders (<?php echo count($orders); ?>)</h3>
</div>

<?php if (empty($orders)): ?>
  <div class="admin-card" style="padding: 50px; text-align: center; color: var(--text-muted);">
    <h3>No Orders Found</h3>
    <p style="margin-top: 8px;">No customer orders have been placed yet.</p>
  </div>
<?php else: ?>
  <?php foreach ($orders as $ord): 
    $items = $order_items_by_id[$ord['id']] ?? [];
  ?>
    <div class="admin-card" style="margin-bottom: 24px;">
      
      <!-- Order Header Bar -->
      <div class="admin-card-header" style="background: #F8FAFC;">
        <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
          <h3 style="margin: 0; color: var(--primary);">
            Order #<?php echo safe($ord['order_number']); ?>
          </h3>
          <span style="font-size: 0.85rem; color: var(--text-muted);">
            &#128197; <?php echo date('M d, Y - h:i A', strtotime($ord['created_at'])); ?>
          </span>
          <span class="badge" style="background: #DCFCE7; color: #166534;">
            Cash on Delivery
          </span>
        </div>

        <!-- Status Form Pill -->
        <div>
          <form action="<?php echo BASE_URL; ?>/admin/orders.php" method="POST" style="display: flex; align-items: center; gap: 8px;">
            <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
            <label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">Status:</label>
            <select name="new_status" class="form-control" style="width: auto; padding: 6px 12px; font-weight: 700; font-size: 0.88rem;">
              <option value="Pending" <?php echo ($ord['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
              <option value="Processing" <?php echo ($ord['status'] === 'Processing') ? 'selected' : ''; ?>>Processing</option>
              <option value="Shipped" <?php echo ($ord['status'] === 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
              <option value="Delivered" <?php echo ($ord['status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
            </select>
            <button type="submit" name="update_status" class="btn btn-primary" style="padding: 6px 12px; font-size: 0.85rem;">
              Update
            </button>
          </form>
        </div>
      </div>

      <!-- Order Details Grid -->
      <div style="padding: 20px 24px; display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px;">
        
        <!-- Customer & Delivery Information -->
        <div style="background: var(--bg-cream); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 18px;">
          <h4 style="margin: 0 0 12px 0; color: var(--primary); font-size: 0.95rem;">Customer Information</h4>
          <div style="font-size: 0.9rem; line-height: 1.6;">
            <div><b>Name:</b> <?php echo safe($ord['customer_name']); ?></div>
            <div><b>Phone:</b> <a href="tel:<?php echo safe($ord['customer_phone']); ?>" style="color: var(--primary); text-decoration: none;"><?php echo safe($ord['customer_phone']); ?></a></div>
            <?php if (!empty($ord['customer_email'])): ?>
              <div><b>Email:</b> <?php echo safe($ord['customer_email']); ?></div>
            <?php endif; ?>
            <div style="margin-top: 8px;"><b>Delivery Address:</b><br><?php echo nl2br(safe($ord['shipping_address'])); ?></div>
          </div>
        </div>

        <!-- Ordered Items Table -->
        <div>
          <h4 style="margin: 0 0 12px 0; color: var(--primary); font-size: 0.95rem;">Order Items</h4>
          <table class="admin-table" style="font-size: 0.88rem;">
            <thead>
              <tr>
                <th>Item</th>
                <th>Size</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th style="text-align: right;">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $it): ?>
                <tr>
                  <td><b><?php echo safe($it['product_name']); ?></b></td>
                  <td><?php echo safe($it['size']); ?></td>
                  <td><?php echo $it['quantity']; ?></td>
                  <td><?php echo format_price($it['unit_price']); ?></td>
                  <td style="text-align: right;"><b><?php echo format_price($it['subtotal']); ?></b></td>
                </tr>
              <?php endforeach; ?>
              <tr style="background: #F8FAFC; font-weight: 700;">
                <td colspan="4" style="text-align: right; color: var(--primary);">Total Order Amount:</td>
                <td style="text-align: right; color: var(--primary); font-size: 1rem;">
                  <?php echo format_price($ord['total_amount']); ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>

    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>
