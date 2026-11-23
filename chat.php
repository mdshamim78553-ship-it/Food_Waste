<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = (int)$_SESSION['user_id'];
$current_role = $_SESSION['role'] ?? 'receiver';
$request_id = isset($_GET['request_id']) ? (int)$_GET['request_id'] : 0;

if ($request_id <= 0) {
    die("Invalid request ID.");
}

// রিকোয়েস্ট, ডোনার এবং রিসিভারের তথ্য আনা
$req_sql = "SELECT fr.*, 
                   d.food_item, d.food_title, d.pickup_address, d.contact_phone AS don_contact, d.donor_id,
                   r.name AS receiver_name, r.phone AS receiver_phone,
                   u.name AS donor_name, u.phone AS donor_phone
            FROM food_requests fr
            JOIN donations d ON fr.donation_id = d.id
            JOIN users r ON fr.receiver_id = r.id
            LEFT JOIN users u ON d.donor_id = u.id
            WHERE fr.id = '$request_id' LIMIT 1";
$req_res = mysqli_query($conn, $req_sql);

if (!$req_res || mysqli_num_rows($req_res) !== 1) {
    die("Food request not found.");
}

$req = mysqli_fetch_assoc($req_res);

// শুধু সংশ্লিষ্ট Donor, Receiver বা Admin চ্যাটে ঢুকতে পারবে
$is_donor = ($current_user_id === (int)$req['donor_id']);
$is_receiver = ($current_user_id === (int)$req['receiver_id']);
$is_admin = ($current_role === 'admin');

if (!$is_donor && !$is_receiver && !$is_admin) {
    die("Access denied to this chat.");
}

// অ্যাডমিন Accept করার পরই চ্যাট সক্রিয় হবে
if ($req['status'] !== 'Accepted' && !$is_admin) {
    die("Chat will be available once the admin accepts this food request. Current status: " . htmlspecialchars($req['status']));
}

// নতুন মেসেজ পাঠানো (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $msg_text = mysqli_real_escape_string($conn, trim($_POST['message']));
    if (!empty($msg_text)) {
        mysqli_query($conn, "INSERT INTO messages (request_id, sender_id, message) VALUES ('$request_id', '$current_user_id', '$msg_text')");
        header("Location: chat.php?request_id=" . $request_id);
        exit();
    }
}

// সব মেসেজ আনা
$msgs_sql = "SELECT m.*, u.name AS sender_name, u.role AS sender_role
             FROM messages m
             JOIN users u ON m.sender_id = u.id
             WHERE m.request_id = '$request_id'
             ORDER BY m.id ASC";
$msgs_res = mysqli_query($conn, $msgs_sql);

