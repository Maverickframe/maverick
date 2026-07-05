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
  // Light build: no subtitles / alt-audio / EME — none of which Bunny VOD needs.
  // ~110 KB min vs ~160 KB for the full build, still fetched as its own lazy chunk.
  if (!hlsPromise) hlsPromise = import('hls.js/dist/hls.light.mjs').then((m) => m.default || m);
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
async function attachSource(video) {
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
    const hls = new Hls({
      capLevelToPlayerSize: true, // don't pull 4K into a 550px window
      maxBufferLength: 30,
    });
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
    hls.loadSource(src);
    hls.attachMedia(video);
    video._mfsHls = hls;
  } else {
    // Last resort (very old browsers): let the element try the manifest itself.
    video.src = src;
  }
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
          attachSource(video).then(() => video.play().catch(() => {}));
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
    attachSource(video).then(() => {
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
    attachSource(video).then(() => video.play().catch(() => {}));
  };
  btn.addEventListener('click', play);
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
  // Poster with graceful fallback: <video poster> gives no load-error event, so
  // probe the preferred poster via an Image and swap to Bunny's preview.webp if
  // it fails (e.g. a page without a featured image).
  const poster = root.dataset.poster;
  const posterFb = root.dataset.posterFallback;
  if (poster) {
    video.poster = poster;
    if (posterFb && posterFb !== poster) {
      const probe = new Image();
      probe.onerror = () => { video.poster = posterFb; };
      probe.src = poster;
    }
  } else if (posterFb) {
    video.poster = posterFb;
  }
  if (root.dataset.title) video.setAttribute('title', root.dataset.title);
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
