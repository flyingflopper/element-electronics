<?php
session_start();
include('dbconnect.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access or missing data']);
    exit;
}

$userId = $_SESSION['user_id'];
$addressId = $_POST['id'];

// Reset all addresses default status
$dbcnx->query("UPDATE addresses SET is_default = FALSE WHERE user_id = $userId");

// Set the chosen address as default
$stmt = $dbcnx->prepare("UPDATE addresses SET is_default = TRUE WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $addressId, $userId);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
