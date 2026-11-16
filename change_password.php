<?php
$page_title = 'Change Password';
require_once 'config/db.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }

$msg = $err = '';
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = $_POST['old'] ?? '';
    $new = $_POST['new'] ?? '';
    $cf  = $_POST['confirm'] ?? '';

    $s = $conn->prepare('SELECT password FROM users WHERE user_id=?');
    $s->bind_param('i', $uid);
    $s->execute();
    $row = $s->get_result()->fetch_assoc();

    if (!password_verify($old, $row['password'])) {
        $err = 'Current password is incorrect.';
    } elseif (strlen($new) < 6) {
        $err = 'New password must be at least 6 characters.';
    } elseif ($new !== $cf) {
        $err = 'New passwords do not match.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $u = $conn->prepare('UPDATE users SET password=? WHERE user_id=?');
        $u->bind_param('si', $hash, $uid);
        $msg = $u->execute() ? 'Password changed successfully.' : 'Something went wrong.';
    }
}

include 'includes/header.php';
?>
<div class="card form-card">
  <h2>Change Password</h2>
  <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <label>Current Password</label>
    <input type="password" name="old" required>
    <label>New Password</label>
    <input type="password" name="new" required>
    <label>Confirm New Password</label>
    <input type="password" name="confirm" required>
    <button class="btn" type="submit">Change Password</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>