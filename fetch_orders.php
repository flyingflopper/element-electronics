<?php
session_start();
include('dbconnect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

$query = "SELECT order_id, order_date, status, total FROM orders WHERE user_id = ?";
$stmt = $dbcnx->prepare($query);

if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $dbcnx->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode(['orders' => $orders]);
?>
