<?php
session_start();
include 'dbconnect.php'; // Include your database connection

// Ensure the user is logged in
if (isset($_SESSION['userID'])) {
    $user_id = $_SESSION['userID'];

    // Prepare and execute the SQL statement to update last_activity
    $stmt = mysqli_prepare($connection, "
        UPDATE crafthub_users SET last_activity = NOW() WHERE user_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update last activity']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
}

mysqli_close($connection);
?>
