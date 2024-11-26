

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

    <?php
    // Get the current page filename
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>

<div class="shop-container">
        <!--=============== SIDEBAR ===============-->
        <div class="shop-sidebar">
            <a href="merdashboard.php" class="menu-item <?php echo ($current_page == 'merdashboard.php') ? 'active' : ''; ?>" data-section="dashboard">
                <i class="ri-dashboard-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="merprofile.php" class="menu-item <?php echo ($current_page == 'merprofile.php') ? 'active' : ''; ?>" data-section="profile">
                <i class="ri-user-line"></i>
                <span>Profile</span>
            </a>
            <a href="merproducts.php" class="menu-item <?php echo ($current_page == 'merproducts.php') ? 'active' : ''; ?>" data-section="products">
                <i class="ri-shopping-bag-line"></i>
                <span>Products</span>
            </a>
            <a href="meradd-product.php" class="menu-item <?php echo ($current_page == 'meradd-product.php') ? 'active' : ''; ?>" data-section="add-listings">
                <i class="ri-add-circle-line"></i>
                <span>Add Product</span>
            </a>
            <a href="merorders.php" class="menu-item <?php echo ($current_page == 'merorders.php') ? 'active' : ''; ?>" data-section="orders">
                <i class="ri-file-list-line"></i>
                <span>Orders</span>
            </a>
            <a href="merratings.php" class="menu-item <?php echo ($current_page == 'merratings.php') ? 'active' : ''; ?>" data-section="ratings">
                <i class="ri-star-line"></i>
                <span>Product Ratings</span>
            </a>
            <a href="mermessages.php" class="menu-item <?php echo ($current_page == 'mermessages.php') ? 'active' : ''; ?>" data-section="messages">
                <i class="ri-message-3-line"></i>
                <span>Messages</span>
            </a>
        </div>

        <!--=============== MAIN CONTENT ===============-->
        <div class="shop-content">
            <?php echo $content; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/shopDashboard.js"></script>
</body>
</html> 