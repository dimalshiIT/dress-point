<?php
/**
 * Dress Point - Product Detail Page
 * Features: Large image display, thumbnail gallery, size selector pills, price in LKR, Add to Cart & Related items
 * Uses mysqli prepared statement for SQL security
 */
require_once __DIR__ . '/config.php';

// Validate product ID parameter
$product_id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header('Location: ' . BASE_URL . '/shop.php');
    exit;
}

// Fetch product details using prepared statement
$product = null;
if ($db_connected) {
    $stmt = $conn->prepare("
        SELECT p.*, c.name AS category_name, c.slug AS category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ? 
        LIMIT 1
    ");
    if ($stmt) {
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    }
}

if (!$product) {
    header('Location: ' . BASE_URL . '/shop.php');
    exit;
}

$page_title = safe($product['name']);
require_once __DIR__ . '/header.php';

// Parse available sizes into an array
$sizes_list = array_map('trim', explode(',', $product['sizes']));

// Fetch related items from the same category
$related_products = [];
if ($db_connected) {
    $rel_stmt = $conn->prepare("
        SELECT p.*, c.name AS category_name, c.slug AS category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.category_id = ? AND p.id != ? 
        ORDER BY RAND() 
        LIMIT 4
    ");
    if ($rel_stmt) {
        $cat_id = $product['category_id'];
        $rel_stmt->bind_param("ii", $cat_id, $product_id);
        $rel_stmt->execute();
        $rel_res = $rel_stmt->get_result();
        while ($row = $rel_res->fetch_assoc()) {
            $related_products[] = $row;
        }
        $rel_stmt->close();
    }
}
?>

<div class="container section">

  <!-- Breadcrumbs -->
  <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 24px;">
    <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &nbsp;/&nbsp; 
    <a href="<?php echo BASE_URL; ?>/shop.php">Shop</a> &nbsp;/&nbsp; 
    <a href="<?php echo BASE_URL; ?>/shop.php?category=<?php echo urlencode($product['category_slug']); ?>"><?php echo safe($product['category_name']); ?></a> &nbsp;/&nbsp; 
    <b style="color: var(--primary);"><?php echo safe($product['name']); ?></b>
  </div>

  <!-- Product Main Detail Grid -->
  <div class="product-detail-grid">
    
    <!-- Gallery Column (Large Image + Thumbnail Selector) -->
    <div class="product-gallery">
      <div class="gallery-main">
        <img id="mainProductImg" src="<?php echo BASE_URL . '/' . safe($product['image']); ?>" alt="<?php echo safe($product['name']); ?>">
      </div>

      <!-- Thumbnail Preview Gallery -->
      <div class="gallery-thumbs">
        <div class="thumb-item active">
          <img src="<?php echo BASE_URL . '/' . safe($product['image']); ?>" alt="Primary Angle">
        </div>
        <!-- Additional aesthetic thumbnail views -->
        <div class="thumb-item">
          <img src="<?php echo BASE_URL . '/' . safe($product['image']); ?>" alt="Close-up View">
        </div>
      </div>
    </div>

    <!-- Product Information Column -->
    <div class="product-details">
      <span class="detail-category"><?php echo safe($product['category_name']); ?></span>
      <h1 class="detail-title"><?php echo safe($product['name']); ?></h1>

      <div class="detail-price-box">
        <span class="detail-price"><?php echo format_price($product['price']); ?></span>
        <span class="detail-stock">&#10004; In Stock (<?php echo (int)$product['stock']; ?> available)</span>
      </div>

      <p class="detail-desc">
        <?php echo nl2br(safe($product['description'])); ?>
      </p>

      <!-- Add to Bag Form -->
      <form action="<?php echo BASE_URL; ?>/cart_action.php" method="POST" id="addToCartForm">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="name" value="<?php echo safe($product['name']); ?>">
        <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
        <input type="hidden" name="image" value="<?php echo safe($product['image']); ?>">

        <!-- Sizes Selection Pills -->
        <div class="detail-option-group">
          <label class="option-label">Select Available Size:</label>
          <div class="size-selector">
            <?php foreach ($sizes_list as $index => $size_val): ?>
              <label class="size-radio-label">
                <input type="radio" name="size" value="<?php echo safe($size_val); ?>" <?php echo $index === 0 ? 'checked' : ''; ?> required>
                <span class="size-pill"><?php echo safe($size_val); ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Quantity Picker & Action Buttons -->
        <div class="detail-actions">
          <div class="qty-picker">
            <button type="button" class="qty-btn" onclick="let q = document.getElementById('detailQty'); if(q.value > 1) q.value--;">&minus;</button>
            <input type="number" name="quantity" id="detailQty" class="qty-input" value="1" min="1" max="<?php echo (int)$product['stock']; ?>">
            <button type="button" class="qty-btn" onclick="let q = document.getElementById('detailQty'); if(q.value < <?php echo (int)$product['stock']; ?>) q.value++;">&plus;</button>
          </div>

          <button type="submit" class="btn btn-accent detail-add-btn">
            <span>&#128722;</span> Add to Shopping Bag
          </button>
        </div>
      </form>

      <!-- Value Delivery Features -->
      <div style="background: var(--bg-cream); border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 18px; margin-top: 20px; font-size: 0.88rem; display: flex; flex-direction: column; gap: 8px;">
        <div><b>&#128666; Island-wide Delivery:</b> 2-3 business days within Western Province, 3-5 days outstation.</div>
        <div><b>&#128181; Payment Options:</b> Cash on Delivery available island-wide.</div>
        <div><b>&#8644; Easy Returns:</b> 7-day hassle-free size exchange guarantee.</div>
      </div>

    </div>

  </div>

  <!-- Related Products in Category -->
  <?php if (!empty($related_products)): ?>
    <div style="margin-top: 60px;">
      <div class="section-header" style="text-align: left; margin-bottom: 24px;">
        <div class="section-subtitle">Recommended For You</div>
        <h2 class="section-title" style="text-align: left;">Similar <?php echo safe($product['category_name']); ?></h2>
      </div>

      <div class="product-grid">
        <?php foreach ($related_products as $rp): ?>
          <?php 
            $rp_sizes = array_map('trim', explode(',', $rp['sizes']));
            $rp_first_size = !empty($rp_sizes) ? $rp_sizes[0] : 'Standard';
          ?>
          <div class="product-card">
            <div class="product-thumb-wrap">
              <img src="<?php echo BASE_URL . '/' . safe($rp['image']); ?>" alt="<?php echo safe($rp['name']); ?>" loading="lazy">
              <div class="quick-view-overlay">
                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $rp['id']; ?>" class="quick-view-btn">
                  View Item &rarr;
                </a>
              </div>
            </div>
            <div class="product-info">
              <span class="product-category-name"><?php echo safe($rp['category_name']); ?></span>
              <h3 class="product-name">
                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $rp['id']; ?>">
                  <?php echo safe($rp['name']); ?>
                </a>
              </h3>
              <div class="product-bottom-row">
                <div class="product-price"><?php echo format_price($rp['price']); ?></div>
                <button type="button" class="card-add-btn" 
                        title="Add to Cart"
                        onclick="addToCart(<?php echo $rp['id']; ?>, '<?php echo addslashes($rp['name']); ?>', <?php echo $rp['price']; ?>, '<?php echo addslashes($rp_first_size); ?>', '<?php echo addslashes($rp['image']); ?>', 1)">
                  &#43;
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>

