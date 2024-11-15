<?php
session_start();
include('dbconnect.php');

header('Content-Type: application/json');
ini_set('display_errors', 0); // Turn off error display
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.log');

// Ensure user is authenticated and order_id is provided
if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    error_log("Access denied: user_id or order_id missing");
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);

// Fetch order details from the orders table for the given order_id and user_id
$orderQuery = "SELECT name, country, address1, address2, postal_code, phone, shipping_type, payment_type, billing_address1, billing_address2, total 
               FROM orders 
               WHERE order_id = ? AND user_id = ?";
$orderStmt = $dbcnx->prepare($orderQuery);
$orderStmt->bind_param("ii", $order_id, $user_id);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();
$orderDetails = $orderResult->fetch_assoc();

if (!$orderDetails) {
    error_log("Order details not found for order_id: $order_id");
    echo json_encode(['error' => 'Order details not found']);
    exit;
}

// Fetch order items including product details from products and order_items tables
$itemQuery = "SELECT p.name as product_name, oi.quantity, oi.price, oi.color, oi.storage, p.image_url as image_url 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.product_id
              WHERE oi.order_id = ?";
$itemStmt = $dbcnx->prepare($itemQuery);
$itemStmt->bind_param("i", $order_id);
$itemStmt->execute();
$itemResult = $itemStmt->get_result();

$items = [];
while ($row = $itemResult->fetch_assoc()) {
    $items[] = [
        'product_name' => $row['product_name'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'color' => $row['color'],
        'storage' => $row['storage'],
        'image_url' => $row['image_url']
    ];
}

if (empty($items)) {
    error_log("No items found for order_id: $order_id");
    echo json_encode(['error' => 'No items found']);
    exit;
}

// Prepare the JSON response including order and items details
$response = [
    'order' => [
        'order_id' => $order_id,
        'name' => $orderDetails['name'],
        'country' => $orderDetails['country'],
        'address1' => $orderDetails['address1'],
        'address2' => $orderDetails['address2'],
        'postal_code' => $orderDetails['postal_code'],
        'phone' => $orderDetails['phone'],
        'shipping_type' => $orderDetails['shipping_type'],
        'payment_type' => $orderDetails['payment_type'],
        'billing_address1' => $orderDetails['billing_address1'],
        'billing_address2' => $orderDetails['billing_address2'],
        'total' => $orderDetails['total']
    ],
    'items' => $items
];

echo json_encode($response);
?>
