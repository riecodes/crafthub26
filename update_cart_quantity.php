<?php
session_start();
include 'dbconnect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['userID'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$cart_id = $_POST['cart_id'];
$quantity = intval($_POST['quantity']);
$user_id = $_SESSION['userID'];

if ($quantity < 1) {
    echo json_encode(['success' => false, 'error' => 'Invalid quantity']);
    exit;
}

$stmt = $connection->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ? AND user_id = ?");
$stmt->bind_param("iii", $quantity, $cart_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$connection->close();
?>
