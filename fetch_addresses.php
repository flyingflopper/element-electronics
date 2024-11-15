<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include('dbconnect.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $dbcnx->prepare("
    SELECT id, address_line1, address_line2, postal_code, phone_number, city, country, is_default
    FROM addresses 
    WHERE user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$addresses = [];
while ($row = $result->fetch_assoc()) {
    $addresses[] = [
        'id' => $row['id'],
        'address_line1' => $row['address_line1'],
        'address_line2' => $row['address_line2'],
        'postal_code' => $row['postal_code'],
        'phone_number' => $row['phone_number'],
        'city' => $row['city'],
        'country' => $row['country'],
        'is_default' => (int)$row['is_default']
    ];
}

echo json_encode($addresses);
?>
