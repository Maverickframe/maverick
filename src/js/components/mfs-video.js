// mfs-video.js — native <video> + hls.js player that replaces Bunny iframe embeds.
//
// The PHP converter (inc.video.php) rewrites every Bunny embed
// (iframe.mediadelivery.net / player.mediadelivery.net/embed/655216/{GUID})
// into a lightweight placeholder:
//
//   <div class="mfs-video js-mfs-video mfs-video--{mode}"
//        data-guid=".." data-src="https://{pz}/{guid}/playlist.m3u8"
//        data-poster="https://{pz}/{guid}/thumbnail.jpg"
//        data-mode="bg|click|hover" data-title=".."></div>
//
// This module hydrates those placeholders with a real <video> pointed at the
// Bunny HLS manifest. hls.js is dynamic-imported ONLY when a player actually
// needs to play (near viewport / hover / click), so pages without video never
// download it and even video pages fetch the ~40 KB gz chunk lazily.
//
// Modes:
//   bg    — muted autoplay loop, no controls (homepage showreel, sticky CTA).
//           Plays while in viewport, pauses when scrolled away.
//   click — poster + play button; click starts playback WITH sound + controls
//           (portfolio / case reels). Fires the GA4 video funnel.
//   hover — muted loop preview that plays on hover, resets on leave (grids).
//           Tap toggles on touch devices.
//
// Coexists with the legacy native-video system in videoPlay.js — that one keys
// off .js-video*, this one off .js-mfs-video, so there is no selector overlap.

let hlsPromise = null;
function loadHls() {
  // Full build (NOT hls.light): Bunny Stream serves demuxed audio via a separate
  // #EXT-X-MEDIA:TYPE=AUDIO rendition group, which the light build can't handle —
  // it throws manifestParsingError and the video never starts. The full build is
  // still fetched as its own lazy chunk, only when a player actually needs to play.
  if (!hlsPromise) hlsPromise = import('hls.js').then((m) => m.default || m);
  return hlsPromise;
}

// Safari / iOS play HLS natively via a plain <video src="…m3u8">; everyone else
// needs Media Source Extensions through hls.js.
function canPlayNativeHls(video) {
  return video.canPlayType('application/vnd.apple.mpegurl') !== '';
}

// Attach the manifest once. Returns a promise that resolves when the source is
// wired (native) or hls.js is attached. Transient (non-fatal) HLS errors are
// swallowed and fatal ones recovered quietly, so nothing lands in the console
// (keeps PSI Best-Practices clean — the whole point of dropping Bunny's iframe).
async function attachSource(video, root) {
  if (video.dataset.mfsAttached) return;
  video.dataset.mfsAttached = '1';

  const src = video.dataset.src;
  if (!src) return;

  // Prefer hls.js wherever Media Source Extensions exist (Chrome/Firefox/Edge/
  // Android). Chrome reports canPlayType('application/vnd.apple.mpegurl') ===
  // 'maybe' but CANNOT actually play HLS natively — trusting that would stall the
  // video at readyState 0. Use native HLS only on Safari/iOS, which have native
  // playback but no usable MSE-for-HLS, so we also skip downloading hls.js there.
  const mseOk =
    'MediaSource' in window &&
    MediaSource.isTypeSupported('video/mp4; codecs="avc1.42E01E,mp4a.40.2"');
  if (canPlayNativeHls(video) && !mseOk) {
    video.src = src;
    return;
  }

  const Hls = await loadHls();
  if (Hls && Hls.isSupported()) {
    // No capLevelToPlayerSize: Auto is pure bandwidth ABR (like Bunny/YouTube),
    // and a manual quality pick can force ANY rung regardless of window size.
    const hls = new Hls({ maxBufferLength: 30 });
    hls.on(Hls.Events.ERROR, (_evt, data) => {
      if (!data || !data.fatal) return; // transient — ignore, no console noise
      if (data.type === Hls.ErrorTypes.NETWORK_ERROR) {
        hls.startLoad();
      } else if (data.type === Hls.ErrorTypes.MEDIA_ERROR) {
        hls.recoverMediaError();
      } else {
        hls.destroy();
      }
    });
    // Bunny-style quality picker: build the gear menu once the level ladder is
    // known. Click mode only (bg loops / hover previews have no controls).
    if (root && root.dataset.mode === 'click') {
      hls.on(Hls.Events.MANIFEST_PARSED, () => buildQualityMenu(root, hls, Hls));
    }
    hls.loadSource(src);
    hls.attachMedia(video);
    video._mfsHls = hls;
  } else {
    // Last resort (very old browsers): let the element try the manifest itself.
    video.src = src;
  }
}

