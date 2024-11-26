<?php
session_start();
include 'dbconnect.php';

if (isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $stmt = $connection->prepare("SELECT COUNT(*) AS unread_count FROM messages WHERE receiver_id = ? AND is_read = 0");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $row = $result->fetch_assoc();
        $unread_count = $row['unread_count'] ? $row['unread_count'] : 0;
    }

    echo json_encode(['unread_count' => $unread_count]);
} else {
    echo json_encode(['error' => 'User not logged in']);
}
?>
