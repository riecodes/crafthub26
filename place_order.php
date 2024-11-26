<?php
include 'dbconnect.php';
session_start();

$price = $_POST['price'];        
$quantity = $_POST['quantity'];   
$total_price = $price * $quantity; 

if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['userID'];
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $size = $_POST['size'];
    $color = $_POST['color'];
    $total_price = $_POST['total_price'];
    $payment_method = $_POST['payment_method'];
    $user_note = $_POST['msg'];
    $status = 'to pay';

    // SQL query to insert data into the orders table
    $query = "INSERT INTO orders (user_id, product_id, product_color, product_size, quantity, total, payment_method, user_note, status) 
              VALUES ('$user_id', '$product_id', '$color', '$size','$quantity', '$total_price', '$payment_method', '$user_note', '$status')";

    if (mysqli_query($connection, $query)) {
        // Redirect to order confirmation page or display a success message
        echo "<script>
                alert('Order placed successfully! Thank you for shopping with us.');
                setTimeout(function() {
                    window.location.href = 'mypurchase.php';
                }, 2000); // redirect after 2 seconds
              </script>";
        exit;
    } else {
        die("Query failed: " . mysqli_error($connection));
    }
}
?>
