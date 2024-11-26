<?php
include 'dbcon.php';
session_start();

if (isset($_SESSION['merchant_id'])) {
    $merchant_id = $_SESSION['merchant_id'];
} else {
    echo 'Merchant not found';
    exit;
}

// Handle sorting logic
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'latest';  // Default to 'latest'
$product_query = "SELECT mp.*, IFNULL(SUM(o.quantity), 0) as total_sales FROM merchant_products mp 
                  LEFT JOIN orders o ON mp.product_id = o.product_id 
                  WHERE mp.merchant_id = '$merchant_id' ";

// Apply sorting
switch ($sort) {
    case 'popular':
        $product_query .= "GROUP BY mp.product_id ORDER BY total_sales DESC";  // Most sales (Top Sales)
        break;
    case 'price-low-high':
        $product_query .= "ORDER BY mp.price ASC";  // Low to High
        break;
    case 'price-high-low':
        $product_query .= "ORDER BY mp.price DESC";  // High to Low
        break;
    case 'latest':
    default:
        $product_query .= "ORDER BY mp.product_id DESC";  // Latest products
        break;
}

// Fetch products
$product = mysqli_query($connection, $product_query);
$product_count = mysqli_num_rows($product);

if ($product_count > 0) {
    while ($row = mysqli_fetch_assoc($product)) {
        echo '
            <div class="product-card" data-category="' . htmlspecialchars($row['category']) . '">
                <div class="product-image">
                    <img src="uploads/' . htmlspecialchars($row['product_img']) . '" alt="Product Image">
                </div>
                <div class="product-details">
                    <h5 class="product-name">' . htmlspecialchars($row['product_name']) . '</h5>
                    
                    </span>
                    <a href="buynow.php?product_id=' . $row['product_id'] . '">
                        <button class="buybtn" name="buy-now">View Product</button>
                    </a>
                </div>
            </div>';
    }
} else {
    echo '<p>No products found for this merchant.</p>';
}
