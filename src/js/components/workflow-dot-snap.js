// Snap each workflow step's dot (and its dashed connector) exactly onto the
// curved SVG path. The block positions dots with hand-tuned CSS offsets that only
// approximate the fixed curve, so dots sit off the line on most pages/viewports.
//
// v2 (rewritten): instead of "nearest sampled point in a ±band" (which drifted up
// to ~10px on steep curve sections) we compute the EXACT intersections of the
// curve with the dot's row by linear interpolation between path samples, and put
// the dot dead on the chosen crossing. The dashed connector is then drawn
// geometrically from the heading's edge to the dot — no per-page overrides, no
// minimum-length hacks that used to push dots off the line.
//
// Re-snap triggers cover late layout shifts (lazy media above the block, web
// fonts, GSAP reveals): load, timeouts, fonts.ready, IntersectionObserver on
// every entry, ResizeObserver on the block, window resize.

function snapWorkflowDots() {
  document.querySelectorAll('.workflow').forEach((wf) => {
    // The visible curve SVG (mobile / desktop / desktop-big depending on breakpoint).
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
    if (pts.length < 2) return;

    // Exact x positions where the curve crosses a horizontal row y.
    // Fallback: if the curve never crosses that row (e.g. it ends just above),
    // use its closest-approach point when it is within 60px vertically.
    const candidatesAt = (y) => {
      const xs = [];
      for (let i = 1; i < pts.length; i++) {
        const a = pts[i - 1];
        const b = pts[i];
        if ((a.y - y) * (b.y - y) <= 0 && a.y !== b.y) {
          const t = (y - a.y) / (b.y - a.y);
          xs.push(a.x + (b.x - a.x) * t);
        }
      }
      if (!xs.length) {
        let best = null;
        for (const p of pts) {
          if (!best || Math.abs(p.y - y) < Math.abs(best.y - y)) best = p;
        }
        if (best && Math.abs(best.y - y) < 60) xs.push(best.x);
      }
      return xs;
    };

    wf.querySelectorAll('.workflow-item').forEach((item) => {
      const dot = item.querySelector('.dot');
      const conn = item.querySelector('.connector');
      if (!dot || getComputedStyle(dot).display === 'none') return;

      const content = item.querySelector('.workflow-item__content');
      const heading = item.querySelector('h3');
      const ir = item.getBoundingClientRect();
      // Anchor the dash to the heading's own edge (tight to the visible text);
      // the content box is the full column width.
      const hr = (heading || content || item).getBoundingClientRect();
      const dotSize = dot.offsetWidth || 14;

      // Reset previous inline positioning so the CSS offsets are re-measured
      // cleanly on re-snaps (resize, layout shifts).
      dot.style.left = '';
      dot.style.right = '';
      dot.style.top = '';
      if (conn) {
        conn.style.left = '';
        conn.style.right = '';
        conn.style.top = '';
        conn.style.width = '';
        conn.style.display = '';
      }

      const dotCY = ir.top + dot.offsetTop + dotSize / 2;
      const cssDotCX = ir.left + dot.offsetLeft + dotSize / 2;

      // Mobile: steps alternate sides (align-self flex-end/flex-start). Snap
      // the dot to the innermost crossing on the step's OWN side of the
      // heading (at the heading row, matching the original design), so the
      // dash always points outward from the text.
      const align = getComputedStyle(item).alignSelf;
      const mobileSide =
        align === 'flex-end' ? 'left' : align === 'flex-start' ? 'right' : null;

      const xs = candidatesAt(dotCY);
      if (!xs.length) return;

      let dotX;
      if (mobileSide) {
        const edge = mobileSide === 'left' ? hr.left : hr.right;
        const outward = xs.filter((x) =>
          mobileSide === 'left' ? x <= edge - 2 : x >= edge + 2
        );
        dotX = outward.length
          ? (mobileSide === 'left' ? Math.max(...outward) : Math.min(...outward))
          : xs.reduce((a, b) => (Math.abs(b - cssDotCX) < Math.abs(a - cssDotCX) ? b : a));
      } else {
        // Desktop: the crossing nearest to where the design's CSS put the dot —
        // keeps the intended branch when the looping curve crosses the row
        // several times.
        dotX = xs.reduce(
          (a, b) => (Math.abs(b - cssDotCX) < Math.abs(a - cssDotCX) ? b : a)
        );
      }

      dot.style.left = `${dotX - ir.left - dotSize / 2}px`;
      dot.style.right = 'auto';

      if (conn) {
        conn.style.right = 'auto';
        const GAP = 8; // breathing room next to the text
        let start = 0;
        let width = 0;
        if (dotX > hr.right + 2) {
          // dot to the right of the heading: dash from text edge to the dot
          start = hr.right + GAP;
          width = dotX - dotSize / 2 - start;
        } else if (dotX < hr.left - 2) {
          // dot to the left: dash from the dot to the text edge
          start = dotX + dotSize / 2;
          width = hr.left - GAP - start;
        }
        if (width >= 2) {
          conn.style.display = '';
          conn.style.left = `${start - ir.left}px`;
          conn.style.width = `${width}px`;
        } else {
          // dot sits within the heading's horizontal span — no room for a dash
          conn.style.display = 'none';
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

const workflows = document.querySelectorAll('.workflow');
if (workflows.length) {
  window.addEventListener('load', scheduleSnap);
  window.addEventListener('resize', () => {
    clearTimeout(window.__wfSnapT);
    window.__wfSnapT = setTimeout(scheduleSnap, 150);
  });

  // Late layout shifts: fonts, lazy media above the block, GSAP reveals.
  scheduleSnap();
  setTimeout(scheduleSnap, 600);
  setTimeout(scheduleSnap, 1600);
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(scheduleSnap);
  }

  // Re-snap every time a workflow block (re)enters the viewport — by then all
  // layout above it has settled, so dots are correct right before they're seen.
  const io = new IntersectionObserver((entries) => {
    if (entries.some((e) => e.isIntersecting)) scheduleSnap();
  }, { rootMargin: '200px 0px' });
  workflows.forEach((wf) => io.observe(wf));

  // Container size changes (fluid layout, orientation) — cheap and debounced.
  if (typeof ResizeObserver !== 'undefined') {
    let roT;
    const ro = new ResizeObserver(() => {
      clearTimeout(roT);
      roT = setTimeout(scheduleSnap, 100);
    });
    workflows.forEach((wf) => ro.observe(wf));
  }
}
