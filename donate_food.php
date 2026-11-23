<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$donor_id = (int)$_SESSION['user_id'];
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['donate_food'])) {
    $food_title = mysqli_real_escape_string($conn, trim($_POST['food_title']));
    $category = mysqli_real_escape_string($conn, trim($_POST['category']));
    $quantity = mysqli_real_escape_string($conn, trim($_POST['quantity']));
    $expiry_raw = trim($_POST['expiry_time']);
    $pickup_address = mysqli_real_escape_string($conn, trim($_POST['pickup_address']));
    $contact_phone = mysqli_real_escape_string($conn, trim($_POST['contact_phone']));

    // datetime-local ইনপুট (YYYY-MM-DDTHH:MM) কে MySQL DATETIME ফরম্যাটে নেওয়া
    $expiry_formatted = str_replace("T", " ", $expiry_raw);
    if (strlen($expiry_formatted) === 16) {
        $expiry_formatted .= ":00";
    }
    $expiry_time = mysqli_real_escape_string($conn, $expiry_formatted);

    $food_image_name = NULL;

    // অপশনাল ছবি আপলোড হলে uploads ফোল্ডারে সেভ করা
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $orig_name = $_FILES['food_image']['name'];
        $tmp_name = $_FILES['food_image']['tmp_name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed_ext)) {
            $error = "Only JPG, JPEG, PNG, and WEBP images are allowed.";
        } else {
            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }
            $new_name = "food_" . time() . "_" . rand(1000, 9999) . "." . $ext;
            $target_path = "uploads/" . $new_name;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $food_image_name = $new_name;
            } else {
                $error = "Failed to upload food photo.";
            }
        }
    }

    if (empty($error)) {
        if (empty($food_title) || empty($category) || empty($quantity) || empty($expiry_time) || empty($pickup_address) || empty($contact_phone)) {
            $error = "Please fill in all required fields.";
        } else {
            $img_sql_val = $food_image_name ? "'" . mysqli_real_escape_string($conn, $food_image_name) . "'" : "NULL";

            // food_item এবং food_title দুটো কলামেই ভ্যালু দেওয়া হলো যাতে কোনো NOT NULL এরর না আসে
            $sql = "INSERT INTO donations 
                    (donor_id, food_item, food_title, category, quantity, expiry_time, pickup_address, contact_phone, food_image, status) 
                    VALUES 
                    ('$donor_id', '$food_title', '$food_title', '$category', '$quantity', '$expiry_time', '$pickup_address', '$contact_phone', $img_sql_val, 'Available')";

            if (mysqli_query($conn, $sql)) {
                $msg = "Thank you! Your food donation has been posted successfully.";
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
    <title>Donate Excess Food - FoodShare</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background: #f4f7f6;
            color: #26332b;
        }
        .navbar {
            background: #16834a;
            color: white;
            padding: 15px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-left: 18px;
        }
        .container {
            max-width: 980px;
            margin: 35px auto;
            padding: 0 20px;
        }
        .donate-wrapper {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.07);
            display: grid;
            grid-template-columns: 1fr 1.25fr;
        }
        .side-banner {
            background: linear-gradient(160deg, #16834a, #0f5c33);
            color: white;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .side-banner img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .side-banner h2 {
            font-size: 26px;
            margin-bottom: 10px;
        }
        .side-banner p {
            font-size: 14px;
            line-height: 1.6;
            opacity: 0.92;
        }
        .form-panel {
            padding: 32px;
        }
        .form-panel h1 {
            color: #173c29;
            font-size: 26px;
            margin-bottom: 6px;
        }
        .subtitle {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
            color: #333;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .btn-submit {
            width: 100%;
            background: #16834a;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 6px;
        }
        .btn-submit:hover {
            background: #11683a;
        }
        .back-link {
            display: inline-block;
            margin-top: 16px;
            color: #16834a;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }
        .msg-success {
            background: #e4f5e9;
            color: #16834a;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: bold;
            font-size: 14px;
        }
        .msg-error {
            background: #fdecea;
            color: #c62828;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
            font-weight: bold;
            font-size: 14px;
        }
        @media (max-width: 800px) {
            .donate-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>🍱 FoodShare</h2>
    <div>
        <a href="index.php">Home</a>
        <a href="available_food.php">Available Food</a>
        <a href="dashboard.php">Dashboard</a>
    </div>
</div>

<div class="container">
    <div class="donate-wrapper">
        <div class="side-banner">
            <div>
                <img src="https://images.unsplash.com/photo-1488459716781-31db52582fe9?w=800" alt="Fresh food sharing">
                <h2>Share Excess Food</h2>
                <p>Your extra meals or fresh ingredients can feed someone in need today. Upload a photo so receivers know what to expect.</p>
            </div>
            <p style="margin-top: 18px; font-size: 13px; opacity: 0.85;">🌱 Zero Waste • Community Care</p>
        </div>

        <div class="form-panel">
            <h1>Donate Excess Food</h1>
            <p class="subtitle">Share fresh food with people who need it right now.</p>

            <?php if (!empty($msg)): ?>
                <div class="msg-success"><?php echo htmlspecialchars($msg); ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="msg-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Food Title / Item Name *</label>
                    <input type="text" name="food_title" placeholder="e.g., Cooked Rice & Chicken Curry, Packed Meals" required>
                </div>

                <div class="form-group">
                    <label>Food Category *</label>
                    <select name="category" required>
                        <option value="Cooked Food">Cooked Food</option>
                        <option value="Packed Meals">Packed Meals</option>
                        <option value="Fruits & Vegetables">Fruits & Vegetables</option>
                        <option value="Bakery & Snacks">Bakery & Snacks</option>
                        <option value="Dry & Packaged Food">Dry & Packaged Food</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Quantity / Servings *</label>
                    <input type="text" name="quantity" placeholder="e.g., For 10 people / 5 kg" required>
                </div>

                <div class="form-group">
                    <label>Best Before / Expiry Time *</label>
                    <input type="datetime-local" name="expiry_time" required>
                </div>

                <div class="form-group">
                    <label>Pickup Address *</label>
                    <textarea name="pickup_address" rows="3" placeholder="Enter full pickup location" required></textarea>
                </div>

                <div class="form-group">
                    <label>Contact Phone Number *</label>
                    <input type="text" name="contact_phone" placeholder="e.g., 017XXXXXXXX" required>
                </div>

                <div class="form-group">
                    <label>Food Photo (Optional)</label>
                    <input type="file" name="food_image" accept=".jpg,.jpeg,.png,.webp">
                </div>

                <button type="submit" name="donate_food" class="btn-submit">Post Food Donation</button>
            </form>

            <a href="index.php" class="back-link">← Back to Home</a>
        </div>
    </div>
</div>

</body>
</html>