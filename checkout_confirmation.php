<?php
session_start();
include('dbconnect.php');

ob_start(); // Start output buffering

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get total quantity in the cart for the header
$cartQuantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($cartQuantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartQuantity = $row['total_quantity'] ?? 0;

// Fetch user shipping information (if stored in the database)
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $dbcnx->prepare($userQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
$email = $user['email'];  // User's email address

// Retrieve data from session set in checkout_step1.php
$name = $_SESSION['name'] ?? '';
$country = $_SESSION['country'] ?? '';
$address1 = $_SESSION['address1'] ?? '';
$address2 = $_SESSION['address2'] ?? '';
$postal_code = $_SESSION['postal_code'] ?? '';
$phone = $_SESSION['phone'] ?? '';

// Retrieve POST data
$shipping_type = $_POST['shipping_type'] ?? '';
$payment_type = $_POST['payment_type'] ?? '';
$billing_address1 = $_POST['billing_address1'] ?? '';
$billing_address2 = $_POST['billing_address2'] ?? '';
$total = $_POST['total'] ?? 0.0;

$cartItemsQuery = "
SELECT c.product_id, p.name, p.price AS base_price, p.thumbnail_urls AS thumbnail, c.quantity, c.color, c.storage
FROM carts c
JOIN products p ON c.product_id = p.product_id
WHERE c.user_id = ?";
$stmt = $dbcnx->prepare($cartItemsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartResult = $stmt->get_result();

$cartTotal = 0;
$cartItems = [];
while ($item = $cartResult->fetch_assoc()) {
    $adjustedPrice = adjustPriceBasedOnStorage($item['base_price'], $item['storage']);
    $itemTotal = $adjustedPrice * $item['quantity'];
    $cartTotal += $itemTotal;

    $thumbnailUrls = explode(',', $item['thumbnail']);
    $mainThumbnail = trim($thumbnailUrls[0]);

    $cartItems[] = [
        'id' => $item['product_id'],
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
            return $basePrice + 100;
        case '1TB':
            return $basePrice + 200;
        case '2TB':
            return $basePrice + 400;
        default:
            return $basePrice;
    }
}

// Insert data into orders table
$sql = "INSERT INTO orders (user_id, name, country, address1, address2, postal_code, phone, shipping_type, payment_type, billing_address1, billing_address2, total, order_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
$stmt = $dbcnx->prepare($sql);
$stmt->bind_param("issssssssssd", $user_id, $name, $country, $address1, $address2, $postal_code, $phone, $shipping_type, $payment_type, $billing_address1, $billing_address2, $total);

if ($stmt->execute()) {
    $order_id = $stmt->insert_id;
    foreach ($cartItems as $item) {
        $insertItemSql = "INSERT INTO order_items (order_id, product_id, quantity, price, color, storage) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $dbcnx->prepare($insertItemSql);
        $stmt->bind_param("iiidss", $order_id, $item['id'], $item['quantity'], $item['price'], $item['color'], $item['storage']);
        $stmt->execute();
    }

    // Empty cart from session
    unset($_SESSION['cart_items']); // Clear session-based cart

    // Empty cart from database
    $deleteCartQuery = "DELETE FROM carts WHERE user_id = ?";
    $stmt = $dbcnx->prepare($deleteCartQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $_SESSION['order_processed'] = true; // Set a flag that the order is processed
    $_SESSION['order_id'] = $order_id; // Save order ID for display
} else {
    echo "<p>Error placing order: " . $stmt->error . "</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="styles.css">
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

    <div class="order-details-wrapper">
        <!-- Left side: Order Details in a grid -->
        <div class="order-details-container">
            <h2>Order Confirmed</h2>
            <div class="order-details-grid">
                <p><strong>Order ID:</strong> <?= htmlspecialchars($order_id) ?></p>
                <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
                <p><strong>Country:</strong> <?= htmlspecialchars($country) ?></p>
                <p><strong>Address 1:</strong> <?= htmlspecialchars($address1) ?></p>
                <p><strong>Address 2:</strong> <?= htmlspecialchars($address2) ?></p>
                <p><strong>Postal Code:</strong> <?= htmlspecialchars($postal_code) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>
                <p><strong>Shipping Type:</strong> <?= htmlspecialchars($shipping_type) ?></p>
                <p><strong>Payment Type:</strong> <?= htmlspecialchars($payment_type) ?></p>
                <p><strong>Billing Address 1:</strong> <?= htmlspecialchars($billing_address1) ?></p>
                <p><strong>Billing Address 2:</strong> <?= htmlspecialchars($billing_address2) ?></p>
                <p><strong>Total Amount:</strong> S$<?= number_format($total, 2) ?></p>
            </div>
            <button type="button" onclick="location.href='homepage.php'">Return to Homepage</a>
        </div>

        <!-- Right side: Cart Summary -->
        <div class="cart-summary">
            <h2>Order Summary</h2>
            <?php if (count($cartItems) > 0): ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($item['thumbnail']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="cart-thumbnail">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <?php if ($item['color'] !== 'Default'): ?>
                                <p><strong>Color: </strong><?php echo htmlspecialchars($item['color']); ?></p>
                            <?php endif; ?>
                            <?php if ($item['storage'] !== 'Default'): ?>
                                <p><strong>Storage: </strong><?php echo htmlspecialchars($item['storage']); ?></p>
                            <?php endif; ?>
                            <p><strong>Unit Price: </strong>$<?php echo number_format($item['price'], 2); ?></p>
                            <p><strong>Quantity: </strong><?php echo $item['quantity']; ?></p>
                            <p><strong>Total: </strong>$<?php echo number_format($item['itemTotal'], 2); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p><strong>Total Order Price:</strong> $<?php echo number_format($cartTotal, 2); ?></p>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
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

<?php
$htmlContent = ob_get_contents(); // Store the buffered content into a variable
ob_end_clean(); // Clean the buffer and turn off buffering

// Email section
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= 'From: Element Electronics <f32ee@localhost>' . "\r\n";

if(mail($email, "Order Confirmation", $htmlContent, $headers)) {
    // Set session variables if needed
    $_SESSION['order_processed'] = true;
    $_SESSION['order_id'] = $order_id;
    // Maybe redirect or display a message
    echo "<script>alert('Email sent successfully.');</script>";
} else {
    echo "<script>alert('Failed to send email.');</script>";
}

// Finally display the content
echo $htmlContent;
?>