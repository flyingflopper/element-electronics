<?php
session_start();
include('dbconnect.php');

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $dbcnx->prepare("SELECT SUM(quantity) AS total_quantity FROM carts WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['quantity' => $row['total_quantity']]);
    } else {
        echo json_encode(['quantity' => 0]);
    }
} else {
    echo json_encode(['quantity' => 0]);
}
?>
