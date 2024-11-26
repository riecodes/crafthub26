<?php
session_start(); 
include 'dbconnect.php';


if (isset($_POST['add_product'])) {
    // Get the basic product information
    $product_name = $_POST['product_name'];
    $product_desc = $_POST['product_desc'];
    $material = isset($_POST['material']) ? $_POST['material'] : ''; // Handle undefined material key
    $stock = $_POST['stock'];
    $categ = $_POST['product_category'];
    $application_id = isset($_SESSION['application_id']) ? $_SESSION['application_id'] : null; // Handle undefined application_id

    if (!$application_id) {
        die("Application ID not found. Please make sure you're logged in correctly.");
    }

    // File upload path for product image
    $target_directory = "uploads/";
    $product_image = basename($_FILES["product_image"]["name"]);
    $target_file = $target_directory . $product_image;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image
    if (isset($_POST["submit"])) {
        $check = getimagesize($_FILES["product_image"]["tmp_name"]);
        if ($check === false) {
            die("<script>alert('File is not an image.')</script>");
        }
    }

    // Check file size
    if ($_FILES["product_image"]["size"] > 1000000000) {
        die("<script>alert('Sorry, your file is too large.')</script>");
    }

    // Allow specific file formats
    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
        die("<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>");
    }

    // Attempt to upload file
    if (!move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
        die("<script>alert('Sorry, there was an error uploading your file.')</script>");
    }

    // Insert the basic product information into the database
    $insert_product_query = "INSERT INTO merchant_products (application_id, product_name, product_desc, stock, category, material, product_img) VALUES ('$application_id', '$product_name', '$product_desc', '$stock', '$categ', '$material', '$product_image')";
    $result = mysqli_query($connection, $insert_product_query);

    if ($result) {
        $product_id = mysqli_insert_id($connection);

        // Get the sizes data
        $sizes = $_POST['size'];
        $prices = $_POST['price'];

        // Insert sizes into the database
        for ($i = 0; $i < count($sizes); $i++) {
            $current_size = $sizes[$i];
            $price = $prices[$i];

            $insert_size_query = "INSERT INTO product_sizes (product_id, application_id, sizes, price) VALUES ('$product_id', '$application_id', '$current_size', '$price')";
            $size_result = mysqli_query($connection, $insert_size_query);

            if (!$size_result) {
                die("Error inserting size: " . mysqli_error($connection));
            }
        }

        // Get the colors data
        $colors = $_POST['color'];

        // Insert colors into the database
        foreach ($colors as $color) {
            $insert_color_query = "INSERT INTO product_color (product_id, application_id, color) VALUES ('$product_id', '$application_id', '$color')";
            $color_result = mysqli_query($connection, $insert_color_query);

            if (!$color_result) {
                die("Error inserting color: " . mysqli_error($connection));
            }
        }

        echo "<script>alert('Product, sizes, and colors added successfully');
              window.location.href = 'merproducts.php';</script>";
    } else {
        die("Error inserting product: " . mysqli_error($connection));
    }
}
?>
