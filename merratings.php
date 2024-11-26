<?php
ob_start();
include 'dbconnect.php';
include 'cart_count.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userID'];

// Fetch application ID
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
$countQuery = "
    SELECT COUNT(pf.feedback_id) AS feedback_count
    FROM product_feedback pf
    JOIN merchant_products mp ON pf.product_id = mp.product_id
    WHERE mp.application_id = ?
";
$stmtCount = $connection->prepare($countQuery);
$stmtCount->bind_param("i", $application_id);
$stmtCount->execute();
$countResult = $stmtCount->get_result();
$feedbackCount = $countResult->fetch_assoc()['feedback_count'];
$stmtCount->close();

$search = isset($_GET['searchbar']) ? "%" . $_GET['searchbar'] . "%" : "%";

$query = "
    SELECT pf.feedback_id, pf.product_id, pf.user_id, pf.quality_rating, pf.price_rating, pf.service_rating, 
           pf.feedback_notes, pf.feedback_date, mp.product_name, mp.product_img, cu.username, cu.user_profile
    FROM product_feedback pf
    JOIN merchant_products mp ON pf.product_id = mp.product_id
    JOIN crafthub_users cu ON pf.user_id = cu.user_id
    WHERE mp.application_id = ?
    AND (mp.product_name LIKE ? OR cu.username LIKE ? OR pf.feedback_notes LIKE ?)
    ORDER BY pf.feedback_date DESC
";

$stmt = $connection->prepare($query);
if ($stmt) {
    $stmt->bind_param("isss", $application_id, $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    exit("Query preparation failed: " . $connection->error);
}

?>

<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="heading-with-count">Product Ratings <span class="product-count"><?php echo $feedbackCount; ?></span></h4>
        </div>
    </div>
    
    <div class="container-feedback">
        <?php if (!empty($feedbacks)): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <?php
                $profile_image = !empty($feedback['user_profile']) ? '../uploads/' . $feedback['user_profile'] : 'images/user.png';
                $product_image = !empty($feedback['product_img']) ? 'uploads/' . $feedback['product_img'] : 'images/default-product.png';
                ?>
                
                <div class="rating-container mb-4">
                    <div class="user-info-section">
                        <img src="<?php echo htmlspecialchars($profile_image); ?>" 
                            class="user-avatar" 
                            alt="<?php echo htmlspecialchars($feedback['username']); ?>">
                        <div class="user-details">
                            <div class="user-header">
                                <h5 class="user-name"><?php echo htmlspecialchars($feedback['username']); ?></h5>
                                <span class="rating-date"><?php echo date("F j, Y", strtotime($feedback['feedback_date'])); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="product-info-section">
                        <img src="<?php echo htmlspecialchars($product_image); ?>" 
                            class="product-thumbnail" 
                            alt="<?php echo htmlspecialchars($feedback['product_name']); ?>">
                        <div class="product-details">
                            <h6 class="product-title"><?php echo htmlspecialchars($feedback['product_name']); ?></h6>
                            <div class="rating-details">
                                <div class="rating-item">
                                    <span class="rating-label">Quality:</span>
                                    <div class="stars">
                                        <?php echo str_repeat('<i class="fas fa-star"></i>', $feedback['quality_rating']); ?>
                                        <?php echo str_repeat('<i class="far fa-star"></i>', 5 - $feedback['quality_rating']); ?>
                                    </div>
                                </div>
                                <div class="rating-item">
                                    <span class="rating-label">Price:</span>
                                    <div class="stars">
                                        <?php echo str_repeat('<i class="fas fa-star"></i>', $feedback['price_rating']); ?>
                                        <?php echo str_repeat('<i class="far fa-star"></i>', 5 - $feedback['price_rating']); ?>
                                    </div>
                                </div>
                                <div class="rating-item">
                                    <span class="rating-label">Seller Service:</span>
                                    <div class="stars">
                                        <?php echo str_repeat('<i class="fas fa-star"></i>', $feedback['service_rating']); ?>
                                        <?php echo str_repeat('<i class="far fa-star"></i>', 5 - $feedback['service_rating']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feedback-text">
                        <?php echo htmlspecialchars($feedback['feedback_notes']); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No feedback available for your products.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'mersidebar.php';
?>
