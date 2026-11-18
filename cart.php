<?php
/**
 * Dress Point - Shopping Cart Page
 * Displays selected items, size breakdown, quantity adjusters, and price summary
 */
$page_title = "Your Shopping Bag";
require_once __DIR__ . '/header.php';

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$subtotal = 0;

foreach ($cart_items as $item) {
    $subtotal += ($item['price'] * $item['quantity']);
}

// Shipping calculation (Free for orders over Rs. 10,000; otherwise flat Rs. 350 island-wide)
$shipping_fee = ($subtotal >= 10000 || $subtotal == 0) ? 0 : 350;
$grand_total = $subtotal + $shipping_fee;
?>

<div class="container section">

  <!-- Breadcrumbs -->
  <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 24px;">
    <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &nbsp;/&nbsp; 
    <b style="color: var(--primary);">Shopping Bag</b>
  </div>

  <div class="section-header" style="text-align: left; margin-bottom: 30px;">
    <h1 class="section-title" style="text-align: left;">Your Shopping Bag (<?php echo get_cart_count(); ?> items)</h1>
  </div>

  <?php if (!empty($cart_items)): ?>
    <div class="cart-layout">
      
      <!-- Cart Items Table -->
      <div class="cart-table-wrap">
        <table class="cart-table">
          <thead>
            <tr>
              <th>Product</th>
              <th>Unit Price</th>
              <th>Quantity</th>
              <th>Subtotal</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cart_items as $key => $item): ?>
              <?php $item_subtotal = $item['price'] * $item['quantity']; ?>
              <tr>
                <td>
                  <div class="cart-item-cell">
                    <img src="<?php echo BASE_URL . '/' . safe($item['image']); ?>" alt="<?php echo safe($item['name']); ?>" class="cart-item-img">
                    <div class="cart-item-info">
                      <h4>
                        <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $item['product_id']; ?>">
                          <?php echo safe($item['name']); ?>
                        </a>
                      </h4>
                      <div class="cart-item-size">Selected Size: <b><?php echo safe($item['size']); ?></b></div>
                    </div>
                  </div>
                </td>

                <td class="cart-item-price">
                  <?php echo format_price($item['price']); ?>
                </td>

                <td>
                  <!-- Quantity Update Form -->
                  <form action="<?php echo BASE_URL; ?>/cart_action.php" method="POST" style="display: flex; align-items: center; gap: 4px;">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="item_key" value="<?php echo safe($key); ?>">
                    
                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="qty-btn" style="height: 32px; width: 32px; border: 1px solid var(--border-light);">&minus;</button>
                    <span style="font-weight: 700; min-width: 28px; text-align: center;"><?php echo $item['quantity']; ?></span>
                    <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="qty-btn" style="height: 32px; width: 32px; border: 1px solid var(--border-light);">&plus;</button>
                  </form>
                </td>

                <td class="cart-item-subtotal">
                  <?php echo format_price($item_subtotal); ?>
                </td>

                <td>
                  <a href="<?php echo BASE_URL; ?>/cart_action.php?action=remove&key=<?php echo urlencode($key); ?>" 
                     class="cart-remove-btn" 
                     title="Remove item"
                     onclick="return confirm('Remove this item from your bag?');">
                    &#10006;
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <!-- Bottom Table Bar -->
        <div style="padding: 16px 20px; background: var(--bg-cream); display: flex; justify-content: space-between; align-items: center;">
          <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-outline btn-sm">
            &larr; Continue Shopping
          </a>
          <a href="<?php echo BASE_URL; ?>/cart_action.php?action=clear" class="btn btn-sm" style="background: #FEE2E2; color: #DC2626;" onclick="return confirm('Clear your entire bag?');">
            Clear Bag
          </a>
        </div>
      </div>

      <!-- Right Order Summary Box -->
      <aside class="order-summary-box">
        <h3 class="summary-title">Order Summary</h3>

        <div class="summary-row">
          <span>Bag Subtotal</span>
          <span><?php echo format_price($subtotal); ?></span>
        </div>

        <div class="summary-row">
          <span>Island-wide Delivery</span>
          <span>
            <?php if ($shipping_fee === 0): ?>
              <b style="color: #10B981;">FREE</b>
            <?php else: ?>
              <?php echo format_price($shipping_fee); ?>
            <?php endif; ?>
          </span>
        </div>

        <?php if ($subtotal < 10000): ?>
          <div style="font-size: 0.78rem; background: var(--accent-soft); color: var(--primary); padding: 8px 10px; border-radius: var(--radius-sm); margin-bottom: 14px;">
            &#9733; Add <b><?php echo format_price(10000 - $subtotal); ?></b> more to unlock <b>FREE Delivery</b>!
          </div>
        <?php endif; ?>

        <div class="summary-row total-row">
          <span>Grand Total</span>
          <span><?php echo format_price($grand_total); ?></span>
        </div>

        <a href="<?php echo BASE_URL; ?>/checkout.php" class="btn btn-accent btn-block" style="padding: 14px; margin-top: 18px; font-size: 1.05rem;">
          Proceed to Checkout &rarr;
        </a>

        <div style="margin-top: 20px; font-size: 0.78rem; color: var(--text-muted); text-align: center;">
          &#128274; Safe & Secure Checkout &bull; Cash on Delivery Available
        </div>
      </aside>

    </div>
  <?php else: ?>
    <!-- Empty Cart Display -->
    <div class="empty-catalog" style="max-width: 600px; margin: 40px auto;">
      <div style="font-size: 3.5rem; color: var(--accent); margin-bottom: 12px;">&#128722;</div>
      <h2>Your Shopping Bag is Empty</h2>
      <p>Discover our latest arrivals in heels, dresses, sarees, and menswear to add to your collection.</p>
      <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-accent" style="margin-top: 10px;">
        Browse Catalog Now &rarr;
      </a>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>

