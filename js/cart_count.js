function updateCartCount() {
    $.ajax({
        url: 'get_cart_count.php',
        type: 'GET',
        success: function(data) {
            var cartCount = parseInt(data);

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

// Trigger the update whenever the page loads or an event happens
$(document).ready(function() {
    updateCartCount();
});
