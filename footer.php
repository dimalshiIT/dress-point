<?php
/**
 * Dress Point Basic - Footer Component
 * Includes store details, quick links, category shortcuts, and copyright
 */
?>
  <!-- Footer -->
  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        
        <!-- Column 1: About -->
        <div class="footer-col">
          <h3>About Dress Point</h3>
          <p>
            Dress Point is an exclusive online fashion store offering modern and traditional apparel, heels, shoes, and curated accessories for fashion lovers and enthusiasts across Sri Lanka.
          </p>
          <p style="color: var(--accent); font-weight: 600;">
            100% Quality Guaranteed &bull; Island-wide Delivery
          </p>
        </div>

        <!-- Column 2: Quick Links -->
        <div class="footer-col">
          <h3>Quick Links</h3>
          <ul class="footer-links">
            <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/shop.php">All Products</a></li>
            <li><a href="<?php echo BASE_URL; ?>/cart.php">Shopping Bag</a></li>
            <li><a href="<?php echo BASE_URL; ?>/contact.php">Contact & Support</a></li>
            <li><a href="<?php echo BASE_URL; ?>/admin/login.php">Admin Portal</a></li>
          </ul>
        </div>

        <!-- Column 3: Featured Categories -->
        <div class="footer-col">
          <h3>Top Departments</h3>
          <ul class="footer-links">
            <li><a href="<?php echo BASE_URL; ?>/shop.php?category=heels">Designer Heels</a></li>
            <li><a href="<?php echo BASE_URL; ?>/shop.php?category=gents-wear">Gents Wear</a></li>
            <li><a href="<?php echo BASE_URL; ?>/shop.php?category=sarees">Traditional Sarees</a></li>
            <li><a href="<?php echo BASE_URL; ?>/shop.php?category=dresses-gowns">Dresses & Gowns</a></li>
            <li><a href="<?php echo BASE_URL; ?>/shop.php?category=kids-wear">Kids Wear</a></li>
          </ul>
        </div>

        <!-- Column 4: Contact Info -->
        <div class="footer-col">
          <h3>Store Information</h3>
          <ul class="footer-contact">
            <li>
              <span>&#128205;</span>
              <span>45 Galle Road, Bambalapitiya, Colombo 04, Sri Lanka</span>
            </li>
            <li>
              <span>&#128222;</span>
              <span>+94 (0) 11 234 5678 / +94 77 123 4567</span>
            </li>
            <li>
              <span>&#9993;</span>
              <span>support@dresspoint.lk</span>
            </li>
            <li>
              <span>&#128337;</span>
              <span>Mon - Sat: 9:00 AM - 8:00 PM</span>
            </li>
          </ul>
        </div>

      </div>

      <!-- Copyright Bar -->
      <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <strong>Dress Point</strong>. Premium Online Fashion Store. All Rights Reserved.</p>
      </div>
    </div>
  </footer>

  <!-- Vanilla JavaScript -->
  <script src="<?php echo BASE_URL; ?>/js/main.js"></script>
</body>
</html>
