const editButton = document.getElementById('editButton');
const saveButton = document.getElementById('saveButton');
const cancelButton = document.getElementById('cancelButton');
const uploadButton = document.getElementById('uploadButton');
const resetButton = document.getElementById('resetImage');
const inputs = document.querySelectorAll('input[type="text"]');
const selects = document.querySelectorAll('select');  // Add this line to include dropdowns

// Store the original image source
const originalImageSrc = imageContainer.src;

editButton.addEventListener('click', () => {
    inputs.forEach(input => input.removeAttribute('readonly'));
    selects.forEach(select => select.removeAttribute('disabled'));  // Enable dropdowns
    
    // Show upload and reset buttons
    uploadButton.classList.remove('d-none');
    resetButton.classList.remove('d-none');
    
    // Toggle buttons visibility
    editButton.classList.add('d-none');
    saveButton.classList.remove('d-none');
    cancelButton.classList.remove('d-none');
});

cancelButton.addEventListener('click', () => {
    inputs.forEach(input => input.setAttribute('readonly', 'readonly'));
    selects.forEach(select => select.setAttribute('disabled', 'disabled'));  // Disable dropdowns
    
    // Hide upload and reset buttons
    uploadButton.classList.add('d-none');
    resetButton.classList.add('d-none');
    
    // Toggle buttons visibility
    editButton.classList.remove('d-none');
    saveButton.classList.add('d-none');
    cancelButton.classList.add('d-none');
});

saveButton.addEventListener('click', () => {
    document.getElementById('userProfileForm').submit();
});

resetButton.addEventListener('click', () => {
    // Reset the image to the original source
    imageContainer.src = originalImageSrc;
});
