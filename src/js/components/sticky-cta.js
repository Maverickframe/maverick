// Global sticky CTA behaviour:
//  1) close button hides ONLY the video (button stays), remembered across pages;
//  2) the whole card is auto-hidden while the Free Test Render section is on
//     screen, so it never overlaps that form.

const STORAGE_KEY = 'mfsStickyCtaMediaHidden';

function initStickyCta() {
  const card = document.querySelector('.js-sticky-cta');
  if (!card) return;

  // 1) Restore persisted "video hidden" state.
  try {
    if (localStorage.getItem(STORAGE_KEY) === '1') {
      card.classList.add('is-media-hidden');
    }
  } catch (e) {
    /* localStorage unavailable — ignore */
  }

  const closeBtn = card.querySelector('.js-sticky-cta-close');
  if (closeBtn) {
    closeBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      card.classList.add('is-media-hidden');
      try {
        localStorage.setItem(STORAGE_KEY, '1');
      } catch (err) {
        /* ignore */
      }
    });
  }

  // 1b) Keep the card hidden on the FIRST screen; arm it only once the visitor
  //     has scrolled roughly one viewport down. A zero-size sentinel parked at
  //     top:100vh triggers the reveal — no scroll listener, no per-frame reads.
  //     Bonus: while the card is display:none the inner player's Intersection
  //     Observer can't fire, so the Bunny stream + poster are NOT fetched on the
  //     first screen (the card is position:fixed, so it would otherwise always
  //     intersect and load immediately). Rendered with `is-armed` in PHP so it
  //     never flashes before hydration.
  if (card.classList.contains('is-armed') && 'IntersectionObserver' in window) {
    const sentinel = document.createElement('div');
    sentinel.setAttribute('aria-hidden', 'true');
    sentinel.style.cssText =
      'position:absolute;top:100vh;left:0;width:1px;height:1px;pointer-events:none;';
    document.body.appendChild(sentinel);
    const reveal = () => {
      card.classList.remove('is-armed');
      armIo.disconnect();
      sentinel.remove();
    };
    const armIo = new IntersectionObserver((entries) => {
      // Reveal when the sentinel is in view OR already scrolled past it. A fast
      // scroll / Page Down can jump the sentinel above the viewport in a single
      // frame — then IO fires once with isIntersecting:false, so a plain
      // isIntersecting check would miss it and the card would never appear.
      // boundingClientRect.top < 0 catches "already scrolled past".
      if (entries.some((e) => e.isIntersecting || e.boundingClientRect.top < 0)) {
        reveal();
      }
    });
    armIo.observe(sentinel);
  } else {
    // No IO support (very old browsers): just show it.
    card.classList.remove('is-armed');
  }

  // 2) Suppress the card while the Free Test Render form OR the lead quiz is in
  //    view, so it never overlaps either. Scroll is native (no virtualised /
  //    transform-based scroll — GSAP/Lenis were removed), so IntersectionObserver
  //    fires reliably. Observe the sections and toggle the class only on change —
  //    zero per-frame layout reads (the old rAF poll forced a reflow every frame).
  const sections = document.querySelectorAll('.free-test-render, .mfsq, .footer');
  if (sections.length && 'IntersectionObserver' in window) {
    const visible = new Set();
    let last = null;
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) visible.add(entry.target);
        else visible.delete(entry.target);
      });
      const inView = visible.size > 0;
      if (inView !== last) {
        last = inView;
        card.classList.toggle('is-suppressed', inView);
      }
    });
    sections.forEach((s) => io.observe(s));
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initStickyCta);
} else {
  initStickyCta();
}