// ---- Quality picker (Bunny-style gear) --------------------------------------
// Builds a gear button + dropdown listing "Auto" and each resolution rung from
// the HLS manifest. Auto = bandwidth ABR; picking a rung forces that level.
function buildQualityMenu(root, hls, Hls) {
  if (root.querySelector('.mfs-video__quality')) return; // once per player
  const levels = hls.levels || [];
  if (levels.length < 2) return; // nothing to choose

  // One entry per distinct height (keep the highest-bitrate rung for each).
  const byHeight = new Map();
  levels.forEach((l, i) => {
    const prev = byHeight.get(l.height);
    if (!prev || l.bitrate > prev.bitrate) byHeight.set(l.height, { i, h: l.height, bitrate: l.bitrate });
  });
  const rungs = [...byHeight.values()].sort((a, b) => b.h - a.h);

  const wrap = document.createElement('div');
  wrap.className = 'mfs-video__quality';

  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'mfs-video__gear js-mfs-gear';
  btn.setAttribute('aria-label', 'Quality');
  btn.innerHTML = '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false"><path fill="currentColor" d="M19.14 12.94a7.5 7.5 0 000-1.88l2.03-1.58a.5.5 0 00.12-.64l-1.92-3.32a.5.5 0 00-.6-.22l-2.39.96a7 7 0 00-1.62-.94l-.36-2.54a.5.5 0 00-.5-.42h-3.84a.5.5 0 00-.5.42l-.36 2.54c-.58.24-1.12.56-1.62.94l-2.39-.96a.5.5 0 00-.6.22L2.68 8.84a.5.5 0 00.12.64l2.03 1.58a7.5 7.5 0 000 1.88l-2.03 1.58a.5.5 0 00-.12.64l1.92 3.32a.5.5 0 00.6.22l2.39-.96c.5.38 1.04.7 1.62.94l.36 2.54a.5.5 0 00.5.42h3.84a.5.5 0 00.5-.42l.36-2.54c.58-.24 1.12-.56 1.62-.94l2.39.96a.5.5 0 00.6-.22l1.92-3.32a.5.5 0 00-.12-.64l-2.03-1.58zM12 15.5A3.5 3.5 0 1112 8.5a3.5 3.5 0 010 7z"/></svg>';

  const menu = document.createElement('div');
  menu.className = 'mfs-video__menu';

  function render() {
    menu.innerHTML = '';
    const auto = hls.autoLevelEnabled;               // true when in Auto
    const playing = hls.levels[hls.currentLevel];    // active rung (or undefined in auto)
    const mk = (label, val, active) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.className = 'mfs-video__menu-item' + (active ? ' is-active' : '');
      b.textContent = label;
      b.addEventListener('click', (e) => {
        e.stopPropagation();
        hls.currentLevel = val;   // -1 = auto, else force that rung
        menu.classList.remove('is-open');
        render();
      });
      menu.appendChild(b);
    };
    // Auto shows the currently-playing height as a hint, like YouTube/Bunny.
    const autoHint = auto && playing ? ' (' + playing.height + 'p)' : '';
    mk('Auto' + autoHint, -1, auto);
    rungs.forEach((r) => mk(r.h + 'p', r.i, !auto && hls.currentLevel === r.i));
  }

  btn.addEventListener('click', (e) => {
    e.stopPropagation();
    render();
    menu.classList.toggle('is-open');
  });
  // Close on outside click / when the video is tapped.
  document.addEventListener('click', () => menu.classList.remove('is-open'));
  // Keep the Auto hint fresh as ABR switches rungs.
  hls.on(Hls.Events.LEVEL_SWITCHED, () => { if (menu.classList.contains('is-open')) render(); });

  wrap.appendChild(btn);
  wrap.appendChild(menu);
  root.appendChild(wrap);
}

// ---- GA4 funnel (dataLayer → GTM). Fired for click reels only: those are the
// engaged, sound-on plays that replace Bunny Stream's view stats. bg loops and
// muted hover previews would just spam the funnel, so they stay silent. --------
function attachTracking(video, title) {
  const marks = { 25: false, 50: false, 75: false };
  let started = false;
  let completed = false;

  function push(event, percent) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push({
      event,
      video_title: title || 'video',
      video_provider: 'bunny',
      video_percent: percent,
    });
  }

  video.addEventListener('play', () => {
    if (started) return;
    started = true;
    push('video_start', 0);
  });

  video.addEventListener('timeupdate', () => {
    if (!video.duration || !isFinite(video.duration)) return;
    const p = (video.currentTime / video.duration) * 100;
    [25, 50, 75].forEach((m) => {
      if (p >= m && !marks[m]) {
        marks[m] = true;
        push('video_progress', m);
      }
    });
  });

  video.addEventListener('ended', () => {
    if (completed) return;
    completed = true;
    push('video_complete', 100);
  });
}