$food_name = !empty($req['food_title']) ? $req['food_title'] : $req['food_item'];
$donor_phone_clean = preg_replace('/[^0-9]/', '', !empty($req['don_contact']) ? $req['don_contact'] : ($req['donor_phone'] ?? ''));
$receiver_phone_clean = preg_replace('/[^0-9]/', '', $req['receiver_phone'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - <?php echo htmlspecialchars($food_name); ?> | FoodShare</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background: #eef3f0; color: #26332b; }

        .chat-header {
            background: #16834a; color: white; padding: 16px 6%;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;
        }
        .chat-header h2 { font-size: 20px; }
        .chat-header p { font-size: 13px; opacity: 0.92; margin-top: 3px; }
        .header-actions a {
            color: white; text-decoration: none; background: rgba(255,255,255,0.18);
            padding: 8px 14px; border-radius: 6px; font-weight: bold; font-size: 13px; margin-left: 8px;
        }

        .chat-container {
            max-width: 820px; margin: 24px auto; background: white; border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08); overflow: hidden; display: flex; flex-direction: column;
        }

        .info-bar {
            background: #f6fbf7; padding: 14px 20px; border-bottom: 1px solid #e1ebe4;
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; font-size: 13px;
        }
        .quick-contact a {
            display: inline-block; text-decoration: none; padding: 6px 12px; border-radius: 20px;
            font-weight: bold; font-size: 12px; margin-left: 6px;
        }
        .btn-call { background: #e4f5e9; color: #16834a; border: 1px solid #16834a; }
        .btn-wa { background: #25D366; color: white; }

        .messages-box {
            height: 410px; overflow-y: auto; padding: 20px; background: #f9fbfa;
            display: flex; flex-direction: column; gap: 12px;
        }

        .bubble {
            max-width: 72%; padding: 11px 15px; border-radius: 14px; font-size: 14px; line-height: 1.5;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .bubble-me {
            align-self: flex-end; background: #16834a; color: white; border-bottom-right-radius: 4px;
        }
        .bubble-other {
            align-self: flex-start; background: white; color: #222; border: 1px solid #e2e8e4; border-bottom-left-radius: 4px;
        }
        .bubble-meta {
            font-size: 11px; opacity: 0.78; margin-top: 5px; text-align: right;
        }
        .bubble-other .bubble-meta { text-align: left; }

        .chat-input-form {
            display: flex; gap: 10px; padding: 14px 18px; background: white; border-top: 1px solid #e5ece7;
        }
        .chat-input-form input {
            flex: 1; padding: 12px 15px; border: 1px solid #ccc; border-radius: 25px; font-size: 14px; outline: none;
        }
        .chat-input-form input:focus { border-color: #16834a; }
        .chat-input-form button {
            background: #16834a; color: white; border: none; padding: 0 22px;
            border-radius: 25px; font-weight: bold; cursor: pointer; font-size: 14px;
        }
        .chat-input-form button:hover { background: #11683a; }
    </style>
</head>
<body>

<div class="chat-header">
    <div>
        <h2>💬 <?php echo htmlspecialchars($food_name); ?></h2>
        <p>
            Donor: <strong><?php echo htmlspecialchars($req['donor_name'] ?? 'Donor'); ?></strong> &nbsp;|&nbsp;
            Receiver: <strong><?php echo htmlspecialchars($req['receiver_name']); ?></strong> &nbsp;|&nbsp;
            Status: <strong><?php echo htmlspecialchars($req['status']); ?></strong>
        </p>
    </div>
    <div class="header-actions">
        <?php if ($is_admin): ?>
            <a href="admin_dashboard.php">← Admin Panel</a>
        <?php elseif ($is_donor): ?>
            <a href="dashboard.php">← Donor Dashboard</a>
        <?php else: ?>
            <a href="available_food.php">← Available Food</a>
        <?php endif; ?>
    </div>
</div>

<div class="chat-container">
    <div class="info-bar">
        <div>
            📍 <strong>Pickup:</strong> <?php echo htmlspecialchars($req['pickup_address']); ?>
        </div>
        <div class="quick-contact">
            <?php
            $target_phone = $is_donor ? $receiver_phone_clean : $donor_phone_clean;
            $target_raw = $is_donor ? ($req['receiver_phone'] ?? '') : (!empty($req['don_contact']) ? $req['don_contact'] : ($req['donor_phone'] ?? ''));
            if (!empty($target_phone)):
                $wa_num = (substr($target_phone, 0, 2) === '88') ? $target_phone : '88' . $target_phone;
            ?>
                <span>📞 <?php echo htmlspecialchars($target_raw); ?></span>
                <a href="tel:<?php echo htmlspecialchars($target_phone); ?>" class="btn-call">Call</a>
                <a href="https://wa.me/<?php echo htmlspecialchars($wa_num); ?>" target="_blank" class="btn-wa">WhatsApp</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="messages-box" id="messagesBox">
        <?php if ($msgs_res && mysqli_num_rows($msgs_res) > 0): ?>
            <?php while ($m = mysqli_fetch_assoc($msgs_res)): ?>
                <?php $is_me = ((int)$m['sender_id'] === $current_user_id); ?>
                <div class="bubble <?php echo $is_me ? 'bubble-me' : 'bubble-other'; ?>">
                    <div><?php echo nl2br(htmlspecialchars($m['message'])); ?></div>
                    <div class="bubble-meta">
                        <?php echo htmlspecialchars($m['sender_name']); ?> • <?php echo date("h:i A", strtotime($m['created_at'])); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color:#777; margin:auto;">
                Request accepted! Start chatting to coordinate food pickup time and location.
            </p>
        <?php endif; ?>
    </div>

    <form method="POST" action="" class="chat-input-form">
        <input type="text" name="message" placeholder="Type your message (e.g., I'm at the pickup point)..." autocomplete="off" required>
        <button type="submit" name="send_message">Send</button>
    </form>
</div>

<script>
    const msgBox = document.getElementById("messagesBox");
    msgBox.scrollTop = msgBox.scrollHeight;

    // প্রতি ৩ সেকেন্ড পরপর নতুন মেসেজ থাকলে পেজ আপডেট করবে
    setInterval(function () {
        const activeInput = document.querySelector('.chat-input-form input');
        if (document.activeElement !== activeInput || activeInput.value === "") {
            fetch(window.location.href)
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, "text/html");
                    const newBox = doc.getElementById("messagesBox");
                    if (newBox && newBox.innerHTML !== msgBox.innerHTML) {
                        msgBox.innerHTML = newBox.innerHTML;
                        msgBox.scrollTop = msgBox.scrollHeight;
                    }
                });
        }
    }, 3000);
</script>

</body>
</html>