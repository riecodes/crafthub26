<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    
    // Delete related records first
    $delete_sizes = "DELETE FROM product_sizes WHERE product_id = '$product_id'";
    $delete_colors = "DELETE FROM product_color WHERE product_id = '$product_id'";
    $delete_product = "DELETE FROM merchant_products WHERE product_id = '$product_id'";
    
    // Execute the queries
    mysqli_query($connection, $delete_sizes);
    mysqli_query($connection, $delete_colors);
    mysqli_query($connection, $delete_product);
    
    // Redirect back to products page
    header('Location: merproducts.php');
    exit();
}
?> 