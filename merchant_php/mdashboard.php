<?php
include 'dbconnect.php';

if (!isset($user_id)) {
    die("User ID is not set in session.");
}

$appQuery = "SELECT application_id FROM merchant_applications WHERE user_id = '$user_id' LIMIT 1";
$appResult = mysqli_query($connection, $appQuery);

if ($appResult && mysqli_num_rows($appResult) > 0) {
    $appRow = mysqli_fetch_assoc($appResult);
    $application_id = $appRow['application_id'];
} else {
    die("Application ID not found for this user.");
}

    //complete orders
    $countCompletedOrders = "SELECT COUNT(*) AS total_completed 
                            FROM orders o
                            JOIN merchant_products mp ON o.product_id = mp.product_id
                            WHERE mp.application_id = '$application_id' AND (o.status = 'order received' OR o.status = 'already rated')";

                        $countResult = mysqli_query($connection, $countCompletedOrders);

                        if ($countResult) {
                            $countRow = mysqli_fetch_assoc($countResult);
                            $totalCompleted = $countRow['total_completed'];
                        } else {
                            die("Query Failed: " . mysqli_error($connection));
                        }

    //pending orders                   
    $countPendingOrders = "SELECT COUNT(*) AS total_pending 
                        FROM orders o
                        JOIN merchant_products mp ON o.product_id = mp.product_id
                        WHERE mp.application_id = '$application_id' AND o.status = 'to pay'";

                    $countResult1 = mysqli_query($connection, $countPendingOrders);

                    if ($countResult1) {
                        $countRow1 = mysqli_fetch_assoc($countResult1);
                        $totalPending = $countRow1['total_pending'];
                    } else {
                        die("Query Failed: " . mysqli_error($connection));
                    }

    // ship orders
    $countToShipOrders = "SELECT COUNT(*) AS total_ship 
                        FROM orders o
                        JOIN merchant_products mp ON o.product_id = mp.product_id
                        WHERE mp.application_id = '$application_id' AND o.status = 'preparing'";

                    $countResult2 = mysqli_query($connection, $countToShipOrders);

                    if ($countResult2) {
                        $countRow2 = mysqli_fetch_assoc($countResult2);
                        $totalShip = $countRow2['total_ship'];
                    } else {
                        die("Query Failed: " . mysqli_error($connection));
                    }

    // cancelled orders
    $countCancelledOrders = "SELECT COUNT(*) AS total_cancelled 
                            FROM orders o
                            JOIN merchant_products mp ON o.product_id = mp.product_id
                            WHERE mp.application_id = '$application_id' AND o.status = 'cancelled'";

                            $countResult3 = mysqli_query($connection, $countCancelledOrders);

                        if ($countResult3) {
                            $countRow3 = mysqli_fetch_assoc($countResult3);
                            $totalCancel = $countRow3['total_cancelled'];
                        } else {
                            die("Query Failed: " . mysqli_error($connection));
                        }

    // Count returned orders
    $countReturnedOrders = "SELECT COUNT(*) AS total_returned 
                            FROM orders o
                            JOIN merchant_products mp ON o.product_id = mp.product_id
                            WHERE mp.application_id = '$application_id' AND o.status = 'returned'";

                            $countReturnedResult = mysqli_query($connection, $countReturnedOrders);

                            if ($countReturnedResult) {
                            $countReturnedRow = mysqli_fetch_assoc($countReturnedResult);
                            $totalReturned = $countReturnedRow['total_returned'];
                            } else {
                            die("Query Failed: " . mysqli_error($connection));
                            }

    // Count feedback for merchant's products
    $countMerchantFeedback = "SELECT COUNT(*) AS total_feedback 
                             FROM product_feedback pf
                            JOIN merchant_products mp ON pf.product_id = mp.product_id
                            WHERE mp.application_id = '$application_id'";

                            $countFeedbackResult = mysqli_query($connection, $countMerchantFeedback);

                            if ($countFeedbackResult) {
                            $countFeedbackRow = mysqli_fetch_assoc($countFeedbackResult);
                            $totalFeedback = $countFeedbackRow['total_feedback'];
                            } else {
                            die("Query Failed: " . mysqli_error($connection));
                            }                        
?>