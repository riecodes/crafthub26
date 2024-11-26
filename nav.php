<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session only if it's not already started
}

if (isset($_SESSION['userID'])) { // Check if userID is set
    require_once('dbconnect.php');

    $user_id = $_SESSION['userID'];

    // Fetch user details
    $query = "SELECT * FROM crafthub_users WHERE user_id = '$user_id'";
    $result22 = mysqli_query($connection, $query);

    $isApprovedMerchant = false; // Default value

    if ($result22) {
        $row11 = mysqli_fetch_assoc($result22);
        $_SESSION['username'] = $row11['username'];
        $_SESSION['user_profile'] = $row11['user_profile'];

        // Check if the user role is merchant
        if (trim($row11['role']) === 'merchant') {
            $isApprovedMerchant = true;
        }

        if (trim($row11['role']) === 'customer') {
            $isApprovedMerchant = false;
        }
    }

    // Check if the user is an approved merchant in merchant_applications
    $merchantQuery = "SELECT status FROM merchant_applications WHERE user_id = '$user_id'";
    $merchantResult = mysqli_query($connection, $merchantQuery);

    if ($merchantResult && mysqli_num_rows($merchantResult) > 0) {
        $merchantData = mysqli_fetch_assoc($merchantResult);
        
        // Debug: Print the actual status for verification
        echo "<!-- Merchant Status: " . $merchantData['status'] . " -->"; // Temporary debug line
        
        // Check if status is 'pending' or if role is 'merchant'
        if (trim($merchantData['status']) === 'pending' || $isApprovedMerchant) {
            $isApprovedMerchant = true;
        }
    }
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
    <link rel="stylesheet" href="css/nav.css">
</head>
<body>
<!--=============== NAVIGATION ==============-->
<nav class="ch-flexMain sticky-top" id="mainNavigation">
    <div class="ch-flex3">
        <a href="homepage.php" class="ch-logo">
            <img src="images/navlogo.png" alt="CraftHub Logo">
        </a>
    </div>
    <div class="ch-flex2">
        <form action="searchpage.php" method="GET">
            <div class="ch-search-wrapper">
                <input type="search" name="searchbar" placeholder="Search" class="ch-search-input">
                <button type="submit" class="ch-search-btn" name="btnsearch">
                    <i class='bx bx-search'></i>
                </button>
            </div>
        </form>
    </div>
    <div class="ch-nav-user-section">
        <div id="ch-icons">
            <ul>
                <?php if ($isApprovedMerchant): ?>
                    <li>
                        <a href="merdashboard.php">
                            <i class="fas fa-store"></i>
                        </a>
                    </li>
                <?php endif; ?>
                <?php
                // Determine the appropriate chatroom link
                $chatroomLink = "chatroom_customer.php"; // Default for customers
                if ($isApprovedMerchant) {
                    $chatroomLink = "chatroom.php"; // Redirect to merchant chatroom
                }
                ?>
                <li>
                    <a href="<?php echo $chatroomLink; ?>">
                        <i class="fas fa-envelope"></i>
                        <span class="ch-count"><span id="unreadMessageCount">0</span></span>
                    </a>
                </li>
                <li>
                    <a href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="ch-count"><?php echo $_SESSION['cart_count']; ?></span> 
                    </a>
                </li>
            </ul>
        </div>
        <div class="ch-nav-divider"></div>
        <div class="ch-nav-user-profile">
       
            <span class="ch-nav-username">Welcome, <?php echo $_SESSION['username']; ?></span>
        </div>
        <div class="ch-nav-divider"></div>
        <div class="ch-dropdown">
            <button class="ch-dropbtn">
                <i class="fas fa-bars"></i>
            </button>
            <div class="ch-dropdown-content">
                <a href="accountsettings2.php">Account</a>
                <a href="mypurchase.php">My Purchases</a>
                <a href="storemap.php">Store Map</a>
                <a href="index.php">Log Out <i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
        <!--=============== SIDEBAR ==============-->
        <div class="ch-menu-icon">
            <button class="ch-sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    <div id="ch-overlay"></div>
    <div class="ch-sidebar-overlay" id="ch-sidebar-overlay">
    <div class="ch-sidebar-content">
        <button class="ch-sidebar-close">&times;</button>
        <a href="accountsettings2.php">Account</a>
        <a href="mypurchase.php">My Purchases</a>
        <a href="storemap.php">Store Map</a>
        <a href="logout.php">Log Out</a>
    </div>
</div>
</nav>
<!--=============== END NAVIGATION ==============-->
<script src="js/nav.js"></script>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateLastActivity() {
        $.ajax({
            url: 'online_status.php',
            method: 'POST',
            success: function(response) {
                let result = JSON.parse(response);
                if (result.status === 'error') {
                    console.log("Error updating last activity: " + result.message);
                }
            },
            error: function() {
                console.log("AJAX request failed.");
            }
        });
    }

    updateLastActivity();

    setInterval(updateLastActivity, 6000); 
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function fetchUnreadMessagesCount() {
            fetch('get_unread_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.unread_count !== undefined) {
                        document.getElementById('unreadMessageCount').textContent = data.unread_count;
                    } else {
                        console.error(data.error);
                    }
                })
                .catch(error => console.error('Fetch Error:', error));
        }

        fetchUnreadMessagesCount();

        setInterval(fetchUnreadMessagesCount, 3000);
    });
</script>

</html>
