<?php
session_start();
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['place_order'])) {
    header('Location: checkout.php');
    exit;
}

if (!isset($_POST['cart_items']) || !is_array($_POST['cart_items'])) {
    header('Location: cart.php');
    exit;
}

$cart_items = $_POST['cart_items']; // Array of selected cart item IDs
$user_id = $_SESSION['userID'];
$msg = $_POST['msg'] ?? '';
$payment_method = $_POST['payment_method'];

mysqli_begin_transaction($connection, MYSQLI_TRANS_START_READ_WRITE);

try {
    // Fetch the details of the selected cart items
    $items = [];
    foreach ($cart_items as $cart_id) {
        $query = "SELECT c.*, p.product_name 
                FROM cart c 
                JOIN merchant_products p ON c.product_id = p.product_id 
                WHERE c.cart_id = ? AND c.user_id = ? AND c.status = 'cart'";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param('ii', $cart_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $items[] = $result->fetch_assoc();
        } else {
            throw new Exception("Invalid cart item: $cart_id");
        }
    }

    // Insert order items into the orders table
    $order_query = "INSERT INTO orders (user_id, product_id, product_color, product_size, quantity, total, payment_method, user_note, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'to pay')";
    $stmt = $connection->prepare($order_query);

    $msgs = $_POST['msg'] ?? [];

    foreach ($items as $item) {
        $product_id = $item['product_id'];
        $product_color = $item['product_color'];
        $product_size = $item['product_size'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $total = $quantity * $price;
        
        // Get the unique message for each item using its cart_id
        $item_msg = $msgs[$item['cart_id']] ?? ''; // Default to an empty string if no message provided

        // Insert each item with its unique message
        $stmt->bind_param('iissiiss', $user_id, $product_id, $product_color, $product_size, $quantity, $total, $payment_method, $item_msg);
        $stmt->execute();
    }

    // Update cart status
    $cart_ids_placeholders = implode(',', array_fill(0, count($cart_items), '?')); 
    $update_cart_query = "UPDATE cart SET status = 'ordered' WHERE cart_id IN ($cart_ids_placeholders) AND user_id = ?";
    
    // Bind the parameters dynamically
    $stmt = $connection->prepare($update_cart_query);
    
    // Add user_id at the end of the cart items array
    $params = [...$cart_items, $user_id]; // Combine cart items and user ID into one array
    $types = str_repeat('i', count($cart_items)) . 'i'; // 'i' for each cart item and 'i' for user_id
    
    // Bind the parameters for each item and user_id
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Commit the transaction
    mysqli_commit($connection);

    // Redirect to order confirmation page
    echo "<script>
        alert('Order placed successfully! Thank you for shopping with us.');
        setTimeout(function() {
            window.location.href = 'mypurchase.php';
        }, 2000);
        </script>";

} catch (Exception $e) {
    // Rollback on failure
    mysqli_rollback($connection);
    error_log("Order processing failed: " . $e->getMessage());
    header('Location: error_page.php');
    exit;
}
?>
