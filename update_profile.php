<?php
session_start();
include('dbconnect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login_register.php");
    exit;
}

// Continue only if there is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect POST data with basic validation
    $username = !empty($_POST['username']) ? $_POST['username'] : null;
    $email = !empty($_POST['email']) ? $_POST['email'] : null;
    $birthday = !empty($_POST['birthday']) ? $_POST['birthday'] : null;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : null;
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Prepare SQL Update Statement
    $stmt = $dbcnx->prepare("UPDATE users SET username = ?, email = ?, birthday = ?, gender = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $email, $birthday, $gender, $user_id);

    // Execute and Check if Successful
    if ($stmt->execute()) {
        // Redirect to profile.php with a success message
        $_SESSION['message'] = 'Profile updated successfully!';
        header("Location: profile.php");
        exit;
    } else {
        // Handle possible errors (ideally log these errors)
        echo "Error updating record: " . $dbcnx->error;
    }
} else {
    // Not a POST request, redirect back to profile or other appropriate page
    header("Location: profile.php");
    exit;
}

?>
