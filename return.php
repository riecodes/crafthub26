<?php
include 'dbconnect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $order_id = mysqli_real_escape_string($connection, $_POST['order_id']);
        $reason = mysqli_real_escape_string($connection, $_POST['reason']);
        $details = mysqli_real_escape_string($connection, $_POST['details']);

        // Updated column name to `additional_deets`
        $query = "UPDATE `orders` SET `status`='returned', `returned_date`=NOW(), `reason_for_return`='$reason', `additional_deets`='$details' WHERE `order_id` = '$order_id'";

        if (mysqli_query($connection, $query)) {
            echo "<script>alert('Return request submitted successfully!'); 
            window.location.href = 'mypurchase.php';</script>";
        } else {
            echo "<script>alert('Return request failed! Please try again.'); 
            window.location.href = 'mypurchase.php';</script>";
        }
    }
?>