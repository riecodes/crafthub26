<?php
ob_start();
include 'dbconnect.php';
include 'cart_count.php';

if (!isset($_SESSION['userID'])) {
    header('Location: index.php');
    exit();
}

$user_id = $_SESSION['userID'];

// Fetch shop name and user profile for the logged-in merchant
$query = "
    SELECT ma.shop_name, cu.user_profile 
    FROM merchant_applications ma
    JOIN crafthub_users cu ON ma.user_id = cu.user_id
    WHERE ma.user_id = ? LIMIT 1";
$stmt = $connection->prepare($query);
$stmt->bind_param('s', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $shop_name = $row['shop_name'];
    $user_profile = $row['user_profile'] ? 'uploads/' . $row['user_profile'] : 'images/default-shop.jpg'; // Use default if no profile image
} else {
    die("Shop details not found for this user.");
}
include 'merchant_php/mdashboard.php';
$total_orders = 0;
if(isset($totalPending)) $total_orders += $totalPending;
    if(isset($totalShip)) $total_orders += $totalShip;
    if(isset($totalCompleted)) $total_orders += $totalCompleted;
    if(isset($totalCancel)) $total_orders += $totalCancel;
    if(isset($totalReturned)) $total_orders += $totalReturned;
?>


<div class="welcome-container">
    <div class="welcome-content">
        <h1 class="shop-welcome">
            <span class="greeting"><i class="ri-store-2-line"></i> Welcome back,</span>
            <span class="shop-name"><?php echo htmlspecialchars($shop_name); ?></span>
        </h1>
        <div class="shop-profile">
            <img src="<?php echo htmlspecialchars($user_profile); ?>" alt="Shop Profile">
        </div>
    </div>
</div>

<!--=============== OVERVIEW ===============-->
<div class="mb-4">
    <h2 class="section-title">Overview</h2>
</div>

<!--=============== OVERVIEW CARDS ===============-->
<div class="row g-4">
    <!--=============== COMPLETED ORDERS ===============-->
    <div class="col-md-4">
        <div class="overview-card h-100">
            <div class="card-content">
                <div class="card-icon-area text-success">
                    <i class="ri-checkbox-circle-line"></i>
                </div>
                <div class="card-info">
                    <h3>Completed Orders</h3>
                    <div class="number-display counter-value" data-count="45"><?php echo $totalCompleted; ?></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="merorders.php?tab=completed" class="text-decoration-none text-secondary">
                    <span>View Details</span>
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
    </div>

    <!--=============== PENDING ORDERS ===============-->
    <div class="col-md-4">
        <div class="overview-card h-100">
            <div class="card-content">
                <div class="card-icon-area text-warning">
                    <i class="ri-wallet-3-line"></i>
                </div>
                <div class="card-info">
                    <h3>Pending Orders</h3>
                    <div class="number-display counter-value" data-count="12"><?php echo $totalPending; ?></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="merorders.php?tab=pending" class="text-decoration-none text-secondary">
                    <span>View Details</span>
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
    </div>

    <!--=============== TO SHIP ORDERS ===============-->
    <div class="col-md-4">
        <div class="overview-card h-100">
            <div class="card-content">
                <div class="card-icon-area text-primary">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="card-info">
                    <h3>To Ship Orders</h3>
                    <div class="number-display counter-value" data-count="8"><?php echo $totalShip; ?></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="merorders.php?tab=shipped" class="text-decoration-none text-secondary">
                    <span>View Details</span>
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
    </div>

    <!--=============== OUTGOING ORDERS ===============-->
    <div class="col-md-4">
        <div class="overview-card h-100">
            <div class="card-content">
                <div class="card-icon-area text-primary">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <div class="card-info">
                    <h3>Outgoing Orders</h3>
                    <div class="number-display counter-value" data-count="0">0</div>
                </div>
            </div>
            <div class="card-footer">
                <a href="merorders.php?tab=outgoing" class="text-decoration-none text-secondary">
                    <span>View Details</span>
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
    </div>

    <!--=============== CANCELLED ORDERS ===============-->
    <div class="col-md-4">
        <div class="overview-card h-100">
            <div class="card-content">
                <div class="card-icon-area text-danger">
                    <i class="ri-close-circle-line"></i>
                </div>
                <div class="card-info">
                    <h3>Cancelled Orders</h3>
                    <div class="number-display counter-value" data-count=""><?php echo $totalCancel; ?></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="merorders.php?tab=cancelled" class="text-decoration-none text-secondary">
                    <span>View Details</span>
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
    </div>

    <!--=============== RETURNED ORDERS ===============-->
    <div class="col-md-4">
        <div class="overview-card h-100">
            <div class="card-content">
                <div class="card-icon-area text-danger">
                    <i class="ri-arrow-go-back-line"></i>
                </div>
                <div class="card-info">
                    <h3>Returned Orders</h3>
                    <div class="number-display counter-value" data-count="3"><?php echo $totalReturned; ?></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="merorders.php?tab=returned" class="text-decoration-none text-secondary">
                    <span>View Details</span>
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
    </div>

    <!--=============== PRODUCT RATINGS ===============-->
    <div class="col-md-4">
        <div class="overview-card h-100">
            <div class="card-content">
                <div class="card-icon-area text-warning">
                    <i class="ri-star-line"></i>
                </div>
                <div class="card-info">
                    <h3>Product Ratings</h3>
                    <div class="number-display counter-value" data-count="4.7"><?php echo $totalFeedback; ?></div>
                </div>
            </div>
            <div class="card-footer">
                <a href="merratings.php" class="text-decoration-none text-secondary">
                    <span>View Details</span>
                    <i class="ri-arrow-right-up-line"></i>
                </a>
            </div>
        </div>
    </div>
</div>
<script src="js/dashboard.js"></script>

<?php
$content = ob_get_clean();
include 'mersidebar.php';
?> 