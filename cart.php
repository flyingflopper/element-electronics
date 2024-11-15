<?php
session_start();
include('dbconnect.php');

$isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

// Get total quantity in the cart for the header
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Get total quantity in the cart for the header
$quantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($quantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartQuantity = $row['total_quantity'] ?? 0;

// Fetch user's cart items from the database
$cartItemsQuery = "
    SELECT c.id, p.product_id, p.name, p.price AS base_price, p.thumbnail_urls AS thumbnail, c.quantity, c.color, c.storage
    FROM carts c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?";
$stmt = $dbcnx->prepare($cartItemsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cartTotal = 0;
$cartItems = [];

while ($item = $result->fetch_assoc()) {
    $adjustedPrice = adjustPriceBasedOnStorage($item['base_price'], $item['storage']);
    $itemTotal = $adjustedPrice * $item['quantity'];
    $cartTotal += $itemTotal;

    $thumbnailUrls = explode(',', $item['thumbnail']);
    $mainThumbnail = trim($thumbnailUrls[0]);

    $cartItems[] = [
        'id' => $item['id'],
        'name' => $item['name'],
        'price' => $adjustedPrice,
        'quantity' => $item['quantity'],
        'color' => $item['color'],
        'storage' => $item['storage'],
        'thumbnail' => $mainThumbnail,
        'itemTotal' => $itemTotal
    ];
}

// Function to adjust price based on storage options
function adjustPriceBasedOnStorage($basePrice, $storage) {
    switch ($storage) {
        case '512GB':
            return $basePrice + 100; // Adjust price as per your pricing structure
        case '1TB':
            return $basePrice + 200;
        case '2TB':
            return $basePrice + 400;
        default:
            return $basePrice; // No adjustment if no storage option matched
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Element Electronics</title>
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

    <section class="cart-page-container">
        <h1>Shopping Cart</h1>
        <div id="cart-items-container">
            <?php if (!empty($cartItems)): ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-thumbnail">
                        <div class="cart-item-details">
                            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
                            <?php if ($item['color'] !== 'Default'): ?> <!-- Only display if color is not 'Default' -->
                                <p>Color: <?php echo htmlspecialchars($item['color']); ?></p>
                            <?php endif; ?>
                            <?php if ($item['storage'] !== 'Default'): ?> <!-- Only display if storage is not 'Default' -->
                                <p>Storage: <?php echo htmlspecialchars($item['storage']); ?></p>
                            <?php endif; ?>
                            <p>Unit Price: $<?php echo number_format($item['price'], 2); ?></p>
                            <div class="cart-item-controls">
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">-</button>
                                <span><?php echo $item['quantity']; ?></span>
                                <button onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                                <button onclick="removeItem(<?php echo $item['id']; ?>)" class="remove-button">Remove</button>
                            </div>
                            <p>Total: $<?php echo number_format($item['itemTotal'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="cart-total">
                    <p><strong>Total:</strong> $<span id="cart-total"><?php echo number_format($cartTotal, 2); ?></span></p>
                    <button id="checkout-button" class="checkout-button" onclick="proceedToCheckout()">Proceed to Checkout</button>
                </div>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
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
    <script>
    function updateQuantity(cartItemId, delta) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                location.reload(); // Reload page to reflect changes
            }
        };
        xhr.send(`cartItemId=${cartItemId}&delta=${delta}`);
    }

    function removeItem(cartItemId) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "remove_item.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function () {
            if (xhr.status === 200) {
                location.reload();
            }
        };
        xhr.send(`cartItemId=${cartItemId}`);
    }

    function proceedToCheckout() {
        window.location.href = 'checkout_step1.php';
    }
    </script>
</body>
</html>
