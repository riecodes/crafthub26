<?php
include '../dbconnect.php';
session_start();

    if (isset($_POST['add_product'])) {
        // Get the basic product information
        $product_name = $_POST['product_name'];
        $product_desc = $_POST['product_desc'];
        $material = $_POST['material'];
        $stock = $_POST['stock'];
        $categ = $_POST['product_category'];
        $application_id = $_SESSION['application_id']; // Assuming you have stored the merchant ID in a session variable

        // File upload path for product image
        $target_directory = "uploads/";
        $product_image = basename($_FILES["product_image"]["name"]);
        $target_file = $target_directory . $product_image;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["product_image"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                die("<script>alert('File is not an image..')  </script>");
                
            }
        }

        // Check file size
        if ($_FILES["product_image"]["size"] > 1000000000) {
            die("<script>alert('Sorry, your file is too large.')  </script>");
           
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif") {
            die("<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed..')  </script>");
           
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
           die("<script>alert('Sorry, your file was not uploaded..')  </script>");
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                
            } else {
                die("<script>alert('Sorry, there was an error uploading your file.')  </script>");
            }
        }

        // Insert the basic product information into the database
        $insert_product_query = "INSERT INTO merchant_products (application_id, product_name, product_desc, stock, category, material, product_img) VALUES ('$application_id', '$product_name', '$product_desc', '$stock','$categ' ,'$material','$product_image')";
        $result = mysqli_query($connection, $insert_product_query);

        if ($result) {
            // Get the ID of the inserted product
            $product_id = mysqli_insert_id($connection);

            // Get the sizes data
            $sizes = $_POST['size'];
            $prices = $_POST['price'];

            // Loop through the sizes and insert them into the database
            for ($i = 0; $i < count($sizes); $i++) {
                $current_size = $sizes[$i]; // Fix variable name conflict
                $price = $prices[$i];

                // Insert the size into the database, linking it to the product ID
                $insert_size_query = "INSERT INTO product_sizes (product_id, application_id, sizes, price) VALUES ('$product_id', '$application_id', '$current_size', '$price')";
                $size_result = mysqli_query($connection, $insert_size_query);

                if (!$size_result) {
                    // Handle size insertion error
                    die("Error inserting size: " . mysqli_error($connection));
                }
            }

            // Get the colors data
            $colors = $_POST['color'];

            // Loop through the colors and insert them into the database
            foreach ($colors as $color) {
                // Insert the color into the database, linking it to the product ID
                $insert_color_query = "INSERT INTO product_color (product_id, application_id, color) VALUES ('$product_id', '$application_id', '$color')";
                $color_result = mysqli_query($connection, $insert_color_query);

                if (!$color_result) {
                    // Handle color insertion error
                    die("Error inserting color: " . mysqli_error($connection));
                }
            }

            // Product, sizes, and colors inserted successfully
            echo "<script>alert('Product, sizes, and colors added successfully')
                    window.location.href = 'merproducts.php';
            </script>";
            // Redirect or perform any additional actions as needed
        } else {
            // Handle product insertion error
            die("Error inserting product: " . mysqli_error($connection));
        }
    }
?>
