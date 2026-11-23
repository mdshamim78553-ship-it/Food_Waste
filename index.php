<?php
// Home page of Food Waste Management System
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodShare - Food Waste Management</title>

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
            gap: 28px;
            align-items: center;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            font-weight: 600;
        }

        nav ul li a:hover {
            color: #16834a;
        }

        .login-btn {
            background: #16834a;
            color: white !important;
            padding: 10px 20px;
            border-radius: 6px;
        }

        .logout-btn {
            background: #c62828;
            color: white !important;
            padding: 10px 18px;
            border-radius: 6px;
        }

        /* Hero Section */
        .hero {
            min-height: 530px;
            display: flex;
            align-items: center;
            padding: 70px 8%;
            background: linear-gradient(110deg, #e4f5e9, #f8f9ed);
            gap: 40px;
        }

        .hero-content {
            flex: 1;
        }

        .hero-content h1 {
            font-size: 48px;
            line-height: 1.25;
            margin-bottom: 20px;
            color: #173c29;
        }

        .hero-content h1 span {
            color: #16834a;
        }

        .hero-content p {
            font-size: 17px;
            line-height: 1.8;
            color: #555;
            margin-bottom: 30px;
        }

        .hero-buttons a {
            display: inline-block;
            padding: 14px 24px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .primary-btn {
            background: #16834a;
            color: white;
        }

        .secondary-btn {
            border: 2px solid #16834a;
            color: #16834a;
        }

        .hero-image {
            flex: 1;
            text-align: center;
        }

        .hero-image img {
            width: 100%;
            max-width: 520px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }

        /* Section */
        .section {
            padding: 70px 8%;
            text-align: center;
        }

        .section h2 {
            font-size: 32px;
            margin-bottom: 15px;
            color: #173c29;
        }

        .section-description {
            color: #666;
            margin-bottom: 40px;
            line-height: 1.7;
        }

        /* Features */
        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .feature-card {
            background: white;
            padding: 35px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }

        .feature-card .icon {
            font-size: 42px;
            margin-bottom: 15px;
        }

        .feature-card h3 {
            color: #16834a;
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.7;
        }

        /* Food Section */
        .food-section {
            background: #eef7f0;
        }

        .food-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .food-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            text-align: left;
        }

        .food-card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
        }

        .food-info {
            padding: 22px;
        }

        .food-info h3 {
            margin-bottom: 10px;
            color: #173c29;
        }

        .food-info p {
            color: #666;
            margin-bottom: 10px;
        }

        .food-info a {
            display: inline-block;
            background: #16834a;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 8px;
        }

        /* Call to Action */
        .cta {
            background: #16834a;
            color: white;
            text-align: center;
            padding: 65px 20px;
        }

        .cta h2 {
            font-size: 32px;
            margin-bottom: 15px;
        }

        .cta p {
            margin-bottom: 25px;
            line-height: 1.7;
        }

        .cta a {
            background: white;
            color: #16834a;
            text-decoration: none;
            padding: 13px 25px;
            border-radius: 6px;
            font-weight: bold;
        }

        /* Footer */
        footer {
            background: #142c20;
            color: white;
            padding: 40px 8% 20px;
            text-align: center;
        }

        footer p {
            margin-bottom: 15px;
            line-height: 1.7;
        }

        footer a {
            color: #b8e8c9;
            text-decoration: none;
            margin: 0 10px;
        }

        .copyright {
            border-top: 1px solid #496052;
            padding-top: 20px;
            margin-top: 25px;
            color: #ccc;
        }

        /* Responsive */
        @media(max-width: 850px) {
            nav {
                flex-direction: column;
                gap: 18px;
            }

            nav ul {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .hero {
                flex-direction: column;
                text-align: center;
            }

            .hero-content h1 {
                font-size: 36px;
            }

            .features,
            .food-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width: 550px) {
            .features,
            .food-cards {
                grid-template-columns: 1fr;
            }

            .hero-content h1 {
                font-size: 30px;
            }
        }
    </style>
</head>

<body>

<!-- Navigation Bar -->
<nav>
    <div class="logo">Food<span>Share.</span></div>

    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="available_food.php">Available Food</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'donor') ? 'dashboard.php' : 'available_food.php'; ?>">Dashboard</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Hero Section -->
