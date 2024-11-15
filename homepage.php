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

// Function to update featured products
function updateFeaturedProducts($dbcnx) {
    $duration = "+1 day"; // Featured products are updated every day
    $newFeaturedTime = date('Y-m-d H:i:s', strtotime($duration));
    $dbcnx->query("UPDATE products SET featured_until = NULL"); // Reset all first
    $dbcnx->query("UPDATE products SET featured_until = '$newFeaturedTime' ORDER BY RAND() LIMIT 8");
}

// Get featured products
$currentTime = date('Y-m-d H:i:s');
$result = $dbcnx->query("SELECT * FROM products WHERE featured_until > '$currentTime' LIMIT 8");
$featuredProducts = $result->fetch_all(MYSQLI_ASSOC);

// Check if there are less than 8 featured products or none
if (count($featuredProducts) < 8) {
    updateFeaturedProducts($dbcnx);
    $result = $dbcnx->query("SELECT * FROM products WHERE featured_until > '$currentTime' LIMIT 8");
    $featuredProducts = $result->fetch_all(MYSQLI_ASSOC);
}
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

    <div class="carousel">
        <div class="carousel-container">
            <div class="carousel-slide active">
                <img src="carousel-image1.jpg" alt="Carousel Image 1">
                <div class="overlay"></div>
                <div class="carousel-text">
                    <h2>Experience Cutting-Edge Technology</h2>
                    <p>Explore the latest laptops, smartphones, and accessories</p>
                    <a href="catalog.php" class="btn">Shop Now</a>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="carousel-image2.jpg" alt="Carousel Image 2">
                <div class="overlay"></div>
                <div class="carousel-text">
                    <h2>Upgrade Your Workspace</h2>
                    <p>Discover our collection of monitors, keyboards, and more</p>
                    <a href="catalog.php" class="btn">Shop Now</a>
                </div>
            </div>
            <div class="carousel-slide">
                <img src="carousel-image3.jpg" alt="Carousel Image 3">
                <div class="overlay"></div>
                <div class="carousel-text">
                    <h2>Unbeatable Prices on Top Brands</h2>
                    <p>Get the best deals on Apple, Dell, Samsung, and more</p>
                    <a href="catalog.php" class="btn">Shop Now</a>
                </div>
            </div>
        </div>
        <a class="prev" onclick="moveSlide(-1)">&#10094;</a>
        <a class="next" onclick="moveSlide(1)">&#10095;</a>
    </div>
    
    <section class="products">
        <div class="container">
            <h2>Featured Products</h2>
            <div class="product-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="product-item">
                        <?php
                        $imageURLs = explode(',', $product['thumbnail_urls']);
                        $firstImageURL = trim($imageURLs[0]); // Trimming to remove any accidental whitespace
                        ?>
                        <img src="<?= htmlspecialchars($firstImageURL) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p>From S$<?= number_format($product['price'], 2) ?></p>
                        <?php
                        // Check the category and adjust the button accordingly
                        if (in_array(strtolower($product['category']), ['phones', 'tablets', 'laptops'])) {
                            echo '<button onclick="addToCart(' . $product['product_id'] . ', \'Gold\', \'256GB\', 1)">Add to Cart</button>';
                        } else {
                            echo '<button onclick="addToCart(' . $product['product_id'] . ', \'Default\', \'Default\', 1)">Add to Cart</button>';
                        }
                        ?>
                    </div>
                <?php endforeach; ?>
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
    <script src="script.js"></script>
    <script src="script_search.js"></script>
    <script>
        function addToCart(productId, color, storage, quantity) {
            console.log(`Adding to cart: ${productId}, Color: ${color}, Storage: ${storage}, Quantity: ${quantity}`);
            fetch('add_to_cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&color=${color}&storage=${storage}&quantity=${quantity}`
            })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Product added to cart!');
                            // Optionally update cart quantity displayed in header dynamically
                            updateCartQuantity();
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
