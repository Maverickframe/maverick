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

  // 2) Suppress the card while the Free Test Render form OR the lead quiz is in
  //    view, so it never overlaps either. The theme uses a virtualised
  //    (transform-based) scroll, so native scroll events and IntersectionObserver
  //    don't fire — poll the rects each frame (getBoundingClientRect reflects the
  //    transformed position) and only toggle the class when the state changes.
  const sections = document.querySelectorAll('.free-test-render, .mfsq, .footer');
  if (sections.length) {
    let last = null;
    const tick = () => {
      let inView = false;
      sections.forEach((s) => {
        const rect = s.getBoundingClientRect();
        if (rect.top < window.innerHeight && rect.bottom > 0) inView = true;
      });
      if (inView !== last) {
        last = inView;
        card.classList.toggle('is-suppressed', inView);
      }
      window.requestAnimationFrame(tick);
    };
    window.requestAnimationFrame(tick);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initStickyCta);
} else {
  initStickyCta();
}
