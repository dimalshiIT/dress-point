<?php
/**
 * Dress Point Basic - Admin Products Management (CRUD)
 * Allows adding, viewing, editing, and deleting products with image upload
 * Uses mysqli prepared statements for all operations
 */
require_once __DIR__ . '/../config.php';

$success_msg = '';
$error_msg = '';

// 1. Fetch all categories for dropdown selection
$all_categories = [];
if ($db_connected) {
    $cat_query = $conn->query("SELECT id, name, slug FROM categories ORDER BY name ASC");
    if ($cat_query) {
        while ($c = $cat_query->fetch_assoc()) {
            $all_categories[$c['id']] = $c;
        }
    }
}

// 2. Handle DELETE Product
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $delete_id = (int)$_GET['id'];
    if ($db_connected && $delete_id > 0) {
        $del_stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        if ($del_stmt) {
            $del_stmt->bind_param("i", $delete_id);
            if ($del_stmt->execute()) {
                $success_msg = 'Product #' . $delete_id . ' deleted successfully.';
            } else {
                $error_msg = 'Error deleting product: ' . $conn->error;
            }
            $del_stmt->close();
        }
    }
}

// 3. Handle ADD New Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name        = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price       = (float)($_POST['price'] ?? 0);
    $sizes       = trim($_POST['sizes'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name) || $category_id <= 0 || $price <= 0) {
        $error_msg = 'Please fill all required fields (Name, Category, and valid Price).';
    } else {
        $image_path = 'images/heels/heels-1.jpg'; // Default fallback image
        
        // Handle Image Upload if provided
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['product_image']['tmp_name'];
            $file_name = $_FILES['product_image']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed)) {
                $cat_slug = isset($all_categories[$category_id]) ? $all_categories[$category_id]['slug'] : 'general';
                $target_dir = __DIR__ . '/../images/' . $cat_slug . '/';
                
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $clean_filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
                $new_filename = $clean_filename . '_' . time() . '.' . $file_ext;
                $destination = $target_dir . $new_filename;

                if (move_uploaded_file($file_tmp, $destination)) {
                    $image_path = 'images/' . $cat_slug . '/' . $new_filename;
                } else {
                    $error_msg = 'Failed to upload product image.';
                }
            } else {
                $error_msg = 'Invalid image type. Please choose a JPG, PNG, or WEBP image.';
            }
        }

        if (empty($error_msg) && $db_connected) {
            $insert_stmt = $conn->prepare("INSERT INTO products (category_id, name, description, price, sizes, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($insert_stmt) {
                $insert_stmt->bind_param("issdssi", $category_id, $name, $description, $price, $sizes, $image_path, $is_featured);
                if ($insert_stmt->execute()) {
                    $success_msg = 'New product "' . safe($name) . '" added successfully!';
                } else {
                    $error_msg = 'Database error adding product: ' . $conn->error;
                }
                $insert_stmt->close();
            }
        }
    }
}

// 4. Handle UPDATE Existing Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $edit_id     = (int)($_POST['product_id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price       = (float)($_POST['price'] ?? 0);
    $sizes       = trim($_POST['sizes'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    if ($edit_id > 0 && !empty($name) && $category_id > 0 && $price > 0 && $db_connected) {
        $update_image = false;
        $image_path = '';

        // Check if user uploaded a replacement image
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['product_image']['tmp_name'];
            $file_name = $_FILES['product_image']['name'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed   = ['jpg', 'jpeg', 'png', 'webp'];

            if (in_array($file_ext, $allowed)) {
                $cat_slug = isset($all_categories[$category_id]) ? $all_categories[$category_id]['slug'] : 'general';
                $target_dir = __DIR__ . '/../images/' . $cat_slug . '/';
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                $clean_filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file_name, PATHINFO_FILENAME));
                $new_filename = $clean_filename . '_' . time() . '.' . $file_ext;
                if (move_uploaded_file($file_tmp, $target_dir . $new_filename)) {
                    $image_path = 'images/' . $cat_slug . '/' . $new_filename;
                    $update_image = true;
                }
            }
        }

        if ($update_image) {
            $upd_stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, sizes = ?, image = ?, is_featured = ? WHERE id = ?");
            if ($upd_stmt) {
                $upd_stmt->bind_param("issdssii", $category_id, $name, $description, $price, $sizes, $image_path, $is_featured, $edit_id);
                $upd_stmt->execute();
                $upd_stmt->close();
                $success_msg = 'Product #' . $edit_id . ' updated successfully (including new image)!';
            }
        } else {
            $upd_stmt = $conn->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, sizes = ?, is_featured = ? WHERE id = ?");
            if ($upd_stmt) {
                $upd_stmt->bind_param("issdsii", $category_id, $name, $description, $price, $sizes, $is_featured, $edit_id);
                $upd_stmt->execute();
                $upd_stmt->close();
                $success_msg = 'Product #' . $edit_id . ' updated successfully!';
            }
        }
    }
}

