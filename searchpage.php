<?php

include 'dbconnect.php';
include 'getProductDetails.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    $user_id = $_SESSION['userID'];
}
if (!isset($user_id)) {
        header('Location: index.php');
        exit();
    }

$query = "SELECT * FROM merchant_products WHERE 1=1";

// Check if a search query is present in the URL (GET request)
if (isset($_GET['searchbar']) && !empty($_GET['searchbar'])) {
    $searchQuery = mysqli_real_escape_string($connection, $_GET['searchbar']);
    
    // Modify the query to search both product_name and product_desc
    $query .= " AND (product_name LIKE '%$searchQuery%' OR product_desc LIKE '%$searchQuery%')";
}

// Execute the query
$result = mysqli_query($connection, $query);

// Check if the query failed
if (!$result) {
    die("Query Failed: " . mysqli_error($connection));
}

// Display the search input field with the existing keyword
$existingSearchQuery = isset($_GET['searchbar']) ? htmlspecialchars($_GET['searchbar']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CraftHub: An Online Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!--=============== REMIXICONS ==============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!--=============== BOXICONS ==============-->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link href="css/searchpage.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
</head>
<body>
    <?php include 'nav.php'; ?>
    
    <!--=============== BACKGROUND WALLPAPER ==============-->
    <img src="images/searchbg.jpg" alt="Background Image" class="background-image">

    <div class="main-container">
        <div class="main-content">
            <!--=============== SEARCH RESULTS ===============-->
            <div class="search-results-container">
                <div class="search-results">
                    <div class="search-info">
                        <p id="search-results-text">Search Results for: </p>
                        <p id="selected-category-text"></p>
                    </div>
                    <div class="search-stats">
                        <span id="product-count"></span> products found
                    </div>
                </div>
                <!--=============== CATEGORY DROPDOWN ===============-->
                <div class="category-dropdown">
                    <button class="category-menu-btn" id="categoryMenuBtn">
                        <i class="fas fa-bars"></i> Categories
                    </button>
                    <div class="category-menu" id="categoryMenu">
                        <button class="category-btn" data-category="All Products">All Products</button>
                        <button class="category-btn" data-category="Accessories">Accessories</button>
                        <button class="category-btn" data-category="Coasters">Coasters</button>
                        <button class="category-btn" data-category="Dining">Dining</button>
                        <button class="category-btn" data-category="Decors">Decors</button>
                        <button class="category-btn" data-category="Furniture">Furniture</button>
                        <button class="category-btn" data-category="Lighting">Lighting</button>
                        <button class="category-btn" data-category="Kitchen">Kitchen</button>
                        <button class="category-btn" data-category="Rugs">Rugs</button>
                        <button class="category-btn" data-category="Tables">Tables</button>
                        <button class="category-btn" data-category="Storage">Storage</button>
                        <button class="category-btn" data-category="Trays">Trays</button>
                        <button class="category-btn" data-category="Other Products">Other Products</button>
                    </div>
                </div>
            </div>
        
            <!--=============== PRODUCT CARDS ===============-->
            <div class="scrollable-content">
                <div id="product-container">
                    <?php
                    while ($row = mysqli_fetch_assoc($result)) {
                        $product_id = $row['product_id'];
                        
                        // Fetch price range, rating, and sold count
                        list($min_price, $max_price, $average_rating, $total_ratings, $sold_count) = getProductDetails($connection, $product_id);

                        $imagePath = 'uploads/' . basename($row['product_img']);
                    ?>
                        <div class="product-card">
                            <div class="product-image">
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="Product Image">
                            </div>
                            <div class="product-details">
                                <h5 class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></h5>
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
                                <a href="buynow.php?product_id=<?php echo $product_id; ?>">
                                    <button class="buybtn" name="buy-now">View Product</button>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!--=============== JAVASCRIPT FOR DROPDOWN AND SEARCH ==============-->
    <script>
    $(document).ready(function() {
        // Function to handle category filtering
        function fetchProducts(category) {
            // Get the current search query from the search bar
            var searchQuery = '<?php echo isset($_GET['searchbar']) ? htmlspecialchars($_GET['searchbar']) : ''; ?>';

            $.ajax({
                type: "POST",
                url: "fetch_products.php",
                data: {
                    category: category,
                    searchQuery: searchQuery // Pass the search query along with the category
                },
                success: function(response) {
                    if (response.trim() === '') {
                        // No products found
                        $("#product-container").html(''); // Clear the product container
                        $("#search-results-text").text("No results for: ");
                        $("#selected-category-text").text(category);
                        $("#product-count").text("0");
                    } else {
                        // Products found
                        $("#product-container").html(response);
                        $("#search-results-text").text("Search Results for: ");
                        $("#selected-category-text").text(category);
                        
                        // Count the number of product cards
                        var productCount = $(".product-card").length;
                        $("#product-count").text(productCount);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching products:', error);
                }
            });
        }

        // When a category button is clicked
        $(".category-btn, .dropdown-item").click(function() {
            var selectedCategory = $(this).data("category") || $(this).text();
            fetchProducts(selectedCategory);
        });

        // Initial load of all products
        fetchProducts("All Products");
    });
    </script>
    <script src="cart.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const categoryMenuBtn = document.getElementById('categoryMenuBtn');
        const categoryMenu = document.getElementById('categoryMenu');

        categoryMenuBtn.addEventListener('click', function(event) {
            categoryMenu.classList.toggle('show');
            event.stopPropagation(); // Prevent the click from immediately closing the menu
        });

        // Close the menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!categoryMenuBtn.contains(event.target) && !categoryMenu.contains(event.target)) {
                categoryMenu.classList.remove('show');
            }
        });

        // Add click event listeners to category buttons
        const categoryBtns = document.querySelectorAll('.category-btn');
        categoryBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                categoryMenu.classList.remove('show');
                fetchProducts(this.dataset.category);
            });
        });
    });
    </script>

</body>
</html>
