/**
 * imageWheel — draggable rotating ring of design-work cards for the
 * worldwide-rendering block on design service pages. Pure CSS 3D + vanilla JS,
 * zero dependencies (Framer "ImageWheel" vibe).
 *
 * Activation is complementary to renderReveal: that component intentionally
 * bails when the block's ACF image is an .svg (the legacy dotted globe), and
 * THIS one only activates on that very .svg — design pages get the wheel,
 * CGI pages keep the tear-to-reveal render. No content edits needed.
 *
 * Behaviour:
 *  - the ring spins slowly on its own (tilted disc, cards keep orientation);
 *  - drag to spin with inertia (touch keeps vertical page scroll: pan-y);
 *  - page scroll gives the wheel a proportional speed boost, then it eases
 *    back to the idle drift;
 *  - rAF only while visible & tab active; prefers-reduced-motion = static.
 */

// Brand-palette tiles with icons that illustrate the block's copy: worldwide,
// multilingual, remote, 24/7. Variants cycle: accent / light / outline.
const I = (paths) =>
  `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">${paths}</svg>`;

const CARDS = [
  // globe — worldwide
  { v: 'accent', icon: I('<circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a13.5 13.5 0 0 1 0 18a13.5 13.5 0 0 1 0-18z"/>') },
  // chat bubbles — multilingual
  { v: 'light', icon: I('<path d="M4 5h9a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2H8l-4 3V7a2 2 0 0 1 2-2z" transform="translate(-1 -1)"/><path d="M20 10v5a2 2 0 0 1-2 2h-1v3l-3-3h-3"/>') },
  // clock — 24/7
  { v: 'outline', icon: I('<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>') },
  // paper plane — remote handoff
  { v: 'light', icon: I('<path d="M21 3L10 14"/><path d="M21 3l-7 18-4-7-7-4z"/>') },
  // monitor — web design
  { v: 'accent', icon: I('<rect x="3" y="4" width="18" height="12" rx="1.5"/><path d="M3 8h18"/><path d="M9 20h6"/><path d="M12 16v4"/>') },
  // map pin — any market
  { v: 'outline', icon: I('<path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>') },
  // smartphone — mobile-first
  { v: 'light', icon: I('<rect x="7" y="2.5" width="10" height="19" rx="2"/><path d="M11 18.5h2"/>') },
  // cursor — design tools
  { v: 'accent', icon: I('<path d="M5 3l7 17 2.5-6.5L21 11z"/>') },
];

function initWheel(wrap) {
  const src = wrap.dataset.img;
  if (!src || !/\.svg(\?|#|$)/i.test(src)) return; // raster → renderReveal territory
  if (wrap.classList.contains('is-wheel')) return;

  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  const scene = document.createElement('div');
  scene.className = 'mfs-wheel';
  const tilt = document.createElement('div');
  tilt.className = 'mfs-wheel__tilt';
  scene.appendChild(tilt);

  const n = CARDS.length;
  const step = 360 / n;
  const cards = CARDS.map((def) => {
    const card = document.createElement('div');
    card.className = `mfs-wheel__card mfs-wheel__card--${def.v}`;
    card.innerHTML = def.icon;
    tilt.appendChild(card);
    return card;
  });

  wrap.appendChild(scene);
  wrap.classList.add('is-active', 'is-wheel');

  const BASE_VEL = 0.045; // deg per frame — idle drift
  let angle = 0;
  let vel = BASE_VEL;
  let Rx = 220; // ellipse radii: wide and flat, Framer-style
  let Ry = 92;
  let raf = 0;
  let visible = false;

  function resize() {
    const r = scene.getBoundingClientRect();
    if (!r.width) return;
    Rx = r.width * 0.33;
    Ry = Rx * 0.42;
    layout();
  }

  // Framer's ImageWheel look: cards stay UNSKEWED rounded rects (no 3D plane),
  // travelling along a flat ellipse, scaled by depth and stacked near-over-far.
  function layout() {
    for (let i = 0; i < n; i++) {
      const rad = ((angle + i * step) * Math.PI) / 180;
      const x = Rx * Math.sin(rad);
      const y = -Ry * Math.cos(rad);
      const depth = (1 - Math.cos(rad)) / 2; // 0 = far (top), 1 = near (bottom)
      const s = 0.55 + 0.45 * depth;
      cards[i].style.transform =
        `translate(-50%, -50%) translate(${x}px, ${y}px) scale(${s})`;
      cards[i].style.zIndex = String(100 + Math.round(depth * 100));
      // depth of field, as in the Framer demo: far cards soft, near cards sharp
      const blur = (1 - depth) * 2.5;
      cards[i].style.filter = blur > 0.3 ? `blur(${blur.toFixed(1)}px)` : 'none';
    }
  }

  function frame() {
    raf = visible ? requestAnimationFrame(frame) : 0;
    angle = (angle + vel) % 360;
    // ease back to the idle drift after drags / scroll boosts
    vel += (BASE_VEL - vel) * 0.03;
    layout();
  }

  // --- drag to spin (inertia) ---
  let dragging = false;
  let lastX = 0;
  let lastDx = 0;
  scene.addEventListener('pointerdown', (e) => {
    dragging = true;
    lastX = e.clientX;
    lastDx = 0;
    scene.classList.add('is-dragging');
    scene.setPointerCapture(e.pointerId);
  });
  scene.addEventListener('pointermove', (e) => {
    if (!dragging) return;
    const dx = e.clientX - lastX;
    lastX = e.clientX;
    lastDx = dx;
    angle += dx * 0.25;
    if (reduced) layout();
  });
  const endDrag = () => {
    if (!dragging) return;
    dragging = false;
    scene.classList.remove('is-dragging');
    vel = Math.max(-3, Math.min(3, lastDx * 0.18)) || BASE_VEL; // fling inertia
  };
  scene.addEventListener('pointerup', endDrag);
  scene.addEventListener('pointercancel', endDrag);

  // --- page scroll boosts the spin ---
  if (!reduced) {
    let lastScroll = window.scrollY;
    window.addEventListener('scroll', () => {
      const d = window.scrollY - lastScroll;
      lastScroll = window.scrollY;
      if (!visible) return;
      vel += Math.max(-1.4, Math.min(1.4, d * 0.006));
      vel = Math.max(-3, Math.min(3, vel));
    }, { passive: true });
  }

  let resizeT;
  new ResizeObserver(() => {
    clearTimeout(resizeT);
    resizeT = setTimeout(resize, 150);
  }).observe(wrap);

  if (!reduced) {
    new IntersectionObserver((entries) => {
      visible = entries.some((e) => e.isIntersecting) && !document.hidden;
      if (visible && !raf) raf = requestAnimationFrame(frame);
    }).observe(wrap);

    document.addEventListener('visibilitychange', () => {
      visible = !document.hidden;
      if (visible && !raf) raf = requestAnimationFrame(frame);
    });
  }

  resize();
}

document.querySelectorAll('.js-render-reveal').forEach(initWheel);
