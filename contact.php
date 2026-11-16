<?php
// PHP Backend Logic
if (isset($_POST['submit'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $message = $_POST['message'];
    
    // Database connection or processing logic goes here
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #ffffff;
            color: #333333;
            padding: 40px 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .page-title {
            text-align: center;
            color: #40b5ad;
            font-size: 28px;
            font-weight: 500;
            letter-spacing: 1px;
            margin-bottom: 50px;
            text-transform: uppercase;
        }

        .contact-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 60px;
        }

        .form-section {
            flex: 1;
        }

        .info-section {
            flex: 1;
        }

        .section-title {
            color: #7bc0a3;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 25px;
            text-transform: uppercase;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            color: #444444;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            font-size: 13px;
            outline: none;
            color: #333333;
            background-color: #ffffff;
            transition: border-color 0.2s ease;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #aaaaaa;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #7bc0a3;
        }

        .form-group textarea {
            height: 140px;
            resize: vertical;
        }

        .submit-btn {
            background-color: #7bc0a3;
            color: #ffffff;
            border: none;
            padding: 10px 24px;
            font-size: 14px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }

        .submit-btn:hover {
            background-color: #62a88b;
        }

        .info-block {
            margin-bottom: 25px;
        }

        .info-block h3 {
            font-size: 15px;
            color: #444444;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .info-block p {
            font-size: 13px;
            color: #555555;
            line-height: 1.6;
        }

        .info-details p {
            font-size: 13px;
            color: #555555;
            line-height: 1.8;
        }

        .info-details strong {
            color: #333333;
        }

        @media (max-width: 768px) {
            .contact-wrapper {
                flex-direction: column;
                gap: 40px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1 class="page-title">CONTACT</h1>

        <div class="contact-wrapper">
            <div class="form-section">
                <h2 class="section-title">WANT TO WORK WITH US?</h2>
                
                <form action="contact.php" method="POST">
                    <div class="form-group">
                        <label for="firstname">First Name</label>
                        <input type="text" id="firstname" name="firstname" placeholder="First Name" required>
                    </div>

                    <div class="form-group">
                        <label for="lastname">Last Name</label>
                        <input type="text" id="lastname" name="lastname" placeholder="Last Name" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" placeholder="Phone" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Your Message</label>
                        <textarea id="message" name="message" placeholder="Message.." required></textarea>
                    </div>

                    <button type="submit" name="submit" class="submit-btn">Send Message</button>
                </form>
            </div>

            <div class="info-section">
                <h2 class="section-title">CONTACT US</h2>

                <div class="info-block">
                    <h3>Address</h3>
                    <p><strong>Food Waste Management System</strong></p>
                </div>

                <div class="info-details">
                    <p><strong>Contact Number:</strong> +088-01712345600</p>
                    <p><strong>email:</strong> info@gmail.com</p>
                    <p><strong>Address :</strong> test address</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>