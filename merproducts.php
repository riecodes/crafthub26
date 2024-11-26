<?php
ob_start();
include 'dbconnect.php';
include 'cart_count.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userID'];
// Fetch the application ID for the logged-in user
$appQuery = "SELECT application_id FROM merchant_applications WHERE user_id = ?";
$stmtApp = $connection->prepare($appQuery);
$stmtApp->bind_param('s', $user_id);
$stmtApp->execute();
$appResult = $stmtApp->get_result();

if ($appResult && $appResult->num_rows > 0) {
    $appRow = $appResult->fetch_assoc();
    $application_id = $appRow['application_id'];
} else {
    die('Application ID not found for this user.');
}

$searchQuery = isset($_GET['searchbar']) ? mysqli_real_escape_string($connection, $_GET['searchbar']) : '';
$product_query = "SELECT * FROM merchant_products WHERE application_id = '$application_id'";
if ($searchQuery) {
    $product_query .= " AND (product_name LIKE '%$searchQuery%' OR product_desc LIKE '%$searchQuery%')";
}
$product = mysqli_query($connection, $product_query);
$product_count = mysqli_num_rows($product);

function getProductDetails($connection, $product_id) {
    $details = [];

    $size_query = "
        SELECT MIN(price) as min_price, MAX(price) as max_price 
        FROM product_sizes
        WHERE product_id = '$product_id'
    ";
    $size_result = mysqli_query($connection, $size_query);
    $size_row = mysqli_fetch_assoc($size_result);
    $details['min_price'] = $size_row['min_price'] ?? 0;
    $details['max_price'] = $size_row['max_price'] ?? $size_row['min_price'];

    $rating_query = "
        SELECT ROUND(AVG((quality_rating + price_rating) / 2), 1) as average_rating, 
               COUNT(*) as total_ratings
        FROM product_feedback
        WHERE product_id = '$product_id'
    ";
    $rating_result = mysqli_query($connection, $rating_query);
    $rating_row = mysqli_fetch_assoc($rating_result);
    $details['average_rating'] = $rating_row['average_rating'] ?? 0;
    $details['total_ratings'] = $rating_row['total_ratings'] ?? 0;

    $sold_query = "
        SELECT COUNT(*) as sold_count 
        FROM orders 
        WHERE product_id = '$product_id' AND (status = 'order received' OR status = 'already rated')
    ";
    $sold_result = mysqli_query($connection, $sold_query);
    $sold_row = mysqli_fetch_assoc($sold_result);
    $details['sold_count'] = $sold_row['sold_count'] ?? 0;

    return $details;
}

$count_query = "SELECT COUNT(*) as total FROM merchant_products mp 
                INNER JOIN merchant_applications ma ON mp.application_id = ma.application_id 
                WHERE ma.user_id = ?";
$stmt = $connection->prepare($count_query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$count_result = $stmt->get_result();
$product_count = $count_result->fetch_assoc()['total'];
?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="heading-with-count">My Products <span class="product-count"><?php echo $product_count; ?></span></h4>
            
            <form action="" method="GET" class="form-input">
                <input type="text" name="searchbar" id="searchProducts" placeholder="Search products..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    <!--=============== PRODUCT CONTAINER ===============-->
    <div class="product-container">
                <?php if ($product_count > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($product)): 
                        $productDetails = getProductDetails($connection, $row['product_id']);
                    ?>
                        <div class="product-card">
                            <?php if (!empty($row['product_img'])): ?>
                                <img src="<?php echo 'uploads/' . $row['product_img']; ?>" alt="<?php echo $row['product_name']; ?>" class="product-image">
                            <?php else: ?>
                                <div class="product-image"></div>
                            <?php endif; ?>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo $row['product_name']; ?></h3>
                                <p class="product-price">
                                    <?php if ($productDetails['min_price'] > 0): ?>
                                        ₱<?php echo number_format($productDetails['min_price'], 2); ?>
                                        <?php if ($productDetails['min_price'] != $productDetails['max_price']): ?>
                                            - ₱<?php echo number_format($productDetails['max_price'], 2); ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        No price set
                                    <?php endif; ?>
                                </p>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?php echo $productDetails['average_rating']; ?></span>
                                    <span class="product-sold">(<?php echo $productDetails['sold_count']; ?> sold)</span>
                                </div>

                                <a href="medit-product.php?product_id=<?php echo $row['product_id']; ?>" class="update-btn">Update</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No products found.</p>
                <?php endif; ?>
            </div>
</div>

<script>
function showEditMode() {
    // Store edit mode state in sessionStorage
    sessionStorage.setItem('isEditMode', 'true');
    // Navigate to add-listings.php
    window.location.href = 'meradd-product.php';
}
</script>

<?php
$content = ob_get_clean();
include 'mersidebar.php';
?> 