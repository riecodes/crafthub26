<?php 
    include 'dbconnect.php';


    if(isset($_GET['order_id'])){
        $order_id = $_GET['order_id'];

        $cancel = "UPDATE `orders` SET `status`= 'preparing' WHERE order_id = '$order_id'";

        if (!mysqli_query($connection, $cancel)) {
            echo "<script>alert('Prepare Product Failed!'); 
            window.location.href='merdashboard.php';
            </script>";
        }else{
            // Redirect to order confirmation page
            echo "<script>alert('Product is being prepared!'); 
                window.location.href='merorders.php?tab=shipped';</script>";
            exit;
        }
    
        

    }




?>