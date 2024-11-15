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
$delta = intval($_POST['delta']);

// Fetch the current quantity of the item
$stmt = $dbcnx->prepare("SELECT quantity FROM carts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $cartItemId, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $newQuantity = $row['quantity'] + $delta;

    if ($newQuantity > 0) {
        // Update quantity if the new quantity is positive
        $updateStmt = $dbcnx->prepare("UPDATE carts SET quantity = ? WHERE id = ? AND user_id = ?");
        $updateStmt->bind_param("iii", $newQuantity, $cartItemId, $user_id);
        $updateStmt->execute();
    } else {
        // Remove item if the quantity is zero or less
        $deleteStmt = $dbcnx->prepare("DELETE FROM carts WHERE id = ? AND user_id = ?");
        $deleteStmt->bind_param("ii", $cartItemId, $user_id);
        $deleteStmt->execute();
    }
    echo "Success";
} else {
    echo "Item not found in cart.";
}
