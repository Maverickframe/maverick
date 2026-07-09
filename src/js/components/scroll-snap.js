// Canonical CSS scroll-snap carousel controller (ведро A — replaces the perPage Splide swipers).
// One helper drives every `.mfs-snap` on the page with zero carousel library. The active slide is
// read from an IntersectionObserver keyed to the track, so nothing measures layout during scroll —
// that removes the forced-reflow this whole migration targets. Layout is read ONLY on an arrow/dot
// click (one getBoundingClientRect delta) and, for multi-up blocks, on init/resize (perView probe).
// Every piece degrades gracefully: a block may ship arrows, dots, both, or neither.
//
// Markup contract:
//   .mfs-snap                          carousel root
//     .mfs-snap__track  (scroller)  →  .mfs-snap__item ...        one node per slide
//     .mfs-snap__arrows (optional)  →  .mfs-snap__arrow--prev / --next
//     .mfs-snap__dots   (optional)     empty container — dots are generated inside it
//
//   data-mfs-snap-step="N"    slides advanced per arrow click (single-up only; default 1)
//   data-mfs-snap-multiup     opt-in: dots + arrows become PAGE-based. A page = perView slides,
//                             perView is measured from layout (responsive 4→3→1) on init + resize,
//                             never on scroll. dots = ceil(items / perView), active page =
//                             floor(activeItem / perView). With perView 1 this is identical to the
//                             single-up path, so existing consumers stay byte-for-byte unchanged.

// Exported so dynamically-injected markup (e.g. the What-We-Do modal, whose slides
// are built in JS after page load) can initialise a carousel on demand — the
// bottom auto-run only sees `.mfs-snap` present at chunk-eval time.
// eslint-disable-next-line import/prefer-default-export
export function initSnap(root) {
  const track = root.querySelector('.mfs-snap__track');
  if (!track) return;

  const items = [...track.children].filter((el) => el.classList.contains('mfs-snap__item'));
  if (items.length < 2) return; // single slide — no controls needed

  const prev = root.querySelector('.mfs-snap__arrow--prev');
  const next = root.querySelector('.mfs-snap__arrow--next');
  const dotsBox = root.querySelector('.mfs-snap__dots');

  const multiUp = root.hasAttribute('data-mfs-snap-multiup');
  const stepAttr = Math.max(1, parseInt(root.dataset.mfsSnapStep, 10) || 1);

  let perView = 1; // how many slides share a page (multi-up); always 1 for single-up blocks
  let active = 0; // active ITEM index (from the IntersectionObserver)
  let dots = [];

  const pageOf = (i) => Math.floor(i / perView);
  const pageCount = () => Math.ceil(items.length / perView);

  // Count whole items that fit across the track's CONTENT box. Layout read — runs on init + resize
  // only, never during scroll. Uses the offsetLeft stride (width + gap) and subtracts the inline
  // peek padding from clientWidth, otherwise the gutters inflate the count (e.g. a 3-up page would
  // round up to 4). Result: round(contentBox / stride) === perView at every breakpoint.
  function measurePerView() {
    if (!multiUp) return 1;
    const stride = items.length > 1 ? items[1].offsetLeft - items[0].offsetLeft : items[0].offsetWidth;
    if (stride <= 0) return 1;
    const cs = getComputedStyle(track);
    const contentBox = track.clientWidth - parseFloat(cs.paddingLeft) - parseFloat(cs.paddingRight);
    return Math.max(1, Math.round(contentBox / stride));
  }

  // Move item i to the track's start. getBoundingClientRect (a layout read) runs on click only;
  // mandatory scroll-snap then corrects the final resting position, so gaps/peek need no offsets.
  function scrollToIndex(i) {
    const clamped = Math.max(0, Math.min(items.length - 1, i));
    const delta = items[clamped].getBoundingClientRect().left - track.getBoundingClientRect().left;
    track.scrollBy({ left: delta, behavior: 'smooth' });
  }

  // --- dots (page-based; a single-up page holds exactly one slide) --------------
  function buildDots() {
    if (!dotsBox) return;
    const count = pageCount();
    const currentPage = pageOf(active);
    dotsBox.innerHTML = '';
    dots = Array.from({ length: count }, (unused, p) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'mfs-snap__dot';
      dot.setAttribute('aria-label', `Go to slide ${p * perView + 1}`);
      if (p === currentPage) dot.setAttribute('aria-current', 'true');
      dot.addEventListener('click', () => scrollToIndex(p * perView));
      dotsBox.appendChild(dot);
      return dot;
    });
  }

  function syncControls() {
    const page = pageOf(active);
    dots.forEach((dot, p) => {
      if (p === page) dot.setAttribute('aria-current', 'true');
      else dot.removeAttribute('aria-current');
    });
    if (prev) prev.disabled = page <= 0;
    if (next) next.disabled = page >= pageCount() - 1;
  }

  // --- arrows ------------------------------------------------------------------
  const arrowTarget = (dir) => (multiUp ? (pageOf(active) + dir) * perView : active + dir * stepAttr);
  if (prev) prev.addEventListener('click', () => scrollToIndex(arrowTarget(-1)));
  if (next) next.addEventListener('click', () => scrollToIndex(arrowTarget(1)));

  function setActive(i) {
    if (i === active) return;
    active = i;
    syncControls();
  }

  // --- active slide via IntersectionObserver (no scroll-time layout reads) ----
  const ratios = new Array(items.length).fill(0);
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        const i = items.indexOf(e.target);
        if (i > -1) ratios[i] = e.isIntersecting ? e.intersectionRatio : 0;
      });
      let best = 0;
      for (let i = 1; i < ratios.length; i += 1) if (ratios[i] > ratios[best]) best = i;
      setActive(best);
    },
    { root: track, threshold: [0.25, 0.5, 0.6, 0.75, 1] }
  );

  // --- init + responsive perView (multi-up) -----------------------------------
  perView = measurePerView();
  buildDots();
  syncControls();
  items.forEach((item) => io.observe(item));

  if (multiUp && 'ResizeObserver' in window) {
    const ro = new ResizeObserver(() => {
      const pv = measurePerView();
      if (pv !== perView) {
        perView = pv;
        buildDots();
        syncControls();
      }
    });
    ro.observe(track);
  }
}

document.querySelectorAll('.mfs-snap').forEach(initSnap);