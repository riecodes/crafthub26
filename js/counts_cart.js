function updateCartCount() {
    fetch('get_cart_count.php')
        .then(response => response.text())
        .then(data => {
            console.log('Cart count:', data); // Debugging the response
            document.querySelector('.ri-shopping-cart-line ~ .badge span').textContent = data;
        })
        .catch(error => console.error('Error:', error));
}

window.onload = updateCartCount;
