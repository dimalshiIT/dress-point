<?php
/**
 * Dress Point Basic - Admin Portal Login
 * Secured with password_hash() and password_verify()
 */
require_once __DIR__ . '/../config.php';

// If already logged in, redirect straight to admin dashboard
if (is_admin_logged_in()) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error_msg = '';

// Handle login POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($username) || empty($password)) {
        $error_msg = 'Please enter both your username and password.';
    } elseif (!$db_connected) {
        $error_msg = 'Database not connected. Please import database.sql into phpMyAdmin first.';
    } else {
        // Query admin using mysqli prepared statement
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE username = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($admin = $result->fetch_assoc()) {
                // Verify password against secure bcrypt hash
                if (password_verify($password, $admin['password_hash'])) {
                    // Authentication successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_username'] = $admin['username'];
                    header('Location: ' . BASE_URL . '/admin/index.php');
                    exit;
                } else {
                    $error_msg = 'Incorrect password. Please try again.';
                }
            } else {
                $error_msg = 'Admin user not found.';
            }
            $stmt->close();  //closedatabase dtatement
        } else {
            $error_msg = 'Database query failed: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | <?php echo SITE_NAME; ?></title>
  
  <style>
    /* Scoped styles specifically for the Admin Login Page */
    body.login-page {
      background: linear-gradient(135deg, #4A1523 0%, #2E0E16 100%);
      min-height: 100vh;
      margin: 0;
      padding: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      box-sizing: border-box;
    }

    .login-card {
      background: #FBF5F1;
      width: 100%;
      max-width: 420px;
      border-radius: 16px;
      padding: 44px 36px;
      box-shadow: 0 24px 60px rgba(46, 14, 22, 0.4);
      border: 1px solid rgba(201, 162, 122, 0.35);
      box-sizing: border-box;
    }

    /* Logo & Header in Georgia Serif */
    .login-brand {
      text-align: center;
      margin-bottom: 28px;
    }

    .login-brand h1 {
      font-family: Georgia, 'Times New Roman', serif;
      font-size: 1.75rem;
      font-weight: 700;
      color: #4A1523;
      letter-spacing: 2px;
      margin: 0;
    }

    /* Thin Gold Line under Logo */
    .brand-gold-line {
      width: 48px;
      height: 1.5px;
      background: #C9A27A;
      margin: 10px auto;
    }

    .login-brand p {
      font-size: 0.76rem;
      color: #6E6864;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin: 0;
      font-weight: 600;
    }

    /* Input Fields with Thin Gold Border */
    .login-group {
      margin-bottom: 20px;
    }

    .login-label {
      display: block;
      font-size: 0.86rem;
      font-weight: 600;
      color: #4A1523;
      margin-bottom: 6px;
    }

    .login-input {
      width: 100%;
      padding: 12px 14px;
      border: 1px solid #C9A27A;
      border-radius: 8px;
      background: #FFFFFF;
      color: #2E0E16;
      font-size: 0.95rem;
      box-sizing: border-box;
      transition: all 0.25s ease;
    }

    .login-input:focus {
      outline: none;
      border-color: #C9A27A;
      box-shadow: 0 0 0 3px rgba(201, 162, 122, 0.28);
    }

    /* Luxury Burgundy Button with Gold Text */
    .btn-login {
      width: 100%;
      padding: 13px;
      background: #4A1523;
      color: #C9A27A;
      border: 1px solid #C9A27A;
      border-radius: 8px;
      font-size: 0.92rem;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      cursor: pointer;
      transition: all 0.25s ease;
      margin-top: 6px;
    }

    .btn-login:hover {
      background: #5F1B2D;
      color: #E8D3BC;
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(74, 21, 35, 0.3);
    }

    /* Link below Form */
    .login-back-link {
      display: inline-block;
      color: #4A1523;
      text-decoration: none;
      font-size: 0.84rem;
      transition: color 0.2s ease;
    }

    .login-back-link:hover {
      color: #C9A27A;
    }

    /* Mobile Responsive */
    @media (max-width: 480px) {
      .login-card {
        padding: 34px 22px;
      }
      .login-brand h1 {
        font-size: 1.55rem;
      }
    }
  </style>
</head>
<body class="login-page">

  <div class="login-card">
    
    <!-- Header Brand with Georgia Serif & Thin Gold Line -->
    <div class="login-brand">
      <h1>DRESS POINT</h1>
      <div class="brand-gold-line"></div>
      <p>Administration Portal</p>
    </div>

    <!-- Error Notification if Login Fails -->
    <?php if (!empty($error_msg)): ?>
      <div style="background: #FEE2E2; border-left: 3px solid #DC2626; color: #991B1B; padding: 11px 14px; border-radius: 6px; margin-bottom: 20px; font-size: 0.88rem;">
        &#9888; <?php echo safe($error_msg); ?>
      </div>
    <?php endif; ?>

    <!-- Login Form (Empty fields & autocomplete="off") -->
    <form action="<?php echo BASE_URL; ?>/admin/login.php" method="POST" autocomplete="off">
      <div class="login-group">
        <label class="login-label" for="username">Username</label>
        <input type="text" id="username" name="username" class="login-input" required autofocus placeholder="Enter your username" autocomplete="off">
      </div>

      <div class="login-group" style="margin-bottom: 24px;">
        <label class="login-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="login-input" required placeholder="Enter your password" autocomplete="new-password">
      </div>

      <button type="submit" name="login" class="btn-login">
        Sign In &rarr;
      </button>
    </form>

    <!-- Back to Website Link -->
    <div style="text-align: center; margin-top: 24px;">
      <a href="<?php echo BASE_URL; ?>/index.php" class="login-back-link">
        &larr; Back to website
      </a>
    </div>

  </div>

</body>
</html>

