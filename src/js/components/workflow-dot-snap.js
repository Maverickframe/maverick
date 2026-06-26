// Snap each workflow step's dot (and its dashed connector) exactly onto the
// curved SVG path. The block positions dots with hand-tuned CSS offsets that only
// approximate the fixed curve, so on every page (yacht included) the dots sit a few
// px off the line. This measures the real path and pins each dot to the curve point
// at the dot's own vertical position, then redraws the connector to reach it.

function snapWorkflowDots() {
  document.querySelectorAll('.workflow').forEach((wf) => {
    const items = wf.querySelector('.workflow__items');
    if (!items) return;

    // The visible curve SVG (desktop / desktop-big depending on breakpoint).
    const svg = [...wf.querySelectorAll('.workflow__line svg')].find(
      (s) => s.getBoundingClientRect().width > 1
    );
    if (!svg || !svg.viewBox || !svg.viewBox.baseVal) return;
    const path = svg.querySelector('path');
    if (!path) return;

    const sr = svg.getBoundingClientRect();
    const vb = svg.viewBox.baseVal;
    const sx = sr.width / vb.width;
    const sy = sr.height / vb.height;

    // Sample the path into screen-space points.
    const total = path.getTotalLength();
    const pts = [];
    for (let l = 0; l <= total; l += 2) {
      const p = path.getPointAtLength(l);
      pts.push({ x: sr.left + (p.x - vb.x) * sx, y: sr.top + (p.y - vb.y) * sy });
    }

    // Minimum visible dashed run, so a dot landing right next to its text
    // (where the curve nearly touches the column) still reads as a connector.
    const MIN_CONN = 26;

    wf.querySelectorAll('.workflow-item').forEach((item) => {
      const dot = item.querySelector('.dot');
      const conn = item.querySelector('.connector');
      if (!dot || getComputedStyle(dot).display === 'none') return;

      const content = item.querySelector('.workflow-item__content');
      const ir = item.getBoundingClientRect();
      const cr = content ? content.getBoundingClientRect() : ir;
      const dotSize = dot.offsetWidth || 14;
      const dotCenterX = ir.left + dot.offsetLeft + dotSize / 2;
      const dotCenterY = ir.top + dot.offsetTop + dotSize / 2;

      // Curve crossings at the dot's row (the looping curve can cross a Y several times).
      let minYDist = Infinity;
      for (const p of pts) {
        const dy = Math.abs(p.y - dotCenterY);
        if (dy < minYDist) minYDist = dy;
      }
      const band = minYDist + 8;
      const crossings = pts.filter((p) => Math.abs(p.y - dotCenterY) <= band);
      if (!crossings.length) return;

      // Mobile: steps alternate sides (align-self flex-end/flex-start) and the curve
      // swings the full column width. Desktop: items keep align auto beside a gentle
      // curve — keep its proven behaviour untouched.
      const align = getComputedStyle(item).alignSelf;
      const mobile = align === 'flex-end' || align === 'flex-start';

      if (!mobile) {
        // --- Desktop (unchanged): nearest crossing + bridge to the content edge. ---
        let best = null;
        let bestDx = Infinity;
        for (const p of crossings) {
          const dx = Math.abs(p.x - dotCenterX);
          if (dx < bestDx) { bestDx = dx; best = p; }
        }
        const targetRel = best.x - ir.left;
        dot.style.left = `${targetRel - dotSize / 2}px`;
        dot.style.right = 'auto';
        if (conn) {
          conn.style.right = 'auto';
          if (best.x < cr.left) {
            conn.style.left = `${best.x - ir.left}px`;
            conn.style.width = `${cr.left - best.x}px`;
          } else if (best.x > cr.right) {
            conn.style.left = `${cr.right - ir.left}px`;
            conn.style.width = `${best.x - cr.right}px`;
          } else if (targetRel < 0) {
            conn.style.left = `${targetRel}px`;
            conn.style.width = `${-targetRel}px`;
          } else {
            conn.style.left = '0px';
            conn.style.width = `${targetRel}px`;
          }
        }
        return;
      }

      // --- Mobile ---
      const dotSide = align === 'flex-end' ? 'left' : 'right'; // side the dot lives on

      // Anchor the dash to the heading's own edge, not the content box. The box is
      // the full column width, so a short heading (e.g. "Strategic Planning") would
      // otherwise get only a stub of dash floating next to the dot. The h3 is
      // align-self:flex-start, so its rect is tight to the visible text.
      const heading = item.querySelector('h3');
      const hr = heading ? heading.getBoundingClientRect() : cr;
      const textEdge = dotSide === 'left' ? hr.left : hr.right;

      // Pick the crossing on the text's outward side so the connector points away
      // from the text (never behind it); fall back to the extreme crossing otherwise.
      let best = null;
      if (dotSide === 'left') {
        for (const p of crossings) if (p.x <= cr.left && (!best || p.x > best.x)) best = p;
        if (!best) best = crossings.reduce((a, b) => (b.x < a.x ? b : a));
      } else {
        for (const p of crossings) if (p.x >= cr.right && (!best || p.x < best.x)) best = p;
        if (!best) best = crossings.reduce((a, b) => (b.x > a.x ? b : a));
      }

      // Default nudges: push the dot a few px further onto the curve (the inner
      // crossing lands a hair short of the stroke centre) and let the dash run a
      // touch past the dot. Per-step overrides (keyed by heading text) fine-tune
      // the Solutions page; other pages fall through to the defaults.
      //   overshoot — px the dot moves further out onto the curve
      //   stub      — px the dash runs past the dot, so the dot sits ON the line
      //   trim      — px shaved off the dash on the heading side (dot unchanged)
      const htext = (heading ? heading.textContent : '').trim().toLowerCase();
      let overshoot = 5;
      let stub = 6;
      let trim = 0;
      if (htext.includes('integrated production')) {
        overshoot = 12;
      } else if (htext.includes('delivery')) {
        overshoot = 5; stub = 0; trim = 34;
      } else if (htext.includes('ongoing support')) {
        overshoot = 0; stub = 0;
      }

      let dotX = best.x;
      if (dotSide === 'left') dotX = Math.min(dotX, cr.left - MIN_CONN) - overshoot;
      else dotX = Math.max(dotX, cr.right + MIN_CONN) + overshoot;

      dot.style.left = `${dotX - ir.left - dotSize / 2}px`;
      dot.style.right = 'auto';

      if (conn) {
        conn.style.right = 'auto';
        if (dotSide === 'left') {
          // Dash from a stub left of the dot up to the heading's left edge.
          const start = dotX - stub;
          conn.style.left = `${start - ir.left}px`;
          conn.style.width = `${(textEdge - trim) - start}px`;
        } else {
          // Dash from the heading's right edge to a stub past the dot.
          const start = textEdge + trim;
          conn.style.left = `${start - ir.left}px`;
          conn.style.width = `${(dotX + stub) - start}px`;
        }
      }
    });
  });
}

let snapRaf;
function scheduleSnap() {
  cancelAnimationFrame(snapRaf);
  snapRaf = requestAnimationFrame(snapWorkflowDots);
}

if (document.querySelector('.workflow')) {
  window.addEventListener('load', scheduleSnap);
  window.addEventListener('resize', () => {
    clearTimeout(window.__wfSnapT);
    window.__wfSnapT = setTimeout(scheduleSnap, 150);
  });
  // Re-run shortly after load in case web fonts shift the layout.
  setTimeout(scheduleSnap, 600);
}
