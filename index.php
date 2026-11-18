<?php
/**
 * Dress Point - Home Page
 * University E-Commerce Project
 */
$page_title = "Home — Contemporary & Traditional Fashion";
require_once __DIR__ . '/header.php';

// Fetch all 12 categories for the category cards section
$categories = [];
if ($db_connected) {
    $cat_res = $conn->query("SELECT * FROM categories ORDER BY id ASC");
    if ($cat_res) {
        while ($row = $cat_res->fetch_assoc()) {
            $categories[] = $row;
        }
    }
}

// Fetch featured products using prepared statement
$featured_products = [];
if ($db_connected) {
    $feat_stmt = $conn->prepare("
        SELECT p.*, c.name AS category_name, c.slug AS category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.is_featured = 1 
        ORDER BY p.id ASC 
        LIMIT 10
    ");
    if ($feat_stmt) {
        $feat_stmt->execute();
        $res = $feat_stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $featured_products[] = $row;
        }
        $feat_stmt->close();
    }
}
?>

<!-- Hero Banner -->
<section class="hero-banner">
  <div class="container hero-container">
    <div class="hero-content">
      <div class="hero-pill">
        <span>&#9733;</span> New Festive &amp; Summer Season 2026
      </div>
      <h1 class="hero-title">
        Elegance Defined.<br>
        Style for <span>Every Occasion</span>.
      </h1>
      <p class="hero-desc">
        Explore Sri Lanka's finest collection of contemporary outfits, evening gowns, designer sarees, tailored menswear, footwear, and handcrafted jewelry.
      </p>
      <div class="hero-buttons">
        <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-accent">Explore All Products &rarr;</a>
        <a href="#categoriesSection" class="btn btn-hero-outline">Browse Categories</a>
      </div>
    </div>

    <!-- Neat Small Hero Photo Card on Right -->
    <div class="hero-image-col">
      <div class="hero-neat-card">
        <img src="<?php echo BASE_URL; ?>/images/hero.jpg" alt="Dress Point Festive Collection" class="hero-neat-img">
      </div>
    </div>
  </div>
</section>

<!-- Value Propositions Bar -->
<div class="container">
  <div class="features-bar">
    <div class="features-grid">
      <div class="feature-item">
        <div class="feature-icon">&#128666;</div>
        <div class="feature-info">
          <h4>Island-wide Delivery</h4>
          <p>Delivered to your doorstep across Sri Lanka</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">&#128181;</div>
        <div class="feature-info">
          <h4>Cash on Delivery</h4>
          <p>Pay safely with cash when you receive</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">&#128222;</div>
        <div class="feature-info">
          <h4>Dedicated Support</h4>
          <p>Friendly customer assistance available</p>
        </div>
      </div>
      <div class="feature-item">
        <div class="feature-icon">&#10004;</div>
        <div class="feature-info">
          <h4>Guaranteed Quality</h4>
          <p>Carefully handpicked premium fabrics</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- 12 Category Cards Showcase -->
<section class="section" id="categoriesSection">
  <div class="container">
    <div class="section-header">
      <div class="section-subtitle">Discover Collections</div>
      <h2 class="section-title">Shop by Category</h2>
    </div>

    <div class="categories-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="<?php echo BASE_URL; ?>/shop.php?category=<?php echo urlencode($cat['slug']); ?>" class="category-card">
          <div class="category-img-wrap">
            <img src="<?php echo BASE_URL . '/' . safe($cat['image']); ?>" alt="<?php echo safe($cat['name']); ?>" loading="lazy">
          </div>
          <div class="category-title"><?php echo safe($cat['name']); ?></div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Featured Products Showcase (Small, Uniform Cards with Hover Zoom) -->
<section class="section" style="background-color: var(--bg-white); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
  <div class="container">
    <div class="section-header">
      <div class="section-subtitle">Trending Right Now</div>
      <h2 class="section-title">Featured Highlights</h2>
    </div>

    <div class="product-grid">
      <?php if (!empty($featured_products)): ?>
        <?php foreach ($featured_products as $p): ?>
          <?php 
            $sizes_arr = array_map('trim', explode(',', $p['sizes']));
            $first_size = !empty($sizes_arr) ? $sizes_arr[0] : 'Standard';
          ?>
          <div class="product-card">
            <!-- Thumbnail Image with Hover Zoom -->
            <div class="product-thumb-wrap">
              <img src="<?php echo BASE_URL . '/' . safe($p['image']); ?>" alt="<?php echo safe($p['name']); ?>" loading="lazy">
              
              <div class="product-badge-wrap">
                <span class="badge badge-gold">Featured</span>
              </div>

              <!-- Quick View Overlay -->
              <div class="quick-view-overlay">
                <a href="<?php echo BASE_URL; ?>/product.php?id=<?php echo $p['id']; ?>" class="quick-view-btn">
                  View Details &rarr;
                </a>
              </div>
            </div>

            <!-- Card Information -->
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
        <div class="empty-catalog">
          <h3>No products found</h3>
          <p>Please make sure you have imported database.sql in phpMyAdmin.</p>
        </div>
      <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
      <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-primary" style="padding: 14px 34px;">
        View Complete Store Catalog (96 Items) &rarr;
      </a>
    </div>
  </div>
</section>

<!-- Brand Promotion Banner -->
<section class="section">
  <div class="container">
    <div style="background: linear-gradient(135deg, var(--primary) 0%, #151825 100%); border-radius: var(--radius-lg); border: 2px solid var(--accent); padding: 50px 40px; color: #FFFFFF; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 24px;">
      <div>
        <span style="color: var(--accent); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem;">Exclusive Fashion Collection</span>
        <h2 style="font-size: 2rem; font-weight: 800; margin-top: 6px; margin-bottom: 10px;">Upgrade Your Wardrobe Today</h2>
        <p style="color: #D1D5DB; max-width: 550px; font-size: 0.95rem;">
          Browse our handpicked styles, add your favorite items to your shopping bag, and enjoy convenient Cash on Delivery across Sri Lanka.
        </p>
      </div>
      <div>
        <a href="<?php echo BASE_URL; ?>/shop.php" class="btn btn-accent" style="font-size: 1.05rem; padding: 14px 32px;">
          Start Shopping Now
        </a>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/footer.php'; ?>
