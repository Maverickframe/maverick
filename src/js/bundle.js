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
import './components/workflow-dot-snap';
import './components/visualResultsGallery';
import './components/select';

import './components/collapse';
import './components/showMoreText';
import './components/showSidebar';
import './components/sticky-cta';
import './components/modal-offer';
import './components/quiz';
import './components/calculator';

// --- Heavy modules: split into async chunks, loaded per feature-detect --------
// The old single bundle evaluated Splide, GSAP+ScrollTrigger, THREE.js, Fancybox
// and SimpleLightbox on EVERY page (~2.1s script eval, 144 KB unused on the
// front page per PSI). Each module below already no-ops without its markup, so
// gating the import on the same selector only skips downloading/evaluating the
// library — behavior is unchanged when the markup exists. These modules query
// the DOM at top level (no DOMContentLoaded dependency), so late chunk arrival
// is safe. Vite turns each import() into a hashed chunk next to main-*.js;
// base "./" keeps chunk URLs relative to /build/assets/, and shared deps
// (Splide in sliders+modals, uiManager in menu+modals) are deduped into shared
// chunks automatically.
const lazyModules = [
  // Splide sliders (incl. hero marquee)
  ['.splide', () => import('./components/sliders')],
  // Modals + their inner Splide instances (book-a-call is site-wide)
  ['.modal, .js-modal-open', () => import('./components/modals')],
  // GSAP + ScrollTrigger animations
  [
    '.js-reveal, .js-reveal-group, .js-highlight, .js-quote, .js-workflow-item, .path-anim, .js-video-anim',
    () => import('./components/gsap'),
  ],
  // Fancybox gallery page
  ['.js-gallery-tab-btn, .js-gallery-mobile, [data-fancybox]', () => import('./components/gallery')],
  // SimpleLightbox for picture-in-post links
  ['.js-pip a', () => import('./components/lightbox')],
];

lazyModules.forEach(([selector, load]) => {
  if (document.querySelector(selector)) load();
});

// THREE.js particles (551 KB chunk) — decorative background in the
// worldwide-rendering section, ~5500px below the fold on the front page.
// Loaded only when the wrapper approaches the viewport, so THREE never
// downloads/evaluates during initial load (or in a Lighthouse trace at all).
const particlesWrapper = document.querySelector('.js-particles-wrapper');
if (particlesWrapper) {
  const io = new IntersectionObserver(
    (entries, observer) => {
      if (entries.some((e) => e.isIntersecting)) {
        observer.disconnect();
        import('./components/particlesAnimation');
      }
    },
    { rootMargin: '600px 0px' }
  );
  io.observe(particlesWrapper);
}
