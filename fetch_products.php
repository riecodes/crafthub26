<?php
include 'dbcon.php';
include 'getProductDetails.php'; 

$query = "SELECT * FROM merchant_products WHERE 1=1"; // Start with a condition that's always true

// Apply category filter
if (isset($_POST['category']) && !empty($_POST['category'])) {
    $category = mysqli_real_escape_string($connection, $_POST['category']);
    if ($category !== 'All Products') {
        $query .= " AND category = '$category'";
    }
}

// Apply search filter
if (isset($_POST['searchQuery']) && !empty($_POST['searchQuery'])) {
    $searchQuery = mysqli_real_escape_string($connection, $_POST['searchQuery']);
    $query .= " AND (product_name LIKE '%$searchQuery%' OR product_desc LIKE '%$searchQuery%')";
}

// Execute query
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Query Failed: " . mysqli_error($connection));
}

// Output the filtered products
while ($row = mysqli_fetch_assoc($result)) {
    $product_id = $row['product_id'];

    // Fetch price range, rating, and sold count
    list($min_price, $max_price, $average_rating, $total_ratings, $sold_count) = getProductDetails($connection, $product_id);

    $imagePath = 'uploads/' . basename($row['product_img']);
    ?>
    <div class="product-card">
        <div class="product-image">
            <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Product Image">
        </div>
        <div class="product-details">
            <h5 class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></h5>
            <div class="product-meta">
                <span class="product-price">
                    ₱<?php echo number_format($min_price, 2); ?>
                    <?php if ($min_price != $max_price): ?>
                        - ₱<?php echo number_format($max_price, 2); ?>
                    <?php endif; ?>
                </span>
                <div class="product-stats">
                    <span class="product-rating"><i class="fas fa-star"></i> <?php echo $average_rating; ?> (<?php echo $total_ratings; ?> ratings)</span>
                    <span class="product-sold"><strong><?php echo $sold_count; ?></strong> Sold</span>
                </div>
            </div>
            <a href="buynow.php?product_id=<?php echo $product_id; ?>">
                <button class="buybtn" name="buy-now">View Product</button>
            </a>
        </div>
    </div>
<?php 
} 
mysqli_close($connection);
?>
