<?php
$conn = mysqli_connect("localhost:3307", "root", "", "food_waste");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>