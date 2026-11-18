<?php
/**
 * Dress Point - Admin Customer Inquiries / Messages
 * Manage messages submitted through the Contact Us form
 * Uses mysqli prepared statements for all operations
 */
require_once __DIR__ . '/../config.php';

$success_msg = '';
$error_msg = '';

// Handle Delete Inquiry
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $msg_id = (int)$_GET['id'];
    if ($msg_id > 0 && $db_connected) {
        $del_stmt = $conn->prepare("DELETE FROM messages WHERE id = ?");
        if ($del_stmt) {
            $del_stmt->bind_param("i", $msg_id);
            if ($del_stmt->execute()) {
                $success_msg = 'Inquiry #' . $msg_id . ' deleted successfully.';
            } else {
                $error_msg = 'Error deleting message: ' . $conn->error;
            }
            $del_stmt->close();
        }
    }
}

// Fetch all messages
$messages = [];
if ($db_connected) {
    $res = $conn->query("SELECT * FROM messages ORDER BY id DESC");
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $messages[] = $row;
        }
    }
}

$page_title = "Customer Inquiries";
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

<div class="admin-card">
  <div class="admin-card-header">
    <h3>Customer Inquiries Inbox</h3>
    <span style="font-size: 0.88rem; color: var(--text-muted); font-weight: 600;">
      Total: <?php echo count($messages); ?> Message(s)
    </span>
  </div>

  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Received Date</th>
          <th>Sender Details</th>
          <th>Subject</th>
          <th>Message</th>
          <th style="text-align: right;">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($messages)): ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
              No messages in your inbox. When customers submit inquiries on the Contact Us page, they will show up here.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($messages as $m): ?>
            <tr>
              <td>#<?php echo $m['id']; ?></td>
              <td style="white-space: nowrap;">
                <?php echo date('M d, Y', strtotime($m['created_at'])); ?><br>
                <small style="color: var(--text-muted);"><?php echo date('h:i A', strtotime($m['created_at'])); ?></small>
              </td>
              <td>
                <strong style="color: var(--primary);"><?php echo safe($m['name']); ?></strong><br>
                <a href="mailto:<?php echo safe($m['email']); ?>" style="color: var(--champagne); font-size: 0.85rem; text-decoration: none;">
                  &#9993; <?php echo safe($m['email']); ?>
                </a><br>
                <?php if (!empty($m['phone'])): ?>
                  <a href="tel:<?php echo safe($m['phone']); ?>" style="color: var(--text-muted); font-size: 0.82rem; text-decoration: none;">
                    &#128222; <?php echo safe($m['phone']); ?>
                  </a>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge" style="background: #FEF3C7; color: #92400E; font-weight: 600;">
                  <?php echo safe($m['subject']); ?>
                </span>
              </td>
              <td style="max-width: 380px; line-height: 1.5; font-size: 0.88rem;">
                <?php echo nl2br(safe($m['message'])); ?>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <a href="<?php echo BASE_URL; ?>/admin/messages.php?action=delete&id=<?php echo $m['id']; ?>" class="action-btn action-delete" onclick="return confirm('Are you sure you want to delete inquiry #<?php echo $m['id']; ?>?');" style="text-decoration: none;">
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

