<?php
session_start();
include 'database.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_contact'])) {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $sql = "INSERT INTO contact_messages (name, email, subject, message)
                VALUES ('$name', '$email', '$subject', '$message')";

        if (mysqli_query($conn, $sql)) {
            $success = "Thank you for reaching out! Your message has been sent to our team.";
        } else {
            $error = "Database error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FoodShare</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f8faf8;
            color: #26332b;
        }

        /* Navigation */
        nav {
            background: #ffffff;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo {
            font-size: 25px;
            font-weight: bold;
            color: #16834a;
        }
        .logo span {
            color: #f39c12;
        }
        nav ul {
            display: flex;
            list-style: none;
            gap: 26px;
            align-items: center;
        }
        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
        }
        nav ul li a:hover,
        nav ul li a.active {
            color: #16834a;
        }
        .login-btn {
            background: #16834a;
            color: white !important;
            padding: 9px 18px;
            border-radius: 6px;
        }
        .logout-btn {
            background: #c62828;
            color: white !important;
            padding: 9px 16px;
            border-radius: 6px;
        }

        /* Header Banner */
        .page-banner {
            background: linear-gradient(110deg, #e4f5e9, #f8f9ed);
            padding: 50px 8%;
            text-align: center;
        }
        .page-banner h1 {
            font-size: 38px;
            color: #173c29;
            margin-bottom: 10px;
        }
        .page-banner h1 span {
            color: #16834a;
        }
        .page-banner p {
            color: #555;
            max-width: 620px;
            margin: 0 auto;
            line-height: 1.7;
            font-size: 16px;
        }

        /* Main Contact Grid */
        .contact-section {
            max-width: 1120px;
            margin: 45px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1.15fr;
            gap: 30px;
            align-items: start;
        }

        .info-card, .form-card {
            background: white;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        }
        .info-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .info-card h2 {
            color: #173c29;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .info-card p.lead {
            color: #666;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .info-item {
            margin-bottom: 14px;
            padding-bottom: 12px;
            border-bottom: 1px solid #eef3f0;
            font-size: 14px;
            color: #333;
        }
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .info-item strong {
            display: block;
            color: #16834a;
            margin-bottom: 3px;
        }

        .form-card h2 {
            color: #173c29;
            font-size: 24px;
            margin-bottom: 8px;
        }
        .form-card p.sub {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            font-size: 14px;
            color: #3a473f;
            margin-bottom: 6px;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1px solid #cfd8d3;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #16834a;
        }
        .btn-send {
            background: #16834a;
            color: white;
            border: none;
            padding: 13px 24px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        .btn-send:hover {
            background: #11683a;
        }

        .alert-success {
            background: #e4f5e9;
            color: #16834a;
            border-left: 4px solid #16834a;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: bold;
        }
        .alert-error {
            background: #fdecea;
            color: #c62828;
            border-left: 4px solid #c62828;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
            font-weight: bold;
        }

        /* Footer */
        footer {
            background: #142c20;
            color: white;
            padding: 35px 8% 20px;
            text-align: center;
            margin-top: 40px;
        }
        footer a {
            color: #b8e8c9;
            text-decoration: none;
            margin: 0 10px;
        }
        .copyright {
            border-top: 1px solid #496052;
            padding-top: 18px;
            margin-top: 20px;
            color: #ccc;
            font-size: 14px;
        }

        @media (max-width: 850px) {
            nav { flex-direction: column; gap: 14px; }
            nav ul { flex-wrap: wrap; justify-content: center; gap: 15px; }
            .contact-section { grid-template-columns: 1fr; }
            .page-banner h1 { font-size: 30px; }
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">Food<span>Share.</span></div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="available_food.php">Available Food</a></li>
        <li><a href="contact.php" class="active">Contact Us</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php
            $dash_link = 'available_food.php';
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                $dash_link = 'admin_dashboard.php';
            } elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'donor') {
                $dash_link = 'dashboard.php';
            }
            ?>
            <li><a href="<?php echo $dash_link; ?>">Dashboard</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="page-banner">
    <h1>Get in Touch With <span>FoodShare</span></h1>
    <p>Have questions about donating food, requesting meals, or coordinating pickups? Send us a message anytime.</p>
</section>

<section class="contact-section">
    <div class="info-card">
        <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?w=800" alt="Community support and contact">
        <h2>Contact Information</h2>
        <p class="lead">We are here to help donors, receivers, and volunteers coordinate smoothly and reduce food waste.</p>

        <div class="info-item">
            <strong>📍 Location</strong>
            Dhaka, Bangladesh
        </div>
        <div class="info-item">
            <strong>📧 Email Support</strong>
            support@foodshare.org
        </div>
        <div class="info-item">
            <strong>📞 Helpline</strong>
            +880 1700-000000
        </div>
        <div class="info-item">
            <strong>🕒 Support Hours</strong>
            Saturday – Thursday, 9:00 AM – 8:00 PM
        </div>
    </div>

    <div class="form-card">
        <h2>Send Us a Message</h2>
        <p class="sub">Fill out the form below and your message will be saved for review.</p>

        <?php if (!empty($success)): ?>
            <div class="alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Your Name *</label>
                <input type="text" name="name" placeholder="Enter your full name" value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" placeholder="Enter your email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Subject *</label>
                <input type="text" name="subject" placeholder="e.g., Food donation help, Request issue, Partnership" required>
            </div>

            <div class="form-group">
                <label>Message *</label>
                <textarea name="message" rows="5" placeholder="Write your message here..." required></textarea>
            </div>

            <button type="submit" name="send_contact" class="btn-send">Send Message</button>
        </form>
    </div>
</section>

<footer>
    <h2>FoodShare</h2>
    <p style="margin: 10px 0 14px;">Reducing Food Waste, Sharing Happiness.</p>
    <div>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="available_food.php">Available Food</a>
        <a href="contact.php">Contact</a>
    </div>
    <div class="copyright">
        &copy; 2026 FoodShare. All Rights Reserved.
    </div>
</footer>

</body>
</html>