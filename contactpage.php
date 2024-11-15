<?php
// Include database connection
session_start();
include('dbconnect.php');

$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

// Get total quantity in the cart for the header
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$cartQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($cartQuantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartQuantity = $row['total_quantity'] ?? 0;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve form data
    $name = $dbcnx->real_escape_string($_POST['name']);
    $email = $dbcnx->real_escape_string($_POST['email']);
    $subject = $dbcnx->real_escape_string($_POST['subject']);
    $message = $dbcnx->real_escape_string($_POST['message']);

    // Insert the message into the database
    $sql = "INSERT INTO contact_messages (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

    if ($dbcnx->query($sql)) {
        echo "<script>alert('Thank you for contacting us. We will get back to you shortly.');</script>";
    } else {
        echo "<script>alert('Error: " . $dbcnx->error . "');</script>";
    }

    // Close the database connection
    $dbcnx->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Element Electronics</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo"><a href="homepage.php"><img src="logo.png" alt="Element Electronics" width="50px"></h1>
            <nav>
                <ul>
                    <li><a href="homepage.php">Home</a></li>
                    <li><a href="catalog.php">Shop</a></li>
                    <li><a href="aboutus.php">About Us</a></li>
                    <li><a href="contactpage.php">Contact</a></li>
                </ul>
            </nav>
            <div class="search">
                <form action="search_results.php" method="GET" class="search-form">
                    <img src="search.png" alt="Search Icon" id="search-icon">
                    <input type="search" id="search-input" name="query" placeholder="Search..." style="display: none;">
                    <button type="submit" id="search-button" style="display: none;">Search</button>
                </form>
            </div>
            <div class="account">
                <a href="<?= $isLoggedIn ? 'profile.php' : 'login_register.php' ?>">
                    <img src="account.png" alt="Account Icon">
                </a>
            </div>
            <div class="cart">
                <a href="cart.php"><img src="cart.png" alt="Cart Icon"> Cart (<span id="cart-quantity"><?= $cartQuantity ?></span>)</a>
            </div>
        </div>
    </header>

    <div class="contact-container">
    <main class="contact-page">
        <h1>Contact Us</h1>
        <p>If you have any questions or need further information, feel free to contact us using the form below.</p>

        <form action="contactpage.php" method="POST" class="contact-form">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject:</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit" name="submit">Send Message</button>
        </form>
    </main>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; 2024 Element Electronics. All Rights Reserved.</p>
            <div class="subscription">
                <form id="subscription-form">
                    <input type="email" id="subscriber-email" placeholder="Subscribe to our newsletter" aria-label="Email subscription" required>
                    <button type="submit">Subscribe</button>
                </form>
            </div>
        </div>
    </footer>
    <script src="script_search.js"></script>
</body>
</html>
