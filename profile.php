<?php
$page_title = 'My Profile';
require_once 'config/db.php';
if (!is_logged_in()) { header('Location: login.php'); exit; }

$msg = $err = '';
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    if ($name === '') {
        $err = 'Name is required.';
    } else {
        $s = $conn->prepare('UPDATE users SET name=?, phone=?, address=? WHERE user_id=?');
        $s->bind_param('sssi', $name, $phone, $address, $uid);
        if ($s->execute()) { $_SESSION['name'] = $name; $msg = 'Profile updated successfully.'; }
        else $err = 'Could not update profile.';
    }
}

$s = $conn->prepare('SELECT * FROM users WHERE user_id=?');
$s->bind_param('i', $uid);
$s->execute();
$u = $s->get_result()->fetch_assoc();

include 'includes/header.php';
?>
<div class="card form-card">
  <h2>My Profile</h2>
  <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <label>Email (cannot be changed)</label>
    <input type="text" value="<?= e($u['email']) ?>" disabled>
    <label>Name</label>
    <input type="text" name="name" value="<?= e($u['name']) ?>" required>
    <label>Phone</label>
    <input type="text" name="phone" value="<?= e($u['phone']) ?>">
    <label>Address</label>
    <input type="text" name="address" value="<?= e($u['address']) ?>">
    <button class="btn" type="submit">Update Profile</button>
  </form>
  <p style="margin-top:15px;"><a href="change_password.php">Change Password</a></p>
</div>
<?php include 'includes/footer.php'; ?>