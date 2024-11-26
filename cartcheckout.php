<?php
    include 'dbconnect.php';
  
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_POST['cart_items'])) {
        // Redirect to cart if no items were selected
        header('Location: cart.php');
        exit;
    }
    

    $cart_items = $_POST['cart_items']; // Array of selected cart item IDs
    $user_id = $_SESSION['userID'];

    // Fetch the details of the selected cart items
    $items = [];
    foreach ($cart_items as $cart_id) {
        $cart_ids = explode(',', $cart_id); // Split multiple cart IDs
        $cart_id_list = implode( array_map('intval', $cart_ids)); // Safely create a comma-separated list
        
        $query = "SELECT c.*, p.product_img, p.product_name, m.shop_name, m.application_id 
                FROM cart c 
                JOIN merchant_products p ON c.product_id = p.product_id 
                JOIN merchant_applications m ON p.application_id = m.application_id
                WHERE c.cart_id IN ($cart_id_list) AND c.user_id = '$user_id' AND c.status = 'cart'";
        $result = mysqli_query($connection, $query);
        
        if (!$result) {
            // Log the error
            die("Query failed: " . mysqli_error($connection));
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $key = $row['application_id'] . '_' . $row['product_id'] . '_' . $row['product_size'] . '_' . $row['product_color'];
            if (!isset($items[$key])) {
                $items[$key] = $row;
            } else {
                $items[$key]['quantity'] += $row['quantity'];
            }
        }
    }

    // Group items by shop
    $grouped_items = [];
    foreach ($items as $item) {
        $shop_name = $item['shop_name'];
        if (!isset($grouped_items[$shop_name])) {
            $grouped_items[$shop_name] = [];
        }
        $grouped_items[$shop_name][] = $item;
    }

    $selectuser = "SELECT * FROM crafthub_users WHERE user_id = '$user_id'";
    $result1 = mysqli_query($connection, $selectuser);
        
    if (!$result1) {
        // Log the error
        die("Query failed: " . mysqli_error($connection));
    }

    if (mysqli_num_rows($result1) > 0) {
        $row = mysqli_fetch_assoc($result1);
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
    <!--=============== REMIXICONS ==============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--=============== BOXICONS ==============-->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="css/footer.css">

</head>
<body>
        <?php include 'nav.php'; ?>

    <!--=============== CHECKOUT CONTENT PAGE ===============-->
    <div class="container">
        <div class="container-inner">
            <div class="row">
                <header class="header">
                    <h1><i class="fas fa-shopping-cart"></i> Checkout</h1>
                </header>
            </div>
            <hr class="solid">

            <!--=============== DELIVERY DETAILS ===============-->
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
            <!--=============== END OF DELIVERY DETAILS ===============-->

            <!--=============== PRODUCT ORDERED ===============-->
            <div class="product-ordered-section">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        $total_products = array_sum(array_map('count', $grouped_items));
                        if ($total_products > 1) {
                            echo "<h2>Products Ordered</h2>";
                        } else {
                            echo "<h2>Product Ordered</h2>";
                        }
                        ?>
                    </div>
                </div>
                <form action="process_order.php" method="POST">
                    <?php foreach ($grouped_items as $shop_name => $shop_items) { ?>
                    <div class="shop-container"> <!--=============== SHOP CONTAINER ===============-->
                        <div class="shop-name-container">
                            <h3 class="shop-name"><?php echo htmlspecialchars($shop_name); ?></h3>
                            <?php 
                            $item_count = count($shop_items);
                            $shop_total = 0;
                            foreach ($shop_items as $item) {
                                $shop_total += $item['price'] * $item['quantity'];
                            }
                            // Only display total items and total if there's more than one item
                            if ($item_count > 1) {
                                echo "<span class='shop-total'>Total items: $item_count <span class='shop-total-separator'>|</span> Total: ₱" . number_format($shop_total,2) . "</span>";
                            }
                            ?>
                        </div>
                        <?php foreach ($shop_items as $item) { ?>
                        <div class="product-row" id="product-row-<?php echo $item['cart_id']; ?>">
                            <div class="product-image-container">
                                <img src="<?php echo htmlspecialchars('uploads/' . basename($item['product_img'])); ?>" alt="Product Image" class="product-image">
                            </div>
                            <div class="product-info-container"> <!--=============== PRODUCT CONTAINER ===============-->
                                <h4 class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <div class="product-details">
                                    <div class="detail-row">
                                        <span class="detail-label">Price:</span>
                                        <span class="detail-value">₱<span id="price-<?php echo $item['cart_id']; ?>"><?php echo number_format($item['price'], 2); ?></span></span>
                                    </div>
                                    <?php if ($item['product_color']): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Color:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($item['product_color']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ($item['product_size']): ?>
                                    <div class="detail-row">
                                        <span class="detail-label">Size:</span>
                                        <span class="detail-value"><?php echo htmlspecialchars($item['product_size']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <div class="detail-row quantity-row">
                                        <span class="detail-label quantity-label">Quantity:</span>
                                        <div class="quantity-container">
                                            <button type="button" class="btn-quantity" onclick="decrementQuantity(<?php echo $item['cart_id']; ?>)">-</button>
                                            <span id="quantity-<?php echo $item['cart_id']; ?>"><?php echo htmlspecialchars($item['quantity']); ?></span>
                                            <button type="button" class="btn-quantity" onclick="incrementQuantity(<?php echo $item['cart_id']; ?>)">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--=============== REMOVE PRODUCT ===============-->
                            <div class="remove-product-container">
                                <button type="button" class="btn-remove-product" onclick="removeProduct(<?php echo $item['cart_id']; ?>)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <!--=============== PRODUCT SUBTOTAL ===============-->
                            <div class="product-subtotal-container">
                                <span class="detail-label">Subtotal:</span>
                                <span class="detail-value subtotal">₱<span id="item-subtotal-<?php echo $item['cart_id']; ?>"><?php echo ($item['price'] * $item['quantity'] ); ?></span></span>
                            </div>
                            <input type="hidden" name="product_images[]" value="<?php echo htmlspecialchars($item['product_img']); ?>">
                            <input type="hidden" name="product_names[]" value="<?php echo htmlspecialchars($item['product_name']); ?>">
                            <input type="hidden" name="prices[]" value="<?php echo htmlspecialchars($item['price']); ?>">
                            <input type="hidden" name="sizes[]" value="<?php echo htmlspecialchars($item['product_size']); ?>">
                            <input type="hidden" name="colors[]" value="<?php echo htmlspecialchars($item['product_color']); ?>">
                            <input type="hidden" name="quantities[]" id="quantity-input-<?php echo $item['cart_id']; ?>" value="<?php echo htmlspecialchars($item['quantity']); ?>">
                            <input type="hidden" name="subtotals[]" value="<?php echo htmlspecialchars($item['price'] * $item['quantity']); ?>">
                            <input type="hidden" name="cart_items[]" value="<?php echo $item['cart_id']; ?>">
                            <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($application_id); ?>">       
                        </div>
                        
                        <!--=============== MESSAGE SELLER ===============-->
                        <div class="message-seller">
                            <label for="seller-message-<?php echo $item['cart_id']; ?>">Message for Seller:</label>
                            <input type="text" class="form-control" id="seller-message-<?php echo $item['cart_id']; ?>" name="msg[<?php echo $item['cart_id']; ?>]" placeholder="Enter your message here">
                        </div>
                    </div>
                    <?php } ?>
                    <?php } ?>

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
                    <!--=============== END OF PAYMENT METHOD ===============-->
                                
                    <!--=============== PLACE ORDER ===============-->
                    <div class="card-button">
                        <div class="row align-items-center">
                            <div class="col-md-6 text-left">
                                <h3>Total Amount: <span class="total-amount-highlight">₱<span id="total-amount">0.00</span></span></h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="submit" class="btn btn-success" name="place_order">Place Order</button>
                            </div>
                        </div>
                    </div>
                    <!--=============== END OF PLACE ORDER ===============-->
                </form>
            </div>
        </div>
    </div>


<!--=============== JAVASCRIPT FOR INCREMENT AND DECREMENT ===============-->
<script>
    function incrementQuantity(cartId) {
        var quantityElement = document.getElementById('quantity-' + cartId);
        var quantityInput = document.getElementById('quantity-input-' + cartId);
        var currentQuantity = parseInt(quantityElement.textContent);
        var newQuantity = currentQuantity + 1;
        quantityElement.textContent = newQuantity;
        quantityInput.value = newQuantity;
        updateTotalAmount(cartId);
    }

    function decrementQuantity(cartId) {
        var quantityElement = document.getElementById('quantity-' + cartId);
        var quantityInput = document.getElementById('quantity-input-' + cartId);
        var currentQuantity = parseInt(quantityElement.textContent);
        if (currentQuantity > 1) {
            var newQuantity = currentQuantity - 1;
            quantityElement.textContent = newQuantity;
            quantityInput.value = newQuantity;
            updateTotalAmount(cartId);
        }
    }

    function updateTotalAmount(cartId) {
        var price = parseFloat(document.getElementById('price-' + cartId).textContent);
        var quantity = parseInt(document.getElementById('quantity-' + cartId).textContent);
        var total = price * quantity;
        document.getElementById('item-subtotal-' + cartId).textContent = total.toFixed(2);
        
        // Update the overall total
        updateOverallTotal();
    }

    function updateOverallTotal() {
        var subtotals = document.querySelectorAll('[id^="item-subtotal-"]');
        var total = 0;
        subtotals.forEach(function(subtotal) {
            total += parseFloat(subtotal.textContent);
        });
        document.getElementById('total-amount').textContent = total.toFixed(2);
    }

    // Call updateOverallTotal on page load
    window.onload = updateOverallTotal;

    function removeProduct(cartId) {
        if (confirm('Are you sure you want to remove this product?')) {
            var productRow = document.getElementById('product-row-' + cartId);
            if (productRow) {
                var shopContainer = productRow.closest('.shop-container');
                productRow.remove();
                updateOverallTotal();

                // Update total items for the shop
                var remainingProducts = shopContainer.querySelectorAll('.product-row');
                var shopNameContainer = shopContainer.querySelector('.shop-name-container');
                var shopTotalSpan = shopNameContainer.querySelector('.shop-total');

                if (remainingProducts.length > 1) {
                    var shopTotal = 0;
                    remainingProducts.forEach(function(product) {
                        var subtotal = parseFloat(product.querySelector('[id^="item-subtotal-"]').textContent);
                        shopTotal += subtotal;
                    });
                    
                    if (shopTotalSpan) {
                        shopTotalSpan.innerHTML = 'Total items: ' + remainingProducts.length + 
                            ' <span class="shop-total-separator">|</span> Total: ₱' + shopTotal.toFixed(2);
                    } else {
                        var newShopTotalSpan = document.createElement('span');
                        newShopTotalSpan.className = 'shop-total';
                        newShopTotalSpan.innerHTML = 'Total items: ' + remainingProducts.length + 
                            ' <span class="shop-total-separator">|</span> Total: ₱' + shopTotal.toFixed(2);
                        shopNameContainer.appendChild(newShopTotalSpan);
                    }
                } else if (remainingProducts.length === 1 && shopTotalSpan) {
                    shopTotalSpan.remove();
                }

                // Check if the shop container is empty
                if (remainingProducts.length === 0) {
                    shopContainer.remove();
                }

                // Check if there are any shops left
                var remainingShops = document.querySelectorAll('.shop-container');
                if (remainingShops.length === 0) {
                    // If no shops left, redirect to cart page
                    window.location.href = 'cart.php';
                }
            }
        }
    }

    function removeShop(shopName) {
        if (confirm('Are you sure you want to remove all products from ' + shopName + '?')) {
            var shopContainer = document.querySelector('.shop-container:has(.shop-name:contains("' + shopName + '"))');
            if (shopContainer) {
                shopContainer.remove();
                updateOverallTotal();
                
                // Check if there are any shops left
                var remainingShops = document.querySelectorAll('.shop-container');
                if (remainingShops.length === 0) {
                    // If no shops left, redirect to cart page
                    window.location.href = 'cart.php';
                }
            }
        }
    }
</script>

<script src="cart.js"></script>
</body>
</html>
