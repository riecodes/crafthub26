document.getElementById('showFormBtn').addEventListener('click', function() {
    // Hide the 'Start' button
    document.getElementById('showFormBtn').style.display = 'none';

    // Show the buttons and dropdown
    document.getElementById('buttonContainer').style.display = 'flex';
    document.getElementById('loginBtn').style.display = 'inline-block';
    document.getElementById('registerDropdown').style.display = 'inline-block';

    // Add event listener to toggle dropdown behavior
    document.getElementById('registerDropdown').addEventListener('click', function() {
        var dropdownMenu = document.querySelector('#registerDropdown + .dropdown-menu');
        dropdownMenu.classList.toggle('show'); // Ensure the dropdown menu is toggled
    });
});
