/**
 * renderReveal — "tear the blueprint, see the render" interactive.
 *
 * Replaces the three.js particle sphere (551 KB chunk) in the
 * worldwide-rendering block. Zero dependencies, canvas 2D only.
 *
 * How it works:
 *  - takes the block's raster image (data-img), builds a "blueprint" version
 *    of it on the fly (Sobel edge detection → accent-blue linework on paper,
 *    or a grayscale "clay" fallback if pixels aren't readable cross-origin);
 *  - moving the pointer tears the blueprint like tracing paper: a ragged-edge
 *    hole with a white torn border and soft shadow shows the final render
 *    underneath; the tear slowly "heals" back to blueprint;
 *  - a ghost cursor auto-plays a tear when the block first enters the
 *    viewport and repeats while idle, so the interaction is discoverable
 *    and the block lives on touch devices too.
 *
 * Legacy safety: if data-img is an .svg (old particle-sphere value), the
 * component bails out and the static <img> stays as-is.
 */

const ACCENT = '#2d40ae';
const ACCENT_RGB = [45, 64, 174];

function coverRect(iw, ih, cw, ch) {
    const s = Math.max(cw / iw, ch / ih);
    return [(cw - iw * s) / 2, (ch - ih * s) / 2, iw * s, ih * s];
}

function buildBlueprint(img, w, h) {
    const c = document.createElement('canvas');
    c.width = w;
    c.height = h;
    const ctx = c.getContext('2d');
    const [cx, cy, cw, ch] = coverRect(img.naturalWidth, img.naturalHeight, w, h);

    // paper + grid
    ctx.fillStyle = '#f5f7fb';
    ctx.fillRect(0, 0, w, h);
    ctx.strokeStyle = `rgba(${ACCENT_RGB.join(',')},0.07)`;
    ctx.lineWidth = 1;
    const step = Math.max(28, Math.round(w / 14));
    for (let x = step; x < w; x += step) {
        ctx.beginPath(); ctx.moveTo(x + 0.5, 0); ctx.lineTo(x + 0.5, h); ctx.stroke();
    }
    for (let y = step; y < h; y += step) {
        ctx.beginPath(); ctx.moveTo(0, y + 0.5); ctx.lineTo(w, y + 0.5); ctx.stroke();
    }

    try {
        // Sobel edges at reduced resolution, upscaled with smoothing.
        const dw = Math.min(440, w);
        const dh = Math.round((dw * h) / w);
        const down = document.createElement('canvas');
        down.width = dw;
        down.height = dh;
        const dctx = down.getContext('2d', { willReadFrequently: true });
        const k = dw / w;
        dctx.drawImage(img, cx * k, cy * k, cw * k, ch * k);

        const src = dctx.getImageData(0, 0, dw, dh).data;
        const lum = new Float32Array(dw * dh);
        for (let i = 0; i < dw * dh; i++) {
            lum[i] = src[i * 4] * 0.299 + src[i * 4 + 1] * 0.587 + src[i * 4 + 2] * 0.114;
        }

        const edge = dctx.createImageData(dw, dh);
        const e = edge.data;
        for (let y = 1; y < dh - 1; y++) {
            for (let x = 1; x < dw - 1; x++) {
                const i = y * dw + x;
                const gx =
                    -lum[i - dw - 1] - 2 * lum[i - 1] - lum[i + dw - 1] +
                    lum[i - dw + 1] + 2 * lum[i + 1] + lum[i + dw + 1];
                const gy =
                    -lum[i - dw - 1] - 2 * lum[i - dw] - lum[i - dw + 1] +
                    lum[i + dw - 1] + 2 * lum[i + dw] + lum[i + dw + 1];
                const mag = Math.sqrt(gx * gx + gy * gy);
                const a = Math.max(0, Math.min(235, mag * 1.1 - 30));
                const p = i * 4;
                e[p] = ACCENT_RGB[0];
                e[p + 1] = ACCENT_RGB[1];
                e[p + 2] = ACCENT_RGB[2];
                e[p + 3] = a;
            }
        }
        dctx.clearRect(0, 0, dw, dh);
        dctx.putImageData(edge, 0, 0);
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';
        ctx.drawImage(down, 0, 0, w, h);
    } catch (err) {
        // Tainted canvas (cross-origin image without CORS) — clay fallback.
        ctx.filter = 'grayscale(1) brightness(1.12) contrast(0.95)';
        ctx.drawImage(img, cx, cy, cw, ch);
        ctx.filter = 'none';
        ctx.fillStyle = `rgba(${ACCENT_RGB.join(',')},0.08)`;
        ctx.fillRect(0, 0, w, h);
    }
    return c;
}

