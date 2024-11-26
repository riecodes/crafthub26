document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all "View Details" links in dashboard
    const viewDetailsLinks = document.querySelectorAll('.card-footer a[href="orders.php"]');
    
    viewDetailsLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the order type from the closest overview-card
            const card = this.closest('.overview-card');
            const cardIcon = card.querySelector('.card-icon-area i');
            
            // Determine which tab to open based on the icon class
            let tabId = '';
            if (cardIcon.classList.contains('ri-checkbox-circle-line')) {
                tabId = 'completed';
            } else if (cardIcon.classList.contains('ri-wallet-3-line')) {
                tabId = 'pending';
            } else if (cardIcon.classList.contains('ri-truck-line')) {
                tabId = 'shipped';
            } else if (cardIcon.classList.contains('ri-close-circle-line')) {
                tabId = 'cancelled';
            } else if (cardIcon.classList.contains('ri-arrow-go-back-line')) {
                tabId = 'returned';
            }
            
            // Store the tab ID in sessionStorage
            sessionStorage.setItem('activeOrderTab', tabId);
            
            // Navigate to orders.php
            window.location.href = 'orders.php';
        });
    });

    // Check if we're on the orders page
    if (window.location.pathname.includes('orders.php')) {
        // Get the stored tab ID
        const activeTab = sessionStorage.getItem('activeOrderTab');
        
        if (activeTab) {
            // Find the tab button and trigger a click
            const tabButton = document.querySelector(`#${activeTab}-tab`);
            if (tabButton) {
                tabButton.click();
            }
            
            // Clear the stored tab ID
            sessionStorage.removeItem('activeOrderTab');
        }
    }
}); 