// 5. Fetch All Products (Direct List)
$products = [];
if ($db_connected) {
    $res = $conn->query("
        SELECT p.*, c.name AS category_name, c.slug AS category_slug 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC
    ");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $products[] = $row;
        }
    }
}

// Product to edit if edit button was clicked
$edit_product = null;
if (isset($_GET['edit']) && (int)$_GET['edit'] > 0 && $db_connected) {
    $e_id = (int)$_GET['edit'];
    $e_stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    if ($e_stmt) {
        $e_stmt->bind_param("i", $e_id);
        $e_stmt->execute();
        $edit_product = $e_stmt->get_result()->fetch_assoc();
        $e_stmt->close();
    }
}

$page_title = "Product Management";
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

<!-- Edit Product Section (Shown only when clicking Edit on a product) -->
<?php if ($edit_product): ?>
  <div class="admin-card" style="border: 2px solid var(--accent); background: #FFFCF5; margin-bottom: 30px;">
    <div class="admin-card-header" style="background: rgba(197, 155, 39, 0.1);">
      <h3 style="color: var(--primary);">&#9998; Edit Product #<?php echo $edit_product['id']; ?>: <?php echo safe($edit_product['name']); ?></h3>
      <a href="<?php echo BASE_URL; ?>/admin/products.php" class="btn btn-outline" style="padding: 4px 12px; font-size: 0.85rem;">
        &times; Cancel Edit
      </a>
    </div>
    <div style="padding: 24px;">
      <form action="<?php echo BASE_URL; ?>/admin/products.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $edit_product['id']; ?>">
        
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Product Name *</label>
            <input type="text" name="name" class="form-control" required value="<?php echo safe($edit_product['name']); ?>">
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Category *</label>
            <select name="category_id" class="form-control" required>
              <?php foreach ($all_categories as $cid => $cat): ?>
                <option value="<?php echo $cid; ?>" <?php echo ($edit_product['category_id'] == $cid) ? 'selected' : ''; ?>>
                  <?php echo safe($cat['name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Price (LKR) *</label>
            <input type="number" step="0.01" name="price" class="form-control" required value="<?php echo (float)$edit_product['price']; ?>">
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Available Sizes (Comma Separated)</label>
            <input type="text" name="sizes" class="form-control" placeholder="e.g. S, M, L, XL or 38, 39, 40" value="<?php echo safe($edit_product['sizes']); ?>">
          </div>
          <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Change Image (Leave blank to keep existing)</label>
            <input type="file" name="product_image" class="form-control" accept="image/*">
            <small style="color: var(--text-muted); font-size: 0.8rem;">Current: <code><?php echo safe($edit_product['image']); ?></code></small>
          </div>
        </div>

        <div class="form-group" style="margin-bottom: 16px;">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?php echo safe($edit_product['description']); ?></textarea>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between;">
          <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.92rem;">
            <input type="checkbox" name="is_featured" value="1" <?php echo ($edit_product['is_featured'] == 1) ? 'checked' : ''; ?>>
            <span>Featured Product on Homepage</span>
          </label>
          <button type="submit" name="edit_product" class="btn btn-primary" style="padding: 10px 24px;">
            Save Product Changes
          </button>
        </div>
      </form>
    </div>
  </div>
<?php endif; ?>

<!-- Add New Product Form -->
<div class="admin-card" id="addNew">
  <div class="admin-card-header">
    <h3>+ Add New Product</h3>
    <small style="color: var(--text-muted);">Fill details to add an item to the catalog</small>
  </div>
  <div style="padding: 24px;">
    <form action="<?php echo BASE_URL; ?>/admin/products.php" method="POST" enctype="multipart/form-data">
      <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Product Name *</label>
          <input type="text" name="name" class="form-control" required placeholder="e.g. Silk Designer Saree">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-control" required>
            <option value="">-- Select Category --</option>
            <?php foreach ($all_categories as $cid => $cat): ?>
              <option value="<?php echo $cid; ?>"><?php echo safe($cat['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Price (LKR Rs.) *</label>
          <input type="number" step="0.01" name="price" class="form-control" required placeholder="e.g. 4500.00">
        </div>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Available Sizes (Comma Separated)</label>
          <input type="text" name="sizes" class="form-control" placeholder="e.g. S, M, L, XL or 38, 39, 40">
        </div>
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Product Photo</label>
          <input type="file" name="product_image" class="form-control" accept="image/*">
        </div>
      </div>

      <div class="form-group" style="margin-bottom: 16px;">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="2" placeholder="Brief product features, material, and care instructions..."></textarea>
      </div>

      <div style="display: flex; align-items: center; justify-content: space-between;">
        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.92rem;">
          <input type="checkbox" name="is_featured" value="1">
          <span>Show in Homepage Featured Section</span>
        </label>
        <button type="submit" name="add_product" class="btn btn-primary" style="padding: 10px 24px;">
          + Add Product
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Products Table (View All Products) -->
<div class="admin-card">
  <div class="admin-card-header">
    <h3>All Store Products (<?php echo count($products); ?>)</h3>
  </div>
  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="width: 60px;">Image</th>
          <th>Product Name</th>
          <th>Category</th>
          <th>Sizes</th>
          <th>Price</th>
          <th>Featured</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No products found in the database.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($products as $p): ?>
            <tr>
              <td>
                <img src="<?php echo BASE_URL . '/' . safe($p['image']); ?>" alt="" class="admin-thumb" onerror="this.src='<?php echo BASE_URL; ?>/images/heels/heels-1.jpg';">
              </td>
              <td>
                <strong style="color: var(--primary); font-size: 0.95rem;"><?php echo safe($p['name']); ?></strong>
                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 2px;">ID: #<?php echo $p['id']; ?></div>
              </td>
              <td>
                <span class="badge" style="background: #F1F5F9; color: var(--primary); border: 1px solid var(--border-color);">
                  <?php echo safe($p['category_name']); ?>
                </span>
              </td>
              <td>
                <small style="color: var(--text-muted);"><?php echo safe($p['sizes'] ?: 'One Size'); ?></small>
              </td>
              <td>
                <strong style="color: var(--primary);"><?php echo format_price($p['price']); ?></strong>
              </td>
              <td>
                <?php if ($p['is_featured']): ?>
                  <span class="badge" style="background: #FEF3C7; color: #92400E;">&#9733; Featured</span>
                <?php else: ?>
                  <span style="color: #9CA3AF; font-size: 0.82rem;">Standard</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <a href="<?php echo BASE_URL; ?>/admin/products.php?edit=<?php echo $p['id']; ?>" class="action-btn action-edit" style="text-decoration: none;">
                  &#9998; Edit
                </a>
                <a href="<?php echo BASE_URL; ?>/admin/products.php?action=delete&id=<?php echo $p['id']; ?>" class="action-btn action-delete" style="text-decoration: none; margin-left: 6px;" onclick="return confirm('Are you sure you want to delete this product: <?php echo addslashes($p['name']); ?>?');">
                  &#128465; Delete
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
