<?php 

    include 'dbconnect.php';
    include 'cart_count.php';
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
       
    }
    $user_id = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;
    if (!isset($user_id)) {
        header('Location: index.php');
        exit();
    }
    function getProductDetails($connection, $product_id) {
        // Fetch the price range
        $price_range_query = mysqli_query($connection, "
            SELECT MIN(price) as min_price, MAX(price) as max_price 
            FROM product_sizes 
            WHERE product_id = '$product_id'
        ");
    
        $min_price = 0;
        $max_price = 0;
        if ($price_range_query && mysqli_num_rows($price_range_query) > 0) {
            $price_range_row = mysqli_fetch_assoc($price_range_query);
            $min_price = $price_range_row['min_price'];
            $max_price = $price_range_row['max_price'];
        }
    
        // Fetch average rating
        $rating_query = mysqli_query($connection, "
            SELECT AVG((quality_rating + price_rating) / 2) as average_rating, COUNT(*) as total_ratings 
            FROM product_feedback 
            WHERE product_id = '$product_id'
        ");
    
        $average_rating = 0;
        $total_ratings = 0;
        if ($rating_query && mysqli_num_rows($rating_query) > 0) {
            $rating_row = mysqli_fetch_assoc($rating_query);
            $average_rating = round($rating_row['average_rating'], 1);
            $total_ratings = $rating_row['total_ratings'];
        }
    
        // Fetch sold count
        $order_count_query = mysqli_query($connection, "
            SELECT COUNT(*) as sold_count 
            FROM orders 
            WHERE product_id = '$product_id' 
              AND status = 'order received'
        ");
    
        $sold_count = 0;
        if ($order_count_query && mysqli_num_rows($order_count_query) > 0) {
            $order_count_row = mysqli_fetch_assoc($order_count_query);
            $sold_count = $order_count_row['sold_count'];
        }
    
        return [$min_price, $max_price, $average_rating, $total_ratings, $sold_count];
    }
   ?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--=============== BOXICONS ===============-->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/dd5559ee21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/homepage.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
        <?php include 'nav.php'; ?>

    <!--=============== HOMEPAGE HEADER ==============-->
    <section class="header"> 
        <div class="header_container">
            <div id="headerCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2000">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="images/slide1.jpeg" alt="Header Image 1">
                    </div>
                    <div class="carousel-item">
                        <img src="images/slide2.jpeg" alt="Header Image 2">
                    </div>
                    <div class="carousel-item">
                        <img src="images/slide3.jpeg" alt="Header Image 3">
                    </div>
                    <div class="carousel-item">
                        <img src="images/slide4.jpeg" alt="Header Image 4">
                    </div>
                </div>
            </div>
            <div class="header-content">
                <h1 class="header-title">CraftHub</h1>
                <h5 class="header-description">Where Creativity Meets Community</h5>
                <p class="subtitle">Connecting Creators and Collectors</p>
                <button id="shopNowBtn" class="shop-now-btn">Shop Now</button>
            </div>
        </div>
    </section>
    <!--=============== END OF HOMEPAGE HEADER ==============-->

    <!--=============== PRODUCT CONTAINER ===============-->
    <div class="container-fluid px-3 px-sm-0"> 
    <?php
    // Fetch all categories
    $selectCategories = "SELECT DISTINCT category FROM merchant_products ORDER BY category ASC";
    $categories = $connection->query($selectCategories);

    if ($categories->num_rows > 0) {
        while ($category = $categories->fetch_assoc()) {
            $categoryName = $category['category'];
            echo "<div class='category-header my-4' style='margin-bottom: 30px;'><h1>" . $categoryName . "</h1></div>";

            // Fetch products for this category
            $selectProducts = "SELECT * FROM merchant_products WHERE category = '$categoryName'";
            $products = $connection->query($selectProducts);

            if ($products->num_rows > 0) {
                echo "<div class='row'>"; // Start a row for product cards
                while ($product = $products->fetch_assoc()) {
                    $product_id = $product['product_id'];

                    // Fetch price range, rating, and sold count in a separate function
                    list($min_price, $max_price, $average_rating, $total_ratings, $sold_count) = getProductDetails($connection, $product_id);
                    ?>

                    <!-- Dynamic Product Card -->
                    <div class="col-md-3 col-6">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo 'uploads/' . $product['product_img']; ?>" alt="Product Image" class="img-fluid">
                            </div>
                            <div class="product-details">
                                <div class="product-info">
                                    <h5 class="product-name"><?php echo $product['product_name']; ?></h5>
                                    <div class="product-meta">
                                        <span class="product-price">
                                            ₱<?php echo number_format($min_price, 2); ?>
                                            <?php if ($min_price != $max_price): ?>
                                                - ₱<?php echo number_format($max_price, 2); ?>
                                            <?php endif; ?>
                                        </span>
                                        <div class="product-stats">
                                            <span class="product-rating"><i class="fas fa-star"></i> <?php echo $average_rating; ?></span>
                                            <span class="product-sold"><strong><?php echo $sold_count; ?></strong> Sold</span>
                                        </div>
                                    </div>
                                </div>
                                <a href="buynow.php?product_id=<?php echo $product['product_id']; ?>">
                                    <button class="buybtn" name="buy-now">View Product</button>
                                </a>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                echo "</div>"; 
            } else {
                echo "<p>No products available in this category.</p>";
            }
        }
    } else {
        echo "<p>No categories found.</p>";
    }
    ?>
</div>

    <!--=============== END PRODUCT CONTAINER ===============-->
    <?php include 'footer.php'; ?>

<!--=============== SCRIPTS ===============-->
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="js/homepage.js"></script>

</body>
</html>
