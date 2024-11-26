<?php
    include 'dbconnect.php';
    include 'getProductDetails.php';
    
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['userID'] ?? null;
    if (!$user_id) {
        header('Location: index.php');
        exit();
    }

    $application_id = $_SESSION['application_id'] ?? null;
    if (!$application_id) {
        echo 'Merchant not found';
        exit();  
    }

    $query = "
        SELECT cu.user_id, cu.user_profile, ma.shop_name 
        FROM crafthub_users cu
        JOIN merchant_applications ma ON cu.user_id = ma.user_id 
        WHERE ma.application_id = '$application_id'
    ";
    $result = mysqli_query($connection, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $current_profile_image = $row['user_profile'] ?? 'images/user.png';
        $shop_name = $row['shop_name'] ?? "Unknown Shop";
    } else {
        $current_profile_image = 'images/user.png';
        $shop_name = "Unknown Shop";
    }

    // Fetch all products for this merchant
    $product_query = "SELECT * FROM merchant_products WHERE application_id = '$application_id'";
    $product = mysqli_query($connection, $product_query);
    $product_count = mysqli_num_rows($product);

    // Fetch service rating for this merchant's products
    $service_rating_query = mysqli_query($connection, "
        SELECT ROUND(AVG(service_rating), 1) AS average_service_rating, 
               COUNT(*) AS total_service_ratings
        FROM product_feedback pf
        INNER JOIN merchant_products mp ON pf.product_id = mp.product_id
        WHERE mp.application_id = '$application_id'
    ");
    $rating_row = mysqli_fetch_assoc($service_rating_query);
    $average_service_rating = $rating_row['average_service_rating'] ?? 0;
    $total_service_ratings = $rating_row['total_service_ratings'] ?? 0;

    // Fetch unique categories for this merchant
    $category_query = "
        SELECT DISTINCT category 
        FROM merchant_products 
        WHERE application_id = '$application_id'
    ";
    $category_result = mysqli_query($connection, $category_query);

    // Fetch merchant address details
    $address_query = "
        SELECT ma.shop_street, ma.shop_barangay, ma.shop_municipality, ma.shop_name
        FROM merchant_applications AS ma
        WHERE ma.application_id = '$application_id'
    ";
    
    // Define the getCoordinates function here if it's not already included
    function getCoordinates($address) {
        $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, '2DMapApp/1.0 (your-email@example.com)');
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        if (isset($data[0])) {
            return [
                'lat' => $data[0]['lat'],
                'lon' => $data[0]['lon']
            ];
        } else {
            return null;
        }
    }

    $result5 = mysqli_query($connection, $address_query);
    if ($result5 && mysqli_num_rows($result5) > 0) {
        $row = mysqli_fetch_assoc($result5);
        $address = $row['shop_street'] . ', ' . $row['shop_barangay'] . ', ' . $row['shop_municipality'] . ', Bataan, Philippines';
        $shop_name = $row['shop_name'];

        // Fetch coordinates using the getCoordinates function
        $coordinates = getCoordinates($address);
        if ($coordinates) {
            $lat = $coordinates['lat'];
            $lon = $coordinates['lon'];
        }
    } else {
        echo 'Address not found';
        exit();
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
    <link rel="stylesheet" href="css/viewshop.css">
    <link rel="stylesheet" href="css/footer.css">
</head>
<body>
    <?php include 'nav.php'; ?>

        <div class="main-container">
            <!--=============== SHOP INFO ==============-->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 profile">
                        <div class="header-photo">
                            <img src="images/mheader2.png" alt="Shop Header">
                        </div>
                        <div class="profile-photo">
                        <img src="<?php echo 'uploads/' . $current_profile_image; ?>" id="merchant-image" alt="merchant-image">
                        </div>
                        <div class="shop-details">
                            <h2 class="shop-name"><?php echo $shop_name; ?></h2>
                            <span class="active-status">Active Now</span>
                            <div class="shop-buttons">
                                <button class="chat-btn" onclick="window.location.href='chatroom.php'"><i class="fas fa-comment-alt"></i> Chat</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--=============== SHOP PRODUCTS ==============-->
                <div class="row">
                    <div class="col-md-12 shop-info">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <label><i class="fas fa-box"></i> Products:</label>
                                    <span class="info-value mt-3"><?php echo $product_count; ?> </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <label><i class="fas fa-star"></i> Ratings:</label>
                                    <span class="star-rating">
                                    <?php 
                                    $filled_stars = floor($average_service_rating);
                                    $half_star = $average_service_rating - $filled_stars >= 0.5;
                                    $empty_stars = 5 - $filled_stars - ($half_star ? 1 : 0);

                                    for ($i = 0; $i < $filled_stars; $i++) echo '<i class="ri-star-fill"></i>';
                                    if ($half_star) echo '<i class="ri-star-half-fill"></i>';
                                    for ($i = 0; $i < $empty_stars; $i++) echo '<i class="ri-star-line"></i>';
                                    ?>
                                        <span class="rating-number"><?php echo $average_service_rating; ?> (<?php echo $total_service_ratings; ?> ratings)</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--=============== END OF SHOP INFO ==============-->

            <!--=============== TABS AND PRODUCT CONTAINER ==============-->
            <div class="tabs-and-products">
                <div class="shop-content">
                    <div class="sort-container">
                        <div class="sort-buttons">
                            <span class="sort-label">Sort by:</span>
                            <button class="sort-btn" data-sort="top-sales">Top Sales</button>
                            <button class="sort-btn" data-sort="latest">Latest</button>
                            <button class="sort-btn" data-sort="price-low-high">Price: Low to High</button>
                            <button class="sort-btn" data-sort="price-high-low">Price: High to Low</button>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination">
                                <li class="page-item"><a class="page-link" href="#" data-page="prev">Previous</a></li>
                                <li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>
                                <li class="page-item"><a class="page-link" href="#" data-page="2">2</a></li>
                                <li class="page-item"><a class="page-link" href="#" data-page="3">3</a></li>
                                <li class="page-item"><a class="page-link" href="#" data-page="next">Next</a></li>
                            </ul>
                        </nav>
                    </div>
                    <hr class="tabs-divider">
                    <!--=============== PRODUCT SECTION ==============-->
                    <div class="product-section">
                        <div class="categories-sidebar">
                            <h3>Categories</h3>
                            <ul>
                                <li><a href="#" class="category-link active" data-category="all">All Products</a></li>
                                <?php while ($category_row = mysqli_fetch_assoc($category_result)): ?>
                                    <li><a href="#" class="category-link" data-category="<?php echo htmlspecialchars($category_row['category']); ?>">
                                        <?php echo htmlspecialchars(ucfirst($category_row['category'])); ?>
                                    </a></li>
                                <?php endwhile; ?>
                            </ul>
                        </div>

                        
                        <div class="product-container" id="product-container">
                        <?php if ($product_count > 0): ?>
                            <?php  
                            while ($row = mysqli_fetch_assoc($product)): 
                                $product_id = $row['product_id'];                 
                                list($min_price, $max_price, $average_rating, $total_ratings, $sold_count) = getProductDetails($connection, $product_id);
                                $display_price = $min_price; // We'll use the minimum price for sorting
                                $top_sales_score = $total_ratings + $sold_count; // Calculate top sales score
                            ?>
                            <div class="product-card" data-price="<?php echo $display_price; ?>" data-top-sales="<?php echo $top_sales_score; ?>" data-category="<?php echo htmlspecialchars($row['category']); ?>">
                                <div class="product-image">
                                <img src="<?php echo 'uploads/' . $row['product_img']; ?>" alt="<?php echo $row['product_name']; ?>" class="product-image">
                                </div>
                                <div class="product-details">
                                    <div class="product-info">
                                        <h5 class="product-name"><?php echo $row['product_name']; ?></h5>
                                        <div class="product-meta">
                                        <p class="price">
                                            ₱<?php echo number_format($min_price, 2); ?>
                                            <?php if ($min_price != $max_price): ?>
                                                - ₱<?php echo number_format($max_price, 2); ?>
                                            <?php endif; ?>   
                                        </p>
                                            <span class="star-rating">
                                                <i class="ri-star-fill"></i>
                                                <span class="rating-number"> <?php echo $average_rating; ?> (<?php echo $total_ratings; ?> ratings)</span>
                                                <span class="rating-number"><strong><?php echo $sold_count; ?></strong> Sold</span>
                                            </span>
                                        </div>
                                    </div>
                                        <a href="buynow.php?product_id=<?php echo $row['product_id']; ?>">
                                            <button class="buybtn" name="buy-now">View Product</button>
                                        </a>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            <?php else: ?>
                                <p>No products found.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!--=============== END OF TABS AND PRODUCT CONTAINER ==============-->

            <!--=============== MAP CONTAINER ==============-->
            <div class="map-section">
                <div class="map-container">
                    <label><i class="fas fa-map-marker-alt"></i> Store Map:</label>
                    <div id="map" class="shop-map"></div>
                </div>
            </div>

            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
            <script>
                // Initialize the map at the merchant's coordinates
                const map = L.map('map').setView([<?php echo $lat; ?>, <?php echo $lon; ?>], 15);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                // Add a marker for the shop
                L.marker([<?php echo $lat; ?>, <?php echo $lon; ?>]).addTo(map)
                    .bindPopup(`<b><?php echo $shop_name; ?></b><br><?php echo $address; ?>`)
                    .openPopup();
            </script>
            <!--=============== END OF MAP CONTAINER ==============-->
        </div>
        <?php include 'footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortButtons = document.querySelectorAll('.sort-btn');
    const productContainer = document.getElementById('product-container');
    const categoryLinks = document.querySelectorAll('.category-link');
    const originalOrder = Array.from(productContainer.children);
    let currentSortType = null;
    let currentCategory = 'all';
    
    sortButtons.forEach(button => {
        button.addEventListener('click', function() {
            const sortType = this.dataset.sort;
            
            if (currentSortType === sortType) {
                this.classList.remove('active');
                currentSortType = null;
                applyFiltersAndSort();
            } else {
                sortButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                currentSortType = sortType;
                applyFiltersAndSort();
            }
        });
    });

    categoryLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            categoryLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.category;
            applyFiltersAndSort();
        });
    });

    function applyFiltersAndSort() {
        let filteredProducts = originalOrder;

        // Apply category filter
        if (currentCategory !== 'all') {
            filteredProducts = filteredProducts.filter(product => 
                product.dataset.category === currentCategory
            );
        }

        // Apply sorting
        if (currentSortType) {
            filteredProducts.sort((a, b) => {
                switch(currentSortType) {
                    case 'price-low-high':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price-high-low':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'top-sales':
                        return parseInt(b.dataset.topSales) - parseInt(a.dataset.topSales);
                    case 'latest':
                        // Assuming you've added a data-creation-date attribute
                        return new Date(b.dataset.creationDate) - new Date(a.dataset.creationDate);
                    default:
                        return 0;
                }
            });
        }

        reorderProducts(filteredProducts);
    }

    function reorderProducts(products) {
        productContainer.innerHTML = '';
        if (products.length === 0) {
            productContainer.innerHTML = '<p>No products found in this category.</p>';
        } else {
            products.forEach(product => {
                productContainer.appendChild(product);
            });
        }
    }

    // Initialize with all products shown
    applyFiltersAndSort();
});

function initMap() {
    // Replace with the actual coordinates of the shop
    const shopLocation = { lat: 14.5995, lng: 120.9842 };
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: shopLocation,
    });
    const marker = new google.maps.Marker({
        position: shopLocation,
        map: map,
    });
}
</script>
</body>
</html>