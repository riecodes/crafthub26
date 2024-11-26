<?php
session_start();
require 'dbconnect.php';
date_default_timezone_set('Asia/Manila');

$current_user_id = $_SESSION['userID']; // Ensure this matches your session variable
$chat_with_id = isset($_GET['chat_with_id']) ? intval($_GET['chat_with_id']) : 0;

$query = "  SELECT
                crafthub_users.user_id AS chat_with_id,
                crafthub_users.username AS chat_with_username,
                crafthub_users.last_activity,
                crafthub_users.user_profile,
                messages.message AS last_message,
                messages.message_type AS last_message_type,
                messages.created_at AS last_message_time,
                SUM(CASE WHEN messages.is_read = 0 AND messages.receiver_id = ? THEN 1 ELSE 0 END) AS unread_count
            FROM
                messages
            INNER JOIN
                crafthub_users ON crafthub_users.user_id = CASE
                    WHEN messages.sender_id = ? THEN messages.receiver_id
                    WHEN messages.receiver_id = ? THEN messages.sender_id
                END
            INNER JOIN (
                SELECT 
                    MAX(created_at) AS latest_message_time,
                    CASE 
                        WHEN sender_id = ? THEN receiver_id
                        WHEN receiver_id = ? THEN sender_id
                    END AS chat_with_id
                FROM 
                    messages
                WHERE
                    sender_id = ? OR receiver_id = ?
                GROUP BY chat_with_id
            ) AS latest_messages ON messages.created_at = latest_messages.latest_message_time
            AND crafthub_users.user_id = latest_messages.chat_with_id
            WHERE
                (messages.sender_id = ? OR messages.receiver_id = ?)
                AND crafthub_users.role = 'merchant'

            GROUP BY
                crafthub_users.user_id,
                crafthub_users.username
            ORDER BY
                last_message_time DESC
";

// Prepare the statement
$stmt = mysqli_prepare($connection, $query);

if ($stmt) {
    // Bind the parameters
    mysqli_stmt_bind_param($stmt, "iiiiiiiii", 
        $current_user_id, 
        $current_user_id, 
        $current_user_id, 
        $current_user_id, 
        $current_user_id, 
        $current_user_id, 
        $current_user_id,
        $current_user_id,
        $current_user_id
    );

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result set
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        while ($chat = mysqli_fetch_assoc($result)) {
            $unread_count = $chat['unread_count'];
            $time_ago = time() - strtotime($chat['last_message_time']);
            $time_display = $time_ago < 60 ? $time_ago . 's ago' :
                            ($time_ago < 3600 ? floor($time_ago / 60) . 'm ago' :
                            ($time_ago < 86400 ? floor($time_ago / 3600) . 'h ago' : date("d M", strtotime($chat['last_message_time']))));

            $last_activity = $chat['last_activity'];

            $last_seen_timestamp = strtotime($last_activity);
            $time_diff = time() - $last_seen_timestamp;

            $last_seen_display = "No recent activity";
            
            if ($time_diff < 3600) {
                $minutes = floor($time_diff / 60);
                $last_seen_display = $minutes <= 1 ? "a minute ago" : "$minutes minutes ago";
            } elseif ($time_diff < 86400) {
                $hours = floor($time_diff / 3600);
                $last_seen_display = $hours <= 1 ? "an hour ago" : "$hours hours ago";
            } elseif ($time_diff < 604800) {
                $days = floor($time_diff / 86400);
                $last_seen_display = $days <= 1 ? "a day ago" : "$days days ago";
            } else {
                $last_seen_display = date("F j, Y, g:i a", $last_seen_timestamp);
            }

            $last_message_display = $chat['last_message_type'] === 'image' 
                ? "Photo" 
                : (strlen($chat['last_message']) > 25 
                    ? substr($chat['last_message'], 0, 25) . "..." 
                    : $chat['last_message']);

            $unread_class = $unread_count > 0 ? 'unread' : '';

            echo '
            <li class="chat-item ' . $unread_class . '" onclick="selectChat(this, 
            \'' . htmlspecialchars($chat['chat_with_username']) . '\',  
            \'' . htmlspecialchars($chat['chat_with_id']) . '\', 
            \'' . htmlspecialchars($last_seen_display) . '\')">
                <a href="javascript:void(0);">
                    <img src="' . (isset($chat['user_profile']) && !empty($chat['user_profile']) 
                                    ? '' . ltrim(htmlspecialchars($chat['user_profile']), './') 
                                    : 'images/user.png') . '" 
                    alt="User Avatar" 
                    class="chat-avatar">
                    <div class="chat-info">
                        <h5 class="chat-name">' . htmlspecialchars($chat['chat_with_username']) . '</h5>
                        <p class="chat-preview">' . htmlspecialchars($last_message_display) . '</p>                        
                    </div>
                    <span class="chat-time">' . $time_display . '</span>
                </a>
            </li>';
            


        }
    } else {
        echo "No chats found.";
    }

    // Free result set
    mysqli_free_result($result);
} else {
    echo "Query failed: " . mysqli_error($connection);
}
?>
