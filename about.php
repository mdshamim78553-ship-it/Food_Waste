<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - FoodShare</title>
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

        /* Hero / Intro */
        .about-hero {
            display: flex;
            align-items: center;
            gap: 40px;
            padding: 60px 8%;
            background: linear-gradient(110deg, #e4f5e9, #f8f9ed);
        }
        .about-hero-text {
            flex: 1.1;
        }
        .about-hero-text h1 {
            font-size: 40px;
            color: #173c29;
            margin-bottom: 16px;
            line-height: 1.25;
        }
        .about-hero-text h1 span {
            color: #16834a;
        }
        .about-hero-text p {
            font-size: 16px;
            color: #555;
            line-height: 1.8;
            margin-bottom: 14px;
        }
        .about-hero-img {
            flex: 1;
            text-align: center;
        }
        .about-hero-img img {
            width: 100%;
            max-width: 480px;
            height: 320px;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(0,0,0,0.12);
        }

        /* Mission & Vision */
        .section {
            padding: 65px 8%;
        }
        .section-title {
            text-align: center;
            font-size: 32px;
            color: #173c29;
            margin-bottom: 12px;
        }
        .section-sub {
            text-align: center;
            color: #666;
            max-width: 650px;
            margin: 0 auto 40px;
            line-height: 1.7;
        }
        .mv-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 28px;
        }
        .mv-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 16px rgba(0,0,0,0.07);
        }
        .mv-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .mv-card-body {
            padding: 24px;
        }
        .mv-card-body h3 {
            color: #16834a;
            font-size: 22px;
            margin-bottom: 10px;
        }
        .mv-card-body p {
            color: #555;
            line-height: 1.7;
            font-size: 15px;
        }

        /* How It Works */
        .steps-section {
            background: #eef7f0;
        }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .step-card {
            background: white;
            padding: 28px 22px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border-top: 4px solid #16834a;
        }
        .step-num {
            display: inline-block;
            background: #16834a;
            color: white;
            width: 34px;
            height: 34px;
            line-height: 34px;
            border-radius: 50%;
            font-weight: bold;
            margin-bottom: 14px;
        }
        .step-card h3 {
            color: #173c29;
            margin-bottom: 10px;
        }
        .step-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Impact & CTA */
        .impact-box {
            display: flex;
            align-items: center;
            gap: 35px;
            background: white;
            padding: 32px;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(0,0,0,0.06);
        }
        .impact-box img {
            width: 340px;
            max-width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
        }
        .impact-text h2 {
            color: #173c29;
            font-size: 28px;
            margin-bottom: 12px;
        }
        .impact-text p {
            color: #555;
            line-height: 1.7;
            margin-bottom: 20px;
        }
        .btn-group a {
            display: inline-block;
            padding: 11px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 10px;
        }
        .btn-green {
            background: #16834a;
            color: white;
        }
        .btn-outline {
            border: 2px solid #16834a;
            color: #16834a;
        }

        /* Footer */
        footer {
            background: #142c20;
            color: white;
            padding: 35px 8% 20px;
            text-align: center;
            margin-top: 30px;
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
            .about-hero, .impact-box { flex-direction: column; text-align: center; }
            .mv-grid, .steps-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">Food<span>Share.</span></div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php" class="active">About Us</a></li>
        <li><a href="available_food.php">Available Food</a></li>
        <li><a href="contact.php">Contact Us</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="logout.php" class="login-btn">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php" class="login-btn">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section class="about-hero">
    <div class="about-hero-text">
        <h1>Connecting Surplus Food With <span>People in Need</span></h1>
        <p>
            Every day, large amounts of good, safe food go to waste while many individuals and families struggle to get a proper meal. FoodShare bridges that gap by making food sharing simple, fast, and community-driven.
        </p>
        <p>
            Whether you are a household, event organizer, or local restaurant with extra food—or someone looking for fresh meals—our platform helps turn excess into care.
        </p>
    </div>
    <div class="about-hero-img">
        <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?w=800" alt="Community food sharing">
    </div>
</section>

<section class="section">
    <h2 class="section-title">Our Mission & Vision</h2>
    <p class="section-sub">Driven by care for people and respect for resources.</p>

    <div class="mv-grid">
        <div class="mv-card">
            <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=800" alt="Fresh food mission">
            <div class="mv-card-body">
                <h3>Our Mission</h3>
                <p>
                    To reduce everyday food waste by providing a reliable digital space where donors can quickly list surplus food and receivers can discover and request available meals nearby.
                </p>
            </div>
        </div>

        <div class="mv-card">
            <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=800" alt="Sustainable food vision">
            <div class="mv-card-