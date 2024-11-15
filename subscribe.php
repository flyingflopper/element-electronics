<?php
include('dbconnect.php');

// Check if email is set
if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email address.']);
        exit;
    }

    // Check if the email is already subscribed
    $checkStmt = $dbcnx->prepare("SELECT COUNT(*) FROM newsletter_subscribers WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo json_encode(['success' => false, 'error' => 'Email is already subscribed.']);
        exit;
    }

    // Prepare statement to prevent SQL injection
    $stmt = $dbcnx->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to subscribe.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Email is required.']);
}
?>
