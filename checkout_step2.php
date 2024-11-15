<?php
session_start();
include('dbconnect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data for the session
$userQuery = "SELECT * FROM users WHERE id = ?";
$stmt = $dbcnx->prepare($userQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

$defaultAddressQuery = "SELECT * FROM addresses WHERE user_id = ? AND is_default = 1";
$stmt = $dbcnx->prepare($defaultAddressQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$defaultAddressResult = $stmt->get_result();
$defaultAddress = $defaultAddressResult->fetch_assoc();

// Store total quantity and cart items for display
$quantityQuery = "SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?";
$stmt = $dbcnx->prepare($quantityQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$cartQuantity = $row['total_quantity'] ?? 0;

$cartItemsQuery = "
    SELECT c.id, p.product_id, p.name, p.price AS base_price, p.thumbnail_urls AS thumbnail, c.quantity, c.color, c.storage
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
            return $basePrice + 100;
        case '1TB':
            return $basePrice + 200;
        case '2TB':
            return $basePrice + 400;
        default:
            return $basePrice;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Information - Element Electronics</title>
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
            <div class="cart">
                <a href="cart.php"><img src="cart.png" alt="Cart Icon"> Cart (<span id="cart-quantity"><?= $cartQuantity ?></span>)</a>
            </div>
        </div>
    </header>
    
    <div class="checkout-container">
        <form action="checkout_confirmation.php" method="post" class="checkout-form">
            <h2>Payment Information</h2>
            <label for="shipping_type">Shipping Type:</label>
            <input type="text" id="shipping_type" name="shipping_type" required>
            
            <label for="payment_type">Payment Type:</label>
            <select id="payment_type" name="payment_type" required>
                <option value="">Select a payment method...</option>
                <option value="Credit Card">Credit Card</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Bank Transfer</option>
            </select>
            
            <label for="payment_details">Card Number:</label>
            <input type="text" id="card-number" name="card_number" maxlength="16" minlength="16" pattern="\d{16}" required placeholder="Enter 16-digit card number"/>
            
            <label for="billing_address1">Billing Address Line 1:</label>
            <input type="text" id="billing_address1" name="billing_address1" value="<?= htmlspecialchars($defaultAddress['address_line1'] ?? ''); ?>" required>

            <label for="billing_address2">Billing Address Line 2:</label>
            <input type="text" id="billing_address2" name="billing_address2" value="<?= htmlspecialchars($defaultAddress['address_line2'] ?? ''); ?>">

            <label for="total">Total Amount:</label>
            <input type="number" id="total" name="total" value="<?= round($cartTotal, 2); ?>" step="0.01" readonly>

            <button type="submit">Confirm Payment</button>
        </form>

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
</body>
</html>
