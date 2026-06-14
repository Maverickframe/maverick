/* =========================================================================
   Maverickframe 3D Tour — ENGINE (self-contained tour player).
   Used for live preview in the builder, for the public shortcode viewer,
   AND injected (via .toString()) into the exported standalone .html.
   It must NOT reference anything from outer scope — only its params and
   browser globals. Keep dependency-free (deps are passed in).
   ========================================================================= */

/* --- CSP workaround. This site's CSP (set at the Cloudflare/Rocket edge) has
   no `blob:` in img-src, but Photo Sphere Viewer loads panoramas through blob:
   URLs, which CSP then blocks ("panorama cannot be loaded"). Transparently
   convert image blobs to data: URLs (allowed by `default-src ... data:`).
   Module-level so it runs before any PSV load; kept OUTSIDE createEngine so the
   exported standalone tours (which run off-site, no such CSP) stay clean.
   Proper long-term fix: add `blob:` to img-src at the host/CDN level. --- */
(function mfsPatchBlobImagesForCSP(){
  if (typeof window === 'undefined' || window.__mfsBlobImgPatched) return;
  window.__mfsBlobImgPatched = true;
  try {
    const realCreate = URL.createObjectURL.bind(URL);
    const blobs = new Map();
    // Track every blob URL — PSV builds its panorama blob from an XHR response
    // with an EMPTY mime type, so we can't filter by type here.
    URL.createObjectURL = function(obj){
      const url = realCreate(obj);
      if (obj instanceof Blob) blobs.set(url, obj);
      return url;
    };
    const realRevoke = URL.revokeObjectURL.bind(URL);
    URL.revokeObjectURL = function(url){ blobs.delete(url); return realRevoke(url); };
    // Untyped blobs become data:application/octet-stream which <img> won't
    // decode — sniff the real image type from the base64 magic bytes.
    const fixMime = (dataUrl) => {
      const m = /^data:([^;,]*)(;base64)?,(.*)$/s.exec(dataUrl);
      if (!m || /^image\//i.test(m[1])) return dataUrl;
      const b64 = m[3]; let mime = 'image/jpeg';
      if (b64.indexOf('iVBOR') === 0) mime = 'image/png';
      else if (b64.indexOf('UklGR') === 0) mime = 'image/webp';
      else if (b64.indexOf('R0lGOD') === 0) mime = 'image/gif';
      return 'data:' + mime + ';base64,' + b64;
    };
    const desc = Object.getOwnPropertyDescriptor(HTMLImageElement.prototype, 'src');
    Object.defineProperty(HTMLImageElement.prototype, 'src', {
      configurable: true,
      enumerable: desc.enumerable,
      get(){ return desc.get.call(this); },
      set(v){
        if (typeof v === 'string' && v.indexOf('blob:') === 0 && blobs.has(v)){
          const fr = new FileReader();
          fr.onload = () => desc.set.call(this, fixMime(fr.result));
          fr.onerror = () => desc.set.call(this, v);
          fr.readAsDataURL(blobs.get(v));
          return;
        }
        desc.set.call(this, v);
      },
    });
  } catch (e) { /* non-fatal: fall back to native behaviour */ }
})();

export function createEngine(deps, container, config, opts){
  const { Viewer, MarkersPlugin, AutorotatePlugin, GyroscopePlugin, StereoPlugin, CompassPlugin } = deps;
  opts = opts || {};
  let editable = !!opts.editable;
  let mode = 'day';                 // day | night
  let cur = null;                   // current node id
  let placement = null;             // edit: {type:'nav',target} | {type:'info'}

  const byId = id => config.nodes.find(n => n.id === id);
  const anyNight = () => config.nodes.some(n => n.night);
  const panoOf = n => (mode === 'night' && n.night) ? n.night : n.day;

  // ---- inject runtime CSS (hotspots, controls, info popup) ----
  const css = `
  .mfp-hp{display:flex;align-items:center;justify-content:center;cursor:pointer;transition:transform .15s}
  .mfp-hp:hover{transform:scale(1.12)}
  .mfp-nav{width:46px;height:46px;border-radius:50%;background:rgba(47,107,255,.22);
    border:2px solid #fff;box-shadow:0 0 0 6px rgba(47,107,255,.12),0 4px 14px rgba(0,0,0,.5)}
  .mfp-nav svg{width:24px;height:24px;fill:#fff;filter:drop-shadow(0 1px 2px rgba(0,0,0,.6))}
  .mfp-nav.pulse{animation:mfpPulse 2.2s ease-out infinite}
  @keyframes mfpPulse{0%{box-shadow:0 0 0 0 rgba(255,255,255,.5),0 4px 14px rgba(0,0,0,.5)}70%{box-shadow:0 0 0 16px rgba(255,255,255,0),0 4px 14px rgba(0,0,0,.5)}100%{box-shadow:0 0 0 0 rgba(255,255,255,0)}}
  .mfp-info{width:32px;height:32px;border-radius:50%;background:rgba(255,255,255,.92);color:#11131a;
    font:700 18px Georgia,serif;border:2px solid #2f6bff;box-shadow:0 3px 10px rgba(0,0,0,.5)}
  .mfp-daynight{position:absolute;top:14px;right:14px;z-index:40;display:none}
  .mfp-daynight.show{display:block}
  .mfp-dn-track{width:84px;height:34px;border-radius:40px;cursor:pointer;position:relative;
    background:linear-gradient(90deg,#8fd0ff,#cfeaff);border:1px solid rgba(0,0,0,.2);
    box-shadow:0 4px 14px rgba(0,0,0,.35);transition:background .35s}
  .mfp-dn-track.is-night{background:linear-gradient(90deg,#1b2746,#33406b)}
  .mfp-dn-track span{position:absolute;top:8px;font-size:15px;line-height:1;user-select:none}
  .mfp-dn-track .sun{left:9px}.mfp-dn-track .moon{right:9px;opacity:.55}
  .mfp-dn-track.is-night .sun{opacity:.4}.mfp-dn-track.is-night .moon{opacity:1}
  .mfp-dn-knob{position:absolute;top:3px;left:3px;width:28px;height:28px;border-radius:50%;
    background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.4);transition:left .35s cubic-bezier(.4,1.3,.5,1)}
  .mfp-dn-track.is-night .mfp-dn-knob{left:53px}
  .mfp-strip-btn{position:absolute;left:14px;bottom:14px;z-index:40;background:rgba(20,22,28,.85);
    border:1px solid rgba(255,255,255,.18);color:#fff;font-size:12px;padding:7px 11px;border-radius:2px;cursor:pointer}
  .mfp-strip{position:absolute;left:14px;bottom:54px;z-index:40;display:none;gap:8px;max-width:70%;
    overflow-x:auto;padding:8px;background:rgba(15,16,20,.9);border:1px solid rgba(255,255,255,.12);border-radius:2px}
  .mfp-strip.show{display:flex}
  .mfp-strip img{width:84px;height:56px;object-fit:cover;border-radius:2px;cursor:pointer;border:2px solid transparent;flex:0 0 auto}
  .mfp-strip img.on{border-color:#2f6bff}
  .mfp-pop{position:absolute;inset:0;z-index:50;display:none;align-items:center;justify-content:center;padding:24px;background:rgba(0,0,0,.55)}
  .mfp-pop.show{display:flex}
  .mfp-card{background:#16181d;color:#e8eaed;border:1px solid #2a2e36;border-radius:2px;max-width:420px;width:100%;overflow:hidden;
    font-family:-apple-system,Segoe UI,Roboto,Arial,sans-serif}
  .mfp-card img{width:100%;max-height:230px;object-fit:cover;display:block}
  .mfp-card .c{padding:16px 18px}
  .mfp-card h4{margin:0 0 8px;font-size:16px}
  .mfp-card p{margin:0;font-size:13px;line-height:1.6;color:#c7ccd4;white-space:pre-wrap}
  .mfp-card .close{display:block;margin:14px 18px 18px auto;background:#2f6bff;color:#fff;border:0;padding:8px 16px;border-radius:2px;cursor:pointer;font-size:13px}
  `;
  const st = document.createElement('style'); st.textContent = css; document.head.appendChild(st);

  // ---- viewer ----
  const viewer = new Viewer({
    container,
    navbar: ['zoom','move','autorotate','gyroscope','stereo','caption','fullscreen'],
    defaultZoomLvl: 0,
    mousewheelCtrlKey: false,
    plugins: [
      [MarkersPlugin, {}],
      [AutorotatePlugin, { autostartDelay: null, autorotateSpeed: '0.6rpm' }],
      [GyroscopePlugin, {}],
      [StereoPlugin, {}],
      [CompassPlugin, { size: '90px', backgroundSvg:
        '<svg viewBox="0 0 100 100"><circle cx="50" cy="50" r="49" fill="rgba(0,0,0,.45)" stroke="rgba(255,255,255,.5)" stroke-width="1"/></svg>' }],
    ],
  });
  const markers = viewer.getPlugin(MarkersPlugin);
  const compass = viewer.getPlugin(CompassPlugin);

  // ---- day/night switch ----
  const dn = document.createElement('div'); dn.className = 'mfp-daynight';
  dn.innerHTML = '<div class="mfp-dn-track"><span class="sun">&#9728;</span><span class="moon">&#9790;</span><div class="mfp-dn-knob"></div></div>';
  container.appendChild(dn);
  const dnTrack = dn.querySelector('.mfp-dn-track');
  dnTrack.addEventListener('click', toggleDayNight);

  // ---- scene strip ----
  const stripBtn = document.createElement('button'); stripBtn.className = 'mfp-strip-btn'; stripBtn.textContent = '▦ Scenes';
  const strip = document.createElement('div'); strip.className = 'mfp-strip';
  container.appendChild(stripBtn); container.appendChild(strip);
  stripBtn.addEventListener('click', () => strip.classList.toggle('show'));

  // ---- info popup ----
  const pop = document.createElement('div'); pop.className = 'mfp-pop';
  pop.innerHTML = '<div class="mfp-card"><img id="mfpPopImg"><div class="c"><h4 id="mfpPopTitle"></h4><p id="mfpPopText"></p></div><button class="close">Close</button></div>';
  container.appendChild(pop);
  pop.querySelector('.close').addEventListener('click', () => pop.classList.remove('show'));
  pop.addEventListener('click', e => { if (e.target === pop) pop.classList.remove('show'); });

  function showInfo(d){
    const img = pop.querySelector('#mfpPopImg');
    if (d.image) { img.src = d.image; img.style.display = 'block'; } else { img.style.display = 'none'; }
    pop.querySelector('#mfpPopTitle').textContent = d.title || '';
    pop.querySelector('#mfpPopText').textContent = d.text || '';
    pop.classList.add('show');
  }

  // ---- markers ----
  function navSvg(){ return '<svg viewBox="0 0 24 24"><path d="M12 4l7 8h-4v8H9v-8H5z"/></svg>'; }
  function applyMarkers(node){
    const list = [];
    (node.hotspots || []).forEach(h => {
      if (h.type === 'nav'){
        const t = byId(h.target);
        list.push({ id: h.id, position: h.position, html: `<div class="mfp-hp mfp-nav pulse">${navSvg()}</div>`,
          size: { width:46, height:46 }, anchor:'center center', tooltip: t ? t.name : '', data: h });
      } else {
        list.push({ id: h.id, position: h.position, html: '<div class="mfp-hp mfp-info">i</div>',
          size: { width:32, height:32 }, anchor:'center center', tooltip: h.title || 'Info', data: h });
      }
    });
    markers.setMarkers(list);
  }

  markers.addEventListener('select-marker', ({ marker }) => {
    const d = marker.config.data || {};
    if (editable){ if (opts.onSelectHotspot) opts.onSelectHotspot(d); return; }
    if (d.type === 'nav') goTo(d.target);
    else showInfo(d);
  });

  // edit: click sphere to place a hotspot
  viewer.addEventListener('click', e => {
    if (!editable || !placement) return;
    const pos = { yaw: e.data.yaw, pitch: e.data.pitch };
    const pl = placement; placement = null;
    if (opts.onPlace) opts.onPlace(pl, pos);
  });

  function buildStrip(){
    strip.innerHTML = '';
    config.nodes.forEach(n => {
      const im = document.createElement('img'); im.src = n.day; im.title = n.name;
      if (n.id === cur) im.classList.add('on');
      im.addEventListener('click', () => goTo(n.id));
      strip.appendChild(im);
    });
  }
  function buildCompass(node){
    const hs = (node.hotspots || []).filter(h => h.type === 'nav').map(h => ({ yaw: h.position.yaw, color: '#2f6bff' }));
    if (compass) compass.setHotspots(hs);
  }
  function updateDayNight(node){
    dn.classList.toggle('show', !!node.night);
    dnTrack.classList.toggle('is-night', mode === 'night' && !!node.night);
  }

  async function showScene(id, transition){
    const node = byId(id); if (!node) return;
    cur = id;
    try {
      await viewer.setPanorama(panoOf(node), { transition: transition !== false, showLoader: true, caption: node.name });
    } catch (e) {
      // A non-equirectangular or oversized image can't be used as a 360° panorama.
      console.warn('[mfs-tour] panorama could not be loaded for scene', id, e && e.message ? e.message : e);
    }
    applyMarkers(node); buildStrip(); buildCompass(node); updateDayNight(node);
    if (opts.onNodeChange) opts.onNodeChange(id);
  }
  async function goTo(id){ if (id && id !== cur) await showScene(id, true); }

  async function toggleDayNight(){
    const node = byId(cur); if (!node || !node.night) return;
    mode = (mode === 'night') ? 'day' : 'night';
    dnTrack.classList.toggle('is-night', mode === 'night');
    try {
      await viewer.setPanorama(panoOf(node), { transition: { effect:'fade', rotation:false }, showLoader:false });
    } catch (e) {
      console.warn('[mfs-tour] night panorama could not be loaded', e && e.message ? e.message : e);
    }
    applyMarkers(node);  // re-add markers after panorama swap
  }

  // initial
  if (config.nodes.length){
    showScene(config.startId && byId(config.startId) ? config.startId : config.nodes[0].id, false);
  }

  return {
    viewer, markers,
    goTo, toggleDayNight,
    setEditable(v){ editable = v; placement = null; },
    setPlacement(p){ placement = p; },
    getCurrent(){ return cur; },
    refreshScene(){ const n = byId(cur); if (n){ applyMarkers(n); buildStrip(); buildCompass(n); updateDayNight(n); } },
    rebuild(){ buildStrip(); const n = byId(cur); if (!n && config.nodes.length) showScene(config.nodes[0].id, false); else if (n) this.refreshScene(); },
    destroy(){ viewer.destroy(); st.remove(); dn.remove(); strip.remove(); stripBtn.remove(); pop.remove(); },
  };
}
