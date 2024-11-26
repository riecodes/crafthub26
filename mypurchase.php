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
    function getOrders($connection, $user_id, $status) {
        $query = "SELECT
                    o.*, 
                    p.product_img, 
                    p.product_name, 
                    m.application_id,
                    m.shop_name
                FROM 
                    orders o
                JOIN 
                    merchant_products p ON o.product_id = p.product_id
                JOIN 
                    merchant_applications m ON p.application_id = m.application_id
               
                WHERE 
                    o.user_id = ? AND o.status = ?";
    
        $stmt = $connection->prepare($query);
        $stmt->bind_param('ss', $user_id, $status);
        $stmt->execute();
        return $stmt->get_result();
    }
    
    // Fetch 'to pay' orders specifically for the logged-in user
    $to_pay_orders = getOrders($connection, $user_id, 'to pay');
    $to_ship_orders = getOrders($connection, $user_id, 'preparing');
    $to_receive_orders = getOrders($connection, $user_id, 'shipped');
    $to_rate_orders = getOrders($connection, $user_id, 'order received');
    $cancelled = getOrders($connection, $user_id, 'cancelled');
    $returned = getOrders($connection, $user_id, 'return');
    $completed = getOrders($connection, $user_id, 'already rated');
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--=============== BOXICONS ===============-->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/mypurchase.css">
    <link rel="stylesheet" href="css/footer.css">
