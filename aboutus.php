<?php
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Element Electronics</title>
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

    <main class="about-page">
        <!-- Happy Employees Image -->
        <div class="about-image">
            <img src="happyemployees.jpg" alt="Happy Employees">
        </div>

        <h1>About Us</h1>
        <section class="company-overview">
            <h2>Who We Are</h2>
            <p>Founded in the hearts of NTU's EEE building, the founders of Element Electronics collaborated for one single purpose: 
                complete the notorious IE4727 project. Through much consideration, they came to the conclusion that creating an electronics e-commerce
                website would be the best (and easiest) way through. Little did they know what lies ahead...
            </p>
        </section>

        <section class="mission-statement">
            <h2>Our Mission</h2>
            <p>Our mission is simple: survive. What started as a quest to conquer the IE4727 project has spiraled into an epic saga of late-night coding marathons, countless cups of coffee, and a newfound appreciation for the fine art of debugging. Along the way, we've realized our true calling: to create an electronics e-commerce website so revolutionary, so user-friendly, that even our professors might shed a tear of pride. <br> <br>

In short, our mission is to bring you the best gadgets and gizmos, while proving to the world (and ourselves) that we can turn a desperate bid for project completion into something truly electrifying.
                
            </p>
        </section>

        <section class="team">
            <h2>Meet the Team</h2>
            <div class="team-members">
                <div class="team-member">
                    <img src="images/team1.jpg" alt="Nabil">
                    <h3>Muhammad Nabil</h3>
                    <p>CEO & Founder</p>
                </div>
                <div class="team-member">
                    <img src="images/team2.jpg" alt="Matin">
                    <h3>Ahmadul Matin</h3>
                    <p>Chief Technology Officer</p>
                </div>
                <!-- Add more team members as needed -->
            </div>
        </section>

        <section class="contact-info">
            <h2>Get in Touch</h2>
            <p>For inquiries, feel free to <a href="contactpage.php">Contact Us</a>.</p>
        </section>
    </main>

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
