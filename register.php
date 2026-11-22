<?php
session_start();
include 'database.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $address = mysqli_real_escape_string($conn, trim($_POST['address']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password'];
    $role = isset($_POST['role']) && $_POST['role'] === 'donor' ? 'donor' : 'receiver';

    if (empty($name) || empty($address) || empty($phone) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if ($check && mysqli_num_rows($check) > 0) {
            $error = "This email is already registered. Please login.";
        } else {
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, address, phone, password, role) 
                    VALUES ('$name', '$email', '$address', '$phone', '$hashed_pass', '$role')";

            if (mysqli_query($conn, $sql)) {
                $success = "Account created successfully! Please login now.";
            } else {
                $error = "Database error: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account - FoodShare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f7f6; color: #26332b; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .auth-box {
            background: white;
            max-width: 900px;
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            display: grid;
            grid-template-columns: 1fr 1.2fr;
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
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .auth-banner h2 { font-size: 26px; margin-bottom: 10px; }
        .auth-banner p { font-size: 14px; line-height: 1.6; opacity: 0.92; }
        .auth-form { padding: 32px; }
        .auth-form h1 { color: #173c29; font-size: 26px; margin-bottom: 6px; }
        .auth-form p.sub { color: #666; font-size: 14px; margin-bottom: 18px; }
        .form-group { margin-bottom: 13px; }
        .form-group label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #444; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;
        }
        .btn-primary {
            width: 100%; background: #16834a; color: white; border: none; padding: 12px;
            border-radius: 6px; font-weight: bold; font-size: 15px; cursor: pointer; margin-top: 6px;
        }
        .btn-primary:hover { background: #11683a; }
        .msg-error { background: #fdecea; color: #c62828; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: bold; margin-bottom: 14px; }
        .msg-success { background: #e4f5e9; color: #16834a; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: bold; margin-bottom: 14px; }
        .bottom-links { margin-top: 16px; font-size: 14px; text-align: center; }
        .bottom-links a { color: #16834a; font-weight: bold; text-decoration: none; }
        @media (max-width: 768px) { .auth-box { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="auth-box">
    <div class="auth-banner">
        <div>
            <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?w=800" alt="Join FoodShare">
            <h2>Join FoodShare</h2>
            <p>Create an account to request fresh available food near your location or share excess meals with others.</p>
        </div>
        <p style="font-size: 13px; opacity: 0.85;">🌱 Together Against Food Waste</p>
    </div>

    <div class="auth-form">
        <h1>Create an Account</h1>
        <p class="sub">Fill in your details to get started.</p>

        <?php if (!empty($error)): ?>
            <div class="msg-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="msg-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label>Address (ঠিকানা) *</label>
                <textarea name="address" rows="2" placeholder="Enter your area / full address" required></textarea>
            </div>

            <div class="form-group">
                <label>Phone Number *</label>
                <input type="text" name="phone" placeholder="e.g., 017XXXXXXXX" required>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" placeholder="Create a password" required>
            </div>

            <div class="form-group">
                <label>Account Type *</label>
                <select name="role">
                    <option value="receiver">Receiver (Find & Request Food)</option>
                    <option value="donor">Donor (Donate Excess Food)</option>
                </select>
            </div>

            <button type="submit" name="register" class="btn-primary">Create Account</button>
        </form>

        <div class="bottom-links">
            Already have an account? <a href="login.php">Login here</a><br><br>
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>