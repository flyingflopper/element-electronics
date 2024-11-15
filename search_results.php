<?php
session_start();
include('dbconnect.php');

$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

$query = $_GET['query'] ?? ''; // Retrieve the search term from the URL

// Sanitize the input
$searchTerm = htmlspecialchars($query);

// Get total quantity in the cart for the header
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
$cartQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($cartQuantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartQuantity = $row['total_quantity'] ?? 0;

// Prepare SQL query to fetch products by name only
$searchQuery = "SELECT * FROM products WHERE name LIKE ?";
$stmt = $dbcnx->prepare($searchQuery);
if (!$stmt) {
    die("Prepare failed: " . $dbcnx->error);
}
$likeTerm = '%' . $searchTerm . '%';
$stmt->bind_param("s", $likeTerm);
$stmt->execute();
if ($stmt->error) {
    die("Execution error: " . $stmt->error);
}
$result = $stmt->get_result();
$products = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Element Electronics: Shop Online for Electronics</title>
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

    <!-- Main search content -->
    <section class="search-content">
        <div class="search-container">
            <?php if (!empty($query)): ?>
                <h1>Search Results for: <?= htmlspecialchars($query) ?></h1>
            <?php else: ?>
                <h1>Search Results</h1>
            <?php endif; ?>

            <div class="search-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-item">
                        <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p>$<?= number_format($product['price'], 2) ?></p>
                        <a href="product.php?id=<?= urlencode($product['product_id']) ?>"><button>View Details</button></a>
                        <button onclick="addToCart(<?= $product['product_id'] ?>, 'Gold', '256GB', 1)">Add to Cart</button>
                    </div>
                <?php endforeach; ?>
                <?php if (count($products) === 0): ?>
                    <p>No products found matching your query.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

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
    <script type="text/javascript">
        function addToCart(productId, color, storage, quantity) {
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&color=${color}&storage=${storage}&quantity=${quantity}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Product added to cart!');
                    updateCartQuantity(); // Update the cart quantity displayed
                } else {
                    alert(data.error || "Failed to add product to cart.");
                }
            })
            .catch(error => alert('Error adding product to cart: ' + error.message));
        }

        function updateCartQuantity() {
            fetch('cart_quantity_fetch.php')
            .then(response => response.json())
            .then(data => {
                document.getElementById('cart-quantity').innerText = data.quantity;
            })
            .catch(error => console.error('Error updating cart quantity:', error));
        }

    </script>
</body>
</html>