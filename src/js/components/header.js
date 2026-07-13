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
  // `updateHeaderScroll` reads window.scrollY — a layout-forcing property. Calling
  // it synchronously during module init (while the first layout is still pending)
  // forces an immediate style+layout recalc: DevTools Performance attributed ~47ms
  // of load-time "forced reflow" to this exact call. Defer the first read to after
  // the first paint via rAF — the class still lands in frame 1, but the layout is
  // already computed so scrollY is free. The scroll listener stays synchronous
  // (by then layout is clean, so per-event scrollY reads don't force reflow).
  requestAnimationFrame(updateHeaderScroll);

  window.addEventListener('scroll', updateHeaderScroll, { passive: true });
}