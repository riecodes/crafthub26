document.addEventListener('DOMContentLoaded', function() {
    // Select all the remove buttons
    const removeButtons = document.querySelectorAll('.remove-product');
    const incrementButtons = document.querySelectorAll('.add-btn');
    const decrementButtons = document.querySelectorAll('.minus-btn');
    const selectAllCheckbox = document.getElementById('select-all');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const deleteAllText = document.getElementById('delete-all');

    // Handle product removal
    removeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const cartItem = this.closest('.cart-item');
            removeCartItem(cartItem, productId);
        });
    });

    // Handle incrementing quantity
    incrementButtons.forEach(button => {
        button.addEventListener('click', function() {
            const unitPrice = parseFloat(this.getAttribute('data-unit-price'));
            incrementQuantity(this, unitPrice);
        });
    });

    // Handle decrementing quantity
    decrementButtons.forEach(button => {
        button.addEventListener('click', function() {
            const unitPrice = parseFloat(this.getAttribute('data-unit-price'));
            decrementQuantity(this, unitPrice);
        });
    });

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        productCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
            updateCartItemAppearance(checkbox);
        });
    });

    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateCartItemAppearance(this);
            
            // Update Select All checkbox
            const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        });
    });

    // Click anywhere on cart item to toggle selection
    const cartItemsForSelection = document.querySelectorAll('.cart-item');
    cartItemsForSelection.forEach(item => {
        item.addEventListener('click', function(e) {
            // Prevent toggling when clicking on buttons or checkboxes
            if (!e.target.closest('button') && !e.target.closest('.product-checkbox')) {
                const checkbox = this.querySelector('.product-checkbox');
                checkbox.checked = !checkbox.checked;
                updateCartItemAppearance(checkbox);

                // Update Select All checkbox
                const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            }
        });
    });

    // Delete All functionality
    deleteAllText.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete all items from your cart?')) {
            deleteAllCartItems();
        }
    });

    // Add swipe functionality
    addSwipeToReveal();

    // Functions

    function removeCartItem(item, productId) {
        const shopContainer = item.closest('.shop-container');

        fetch('remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cart_id: productId }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                item.remove();

                const remainingItems = shopContainer.querySelectorAll('.cart-item');
                if (remainingItems.length === 0) {
                    shopContainer.remove();
                }

                const remainingShops = document.querySelectorAll('.shop-container');
                if (remainingShops.length === 0) {
                    displayEmptyCartMessage();
                }
            } else {
                console.log('Failed to remove product. Error: ', data.error);
                alert('Failed to remove the product.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function incrementQuantity(button, unitPrice) {
        var quantityElement = button.parentElement.querySelector('.quantity');
        var currentQuantity = parseInt(quantityElement.textContent);

        quantityElement.textContent = currentQuantity + 1;
        updatePrice(button, unitPrice);

        const productId = button.closest('.cart-item').querySelector('.product-checkbox').value;
        updateQuantityInDatabase(productId, currentQuantity + 1);
    }

    function decrementQuantity(button, unitPrice) {
        var quantityElement = button.parentElement.querySelector('.quantity');
        var currentQuantity = parseInt(quantityElement.textContent);

        if (currentQuantity > 1) {
            quantityElement.textContent = currentQuantity - 1;
            updatePrice(button, unitPrice);

            const productId = button.closest('.cart-item').querySelector('.product-checkbox').value;
            updateQuantityInDatabase(productId, currentQuantity - 1);
        }
    }

    function updatePrice(button, unitPrice) {
        var quantityElement = button.parentElement.querySelector('.quantity');
        var currentQuantity = parseInt(quantityElement.textContent);
        var newTotalPrice = currentQuantity * unitPrice;
        var totalPriceElement = button.closest('.cart-item').querySelector('.total-price');
        totalPriceElement.textContent = '₱' + newTotalPrice.toFixed(2);
    }

    function updateQuantityInDatabase(productId, newQuantity) {
        fetch('update_cart_quantity.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cart_id=${productId}&quantity=${newQuantity}`,
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error('Failed to update quantity:', data.error);
                alert('Failed to update the quantity in the database.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function displayEmptyCartMessage() {
        const container = document.querySelector('.cart-container');
        
        container.innerHTML = `
            <div class="cart-header">
                <h2><i class="fas fa-shopping-cart"></i> My Cart</h2>
            </div>
            <div class="empty-cart-message">
                <i class="fas fa-shopping-basket"></i>
                <p>Your cart is empty</p>
                <a href="homepage.php" class="btn-shop">Continue Shopping</a>
            </div>
        `;

        const checkoutButton = document.querySelector('.btn-checkout');
        if (checkoutButton) {
            checkoutButton.remove();
        }
    }

    function updateCartItemAppearance(checkbox) {
        const cartItem = checkbox.closest('.cart-item');
        if (checkbox.checked) {
            cartItem.classList.add('selected');
        } else {
            cartItem.classList.remove('selected');
        }
    }

    function addSwipeToReveal() {
        const cartItems = document.querySelectorAll('.cart-item');
        
        cartItems.forEach(item => {
            let startX, moveX;
            let isSwiping = false;

            item.addEventListener('touchstart', (e) => {
                startX = e.touches[0].clientX;
                isSwiping = true;
            });

            item.addEventListener('touchmove', (e) => {
                if (!isSwiping) return;
                moveX = e.touches[0].clientX;
                const diff = startX - moveX;

                if (diff > 0 && diff <= 80) {
                    item.querySelector('.cart-item-content').style.transform = `translateX(-${diff}px)`;
                    item.querySelector('.item-total').style.transform = `translateX(-${diff}px)`;
                    item.querySelector('.btn-remove-swipe').style.transform = `translateX(${80 - diff}px)`;
                }
            });

            item.addEventListener('touchend', () => {
                isSwiping = false;
                const diff = startX - moveX;

                if (diff > 40) {
                    item.classList.add('swiped');
                    item.querySelector('.cart-item-content').style.transform = 'translateX(-80px)';
                    item.querySelector('.item-total').style.transform = 'translateX(-80px)';
                    item.querySelector('.btn-remove-swipe').style.transform = 'translateX(0)';
                } else {
                    item.classList.remove('swiped');
                    item.querySelector('.cart-item-content').style.transform = 'translateX(0)';
                    item.querySelector('.item-total').style.transform = 'translateX(0)';
                    item.querySelector('.btn-remove-swipe').style.transform = 'translateX(100%)';
                }
            });
        });

        // Close swipe when clicking anywhere else
        document.addEventListener('touchstart', (e) => {
            cartItems.forEach(item => {
                if (!item.contains(e.target) && item.classList.contains('swiped')) {
                    item.classList.remove('swiped');
                    item.querySelector('.cart-item-content').style.transform = 'translateX(0)';
                    item.querySelector('.item-total').style.transform = 'translateX(0)';
                    item.querySelector('.btn-remove-swipe').style.transform = 'translateX(100%)';
                }
            });
        });
    }

    function deleteAllCartItems() {
        fetch('delete_all_cart_items.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const shopContainers = document.querySelectorAll('.shop-container');
                shopContainers.forEach(container => container.remove());
                displayEmptyCartMessage();
                updateCartCount();
            } else {
                console.log('Failed to delete all items. Error: ', data.error);
                alert('Failed to delete all items from the cart.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    // Check if cart is empty on page load
    const cartItems = document.querySelectorAll('.cart-item');
    if (cartItems.length === 0) {
        displayEmptyCartMessage();
    }
});

// Function to update cart count (you may need to implement this based on your requirements)
function updateCartCount() {
    // Implementation for updating cart count
}