</head>

        <?php include 'nav.php'; ?>
        
   <!--=============== NAVTABS ===============-->
    <div class="container-fluid mypurchase-container">
         <!--=============== NAVTAB HEADERS ===============-->
         <ul class="nav nav-tabs nav-justified" id="responsive-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#to-pay">To Pay <span class="badge badge-custom"></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#to-ship">To Ship <span class="badge badge-custom"></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#to-receive">To Receive <span class="badge badge-custom"></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#to-rate">To Rate <span class="badge badge-custom"></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#cancelled">Cancelled <span class="badge badge-custom"></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#return">Return <span class="badge badge-custom"></span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#completed">Completed <span class="badge badge-custom"></span></a>
        </li>
        </ul>
        <!--=============== END OF NAVTAB HEADERS ===============-->


        <!--=============== TAB PANES ===============-->
        <div id="common-content" class="tab-content active">
            <div class="tab-pane fade show active" id="to-pay">
                <div class="order-forms-container">
                    <?php
                    if (mysqli_num_rows($to_pay_orders) > 0) {
                        while ($topay = mysqli_fetch_assoc($to_pay_orders)) {
                    ?>
                    <div class="prod-container mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info">
                                    <span class="shop-name"><i class="fas fa-store"></i> <?php echo htmlspecialchars($topay['shop_name']); ?></span>
                                </div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="<?php echo 'uploads/' . htmlspecialchars(basename($topay['product_img'])); ?>" id="product-image" alt="product image" class="img-fluid">
                            </div>
                            <div class="col-md-10">
                                <div class="product-name"><?php echo htmlspecialchars($topay['product_name']); ?></div>
                                <div class="order-date">Order Date: <?php echo date("F j, Y g:i A", strtotime($topay['order_date'])); ?></div>
                                <div class="product-variation">
                                    <span><strong>Color:</strong> <em><?php echo htmlspecialchars($topay['product_color']); ?></em></span>
                                    <span><strong>Size:</strong> <em><?php echo htmlspecialchars($topay['product_size']); ?></em></span>
                                    <span><strong>Qty:</strong> <em><?php echo htmlspecialchars($topay['quantity']); ?></em></span>
                                </div>
                                <div class="order-note">Note: <input type="text" value="<?php echo htmlspecialchars($topay['user_note']); ?>" readonly></div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row">
                            <div class="col-md-6"></div>
                            <div class="col-md-6 text-end">
                                <div class="highlight-price">Total Price: ₱<?php echo number_format($topay['total'], 2); ?></div>
                                <div class="product-actions">
                                    <button type="button" class="btn btn-danger">
                                        <a href="cancel_order.php?order_id=<?php echo htmlspecialchars($topay['order_id']); ?>">Cancel</a>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<p class="no-products">No products available</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="tab-pane fade" id="to-ship">
                <div class="order-forms-container">
                    <?php
                    if (mysqli_num_rows($to_ship_orders) > 0) {
                        while ($toship = mysqli_fetch_assoc($to_ship_orders)) {
                    ?>
                    <div class="prod-container mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info">
                                    <span class="shop-name"><i class="fas fa-store"></i> <?php echo htmlspecialchars($toship['shop_name']); ?></span>
                                </div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="<?php echo 'uploads/' . htmlspecialchars(basename($toship['product_img'])); ?>" id="product-image" alt="product image" class="img-fluid">
                            </div>
                            <div class="col-md-10">
                                <div class="product-name"><?php echo htmlspecialchars($toship['product_name']); ?></div>
                                <div class="order-date">Order Date: <?php echo date("F j, Y g:i A", strtotime($toship['order_date'])); ?></div>
                                <div class="product-variation">
                                    <span><strong>Color:</strong> <em><?php echo htmlspecialchars($toship['product_color']); ?></em></span>
                                    <span><strong>Size:</strong> <em><?php echo htmlspecialchars($toship['product_size']); ?></em></span>
                                    <span><strong>Qty:</strong> <em><?php echo htmlspecialchars($toship['quantity']); ?></em></span>
                                </div>
                                <div class="order-note">Note: <input type="text" value="<?php echo htmlspecialchars($toship['user_note']); ?>" readonly></div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- For alignment -->
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="highlight-price">Total Price: ₱<?php echo number_format($toship['total'], 2); ?></div>
                                <div class="product-actions">
                                    <p class="text-muted">The seller is preparing your order</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<p class="no-products">No products available</p>';
                    }
                    ?>
                </div>
            </div>
            <div class="tab-pane fade" id="to-receive">
                <div class="order-forms-container">
                    <?php
                    if (mysqli_num_rows($to_receive_orders) > 0) {
                        while ($toreceive = mysqli_fetch_assoc($to_receive_orders)) {
                    ?>
                    <div class="prod-container mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info">
                                    <span class="shop-name"><i class="fas fa-store"></i> <?php echo htmlspecialchars($toreceive['shop_name']); ?></span>
                                </div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="<?php echo 'uploads/' . htmlspecialchars(basename($toreceive['product_img'])); ?>" id="product-image" alt="product image" class="img-fluid">
                            </div>
                            <div class="col-md-10">
                                <div class="product-name"><?php echo htmlspecialchars($toreceive['product_name']); ?></div>
                                <div class="order-date">Ship Date: <?php echo date("F j, Y g:i A", strtotime($toreceive['order_date'])); ?></div>
                                <div class="product-variation">
                                    <span><strong>Color:</strong> <em><?php echo htmlspecialchars($toreceive['product_color']); ?></em></span>
                                    <span><strong>Size:</strong> <em><?php echo htmlspecialchars($toreceive['product_size']); ?></em></span>
                                    <span><strong>Qty:</strong> <em><?php echo htmlspecialchars($toreceive['quantity']); ?></em></span>
                                </div>
                                <div class="order-note">Note: <input type="text" value="<?php echo htmlspecialchars($toreceive['user_note']); ?>" readonly></div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- For alignment -->
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="highlight-price">Total Price: ₱<?php echo number_format($toreceive['total'], 2); ?></div>
                                <div class="product-actions">
                                    <button type="button" class="btn btn-success">
                                        <a href="received.php?order_id=<?php echo htmlspecialchars($toreceive['order_id']); ?>">Order Received</a>
                                    </button>
                                    <button type="button" class="btn btn-danger return-btn" data-bs-toggle="modal" data-bs-target="#returnModal" data-order-id="<?php echo htmlspecialchars($toreceive['order_id']); ?>">
                                        Return/Refund
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<p class="no-products">No products available</p>';
                    }
                    ?>
                </div>
            </div>
            <div class="tab-pane fade" id="to-rate">
                <div class="order-forms-container">
                    <?php
                    if (mysqli_num_rows($to_rate_orders) > 0) {
                        while ($torate = mysqli_fetch_assoc($to_rate_orders)) {
                    ?>
                    <div class="prod-container mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info">
                                    <span class="shop-name"><i class="fas fa-store"></i> <?php echo htmlspecialchars($torate['shop_name']); ?></span>
                                </div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="<?php echo 'uploads/' . htmlspecialchars(basename($torate['product_img'])); ?>" id="product-image" alt="product image" class="img-fluid">
                            </div>
                            <div class="col-md-10">
                                <div class="product-name"><?php echo htmlspecialchars($torate['product_name']); ?></div>
                                <div class="order-date">Received Date: <?php echo date("F j, Y g:i A", strtotime($torate['order_date'])); ?></div>
                                <div class="product-variation">
                                    <span><strong>Color:</strong> <em><?php echo htmlspecialchars($torate['product_color']); ?></em></span>
                                    <span><strong>Size:</strong> <em><?php echo htmlspecialchars($torate['product_size']); ?></em></span>
                                    <span><strong>Qty:</strong> <em><?php echo htmlspecialchars($torate['quantity']); ?></em></span>
                                </div>
                                <div class="order-note">Note: <input type="text" value="<?php echo htmlspecialchars($torate['user_note']); ?>" readonly></div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row">
                            <div class="col-md-6">
                                <!-- For alignment -->
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="highlight-price">Total Price: ₱<?php echo number_format($torate['total'], 2); ?></div>
                                <div class="product-actions">
                                    <div class="d-flex justify-content-end">
                                        
                                        <button type="button" class="btn btn-primary me-2 rate-btn" data-bs-toggle="modal" data-bs-target="#rateModal<?php echo $torate['order_id']; ?>">Rate</button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--=============== RATE MODAL ===============-->
                    <div class="modal fade" id="rateModal<?php echo $torate['order_id']; ?>" tabindex="-1" aria-labelledby="rateModalLabel<?php echo $torate['order_id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                        <form action="process_feedback.php" method="post" class="rateForm">
                            <input type="hidden" name="order_id" value="<?php echo $torate['order_id']; ?>">
                            <input type="hidden" name="product_id" value="<?php echo $torate['product_id']; ?>">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-center w-100" id="rateModalLabel<?php echo $torate['order_id']; ?>">Rate Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="product-summary mb-4">
                                        <img src="<?php echo 'uploads/'. basename($torate['product_img']); ?>" alt="Product Image" class="img-fluid rounded mb-3" style="max-height: 100px; object-fit: contain;">
                                        <h6 class="product-name"><?php echo $torate['product_name']; ?></h6>
                                        <p class="order-details">
                                            Order Date: <?php echo date("F j, Y g:i A", strtotime($torate['order_date'])); ?><br>
                                            Quantity: <?php echo $torate['quantity']; ?><br>
                                            Color: <?php echo $torate['product_color']; ?><br>
                                            Size: <?php echo $torate['product_size']; ?><br>
                                            Total Price: ₱<?php echo number_format($torate['total'], 2); ?>
                                        </p>
                                    </div>

                                        
                                        <!-- Product Quality Rating -->
                                        <div class="rating-group">
                                            <label for="quality_rating<?php echo $torate['order_id']; ?>" class="rating-label">Product Quality</label>
                                            <div class="star-rating-container">
                                                <div class="star-rating" id="quality_rating<?php echo $torate['order_id']; ?>">
                                                    <input type="radio" id="quality5" name="quality-rating" value="5" required /><label for="quality5"></label>
                                                    <input type="radio" id="quality4" name="quality-rating" value="4" required /><label for="quality4"></label>
                                                    <input type="radio" id="quality3" name="quality-rating" value="3" required /><label for="quality3"></label>
                                                    <input type="radio" id="quality2" name="quality-rating" value="2" required /><label for="quality2"></label>
                                                    <input type="radio" id="quality1" name="quality-rating" value="1" required /><label for="quality1"></label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Product Price Rating -->
                                        <div class="rating-group">
                                            <label for="price_rating<?php echo $torate['order_id']; ?>" class="rating-label">Product Price</label>
                                            <div class="star-rating-container">
                                                <div class="star-rating" id="price_rating<?php echo $torate['order_id']; ?>">
                                                    <input type="radio" id="price5" name="price-rating" value="5" required /><label for="price5"></label>
                                                    <input type="radio" id="price4" name="price-rating" value="4" required /><label for="price4"></label>
                                                    <input type="radio" id="price3" name="price-rating" value="3" required /><label for="price3"></label>
                                                    <input type="radio" id="price2" name="price-rating" value="2" required /><label for="price2"></label>
                                                    <input type="radio" id="price1" name="price-rating" value="1" required /><label for="price1"></label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Seller Service Rating -->
                                        <div class="rating-group">
                                            <label for="service_rating<?php echo $torate['order_id']; ?>" class="rating-label">Seller Service</label>
                                            <div class="star-rating-container">
                                                <div class="star-rating" id="service_rating<?php echo $torate['order_id']; ?>">
                                                    <input type="radio" id="service5" name="service-rating" value="5" required /><label for="service5"></label>
                                                    <input type="radio" id="service4" name="service-rating" value="4" required /><label for="service4"></label>
                                                    <input type="radio" id="service3" name="service-rating" value="3" required /><label for="service3"></label>
                                                    <input type="radio" id="service2" name="service-rating" value="2" required /><label for="service2"></label>
                                                    <input type="radio" id="service1" name="service-rating" value="1" required /><label for="service1"></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="comment" class="form-label">Comment</label>
                                            <textarea class="form-control" id="notes<?php echo $torate['order_id']; ?>" name="notes" required rows="4" placeholder="Share your thoughts about the product..."></textarea>
                                        </div>
                                    
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success" id="submit-btn" name="submit">Submit Review</button>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<p class="no-products">No products available</p>';
                    }
                    ?>
                </div>
            </div>
            <!--=============== CANCELLED ORDERS ===============-->
            <div class="tab-pane fade" id="cancelled">
                <div class="order-forms-container">
                <?php
                    if (mysqli_num_rows($cancelled) > 0) {
                        while ($cancel = mysqli_fetch_assoc($cancelled)) {
                    ?>
                    <div class="prod-container mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info">
                                    <span class="shop-name"><i class="fas fa-store"></i> <?php echo $cancel['shop_name']; ?></span>
                                </div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                            <img src="<?php echo 'uploads/'. basename($cancel['product_img']); ?>" id="product-image" alt="product image" class="img-fluid">
                            </div>
                            <div class="col-md-10">
                                <div class="product-name"><?php echo $cancel['product_name']; ?></div>
                                <div class="order-date">Cancel Date: Date <?php echo date("F j, Y g:i A", strtotime ($cancel['cancel_date'])); ?></div>
                                <div class="product-variation">
                                    <span><strong>Color:</strong> <em>Color <?php echo htmlspecialchars($cancel['product_color']); ?></em></span>
                                    <span><strong>Size:</strong> <em>Size <?php echo htmlspecialchars($cancel['product_size']); ?></em></span>
                                    <span><strong>Qty:</strong> <em>Quantity: <?php echo htmlspecialchars($cancel['quantity']); ?></em></span>
                                </div>
                                <div class="order-note">Note: <input type="text" value="<?php echo htmlspecialchars(string: $cancel['user_note']); ?>" readonly></div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row">
                            <div class="col-md-6">
                                <!--=============== FOR ALIGNMENT PURPOSES ===============-->
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="highlight-price">Total Price: ₱<?php echo number_format($cancel['total'], 2); ?></div>
                                <div class="product-actions">
                                    <a href="buynow.php?product_id=<?php echo $cancel['product_id']; ?>&color=<?php echo urlencode($cancel['product_color']); ?>&size=<?php echo urlencode($cancel['product_size']); ?>&quantity=<?php echo $cancel['quantity']; ?>">
                                        <button type="button" class="btn btn-primary buy-again-btn">Buy Again</button>
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<p class="no-products">No products available</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="tab-pane fade" id="return">
                <div class="order-forms-container">
                <?php
                    if (mysqli_num_rows($returned) > 0) {
                        while ($return = mysqli_fetch_assoc($returned)) {
                    ?>
                    <div class="prod-container mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info">
                                    <span class="shop-name"><i class="fas fa-store"></i> <?php echo $return['shop_name']; ?></span>
                                </div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                            <img src="<?php echo 'uploads/'. basename($return['product_img']); ?>" id="product-image" alt="product image" class="img-fluid">
                            </div>
                            <div class="col-md-10">
                                <div class="product-name"><?php echo $return['product_name']; ?></div>
                                <div class="order-date">Returned Date: Date <?php echo date("F j, Y g:i A", strtotime ($return['returned_date'])); ?></div>
                                <div class="product-variation">
                                    <span><strong>Color:</strong> <em>Color <?php echo htmlspecialchars($return['product_color']); ?></em></span>
                                    <span><strong>Size:</strong> <em>Size <?php echo htmlspecialchars($return['product_size']); ?></em></span>
                                    <span><strong>Qty:</strong> <em>Quantity: <?php echo htmlspecialchars($return['quantity']); ?></em></span>
                                </div>
                                <div class="order-note">Reason: <input type="text" value="<?php echo htmlspecialchars($return['reason_for_return']); ?>" readonly></div>
                                <div class="return-details">Additional Details: <input type="text" readonly></div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row">
                            <div class="col-md-6">
                                <!--=============== FOR ALIGNMENT PURPOSES ===============-->
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="highlight-price">Total Price: ₱<?php echo number_format($return['total'], 2); ?></div>
                                <div class="product-actions">
                                    <a href="buynow.php?product_id=<?php echo $return['product_id']; ?>&color=<?php echo urlencode($return['product_color']); ?>&size=<?php echo urlencode($return['product_size']); ?>&quantity=<?php echo $return['quantity']; ?>">
                                        <button type="button" class="btn btn-primary buy-again-btn">Buy Again</button>
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<p class="no-products">No products available</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="tab-pane fade" id="completed"> 
            <div class="order-forms-container">
                <?php
                    if (mysqli_num_rows($completed) > 0) {
                        while ($complete = mysqli_fetch_assoc($completed)) {
                    ?>
                    <div class="prod-container mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="customer-info">
                                    <span class="shop-name"><i class="fas fa-store"></i> <?php echo $complete['shop_name']; ?></span>
                                </div>
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                            <img src="<?php echo 'uploads/'. basename($complete['product_img']); ?>" id="product-image" alt="product image" class="img-fluid">
                            </div>
                            <div class="col-md-10">
                                <div class="product-name"><?php echo $complete['product_name']; ?></div>
                                <div class="order-date">Complete Date: Date <?php echo date("F j, Y g:i A", strtotime ($complete['received_date'])); ?></div>
                                <div class="product-variation">
                                    <span><strong>Color:</strong> <em>Color <?php echo htmlspecialchars($complete['product_color']); ?></em></span >
                                    <span><strong>Size:</strong> <em>Size <?php echo htmlspecialchars($complete['product_size']); ?></em></span>
                                    <span><strong>Qty:</strong> <em>Quantity: <?php echo htmlspecialchars($complete['quantity']); ?></em></span>
                                </div>
                               
                            </div>
                        </div>
                        <hr class="divider">
                        <div class="row">
                            <div class="col-md-6">
                                <!--=============== FOR ALIGNMENT PURPOSES ===============-->
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="highlight-price">Total Price: ₱<?php echo number_format($complete['total'], 2); ?></div>
                                <div class="product-actions">
                                    <a href="buynow.php?product_id=<?php echo $complete['product_id']; ?>&color=<?php echo urlencode($complete['product_color']); ?>&size=<?php echo urlencode($complete['product_size']); ?>&quantity=<?php echo $complete['quantity']; ?>">
                                        <button type="button" class="btn btn-primary buy-again-btn">Buy Again</button>
                                    </a>                                
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo '<p class="no-products">No products available</p>';
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>
     <!--=============== END OF TAB CONTENT CONTAINER ===============-->
     <?php include 'footer.php'; ?>    

     <script>
function updateSpanCounts() {
    // Updated to include 'to-rate' tab
    const tabs = ['to-pay', 'to-ship', 'to-receive', 'completed', 'cancelled', 'return', 'to-rate'];
    
    tabs.forEach(tab => {
        const container = document.querySelector(`#${tab} .order-forms-container`);
        const orderForms = container ? container.querySelectorAll('.prod-container').length : 0;
        const badge = document.querySelector(`a[href="#${tab}"] .badge`);

        if (orderForms > 0) {
            badge.textContent = orderForms;
            badge.style.display = 'inline-flex'; // Ensure the badge is visible if count > 0
        } else {
            badge.style.display = 'none'; // Hide the badge if count is 0
        }
    });
}

function handleCancelButton(event) {
    const actionDiv = event.target.closest('.product-actions');
    actionDiv.innerHTML = `
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success me-2 confirm-btn">Confirm</button>
            <button type="button" class="btn btn-secondary close-btn">Close</button>
        </div>
    `;
    
    const confirmBtn = actionDiv.querySelector('.confirm-btn');
    const closeBtn = actionDiv.querySelector('.close-btn');
    
    confirmBtn.addEventListener('click', handleConfirmButton);
    closeBtn.addEventListener('click', handleCloseButton);
}

function handleConfirmButton(event) {
    const orderForm = event.target.closest('.prod-container');
    orderForm.remove();
    updateSpanCounts();
}

function handleCloseButton(event) {
    const actionDiv = event.target.closest('.product-actions');
    actionDiv.innerHTML = `
        <button type="button" class="btn btn-danger cancel-btn">Cancel</button>
    `;
    
    const cancelBtn = actionDiv.querySelector('.cancel-btn');
    cancelBtn.addEventListener('click', handleCancelButton);
}

document.addEventListener('DOMContentLoaded', () => {
    updateSpanCounts();
    
    const cancelButtons = document.querySelectorAll('.cancel-btn');
    cancelButtons.forEach(button => {
        button.addEventListener('click', handleCancelButton);
    });
});

</script>

<script>
function containsProfanity(text) {
    const profanityList = ['fuck', 'shit', 'ass', 'bitch', 'damn','dumb', 'hayop','tangna',
     'tangina', 'bobo', 'ulol', 'tarantado', 'tanga','gago', 'pakshet','deym','btch']; 
    const lowercaseText = text.toLowerCase();
    return profanityList.some(word => lowercaseText.includes(word));
}

function showWarning(message) {
    const warningElement = document.createElement('div');
    warningElement.className = 'alert alert-danger mt-2';
    warningElement.textContent = message;
    return warningElement;
}

document.addEventListener('DOMContentLoaded', function() {
    const rateForms = document.querySelectorAll('.rateForm');

    rateForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const notesTextarea = form.querySelector('textarea[name="notes"]');
            const notes = notesTextarea.value.trim();
            let warningContainer = form.querySelector('#warning-message-container');

            if (!warningContainer) {
                warningContainer = document.createElement('div');
                warningContainer.id = 'warning-message-container';
                form.querySelector('.modal-body').appendChild(warningContainer);
            }

            warningContainer.innerHTML = '';

            if (containsProfanity(notes)) {
                warningContainer.appendChild(showWarning('Your review contains inappropriate language. Please revise your comment.'));
                e.preventDefault(); 
            }
        });
    });
});
</script>


<script src="cart.js"></script>

<!-- Add this modal structure at the end of your file, before the closing body tag -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="return.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="returnModalLabel">Return/Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="returnOrderId" name="order_id">
                    <div class="mb-3">
                        <label for="returnReason" class="form-label">Reason for Return/Refund</label>
                        <select class="form-select" id="returnReason" name="reason" required>
                            <option value="">Select a reason</option>
                            <option value="Damaged item">Damaged item</option>
                            <option value="Wrong item received">Wrong item received</option>
                            <option value="Item not as described">Item not as described</option>
                            <option value="Size or fit issue">Size or fit issue</option>
                            <option value="Quality not as expected">Quality not as expected</option>
                            <option value="Arrived too late">Arrived too late</option>
                            <option value="Defective product">Defective product</option>
                            <option value="Change of mind">Change of mind</option>
                            <option value="Found better price elsewhere">Found better price elsewhere</option>
                            <option value="Accidental order">Accidental order</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="returnDetails" class="form-label">Additional Details</label>
                        <textarea class="form-control" id="returnDetails" name="details" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Add this script at the end of your file, before the closing body tag -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var returnModal = document.getElementById('returnModal');
        returnModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var orderId = button.getAttribute('data-order-id');
            document.getElementById('returnOrderId').value = orderId;
        });

        document.getElementById('submitReturn').addEventListener('click', function() {
            var form = document.getElementById('returnForm');
            if (form.checkValidity()) {
                // For now, we'll just close the modal and show an alert
                var modal = bootstrap.Modal.getInstance(returnModal);
                modal.hide();
                alert('Return request submitted successfully!');
            } else {
                form.reportValidity();
            }
        });
    });

</script>
</body>
</html>
