document.addEventListener('DOMContentLoaded', function() {
    const modalAgreeCheckbox = document.getElementById('agreeCheckbox');
    const registerCheckbox = document.getElementById('register-check');
    const nextBtn = document.getElementById('nextBtn');

    // When modal checkbox is clicked
    modalAgreeCheckbox.addEventListener('click', function() {
        if (this.checked) {
            registerCheckbox.checked = true;
            $('#iagreeModal').modal('hide');
            updateNextButtonState();
        }
    });

    // When main form checkbox is clicked
    registerCheckbox.addEventListener('click', function(event) {
        // Prevent the default checkbox behavior
        event.preventDefault();
        // Show the modal
        $('#iagreeModal').modal('show');
    });

    // When modal is hidden
    $('#iagreeModal').on('hidden.bs.modal', function () {
        // Sync the main checkbox with modal checkbox
        registerCheckbox.checked = modalAgreeCheckbox.checked;
        updateNextButtonState();
    });

    // Update next button state
    function updateNextButtonState() {
        nextBtn.disabled = !registerCheckbox.checked;
        // Remove color change, keep it light
        nextBtn.classList.add('btn-light');
        nextBtn.classList.remove('btn-primary');
    }

    // Initialize states
    registerCheckbox.checked = false;
    modalAgreeCheckbox.checked = false;
    updateNextButtonState();
});
