<?php
require_once 'config/db.php';

if (is_logged_in()) redirect_by_role();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT user_id, name, password, role FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];
        redirect_by_role();
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign In | FWMS</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/auth.css">
</head>
<body class="auth-body">
  <div class="auth-box">
    <h2>Sign In Now</h2>

    <?php if ($error): ?><div class="auth-error"><?= e($error) ?></div><?php endif; ?>

    <form method="post">
      <input type="email" name="email" placeholder="E-mail" value="<?= e($_POST['email'] ?? '') ?>" required>
      <input type="password" name="password" placeholder="Password" required>

      <a class="forgot" href="<?= BASE_URL ?>forgot_password.php">Forgot Password?</a>

      <div class="auth-btn-wrap">
        <button class="auth-btn" type="submit">Sign In</button>
      </div>
    </form>

    <div class="auth-links">
      Don't Have an Account ? <a href="<?= BASE_URL ?>register.php">Create an account</a>
      <a class="home" href="<?= BASE_URL ?>index.php">🏠 Home Page</a>
    </div>
  </div>
</body>
</html>