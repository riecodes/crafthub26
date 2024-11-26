<?php
include 'dbcon.php';
include 'getProductDetails.php';

$merchant_id = $_POST['merchant_id'];
$sortOption = $_POST['sortOption'];

// Build the base query
$query = "SELECT mp.*, 
    (SELECT MIN(ps.price) FROM product_sizes ps WHERE ps.product_id = mp.product_id) as min_price,
    (SELECT MAX(ps.price) FROM product_sizes ps WHERE ps.product_id = mp.product_id) as max_price,
    (SELECT COUNT(*) FROM orders o WHERE o.product_id = mp.product_id AND o.status = 'order received') as sold_count
    FROM merchant_products mp 
    WHERE mp.merchant_id = '$merchant_id'";

// Modify the query based on the sort option
switch ($sortOption) {
    case 'top-sales':
        $query .= " ORDER BY sold_count DESC"; // Sort by most sold to least sold
        break;
    case 'price-low-high':
        $query .= " ORDER BY min_price ASC"; // Sort by lowest price first
        break;
    case 'price-high-low':
        $query .= " ORDER BY min_price DESC"; // Sort by highest price first
        break;
    default:
        $query .= " ORDER BY upload_date DESC"; // Default to latest products
        break;
}

// Execute query
$product = mysqli_query($connection, $query);

if (mysqli_num_rows($product) > 0) {
    while ($row = mysqli_fetch_assoc($product)) {
        $product_id = $row['product_id'];
        list($min_price, $max_price, $average_rating, $total_ratings, $sold_count) = getProductDetails($connection, $product_id);
        ?>
        <div class="product-card">
            <div class="product-image">
                <img src="<?php echo 'uploads/' . $row['product_img']; ?>" alt="<?php echo $row['product_name']; ?>">
            </div>
            <div class="product-details">
                <h5 class="product-name"><?php echo $row['product_name']; ?></h5>
                <p class="price">₱<?php echo number_format($min_price, 2); ?><?php if ($min_price != $max_price): ?> - ₱<?php echo number_format($max_price, 2); ?><?php endif; ?></p>
                <div class="star-rating">
                    <i class="ri-star-fill"></i> <?php echo $average_rating; ?> (<?php echo $total_ratings; ?> ratings)
                    <strong><?php echo $sold_count; ?></strong> Sold
                </div>
                <a href="buynow.php?product_id=<?php echo $row['product_id']; ?>">
                    <button class="buybtn">View Product</button>
                </a>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p>No products found.</p>";
}

mysqli_close($connection);
?>
