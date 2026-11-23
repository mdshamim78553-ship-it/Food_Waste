<?php
session_start();
include 'database.php';

// শুধু admin রোলধারী ইউজার ঢুকতে পারবে
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$msg = "";
$error = "";

// Food Request Accept / Reject / Deliver হ্যান্ডলিং
if (isset($_GET['action']) && isset($_GET['req_id'])) {
    $req_id = (int)$_GET['req_id'];
    $action = $_GET['action'];

    $req_q = mysqli_query($conn, "SELECT * FROM food_requests WHERE id = '$req_id' LIMIT 1");
    if ($req_q && mysqli_num_rows($req_q) === 1) {
        $req_row = mysqli_fetch_assoc($req_q);
        $donation_id = (int)$req_row['donation_id'];

        if ($action === 'accept_req') {
            mysqli_query($conn, "UPDATE food_requests SET status = 'Accepted' WHERE id = '$req_id'");
            mysqli_query($conn, "UPDATE donations SET status = 'Accepted' WHERE id = '$donation_id'");
            // এই ডোনেশনের বাকি পেন্ডিং রিকোয়েস্ট থাকলে Rejected করে দেওয়া
            mysqli_query($conn, "UPDATE food_requests SET status = 'Rejected' WHERE donation_id = '$donation_id' AND id != '$req_id' AND status = 'Pending'");
            $msg = "Request #$req_id accepted! Donor and Receiver can now chat and coordinate pickup.";
        } elseif ($action === 'reject_req') {
            mysqli_query($conn, "UPDATE food_requests SET status = 'Rejected' WHERE id = '$req_id'");
            // অন্য কোনো Accepted রিকোয়েস্ট না থাকলে ডোনেশনটি আবার Available করা
            $check_acc = mysqli_query($conn, "SELECT id FROM food_requests WHERE donation_id = '$donation_id' AND status = 'Accepted'");
            if ($check_acc && mysqli_num_rows($check_acc) === 0) {
                mysqli_query($conn, "UPDATE donations SET status = 'Available' WHERE id = '$donation_id'");
            }
            $msg = "Request #$req_id rejected.";
        } elseif ($action === 'deliver_req') {
            mysqli_query($conn, "UPDATE food_requests SET status = 'Delivered' WHERE id = '$req_id'");
            mysqli_query($conn, "UPDATE donations SET status = 'Delivered' WHERE id = '$donation_id'");
            $msg = "Request #$req_id marked as Delivered.";
        }
    }
}

// Donation Approve / Delete হ্যান্ডলিং
if (isset($_GET['don_action']) && isset($_GET['don_id'])) {
    $don_id = (int)$_GET['don_id'];
    $don_action = $_GET['don_action'];

    if ($don_action === 'approve_don') {
        mysqli_query($conn, "UPDATE donations SET status = 'Available' WHERE id = '$don_id'");
        $msg = "Donation #$don_id is now Available.";
    } elseif ($don_action === 'delete_don') {
        mysqli_query($conn, "DELETE FROM food_requests WHERE donation_id = '$don_id'");
        mysqli_query($conn, "DELETE FROM donations WHERE id = '$don_id'");
        $msg = "Donation #$don_id deleted.";
    }
}

// সামারি কাউন্ট
$total_donations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM donations"))['cnt'] ?? 0;
$pending_requests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM food_requests WHERE status = 'Pending'"))['cnt'] ?? 0;
$accepted_requests = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM food_requests WHERE status = 'Accepted'"))['cnt'] ?? 0;
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM users"))['cnt'] ?? 0;

// সব Food Requests আনা (Receiver ও Donor তথ্যসহ)
$requests_sql = "SELECT fr.id AS req_id, fr.status AS req_status, fr.requested_at,
                        r.name AS receiver_name, r.phone AS receiver_phone, r.address AS receiver_address,
                        d.id AS donation_id, d.food_item, d.food_title, d.quantity, d.pickup_address, d.contact_phone AS don_phone,
                        u.name AS donor_name, u.phone AS donor_user_phone
                 FROM food_requests fr
                 JOIN users r ON fr.receiver_id = r.id
                 JOIN donations d ON fr.donation_id = d.id
                 LEFT JOIN users u ON d.donor_id = u.id
                 ORDER BY fr.id DESC";
