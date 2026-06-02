const header = document.querySelector('.header');
const MD_WIDTH = 1270;
const LG_WIDTH = 1750;

function updateTabletMenu() {
  if (!header) return;

  const isTablet = window.innerWidth >= MD_WIDTH && window.innerWidth < LG_WIDTH;
  const scrolled = window.scrollY > 0;

  header.classList.toggle('is-tablet-menu', isTablet && scrolled);
}

function updateHeaderScroll() {
  if (!header) return;

  header.classList.toggle('is-scrolled', window.scrollY > 0);
  updateTabletMenu();
}

if (header) {
  updateHeaderScroll();

  window.addEventListener('scroll', updateHeaderScroll, { passive: true });
  window.addEventListener('resize', updateTabletMenu);
}