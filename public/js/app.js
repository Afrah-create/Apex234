// Sidebar toggle for mobile (no Alpine.js required)
document.addEventListener('DOMContentLoaded', function() {
    var menuBtn = document.querySelector('.navbar-menu button');
    var sidebar = document.querySelector('.sidebar');
    var overlay = document.querySelector('.sidebar-overlay');

    if (menuBtn && sidebar) {
        menuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        });
    }
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    }
    // Hide sidebar on resize to desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024 && sidebar) {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('open');
        }
    });
}); 