<?php
session_start();
include 'database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $sql = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // hashed অথবা plain password দুটোই সাপোর্ট রাখা হলো
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // এখানে Admin, Donor ও Receiver অনুযায়ী রিডাইরেক্ট ঠিক করা হয়েছে
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif ($user['role'] === 'donor') {
                    header("Location: dashboard.php");
                } else {
                    header("Location: available_food.php");
                }
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "No account found with this email. Please create an account.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodShare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f7f6; color: #26332b; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .auth-box {
            background: white;
            max-width: 860px;
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            display: grid;
            grid-template-columns: 1fr 1.1fr;
        }
        .auth-banner {
            background: linear-gradient(160deg, #16834a, #0e5430);
            color: white;
            padding: 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .auth-banner img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .auth-banner h2 { font-size: 25px; margin-bottom: 10px; }
        .auth-banner p { font-size: 14px; line-height: 1.6; opacity: 0.92; }
        .auth-form { padding: 36px 32px; display: flex; flex-direction: column; justify-content: center; }
        .auth-form h1 { color: #173c29; font-size: 28px; margin-bottom: 6px; }
        .auth-form p.sub { color: #666; font-size: 14px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 14px; font-weight: bold; margin-bottom: 6px; color: #444; }
        .form-group input {
            width: 100%; padding: 11px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;
        }
        .btn-primary {
            width: 100%; background: #16834a; color: white; border: none; padding: 12px;
            border-radius: 6px; font-weight: bold; font-size: 15px; cursor: pointer; margin-top: 6px;
        }
        .btn-primary:hover { background: #11683a; }
        .msg-error { background: #fdecea; color: #c62828; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: bold; margin-bottom: 15px; }
        .bottom-links { margin-top: 18px; font-size: 14px; text-align: center; line-height: 1.8; }
        .bottom-links a { color: #16834a; font-weight: bold; text-decoration: none; }
        @media (max-width: 768px) { .auth-box { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="auth-box">
    <div class="auth-banner">
        <div>
            <img src="https://images.unsplash.com/photo-1547592180-85f173990554?w=800" alt="FoodShare Login">
            <h2>Welcome Back!</h2>
            <p>Login to browse available meals, check your request history, or share surplus food with your community.</p>
        </div>
        <p style="font-size: 13px; opacity: 0.85;">🍱 Fresh Food • Zero Waste</p>
    </div>

    <div class="auth-form">
        <h1>Login</h1>
        <p class="sub">Enter your email address and password.</p>

        <?php if (!empty($error)): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" name="login" class="btn-primary">Login</button>
        </form>

        <div class="bottom-links">
            Don't have an account? <a href="register.php">Create an Account</a><br>
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>