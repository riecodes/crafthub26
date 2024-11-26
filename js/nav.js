document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.querySelector('.ch-sidebar-toggle');
    const sidebarOverlay = document.getElementById('ch-sidebar-overlay');
    const sidebarClose = document.querySelector('.ch-sidebar-close');

    sidebarToggle.addEventListener('click', function() {
        sidebarOverlay.classList.add('active');
    });

    sidebarClose.addEventListener('click', function() {
        sidebarOverlay.classList.remove('active');
    });

    sidebarOverlay.addEventListener('click', function(e) {
        if (e.target === sidebarOverlay) {
            sidebarOverlay.classList.remove('active');
        }
    });
});
