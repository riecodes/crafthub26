<?php
ob_start();
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

    // Calculate total orders
    $total_orders = 0;
    
    // Add up all the order counts
    if(isset($totalPending)) $total_orders += $totalPending;
    if(isset($totalShip)) $total_orders += $totalShip;
    if(isset($totalOutgoing)) $total_orders += $totalOutgoing;
    if(isset($totalCompleted)) $total_orders += $totalCompleted;
    if(isset($totalCancel)) $total_orders += $totalCancel;
    if(isset($totalReturned)) $total_orders += $totalReturned;

    //$toPayOrders = getOrders($connection, $application_id, 'to pay');

    

?>

<div class="container">
    <div class="row mb-4 align-items-center">
        <div class="col-12">
            <h4 class="heading-with-count">Order Management <span class="product-count"><?php echo $total_orders; ?></span></h4>
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
                <button class="nav-link" id="toship-tab" data-bs-toggle="tab" data-bs-target="#shipped" type="button" role="tab">
                    <i class="fa-solid fa-box"></i> To Ship Orders
                    <span class="count-badge"><?php echo $totalShip; ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="outgoing-tab" data-bs-toggle="tab" data-bs-target="#outgoing" type="button" role="tab">
                    <i class="fa-solid fa-truck"></i> Outgoing Orders
                    <span class="count-badge">0</span>
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
                } else {
                    if(mysqli_num_rows($result_pending) > 0) {
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
                                      
                                        <button type="button" class="btn btn-primary"> <a href="merchantupdate_order.php?order_id=<?php echo $pending['order_id'] ?>">Prepare Order</a></button>
                                    </div>
                                </div>
                            </div>
                            <?php 
                        }
                    } else {
                        echo '<div class="empty-state-message">
                                <h3>No pending orders yet</h3>
                                <p>When customers place new orders, they will appear here</p>
                            </div>';
                    }
                }
            ?>
        </div>

        <!--=============== TO SHIP ORDERS TAB ===============-->
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
                                        
                                        <button class="btn btn-primary"><a href="merchant_ship_order.php?order_id=<?php echo $preparing['order_id'] ?>">Ship Order</a></button>
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        echo '<div class="empty-state-message">
                                <h3>No shipped orders yet</h3>
                                <p>Orders that are being shipped will appear here</p>
                            </div>';
                    }
                }
            ?>
        </div>

        <!--=============== OUTGOING ORDERS TAB ===============-->
        <div class="tab-pane fade" id="outgoing" role="tabpanel">
            <div class="prod-container">
                <div class="order-header">
                    <div class="order-id">Order #12345</div>
                    <div class="order-date">January 15, 2024</div>
                </div>

                <div class="customer-info">
                    <div class="customer-info-item">
                        <div class="customer-info-label">Customer Name</div>
                        <div class="customer-info-value">John Doe</div>
                    </div>
                    <div class="customer-info-item">
                        <div class="customer-info-label">Contact Number</div>
                        <div class="customer-info-value">+63 912 345 6789</div>
                    </div>
                    <div class="customer-info-item customer-address">
                        <div class="customer-info-label">Shipping Address</div>
                        <div class="customer-info-value">123 Sample Street, Barangay Sample, City Sample</div>
                    </div>
                </div>
                
                <div class="section-separator"></div>
                
                <div class="product-section">
                    <img src="images/cea.jpg" 
                        class="product-image" 
                        alt="Product Image">
                    <div class="product-details">
                        <div class="product-name">Sample Product Name</div>
                        <div class="product-specs">
                            <span>Size: Medium</span>
                            <span>Color: Blue</span>
                            <span>Quantity: 2</span>
                        </div>
                        <div class="order-note">
                            <i class="fas fa-comment-alt"></i>
                            <span>Please handle with care</span>
                        </div>
                    </div>
                </div>
                
                <div class="section-separator"></div>
                <div class="order-footer">
                    <div class="price-section">
                        Total: ₱1,500
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-primary">Mark as Delivered</button>
                    </div>
                </div>
            </div>
        </div>

        <!--=============== COMPLETED ORDERS TAB ===============-->
        <div class="tab-pane fade" id="completed" role="tabpanel">
            <?php 
                $select_completed = "SELECT o.*, u.*, mp.*
                                    FROM orders o
                                    JOIN merchant_products mp ON o.product_id = mp.product_id
                                    JOIN crafthub_users u ON o.user_id = u.user_id
                                    WHERE mp.application_id = $application_id AND (o.status = 'order received' OR o.status = 'already rated')";

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
                                        
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        echo '<div class="empty-state-message">
                                <h3>No completed orders yet</h3>
                                <p>Successfully delivered orders will appear here</p>
                            </div>';
                    }
                }
            ?>
        </div>

        <!--=============== CANCELLED ORDERS TAB ===============-->
        <div class="tab-pane fade" id="cancelled" role="tabpanel">
            <?php 
                $select_cancelled = "SELECT o.*, u.*, mp.*
                                    FROM orders o
                                    JOIN merchant_products mp ON o.product_id = mp.product_id
                                    JOIN crafthub_users u ON o.user_id = u.user_id
                                    WHERE mp.application_id = $application_id AND o.status = 'cancelled'";

                $result_cancelled = mysqli_query($connection, $select_cancelled);

                if (!$result_cancelled) {
                    die("Query Failed: " . mysqli_error($connection));
                } else {
                    if (mysqli_num_rows($result_cancelled) > 0) {
                        while ($cancelled = mysqli_fetch_assoc($result_cancelled)) {
                            ?>
                            <div class="prod-container">
                                <div class="order-header">
                                    <div class="order-id">Order # <?php echo htmlspecialchars($cancelled['order_id']); ?></div>
                                    <div class="order-date"><?php echo date("F j, Y", strtotime($cancelled['order_date'])); ?></div>
                                </div>

                                <div class="customer-info">
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Customer Name</div>
                                        <div class="customer-info-value"><?php echo htmlspecialchars($cancelled['first_name'] . ' ' . $cancelled['middle_name'] . ' ' . $cancelled['last_name']); ?></div>
                                    </div>
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Contact Number</div>
                                        <div class="customer-info-value"><?php echo htmlspecialchars($cancelled['contact_no']); ?></div>
                                    </div>
                                    <div class="customer-info-item customer-address">
                                        <div class="customer-info-label">Shipping Address</div>
                                        <div class="customer-info-value"><?php echo htmlspecialchars($cancelled['address']); ?></div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="product-section">
                                    <img src="<?php echo 'uploads/' . htmlspecialchars($cancelled['product_img']); ?>" 
                                        class="product-image" 
                                        alt="Product Image">
                                    <div class="product-details">
                                        <div class="product-name"><?php echo htmlspecialchars($cancelled['product_name']); ?></div>
                                        <div class="product-specs">
                                            <span>Size: <?php echo htmlspecialchars($cancelled['product_size']); ?></span>
                                            <span>Color: <?php echo htmlspecialchars($cancelled['product_color']); ?></span>
                                            <span>Quantity: <?php echo htmlspecialchars($cancelled['quantity']); ?></span>
                                        </div>
                                        <div class="order-note">
                                            <i class="fas fa-comment-alt"></i>
                                            <span><?php echo htmlspecialchars($cancelled['user_note']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="order-footer">
                                    <div class="price-section">
                                        Total: <?php echo htmlspecialchars($cancelled['total']); ?>
                                    </div>
                                    <div class="action-buttons">
                                        
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        echo '<div class="empty-state-message">
                                <h3>No cancelled orders yet</h3>
                                <p>Cancelled orders will appear here</p>
                            </div>';
                    }
                }
            ?>
        </div>


        <!--=============== RETURNED ORDERS TAB ===============-->
        <div class="tab-pane fade" id="returned" role="tabpanel">
            <?php 
                $select_returned = "SELECT o.*, u.*, mp.*
                                    FROM orders o
                                    JOIN merchant_products mp ON o.product_id = mp.product_id
                                    JOIN crafthub_users u ON o.user_id = u.user_id
                                    WHERE mp.application_id = $application_id AND o.status = 'returned'";

                $result_returned = mysqli_query($connection, $select_returned);

                if (!$result_returned) {
                    die("Query Failed: " . mysqli_error($connection));
                } else {
                    if (mysqli_num_rows($result_returned) > 0) {
                        while ($returned = mysqli_fetch_assoc($result_returned)) {
                            ?>
                            <div class="prod-container">
                                <div class="order-header">
                                    <div class="order-id">Order # <?php echo htmlspecialchars($returned['order_id']); ?></div>
                                    <div class="order-date"><?php echo date("F j, Y", strtotime($returned['order_date'])); ?></div>
                                </div>

                                <div class="customer-info">
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Customer Name</div>
                                        <div class="customer-info-value"><?php echo htmlspecialchars($returned['first_name'] . ' ' . $returned['middle_name'] . ' ' . $returned['last_name']); ?></div>
                                    </div>
                                    <div class="customer-info-item">
                                        <div class="customer-info-label">Contact Number</div>
                                        <div class="customer-info-value"><?php echo htmlspecialchars($returned['contact_no']); ?></div>
                                    </div>
                                    <div class="customer-info-item customer-address">
                                        <div class="customer-info-label">Shipping Address</div>
                                        <div class="customer-info-value"><?php echo htmlspecialchars($returned['address']); ?></div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="product-section">
                                    <img src="<?php echo 'uploads/' . htmlspecialchars($returned['product_img']); ?>" 
                                        class="product-image" 
                                        alt="Product Image">
                                    <div class="product-details">
                                        <div class="product-name"><?php echo htmlspecialchars($returned['product_name']); ?></div>
                                        <div class="product-specs">
                                            <span>Size: <?php echo htmlspecialchars($returned['product_size']); ?></span>
                                            <span>Color: <?php echo htmlspecialchars($returned['product_color']); ?></span>
                                            <span>Quantity: <?php echo htmlspecialchars($returned['quantity']); ?></span>
                                        </div>
                                        <div class="order-note">
                                            <i class="fas fa-comment-alt"></i>
                                            <span><?php echo htmlspecialchars($returned['user_note']); ?></span>
                                        </div>
                                        <div class="return-details">
                                            <div class="return-reason">
                                                <div class="return-label">Reason for Return:</div>
                                                <div class="return-value">Product damaged upon arrival</div>
                                            </div>
                                            <div class="return-additional">
                                                <div class="return-label">Additional Details:</div>
                                                <div class="return-value">Box was crushed and item inside was broken</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="section-separator"></div>
                                
                                <div class="order-footer">
                                    <div class="price-section">
                                        Total: <?php echo htmlspecialchars($returned['total']); ?>
                                    </div>
                                    <div class="action-buttons">
         
                                    </div>
                                </div>
                            </div>
                        <?php
                        }
                    } else {
                        echo '<div class="empty-state-message">
                                <h3>No returned orders yet</h3>
                                <p>Product returns will appear here</p>
                            </div>';
                    }
                }
            ?>
        </div>  

    </div>
</div>

<script src="js/dashboard.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the tab parameter from URL
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab) {
        // Find the tab element
        const tabElement = document.querySelector(`#${tab}-tab`);
        if (tabElement) {
            // Create a new bootstrap tab instance and show it
            const bsTab = new bootstrap.Tab(tabElement);
            bsTab.show();
        }
    }
});
</script>

<?php
$content = ob_get_clean();
include 'mersidebar.php';
?> 