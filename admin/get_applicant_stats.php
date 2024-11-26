<?php
include 'dbconnect.php';

// Get total pending and approved applicants
$total_query = "
    SELECT 
        (SELECT COUNT(*) FROM merchant_applications WHERE status = 'pending') AS pending,
        (SELECT COUNT(*) FROM merchant_applications WHERE status = 'approved') AS approved
";
$total_result = mysqli_query($connection, $total_query);
$total_row = mysqli_fetch_assoc($total_result);
$pending = $total_row['pending'];
$approved = $total_row['approved'];

// Prepare the response
$response = [
    'total' => $pending + $approved, // Total of pending and approved applicants
    'pending' => $pending,
    'approved' => $approved
];

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
mysqli_close($connection);
?>
