const header = document.querySelector('.header');

function updateHeaderScroll() {
  if (!header) return;

  header.classList.toggle('is-scrolled', window.scrollY > 0);

  // Keep the full desktop menu visible down to $md (1270px). The "tablet menu"
  // overlay (is-tablet-menu) was unfinished — it hid the desktop nav on scroll
  // at 1270–1750px while its replacement burger stayed display:none, so the
  // whole menu vanished. Disabled per design decision (2026-06-04).
  header.classList.remove('is-tablet-menu');
}

if (header) {
  updateHeaderScroll();

  window.addEventListener('scroll', updateHeaderScroll, { passive: true });
}