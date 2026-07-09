// Reviews block — thumbnail ↔ main sync on top of pure CSS scroll-snap (no Splide).
// The main slider is a plain `.mfs-snap` driven by the generic scroll-snap helper
// (arrows + dots + active slide). This module adds only the cross-slider link that
// Splide used to provide: the thumbnail strip previews the NEXT review, clicking a
// thumbnail (or its "Next review" button) advances the main slider, and the strip
// follows the main slide. Scroll-snap has no infinite loop, so the ends simply stop
// (fine for a swipe carousel; this block is slated for a redesign).
//
// Kept intentionally small — one IntersectionObserver reads the active main slide
// (no layout reads on scroll), everything else is a click → smooth scrollBy.

function scrollTrackTo(track, i) {
  const items = [...track.children];
  const clamped = Math.max(0, Math.min(items.length - 1, i));
  const target = items[clamped];
  if (!target) return;
  const delta = target.getBoundingClientRect().left - track.getBoundingClientRect().left;
  track.scrollBy({ left: delta, behavior: 'smooth' });
}

function initReviewsSync() {
  const main = document.querySelector('.js-reviews-slider');
  const thumbs = document.querySelector('.js-reviews-thumbnails-slider');
  if (!main || !thumbs) return;

  const mainTrack = main.querySelector('.mfs-snap__track');
  const thumbTrack = thumbs.querySelector('.mfs-snap__track');
  if (!mainTrack || !thumbTrack) return;

  const mainItems = [...mainTrack.children];
  if (mainItems.length < 2) return;

  let active = 0;

  // Active main slide via IntersectionObserver → preview the NEXT review in the strip.
  const ratios = new Array(mainItems.length).fill(0);
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((e) => {
        const i = mainItems.indexOf(e.target);
        if (i > -1) ratios[i] = e.isIntersecting ? e.intersectionRatio : 0;
      });
      let best = 0;
      for (let i = 1; i < ratios.length; i += 1) if (ratios[i] > ratios[best]) best = i;
      if (best !== active) {
        active = best;
        scrollTrackTo(thumbTrack, active + 1);
      }
    },
    { root: mainTrack, threshold: [0.25, 0.5, 0.6, 0.75, 1] }
  );
  mainItems.forEach((item) => io.observe(item));

  // Click a thumbnail → jump the main slider to that review.
  [...thumbTrack.children].forEach((thumb, i) => {
    thumb.addEventListener('click', () => scrollTrackTo(mainTrack, i));
  });

  // "Next review" button inside a thumbnail → advance one (also works on desktop,
  // where the thumbnail strip is the only visible navigation).
  thumbs.querySelectorAll('.reviews-item-thumb__arrow').forEach((btn) => {
    btn.addEventListener(
      'click',
      (e) => {
        e.preventDefault();
        e.stopPropagation();
        scrollTrackTo(mainTrack, active + 1);
      },
      true
    );
  });
}

initReviewsSync();