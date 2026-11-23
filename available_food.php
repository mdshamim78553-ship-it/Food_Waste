<?php
session_start();
include 'database.php';

// লগইন করা না থাকলে আগে লগইন পেজে পাঠানো
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$search = "";

if (isset($_GET['search']) && trim($_GET['search']) !== "") {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    $sql = "SELECT * FROM donations 
            WHERE status = 'Available' 
            AND (food_item LIKE '%$search%' OR food_title LIKE '%$search%' OR pickup_address LIKE '%$search%') 
            ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM donations WHERE status = 'Available' ORDER BY id DESC";
}
$result = mysqli_query($conn, $sql);

// ইউজারের নিজের Request History আনা
$req_history_sql = "SELECT fr.id AS req_id, fr.status AS req_status, fr.requested_at, 
                           d.food_item, d.food_title, d.quantity, d.pickup_address, d.contact_phone, d.food_image
                    FROM food_requests fr
                    JOIN donations d ON fr.donation_id = d.id
                    WHERE fr.receiver_id = '$user_id'
                    ORDER BY fr.id DESC";
$req_history_result = mysqli_query($conn, $req_history_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Food & History - FoodShare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f7f6; color: #26332b; }
        nav {
            background: #ffffff; padding: 16px 7%; display: flex; justify-content: space-between;
            align-items: center; box-shadow: 0 2px 12px rgba(0,0,0,0.08); position: sticky; top: 0; z-index: 100;
        }
        .logo { font-size: 25px; font-weight: bold; color: #16834a; }
        .logo span { color: #f39c12; }
        nav ul { display: flex; list-style: none; gap: 22px; align-items: center; }
        nav ul li a { text-decoration: none; color: #333; font-weight: 600; }
        nav ul li a:hover { color: #16834a; }
        .logout-btn { background: #c62828; color: white !important; padding: 8px 16px; border-radius: 6px; }

        .page-header {
            background: linear-gradient(110deg, #e4f5e9, #f8f9ed);
            padding: 38px 8%; text-align: center;
        }
        .page-header h1 { color: #173c29; font-size: 32px; margin-bottom: 8px; }
        .page-header p { color: #555; margin-bottom: 20px; }
        .search-box { max-width: 520px; margin: 0 auto; display: flex; gap: 10px; }
        .search-box input { flex: 1; padding: 11px 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .search-box button { background: #16834a; color: white; border: none; padding: 11px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; }

        .container { max-width: 1150px; margin: 30px auto; padding: 0 20px; }
        .alert-success { background: #d4edda; color: #155724; padding: 13px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border-left: 4px solid #16834a; }
        .alert-warning { background: #fff3cd; color: #856404; padding: 13px 16px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border-left: 4px solid #f39c12; }

        .section-heading { font-size: 22px; color: #173c29; margin-bottom: 16px; border-bottom: 2px solid #dce6e0; padding-bottom: 8px; }
        .food-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; margin-bottom: 45px; }
        .food-card {
            background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .food-card img { width: 100%; height: 185px; object-fit: cover; }
        .food-card-body { padding: 18px; display: flex; flex-direction: column; flex: 1; justify-content: space-between; }
        .status-badge { display: inline-block; background: #e4f5e9; color: #16834a; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; margin-bottom: 8px; width: fit-content; }
        .food-card h3 { color: #173c29; font-size: 19px; margin-bottom: 8px; }
        .food-card p { color: #555; font-size: 14px; margin-bottom: 6px; }
        .btn-request { display: block; text-align: center; background: #16834a; color: white; text-decoration: none; padding: 10px; border-radius: 6px; font-weight: bold; margin-top: 12px; }
        .btn-request:hover { background: #11683a; }

        /* Request History Box */
        .history-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
        .history-card {
            background: white; padding: 16px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            display: flex; gap: 14px; align-items: center; border-left: 4px solid #16834a;
        }
        .history-card img { width: 70px; height: 70px; border-radius: 8px; object-fit: cover; }
        .history-card h4 { color: #173c29; margin-bottom: 4px; }
        .history-card p { font-size: 13px; color: #555; margin-bottom: 3px; }
        .chat-btn {
            display: inline-block; background: #f39c12; color: white; text-decoration: none;
            padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: bold; margin-top: 6px;
        }
        .chat-btn:hover { background: #d68910; }
        .empty-state { background: white; padding: 30px; border-radius: 10px; text-align: center; color: #666; grid-column: 1 / -1; }

        @media (max-width: 850px) {
            .food-grid { grid-template-columns: repeat(2, 1fr); }
            .history-list { grid-template-columns: 1fr; }
            nav { flex-direction: column; gap: 12px; }
        }
        @media (max-width: 550px) {
            .food-grid { grid-template-columns: 1fr; }
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
        <li><a href="contact.php">Contact Us</a></li>
        <li><span style="color:#16834a; font-weight:bold;">Hi, <?php echo htmlspecialchars($_SESSION['name']); ?></span></li>
        <li><a href="logout.php" class="logout-btn">Logout</a></li>
    </ul>
</nav>

<div class="page-header">
    <h1>Available Food Near You</h1>
    <p>Browse fresh donations and check your food request history below.</p>
    <form method="GET" action="available_food.php" class="search-box">
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by food name or pickup address...">
        <button type="submit">Search</button>
    </form>
</div>

<div class="container">
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'requested'): ?>
        <div class="alert-success">Your food request has been submitted successfully! Check your Request History below.</div>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'unavailable'): ?>
        <div class="alert-warning">Sorry, this food item is no longer available.</div>
    <?php endif; ?>

    <h2 class="section-heading">Available Donations</h2>
    <div class="food-grid">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php
                $title = !empty($row['food_title']) ? $row['food_title'] : $row['food_item'];
                $img_src = "https://images.unsplash.com/photo-1547592180-85f173990554?w=700";
                if (!empty($row['food_image']) && file_exists("uploads/" . $row['food_image'])) {
                    $img_src = "uploads/" . htmlspecialchars($row['food_image']);
                }
                ?>
                <div class="food-card">
                    <img src="<?php echo $img_src; ?>" alt="<?php echo htmlspecialchars($title); ?>">
                    <div class="food-card-body">
                        <div>
                            <span class="status-badge"><?php echo htmlspecialchars($row['status']); ?></span>
                            <h3><?php echo htmlspecialchars($title); ?></h3>
                            <p><strong>Quantity:</strong> <?php echo htmlspecialchars($row['quantity']); ?></p>
                            <p><strong>Pickup:</strong> <?php echo htmlspecialchars($row['pickup_address']); ?></p>
                            <?php if (!empty($row['contact_phone'])): ?>
                                <p><strong>Contact:</strong> <?php echo htmlspecialchars($row['contact_phone']); ?></p>
                            <?php endif; ?>
                        </div>
                        <a href="request_food.php?id=<?php echo (int)$row['id']; ?>" class="btn-request">Request Food</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <h3>No available food found right now.</h3>
                <p style="margin-top: 6px;">Check back later for new food donations.</p>
            </div>
        <?php endif; ?>
    </div>

    <h2 class="section-heading">Your Food Request History</h2>
    <div class="history-list">
        <?php if ($req_history_result && mysqli_num_rows($req_history_result) > 0): ?>
            <?php while ($h = mysqli_fetch_assoc($req_history_result)): ?>
                <?php
                $h_title = !empty($h['food_title']) ? $h['food_title'] : $h['food_item'];
                $h_img = "https://images.unsplash.com/photo-1547592180-85f173990554?w=400";
                if (!empty($h['food_image']) && file_exists("uploads/" . $h['food_image'])) {
                    $h_img = "uploads/" . htmlspecialchars($h['food_image']);
                }
                ?>
                <div class="history-card">
                    <img src="<?php echo $h_img; ?>" alt="<?php echo htmlspecialchars($h_title); ?>">
                    <div>
                        <h4><?php echo htmlspecialchars($h_title); ?></h4>
                        <p><strong>Quantity:</strong> <?php echo htmlspecialchars($h['quantity']); ?></p>
                        <p><strong>Pickup:</strong> <?php echo htmlspecialchars($h['pickup_address']); ?></p>
                        <p><strong>Status:</strong> <span style="color:#16834a; font-weight:bold;"><?php echo htmlspecialchars($h['req_status']); ?></span></p>
                        <?php if ($h['req_status'] === 'Accepted'): ?>
                            <a href="chat.php?request_id=<?php echo (int)$h['req_id']; ?>" class="chat-btn">💬 Chat with Donor</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <p>You haven't requested any food yet. Select an available meal above to place a request.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>