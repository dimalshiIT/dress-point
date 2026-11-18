<?php
/**
 * Dress Point - Contact Us Page
 * Allows visitors to send inquiries which are saved to the `messages` table
 * for the store administrator to review in the Admin Portal.
 */
require_once __DIR__ . '/config.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $name    = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email   = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone   = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_msg = 'Please complete all required fields (Name, Email, Subject, and Message).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_msg = 'Please provide a valid email address.';
    } elseif (!$db_connected) {
        $error_msg = 'Database is currently offline. Please import database.sql in phpMyAdmin first.';
    } else {
        // Safe prepared statement insert
        $stmt = $conn->prepare("INSERT INTO messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
            if ($stmt->execute()) {
                $success_msg = 'Thank you, ' . safe($name) . '! Your message has been sent successfully. Our team will contact you shortly.';
                // Reset fields
                $name = $email = $phone = $subject = $message = '';
            } else {
                $error_msg = 'Error sending your message: ' . $conn->error;
            }
            $stmt->close();
        } else {
            $error_msg = 'Database statement error: ' . $conn->error;
        }
    }
}

$page_title = "Contact Us";
require_once __DIR__ . '/header.php';
?>

<div class="container section">

  <!-- Breadcrumbs -->
  <div style="font-size: 0.88rem; color: var(--text-muted); margin-bottom: 24px;">
    <a href="<?php echo BASE_URL; ?>/index.php">Home</a> &nbsp;/&nbsp; 
    <b style="color: var(--primary);">Contact Us</b>
  </div>

  <div class="section-header">
    <span class="section-subtitle">We Are Here For You</span>
    <h1 class="section-title">Get in Touch with Dress Point</h1>
    <p class="section-desc">Have a question about an outfit, order delivery, or sizing? Our customer care specialists are happy to help.</p>
  </div>

  <?php if (!empty($success_msg)): ?>
    <div style="background: #F0FDF4; border: 1px solid #BBF7D0; color: #166534; padding: 16px 20px; border-radius: var(--radius); margin-bottom: 24px; font-weight: 500;">
      &#10003; <?php echo $success_msg; ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($error_msg)): ?>
    <div style="background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; padding: 16px 20px; border-radius: var(--radius); margin-bottom: 24px; font-weight: 500;">
      &#9888; <?php echo $error_msg; ?>
    </div>
  <?php endif; ?>

  <div style="display: grid; grid-template-columns: 1fr 1.3fr; gap: 40px; margin-bottom: 50px;">
    
    <!-- Contact Information Column -->
    <div>
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 32px; box-shadow: var(--shadow-sm); height: 100%;">
        <h2 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 20px;">Store Information</h2>
        
        <div style="display: flex; gap: 16px; margin-bottom: 24px;">
          <div style="width: 44px; height: 44px; background: rgba(197, 155, 39, 0.12); color: var(--champagne); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
            &#128205;
          </div>
          <div>
            <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 4px;">Store Location</h4>
            <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5;">
              No. 128, Galle Road, Bambalapitiya,<br>Colombo 03, Western Province, Sri Lanka.
            </p>
          </div>
        </div>

        <div style="display: flex; gap: 16px; margin-bottom: 24px;">
          <div style="width: 44px; height: 44px; background: rgba(197, 155, 39, 0.12); color: var(--champagne); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
            &#128222;
          </div>
          <div>
            <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 4px;">Phone & Hotline</h4>
            <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5;">
              +94 11 234 5678 (Head Office)<br>
              +94 77 123 4567 (WhatsApp / Hotline)
            </p>
          </div>
        </div>

        <div style="display: flex; gap: 16px; margin-bottom: 24px;">
          <div style="width: 44px; height: 44px; background: rgba(197, 155, 39, 0.12); color: var(--champagne); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
            &#9993;
          </div>
          <div>
            <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 4px;">Email Support</h4>
            <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5;">
              info@dresspoint.lk<br>
              support@dresspoint.lk
            </p>
          </div>
        </div>

        <div style="display: flex; gap: 16px;">
          <div style="width: 44px; height: 44px; background: rgba(197, 155, 39, 0.12); color: var(--champagne); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0;">
            &#128340;
          </div>
          <div>
            <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 4px;">Opening Hours</h4>
            <p style="color: var(--text-muted); font-size: 0.92rem; line-height: 1.5;">
              Monday – Saturday: 9:00 AM – 8:30 PM<br>
              Sunday & Public Holidays: 10:00 AM – 6:00 PM
            </p>
          </div>
        </div>

        <div style="margin-top: 30px; padding: 14px 18px; background: #F8FAFC; border-left: 4px solid var(--champagne); border-radius: 4px; font-size: 0.85rem; color: var(--text-muted);">
          <b>Customer Care:</b> We aim to respond to all inquiries within 24 hours on business days.
        </div>

      </div>
    </div>

    <!-- Contact Form Column -->
    <div>
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 32px; box-shadow: var(--shadow-sm);">
        <h2 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 10px;">Send Us a Message</h2>
        <p style="color: var(--text-muted); font-size: 0.92rem; margin-bottom: 24px;">
          Fill out the form below and we will get back to you within 24 hours.
        </p>

        <form action="<?php echo BASE_URL; ?>/contact.php" method="POST">
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="c_name">Your Full Name <span style="color:#DC2626;">*</span></label>
              <input type="text" id="c_name" name="name" class="form-control" required placeholder="e.g. Kasun Perera" value="<?php echo isset($name) ? safe($name) : ''; ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="c_email">Email Address <span style="color:#DC2626;">*</span></label>
              <input type="email" id="c_email" name="email" class="form-control" required placeholder="e.g. kasun@example.com" value="<?php echo isset($email) ? safe($email) : ''; ?>">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="c_phone">Phone Number (Optional)</label>
              <input type="text" id="c_phone" name="phone" class="form-control" placeholder="e.g. 077 123 4567" value="<?php echo isset($phone) ? safe($phone) : ''; ?>">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
              <label class="form-label" for="c_subject">Inquiry Subject <span style="color:#DC2626;">*</span></label>
              <input type="text" id="c_subject" name="subject" class="form-control" required placeholder="e.g. Question about Saree delivery" value="<?php echo isset($subject) ? safe($subject) : ''; ?>">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label" for="c_msg">Your Message <span style="color:#DC2626;">*</span></label>
            <textarea id="c_msg" name="message" class="form-control" rows="5" required placeholder="Please write your message or question here..."><?php echo isset($message) ? safe($message) : ''; ?></textarea>
          </div>

          <button type="submit" name="send_message" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem;">
            &#128233; Send Inquiry
          </button>
        </form>
      </div>
    </div>

  </div>

  <!-- Frequently Asked Questions Accordion/Grid -->
  <div style="margin-top: 50px;">
    <div class="section-header">
      <span class="section-subtitle">Help Center</span>
      <h2 class="section-title">Frequently Asked Questions</h2>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px;">
      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 20px;">
        <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 8px;">&#10004; How long does delivery take?</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
          Orders within Colombo and Western Province are delivered within 1 to 2 business days. Outstation deliveries take 2 to 4 business days.
        </p>
      </div>

      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 20px;">
        <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 8px;">&#10004; Can I pay with Cash on Delivery?</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
          Yes! Cash on Delivery (COD) is available for all addresses across Sri Lanka. You can inspect your package and pay the courier directly.
        </p>
      </div>

      <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius); padding: 20px;">
        <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 8px;">&#10004; What is the return or exchange policy?</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">
          We accept exchanges within 7 days of delivery for unworn items with tags intact. Contact customer care to arrange an exchange.
        </p>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>

