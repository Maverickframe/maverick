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

if (window.innerWidth < 1270) {
    document.addEventListener('DOMContentLoaded', showSidebar);
}