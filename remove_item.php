<?php
session_start();
include('dbconnect.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Unauthorized";
    exit;
}

$user_id = $_SESSION['user_id'];
$cartItemId = $_POST['cartItemId'];

// Delete the item from the user's cart
$stmt = $dbcnx->prepare("DELETE FROM carts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cartItemId, $user_id);
if ($stmt->execute()) {
    echo "Item removed";
} else {
    echo "Error removing item";
}
?>
