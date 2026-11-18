<?php
/**
 * Dress Point Basic - Shop Catalog Page
 * Shows products from the database by category
 * Uses mysqli prepared statements for SQL security
 */
$page_title = "Shop Products";
require_once __DIR__ . '/header.php';

// Get selected category slug from URL parameter
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';

// 1. Fetch all categories for the category menu
$categories = [];
$active_cat_name = 'All Products';

if ($db_connected) {
    $cat_res = $conn->query("
        SELECT c.*, COUNT(p.id) AS prod_count 
        FROM categories c 
        LEFT JOIN products p ON c.id = p.category_id 
        GROUP BY c.id 
        ORDER BY c.id ASC
    ");
    if ($cat_res) {
        while ($row = $cat_res->fetch_assoc()) {
            $categories[] = $row;
            if ($selected_category !== '' && $row['slug'] === $selected_category) {
                $active_cat_name = $row['name'];
            }
        }
    }
}

// 2. Fetch products by category using prepared statements
$products = [];
if ($db_connected) {
    if ($selected_category !== '') {
        // Query products for a specific category
        $stmt = $conn->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE c.slug = ? 
            ORDER BY p.id DESC
        ");
        if ($stmt) {
            $stmt->bind_param("s", $selected_category);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt->close();
        }
    } else {
        // Query all products when no category is selected
        $stmt = $conn->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            ORDER BY p.id DESC
        ");
        if ($stmt) {
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) {
                $products[] = $row;
            }
            $stmt->close();
        }
    }
}
?>

<div class="container section">
  
  <!-- Breadcrumbs -->
  <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 20px;">
    <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &nbsp;/&nbsp; 
    <a href="<?php echo BASE_URL; ?>/shop.php">Shop</a> 
    <?php if ($selected_category !== ''): ?>
      &nbsp;/&nbsp; <b style="color: var(--primary);"><?php echo safe($active_cat_name); ?></b>
    <?php endif; ?>
  </div>

  <div class="shop-layout">
    
    <!-- Left Category Navigation Sidebar -->
    <aside class="filter-sidebar">
      <div class="filter-sidebar-title">
        <span>Categories</span>
      </div>

      <div class="filter-group">
        <ul class="filter-category-list">
          <li class="filter-category-item">
            <a href="<?php echo BASE_URL; ?>/shop.php" 
               class="filter-category-link <?php echo $selected_category === '' ? 'active' : ''; ?>">
              <span>All Products</span>
            </a>
          </li>
          <?php foreach ($categories as $cat): ?>
            <li class="filter-category-item">
              <a href="<?php echo BASE_URL; ?>/shop.php?category=<?php echo urlencode($cat['slug']); ?>" 
                 class="filter-category-link <?php echo $selected_category === $cat['slug'] ? 'active' : ''; ?>">
                <span><?php echo safe($cat['name']); ?></span>
                <span class="filter-count"><?php echo $cat['prod_count']; ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>

    <!-- Right Products Grid Container -->
    <main>
      
      <!-- Catalog Header -->
      <div class="shop-header">
        <div class="shop-results-count">
          Showing <b><?php echo count($products); ?></b> items in <b><?php echo safe($active_cat_name); ?></b>
        </div>
      </div>

      <!-- Uniform Product Cards Grid -->
      <div class="product-grid">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $p): ?>
            <?php 
              $sizes_arr = array_map('trim', explode(',', $p['sizes']));
              $first_size = !empty($sizes_arr) ? $sizes_arr[0] : 'Standard';
            ?>
            <div class="product-card">
              <!-- Thumbnail with Hover Zoom -->
              <div class="product-thumb-wrap">
                <img src="<?php echo BASE_URL . '/' . safe($p['image']); ?>" alt="<?php echo safe($p['name']); ?>" loading="lazy">
                
                <?php if ($p['is_featured']): ?>
                  <div class="product-badge-wrap">
                    <span class="badge badge-gold">Featured</span>
                  </div>
                <?php endif; ?>

                <div class="quick-view-overlay">
                  <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $p['id']; ?>" class="quick-view-btn">
                    View Details &rarr;
                  </a>
                </div>
              </div>

              <!-- Product Information -->
              <div class="product-info">
                <span class="product-category-name"><?php echo safe($p['category_name']); ?></span>
                <h3 class="product-name">
                  <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $p['id']; ?>">
                    <?php echo safe($p['name']); ?>
                  </a>
                </h3>

                <div class="product-sizes-preview">
                  Sizes: <b><?php echo safe($p['sizes']); ?></b>
                </div>

                <div class="product-bottom-row">
                  <div class="product-price">
                    <?php echo format_price($p['price']); ?>
                  </div>

                  <button type="button" class="card-add-btn" 
                          title="Add to Cart"
                          onclick="addToCart(<?php echo $p['id']; ?>, '<?php echo addslashes($p['name']); ?>', <?php echo $p['price']; ?>, '<?php echo addslashes($first_size); ?>', '<?php echo addslashes($p['image']); ?>', 1)">
                    &#43;
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-catalog" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
            <h3>No products found</h3>
            <p>No products are currently available in this category.</p>
            <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-primary" style="margin-top: 15px;">Browse All Products</a>
          </div>
        <?php endif; ?>
      </div>

    </main>

  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
