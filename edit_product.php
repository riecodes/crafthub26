<?php
session_start();
include 'dbconnect.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $material = $_POST['material'];
    $stock = $_POST['stock'];
    $category = $_POST['product_category'];

    // Update product information
    $update_product = "UPDATE merchant_products SET 
        product_name = ?, 
        product_desc = ?, 
        material = ?, 
        stock = ?, 
        category = ?";
    
    $params = [$product_name, $product_desc, $material, $stock, $category];
    $types = "sssss";

    // Handle image upload if a new image was uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $target_directory = "uploads/";
        $product_image = basename($_FILES["product_image"]["name"]);
        $target_file = $target_directory . $product_image;
        
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            $update_product .= ", product_img = ?";
            $params[] = $product_image;
            $types .= "s";
        }
    }

    $update_product .= " WHERE product_id = ?";
    $params[] = $product_id;
    $types .= "s";

    $stmt = $connection->prepare($update_product);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Update colors
    if (isset($_POST['color'])) {
        // Delete existing colors first
        mysqli_query($connection, "DELETE FROM product_color WHERE product_id = '$product_id'");
        
        // Insert new colors
        foreach ($_POST['color'] as $color) {
            if (!empty($color)) {
                $insert_color = "INSERT INTO product_color (product_id, application_id, color) VALUES (?, ?, ?)";
                $stmt = $connection->prepare($insert_color);
                $stmt->bind_param("sss", $product_id, $_SESSION['application_id'], $color);
                $stmt->execute();
            }
        }
    }

    // Update sizes and prices
    if (isset($_POST['size']) && isset($_POST['price'])) {
        // Delete existing sizes first
        mysqli_query($connection, "DELETE FROM product_sizes WHERE product_id = '$product_id'");
        
        // Insert new sizes and prices
        $sizes = $_POST['size'];
        $prices = $_POST['price'];
        for ($i = 0; $i < count($sizes); $i++) {
            if (!empty($sizes[$i]) && isset($prices[$i])) {
                $insert_size = "INSERT INTO product_sizes (product_id, application_id, sizes, price) VALUES (?, ?, ?, ?)";
                $stmt = $connection->prepare($insert_size);
                $stmt->bind_param("sssd", $product_id, $_SESSION['application_id'], $sizes[$i], $prices[$i]);
                $stmt->execute();
            }
        }
    }

    // Redirect back to products page
    header('Location: merproducts.php');
    exit();
}
?>
