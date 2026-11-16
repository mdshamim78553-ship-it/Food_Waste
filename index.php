<?php
require_once 'config/db.php';

// Donor / Admin link: login thakle dashboard, na thakle login page
$role = $_SESSION['role'] ?? '';
$donor_link = ($role === 'donor') ? BASE_URL . 'donor/dashboard.php' : BASE_URL . 'login.php';
$admin_link = ($role === 'admin') ? BASE_URL . 'admin/dashboard.php' : BASE_URL . 'login.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FWMS | Food Waste Management System</title>
  <link href="https://fonts.googleapis.com/css2?family=Noto+Serif:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/home.css">
</head>
<body>

<!-- Navbar -->
<nav class="home-nav">
  <a class="active" href="<?= BASE_URL ?>index.php">Home</a>
  <a href="<?= BASE_URL ?>about.php">About</a>
  <a href="<?= BASE_URL ?>food_list.php">Available Food List</a>
  <a href="<?= BASE_URL ?>contact.php">Contact</a>
  <a href="<?= $donor_link ?>">Donor</a>
  <a href="<?= $admin_link ?>">Admin</a>
  <?php if (is_logged_in()): ?>
    <a href="<?= BASE_URL ?>logout.php">Logout</a>
  <?php endif; ?>
</nav>

<!-- Ribbon logo -->
<div class="logo-ribbon">
  <div class="orn">❦</div>
  <div class="txt">FWMS</div>
  <div class="orn">❦</div>
</div>

<!-- Hero -->
<section class="hero-full">
  <div class="support-box">
    <h2>We Need Your Support</h2>
    <p>Every day a lot of good food is thrown away while many people go hungry.
       Join us as a donor or receiver and help us deliver extra food to those who need it most.</p>
    <a class="read-more" href="<?= BASE_URL ?>register.php">Read More</a>
  </div>
</section>
<!-- Our Mission -->
<section class="mission">
  <h2 class="mission-title">Our Mission</h2>
  <div class="mission-wrap">
    <div class="mission-img"></div>

    <div class="mission-list">
      <div class="m-item">
        <div class="m-ico">
          <svg viewBox="0 0 64 64" fill="none" stroke="#26c6da" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 32C6 18 18 8 32 8s26 10 26 24H6z"/>
            <path d="M32 32v18a5 5 0 0 1-10 0"/>
          </svg>
        </div>
        <div>
          <h3>HELP &amp; SUPPORT</h3>
          <p>We connect people who have extra food with people who need it, so that good food is never wasted and nobody sleeps hungry.</p>
        </div>
      </div>

      <div class="m-item">
        <div class="m-ico">
          <svg viewBox="0 0 64 64" fill="none" stroke="#26c6da" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="32" cy="26" r="18"/>
            <path d="M14 26h36M32 8c-8 8-8 28 0 36M32 8c8 8 8 28 0 36"/>
            <path d="M22 50h20M32 44v6"/>
          </svg>
        </div>
        <div>
          <h3>Starve Free World</h3>
          <p>Our goal is a hunger-free community where every extra meal reaches a hungry person within the right time.</p>
        </div>
      </div>

      <div class="m-item">
        <div class="m-ico">
          <svg viewBox="0 0 64 64" fill="none" stroke="#111" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M8 36l10 8h14l12-6"/>
            <path d="M56 36l-10 8"/>
            <path d="M8 36c0-4 4-6 8-4l12 6"/>
            <path d="M56 36c0-4-4-6-8-4l-12 6"/>
            <rect x="24" y="12" width="16" height="14" rx="2"/>
          </svg>
        </div>
        <div>
          <h3>Helping Hand</h3>
          <p>Restaurants, hotels, shops and individuals can lend a helping hand by donating food in just a few clicks.</p>
        </div>
      </div>
    </div>
  </div>
</section>

</body>
</html>