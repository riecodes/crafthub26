
<?php
include 'dbconnect.php';
function getProductDetails($connection, $product_id) {

    $price_range_query = mysqli_query($connection, "
        SELECT MIN(price) as min_price, MAX(price) as max_price 
        FROM product_sizes 
        WHERE product_id = '$product_id'
    ");

    $min_price = 0;
    $max_price = 0;
    if ($price_range_query && mysqli_num_rows($price_range_query) > 0) {
        $price_range_row = mysqli_fetch_assoc($price_range_query);
        $min_price = $price_range_row['min_price'];
        $max_price = $price_range_row['max_price'];
    }
    $rating_query = mysqli_query($connection, "
        SELECT AVG((quality_rating + price_rating) / 2) as average_rating, COUNT(*) as total_ratings 
        FROM product_feedback 
        WHERE product_id = '$product_id'
    ");

    $average_rating = 0;
    $total_ratings = 0;
    if ($rating_query && mysqli_num_rows($rating_query) > 0) {
        $rating_row = mysqli_fetch_assoc($rating_query);
        $average_rating = round($rating_row['average_rating'], 1);
        $total_ratings = $rating_row['total_ratings'];
    }

    $order_count_query = mysqli_query($connection, "
        SELECT COUNT(*) as sold_count 
        FROM orders 
        WHERE product_id = '$product_id' 
          AND status = 'order received'
    ");

    $sold_count = 0;
    if ($order_count_query && mysqli_num_rows($order_count_query) > 0) {
        $order_count_row = mysqli_fetch_assoc($order_count_query);
        $sold_count = $order_count_row['sold_count'];
    }

    return [$min_price, $max_price, $average_rating, $total_ratings, $sold_count];
}
