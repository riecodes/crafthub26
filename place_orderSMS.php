<?php
include 'dbcon.php';
session_start();

require __DIR__ . '/vendor/twilio/sdk/src/Twilio/autoload.php';

$price = $_POST['price'];        
$quantity = $_POST['quantity'];   
$total_price = $price * $quantity; 

    if (isset($_POST['place_order'])) {
        $user_id = $_SESSION['userID'];
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_price = $_POST['price'];
        $quantity = $_POST['quantity'];
        $size = $_POST['size'];
        $color = $_POST['color'];
        $total_price = $_POST['total_price'];
        $payment_method = $_POST['payment_method'];
        $user_note = $_POST['msg'];
        $status = 'to pay';

        // SQL query to insert data into the orders table
        $query = "INSERT INTO orders (user_id, product_id, product_color, product_size, quantity, total, payment_method, user_note, status) 
                VALUES ('$user_id', '$product_id', '$color', '$size','$quantity', '$total_price', '$payment_method', '$user_note', '$status')";

    if (mysqli_query($connection, $query)) {
        // Send SMS notification using Twilio
        $sid = "AC8911ae5d09bba35d6ad1e624a36ed4a0";
        $token = "0a929dd064efce763095a9dd599a63f5";
        $client = new Twilio\Rest\Client($sid, $token);

        try {
            // Use the Client to make requests to the Twilio REST API
            $client->messages->create(
                '+639106834097', // Recipient's phone number
                [
                    // A Twilio phone number you purchased at https://console.twilio.com
                    'from' => '+13609791119',
                    // The body of the text message you'd like to send
                    'body' => "Your order successfully placed."
                ]
            );
            echo "<script>
                    alert('Order placed and SMS sent successfully! Thank you for shopping with us.');
                    setTimeout(function() {
                        window.location.href = 'mypurchase.php';
                    }, 2000); // redirect after 2 seconds
                </script>";
        } catch (Exception $e) {
            echo "Error sending SMS: " . $e->getMessage();
        }

            exit;
        } else {
            die("Query failed: " . mysqli_error($connection));
        }
    }

?>
