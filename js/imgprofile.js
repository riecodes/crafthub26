document.addEventListener('DOMContentLoaded', () => {
    const profileImage = document.getElementById('profileImage');
    const uploadImageInput = document.getElementById('uploadImageInput');
    const resetImage = document.getElementById('resetImage');

    uploadImageInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                profileImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    resetImage.addEventListener('click', () => {
        
    });
});