$requests_res = mysqli_query($conn, $requests_sql);

// সব Donations আনা
$donations_sql = "SELECT d.*, u.name AS donor_name
                  FROM donations d
                  LEFT JOIN users u ON d.donor_id = u.id
                  ORDER BY d.id DESC";
$donations_res = mysqli_query($conn, $donations_sql);

// সব Contact Messages আনা
$contact_res = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FoodShare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #f4f7f6; color: #26332b; }
        .navbar {
            background: #142c20; color: white; padding: 15px 6%;
            display: flex; justify-content: space-between; align-items: center; gap: 15px;
        }
        .navbar h2 { font-size: 20px; }
        .nav-links { display: flex; align-items: center; gap: 18px; }
        .nav-links a { color: white; text-decoration: none; font-weight: 600; font-size: 14px; }
        .btn-logout { background: #c62828; padding: 8px 14px; border-radius: 5px; }

        .container { max-width: 1200px; margin: 28px auto; padding: 0 20px; }
        .msg-box {
            background: #e4f5e9; color: #16834a; border-left: 4px solid #16834a;
            padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-weight: bold;
        }

        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; margin-bottom: 28px; }
        .stat-card {
            background: white; padding: 20px; border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05); border-top: 4px solid #16834a;
        }
        .stat-card h4 { color: #666; font-size: 13px; text-transform: uppercase; margin-bottom: 8px; }
        .stat-card .num { font-size: 28px; font-weight: bold; color: #173c29; }

        .panel {
            background: white; padding: 22px; border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05); margin-bottom: 28px;
        }
        .panel h3 { color: #173c29; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 2px solid #f0f4f1; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 11px 12px; border-bottom: 1px solid #e6ece9; text-align: left; vertical-align: top; }
        th { background: #eef7f0; color: #173c29; font-weight: bold; }

        .badge { display: inline-block; padding: 3px 9px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-accepted { background: #e4f5e9; color: #16834a; }
        .badge-rejected { background: #fdecea; color: #c62828; }
        .badge-delivered { background: #e3f2fd; color: #1565c0; }

        .action-btn {
            display: inline-block; padding: 5px 10px; border-radius: 5px; color: white;
            text-decoration: none; font-size: 12px; font-weight: bold; margin: 2px 2px 2px 0;
        }
        .btn-accept { background: #16834a; }
        .btn-reject { background: #c62828; }
        .btn-deliver { background: #1565c0; }
        .btn-chat { background: #f39c12; color: #fff; }

        @media (max-width: 850px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 500px) {
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>🛡️ FoodShare Admin Panel</h2>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="available_food.php">Available Food</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</div>

<div class="container">
    <?php if (!empty($msg)): ?>
        <div class="msg-box"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total Donations</h4>
            <div class="num"><?php echo (int)$total_donations; ?></div>
        </div>
        <div class="stat-card">
            <h4>Pending Requests</h4>
            <div class="num"><?php echo (int)$pending_requests; ?></div>
        </div>
        <div class="stat-card">
            <h4>Accepted (Chat Active)</h4>
            <div class="num"><?php echo (int)$accepted_requests; ?></div>
        </div>
        <div class="stat-card">
            <h4>Total Users</h4>
            <div class="num"><?php echo (int)$total_users; ?></div>
        </div>
    </div>

    <!-- Food Requests Management -->
    <div class="panel">
        <h3>Food Requests (Accept / Reject & Coordinate)</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Food Item</th>
                        <th>Donor</th>
                        <th>Receiver</th>
                        <th>Pickup & Contact</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($requests_res && mysqli_num_rows($requests_res) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($requests_res)): ?>
                        <?php
                        $food_name = !empty($r['food_title']) ? $r['food_title'] : $r['food_item'];
                        $st = $r['req_status'];
                        $badge_class = 'badge-pending';
                        if ($st === 'Accepted') $badge_class = 'badge-accepted';
                        elseif ($st === 'Rejected') $badge_class = 'badge-rejected';
                        elseif ($st === 'Delivered') $badge_class = 'badge-delivered';

                        $donor_phone = !empty($r['don_phone']) ? $r['don_phone'] : ($r['donor_user_phone'] ?? 'N/A');
                        ?>
                        <tr>
                            <td>#<?php echo (int)$r['req_id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($food_name); ?></strong><br>
                                <small>Qty: <?php echo htmlspecialchars($r['quantity']); ?></small>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($r['donor_name'] ?? 'Donor'); ?><br>
                                <small>📞 <?php echo htmlspecialchars($donor_phone); ?></small>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($r['receiver_name']); ?></strong><br>
                                <small>📞 <?php echo htmlspecialchars($r['receiver_phone'] ?? 'N/A'); ?></small><br>
                                <small>📍 <?php echo htmlspecialchars($r['receiver_address'] ?? 'N/A'); ?></small>
                            </td>
                            <td>
                                <small>Pickup: <?php echo htmlspecialchars($r['pickup_address']); ?></small>
                            </td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($st); ?></span>
                            </td>
                            <td>
                                <?php if ($st === 'Pending'): ?>
                                    <a href="admin_dashboard.php?action=accept_req&req_id=<?php echo (int)$r['req_id']; ?>" class="action-btn btn-accept">Accept</a>
                                    <a href="admin_dashboard.php?action=reject_req&req_id=<?php echo (int)$r['req_id']; ?>" class="action-btn btn-reject">Reject</a>
                                <?php elseif ($st === 'Accepted'): ?>
                                    <a href="chat.php?request_id=<?php echo (int)$r['req_id']; ?>" class="action-btn btn-chat">💬 Chat</a>
                                    <a href="admin_dashboard.php?action=deliver_req&req_id=<?php echo (int)$r['req_id']; ?>" class="action-btn btn-deliver">Delivered</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center; color:#777;">No food requests found yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- All Food Donations -->
    <div class="panel">
        <h3>All Food Donations</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Food</th>
                        <th>Donor</th>
                        <th>Quantity</th>
                        <th>Pickup Address</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($donations_res && mysqli_num_rows($donations_res) > 0): ?>
                    <?php while ($d = mysqli_fetch_assoc($donations_res)): ?>
                        <?php $d_title = !empty($d['food_title']) ? $d['food_title'] : $d['food_item']; ?>
                        <tr>
                            <td>#<?php echo (int)$d['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($d_title); ?></strong></td>
                            <td><?php echo htmlspecialchars($d['donor_name'] ?? 'Donor'); ?></td>
                            <td><?php echo htmlspecialchars($d['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($d['pickup_address']); ?></td>
                            <td><span class="badge badge-accepted"><?php echo htmlspecialchars($d['status']); ?></span></td>
                            <td>
                                <a href="admin_dashboard.php?don_action=delete_don&don_id=<?php echo (int)$d['id']; ?>" class="action-btn btn-reject" onclick="return confirm('Delete this donation?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center; color:#777;">No donations found yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Contact Messages -->
    <div class="panel">
        <h3>Contact Messages</h3>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($contact_res && mysqli_num_rows($contact_res) > 0): ?>
                    <?php while ($cm = mysqli_fetch_assoc($contact_res)): ?>
                        <tr>
                            <td>#<?php echo (int)$cm['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($cm['name']); ?></strong><br>
                                <small><?php echo htmlspecialchars($cm['email']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($cm['subject']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($cm['message'])); ?></td>
                            <td><small><?php echo htmlspecialchars($cm['created_at']); ?></small></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; color:#777;">No contact messages yet.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>