import 'lazysizes';
import 'lazysizes/plugins/unveilhooks/ls.unveilhooks';

// --- Always-on, lightweight modules (vanilla JS, no heavy deps) ---------------
// NOTE: modules that wait for DOMContentLoaded (videoPlay, visualResultsGallery,
// sticky-cta) MUST stay in this static list: async chunks can land after the
// event has fired and their init would never run.

import './components/animated-scroll';
import './components/svg-sprite';

import './components/accordeon';
import './components/blogFilter';
import './components/contacts';
import './components/contacts-phone';
import './components/counters';
import './components/filters';
import './components/header';
import './components/intersectionObserver';
import './components/menu';

import './components/scrollTop';
import './components/services';
import './components/showMore';
import './components/tabs';
import './components/toc';
import './components/videoPlay';
import './components/mfs-video';
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
  // Reveal-on-enter animations — vanilla IntersectionObserver + CSS transitions,
  // no GSAP. Own tiny chunk; loads on every page that has reveals (i.e. almost
  // all of them) but without pulling in GSAP+ScrollTrigger.
  [
    '.js-reveal, .js-reveal-group, .js-highlight',
    () => import('./components/reveal')
  ],
  // Fancybox gallery page
  ['.js-gallery-tab-btn, .js-gallery-mobile, [data-fancybox]', () => import('./components/gallery')],
  // SimpleLightbox for picture-in-post links
  ['.js-pip a', () => import('./components/lightbox')]
];

lazyModules.forEach(([selector, load]) => {
  if (document.querySelector(selector)) load();
});

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