<?php  
    include 'dbconnect.php';
    include 'cart_count.php';
    include 'nav.php'; 
    $user_id = $_SESSION['userID'];
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $carts = "SELECT
                c.*, 
                p.product_img, 
                p.product_name,
                m.shop_name,
                m.application_id
            FROM 
                cart c
            JOIN 
                merchant_products p ON c.product_id = p.product_id
            JOIN
                merchant_applications m ON p.application_id = m.application_id
            WHERE 
                c.user_id = '$user_id' AND c.status = 'cart'";

     $result = $connection->query($carts);

    if (!$result) {
        die("Query failed: " . $connection->error);
    }

    // Group items by shop and product
    $grouped_items = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $shop_id = $row['application_id'];
        $product_id = $row['product_id'];
        $key = $shop_id . '_' . $product_id . '_' . $row['product_size'] . '_' . $row['product_color'];
        
        if (!isset($grouped_items[$key])) {
            $grouped_items[$key] = $row;
        } else {
            $grouped_items[$key]['quantity'] += $row['quantity'];
            $grouped_items[$key]['cart_id'] .= ',' . $row['cart_id'];
        }
    }

    // Group items by shop
    $shop_products = [];
    foreach ($grouped_items as $item) {
        $shop_name = $item['shop_name'];
        if (!isset($shop_products[$shop_name])) {
            $shop_products[$shop_name] = [];
        }
        $shop_products[$shop_name][] = $item;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>

<!--=============== CART ===============-->
<div class="cart-container">
    <div class="cart-header">
        <h2><i class="fas fa-shopping-cart"></i> My Cart</h2>
        <div class="cart-actions">
            <div class="select-all-container">
                <input type="checkbox" id="select-all" class="select-all-checkbox">
                <label for="select-all" class="select-all-label">Select All</label>
            </div>
            <div class="action-divider"></div>
            <span id="delete-all" class="delete-all-text">Delete All</span>
        </div>
    </div>

    <form method="post" action="cartcheckout.php">
        <?php 
        foreach ($shop_products as $shop_name => $products) {
            echo '<div class="shop-container">';
            echo '<h3 class="shop-name"><i class="fas fa-store"></i> ' . htmlspecialchars($shop_name) . '</h3>';

            foreach ($products as $row) {
        ?>
            <div class="cart-item">
                <div class="cart-item-content">
                    <div class="item-image">
                        <img src="<?php echo 'uploads/' . basename($row['product_img']); ?>" alt="<?php echo $row['product_name']; ?>">
                    </div>
                    <div class="item-details">
                        <h4 class="item-name"><?php echo $row['product_name']; ?></h4>
                        <p class="item-size">Size: <?php echo $row['product_size']; ?></p>
                        <p class="item-color">Color: <?php echo $row['product_color']; ?></p>
                        <p class="unit-price">₱<?php echo number_format($row['price'], 2); ?></p>
                    </div>
                    <div class="item-actions">
                        <div class="quantity-controls">
                            <button type="button" class="btn btn-quantity minus-btn" data-unit-price="<?php echo $row['price']; ?>">-</button>
                            <span class="quantity"><?php echo $row['quantity']; ?></span>
                            <button type="button" class="btn btn-quantity add-btn" data-unit-price="<?php echo $row['price']; ?>">+</button>
                        </div>
                        <button type="button" class="btn btn-remove remove-product" data-product-id="<?php echo $row['cart_id']; ?>">Remove</button>
                    </div>
                </div>
                <div class="item-divider"></div>
                <div class="item-total">
                    <p class="total-price-label">Total:</p>
                    <p class="total-price">₱<?php echo number_format($row['price'] * $row['quantity'], 2); ?></p>
                </div>
                <input type="checkbox" class="item-checkbox product-checkbox" id="option-check-<?php echo $row['cart_id']; ?>" name="cart_items[]" value="<?php echo $row['cart_id']; ?>">
            </div>
        <?php 
            }
            echo '</div>'; // Close shop container
        }
        ?>

        <div class="cart-footer">
            <button type="submit" class="btn btn-checkout" id="buybtn" name="btnall">Proceed to Checkout</button>
        </div>
    </form>
</div>
<!--=============== END OF CART ===============-->

<?php include 'footer.php'; ?>

<!--=============== JAVASCRIPT ===============-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/cart.js"></script>
</body>
</html>
