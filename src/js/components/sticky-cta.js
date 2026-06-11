// Global sticky CTA — close button hides ONLY the video, keeping the button.
// State is remembered so the video stays hidden across pages/reloads.

const STORAGE_KEY = 'mfsStickyCtaMediaHidden';

function initStickyCta() {
  const card = document.querySelector('.js-sticky-cta');
  if (!card) return;

  // Restore persisted "video hidden" state.
  try {
    if (localStorage.getItem(STORAGE_KEY) === '1') {
      card.classList.add('is-media-hidden');
    }
  } catch (e) {
    /* localStorage unavailable — ignore */
  }

  const closeBtn = card.querySelector('.js-sticky-cta-close');
  if (!closeBtn) return;

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

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initStickyCta);
} else {
  initStickyCta();
}
