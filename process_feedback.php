<?php
include 'dbconnect.php';
session_start();
$user_id = $_SESSION['userID'];

// Get the posted values
if(isset($_POST['submit'])){
    $qualityRating = $_POST['quality-rating'];
    $priceRating = $_POST['price-rating'];
    $serviceRating = $_POST['service-rating'];
    $notes = $_POST['notes'];
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];

    // Begin transaction
    mysqli_begin_transaction($connection);

    try {
        // Insert the feedback into the database
        $query = "INSERT INTO product_feedback (`product_id`, `user_id`, `quality_rating`, `price_rating`, `service_rating`, `feedback_notes`) 
                  VALUES ('$product_id', '$user_id', '$qualityRating', '$priceRating', '$serviceRating', '$notes')";
        $result = mysqli_query($connection, $query);
        
        if (!$result) {
            throw new Exception("Error inserting feedback: " . $connection->error);
        }

        // Update the status of the order to "already rated"
        $updateQuery = "UPDATE orders SET status = 'already rated' WHERE order_id = '$order_id'";
        $updateResult = mysqli_query($connection, $updateQuery);

        if (!$updateResult) {
            throw new Exception("Error updating order status: " . $connection->error);
        }

        // Commit the transaction if both queries were successful
        mysqli_commit($connection);

        echo "<script>
                alert('Feedback received! Thanks for helping us improve.');
                setTimeout(function() {
                    window.location.href = 'mypurchase.php';
                }, 2000); // redirect after 2 seconds
              </script>";
        exit;

    } catch (Exception $e) {
        // Rollback the transaction on error
        mysqli_rollback($connection);
        die("Transaction failed: " . $e->getMessage());
    }
}
?>
