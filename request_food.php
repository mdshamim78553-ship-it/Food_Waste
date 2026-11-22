<?php
session_start();
include 'database.php'; // Food_Waste ফোল্ডারের ভেতরের database.php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$receiver_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: available_food.php");
    exit();
}

$donation_id = (int)$_GET['id'];

// খাবারটি এখনো Available আছে কি না চেক করা
$check_sql = "SELECT * FROM donations WHERE id = '$donation_id' AND status = 'Available'";
$check_result = mysqli_query($conn, $check_sql);

if ($check_result && mysqli_num_rows($check_result) > 0) {
    // রিকোয়েস্ট টেবিলে ডাটা INSERT করা
    $insert_sql = "INSERT INTO food_requests (donation_id, receiver_id, status) 
                   VALUES ('$donation_id', '$receiver_id', 'Pending')";

    if (mysqli_query($conn, $insert_sql)) {
        // donations টেবিলের স্ট্যাটাস আপডেট করে 'Requested' করা
        $update_sql = "UPDATE donations SET status = 'Requested' WHERE id = '$donation_id'";
        mysqli_query($conn, $update_sql);

        header("Location: available_food.php?msg=requested");
        exit();
    } else {
        echo "Error submitting request: " . mysqli_error($conn);
    }
} else {
    header("Location: available_food.php?msg=unavailable");
    exit();
}