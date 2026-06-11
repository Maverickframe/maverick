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
  const section = document.querySelector('.free-test-render');
  if (section && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          card.classList.toggle('is-suppressed', entry.isIntersecting);
        });
      },
      { threshold: 0 }
    );
    observer.observe(section);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initStickyCta);
} else {
  initStickyCta();
}
