<?php
/**
 * Dress Point Basic - Checkout Page
 * Simple order form (Name, Phone, Address) with Cash on Delivery
 * Saves the order and order items to the database using mysqli prepared statements
 */
require_once __DIR__ . '/config.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculate total cart amounts
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}
$shipping_fee = ($subtotal >= 10000 || $subtotal == 0) ? 0 : 350;
$grand_total = $subtotal + $shipping_fee;

$errors = [];
$order_placed = false;
$placed_order_number = '';
$placed_name = '';
$placed_phone = '';
$placed_address = '';
$placed_total = 0.0;

// Handle checkout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($cart_items)) {
        header('Location: ' . BASE_URL . '/cart.php');
        exit;
    }

    $customer_name    = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $customer_phone   = isset($_POST['customer_phone']) ? trim($_POST['customer_phone']) : '';
    $shipping_address = isset($_POST['shipping_address']) ? trim($_POST['shipping_address']) : '';

    // Validate form inputs
    if (empty($customer_name)) {
        $errors[] = 'Please enter your full name.';
    }
    if (empty($customer_phone)) {
        $errors[] = 'Please enter your phone number.';
    }
    if (empty($shipping_address)) {
        $errors[] = 'Please enter your delivery address.';
    }

    // Save order if valid and database is connected
    if (empty($errors)) {
        if (!$db_connected) {
            $errors[] = 'Database is offline. Please import database.sql in phpMyAdmin first.';
        } else {
            // Generate unique order number (e.g. ORD-20261110-4821)
            $order_number = 'ORD-' . date('Ymd') . '-' . rand(1000, 9999);
            $payment_method = 'Cash on Delivery';
            $status = 'Pending';

            // 1. Insert order into orders table using prepared statement
            $order_stmt = $conn->prepare("
                INSERT INTO orders 
                (order_number, customer_name, customer_phone, shipping_address, payment_method, total_amount, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            if ($order_stmt) {
                $order_stmt->bind_param("sssssds", $order_number, $customer_name, $customer_phone, $shipping_address, $payment_method, $grand_total, $status);
                
                if ($order_stmt->execute()) {
                    $order_id = $conn->insert_id;
                    $order_stmt->close();

                    // 2. Insert each item into order_items table using prepared statement
                    $item_stmt = $conn->prepare("
                        INSERT INTO order_items 
                        (order_id, product_id, product_name, size, quantity, unit_price, subtotal) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");

                    if ($item_stmt) {
                        foreach ($cart_items as $item) {
                            $prod_id  = (int)$item['product_id'];
                            $prod_name= (string)$item['name'];
                            $prod_size= (string)$item['size'];
                            $prod_qty = (int)$item['quantity'];
                            $unit_pr  = (float)$item['price'];
                            $sub_tot  = (float)($unit_pr * $prod_qty);

                            $item_stmt->bind_param("iissidd", $order_id, $prod_id, $prod_name, $prod_size, $prod_qty, $unit_pr, $sub_tot);
                            $item_stmt->execute();
                        }
                        $item_stmt->close();
                    }

                    // Save details for success view
                    $order_placed = true;
                    $placed_order_number = $order_number;
                    $placed_name = $customer_name;
                    $placed_phone = $customer_phone;
                    $placed_address = $shipping_address;
                    $placed_total = $grand_total;

                    // Clear the cart session after order is saved
                    $_SESSION['cart'] = [];
                } else {
                    $errors[] = 'Failed to save order: ' . $conn->error;
                }
            } else {
                $errors[] = 'Database statement error: ' . $conn->error;
            }
        }
    }
}

// If cart is empty and no order was just placed, go to cart
if (empty($cart_items) && !$order_placed) {
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

$page_title = $order_placed ? "Order Confirmed" : "Checkout";
require_once __DIR__ . '/header.php';
?>

<div class="container section">

  <!-- Breadcrumbs -->
  <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 24px;">
    <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &nbsp;/&nbsp; 
    <a href="<?php echo BASE_URL; ?>/cart.php">Shopping Bag</a> &nbsp;/&nbsp; 
    <b style="color: var(--primary);"><?php echo $order_placed ? 'Confirmation' : 'Checkout'; ?></b>
  </div>

  <?php if ($order_placed): ?>
    <!-- Simple Order Confirmation Display -->
    <div style="max-width: 650px; margin: 40px auto; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 40px; box-shadow: var(--shadow-md); text-align: center;">
      <div style="width: 70px; height: 70px; background: #DCFCE7; color: #16A34A; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 20px;">
        &#10003;
      </div>

      <h1 style="font-size: 1.8rem; color: var(--primary); margin-bottom: 8px;">Order Placed Successfully!</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 24px;">
        Thank you for shopping with Dress Point. Your order has been placed with <b>Cash on Delivery</b>.
      </p>

      <div style="background: var(--bg-cream); border: 1px solid var(--border-light); border-radius: var(--radius); padding: 20px; text-align: left; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-light);">
          <span style="color: var(--text-muted);">Order Number:</span>
          <strong style="color: var(--primary);"><?php echo safe($placed_order_number); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-light);">
          <span style="color: var(--text-muted);">Customer Name:</span>
          <strong><?php echo safe($placed_name); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-light);">
          <span style="color: var(--text-muted);">Phone Number:</span>
          <strong><?php echo safe($placed_phone); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-light);">
          <span style="color: var(--text-muted);">Delivery Address:</span>
          <strong><?php echo safe($placed_address); ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px dashed var(--border-light);">
          <span style="color: var(--text-muted);">Payment Method:</span>
          <strong style="color: var(--accent);">Cash on Delivery</strong>
        </div>
        <div style="display: flex; justify-content: space-between; font-size: 1.1rem;">
          <span style="font-weight: 700; color: var(--primary);">Total to Pay:</span>
          <strong style="color: var(--primary);"><?php echo format_price($placed_total); ?></strong>
        </div>
      </div>

      <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap;">
        <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-accent" style="padding: 12px 28px;">
          Continue Shopping &rarr;
        </a>
        <a href="<?php echo BASE_URL; ?>/index.php" class="btn btn-outline" style="padding: 12px 24px;">
          Back to Home
        </a>
      </div>
    </div>

  <?php else: ?>

    <div class="section-header" style="text-align: left; margin-bottom: 30px;">
      <h1 class="section-title" style="text-align: left;">Checkout (Cash on Delivery)</h1>
      <p style="color: var(--text-muted); font-size: 0.95rem;">Please enter your delivery details below to confirm your order.</p>
    </div>

    <?php if (!empty($errors)): ?>
      <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 14px 18px; border-radius: var(--radius); margin-bottom: 24px;">
        <b>Please correct the following:</b>
        <ul style="margin-left: 20px; margin-top: 6px;">
          <?php foreach ($errors as $err): ?>
            <li><?php echo safe($err); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/checkout.php" method="POST">
      <div class="checkout-layout">
        
        <!-- Left Column: Simple Delivery Details Form -->
        <div>
          <div class="checkout-box">
            <h2 class="checkout-box-title">
              <span class="step-num">1</span> Customer &amp; Delivery Details
            </h2>

            <div class="form-grid">
              <div class="form-group form-full">
                <label class="form-label" for="customer_name">Full Name *</label>
                <input type="text" id="customer_name" name="customer_name" class="form-control" placeholder="Enter your full name" value="<?php echo isset($_POST['customer_name']) ? safe($_POST['customer_name']) : ''; ?>" required>
              </div>

              <div class="form-group form-full">
                <label class="form-label" for="customer_phone">Phone Number *</label>
                <input type="tel" id="customer_phone" name="customer_phone" class="form-control" placeholder="e.g. 077 123 4567" value="<?php echo isset($_POST['customer_phone']) ? safe($_POST['customer_phone']) : ''; ?>" required>
              </div>

              <div class="form-group form-full">
                <label class="form-label" for="shipping_address">Delivery Address *</label>
                <textarea id="shipping_address" name="shipping_address" class="form-control" rows="3" placeholder="House/Street, City, Postal Code" required><?php echo isset($_POST['shipping_address']) ? safe($_POST['shipping_address']) : ''; ?></textarea>
              </div>
            </div>
          </div>

          <!-- Step 2: Payment Method (Cash on Delivery Only) -->
          <div class="checkout-box">
            <h2 class="checkout-box-title">
              <span class="step-num">2</span> Payment Method
            </h2>

            <div style="background: #F8FAFC; border: 2px solid var(--accent); border-radius: var(--radius); padding: 20px; display: flex; align-items: center; gap: 16px;">
              <div style="font-size: 2rem; color: var(--accent);">&#128181;</div>
              <div>
                <h4 style="margin: 0 0 4px 0; color: var(--primary);">Cash on Delivery (COD)</h4>
                <p style="margin: 0; color: var(--text-muted); font-size: 0.88rem;">
                  Pay in cash when your package is safely delivered to your doorstep.
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Order Summary -->
        <aside class="order-summary-box">
          <h3 class="summary-title">Order Items (<?php echo get_cart_count(); ?>)</h3>

          <div style="max-height: 240px; overflow-y: auto; margin-bottom: 18px; padding-right: 6px;">
            <?php foreach ($cart_items as $item): ?>
              <div style="display: flex; gap: 12px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px dashed var(--border-light);">
                <img src="<?php echo BASE_URL . '/' . safe($item['image']); ?>" alt="<?php echo safe($item['name']); ?>" style="width: 44px; height: 58px; object-fit: cover; border-radius: var(--radius-sm);">
                <div style="flex: 1; font-size: 0.85rem;">
                  <div style="font-weight: 700; color: var(--primary);"><?php echo safe($item['name']); ?></div>
                  <div style="color: var(--text-muted);">Size: <?php echo safe($item['size']); ?> &bull; Qty: <?php echo $item['quantity']; ?></div>
                  <div style="font-weight: 700; color: var(--accent);"><?php echo format_price($item['price'] * $item['quantity']); ?></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="summary-row">
            <span>Subtotal</span>
            <span><?php echo format_price($subtotal); ?></span>
          </div>

          <div class="summary-row">
            <span>Delivery</span>
            <span><?php echo $shipping_fee === 0 ? '<b style="color:#10B981;">FREE</b>' : format_price($shipping_fee); ?></span>
          </div>

          <div class="summary-row total-row">
            <span>Grand Total</span>
            <span><?php echo format_price($grand_total); ?></span>
          </div>

          <button type="submit" class="btn btn-accent btn-block" style="padding: 14px; margin-top: 20px; font-size: 1.05rem;">
            Place Order (Cash on Delivery) &rarr;
          </button>

          <div style="text-align: center; font-size: 0.78rem; color: var(--text-muted); margin-top: 14px;">
            &#128274; Island-wide doorstep delivery across Sri Lanka.
          </div>
        </aside>

      </div>
    </form>

  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
