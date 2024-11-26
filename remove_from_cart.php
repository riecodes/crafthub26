<?php
include 'dbconnect.php';
session_start();

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['cart_id'])) {
    $cart_id = $data['cart_id'];

    // Prepare the DELETE query
    $deleteQuery = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $connection->prepare($deleteQuery);
    $stmt->bind_param("i", $cart_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'No cart_id provided']);
}
?>
