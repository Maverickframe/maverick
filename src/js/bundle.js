// Native-IntersectionObserver lazy media (replaces lazysizes + unveilhooks; the
// only markup still using .lazyload/data-src is hover reels, a panorama iframe and
// a few portfolio images). Images are native loading="lazy" site-wide.
import './components/lazy-media';

// --- Always-on, lightweight modules (vanilla JS, no heavy deps) ---------------
// NOTE: modules that wait for DOMContentLoaded (videoPlay, visualResultsGallery,
// sticky-cta) MUST stay in this static list: async chunks can land after the
// event has fired and their init would never run.

import './components/animated-scroll';
import './components/svg-sprite';

import './components/accordeon';
// blogFilter → lazy (blog listing only, gated on .js-articles-items)
import './components/contacts';
import './components/contacts-phone';
import './components/counters';
// filters → lazy (portfolio grids only, gated on .js-portfolio-items/.js-portfolio-front-item)
import './components/header';
import './components/intersectionObserver';
import './components/menu';

import './components/scrollTop';
import './components/services';
import './components/showMore';
import './components/tabs';
// toc → lazy (article/blog singles only, gated on .js-toc-item; DCL-guarded)
import './components/videoPlay';
// mfs-video → lazy (pages with Bunny video only, gated on .js-mfs-video)
import './components/visualResultsGallery';
import './components/select';

import './components/collapse';
import './components/showMoreText';
import './components/showSidebar';
import './components/sticky-cta';
import './components/modal-offer';

// --- Heavy modules: split into async chunks, loaded per feature-detect --------
// The old single bundle evaluated Splide, GSAP+ScrollTrigger, THREE.js, Fancybox
// and SimpleLightbox on EVERY page (~2.1s script eval, 144 KB unused on the
// front page per PSI). Each module below already no-ops without its markup, so
// gating the import on the same selector only skips downloading/evaluating the
// library — behavior is unchanged when the markup exists. These modules query
// the DOM at top level (no DOMContentLoaded dependency), so late chunk arrival
// is safe. Vite turns each import() into a hashed chunk next to main-*.js;
// base "./" keeps chunk URLs relative to /build/assets/, and shared deps
// (uiManager in menu+modals) are deduped into shared chunks automatically.
const lazyModules = [
  // CSS scroll-snap carousels (ведро A/C) — vanilla, no Splide; own light chunk.
  // Every migrated swiper (team-items, visual-results, reviews main, modal stats…)
  // is a `.mfs-snap`, so this one selector replaces the old Splide chunk entirely.
  ['.mfs-snap', () => import('./components/scroll-snap')],
  // Reviews thumbnail ↔ main sync (rides on top of the .mfs-snap main slider)
  ['.js-reviews-slider', () => import('./components/reviews-sync')],
  // (reveal-on-enter moved out of this presence-gated list — see the dedicated
  //  reveal loader below the loop, which defers it off the critical path on pages
  //  whose above-the-fold content doesn't depend on it.)
  // Fancybox gallery page
  ['.js-gallery-tab-btn, .js-gallery-mobile, [data-fancybox]', () => import('./components/gallery')],
  // SimpleLightbox for picture-in-post links
  ['.js-pip a', () => import('./components/lightbox')],
  // --- Page-specific modules moved out of the static entry (JS-split phase) ---
  // Each queries the DOM at top level (or is readyState-guarded), so a late
  // chunk is safe; each no-ops without its markup. Selectors verified against
  // the theme templates, not guessed.
  // Blog listing filter/search/loadmore — only the blog archive.
  ['.js-articles-items', () => import('./components/blogFilter')],
  // Article table-of-contents scroll-spy — blog/article singles (refactored off
  // DOMContentLoaded to a readyState guard so lazy import is safe).
  ['.js-toc-item', () => import('./components/toc')],
  // Portfolio grid filter + front loadmore — portfolio/archive pages only.
  ['.js-portfolio-items, .js-portfolio-front-item', () => import('./components/filters')],
  // Native <video>+hls.js hydrator for Bunny placeholders — pages with video
  // only. Self-guards on readyState + top-level .js-mfs-video query; hls.js is a
  // further lazy chunk fetched only when a player actually plays.
  ['.js-mfs-video', () => import('./components/mfs-video')]
];

lazyModules.forEach(([selector, load]) => {
  if (document.querySelector(selector)) load();
});

