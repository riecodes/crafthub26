let uploadedFiles = {}; // Track uploaded files by their input ID

function previewPDF(input, inputId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            var pdfPreviewFrame = document.getElementById('pdfPreviewFrame');
            pdfPreviewFrame.src = e.target.result;
            pdfPreviewFrame.setAttribute('data-input-id', inputId);
            
            var pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));
            pdfModal.show();
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removePDF() {
    const previewElement = document.getElementById('pdfPreviewFrame');
    const inputId = previewElement.dataset.inputId; // Get the inputId from the preview element
    const inputElement = document.getElementById(inputId);

    if (inputElement) {
        inputElement.value = ''; // Reset the file input
        updateLabel(inputElement, "Choose File"); // Reset the label
        delete uploadedFiles[inputId]; // Remove the file from the uploadedFiles object
    }

    previewElement.src = ''; // Clear the PDF preview
    $('#pdfModal').modal('hide'); // Hide the modal
}

function updateLabel(input, labelText) {
    const label = input.closest('.file-upload').querySelector('label[for="' + input.id + '"]');
    if (label) {
        label.textContent = labelText;
    }
}

document.querySelectorAll('input[type="file"]').forEach(input => {
    input.addEventListener('click', (e) => {
        const inputId = input.id;
        if (uploadedFiles[inputId]) {
            // Prevent file upload dialog when a file is already uploaded
            e.preventDefault();
            previewPDF(input, inputId);
        }
    });
    input.addEventListener('change', (e) => {
        if (input.files.length > 0) {
            updateLabel(input, "File Uploaded");
        }
    });
});
