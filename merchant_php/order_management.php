<?php
include 'dbconnect.php';

if (!isset($user_id)) {
    die("User ID is not set in session.");
}

// Fetch the application ID for the logged-in user
$appQuery = "SELECT application_id FROM merchant_applications WHERE user_id = ?";
$stmtApp = $connection->prepare($appQuery);
$stmtApp->bind_param('s', $user_id);
$stmtApp->execute();
$appResult = $stmtApp->get_result();

if ($appResult && $appResult->num_rows > 0) {
    $appRow = $appResult->fetch_assoc();
    $application_id = $appRow['application_id'];
} else {
    die("Application ID not found for this user.");
}

// Function to fetch detailed order information by status for the specified application_id
function getOrders($connection, $application_id, $status) {
    $query = "SELECT
                cu.first_name, 
                cu.middle_name, 
                cu.last_name,
                cu.address,
                cu.contact_no,
                mp.product_img,
                mp.product_name,
                o.product_color,
                o.product_size,
                o.quantity,
                o.total,
                o.user_note
            FROM 
                orders o
            JOIN 
                merchant_products mp ON o.product_id = mp.product_id
            JOIN 
                merchant_applications m ON mp.application_id = m.application_id
            JOIN 
                crafthub_users cu ON o.user_id = cu.user_id
            WHERE 
                mp.application_id = ? AND o.status = ?";

    $stmt = $connection->prepare($query);
    $stmt->bind_param('ss', $application_id, $status);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch orders by status for the logged-in merchant's application_id
$to_pay_orders = getOrders($connection, $application_id, 'to pay');
$to_ship_orders = getOrders($connection, $application_id, 'preparing');
$to_receive_orders = getOrders($connection, $application_id, 'shipped');
$to_rate_orders = getOrders($connection, $application_id, 'order received');
$cancelled_orders = getOrders($connection, $application_id, 'cancelled');
$returned_orders = getOrders($connection, $application_id, 'return');
$completed_orders = getOrders($connection, $application_id, 'already rated');

// Example usage
while ($order = $to_pay_orders->fetch_assoc()) {
    echo "Customer Name: " . $order['first_name'] . " " . $order['middle_name'] . " " . $order['last_name'] . "<br>";
    echo "Address: " . $order['address'] . "<br>";
    echo "Contact Number: " . $order['contact_no'] . "<br>";
    echo "Product Image: " . $order['product_img'] . "<br>";
    echo "Product Name: " . $order['product_name'] . "<br>";
    echo "Color: " . $order['product_color'] . "<br>";
    echo "Size: " . $order['product_size'] . "<br>";
    echo "Quantity: " . $order['quantity'] . "<br>";
    echo "Total: " . $order['total'] . "<br>";
    echo "User Note: " . $order['user_note'] . "<br>";
    echo "<hr>";
}
?>
