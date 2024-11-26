<?php
session_start();
require 'dbconnect.php';

$current_user_id = $_SESSION['userID'];
$chat_with_id = isset($_GET['chat_with_id']) ? intval($_GET['chat_with_id']) : 0;

// Check if chat_with_id is provided
if (!$chat_with_id) {
    echo json_encode(['error' => 'Chat ID not provided']);
    exit;
}

$query = "
    SELECT
        messages.message AS content,
        messages.created_at AS time,
        messages.message_type AS type,
        messages.media_path,
        CASE 
            WHEN messages.sender_id = ? THEN 1
            ELSE 0
        END AS is_sender
    FROM
        messages
    WHERE
        (messages.sender_id = ? AND messages.receiver_id = ?)
        OR (messages.sender_id = ? AND messages.receiver_id = ?)
    ORDER BY
        messages.created_at ASC
";

$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "iiiii", 
        $current_user_id, 
        $current_user_id, 
        $chat_with_id, 
        $chat_with_id, 
        $current_user_id
    );

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $messages = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $messages[] = [
                'content' => htmlspecialchars($row['content']),
                'time' => date('H:i', strtotime($row['time'])),
                'is_sender' => $row['is_sender'],
                'type' => $row['type'],
                'media_path' => $row['media_path']
            ];
        }
        echo json_encode($messages);
    } else {
        echo json_encode(['error' => 'No messages found']);
    }

    mysqli_stmt_close($stmt);

    $update_query = "
        UPDATE messages
        SET is_read = 1, read_date = NOW()
        WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
    ";

    $update_stmt = mysqli_prepare($connection, $update_query);
    mysqli_stmt_bind_param(
        $update_stmt,
        "ii",
        $chat_with_id,
        $current_user_id
    );
    mysqli_stmt_execute($update_stmt);
    mysqli_stmt_close($update_stmt);
} else {
    echo json_encode(['error' => mysqli_error($connection)]);
}
?>
