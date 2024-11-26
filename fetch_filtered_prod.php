<?php
include 'dbcon.php';
include 'getProductDetails.php';

$merchant_id = $_POST['merchant_id'];
$category = $_POST['category'];

// Build query based on category
if ($category == 'all') {
    $product_query = "SELECT * FROM merchant_products WHERE merchant_id = '$merchant_id'";
} else {
    $product_query = "SELECT * FROM merchant_products WHERE merchant_id = '$merchant_id' AND category = '$category'";
}

$product = mysqli_query($connection, $product_query);

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
    echo "<p>No products found in this category.</p>";
}
?>
