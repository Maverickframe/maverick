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

  // 2) Suppress the card while the Free Test Render form is in view.
  //    Scroll-based (reliable everywhere) rather than IntersectionObserver.
  const section = document.querySelector('.free-test-render');
  if (section) {
    let ticking = false;

    const update = () => {
      ticking = false;
      const rect = section.getBoundingClientRect();
      const inView = rect.top < window.innerHeight && rect.bottom > 0;
      card.classList.toggle('is-suppressed', inView);
    };

    const onScroll = () => {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(update);
      }
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    update();
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initStickyCta);
} else {
  initStickyCta();
}
