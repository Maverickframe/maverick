// Canonical CSS scroll-snap carousel controller (ведро A — replaces the perPage Splide swipers).
// One helper drives every `.mfs-snap` on the page with zero carousel library. The active slide is
// read from an IntersectionObserver keyed to the track, so nothing measures layout during scroll —
// that removes the forced-reflow this whole migration targets. Layout is read ONLY on an arrow/dot
// click (one getBoundingClientRect delta) and mandatory CSS snap finishes the alignment, so peek
// padding and gaps need no math here. Every piece degrades gracefully: a block may ship arrows,
// dots, both, or neither.
//
// Markup contract:
//   .mfs-snap                          carousel root
//     .mfs-snap__track  (scroller)  →  .mfs-snap__item ...        one node per slide
//     .mfs-snap__arrows (optional)  →  .mfs-snap__arrow--prev / --next
//     .mfs-snap__dots   (optional)     empty container — dots are generated inside it
//
//   data-mfs-snap-step="N"   slides advanced per arrow click (default 1; multi-up blocks set perPage)

function initSnap(root) {
  const track = root.querySelector('.mfs-snap__track');
  if (!track) return;

  const items = [...track.children].filter((el) => el.classList.contains('mfs-snap__item'));
  if (items.length < 2) return; // single slide — no controls needed

  const prev = root.querySelector('.mfs-snap__arrow--prev');
  const next = root.querySelector('.mfs-snap__arrow--next');
  const dotsBox = root.querySelector('.mfs-snap__dots');
  const step = Math.max(1, parseInt(root.dataset.mfsSnapStep, 10) || 1);

  let active = 0;

  // Move item i to the track's start. getBoundingClientRect (a layout read) runs on click only;
  // mandatory scroll-snap then corrects the final resting position, so gaps/peek need no offsets.
  function scrollToIndex(i) {
    const clamped = Math.max(0, Math.min(items.length - 1, i));
    const delta = items[clamped].getBoundingClientRect().left - track.getBoundingClientRect().left;
    track.scrollBy({ left: delta, behavior: 'smooth' });
  }

  // --- dots ------------------------------------------------------------------
  let dots = [];
  if (dotsBox) {
    dotsBox.innerHTML = '';
    dots = items.map((item, i) => {
      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'mfs-snap__dot';
      dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
      if (i === 0) dot.setAttribute('aria-current', 'true');
      dot.addEventListener('click', () => scrollToIndex(i));
      dotsBox.appendChild(dot);
      return dot;
    });
  }

  // --- arrows ----------------------------------------------------------------
  if (prev) {
    prev.addEventListener('click', () => scrollToIndex(active - step));
    prev.disabled = true;
  }
  if (next) {
    next.addEventListener('click', () => scrollToIndex(active + step));
    next.disabled = false;
  }

  function setActive(i) {
    if (i === active) return;
    active = i;
    dots.forEach((dot, di) => {
      if (di === i) dot.setAttribute('aria-current', 'true');
      else dot.removeAttribute('aria-current');
    });
    if (prev) prev.disabled = i <= 0;
    if (next) next.disabled = i >= items.length - 1;
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
  items.forEach((item) => io.observe(item));
}

document.querySelectorAll('.mfs-snap').forEach(initSnap);
