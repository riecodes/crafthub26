<?php
require 'dbconnect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the request is a JSON request
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

    if (strpos($contentType, 'application/json') !== false) {
        // Receive the RAW post data.
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);

        $sender_id = $_SESSION['userID'];
        $sender_type = '';
        $receiver_id = $data['receiver_id'];
        $receiver_type = '';
        $message = $data['message'] ?? '';
        $message_type = 'text';
        $media_path = null;

        // Query to fetch the sender's role
        $query = "SELECT role FROM crafthub_users WHERE user_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $sender_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sender = $result->fetch_assoc();

        // Check sender role
        if ($sender && $sender['role'] === 'merchant') {
            $sender_type = 'merchant';
        } else {
            $sender_type = 'user';
        }

        // Query to fetch the receiver's role
        $query = "SELECT role FROM crafthub_users WHERE user_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $receiver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $receiver = $result->fetch_assoc();

        // Check receiver role
        if ($receiver && $receiver['role'] === 'merchant') {
            $receiver_type = 'merchant';
        } else {
            $receiver_type = 'user';
        }

        // Insert the message into the database
        $stmt = $connection->prepare("INSERT INTO messages (sender_id, sender_type, receiver_id, receiver_type, message, media_path, message_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isissss", $sender_id, $sender_type, $receiver_id, $receiver_type, $message, $media_path, $message_type);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message.']);
        }
    } elseif (!empty($_FILES['media'])) {
        // Handle media upload
        $sender_id = $_SESSION['userID'];
        $sender_type = '';
        $receiver_id = $_POST['receiver_id'];
        $receiver_type = '';
        $message = $data['message'] ?? '';
        $message_type = 'media';
        $media_path = null;

        // Query to fetch the sender's role
        $query = "SELECT role FROM crafthub_users WHERE user_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $sender_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $sender = $result->fetch_assoc();

        // Check sender role
        if ($sender && $sender['role'] === 'merchant') {
            $sender_type = 'merchant';
        } else {
            $sender_type = 'user';
        }

        // Query to fetch the receiver's role
        $query = "SELECT role FROM crafthub_users WHERE user_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $receiver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $receiver = $result->fetch_assoc();

        // Check receiver role
        if ($receiver && $receiver['role'] === 'merchant') {
            $receiver_type = 'merchant';
        } else {
            $receiver_type = 'user';
        }
        
        $file = $_FILES['media'];
        $media_dir = 'uploads/';
        $file_name = time() . '_' . basename($file['name']);
        $file_path = $media_dir . $file_name;
        $file_type = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $media_path = $file_path;
            $message_type = in_array($file_type, ['jpg', 'png', 'jpeg', 'gif']) ? 'image' : 'video';

            // Insert the message with media into the database
            $stmt = $connection->prepare("INSERT INTO messages (sender_id, sender_type, receiver_id, receiver_type, message, media_path, message_type) 
                                            VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isissss", $sender_id, $sender_type, $receiver_id, $receiver_type, $message, $media_path, $message_type);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'media_path' => $media_path]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to send message with media.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to upload media.']);
        }
    }
}
?>
