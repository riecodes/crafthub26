<?php 
session_start();
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['userID']; // Assuming you have user_id stored in session after login
    $product_id = mysqli_real_escape_string($connection, $_POST['product_id']);
    $application_id = mysqli_real_escape_string($connection, $_POST['application_id']);
    $product_name = mysqli_real_escape_string($connection, $_POST['product_name']);
    $product_desc = mysqli_real_escape_string($connection, $_POST['product_desc']);
    $product_image = mysqli_real_escape_string($connection, $_POST['product_image']);
    $price = mysqli_real_escape_string($connection, $_POST['price']);
    $size = isset($_POST['size']) ? mysqli_real_escape_string($connection, $_POST['size']) : '';
    $color = isset($_POST['color']) ? mysqli_real_escape_string($connection, $_POST['color']) : '';
    $quantity = mysqli_real_escape_string($connection, $_POST['quantity']);

    // Prepare the insert statement
    $insert_query = "
        INSERT INTO `cart` (`user_id`, `product_id`,`product_color`, `product_size`, `quantity`, `price`, `status`)
        VALUES ('$user_id', '$product_id', '$color', '$size', '$quantity', '$price' , 'cart')";

    // Execute the query
    if (mysqli_query($connection, $insert_query)) {
        echo "<script>alert('Product added to cart successfully!'); window.location.href='cart.php';</script>";
    } else {
        die('Error: ' . mysqli_error($connection));
    }
} else {
    echo "Invalid request method.";
}
?>