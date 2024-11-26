<?php 
session_start();
include 'dbconnect.php';

$user_id = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

$product_sizes = [];
$product_color = [];
$application_id = null; 
$merchant_profile = 'images/user.png'; 
$product_count = 0;

if (isset($_GET['product_id'])) {
    $product_id = mysqli_real_escape_string($connection, $_GET['product_id']);

    // Query to get product details, sizes, colors, and merchant details
    $select = mysqli_query($connection, "
        SELECT 
            mp.*, 
            ps.sizes, 
            ps.price AS size_price, 
            pc.color, 
            cu.user_profile AS merchant_profile, 
            cma.application_id, 
            cma.shop_name
        FROM `merchant_products` mp
        LEFT JOIN `product_sizes` ps ON mp.product_id = ps.product_id AND mp.application_id = ps.application_id
        LEFT JOIN `product_color` pc ON mp.product_id = pc.product_id AND mp.application_id = pc.application_id
        LEFT JOIN `merchant_applications` cma ON mp.application_id = cma.application_id
        LEFT JOIN `crafthub_users` cu ON cma.user_id = cu.user_id
        WHERE mp.product_id = '$product_id'
    ");

    if (!$select) {
        die('Query failed: ' . mysqli_error($connection));
    }

    if (mysqli_num_rows($select) > 0) {
        $seen_sizes = []; // Track seen sizes
        $seen_colors = []; // Track seen colors
        while ($row = mysqli_fetch_assoc($select)) {
            $fetch = $row;
            $application_id = $row['application_id']; // Get the application_id
            $merchant_profile = !empty($row['merchant_profile']) ? $row['merchant_profile'] : 'images/user.png';
            $shop_name = $row['shop_name'];
            $price = isset($_POST['price']) ? (float) $_POST['price'] : 0.0;

            if (!empty($row['sizes']) && !in_array($row['sizes'], $seen_sizes)) {
                $product_sizes[] = ['size' => $row['sizes'], 'price' => $row['size_price']];
                $seen_sizes[] = $row['sizes']; // Mark this size as seen
            }
            if (!empty($row['color']) && !in_array($row['color'], $seen_colors)) {
                $product_color[] = $row['color'];
                $seen_colors[] = $row['color']; // Mark this color as seen
            }
        }
    }

    if (!empty($product_sizes)) {
        // Extract the price values from the product sizes
        $prices = array_column($product_sizes, 'price');

        // Calculate the minimum and maximum price
        $min_price = min($prices);
        $max_price = max($prices);
    }

    // Count the number of products the merchant has
    $count_query = mysqli_query($connection, "
        SELECT COUNT(*) AS product_count 
        FROM merchant_products 
        WHERE application_id = '$application_id'
    ");
    
    if ($count_query && mysqli_num_rows($count_query) > 0) {
        $count_row = mysqli_fetch_assoc($count_query);
        $product_count = $count_row['product_count'];
    }
}


// Query to get the average product rating (quality and price) for a specific product
$rating_query = mysqli_query($connection, "
    SELECT 
        AVG((quality_rating + price_rating) / 2) AS average_rating, 
        COUNT(*) AS total_ratings 
    FROM 
        product_feedback 
    WHERE 
        product_id = '$product_id'
");

$average_rating = 0;
$total_ratings = 0;

if ($rating_query && mysqli_num_rows($rating_query) > 0) {
    $rating_row = mysqli_fetch_assoc($rating_query);
    $average_rating = round($rating_row['average_rating'], 1); // Round to 1 decimal place
    $total_ratings = $rating_row['total_ratings'];
}

// Query to get the average service rating for the shop associated with the product
$service_rating_query = mysqli_query($connection, "
    SELECT 
        AVG(service_rating) AS average_service_rating, 
        COUNT(*) AS total_service_ratings
    FROM 
        product_feedback pf
    INNER JOIN 
        merchant_products mp ON pf.product_id = mp.product_id
    INNER JOIN 
        merchant_applications ma ON mp.application_id = ma.application_id
    WHERE 
        ma.application_id = '$application_id'
");

$average_service_rating = 0;
$total_service_ratings = 0;

if ($service_rating_query && mysqli_num_rows($service_rating_query) > 0) {
    $service_rating_row = mysqli_fetch_assoc($service_rating_query);
    $average_service_rating = round($service_rating_row['average_service_rating'], 1); // Round to 1 decimal place
    $total_service_ratings = $service_rating_row['total_service_ratings'];
}

// Query to count the number of orders with status='order received' for the current product
$order_count_query = mysqli_query($connection, "
    SELECT 
        COUNT(*) AS sold_count 
    FROM 
        orders 
    WHERE 
        product_id = '$product_id' 
        AND status = 'already rated'
");

$sold_count = 0;

if ($order_count_query && mysqli_num_rows($order_count_query) > 0) {
    $order_count_row = mysqli_fetch_assoc($order_count_query);
    $sold_count = $order_count_row['sold_count'];
}

// Fetch user details
$query = "SELECT * FROM crafthub_users WHERE user_id = '$user_id'";
$result = mysqli_query($connection, $query);

if ($result) {
    $row11 = mysqli_fetch_assoc($result);
    $role = $row11['role'];

    if ($role === 'merchant') {
        // Redirect to merchant chatroom
        $chatroom_url = "chatroom.php?chat_with_id={$application_id}";
    } elseif ($role === 'customer') {
        // Redirect to customer chatroom
        $chatroom_url = "chatroom_customer.php?chat_with_id={$application_id}";
    } else {
        // Optional: Handle other roles or invalid cases
        echo 'Invalid role';
    }
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
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--=============== BOXICONS ===============-->
    <link href="https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css" rel="stylesheet">
    <!--=============== FONT AWESOME ===============-->
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/buynow.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<?php include 'nav.php'; ?>
<body class="buynow-page">

    <!--=============== PRODUCT CONTAINER ===============-->
    
    <form action="checkout.php" method="post" id="checkoutForm">
        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($fetch['product_id']); ?>">
        <input type="hidden" name="application_id" value="<?php echo htmlspecialchars($fetch['application_id']); ?>">
        <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($fetch['product_name']); ?>">
        <input type="hidden" name="product_desc" value="<?php echo htmlspecialchars($fetch['product_desc']); ?>">
        <input type="hidden" name="product_image" value="<?php echo htmlspecialchars('uploads/' . basename($fetch['product_img'])); ?>">
        <input type="hidden" name="price" id="price" value="<?php echo htmlspecialchars($fetch['price']); ?>">
        <input type="hidden" name="shop_name" value="<?php echo htmlspecialchars($fetch['shop_name']); ?>">
        <div class="buynow-container">
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="buynow-product-image-container">
                        <img src="<?php echo 'uploads/'. basename($fetch['product_img']); ?>" alt="Product Image" class="buynow-product-image">
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="buynow-product-name"> <!--=============== PRODUCT NAME ===============-->
                        <h3><?php echo $fetch['product_name']; ?></h3>
                        <div class="row">
                            <div class="col-md-9">
                                <span class="star-rating"> 
                                    <?php 
                                    $filled_stars = floor($average_rating);
                                    $half_star = $average_rating - $filled_stars >= 0.5;
                                    $empty_stars = 5 - $filled_stars - ($half_star ? 1 : 0);

                                    // Filled stars
                                    for ($i = 0; $i < $filled_stars; $i++) {
                                        echo '<i class="ri-star-fill"></i>';
                                    }

                                    // Half star
                                    if ($half_star) {
                                        echo '<i class="ri-star-half-fill"></i>';
                                    }

                                    // Empty stars
                                    for ($i = 0; $i < $empty_stars; $i++) {
                                        echo '<i class="ri-star-line"></i>';
                                    }
                                    ?>
                                    <span class="rating-number"><?php echo $average_rating; ?> </span>
                                    <span class="vertical-divider"></span>
                                    <span class="rating-count"><?php echo $total_ratings; ?> Ratings</span>
                                    <span class="vertical-divider"></span>
                                    <span class="sold-count"><?php echo $sold_count; ?> sold</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="buynow-price">
                        <label for="product-price"></label>
                        <p id="product-price">
                            <?php if (!empty($product_sizes)): ?>
                                <?php echo number_format($min_price, 2); ?> - <?php echo number_format($max_price, 2); ?>
                            <?php else: ?>
                                <?php echo number_format((float)str_replace(',', '', $fetch['price']), 2); ?> <!-- Format and sanitize the price -->
                            <?php endif; ?>
                        </p>
                    </div>


                    <?php if (!empty($product_sizes)): ?>
                        <!--=============== SIZES ===============-->
                        <div class="size-options">
                            <label for="size">Sizes:</label>
                            <select id="size" name="size" class="custom-select" onchange="updatePrice()">
                                
                                <?php foreach ($product_sizes as $size): ?>
                                    <option value="<?php echo htmlspecialchars($size['size']); ?>" data-price="<?php echo $size['price']; ?>">
                                        <?php echo htmlspecialchars($size['size']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($product_color)): ?>
                        <!--=============== COLORS ===============-->
                        <div class="color-options">
                            <label for="color">Colors:</label>
                            <select id="color" name="color" class="custom-select">
                                
                                <?php foreach ($product_color as $color): ?>
                                    <option value="<?php echo htmlspecialchars($color); ?>">
                                        <?php echo htmlspecialchars($color); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                        <!--=============== QUANTITY ===============-->
                        <div class="row" id="beforebtn">
                        <div class="col-md-6 product-quantity">
                            <div class="d-flex align-items-center">
                                <label for="quantity">Quantity:</label> 
                                <div class="input-group qty-input"> 
                                    <button type="button" class="quantity-btn qty-count" onclick="decrementQuantity()">-</button>
                                    <input type="text" id="quantity" name="quantity" class="product-qty" value="1">
                                    <button type="button" class="quantity-btn qty-count" onclick="incrementQuantity()">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--=============== BUY NOW & ADD TO CART ===============-->
                    <div class="row" id="btn">
                        <div class="product-button">
                            <div class="button-group">
                                <button type="submit" id="buyNowLink" class="buy-btn">Buy Now</button>
                                <button type="button" id="addToCartBtn" class="add-btn">Add to cart</button>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </form>  
    <!--=============== END PRODUCT CONTAINER ===============-->

   <!--=============== SHOP PROFILE CONTAINER ===============-->
            <div class="buynow-container-shop">
                <div class="row"> 
                    <div class="col-3 col-md-1 shop-profile"> 
                        <img src="<?php echo 'uploads/'. basename($fetch['merchant_profile']); ?>" id="shop-profile" alt="shop profile">  
                    </div> 
                    <div class="col-9 col-md-4 name"> 
                        <div class="shop-name mb-1">
                            <span><?php echo $fetch['shop_name']; ?></span>
                        </div> 
                        <div class="button-container d-flex align-items-center mt-2">
                            <a href="<?php echo $chatroom_url; ?>"><button type="submit" class="chat-btn">Chat Now</button></a>
                            <button type="button" class="view-btn" onclick="viewShop(<?php echo $fetch['application_id']; ?>)">View Shop</button>

                        </div>
                    </div> 
                    <div class="col-12 col-md-5 shop-rate">
                        <div class="seller-rate">
                            <p>Ratings: 
                            <span class="star-rating"> 
                                <?php 
                                $filled_stars = floor($average_service_rating);
                                $half_star = $average_service_rating - $filled_stars >= 0.5;
                                $empty_stars = 5 - $filled_stars - ($half_star ? 1 : 0);

                                // Filled stars
                                for ($i = 0; $i < $filled_stars; $i++) {
                                    echo '<i class="ri-star-fill"></i>';
                                }

                                // Half star
                                if ($half_star) {
                                    echo '<i class="ri-star-half-fill"></i>';
                                }

                                // Empty stars
                                for ($i = 0; $i < $empty_stars; $i++) {
                                    echo '<i class="ri-star-line"></i>';
                                }
                                ?>
                                <span class="rating-number"><?php echo $average_service_rating; ?></span>
                            </span></p>
                            <p>Product Count: <span id="products-count"><?php echo $product_count; ?></span></p>
                        </div>
                    </div>
                </div> 
            </div>

            <!--=============== END SHOP PROFILE CONTAINER ===============-->

        <div class="buynow-container-description">
            <div class="row"> 
                <div class="col-md-12 product-info"> 
                    <div class="product-specs">
                        <div class="specs">Product Specification</div>
                        <div class="specs-info">
                            <p>Category: <?php echo $fetch['category']; ?></p>
                            <p>Material: <?php echo $fetch['material']; ?></p>
                            <p>Stock: <?php echo $fetch['stock']; ?></p>
                        </div>
                    </div>
                    <div class="product-description mt-4"> 
                        <div class="description">Product Description</div> 
                        <p><?php echo $fetch['product_desc']; ?></p>
                    </div> 
                </div> 
            </div> 
        </div> 
        


    <!--=============== PRODUCT FEEDBACKS ===============-->
    <div class="buynow-container-feedback"> 
        <div class="row"> 
            <div class="col-md-12"> 
                <div class="product-feedback"> 
                    <div class="feedback">Feedbacks</div> 
                </div> 
            </div> 
        </div> 

        <?php
        // Fetch feedback and user profiles
        $select_feedback = mysqli_query($connection, "
            SELECT f.*, cu.username, cu.user_profile 
            FROM product_feedback f 
            INNER JOIN crafthub_users cu ON f.user_id = cu.user_id 
            WHERE f.product_id = '$product_id'
            ORDER BY f.feedback_date DESC
        ") or die('Query failed: ' . mysqli_error($connection));

        $feedback_count = mysqli_num_rows($select_feedback);
        $i = 0;
        while ($feedback = mysqli_fetch_assoc($select_feedback)) {
            // Determine the profile image to use
            $profile_image = !empty($feedback['user_profile']) ? 'uploads/' . $feedback['user_profile'] : 'images/user.png';
            $hidden_class = $i >= 3 ? 'hidden-feedback' : '';
            ?>

        <div class="feedback-item <?php echo $hidden_class; ?>">
            <div class="row feedback-row"> 
                <div class="col-2 col-md-1 user-profile-container"> 
                    <img src="<?php echo htmlspecialchars($profile_image); ?>" id="user-profile" alt="User Profile Image">
                </div> 
                <div class="col-10 col-md-11 user-info"> 
                    <div class="customer-name">
                        <?php echo htmlspecialchars($feedback['username']); ?>
                    </div> 
                    <div class="order-date-and-ratings">
                        <span class="order-date">
                            <?php echo htmlspecialchars($feedback['feedback_date']); ?>
                        </span>
                        <span class="ratings-divider"></span>
                        <span class="ratings">
                            Quality: <span class="star-rating">
                                <?php echo str_repeat('<i class="ri-star-fill"></i>', $feedback['quality_rating']); ?>
                                <?php echo str_repeat('<i class="ri-star-line"></i>', 5 - $feedback['quality_rating']); ?>
                            </span>
                            Service: <span class="star-rating">
                                <?php echo str_repeat('<i class="ri-star-fill"></i>', $feedback['service_rating']); ?>
                                <?php echo str_repeat('<i class="ri-star-line"></i>', 5 - $feedback['service_rating']); ?>
                            </span>
                            Price: <span class="star-rating">
                                <?php echo str_repeat('<i class="ri-star-fill"></i>', $feedback['price_rating']); ?>
                                <?php echo str_repeat('<i class="ri-star-line"></i>', 5 - $feedback['price_rating']); ?>
                            </span>
                        </span>
                    </div>
                    <div class="note-container">
                        <span class="comment-label">Comment:</span>
                        <div class="note" style="margin-right: 70px; max-width: 90%; width: 100%;">
                            <?php echo htmlspecialchars($feedback['feedback_notes']); ?>
                        </div>  
                    </div>
                </div> 
            </div>
            <hr class="feedback-divider">
        </div>
        <?php
            $i++;
        }
        if ($feedback_count > 3) {
            echo '<div id="view-more-container" class="text-center mt-3">';
            echo '<button id="view-more-btn" class="btn btn-link">View more</button>';
            echo '</div>';
        }
        ?> 
    </div> 

    <?php include 'footer.php'; ?>
    <!--=============== END PRODUCT FEEDBACKSS ===============-->

<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="js/homepage.js"></script>

<style>
    #view-more-btn {
        background: none;
        border: none;
        color: #3F704D;
        cursor: pointer;
        font-weight: bold;
        font-size: 1.1em;
        padding: 5px 0;
        margin: 10px 0;
        display: inline-block;
        transition: color 0.3s ease;
    }

    #view-more-btn:hover {
        color: #2C4F36;
        text-decoration: underline;
    }
</style>

<script>
    function updatePrice() {
        const sizeSelect = document.getElementById('size');
        const priceDisplay = document.getElementById('product-price');
        const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
        const price = selectedOption.getAttribute('data-price');

        if (price) {
            priceDisplay.textContent = parseFloat(price).toFixed(2);
        } else {
            // If no size is selected, you may want to reset the price to the original range
            const minPrice = '<?php echo number_format($min_price, 2); ?>';
            const maxPrice = '<?php echo number_format($max_price, 2); ?>';
            priceDisplay.textContent = `${minPrice} - ${maxPrice}`;
        }
                
        document.getElementById('price').value = price ? price.replace(/,/g, '') : '';
    }


    function incrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        quantityInput.value = parseInt(quantityInput.value) + 1;
    }

    function decrementQuantity() {
        const quantityInput = document.getElementById('quantity');
        if (quantityInput.value > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        updatePrice();
    });

    document.getElementById('addToCartBtn').addEventListener('click', function() {
        const form = document.getElementById('checkoutForm');
        form.action = 'add_to_cart.php';
        form.method = 'post';
        form.submit();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const viewMoreBtn = document.getElementById('view-more-btn');
        const hiddenFeedbacks = document.querySelectorAll('.hidden-feedback');
        let isExpanded = false;

        if (viewMoreBtn) {
            viewMoreBtn.addEventListener('click', function() {
                if (!isExpanded) {
                    hiddenFeedbacks.forEach(feedback => {
                        feedback.style.display = 'block';
                    });
                    viewMoreBtn.textContent = 'View less';
                    isExpanded = true;
                } else {
                    hiddenFeedbacks.forEach(feedback => {
                        feedback.style.display = 'none';
                    });
                    viewMoreBtn.textContent = 'View more';
                    isExpanded = false;
                }
            });
        }

        // Initially hide feedbacks beyond the first three
        hiddenFeedbacks.forEach(feedback => {
            feedback.style.display = 'none';
        });
    }); 
    function viewShop(application_id) {
            fetch('set_merchant_session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `application_id=${application_id}`
            })
            .then(response => response.text())
            .then(data => {
                window.location.href = 'viewshop.php';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

</script>
<script src="cart.js"></script>
</body>
</html>

        