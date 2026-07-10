// lazy-media.js — tiny native-IntersectionObserver stand-in for the only part of
// lazysizes the theme still relied on: swap data-src / data-srcset -> the real
// attribute on `.lazyload` elements (hover-play videos, the panorama iframe, a few
// portfolio images) as they approach the viewport, then add `.lazyloaded` (drives
// the `.blur-up` reveal and marks hover reels ready to play).
//
// Images site-wide already use native loading="lazy" (no data-src), and
// `.js-video-autoplay` videos own their own IntersectionObserver in videoPlay.js —
// both are skipped here. This let us drop the lazysizes + unveilhooks dependency
// from the eager main bundle.

const REVEAL_MARGIN = '600px 0px';

function reveal(el) {
  if (el.dataset.srcset) el.srcset = el.dataset.srcset;
  if (el.dataset.src) el.src = el.dataset.src;
  // <video>/<audio> need an explicit load() to pick up the freshly-set src — the
  // same thing lazysizes' unveilhooks did, so hover reels stay ready to play.
  if (el.tagName === 'VIDEO' || el.tagName === 'AUDIO') {
    try { el.load(); } catch (e) { /* not ready — harmless */ }
  }
  el.classList.add('lazyloaded');
  el.classList.remove('lazyload');
}

const io = 'IntersectionObserver' in window
  ? new IntersectionObserver((entries, obs) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;
      reveal(entry.target);
      obs.unobserve(entry.target);
    });
  }, { rootMargin: REVEAL_MARGIN })
  : null;

// Exported so dynamically-injected markup can be (re)scanned; also runs once on load.
export function scanLazyMedia(root = document) {
  root.querySelectorAll('.lazyload').forEach((el) => {
    if (el.classList.contains('js-video-autoplay')) return; // owned by videoPlay.js
    if (!el.dataset.src && !el.dataset.srcset) return;       // nothing to swap
    if (io) io.observe(el);
    else reveal(el);                                          // no IO support: load now
  });
}

scanLazyMedia();
