<?php
    include 'dbconnect.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        $user_id = $_SESSION['userID'];
    }
    if (!isset($user_id)) {
        header('Location: index.php');
        exit();
    }

    $product_id = $_POST['product_id'] ?? null;
    $application_id = $_POST['application_id'] ?? null;
    $product_name = $_POST['product_name'] ?? null;
    $product_desc = $_POST['product_desc'] ?? null;
    $product_image = $_POST['product_image'] ?? null;
    $size = $_POST['size'] ?? null;
    $color = $_POST['color'] ?? null;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $total_price = $price * $quantity;
    $shop_name = $_POST['shop_name'] ?? 'Unknown Shop'; // get the shop name

    $select_info = "SELECT * FROM `crafthub_users` WHERE `user_id` = '$user_id'";

    $result = mysqli_query($connection, $select_info);

    if (!$result) {
        die("Query Failed: " . mysqli_error($connection));
    } else {
        $row = mysqli_fetch_assoc($result);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <!--=============== CHECKOUT ===============-->
    <div class="container">
        <div class="container-inner">
            <div class="row">
                <header class="header">
                    <h1><i class="fas fa-shopping-cart"></i> Checkout</h1>
                </header>
            </div>
            <hr class="solid">

            <!--=============== DELIVERY INFO ===============-->
            <div class="row delivery-info">
                <div class="delivery-header">
                    <div>
                        <h2><i class="fas fa-map-marker-alt"></i> Delivery Address</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="delivery-details">
                        <p>Full Name: <?php echo $row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']; ?></p>
                        <p>Address: <?php echo $row['address']; ?></p>
                        <span>Phone Number: <?php echo $row['contact_no']; ?></span>
                    </div>
                </div>
            </div>
            <hr class="dashed">

            <!--=============== ORDERED PRODUCTS ===============-->
            <div class="product-ordered-section">
                <div class="row">
                    <div class="col-md-12">
                        <h2>Product Ordered</h2>
                    </div>
                </div>
                <form action="place_order.php" method="POST">
                    <div class="shop-container">
                        <div class="shop-name-container">
                            <h3 class="shop-name"><?php echo htmlspecialchars($shop_name); ?></h3>
                        </div>
                        <div class="product-row" id="product-row-<?php echo $product_id; ?>">
                            <div class="product-image-container">
                                <img src="<?php echo htmlspecialchars('uploads/' . basename($product_image)); ?>" alt="Product Image" class="product-image">
                            </div>
                            <div class="product-info-container">
                                <h4 class="product-name"><?php echo htmlspecialchars($product_name); ?></h4>
                                <div class="product-details">
                                    <div class="detail-row">
                                        <span class="detail-label">Price:</span>
                                        <span class="detail-value">₱<span id="price-<?php echo $product_id; ?>"><?php echo number_format($price, 2); ?></span></span>
                                    </div>
                                    <?php if ($color): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Color:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($color); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($size): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Size:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($size); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="detail-row quantity-row">
                                        <span class="detail-label quantity-label">Quantity:</span>
                                        <div class="quantity-container">
                                            <button type="button" class="btn-quantity" onclick="decrementQuantity(<?php echo $product_id; ?>)">-</button>
                                            <span id="quantity-<?php echo $product_id; ?>"><?php echo htmlspecialchars($quantity); ?></span>
                                            <button type="button" class="btn-quantity" onclick="incrementQuantity(<?php echo $product_id; ?>)">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product-subtotal-container">
                                <span class="detail-label">Subtotal:</span>
                                <span class="detail-value subtotal">₱<span id="item-subtotal-<?php echo $product_id; ?>"><?php echo number_format($total_price, 2); ?></span></span>
                            </div>
                            <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product_image); ?>">
                            <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>">
                            <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
                            <input type="hidden" name="size" value="<?php echo htmlspecialchars($size); ?>">
                            <input type="hidden" name="color" value="<?php echo htmlspecialchars($color); ?>">
                            <input type="hidden" name="quantity" id="quantity-input-<?php echo $product_id; ?>" value="<?php echo htmlspecialchars($quantity); ?>">
                            <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
                        </div>
                        <!--=============== MESSAGE SELLER ===============-->
                        <div class="message-seller">
                            <label for="seller-message">Message for Seller:</label>
                            <input type="text" class="form-control" id="seller-message" name="msg" placeholder="Enter your message here">
                        </div>
                    </div>
                    <!--=============== PAYMENT METHOD ===============-->
                    <div class="payment-method-section">
                        <h3>Payment Method</h3>
                        <div class="payment-options">
                            <div class="payment-option">
                                <input type="radio" id="cash" name="payment_method" value="cash" checked>
                                <label for="cash">
                                    <i class="fas fa-money-bill-wave"></i>
                                    Cash on Delivery
                                </label>
                            </div>
                            
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                    <input type="hidden" name="merchant_id" value="<?php echo htmlspecialchars($merchant_id); ?>">
                    <div class="card-button">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-left">
                                <h3>Total Amount: <span class="total-amount-highlight">₱<span id="total-amount"><?php echo number_format($total_price, 2); ?></span></span></h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-success" name="place_order">Place Order</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>

    <script>
        function incrementQuantity(productId) {
            var quantityElement = document.getElementById('quantity-' + productId);
            var quantityInput = document.getElementById('quantity-input-' + productId);
            var currentQuantity = parseInt(quantityElement.textContent);
            var newQuantity = currentQuantity + 1;
            quantityElement.textContent = newQuantity;
            quantityInput.value = newQuantity;
            updateTotalAmount(productId);
        }

        function decrementQuantity(productId) {
            var quantityElement = document.getElementById('quantity-' + productId);
            var quantityInput = document.getElementById('quantity-input-' + productId);
            var currentQuantity = parseInt(quantityElement.textContent);
            if (currentQuantity > 1) {
                var newQuantity = currentQuantity - 1;
                quantityElement.textContent = newQuantity;
                quantityInput.value = newQuantity;
                updateTotalAmount(productId);
            }
        }

        function updateTotalAmount(productId) {
            var price = parseFloat(document.getElementById('price-' + productId).textContent);
            var quantity = parseInt(document.getElementById('quantity-' + productId).textContent);
            var total = price * quantity;
            document.getElementById('item-subtotal-' + productId).textContent = total.toFixed(2);
            document.getElementById('total-amount').textContent = total.toFixed(2);
        }
    </script>
    <script src="cart.js"></script>
</body>
</html>
