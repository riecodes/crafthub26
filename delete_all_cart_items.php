<?php
session_start();
include 'dbconnect.php';

if (isset($_SESSION['userID'])) {
    $user_id = $_SESSION['userID'];

    // Prepare the DELETE query
    $deleteQuery = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $connection->prepare($deleteQuery);
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
}
?>
