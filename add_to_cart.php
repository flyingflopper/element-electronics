<?php
session_start();
header('Content-Type: application/json'); // Ensure JSON response

include('dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in.']);
    exit;
}

if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    die(json_encode(['success' => false, 'error' => 'Missing product details']));
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$color = isset($_POST['color']) ? $_POST['color'] : 'Default'; // Default value if color is not provided
$storage = isset($_POST['storage']) ? $_POST['storage'] : 'Default'; // Default value if storage is not provided

// Fetch the base price from the product table
$priceQuery = $dbcnx->prepare("SELECT price FROM products WHERE product_id = ?");
$priceQuery->bind_param("i", $product_id);
$priceQuery->execute();
$priceResult = $priceQuery->get_result();
if ($priceResult->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'Product not found']);
    exit;
}
$priceData = $priceResult->fetch_assoc();
$basePrice = $priceData['price'];

// Calculate price adjustment based on storage
$priceAdjustment = 0;
if (in_array($storage, ['512GB', '1TB', '2TB'])) {
    switch ($storage) {
        case '512GB':
            $priceAdjustment = 100;
            break;
        case '1TB':
            $priceAdjustment = 200;
            break;
        case '2TB':
            $priceAdjustment = 400;
            break;
    }
}

// Total price for the product based on the storage and quantity
$totalPrice = ($basePrice + $priceAdjustment) * $quantity;

// Check if the product is already in the cart with the same configuration
$stmt = $dbcnx->prepare("SELECT id FROM carts WHERE user_id = ? AND product_id = ? AND color = ? AND storage = ?");
$stmt->bind_param("iiss", $user_id, $product_id, $color, $storage);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing cart item
    $stmt = $dbcnx->prepare("UPDATE carts SET quantity = quantity + ?, price = ? WHERE user_id = ? AND product_id = ? AND color = ? AND storage = ?");
    $stmt->bind_param("idiiis", $quantity, $totalPrice, $user_id, $product_id, $color, $storage);
} else {
    // Insert new cart item
    $stmt = $dbcnx->prepare("INSERT INTO carts (user_id, product_id, quantity, price, color, storage) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidss", $user_id, $product_id, $quantity, $totalPrice, $color, $storage);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product added to cart!', 'totalPrice' => $totalPrice]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update cart.', 'dbError' => $stmt->error]);
}

$dbcnx->close();
?>
