// Reveal-on-enter animations — vanilla IntersectionObserver, no GSAP.
// Replaces the former gsap.fromTo reveals: the observer only toggles a class,
// and all motion lives in CSS transitions (see common.scss .js-reveal). That
// removes the per-reveal getComputedStyle read GSAP did on enter and drops the
// whole gsap+ScrollTrigger chunk from every page that only had reveals.
//
// Behaviour parity with the old gsap.js:
//  - direction from [data-anim] (up/down/left/right/fade), default fade
//  - .js-reveal-group staggers its .js-reveal-item children in REVERSE order,
//    0.12s step (matched the old items.reverse() + stagger:0.12)
//  - .js-reveal-init animates immediately on load instead of waiting for IO
//  - .js-highlight adds .is-animated after 0.2s (CSS drives the sweep)

const STAGGER = 0.12;

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;

      const el = entry.target;

      if (el.classList.contains('js-reveal-group')) {
        const items = Array.from(el.querySelectorAll('.js-reveal-item'));
        const last = items.length - 1;
        items.forEach((item, index) => {
          item.style.transitionDelay = `${(last - index) * STAGGER}s`;
          item.classList.add('is-in');
        });
        observer.unobserve(el);
        return;
      }

      if (el.classList.contains('js-highlight')) {
        setTimeout(() => el.classList.add('is-animated'), 200);
        observer.unobserve(el);
        return;
      }

      el.classList.add('is-in');
      observer.unobserve(el);
    });
  },
  {
    threshold: 0.15,
    rootMargin: '0px 0px -10% 0px'
  }
);

document.querySelectorAll('.js-reveal').forEach((el) => {
  if (el.classList.contains('js-reveal-init')) {
    requestAnimationFrame(() => el.classList.add('is-in'));
  } else {
    observer.observe(el);
  }
});

document.querySelectorAll('.js-reveal-group').forEach((el) => observer.observe(el));

document.querySelectorAll('.js-highlight').forEach((el) => observer.observe(el));