function initReveal(wrap) {
    const src = wrap.dataset.img;
    if (!src || /\.svg(\?|#|$)/i.test(src)) return; // legacy sphere value
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        wrap.classList.add('is-reveal-static');
        return;
    }

    const img = new Image();
    try {
        if (new URL(src, location.href).origin !== location.origin) {
            img.crossOrigin = 'anonymous';
        }
    } catch (e) { /* relative url — same origin */ }

    let started = false;
    img.onload = () => start();
    img.onerror = () => {}; // keep static <img>
    img.src = src;

    function start() {
        if (started) return;
        started = true;

        const canvas = document.createElement('canvas');
        canvas.className = 'worldwide-rendering__canvas';
        wrap.appendChild(canvas);
        wrap.classList.add('is-active', 'is-reveal');
        const ctx = canvas.getContext('2d');

        let raf = 0;
        let visible = false;
        let W = 0, H = 0, dpr = 1;
        let blueprint, fin, mask, mctx, edgeC, ectx, comp, cctx;
        const MS = 0.5; // mask scale (low-res = rougher, cheaper edges)

        const pointer = { x: -1e4, y: -1e4, px: -1e4, py: -1e4, lastMove: 0 };

        // ghost auto-tear
        let ghost = null;        // { t0, dur, p0, p1, p2 }
        let ghostPos = null;
        let firstSeen = 0;
        let lastGhost = 0;

        function resize() {
            const r = wrap.getBoundingClientRect();
            if (!r.width || !r.height) return;
            dpr = Math.min(window.devicePixelRatio || 1, 1.75);
            W = Math.round(r.width);
            H = Math.round(r.height);
            canvas.width = W * dpr;
            canvas.height = H * dpr;
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

            blueprint = buildBlueprint(img, W, H);

            fin = document.createElement('canvas');
            fin.width = W;
            fin.height = H;
            fin.getContext('2d').drawImage(
                img, ...coverRect(img.naturalWidth, img.naturalHeight, W, H)
            );

            mask = document.createElement('canvas');
            mask.width = Math.round(W * MS);
            mask.height = Math.round(H * MS);
            mctx = mask.getContext('2d');

            edgeC = document.createElement('canvas');
            edgeC.width = mask.width;
            edgeC.height = mask.height;
            ectx = edgeC.getContext('2d');

            comp = document.createElement('canvas');
            comp.width = W;
            comp.height = H;
            cctx = comp.getContext('2d');

            // Paint the blueprint synchronously so the canvas is never blank
            // even while rAF is throttled (occluded window, background tab).
            ctx.clearRect(0, 0, W, H);
            ctx.drawImage(blueprint, 0, 0, W, H);
        }

        // ragged polygon "tear" — hard edge, jittered vertices
        function paintTear(x, y, r) {
            const n = 11;
            mctx.fillStyle = 'rgba(0,0,0,1)';
            mctx.beginPath();
            for (let i = 0; i <= n; i++) {
                const a = (i / n) * Math.PI * 2;
                const rr = r * (0.7 + Math.random() * 0.55) * MS;
                const px = x * MS + Math.cos(a) * rr;
                const py = y * MS + Math.sin(a) * rr;
                if (i === 0) mctx.moveTo(px, py);
                else mctx.lineTo(px, py);
            }
            mctx.closePath();
            mctx.fill();
        }

        function startGhost(ts) {
            const rnd = (a, b) => a + Math.random() * (b - a);
            const ltr = Math.random() > 0.5;
            ghost = {
                t0: ts,
                dur: 1700,
                p0: { x: W * (ltr ? 0.14 : 0.86), y: H * rnd(0.22, 0.45) },
                p1: { x: W * 0.5, y: H * rnd(0.3, 0.7) },
                p2: { x: W * (ltr ? 0.86 : 0.14), y: H * rnd(0.55, 0.8) },
            };
            lastGhost = ts;
        }

        function frame(ts) {
            raf = visible ? requestAnimationFrame(frame) : 0;
            if (!W) return;

            // the tear slowly "heals"
            mctx.globalCompositeOperation = 'destination-out';
            mctx.fillStyle = 'rgba(0,0,0,0.014)';
            mctx.fillRect(0, 0, mask.width, mask.height);
            mctx.globalCompositeOperation = 'source-over';

            const brushR = Math.max(W, H) * 0.085;

            // pointer tears; interpolate between events so fast moves stay solid
            if (ts - pointer.lastMove < 120 && pointer.x > -1e3) {
                const steps = 4;
                for (let i = 1; i <= steps; i++) {
                    const t = i / steps;
                    paintTear(
                        pointer.px + (pointer.x - pointer.px) * t,
                        pointer.py + (pointer.y - pointer.py) * t,
                        brushR
                    );
                }
                pointer.px = pointer.x;
                pointer.py = pointer.y;
            }

            // ghost auto-tear: once on first view, then again while idle
            const idleFor = ts - Math.max(pointer.lastMove, lastGhost);
            if (!ghost) {
                if (firstSeen && ts - firstSeen > 500 && !lastGhost) startGhost(ts);
                else if (lastGhost && idleFor > 5200 && ts - pointer.lastMove > 5200) startGhost(ts);
            }
            if (ghost) {
                const t = Math.min((ts - ghost.t0) / ghost.dur, 1);
                const e = t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
                const u = 1 - e;
                const gx = u * u * ghost.p0.x + 2 * u * e * ghost.p1.x + e * e * ghost.p2.x;
                const gy = u * u * ghost.p0.y + 2 * u * e * ghost.p1.y + e * e * ghost.p2.y;
                paintTear(gx, gy, brushR * 0.8);
                ghostPos = { x: gx, y: gy, fade: 1 };
                if (t >= 1) { ghost = null; }
            } else if (ghostPos && ghostPos.fade > 0) {
                ghostPos.fade -= 0.04; // ring fades out after the swipe
            }

            // ---- compose ----
            ctx.clearRect(0, 0, W, H);
            ctx.drawImage(blueprint, 0, 0, W, H);

            // white silhouette of the tear (for shadow + torn paper border)
            ectx.globalCompositeOperation = 'source-over';
            ectx.clearRect(0, 0, edgeC.width, edgeC.height);
            ectx.drawImage(mask, 0, 0);
            ectx.globalCompositeOperation = 'source-in';
            ectx.fillStyle = '#fff';
            ectx.fillRect(0, 0, edgeC.width, edgeC.height);

            // soft shadow under the torn hole
            ctx.save();
            ctx.filter = 'blur(7px)';
            ctx.globalAlpha = 0.28;
            ctx.drawImage(edgeC, 3, 5, W, H);
            ctx.restore();

            // torn white border: dilated silhouette behind the photo
            const d = 3;
            for (let i = 0; i < 8; i++) {
                const a = (i / 8) * Math.PI * 2;
                ctx.drawImage(edgeC, Math.cos(a) * d, Math.sin(a) * d, W, H);
            }

            // the render, clipped by the tear
            cctx.clearRect(0, 0, W, H);
            cctx.drawImage(fin, 0, 0);
            cctx.globalCompositeOperation = 'destination-in';
            cctx.drawImage(mask, 0, 0, W, H);
            cctx.globalCompositeOperation = 'source-over';
            ctx.drawImage(comp, 0, 0, W, H);

            // cursor ring (real pointer or ghost)
            let ring = null;
            if (pointer.x > -1e3 && ts - pointer.lastMove < 900) {
                ring = { x: pointer.x, y: pointer.y, a: 1 };
            } else if (ghostPos && ghostPos.fade > 0) {
                ring = { x: ghostPos.x, y: ghostPos.y, a: ghostPos.fade };
            }
            if (ring) {
                ctx.globalAlpha = 0.9 * ring.a;
                ctx.strokeStyle = ACCENT;
                ctx.lineWidth = 1.5;
                ctx.beginPath();
                ctx.arc(ring.x, ring.y, 22, 0, Math.PI * 2);
                ctx.stroke();
                ctx.fillStyle = ACCENT;
                ctx.beginPath();
                ctx.arc(ring.x, ring.y, 3, 0, Math.PI * 2);
                ctx.fill();
                ctx.globalAlpha = 1;
            }
        }

        wrap.addEventListener('pointermove', (e) => {
            const r = wrap.getBoundingClientRect();
            const x = e.clientX - r.left;
            const y = e.clientY - r.top;
            if (pointer.lastMove === 0 || pointer.x < -1e3) {
                pointer.px = x; pointer.py = y;
            }
            pointer.x = x; pointer.y = y;
            pointer.lastMove = performance.now();
            wrap.classList.add('is-touched');
        }, { passive: true });

        wrap.addEventListener('pointerleave', () => {
            pointer.x = -1e4; pointer.y = -1e4;
        });

        let resizeT;
        new ResizeObserver(() => {
            clearTimeout(resizeT);
            resizeT = setTimeout(resize, 150);
        }).observe(wrap);

        new IntersectionObserver((entries) => {
            const on = entries.some((e) => e.isIntersecting) && !document.hidden;
            if (on && !firstSeen) firstSeen = performance.now();
            visible = on;
            if (visible && !raf) raf = requestAnimationFrame(frame);
        }, { threshold: 0.35 }).observe(wrap);

        document.addEventListener('visibilitychange', () => {
            visible = !document.hidden;
            if (visible && !raf) raf = requestAnimationFrame(frame);
        });

        resize();
    }
}

document.querySelectorAll('.js-render-reveal').forEach(initReveal);
