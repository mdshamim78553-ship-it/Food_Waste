<?php
require_once 'config/db.php';

if (is_logged_in()) redirect_by_role();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $role     = 'donor';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';
    $agree    = isset($_POST['agree']);

    if ($name === '' || $email === '' || $phone === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!$agree) {
        $error = 'You must agree to the Terms of Service.';
    } else {
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare('INSERT INTO users (name, email, password, phone, role) VALUES (?,?,?,?,?)');
            $ins->bind_param('sssss', $name, $email, $hash, $phone, $role);
            if ($ins->execute()) {
                $success = 'Registration successful! <a href="' . BASE_URL . 'login.php">Please login</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register | FWMS</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/auth.css">
</head>
<body class="auth-body">
  <div class="auth-box">
    <h2>Register Now</h2>

    <?php if ($error): ?><div class="auth-error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="auth-success"><?= $success ?></div><?php endif; ?>

    <form method="post">
      <input type="text" name="name" placeholder="Name" value="<?= e($_POST['name'] ?? '') ?>" required>
      <input type="email" name="email" placeholder="E-mail" value="<?= e($_POST['email'] ?? '') ?>" required>
      <input type="text" name="phone" placeholder="Phone" value="<?= e($_POST['phone'] ?? '') ?>" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm" placeholder="Repeat Password" required>

      <label class="auth-check">
        <input type="checkbox" name="agree" required>
        <span>I agree to the Terms of Service and Privacy Policy</span>
      </label>

      <div class="auth-btn-wrap">
        <button class="auth-btn" type="submit">Submit</button>
      </div>
    </form>

    <div class="auth-links">
      Already Registered. <a href="<?= BASE_URL ?>login.php">Login</a>
      <a class="home" href="<?= BASE_URL ?>index.php">🏠 Home Page</a>
    </div>
  </div>
</body>
</html>