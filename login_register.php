<?php
session_start();
include('dbconnect.php'); // Ensure this file correctly sets up your database connection

// Handle form submission
$cartQuantity = 0; // Default value for cart quantity
$message = '';

// Check if user is logged in to set cart quantity
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cartQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
    $stmt = $dbcnx->prepare($cartQuantityQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $cartQuantity = $row['total_quantity'] ?? 0;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['register'])) {
        // Registration process
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // Check if the username or email already exists
        $stmt = $dbcnx->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Username or email already exists.";
        } else {
            // Insert the new user into the database
            $stmt = $dbcnx->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $password);

            if ($stmt->execute()) {
                $message = "Registration successful! Please log in.";
            } else {
                $message = "Error registering user.";
            }
        }
    } elseif (isset($_POST['login'])) {
        // Login process
        $username = trim($_POST['username']);
        $password = $_POST['password'];

        // Check if the user exists
        $stmt = $dbcnx->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirect to profile.php
                header("Location: profile.php");
                exit;
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Element Electronics: Login or Register</title>
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

    <main class="login-page">
        <div class="login-container">
            <h2 id="form-title">Login or Register</h2>
            <p><?php echo $message; ?></p>
            <div class="tabs">
                <button onclick="showForm('login')">Login</button>
                <button onclick="showForm('register')">Register</button>
            </div>

            <!-- Login Form -->
            <form id="login-form" action="login_register.php" method="POST" class="login-form">
                <input type="hidden" name="login">
                <div class="form-group">
                    <label for="login-username">Username:</label>
                    <input type="text" id="login-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password:</label>
                    <input type="password" id="login-password" name="password" required>
                </div>
                <button type="submit" class="login-button">Login</button>
            </form>

            <!-- Registration Form -->
            <form id="register-form" action="login_register.php" method="POST" class="login-form" style="display: none;">
                <input type="hidden" name="register">
                <div class="form-group">
                    <label for="register-username">Username:</label>
                    <input type="text" id="register-username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email:</label>
                    <input type="email" id="register-email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Password:</label>
                    <input type="password" id="register-password" name="password" required>
                </div>
                <button type="submit" class="login-button">Register</button>
            </form>
        </div>
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
    <script>
        function showForm(formType) {
            document.getElementById("login-form").style.display = formType === "login" ? "block" : "none";
            document.getElementById("register-form").style.display = formType === "register" ? "block" : "none";
            document.getElementById("form-title").innerText = formType === "login" ? "Login" : "Register";
        }

        // Show the login form by default
        document.addEventListener("DOMContentLoaded", () => showForm('login'));
    </script>
</body>
</html>