<section class="hero">

    <div class="hero-content">
        <h1>
            Share Food,<br>
            <span>Spread Happiness.</span>
        </h1>

        <p>
            Don't waste food when someone needs it.
            Our Food Waste Management System connects
            food donors with people who need food.
            Together, we can reduce food waste and
            make a better world.
        </p>

        <div class="hero-buttons">
            <!-- লগইন না থাকলে আগে login.php-তে যাবে, লগইন থাকলে available_food.php-তে যাবে -->
            <a href="<?php echo isset($_SESSION['user_id']) ? 'available_food.php' : 'login.php'; ?>" class="primary-btn">Find Food</a>

            <!-- লগইন না থাকলে আগে login.php-তে যাবে, লগইন থাকলে ড্যাশবোর্ড/ডোনেট পেজে যাবে -->
            <a href="<?php echo isset($_SESSION['user_id']) ? ((isset($_SESSION['role']) && $_SESSION['role'] === 'donor') ? 'dashboard.php' : 'donate_food.php') : 'login.php'; ?>" class="secondary-btn">
                Become a Donor
            </a>
        </div>
    </div>

    <div class="hero-image">
        <img
            src="https://images.unsplash.com/photo-1547592180-85f173990554?w=900"
            alt="Healthy food donation">
    </div>

</section>

<!-- About Section -->
<section class="section">

    <h2>Why FoodShare?</h2>

    <p class="section-description">
        Our goal is to reduce food waste and help people
        by connecting food donors and food receivers.
    </p>

    <div class="features">

        <div class="feature-card">
            <div class="icon">🍱</div>
            <h3>Donate Food</h3>
            <p>
                Donors can share extra food with people
                who need it.
            </p>
        </div>

        <div class="feature-card">
            <div class="icon">🤝</div>
            <h3>Request Food</h3>
            <p>
                Users can find available donated food
                and send food requests.
            </p>
        </div>

        <div class="feature-card">
            <div class="icon">🌍</div>
            <h3>Reduce Food Waste</h3>
            <p>
                Together, we can reduce food waste
                and build a better society.
            </p>
        </div>

    </div>

</section>

<!-- Available Food Section -->
<section class="section food-section" id="food">

    <h2>Explore Available Food</h2>

    <p class="section-description">
        Find food shared by our generous donors.
    </p>

    <div class="food-cards">

        <div class="food-card">
            <img
                src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=700"
                alt="Fresh vegetable food">

            <div class="food-info">
                <h3>Fresh Vegetables</h3>
                <p>Healthy and fresh food donation.</p>
                <p><strong>Status:</strong> Sample</p>
                <a href="login.php">Request Food</a>
            </div>
        </div>

        <div class="food-card">
            <img
                src="https://images.unsplash.com/photo-1547592180-85f173990554?w=700"
                alt="Healthy meal">

            <div class="food-info">
                <h3>Healthy Meal</h3>
                <p>Food shared by generous donors.</p>
                <p><strong>Status:</strong> Sample</p>
                <a href="login.php">Request Food</a>
            </div>
        </div>

        <div class="food-card">
            <img
                src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=700"
                alt="Fresh food">

            <div class="food-info">
                <h3>Fresh Food Items</h3>
                <p>Help reduce food waste by sharing.</p>
                <p><strong>Status:</strong> Sample</p>
                <a href="login.php">Request Food</a>
            </div>
        </div>

    </div>

</section>

<!-- Call to Action -->
<section class="cta">

    <h2>Have Extra Food?</h2>

    <p>
        Don't throw it away. Share your food with
        someone who needs it. Your small contribution
        can make a big difference.
    </p>

    <a href="register.php">Join Us Today</a>

</section>

<!-- Footer -->
<footer>

    <h2>FoodShare</h2>

    <p>
        Reducing Food Waste, Sharing Happiness.
    </p>

    <div>
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <a href="login.php">Login</a>
    </div>

    <div class="copyright">
        <p>
            &copy; 2026 FoodShare.
            All Rights Reserved.
        </p>
    </div>

</footer>

</body>
</html>