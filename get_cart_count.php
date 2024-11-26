<?php
session_start();
include 'dbconnect.php';

$cart_count = 0; // Default cart count

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Use prepared statements
    $stmt = $connection->prepare("SELECT SUM(quantity) as cart_count FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $row = $result->fetch_assoc();
        $cart_count = $row['cart_count'] ? $row['cart_count'] : 0;
    }

    $stmt->close();
}

echo $cart_count; // Output only the cart count
?>
