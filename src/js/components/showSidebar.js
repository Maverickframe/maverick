function showSidebar() {
    const sidebarToggle = document.querySelector('.js-sidebar-toggle');
    const sidebarTitle = document.querySelector('.js-sidebar-title');
    const sidebar = document.querySelector('.js-sidebar');

    if (sidebarToggle && sidebarTitle && sidebar) {
        [sidebarToggle, sidebarTitle].forEach(item => {
            item.addEventListener('click', () => {
                sidebarTitle.classList.toggle('active');
                sidebar.classList.toggle('opened');
            });
        });
    }
}

// NB: do NOT gate this behind a load-time `window.innerWidth` check — that was
// evaluated once at script execution, so loading wide (or DevTools resize from
// desktop to mobile) left the "Contents" toggle without a click handler. The
// toggle button is hidden on desktop via CSS, so always wiring it is safe.
// Also run immediately if the DOM is already parsed (module scripts may execute
// after DOMContentLoaded depending on load order).
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showSidebar);
} else {
    showSidebar();
}