// ---- Per-mode wiring ---------------------------------------------------------

function initBg(root, video) {
  video.muted = true;
  video.loop = true;
  video.setAttribute('muted', '');
  // Play only while visible; releases decode/network when scrolled away.
  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          attachSource(video, root).then(() => video.play().catch(() => {}));
        } else {
          video.pause();
        }
      });
    },
    { threshold: 0.25, rootMargin: '200px 0px' }
  );
  io.observe(root);
}

function initHover(root, video) {
  video.muted = true;
  video.loop = true;
  video.setAttribute('muted', '');
  root.classList.add('is-idle');

  let playing = false;
  const start = () => {
    attachSource(video, root).then(() => {
      video.play().catch(() => {});
      playing = true;
      root.classList.remove('is-idle');
    });
  };
  const stop = () => {
    video.pause();
    try { video.currentTime = 0; } catch (e) { /* not seekable yet */ }
    playing = false;
    root.classList.add('is-idle');
  };

  root.addEventListener('mouseenter', start);
  root.addEventListener('mouseleave', stop);
  // Touch: no hover, so tap toggles.
  root.addEventListener('click', () => (playing ? stop() : start()));
}

function initClick(root, video) {
  video.muted = true; // stays muted until the user opts in by clicking play
  root.classList.add('is-idle');

  const btn = document.createElement('button');
  btn.type = 'button';
  btn.className = 'mfs-video__play js-mfs-video-play';
  btn.setAttribute('aria-label', 'Play video');
  btn.innerHTML = '<svg viewBox="0 0 24 24" width="26" height="26" aria-hidden="true" focusable="false"><path d="M8 5v14l11-7z" fill="currentColor"/></svg>';
  root.appendChild(btn);

  attachTracking(video, root.dataset.title);

  const play = () => {
    root.classList.remove('is-idle');
    root.classList.add('is-playing');
    video.muted = false;
    video.controls = true;
    attachSource(video, root).then(() => video.play().catch(() => {}));
  };
  btn.addEventListener('click', play);
}

// Bunny's per-video preview.webp is an ANIMATED webp. Used directly as a <video
// poster> it loops on idle tiles and reads as a low-quality autoplay. Freeze its
// first frame to a static image via canvas instead (CORS on b-cdn is open). If
// the canvas is tainted for any reason, fall back to the animated image.
function setStaticPoster(video, url) {
  const img = new Image();
  img.crossOrigin = 'anonymous';
  img.onload = () => {
    try {
      const c = document.createElement('canvas');
      c.width = img.naturalWidth;
      c.height = img.naturalHeight;
      c.getContext('2d').drawImage(img, 0, 0);
      video.poster = c.toDataURL('image/jpeg', 0.85);
    } catch (e) {
      video.poster = url;
    }
  };
  img.onerror = () => { /* no poster — the badge still marks it as video */ };
  img.src = url;
}

function isAnimatedPoster(u) {
  return !!u && /preview\.webp/i.test(u);
}

// Featured images / non-preview posters are already static → set directly, with a
// probe that swaps to the fallback preview if the preferred image fails to load.
function setPoster(video, poster, posterFb) {
  const applyFb = () => {
    if (!posterFb) return;
    if (isAnimatedPoster(posterFb)) setStaticPoster(video, posterFb);
    else video.poster = posterFb;
  };
  if (poster) {
    if (isAnimatedPoster(poster)) {
      setStaticPoster(video, poster);
    } else {
      video.poster = poster;
      if (posterFb && posterFb !== poster) {
        const probe = new Image();
        probe.onerror = applyFb;
        probe.src = poster;
      }
    }
  } else {
    applyFb();
  }
}

function build(root) {
  if (root.dataset.mfsInit) return;
  root.dataset.mfsInit = '1';

  const mode = root.dataset.mode || 'click';
  const video = document.createElement('video');
  video.preload = 'none';
  video.playsInline = true;
  video.setAttribute('playsinline', '');
  video.setAttribute('webkit-playsinline', '');
  video.setAttribute('disablepictureinpicture', '');
  video.setAttribute('disableremoteplayback', '');
  setPoster(video, root.dataset.poster, root.dataset.posterFallback);
  if (root.dataset.title) video.setAttribute('title', root.dataset.title);
  // attachSource() reads the manifest from video.dataset.src; the data-* live on
  // the root placeholder, so copy the source onto the <video> we just created.
  if (root.dataset.src) video.dataset.src = root.dataset.src;
  root.appendChild(video);

  if (mode === 'bg') initBg(root, video);
  else if (mode === 'hover') initHover(root, video);
  else initClick(root, video);
}

function initAll() {
  document.querySelectorAll('.js-mfs-video').forEach(build);
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initAll);
} else {
  initAll();
}
