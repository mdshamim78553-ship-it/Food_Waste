<?php
$page_title = 'Forgot Password';
require_once 'config/db.php';

$msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $new   = $_POST['new'] ?? '';

    $s = $conn->prepare('SELECT user_id FROM users WHERE email=? AND phone=? AND phone <> ""');
    $s->bind_param('ss', $email, $phone);
    $s->execute();
    $u = $s->get_result()->fetch_assoc();

    if (!$u) {
        $err = 'Email and phone number do not match.';
    } elseif (strlen($new) < 6) {
        $err = 'Password must be at least 6 characters.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $up = $conn->prepare('UPDATE users SET password=? WHERE user_id=?');
        $up->bind_param('si', $hash, $u['user_id']);
        $up->execute();
        $msg = 'Password has been reset. You can now <a href="login.php">login</a>.';
    }
}

include 'includes/header.php';
?>
<div class="card form-card">
  <h2>Forgot Password</h2>
  <?php if ($msg): ?><div class="alert alert-success"><?= $msg ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <label>Registered Email</label>
    <input type="email" name="email" required>
    <label>Registered Phone</label>
    <input type="text" name="phone" required>
    <label>New Password</label>
    <input type="password" name="new" required>
    <button class="btn" type="submit">Reset Password</button>
  </form>
  <p style="margin-top:15px;"><a href="login.php">Back to Login</a></p>
</div>
<?php include 'includes/footer.php'; ?>