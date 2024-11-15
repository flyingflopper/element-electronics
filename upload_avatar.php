<?php
session_start();
include('dbconnect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit;
}

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $avatar = $_FILES['avatar'];
    $originalFilename = basename($avatar['name']);
    $fileTmpName = $avatar['tmp_name'];
    $fileType = $avatar['type'];
    $fileSize = $avatar['size'];

    // Define the allowed file types and size limit
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $sizeLimit = 5000000; // 5 MB

    if (in_array($fileType, $allowedTypes) && $fileSize <= $sizeLimit) {
        // Define where to save the file
        $uploadDir = 'uploads/';
        $filename = uniqid() . '-' . $originalFilename; // Ensuring a unique filename
        $uploadPath = $uploadDir . $filename;

        // Check if the upload directory exists, if not create it
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // true allows creation of nested directories
        }

        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            // Update user's avatar path in the database
            $stmt = $dbcnx->prepare("UPDATE users SET avatar_path = ? WHERE id = ?");
            $stmt->bind_param("si", $uploadPath, $userId);
            if ($stmt->execute()) {
                $_SESSION['avatar_path'] = $uploadPath; // Update avatar path in session
                header("Location: profile.php"); // Redirect back to profile page
                exit;
            } else {
                echo "Failed to update database.";
            }
        } else {
            echo "Failed to move uploaded file.";
        }
    } else {
        echo "Invalid file type or size too large.";
    }
} else {
    echo "No file uploaded or there was an error.";
}
?>
