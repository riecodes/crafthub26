document.addEventListener('DOMContentLoaded', function() {
    const merchantYes = document.getElementById('merchantYes');
    const merchantNo = document.getElementById('merchantNo');
    const nextBtn = document.getElementById('nextBtn');
    const personalSection = document.querySelector('.personal-section');
    const businessSection = document.querySelector('.business-section');
    const backBtn = document.getElementById('backBtn');
    const registerCheck = document.getElementById('register-check');

    // Function to update button text based on merchant selection
    function updateButtonText() {
        if (merchantNo.checked) {
            nextBtn.textContent = 'Register';
        } else {
            nextBtn.textContent = 'Next';
        }
    }

    // Initial button text setup
    updateButtonText();

    // Event listeners for merchant radio buttons
    merchantYes.addEventListener('change', updateButtonText);
    merchantNo.addEventListener('change', updateButtonText);

    // Enable/disable next button based on terms checkbox
    registerCheck.addEventListener('change', function() {
        nextBtn.disabled = !this.checked;
    });

    // Handle next button click
    nextBtn.addEventListener('click', function() {
        if (merchantYes.checked) {
            // Show business section if merchant
            personalSection.style.display = 'none';
            businessSection.style.display = 'block';
            setTimeout(() => {
                businessSection.style.opacity = '1';
            }, 50);
        } else {
            // Submit form if not merchant
            document.querySelector('form').submit();
        }
    });

    // Handle back button click
    backBtn?.addEventListener('click', function() {
        businessSection.style.opacity = '0';
        setTimeout(() => {
            businessSection.style.display = 'none';
            personalSection.style.display = 'block';
        }, 300);
    });
}); 