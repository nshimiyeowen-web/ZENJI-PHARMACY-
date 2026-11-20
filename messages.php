<?php
require 'includes/auth.php';
require 'includes/connect.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $id = (int)($_POST['id'] ?? 0);

    if (isset($_POST['read']) && $id > 0) {
        $stmt = mysqli_prepare($conn, 'UPDATE messages SET is_read = 1 WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    if (isset($_POST['delete']) && $id > 0) {
        $stmt = mysqli_prepare($conn, 'DELETE FROM messages WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    
    flash('Message updated.'); 
    header('Location: messages.php'); 
    exit;
}

$result = mysqli_query($conn, 'SELECT * FROM messages ORDER BY created_at DESC');
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$title = 'Messages';
require 'includes/app_header.php';
flash();
?>
<h2>Website enquiries</h2>
<table>
  <tr><th>Date</th><th>From</th><th>Subject</th><th>Message</th><th>Status</th><th>Actions</th></tr>
  <?php foreach ($rows as $r): ?>
  <tr>
    <td><?= e($r['created_at']) ?></td>
    <td><?= e($r['name']) ?><br><small><?= e($r['email']) ?> <?= e($r['phone']) ?></small></td>
    <td><?= e($r['subject']) ?></td>
    <td><?= nl2br(e($r['message'])) ?></td>
    <td><?= $r['is_read'] ? 'Read' : '<b>New</b>' ?></td>
    <td>
      <form method="post" style="display:inline">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= $r['id'] ?>">
        <?php if (!$r['is_read']): ?><button class="btn secondary" name="read">Mark read</button><?php endif; ?>
      </form>
      <form method="post" class="confirm-delete" style="display:inline">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
        <input type="hidden" name="id" value="<?= $r['id'] ?>">
        <button class="btn danger" name="delete">Delete</button>
      </form>
    </td>
  </tr>
  <?php endforeach; if (!$rows) echo '<tr><td colspan="6">No messages yet.</td></tr>'; ?>
</table>
<?php require 'includes/app_footer.php'; ?>