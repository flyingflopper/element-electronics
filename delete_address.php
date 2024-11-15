<?php
session_start();
include('dbconnect.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$addressId = $_POST['id'];
$stmt = $dbcnx->prepare("DELETE FROM addresses WHERE id = ?");
$stmt->bind_param("i", $addressId);
$success = $stmt->execute();

echo json_encode(['success' => $success]);