// --- Reveal-on-enter loader (vanilla IntersectionObserver + CSS transitions) ---
// Reveal must download before an above-the-fold `.js-reveal` paints, or that
// content stays opacity:0 until the chunk lands. Inner-page heroes ARE `.js-reveal`
// (opacity:0) above the fold, so there reveal stays EAGER. The HOMEPAGE hero uses a
// CSS-only reveal (`.hero-front__reveal`, added only when is_front_page) and its
// first opacity:0 `.js-reveal` sits ~1000px down — nothing above the fold needs the
// chunk. So on the homepage we load reveal OFF the critical request chain (on idle
// or first scroll/pointer) instead of during this synchronous pass. The only
// above-the-fold dependant is the hero H1's decorative `.js-highlight` sweep, whose
// text is fully readable before the sweep plays. Pilot = homepage only (via the
// front-page-only `.hero-front__reveal` marker); extend to other page types once
// their heroes are CSS-revealed too.
if (document.querySelector('.js-reveal, .js-reveal-group, .js-highlight')) {
  const loadReveal = () => import('./components/reveal');
  if (document.querySelector('.hero-front__reveal')) {
    let fired = false;
    const once = () => {
      if (fired) return;
      fired = true;
      loadReveal();
    };
    // Wait for the load event BEFORE idle so the chunk is requested after the
    // initial render burst — that's what keeps it out of Lighthouse's critical
    // request chain (requestIdleCallback alone can fire before `load` on a fast
    // page, landing the request back inside the critical window). First scroll /
    // pointer still triggers it earlier if the visitor engages.
    const afterLoad = () => {
      if ('requestIdleCallback' in window) {
        requestIdleCallback(once, { timeout: 2000 });
      } else {
        setTimeout(once, 200);
      }
    };
    if (document.readyState === 'complete') {
      afterLoad();
    } else {
      addEventListener('load', afterLoad, { once: true });
    }
    addEventListener('scroll', once, { once: true, passive: true });
    addEventListener('pointerdown', once, { once: true, passive: true });
  } else {
    loadReveal();
  }
}

// --- Interaction-gated modules: loaded on first CLICK, not on presence ---------
// The modal system and the book-a-call calendar have their trigger buttons /
// hidden shells on almost every page, so presence-gating loaded them site-wide on
// initial load — putting them on PSI's critical request chain even though nothing
// opens until the visitor clicks. Both are pure interaction: load them on the
// first relevant click instead. A lightweight always-on delegated listener catches
// that first click, imports the chunk, then hands the click off to the chunk.

// Popups (Book-a-call, Download catalog, What-We-Do). modals.js registers its own
// body listener on import for every SUBSEQUENT click; openFor() handles this first
// one (which fired before the chunk's listener existed).
let modalsLoaded = false;
document.body.addEventListener('click', (e) => {
  if (modalsLoaded) return;
  const opener = e.target.closest('.js-modal-open');
  if (!opener && !e.target.closest('.js-modal-close')) return;
  modalsLoaded = true;
  import('./components/modals').then((m) => {
    if (opener && m.openFor) m.openFor(opener);
  });
});

// Book-a-call CALENDAR builder. The book_call_click funnel event must fire on
// EVERY open (incl. the first, before the chunk loads), so it stays here in the
// static bundle; only the heavy calendar UI is lazy-imported once.
let bookcalLoaded = false;
document.body.addEventListener('click', (e) => {
  if (!(e.target.closest && e.target.closest('[data-modal="bookcall"]'))) return;
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({ event: 'book_call_click', form_name: 'book_call_calendar' });
  if (bookcalLoaded) return;
  bookcalLoaded = true;
  import('./components/book-calendar').then((m) => m.initBookCalendar());
});

// --- Below-the-fold modules: loaded when their block approaches the viewport --
// These blocks always sit deep in the page, and their INIT cost (not just the
// download) is what hurts: on solutions pages quiz + calculator + workflow-dot-
// snap init made the same 75 KB main.js evaluate for ~2s (PSI TBT 740-1070ms)
// while on the front page they no-op. IO-gating moves both download AND init
// out of the initial load — and out of the Lighthouse trace entirely.
// Safe to arrive late: quiz/calculator init behind a readyState guard,
// workflow-dot-snap re-snaps via its own setTimeout(600) fallback,
// renderReveal queries the DOM at top level.
function loadWhenNear(selector, load) {
  const el = document.querySelector(selector);
  if (!el) return;
  const io = new IntersectionObserver(
    (entries, observer) => {
      if (entries.some((e) => e.isIntersecting)) {
        observer.disconnect();
        load();
      }
    },
    { rootMargin: '600px 0px' }
  );
  io.observe(el);
}

// Render-reveal interactive (canvas 2D, no deps) — worldwide-rendering block.
// Replaced the THREE.js particle sphere (551 KB chunk).
loadWhenNear('.js-render-reveal', () => import('./components/renderReveal'));
// Lead quiz (branching stepper)
loadWhenNear('.js-mfsq', () => import('./components/quiz'));
// Price calculator
loadWhenNear('.js-mfcalc', () => import('./components/calculator'));
// Workflow dot-snapping (heavy getBoundingClientRect loops)
loadWhenNear('.workflow', () => import('./components/workflow-dot-snap'));