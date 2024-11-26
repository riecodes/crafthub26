document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.getAttribute('href') === '#') {
                e.preventDefault();
                
                // Remove active class from all menu items
                menuItems.forEach(menuItem => {
                    menuItem.classList.remove('active');
                });
                
                // Add active class to clicked menu item
                this.classList.add('active');
                
                // Hide all sections
                document.querySelectorAll('.content-section').forEach(section => {
                    section.style.display = 'none';
                });
                
                // Show selected section
                const sectionName = this.getAttribute('data-section');
                const selectedSection = document.querySelector(`.${sectionName}-section`);
                if (selectedSection) {
                    selectedSection.style.display = 'block';
                }
            }
        });
    });
});

// Order search functionality
function searchOrders() {
    const input = document.getElementById('orderSearch');
    const filter = input.value.toLowerCase();
    const containers = document.getElementsByClassName('prod-container');
    let noResultsMessage = document.getElementById('no-results-message');
    let resultsFound = false;

    // Remove existing "no results" message if it exists
    if (noResultsMessage) {
        noResultsMessage.remove();
    }

    for (let container of containers) {
        const productName = container.getAttribute('data-product-name');
        if (productName.includes(filter)) {
            container.style.display = "";
            resultsFound = true;
        } else {
            container.style.display = "none";
        }
    }

    // Display "No results" message if no matches found
    if (!resultsFound && filter !== "") {
        noResultsMessage = document.createElement('p');
        noResultsMessage.id = 'no-results-message';
        noResultsMessage.textContent = `No results for "${input.value}"`;
        noResultsMessage.style.textAlign = 'center';
        noResultsMessage.style.marginTop = '20px';
        noResultsMessage.style.fontStyle = 'italic';
        document.querySelector('.orders-section').appendChild(noResultsMessage);
    }
}

// Add event listener for real-time search
document.getElementById('orderSearch')?.addEventListener('keyup', searchOrders); 