<?php 
    include 'dbconnect.php';
    include 'cart_count.php';
    
    $user_id = $_SESSION['userID'];

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($user_id)) {
        header('Location: index.php');
        exit();
    }

    include 'merchant_php/mdashboard.php';

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
            die('You do not have a access for this page');
        }


    //$toPayOrders = getOrders($connection, $application_id, 'to pay');

    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: Merchant Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--=============== BOXICONS ===============-->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <!--=============== FONT AWESOME ===============-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="css/shop.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
    <link href="css/nav.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="shop-container">
        <!--=============== SIDEBAR ===============-->
        <div class="shop-sidebar">
            <a href="#" class="menu-item" data-section="dashboard" onclick="showSection('dashboard')">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="menu-item" data-section="profile" onclick="showSection('profile')">
                <i class="ri-user-line"></i>
                <span>Profile</span>
            </a>
            <a href="#" class="menu-item" data-section="products" onclick="showSection('products')">
                <i class="ri-shopping-bag-line"></i>
                <span>Products</span>
            </a>
            <a href="#" class="menu-item" data-section="add-listings" onclick="showSection('add-listings')">
                <i class="ri-add-circle-line"></i>
                <span>Add Listings</span>
            </a>
            <a href="#" class="menu-item" data-section="orders" onclick="showSection('orders')">
                <i class="ri-file-list-line"></i>
                <span>Orders</span>
            </a>
            <a href="#" class="menu-item" data-section="ratings" onclick="showSection('ratings')">
                <i class="ri-star-line"></i>
                <span>Product Ratings</span>
            </a>
            <a href="#" class="menu-item" data-section="messages" onclick="showSection('messages')">
                <i class="ri-message-3-line"></i>
                <span>Messages</span>
            </a>
        </div>

        <!--=============== DASHBOARD ===============-->
        <div class="shop-content">
            <div class="content-section dashboard-section">
                <div class="welcome-container">
                    <div class="welcome-content">
                        <h1 class="shop-welcome">
                            <span class="greeting"><i class="ri-store-2-line"></i> Welcome back,</span>
                            <span class="shop-name"><?php echo $_SESSION['username']; ?></span>
                        </h1>
                        <div class="shop-profile">
                            <img src="" alt="Shop Profile">
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
                                <a href="#" class="text-decoration-none text-secondary" onclick="showOrdersTab('completed')">
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
                                <a href="#" class="text-decoration-none text-secondary" onclick="showOrdersTab('pending')">
                                    <span>View Details</span>
                                    <i class="ri-arrow-right-up-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!--=============== SHIPPED ORDERS ===============-->
                    <div class="col-md-4">
                        <div class="overview-card h-100">
                            <div class="card-content">
                                <div class="card-icon-area text-primary">
                                    <i class="ri-truck-line"></i>
                                </div>
                                <div class="card-info">
                                    <h3>Shipped Orders</h3>
                                    <div class="number-display counter-value" data-count="8"><?php echo $totalShip; ?></div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="#" class="text-decoration-none text-secondary" onclick="showOrdersTab('shipped')">
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
                                    <div class="number-display counter-value" data-count="15"><?php echo $totalCancel; ?></div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="#" class="text-decoration-none text-secondary" onclick="showOrdersTab('cancelled')">
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
                                <a href="#" class="text-decoration-none text-secondary" onclick="showOrdersTab('returned')">
                                    <span>View Details</span>
                                    <i class="ri-arrow-right-up-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="overview-card h-100">
                            <div class="card-content">
                                <div class="card-icon-area text-warning">
                                    <i class="ri-star-line"></i>
                                </div>
                                <div class="card-info">
                                    <h3>Product Ratings</h3>
                                    <div class="number-display counter-value" data-count=""><?php echo $totalFeedback; ?></div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <a href="#" class="text-decoration-none text-secondary" onclick="showSection('ratings')">
                                    <span>View Details</span>
                                    <i class="ri-arrow-right-up-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--=============== PRODUCTS ===============-->
            <div class="content-section products-section" style="display: none;">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="heading-with-count">My Products <span class="product-count">4</span></h4>
                    </div>

                    <!--=============== PRODUCT CONTAINER ===============-->
                    <div class="product-container">
                        <!--=============== SAMPLE PRODUCT CARDS ===============-->
                        <div class="product-card">
                            <div class="product-image"></div>
                            <div class="product-info">
                                <h3 class="product-name">Handmade Pottery Vase</h3>
                                <p class="product-price">₱1,299.99</p>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <span>4.8</span>
                                    <span class="product-sold">(25 sold)</span>
                                </div>
                                <a href="#" class="update-btn" onclick="showEditMode(this)" data-product-id="1">Update</a>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image"></div>
                            <div class="product-info">
                                <h3 class="product-name">Woven Basket Set</h3>
                                <p class="product-price">₱899.99</p>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <span>4.5</span>
                                    <span class="product-sold">(18 sold)</span>
                                </div>
                                <a href="#" class="update-btn" onclick="showEditMode(this)" data-product-id="2">Update</a>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image"></div>
                            <div class="product-info">
                                <h3 class="product-name">Macrame Wall Hanging</h3>
                                <p class="product-price">₱1,599.99</p>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <span>4.9</span>
                                    <span class="product-sold">(32 sold)</span>
                                </div>
                                <a href="#" class="update-btn" onclick="showEditMode(this)" data-product-id="3">Update</a>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image"></div>
                            <div class="product-info">
                                <h3 class="product-name">Handcrafted Jewelry Box</h3>
                                <p class="product-price">₱2,499.99</p>
                                <div class="product-rating">
                                    <i class="fas fa-star"></i>
                                    <span>4.7</span>
                                    <span class="product-sold">(15 sold)</span>
                                </div>
                                <a href="#" class="update-btn" onclick="showEditMode(this)" data-product-id="4">Update</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--=============== ORDERS ===============-->
            <div class="content-section orders-section" style="display: none;">
                <div class="container">
                    <div class="row mb-4 align-items-center">
                        <div class="col-12">
                            <h4 class="heading-with-count">Order Management <span class="product-count">31</span></h4>
                        </div>
                    </div>
                    
                    <!--=============== NAVTABS ===============-->
                    <div class="custom-tabs">
                        <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending">
                                    <i class="fa-regular fa-clock"></i> Pending
                                    <span class="count-badge"><?php echo $totalPending; ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="shipped-tab" data-bs-toggle="tab" data-bs-target="#shipped" type="button" role="tab">
                                    <i class="fa-solid fa-truck"></i> Shipped Orders
                                    <span class="count-badge"><?php echo $totalShip; ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
                                    <i class="fa-solid fa-check-circle"></i> Completed Orders
                                    <span class="count-badge"><?php echo $totalCompleted; ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled" type="button" role="tab">
                                    <i class="fa-solid fa-ban"></i> Cancelled Orders
                                    <span class="count-badge"><?php echo $totalCancel; ?></span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="returned-tab" data-bs-toggle="tab" data-bs-target="#returned" type="button" role="tab">
                                    <i class="fa-solid fa-undo"></i> Returned Orders
                                    <span class="count-badge"><?php echo $totalReturned; ?></span>
                                </button>
                            </li>
                        </ul>
                    </div>

                    <!--=============== TAB CONTENT ===============-->
                    <div class="tab-content mt-4" id="orderTabsContent">

                        <!--=============== PENDING ORDERS TAB ===============-->

                        <div class="tab-pane fade show active" id="pending" role="tabpanel">
                            <?php 
                                $select_pending = "SELECT o.*, u.*, mp.*
                                                    FROM orders o
                                                    JOIN merchant_products mp ON o.product_id = mp.product_id
                                                    JOIN crafthub_users u ON o.user_id = u.user_id
                                                    WHERE mp.application_id = $application_id AND o.status = 'to pay'";

                                $result_pending = mysqli_query($connection, $select_pending);

                                if(!$result_pending){
                                    die("query Failed".mysqli_error($connection));
                                }else{
                                    while($pending = mysqli_fetch_assoc($result_pending)){
                                ?>
                            
                                    <div class="prod-container">
                                        <div class="order-header">
                                            <div class="order-id">Order # <?php echo $pending['order_id']; ?></div>
                                            <div class="order-date"><?php echo $pending['order_date']; ?> </div>
                                        </div>

                                        <div class="customer-info">
                                            <div class="customer-info-item">
                                                <div class="customer-info-label">Customer Name</div>
                                                <div class="customer-info-value"><?php echo $pending['first_name'] . ' ' . $pending['middle_name'] . ' ' . $pending['last_name']; ?></div>
                                            </div>
                                            <div class="customer-info-item">
                                                <div class="customer-info-label">Contact Number</div>
                                                <div class="customer-info-value"><?php echo $pending['contact_no']; ?></div>
                                            </div>
                                            <div class="customer-info-item customer-address">
                                                <div class="customer-info-label">Shipping Address</div>
                                                <div class="customer-info-value"><?php echo $pending['address']; ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="section-separator"></div>
                                        
                                        <div class="product-section">
                                            <img src="<?php echo 'uploads/'. $pending['product_img']; ?>" 
                                                class="product-image" 
                                                alt="">
                                            <div class="product-details">
                                                <div class="product-name"><?php echo $pending['product_name']; ?></div>
                                                <div class="product-specs">
                                                    <span>Size: <?php echo $pending['product_size']; ?></span>
                                                    <span>Color: <?php echo $pending['product_color']; ?></span>
                                                    <span>Quantity: <?php echo $pending['quantity']; ?></span>
                                                </div>
                                                <div class="order-note">
                                                    <i class="fas fa-comment-alt"></i>
                                                    <span><?php echo $pending['user_note']; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="section-separator"></div>
                                        
                                        <div class="order-footer">
                                            <div class="price-section">
                                                Total: <?php echo $pending['total']; ?> 
                                            </div>
                                            <div class="action-buttons">
                                                <button class="btn btn-outline-danger">Cancel Order</button>
                                                <button class="btn btn-primary">Prepare Order</button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                    }
                                }
                                    ?>
                                <!--<p>No orders available for this status.</p>-->
                            
                        </div>


                        <!--=============== SHIPPED ORDERS TAB ===============-->
                        <div class="tab-pane fade" id="shipped" role="tabpanel">
                            <?php 
                                $select_preparing = "SELECT o.*, u.*, mp.*
                                                    FROM orders o
                                                    JOIN merchant_products mp ON o.product_id = mp.product_id
                                                    JOIN crafthub_users u ON o.user_id = u.user_id
                                                    WHERE mp.application_id = $application_id AND o.status = 'preparing'";

                                $result_preparing = mysqli_query($connection, $select_preparing);

                                if (!$result_preparing) {
                                    die("Query Failed: " . mysqli_error($connection));
                                } else {
                                    if (mysqli_num_rows($result_preparing) > 0) {
                                        while ($preparing = mysqli_fetch_assoc($result_preparing)) {
                                            ?>
                                            <div class="prod-container">
                                                <div class="order-header">
                                                    <div class="order-id">Order # <?php echo htmlspecialchars($preparing['order_id']); ?></div>
                                                    <div class="order-date"><?php echo date("F j, Y", strtotime($preparing['order_date'])); ?></div>
                                                </div>

                                                <div class="customer-info">
                                                    <div class="customer-info-item">
                                                        <div class="customer-info-label">Customer Name</div>
                                                        <div class="customer-info-value"><?php echo htmlspecialchars($preparing['first_name'] . ' ' . $preparing['middle_name'] . ' ' . $preparing['last_name']); ?></div>
                                                    </div>
                                                    <div class="customer-info-item">
                                                        <div class="customer-info-label">Contact Number</div>
                                                        <div class="customer-info-value"><?php echo htmlspecialchars($preparing['contact_no']); ?></div>
                                                    </div>
                                                    <div class="customer-info-item customer-address">
                                                        <div class="customer-info-label">Shipping Address</div>
                                                        <div class="customer-info-value"><?php echo htmlspecialchars($preparing['address']); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="section-separator"></div>
                                                
                                                <div class="product-section">
                                                    <img src="<?php echo 'uploads/' . htmlspecialchars($preparing['product_img']); ?>" 
                                                        class="product-image" 
                                                        alt="Product Image">
                                                    <div class="product-details">
                                                        <div class="product-name"><?php echo htmlspecialchars($preparing['product_name']); ?></div>
                                                        <div class="product-specs">
                                                            <span>Size: <?php echo htmlspecialchars($preparing['product_size']); ?></span>
                                                            <span>Color: <?php echo htmlspecialchars($preparing['product_color']); ?></span>
                                                            <span>Quantity: <?php echo htmlspecialchars($preparing['quantity']); ?></span>
                                                        </div>
                                                        <div class="order-note">
                                                            <i class="fas fa-comment-alt"></i>
                                                            <span><?php echo htmlspecialchars($preparing['user_note']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="section-separator"></div>
                                                
                                                <div class="order-footer">
                                                    <div class="price-section">
                                                        Total: <?php echo htmlspecialchars($preparing['total']); ?>
                                                    </div>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-outline-danger">Cancel Shipment</button>
                                                        <button class="btn btn-primary">Track Order</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    } else {
                                        echo "<p>No orders available for this status.</p>";
                                    }
                                }
                            ?>
                        </div>


                        <!--=============== COMPLETED ORDERS TAB =============== -->
                        <div class="tab-pane fade" id="completed" role="tabpanel">
                            <?php 
                                $select_completed = "SELECT o.*, u.*, mp.*
                                                    FROM orders o
                                                    JOIN merchant_products mp ON o.product_id = mp.product_id
                                                    JOIN crafthub_users u ON o.user_id = u.user_id
                                                    WHERE mp.application_id = $application_id AND o.status = 'order received'";

                                $result_completed = mysqli_query($connection, $select_completed);

                                if (!$result_completed) {
                                    die("Query Failed: " . mysqli_error($connection));
                                } else {
                                    if (mysqli_num_rows($result_completed) > 0) {
                                        while ($completed = mysqli_fetch_assoc($result_completed)) {
                                            ?>
                                            <div class="prod-container">
                                                <div class="order-header">
                                                    <div class="order-id">Order # <?php echo htmlspecialchars($completed['order_id']); ?></div>
                                                    <div class="order-date"><?php echo date("F j, Y", strtotime($completed['order_date'])); ?></div>
                                                </div>

                                                <div class="customer-info">
                                                    <div class="customer-info-item">
                                                        <div class="customer-info-label">Customer Name</div>
                                                        <div class="customer-info-value"><?php echo htmlspecialchars($completed['first_name'] . ' ' . $completed['middle_name'] . ' ' . $completed['last_name']); ?></div>
                                                    </div>
                                                    <div class="customer-info-item">
                                                        <div class="customer-info-label">Contact Number</div>
                                                        <div class="customer-info-value"><?php echo htmlspecialchars($completed['contact_no']); ?></div>
                                                    </div>
                                                    <div class="customer-info-item customer-address">
                                                        <div class="customer-info-label">Shipping Address</div>
                                                        <div class="customer-info-value"><?php echo htmlspecialchars($completed['address']); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="section-separator"></div>
                                                
                                                <div class="product-section">
                                                    <img src="<?php echo 'uploads/' . htmlspecialchars($completed['product_img']); ?>" 
                                                        class="product-image" 
                                                        alt="Product Image">
                                                    <div class="product-details">
                                                        <div class="product-name"><?php echo htmlspecialchars($completed['product_name']); ?></div>
                                                        <div class="product-specs">
                                                            <span>Size: <?php echo htmlspecialchars($completed['product_size']); ?></span>
                                                            <span>Color: <?php echo htmlspecialchars($completed['product_color']); ?></span>
                                                            <span>Quantity: <?php echo htmlspecialchars($completed['quantity']); ?></span>
                                                        </div>
                                                        <div class="order-note">
                                                            <i class="fas fa-comment-alt"></i>
                                                            <span><?php echo htmlspecialchars($completed['user_note']); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="section-separator"></div>
                                                
                                                <div class="order-footer">
                                                    <div class="price-section">
                                                        Total: <?php echo htmlspecialchars($completed['total']); ?>
                                                    </div>
                                                    <div class="action-buttons">
                                                        <button class="btn btn-primary">View Details</button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    } else {
                                        echo "<p>No completed orders available.</p>";
                                    }
                                }
                            ?>
                        </div>

                        <!--=============== CANCELLED ORDERS TAB ===============-->
                        <div class="tab-pane fade" id="cancelled" role="tabpanel">
                            <div class="prod-container">
                                <div class="order-header">
                                    <div class="order-id">Order #123459</div>
                                    <div class="order-date">March 12, 2024</div>
                                </div>

                                <div class="customer-info">
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Customer Name</div>
                                        <div class="customer-info-value">Sarah Wilson</div>
                                    </div>
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Contact Number</div>
                                        <div class="customer-info-value">+1234567893</div>
                                    </div>
                                    <div class="customer-info-item customer-address">
                                        <div class="customer-info-label">Shipping Address</div>
                                        <div class="customer-info-value">321 Sample Road, City, State, 12348</div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="product-section">
                                    <img src="https://images.unsplash.com/photo-1610701596088-799ab85198d4?w=800&q=80" 
                                         class="product-image" 
                                         alt="Macrame Plant Hanger">
                                    <div class="product-details">
                                        <div class="product-name">Macrame Plant Hanger</div>
                                        <div class="product-specs">
                                            <span>Size: Large</span>
                                            <span>Color: White</span>
                                            <span>Quantity: 2</span>
                                        </div>
                                        <div class="order-note">
                                            <i class="fas fa-comment-alt"></i>
                                            <span>Please make sure to wrap it carefully as this is a gift. Thank you!</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="order-footer">
                                    <div class="price-section">
                                        Total: ₱1,598.00
                                    </div>
                                    <div class="action-buttons">
                                        <button class="btn btn-primary">View Details</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--=============== RETURNED ORDERS TAB ===============-->
                        <div class="tab-pane fade" id="returned" role="tabpanel">
                            <div class="prod-container">
                                <div class="order-header">
                                    <div class="order-id">Order #123460</div>
                                    <div class="order-date">March 11, 2024</div>
                                </div>

                                <div class="customer-info">
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Customer Name</div>
                                        <div class="customer-info-value">Tom Brown</div>
                                    </div>
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Contact Number</div>
                                        <div class="customer-info-value">+1234567894</div>
                                    </div>
                                    <div class="customer-info-item customer-address">
                                        <div class="customer-info-label">Shipping Address</div>
                                        <div class="customer-info-value">654 Sample Lane, City, State, 12349</div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="product-section">
                                    <img src="https://images.unsplash.com/photo-1610701596135-d7f0c1b43501?w=800&q=80" 
                                         class="product-image" 
                                         alt="Handwoven Basket">
                                    <div class="product-details">
                                        <div class="product-name">Handwoven Basket</div>
                                        <div class="product-specs">
                                            <span>Size: Medium</span>
                                            <span>Color: Natural</span>
                                            <span>Quantity: 1</span>
                                        </div>
                                        <div class="order-note">
                                            <i class="fas fa-comment-alt"></i>
                                            <span>Please make sure to wrap it carefully as this is a gift. Thank you!</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="order-footer">
                                    <div class="price-section">
                                        Total: ₱1,299.00
                                    </div>
                                    <div class="action-buttons">
                                        <button class="btn btn-outline-danger">Reject Return</button>
                                        <button class="btn btn-primary">Accept Return</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--=============== ADD LISTINGS ===============-->
            <div class="content-section add-listings-section" style="display: none;">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="heading-with-count">Add New Product</h4>
                        </div>
                    </div>
                    
                    <div class="add-product-container">
                        <form id="product-form" action="add_product.php" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="image-upload-container">
                                        <div class="image-preview">
                                            <div id="image-preview">
                                                <i class="ri-image-add-line"></i>
                                                <span>Upload Product Image</span>
                                            </div>
                                            <img src="" alt="Product Image" class="prod-img" id="product_image" style="display: none;">
                                        </div>
                                        <input type="file" id="image-upload" name="product_image" accept="image/*" style="display: none;" required>
                                        <button type="button" class="btn btn-outline-primary w-100 mt-3" id="image-btn">
                                            <i class="ri-upload-2-line"></i> Choose Image
                                        </button>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label for="product_name">Product Name</label>
                                        <input type="text" name="product_name" class="form-control" placeholder="Enter product name" required>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="stock">Stock</label>
                                                <input type="number" name="stock" class="form-control" placeholder="Available quantity" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="product_category">Category</label>
                                                <select name="product_category" class="form-select" required>
                                                    <option value="">Select category</option>
                                                    <option value="Rugs">Rugs</option>
                                                    <option value="Tables">Tables</option>
                                                    <option value="Dining">Dining</option>
                                                    <option value="Lighting">Lighting</option>
                                                    <option value="Storage">Storage</option>
                                                    <option value="Furniture">Furniture</option>
                                                    <option value="Kitchen">Kitchen</option>
                                                    <option value="Decor">Decors</option>
                                                    <option value="Coasters">Coasters</option>
                                                    <option value="Trays">Trays</option>
                                                    <option value="Accessories">Accessories</option>
                                                    <option value="Others">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="product_desc">Product Description</label>
                                        <textarea name="product_desc" class="form-control" rows="3" placeholder="Describe your product" required></textarea>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="material">Materials Used</label>
                                        <div class="tags-input-container">
                                            <input type="text" class="tags-input" placeholder="Materials Used">
                                            <div class="tags-container"></div>
                                            <input type="hidden" name="materials" id="materials-hidden">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label>Colors</label>
                                        <div class="input-group">
                                            <input type="text" name="color[]" class="form-control" placeholder="Add a color">
                                            <button type="button" class="btn btn-primary" id="add-color">
                                                <i class="ri-add-line"></i>
                                            </button>
                                        </div>
                                        <div id="color-container"></div>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Sizes</label>
                                                <div class="input-group">
                                                    <input type="text" name="size[]" class="form-control" style="flex: 1;" placeholder="Add a size">
                                                </div>
                                                <div id="size-container"></div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label>Prices</label>
                                                <div class="input-group">
                                                    <input type="number" name="price[]" class="form-control" placeholder="Enter price">
                                                    <button type="button" class="btn btn-primary" id="add-size-price">
                                                        <i class="ri-add-line"></i>
                                                    </button>
                                                </div>
                                                <div id="price-container"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12" id="form-buttons">
                                    <!--=============== ADD PRODUCT BUTTON ===============-->
                                    <button type="submit" class="btn btn-primary w-100" id="add_product" name="add_product">
                                        Add Product
                                    </button>
                                    
                                    <!--=============== EDIT PRODUCT BUTTON ===============-->
                                    <div class="d-none" id="edit-buttons">
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-danger flex-grow-1" id="delete_product" name="delete_product">
                                                <i class="ri-delete-bin-line"></i> Delete Product
                                            </button>
                                            <button type="submit" class="btn btn-success flex-grow-1" id="save_changes" name="save_changes">
                                                <i class="ri-save-line"></i> Save Changes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!--=============== PROFILE SECTION ===============-->
            <div class="content-section profile-section" style="display: none;">
                <!--=============== HEADER IMAGE AND PROFILE INFO ===============-->
                <div class="container-fluid">
                    <div class="row position-relative">
                        <img src="images/mprofile.png" id="header-image" alt="header-image" class="img">
                        <div class="merchant-info">
                            <img src="" id="merchant-image" alt="merchant-image">
                            <div class="merchant-details">
                                <p class="merchant-name"></p>
                                <div class="shop-rating">
                                    <span class="rating">Shop Rating</span>
                                    <span class="rating-stars">
                                        <span class="rating-text">(ratings)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--=============== PROFILE BUSINESS FORM ===============-->
                <div class="container mt-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h4 class="mb-4">Business Information</h4>
                            <form action="" method="post" id="businessForm">
                                <div class="form-group mb-3">
                                    <label class="form-label">Business Name</label>
                                    <input type="text" name="business_name" class="form-control" value="" readonly>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Business Contact</label>
                                            <input type="text" name="business_contact" class="form-control" value="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Business Email</label>
                                            <input type="email" name="business_email" class="form-control" value="" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label">Business Address</label>
                                    <input type="text" name="business_address" class="form-control" value="" readonly>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Municipality</label>
                                            <input type="text" name="municipality" class="form-control" value="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Barangay</label>
                                            <input type="text" name="barangay" class="form-control" value="" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Street</label>
                                            <input type="text" name="street" class="form-control" value="" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end mt-4">
                                    <button type="button" class="btn btn-primary" id="editBusinessBtn">
                                        <i class="fas fa-edit"></i> Edit Information
                                    </button>
                                    <button type="submit" class="btn btn-success d-none" id="saveBusinessBtn">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                    <button type="button" class="btn btn-danger d-none" id="cancelBusinessBtn">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!--=============== RATINGS SECTION ===============-->
            <div class="content-section ratings-section" style="display: none;">
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-12">
                            <h4 class="heading-with-count">Product Ratings <span class="product-count">8</span></h4>
                        </div>
                    </div>
                    
                    <!--=============== RATING CARDS ===============-->
                    <div class="rating-container mb-4">
                        <div class="user-info-section">
                            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=200&q=80" 
                                 class="user-avatar" 
                                 alt="Jane Smith">
                            <div class="user-details">
                                <div class="user-header">
                                    <h5 class="user-name">Jane Smith</h5>
                                    <span class="rating-date">March 10, 2024</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-info-section">
                            <img src="https://images.unsplash.com/photo-1612858250434-b5358e2b3625?w=800&q=80" 
                                 class="product-thumbnail" 
                                 alt="Handwoven Table Runner">
                            <div class="product-details">
                                <h6 class="product-title">Handwoven Table Runner</h6>
                                <div class="rating-details">
                                    <div class="rating-item">
                                        <span class="rating-label">Quality:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="rating-item">
                                        <span class="rating-label">Price:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="rating-item">
                                        <span class="rating-label">Seller Service:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="feedback-text">
                            Great product! The craftsmanship is excellent and the natural materials used are of high quality. The table runner adds a beautiful rustic touch to my dining room.
                        </div>
                    </div>

                    <div class="rating-container mb-4">
                        <div class="user-info-section">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&q=80" 
                                 class="user-avatar" 
                                 alt="Michael Chen">
                            <div class="user-details">
                                <div class="user-header">
                                    <h5 class="user-name">Michael Chen</h5>
                                    <span class="rating-date">March 8, 2024</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-info-section">
                            <img src="https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?w=800&q=80" 
                                 class="product-thumbnail" 
                                 alt="Ceramic Flower Vase">
                            <div class="product-details">
                                <h6 class="product-title">Ceramic Flower Vase</h6>
                                <div class="rating-details">
                                    <div class="rating-item">
                                        <span class="rating-label">Quality:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="rating-item">
                                        <span class="rating-label">Price:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="rating-item">
                                        <span class="rating-label">Seller Service:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="feedback-text">
                            Absolutely stunning piece! The attention to detail in this ceramic vase is remarkable. It's become the centerpiece of my living room and I've received many compliments.
                        </div>
                    </div>

                    <div class="rating-container mb-4">
                        <div class="user-info-section">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=200&q=80" 
                                 class="user-avatar" 
                                 alt="Sarah Johnson">
                            <div class="user-details">
                                <div class="user-header">
                                    <h5 class="user-name">Sarah Johnson</h5>
                                    <span class="rating-date">March 5, 2024</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-info-section">
                            <img src="https://images.unsplash.com/photo-1617791160505-6f00504e3519?w=800&q=80" 
                                 class="product-thumbnail" 
                                 alt="Macrame Wall Hanging">
                            <div class="product-details">
                                <h6 class="product-title">Macrame Wall Hanging</h6>
                                <div class="rating-details">
                                    <div class="rating-item">
                                        <span class="rating-label">Quality:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                    </div>
                                    <div class="rating-item">
                                        <span class="rating-label">Price:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                        </div>
                                    </div>
                                    <div class="rating-item">
                                        <span class="rating-label">Seller Service:</span>
                                        <div class="stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="feedback-text">
                            Beautiful macrame work! The pattern is intricate and the cotton rope used is of great quality. It's exactly what I was looking for to decorate my bedroom wall.
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-section messages-section" style="display: none;">
                <div class="container">
                    <div class="chat-wrapper">
                        <div class="sidebar">
                            <!--=============== SEARCH ===============-->
                            <div class="search-container">
                                <input type="text" id="searchInput" class="search-input" placeholder="Search conversations">
                                <i class="fas fa-search search-icon"></i>
                            </div>
                            <!--=============== CHAT LIST ===============-->
                            <ul class="chat-list">
                                <li class="chat-item unread" onclick="selectChat(this, 'John Doe', 'Online')">
                                    <a href="javascript:void(0);">
                                        <img src="images/default-user.png" alt="User Avatar" class="chat-avatar">
                                        <div class="chat-info">
                                            <h5 class="chat-name">John Doe</h5>
                                            <p class="chat-preview">Hello, is this item still available?</p>
                                        </div>
                                        <span class="chat-time">2m ago</span>
                                    </a>
                                </li>
                                <li class="chat-item" onclick="selectChat(this, 'Jane Smith', 'Last seen 5m ago')">
                                    <a href="javascript:void(0);">
                                        <img src="images/default-user.png" alt="User Avatar" class="chat-avatar">
                                        <div class="chat-info">
                                            <h5 class="chat-name">Jane Smith</h5>
                                            <p class="chat-preview">Thank you for the quick delivery!</p>
                                        </div>
                                        <span class="chat-time">1h ago</span>
                                    </a>
                                </li>
                                <li class="chat-item" onclick="selectChat(this, 'Mike Johnson', 'Last seen 1h ago')">
                                    <a href="javascript:void(0);">
                                        <img src="images/default-user.png" alt="User Avatar" class="chat-avatar">
                                        <div class="chat-info">
                                            <h5 class="chat-name">Mike Johnson</h5>
                                            <p class="chat-preview">Can you ship to my address?</p>
                                        </div>
                                        <span class="chat-time">Yesterday</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!--=============== CHAT MAIN ===============-->
                        <div class="chat-main">
                            <div class="chat-header">
                                <img src="images/default-user.png" alt="avatar" class="chat-avatar">
                                <div class="chat-user-info">
                                    <h2 class="chat-user-name">Select a conversation</h2>
                                    <p class="chat-user-status"></p>
                                </div>
                                <div class="chat-actions">
                                    <button class="btn-action" id="captureBtn"><i class="fas fa-camera"></i></button>
                                    <button class="btn-action" id="uploadBtn"><i class="fas fa-image"></i></button>
                                </div>
                            </div>
                            <div class="chat-history" id="chatHistory">
                                <ul class="m-b-0">
                                </ul>
                            </div>
                            <div class="chat-input-area">
                                <textarea id="messageInput" class="chat-input" placeholder="Type a message..." disabled></textarea>
                                <button class="btn-send" id="sendBtn" disabled><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            let selectedChatItem = null;

            function selectChat(chatItem, userName, userStatus) {
                // Remove active class from previously selected chat
                if (selectedChatItem) {
                    selectedChatItem.classList.remove('active');
                }
                
                // Add active class to selected chat
                chatItem.classList.add('active');
                selectedChatItem = chatItem;
                
                // Update chat header
                document.querySelector('.chat-user-name').textContent = userName;
                document.querySelector('.chat-user-status').textContent = userStatus;
                
                // Enable input and send button
                document.getElementById('messageInput').disabled = false;
                document.getElementById('sendBtn').disabled = false;
                
                // Clear chat history and add sample messages
                const chatHistory = document.querySelector('#chatHistory ul');
                chatHistory.innerHTML = `
                    <li style="display: flex; justify-content: flex-start;">
                        <div class="message received">
                            <div class="message-content">Hello, is this item still available?</div>
                        </div>
                    </li>
                    <li style="display: flex; justify-content: flex-end;">
                        <div class="message sent">
                            <div class="message-content">Yes, it's still available! Would you like to place an order?</div>
                        </div>
                    </li>
                `;
                
                // Remove unread status if present
                chatItem.classList.remove('unread');
            }

            // Send message functionality
            document.getElementById('sendBtn').addEventListener('click', sendMessage);
            document.getElementById('messageInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            function sendMessage() {
                const messageInput = document.getElementById('messageInput');
                const message = messageInput.value.trim();
                
                if (message && selectedChatItem) {
                    // Add message to chat history
                    const chatHistory = document.querySelector('#chatHistory ul');
                    chatHistory.innerHTML += `
                        <li style="display: flex; justify-content: flex-end;">
                            <div class="message sent">
                                <div class="message-content">${message}</div>
                            </div>
                        </li>
                    `;
                    
                    // Clear input
                    messageInput.value = '';
                    
                    // Scroll to bottom
                    const chatContainer = document.getElementById('chatHistory');
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                    
                    // Update preview in chat list
                    selectedChatItem.querySelector('.chat-preview').textContent = message;
                    selectedChatItem.querySelector('.chat-time').textContent = 'Just now';
                }
            }
            </script>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
    <script src="js/shopDashboard.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showSection(sectionName) {
            // Hide all sections
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Show selected section and activate menu item
            document.querySelector('.' + sectionName + '-section').style.display = 'block';
            document.querySelector(`[data-section="${sectionName}"]`).classList.add('active');
            
            // Save the active section to localStorage
            localStorage.setItem('activeSection', sectionName);
        }

        // Load the last active section or default to dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const lastActiveSection = localStorage.getItem('activeSection') || 'dashboard';
            showSection(lastActiveSection);
            
            // If we're in the orders section and there's a stored tab
            if (lastActiveSection === 'orders') {
                const lastActiveTab = localStorage.getItem('activeOrderTab');
                if (lastActiveTab) {
                    const tabElement = document.querySelector(`#${lastActiveTab}-tab`);
                    if (tabElement) {
                        const tab = new bootstrap.Tab(tabElement);
                        tab.show();
                    }
                }
            }
        });

        function showOrdersTab(tabName) {
            // First switch to orders section
            showSection('orders');
            
            // Then activate the appropriate tab using Bootstrap's Tab API
            const tabElement = document.querySelector(`#${tabName}-tab`);
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
            
            // Save the active tab to localStorage
            localStorage.setItem('activeOrderTab', tabName);
        }

        // Add event listeners for order tabs
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(event) {
                const tabId = event.target.id;
                const tabName = tabId.replace('-tab', '');
                localStorage.setItem('activeOrderTab', tabName);
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageUpload = document.getElementById('image-upload');
            const imageBtn = document.getElementById('image-btn');
            const productImage = document.getElementById('product_image');
            const imagePreview = document.getElementById('image-preview');

            // Image upload functionality
            imageBtn.addEventListener('click', function() {
                imageUpload.click();
            });

            imageUpload.addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        productImage.src = e.target.result;
                        productImage.style.display = 'block';
                        imagePreview.style.display = 'none';
                    };

                    reader.readAsDataURL(file);
                }
            });

            // Color, Size, and Price functionality
            const colorContainer = document.getElementById('color-container');
            const sizeContainer = document.getElementById('size-container');
            const priceContainer = document.getElementById('price-container');
            const addColorButton = document.getElementById('add-color');
            const addSizePriceButton = document.getElementById('add-size-price');

            function addColor() {
                const newColorInput = document.createElement('div');
                newColorInput.classList.add('input-group', 'mt-2');
                newColorInput.innerHTML = `
                    <input type="text" name="color[]" class="form-control" placeholder="Add a color">
                    <button type="button" class="btn btn-danger remove-color">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                `;
                colorContainer.appendChild(newColorInput);

                // Add event listener to the remove button
                newColorInput.querySelector('.remove-color').addEventListener('click', function() {
                    colorContainer.removeChild(newColorInput);
                });
            }

            function addSizePrice() {
                const newSizeInput = document.createElement('div');
                newSizeInput.classList.add('input-group', 'mt-2');
                newSizeInput.innerHTML = `
                    <input type="text" name="size[]" class="form-control" style="flex: 1;" placeholder="Add a size">
                `;
                sizeContainer.appendChild(newSizeInput);

                const newPriceInput = document.createElement('div');
                newPriceInput.classList.add('input-group', 'mt-2');
                newPriceInput.innerHTML = `
                    <input type="number" name="price[]" class="form-control" placeholder="Enter price">
                    <button type="button" class="btn btn-danger remove-size-price">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                `;
                priceContainer.appendChild(newPriceInput);

                // Add event listener to the remove button
                newPriceInput.querySelector('.remove-size-price').addEventListener('click', function() {
                    sizeContainer.removeChild(newSizeInput);
                    priceContainer.removeChild(newPriceInput);
                });
            }

            // Add event listeners to the add buttons
            addColorButton.addEventListener('click', addColor);
            addSizePriceButton.addEventListener('click', addSizePrice);
        });
    </script>

    <script>
    function showEditMode(button) {
        // Get product ID
        const productId = button.getAttribute('data-product-id');
        
        // Switch to add-listings section
        showSection('add-listings');
        
        // Hide Add Product button and show edit mode buttons
        document.getElementById('add_product').classList.add('d-none');
        document.getElementById('edit-buttons').classList.remove('d-none');
        
        // TODO: Fetch and populate form with product data
        // This would be your AJAX call to get product details
        fetchProductData(productId);
    }

    // Function to reset form to Add Product mode
    function resetToAddMode() {
        document.getElementById('add_product').classList.remove('d-none');
        document.getElementById('edit-buttons').classList.add('d-none');
        document.getElementById('product-form').reset();
    }

    // Add event listener to handle section changes
    document.addEventListener('DOMContentLoaded', function() {
        const originalShowSection = window.showSection;
        window.showSection = function(sectionName) {
            originalShowSection(sectionName);
            
            // Reset form to Add Product mode when switching to a different section
            if (sectionName !== 'add-listings') {
                resetToAddMode();
            }
        };
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tagsContainer = document.querySelector('.tags-container');
        const tagsInput = document.querySelector('.tags-input');
        const hiddenInput = document.querySelector('#materials-hidden');
        let tags = [];

        function updateTags() {
            tagsContainer.innerHTML = '';
            hiddenInput.value = JSON.stringify(tags);
            
            tags.forEach((tag, index) => {
                const tagElement = document.createElement('span');
                tagElement.classList.add('tag');
                tagElement.innerHTML = `
                    ${tag}
                    <span class="remove-tag" data-index="${index}">
                        <i class="ri-close-line"></i>
                    </span>
                `;
                tagsContainer.appendChild(tagElement);
            });
        }

        function addTag(tag) {
            tag = tag.trim();
            if (tag && !tags.includes(tag)) {
                tags.push(tag);
                updateTags();
            }
            tagsInput.value = '';
        }

        function removeTag(index) {
            const tagElement = tagsContainer.children[index];
            tagElement.classList.add('removing');
            
            setTimeout(() => {
                tags.splice(index, 1);
                updateTags();
            }, 200); // Match this with the CSS animation duration
        }

        tagsInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                addTag(tagsInput.value);
            }
        });

        tagsInput.addEventListener('blur', () => {
            if (tagsInput.value.trim()) {
                addTag(tagsInput.value);
            }
        });

        tagsContainer.addEventListener('click', (e) => {
            if (e.target.closest('.remove-tag')) {
                const index = parseInt(e.target.closest('.remove-tag').dataset.index);
                removeTag(index);
            }
        });
    });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editBusinessBtn = document.getElementById('editBusinessBtn');
        const saveBusinessBtn = document.getElementById('saveBusinessBtn');
        const cancelBusinessBtn = document.getElementById('cancelBusinessBtn');
        const businessForm = document.getElementById('businessForm');
        const formInputs = businessForm.querySelectorAll('input');
        
        // Store original values for cancel functionality
        let originalValues = {};
        
        // Enable form editing
        editBusinessBtn.addEventListener('click', function() {
            // Store current values
            formInputs.forEach(input => {
                originalValues[input.name] = input.value;
                input.removeAttribute('readonly');
            });
            
            // Show save/cancel buttons, hide edit button
            editBusinessBtn.classList.add('d-none');
            saveBusinessBtn.classList.remove('d-none');
            cancelBusinessBtn.classList.remove('d-none');
        });
        
        // Cancel editing
        cancelBusinessBtn.addEventListener('click', function() {
            // Restore original values
            formInputs.forEach(input => {
                input.value = originalValues[input.name];
                input.setAttribute('readonly', true);
            });
            
            // Show edit button, hide save/cancel buttons
            editBusinessBtn.classList.remove('d-none');
            saveBusinessBtn.classList.add('d-none');
            cancelBusinessBtn.classList.add('d-none');
        });
        
        // Handle form submission
        businessForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically send the data to the server
            // For now, we'll just simulate a successful update
            formInputs.forEach(input => {
                input.setAttribute('readonly', true);
            });
            
            // Show edit button, hide save/cancel buttons
            editBusinessBtn.classList.remove('d-none');
            saveBusinessBtn.classList.add('d-none');
            cancelBusinessBtn.classList.add('d-none');
            
            // Show success message
            alert('Business information updated successfully!');
        });
    });
    </script>

    <script>
        var CURRENT_USER_ID = <?php echo json_encode($_SESSION['userID']); ?>;
        var CURRENT_USER_TYPE = 'merchant';
        var CURRENT_CHAT_PARTNER_ID = <?php echo isset($chat_partner_id) ? json_encode($chat_partner_id) : 'null'; ?>;
        var CURRENT_CHAT_PARTNER_TYPE = <?php echo isset($chat_partner_type) ? json_encode($chat_partner_type) : 'null'; ?>;

        function fetchMessages() {
            if (!CURRENT_CHAT_PARTNER_ID || !CURRENT_CHAT_PARTNER_TYPE) {
                var chatHistory = document.querySelector('.chat-history ul');
                chatHistory.innerHTML = '<div class="empty-chat-state"><div class="empty-chat-message"><i class="fas fa-comments"></i><p>Select a conversation to start messaging</p></div></div>';
                return;
            }

            fetch('fetch_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    sender_id: CURRENT_USER_ID,
                    sender_type: CURRENT_USER_TYPE,
                    receiver_id: CURRENT_CHAT_PARTNER_ID,
                    receiver_type: CURRENT_CHAT_PARTNER_TYPE,
                }),
            })
            .then(response => response.json())
            .then(data => {
                var chatHistory = document.querySelector('.chat-history ul');
                chatHistory.innerHTML = '';
                
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(function(message) {
                        var direction = (message.sender_id == CURRENT_USER_ID && message.sender_type == CURRENT_USER_TYPE) ? 'outgoing' : 'incoming';
                        var mediaPath = direction == 'incoming' ? '../'+message.media_path : message.media_path;
                        addMessageToChatHistory(message.message_type === 'text' ? message.message : mediaPath, direction, message.message_type);
                    });
                } else {
                    chatHistory.innerHTML = '<div class="no-message"><p>Start a conversation</p></div>';
                }
                
                const chatContainer = document.getElementById('chatHistory0');
                chatContainer.scrollTop = chatContainer.scrollHeight;
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        }

        function fetchChatList() {
            fetch('fetch_chat_list_merchant.php')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('tabList').innerHTML = html;
                    
                    if (tabList.innerHTML.trim() === '') {
                        tabList.innerHTML = '<div class="empty-chat-state"><div class="empty-chat-message"><i class="fas fa-comments"></i><p>No conversations yet</p></div></div>';
                    }
                })
                .catch(error => console.error('Error fetching chat list:', error));
        }

        // Initialize chat functionality when messages section is shown
        document.querySelector('[data-section="messages"]').addEventListener('click', function() {
            // Initial fetches
            fetchMessages();
            fetchChatList();

            // Set up intervals for periodic updates
            setInterval(fetchMessages, 5000);
            setInterval(fetchChatList, 3000);
        });
    </script>
    <script src="js/chat.js"></script>

</body>
</html>
