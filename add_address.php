<?php
session_start();
include('dbconnect.php');

// Set content type for the response to JSON
header('Content-Type: application/json');

// Send everything to error log instead of output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

// Get user ID from session
$userId = $_SESSION['user_id'];

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from POST request
    $address_line1 = $_POST['address_line1'] ?? '';
    $address_line2 = $_POST['address_line2'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $city = $_POST['city'] ?? '';
    $country = $_POST['country'] ?? '';

    // Simple validation
    if (empty($address_line1) || empty($postal_code) || empty($phone_number) || empty($city) || empty($country)) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
        exit;
    }

    // Prepare SQL statement to insert address
    $stmt = $dbcnx->prepare("
        INSERT INTO addresses (user_id, address_line1, address_line2, postal_code, phone_number, city, country)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssss", $userId, $address_line1, $address_line2, $postal_code, $phone_number, $city, $country);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Address added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add address: ' . $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
