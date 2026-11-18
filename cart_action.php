<?php
/**
 * Dress Point - Shopping Cart Action Handler
 * Handles adding, quantity updating, and removing items in the PHP Session
 */
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_POST['action']) ? trim($_POST['action']) : (isset($_GET['action']) ? trim($_GET['action']) : '');

// Detect if request expects JSON
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' 
           || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
           || isset($_POST['action']);

// 1. ADD ITEM TO CART
if ($action === 'add') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $name       = isset($_POST['name']) ? trim($_POST['name']) : '';
    $price      = isset($_POST['price']) ? (float)$_POST['price'] : 0.0;
    $size       = isset($_POST['size']) ? trim($_POST['size']) : 'Standard';
    $image      = isset($_POST['image']) ? trim($_POST['image']) : '';
    $quantity   = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

    if ($product_id > 0 && $name !== '' && $price > 0) {
        $item_key = $product_id . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $size);

        if (isset($_SESSION['cart'][$item_key])) {
            $_SESSION['cart'][$item_key]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$item_key] = [
                'product_id' => $product_id,
                'name'       => $name,
                'price'      => $price,
                'size'       => $size,
                'image'      => $image,
                'quantity'   => $quantity
            ];
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'    => true,
                'message'    => 'Item added to bag',
                'cart_count' => get_cart_count()
            ]);
            exit;
        }
    }

    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

// 2. UPDATE QUANTITY
if ($action === 'update') {
    $key = isset($_POST['item_key']) ? trim($_POST['item_key']) : '';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($key !== '' && isset($_SESSION['cart'][$key])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$key]);
        } else {
            $_SESSION['cart'][$key]['quantity'] = $quantity;
        }
    }

    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

// 3. REMOVE SINGLE ITEM
if ($action === 'remove') {
    $key = isset($_GET['key']) ? trim($_GET['key']) : (isset($_POST['item_key']) ? trim($_POST['item_key']) : '');
    if ($key !== '' && isset($_SESSION['cart'][$key])) {
        unset($_SESSION['cart'][$key]);
    }

    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

// 4. CLEAR ALL
if ($action === 'clear') {
    $_SESSION['cart'] = [];
    header('Location: ' . BASE_URL . '/cart.php');
    exit;
}

header('Location: ' . BASE_URL . '/cart.php');
exit;
?>

