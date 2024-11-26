<?php 
    include 'dbconnect.php';

    if(isset($_GET['order_id'])){
        $order_id = $_GET['order_id'];

        // Update the order status to 'shipped' and set the current timestamp for ship_date
        $cancel = "UPDATE `orders` SET `status` = 'shipped', `ship_date` = NOW() WHERE `order_id` = '$order_id'";

        if (!mysqli_query($connection, $cancel)) {
            echo "<script>alert('Shipped Order Failed!'); 
            window.location.href='merorders.php';
            </script>";
        } else {
            // Redirect to order confirmation page
            echo "<script>alert('You successfully shipped the order!'); 
                window.location.href='merdashboard.php';</script>";
            exit;
        }
    }
?>
