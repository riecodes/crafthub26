<?php 
    include 'dbconnect.php';

    if(isset($_GET['order_id'])){
        $order_id = $_GET['order_id'];

        $cancel = "UPDATE `orders` SET `status`='order received', `received_date`= NOW() WHERE `order_id` = '$order_id'";

        if (!mysqli_query($connection, $cancel)) {
            echo "<script>alert('Order Received Failed!'); 
            window.location.href='mypurchase.php';
            </script>";
        } else {

            echo "<script>alert('You successfully received your order!'); 
            window.location.href='mypurchase.php';</script>";
            exit;
        }
    }
?>
