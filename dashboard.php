<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'donor') {
    header("Location: login.php");
    exit();
}

$donor_id = (int)$_SESSION['user_id'];
$msg = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_donation'])) {
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

    // ছবি আপলোড হলে uploads ফোল্ডারে সেভ করা
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

            $sql = "INSERT INTO donations 
                    (donor_id, food_item, food_title, category, quantity, expiry_time, pickup_address, contact_phone, food_image, status) 
                    VALUES 
                    ('$donor_id', '$food_title', '$food_title', '$category', '$quantity', '$expiry_time', '$pickup_address', '$contact_phone', $img_sql_val, 'Available')";

            if (mysqli_query($conn, $sql)) {
                $msg = "Donation submitted successfully!";
            } else {
                $error = "Database error: " . mysqli_error($conn);
            }
        }
    }
}

// ডোনেশনের সাথে Accepted রিকোয়েস্ট থাকলে তার request_id আনা
$history_sql = "SELECT d.*,
                       (SELECT fr.id FROM food_requests fr 
                        WHERE fr.donation_id = d.id AND fr.status = 'Accepted' 
                        ORDER BY fr.id DESC LIMIT 1) AS accepted_req_id
                FROM donations d
                WHERE d.donor_id = '$donor_id'
                ORDER BY d.id DESC";
$history_result = mysqli_query($conn, $history_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor Dashboard - FoodShare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f7f6; color: #26332b; }
        .navbar { background: #16834a; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; gap: 15px; }
        .navbar h2 { font-size: 20px; }
        .nav-right { display: flex; align-items: center; gap: 15px; }
        .nav-right a.nav-link { color: white; text-decoration: none; font-weight: 600; font-size: 14px; }
        .btn-logout { background: #c62828; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; white-space: nowrap; }
        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .welcome-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border-top: 4px solid #16834a;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }
        .welcome-text { flex: 1; }
        .welcome-img {
            width: 170px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .role-badge { display: inline-block; background: #e4f5e9; color: #16834a; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-top: 8px; }
        .grid { display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 22px; align-items: start; }
        .card { background: white; padding: 22px; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
        .card h3 { color: #16834a; margin-bottom: 16px; border-bottom: 2px solid #f4f7f6; padding-bottom: 8px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #444; font-size: 14px; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d0d7d4;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-submit { background: #16834a; color: white; border: none; padding: 11px 18px; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 14px; }
        .btn-submit:hover { background: #11683a; }
        .msg-success { background: #e4f5e9; color: #16834a; padding: 10px 12px; border-radius: 5px; font-weight: bold; margin-bottom: 14px; font-size: 14px; }
        .msg-error { background: #fdecea; color: #c62828; padding: 10px 12px; border-radius: 5px; font-weight: bold; margin-bottom: 14px; font-size: 14px; }
        .history-item {
            border: 1px solid #e6ece9;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            background: #fafcfb;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .history-thumb {
            width: 72px;
            height: 72px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
        }
        .history-details h4 { color: #173c29; margin-bottom: 4px; font-size: 16px; }
        .history-details p { font-size: 13px; color: #555; margin-bottom: 3px; }
        .status-tag { display: inline-block; background: #e4f5e9; color: #16834a; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-top: 4px; }
        .chat-btn {
            display: inline-block;
            background: #f39c12;
            color: white;
            text-decoration: none;
            padding: 5px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 6px;
        }
        .chat-btn:hover { background: #d68910; }
        @media (max-width: 820px) {
            .grid { grid-template-columns: 1fr; }
            .welcome-box { flex-direction: column; text-align: center; }
            .welcome-img { width: 100%; max-width: 280px; height: 150px; }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h2>🍱 FoodShare Donor Panel</h2>
        <div class="nav-right">
            <a href="index.php" class="nav-link">Home</a>
            <a href="available_food.php" class="nav-link">Available Food</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="welcome-box">
            <div class="welcome-text">
                <h1>Welcome Back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
                <span class="role-badge">Role: <?php echo htmlspecialchars($_SESSION['role']); ?></span>
                <p style="margin-top: 10px;">Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
            </div>
            <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?w=600" alt="Food donation sharing" class="welcome-img">
        </div>

        <div class="grid">
            <div class="card">
                <h3>Add New Food Donation</h3>

                <?php if (!empty($msg)): ?>
                    <p class="msg-success"><?php echo htmlspecialchars($msg); ?></p>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                    <p class="msg-error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Food Photo (Optional)</label>
                        <input type="file" name="food_image" accept=".jpg,.jpeg,.png,.webp">
                    </div>

                    <div class="form-group">
                        <label>Best Before / Expiry Time *</label>
                        <input type="datetime-local" name="expiry_time" required>
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
                        <label>Food Title / Item Name *</label>
                        <input type="text" name="food_title" placeholder="e.g., Cooked Rice & Chicken Curry, Packed Meals" required>
                    </div>

                    <div class="form-group">
                        <label>Quantity / Servings *</label>
                        <input type="text" name="quantity" placeholder="e.g., For 10 people / 5 kg" required>
                    </div>

                    <div class="form-group">
                        <label>Pickup Address *</label>
                        <textarea name="pickup_address" rows="3" placeholder="Enter full pickup location" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Contact Phone Number *</label>
                        <input type="text" name="contact_phone" placeholder="e.g., 017XXXXXXXX" required>
                    </div>

                    <button type="submit" name="add_donation" class="btn-submit">Submit Donation</button>
                </form>
            </div>

            <div class="card">
                <h3>Your Donation History</h3>

                <?php if ($history_result && mysqli_num_rows($history_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                        <?php
                        $title_text = !empty($row['food_title']) ? $row['food_title'] : $row['food_item'];
                        $has_img = !empty($row['food_image']) && file_exists("uploads/" . $row['food_image']);
                        ?>
                        <div class="history-item">
                            <?php if ($has_img): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['food_image']); ?>" alt="<?php echo htmlspecialchars($title_text); ?>" class="history-thumb">
                            <?php endif; ?>
                            <div class="history-details">
                                <h4><?php echo htmlspecialchars($title_text); ?></h4>
                                <?php if (!empty($row['category'])): ?>
                                    <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                                <?php endif; ?>
                                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?></p>
                                <p><strong>Pickup:</strong> <?php echo htmlspecialchars($row['pickup_address']); ?></p>
                                <?php if (!empty($row['expiry_time'])): ?>
                                    <p><strong>Expiry:</strong> <?php echo htmlspecialchars($row['expiry_time']); ?></p>
                                <?php endif; ?>
                                <span class="status-tag"><?php echo htmlspecialchars($row['status']); ?></span>

                                <?php if (!empty($row['accepted_req_id'])): ?>
                                    <div>
                                        <a href="chat.php?request_id=<?php echo (int)$row['accepted_req_id']; ?>" class="chat-btn">💬 Chat with Receiver</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #888; font-style: italic; text-align: center; margin-top: 40px;">No active donations found yet. Add a donation on the left to see it here.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>