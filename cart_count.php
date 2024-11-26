<?php
    include 'dbconnect.php';
    session_start();

    // Ensure user is logged in
    if (isset($_SESSION['userID'])) {
        $user_id = $_SESSION['userID'];

        // Query to count the number of items in the cart
        $cart_count_query = "SELECT COUNT(*) AS cart_count 
                             FROM cart 
                             WHERE user_id = '$user_id' AND status = 'cart'";

        $cart_count_result = $connection->query($cart_count_query);

        // Check if the query was successful
        if (!$cart_count_result) {
            die("Query failed: " . $connection->error);
        }

        // Fetch the cart count and store it in a session variable
        $cart_count_row = $cart_count_result->fetch_assoc();
        $_SESSION['cart_count'] = $cart_count_row['cart_count'];
    } else {
        $_SESSION['cart_count'] = 0; // Default to 0 if not logged in
    }

?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function updateCartCount() {
        $.ajax({
            url: 'get_cart_count.php',  // Separate PHP file to fetch the cart count
            type: 'GET',
            success: function(data) {
                var cartCount = parseInt(data);  // Assuming 'data' returns the cart count

                // Update the cart count span
                $('#cart-count').text(cartCount);

                // Show or hide the badge based on the count
                if (cartCount > 0) {
                    $('#cart-count-badge').show();  // Show badge if cart count > 0
                } else {
                    $('#cart-count-badge').hide();  // Hide badge if cart count == 0
                }
            }
        });
    }

    // Call this function whenever the cart is updated (e.g., after adding/removing items)
    // Example: updateCartCount();
</script>
