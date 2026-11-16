<?php

// Available Food List
// Sample Food Information

$foods = [

    [
        "id" => 1,
        "person" => "Tisha",
        "mobile" => "01712345678",
        "food" => "Rice, Dal, Roti",
        "address" => "Dhaka, Bangladesh",
        "state" => "Dhaka",
        "city" => "Dhaka",
        "date" => "2026-11-16 09:00:00"
    ],

    [
        "id" => 2,
        "person" => "Shamim",
        "mobile" => "01812345678",
        "food" => "Bread, Rice, Vegetables",
        "address" => "Mirpur, Dhaka",
        "state" => "Dhaka",
        "city" => "Dhaka",
        "date" => "2026-11-16 09:30:00"
    ]

];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Available Food List - FWMS</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Georgia, 'Times New Roman', serif;
            background: white;
            color: #333;
        }

        /* NAVBAR */

        .navbar {
            background: #e9d878;
            min-height: 62px;
            padding: 0;
            position: relative;
            z-index: 10;
        }

        .navbar .container {
            max-width: 1000px;
        }

        .navbar-nav {
            gap: 35px;
        }

        .navbar-nav .nav-link {
            color: white;
            font-size: 17px;
            padding: 18px 0;
            white-space: nowrap;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #222;
        }

        /* FWMS LOGO */

        .logo {
            position: absolute;
            top: 0;
            left: 80%;
            transform: translateX(-50%);
            width: 136px;
            height: 160px;
            background: #16bdd0;
            color: white;
            text-align: center;
            padding-top: 32px;
            z-index: 20;

            clip-path: polygon(
                0 0,
                100% 0,
                100% 72%,
                50% 100%,
                0 72%
            );
        }

        .logo h2 {
            font-family: Arial, sans-serif;
            font-size: 27px;
            font-weight: bold;
        }

        .logo span {
            color: #baf5f4;
            font-size: 23px;
        }

        /* TOP BANNER */

        .top-banner {
            height: 120px;

            background:
            url('https://images.unsplash.com/photo-1469571486292-0ba58a3f068b')
            center 40% / cover no-repeat;
        }

        /* FOOD LIST SECTION */

        .food-section {
            min-height: 435px;
            padding: 100px 20px 85px;
            background: white;
        }

        .food-container {
            max-width: 1000px;
            margin: auto;
        }

        .food-title {
            font-size: 32px;
            margin-bottom: 22px;
            color: #333;
        }

        /* TABLE */

        .table-responsive {
            overflow-x: auto;
        }

        .food-table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .food-table th,
        .food-table td {
            border: 1px solid #ddd;
            padding: 14px 12px;
            text-align: left;
            vertical-align: middle;
        }

        .food-table th {
            background: white;
            color: #222;
            font-weight: bold;
        }

        .food-table tr:hover {
            background: #f7fafa;
        }

        .view-btn {
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }

        .view-btn:hover {
            color: #16aabb;
            text-decoration: underline;
        }

        /* FOOTER */

        footer {
            background: #21bfd0;
            color: white;
            padding: 20px 0;
        }

        footer .container {
            max-width: 1000px;
        }

        footer p {
            margin: 0;
            font-size: 13px;
        }

        .social-icons {
            text-align: right;
            font-size: 22px;
        }

        .social-icons a {
            color: white;
            text-decoration: none;
            margin-left: 12px;
        }

        /* RESPONSIVE */

        @media(max-width: 991px) {

            .navbar .container {
                padding: 10px 20px;
            }

            .navbar-nav {
                gap: 5px;
            }

            .logo {
                display: none;
            }

            .food-section {
                padding-top: 55px;
            }

        }

        @media(max-width: 576px) {

            .food-title {
                font-size: 26px;
            }

            footer .social-icons {
                text-align: left;
                margin-top: 15px;
            }

        }

    </style>

</head>

<body>


<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg">

    <div class="container">

        <button class="navbar-toggler bg-light"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#mainNavbar">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        HOME
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="about.php">
                        ABOUT
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="food_list.php">
                        AVAILABLE FOOD LIST
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="contact.php">
                        CONTACT
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="login.php">
                        DONOR
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="admin/login.php">
                        ADMIN
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


<!-- FWMS LOGO -->

<div class="logo">

    <span>❀</span>

    <h2>FWMS</h2>

    <span>❀</span>

</div>


<!-- TOP BANNER -->

<section class="top-banner"></section>


<!-- AVAILABLE FOOD LIST -->

<section class="food-section">

    <div class="food-container">

        <h2 class="food-title">
            Available Food List
        </h2>

        <div class="table-responsive">

            <table class="food-table">

                <thead>

                    <tr>

                        <th>S.NO</th>

                        <th>Contact Person</th>

                        <th>Contact Person Mobile Number</th>

                        <th>Food Items</th>

                        <th>Address</th>

                        <th>State Name</th>

                        <th>City Name</th>

                        <th>Creation Date</th>

                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($foods as $food): ?>

                    <tr>

                        <td>
                            <?php echo $food['id']; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($food['person']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($food['mobile']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($food['food']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($food['address']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($food['state']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($food['city']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($food['date']); ?>
                        </td>

                        <td>

                            <a class="view-btn"
                            href="food_details.php?id=<?php echo $food['id']; ?>">

                                View Details

                            </a>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</section>


<!-- FOOTER -->

<footer>

    <div class="container">

        <div class="row align-items-center">

            <div class="col-md-8">

                <p>
                    &copy; 2026 @ Food Waste Management System
                </p>

            </div>

            <div class="col-md-4 social-icons">

                <a href="#">ⓕ</a>

                <a href="#">♥</a>

                <a href="#">ⓘ</a>

                <a href="#">◎</a>

                <a href="#">⊕</a>

            </div>

        </div>

    </div>

</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>