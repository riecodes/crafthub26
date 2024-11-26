<?php 
    include 'dbconnect.php';

    if(isset($_GET['order_id'])){
        $order_id = $_GET['order_id'];

        $cancel = "UPDATE `orders` SET `status`='cancelled', `cancel_date` = NOW() WHERE order_id = '$order_id'";

        if (!mysqli_query($connection, $cancel)) {
            echo "<script>alert('Cancellation Failed!'); 
            window.location.href='mypurchase.php';
            </script>";
        }else{
            // Redirect to order confirmation page
            echo "<script>alert('You cancelled your order!'); 
            window.location.href='mypurchase.php';</script>";
            exit;
        }
    
        

    }

    



?>