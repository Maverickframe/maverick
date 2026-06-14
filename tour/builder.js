/* =========================================================================
   Maverickframe 3D Tour — BUILDER (UI around the shared engine).
   WordPress integration: panoramas come from the Media Library (wp.media),
   tours are saved into the `pano_tour` CPT via the mfs/v1 REST API.
   localStorage is used only as a local draft-recovery cache.
   ========================================================================= */
import { Viewer } from '@photo-sphere-viewer/core';
import { MarkersPlugin } from '@photo-sphere-viewer/markers-plugin';
import { AutorotatePlugin } from '@photo-sphere-viewer/autorotate-plugin';
import { GyroscopePlugin } from '@photo-sphere-viewer/gyroscope-plugin';
import { StereoPlugin } from '@photo-sphere-viewer/stereo-plugin';
import { CompassPlugin } from '@photo-sphere-viewer/compass-plugin';
import { createEngine } from 'mfs-tour-engine';

const TB = window.MFS_TOUR || {};
const $ = s => document.querySelector(s);
const el = (t,c) => { const e = document.createElement(t); if (c) e.className = c; return e; };
const uid = () => 'h' + Math.random().toString(36).slice(2,8);

let config = (TB.config && Array.isArray(TB.config.nodes)) ? TB.config : { startId: null, nodes: [] };
let tourId = TB.tourId ? parseInt(TB.tourId, 10) : 0;
let seq = config.nodes.reduce((m,n) => Math.max(m, parseInt(String(n.id).replace(/\D/g,'')) || 0), 0) + 1;
let mode = 'edit';
let engine = null;
let nightTargetId = null;     // node awaiting a night image
let pendingInfoPos = null;    // position awaiting info form
let infoImg = null;           // {url,id} for info hotspot being created

const deps = { Viewer, MarkersPlugin, AutorotatePlugin, GyroscopePlugin, StereoPlugin, CompassPlugin };
const byId = id => config.nodes.find(n => n.id === id);

/* ---------- WordPress Media Library pickers ---------- */
function pickPanoramas(){
  if (!window.wp || !wp.media){ toast('Media Library unavailable'); return; }
  const frame = wp.media({ title: 'Select 360° panoramas', button: { text: 'Add to tour' }, multiple: true, library: { type: 'image' } });
  frame.on('select', () => {
    frame.state().get('selection').forEach(att => {
      const a = att.toJSON();
      addNode(a.title || a.filename || 'Scene', a.url, a.id);
    });
  });
  frame.open();
}
function pickSingle(title, cb){
  if (!window.wp || !wp.media){ toast('Media Library unavailable'); return; }
  const frame = wp.media({ title, button: { text: 'Use this image' }, multiple: false, library: { type: 'image' } });
  frame.on('select', () => { cb(frame.state().get('selection').first().toJSON()); });
  frame.open();
}

/* ---------- engine ---------- */
function ensureEngine(){
  if (engine) return;
  if (!config.nodes.length) return;
  $('#dropzone').classList.add('hide');
  engine = createEngine(deps, $('#viewer'), config, {
    editable: mode === 'edit',
    onNodeChange: id => { renderScenes(); renderLinksBar(); },
    onPlace: (pl, pos) => {
      const node = byId(engine.getCurrent());
      if (pl.type === 'nav'){
        node.hotspots = node.hotspots.filter(h => !(h.type==='nav' && h.target===pl.target));
        node.hotspots.push({ id: uid(), type:'nav', target: pl.target, position: pos });
        save(); engine.refreshScene(); renderScenes(); renderLinksBar(); toast('Link added');
        hideBanner();
      } else {
        pendingInfoPos = pos; hideBanner(); openInfoModal();
      }
    },
  });
}

/* ---------- nodes ---------- */
function addNode(name, dayUrl, dayId){
  const id = 'n' + (seq++);
  config.nodes.push({ id, name, day: dayUrl, dayId: dayId || null, night: null, nightId: null, hotspots: [] });
  if (!config.startId) config.startId = id;
  save();
  $('#exportBtn').disabled = false; $('#clearBtn').disabled = false;
  if (!engine){ ensureEngine(); } else { engine.rebuild(); engine.goTo(id); }
  renderScenes();
}
function removeNode(id){
  config.nodes = config.nodes.filter(n => n.id !== id);
  config.nodes.forEach(n => n.hotspots = n.hotspots.filter(h => !(h.type==='nav' && h.target===id)));
  if (config.startId === id) config.startId = config.nodes[0]?.id || null;
  save();
  if (!config.nodes.length){
    if (engine){ engine.destroy(); engine = null; }
    $('#dropzone').classList.remove('hide'); $('#exportBtn').disabled = true; $('#clearBtn').disabled = true;
  } else { engine.rebuild(); if (engine.getCurrent() === id) engine.goTo(config.nodes[0].id); else engine.refreshScene(); }
  renderScenes(); renderLinksBar();
}
function rename(id, name){ const n = byId(id); if (n){ n.name = name; save(); engine && engine.refreshScene(); } }
function setStart(id){ config.startId = id; save(); renderScenes(); }
function removeHotspot(nodeId, hid){
  const n = byId(nodeId); if (!n) return;
  n.hotspots = n.hotspots.filter(h => h.id !== hid);
  save(); engine && engine.refreshScene(); renderScenes(); renderLinksBar();
}

/* ---------- left panel ---------- */
function renderScenes(){
  const list = $('#sceneList');
  list.querySelectorAll('.scene').forEach(e => e.remove());
  $('#sceneEmpty').style.display = config.nodes.length ? 'none' : 'block';
  const curId = engine && engine.getCurrent();
  config.nodes.forEach(n => {
    const navN = n.hotspots.filter(h=>h.type==='nav').length;
    const infoN = n.hotspots.filter(h=>h.type==='info').length;
    const row = el('div', 'scene' + (n.id === curId ? ' active' : ''));
    const top = el('div','top');
    const img = el('img','thumb'); img.src = n.day; top.appendChild(img);
    const meta = el('div','meta');
    const nm = el('div','nm'); nm.textContent = n.name; nm.title = 'Double-click to rename';
    nm.ondblclick = () => { const v = prompt('Scene name:', n.name); if (v){ rename(n.id, v); renderScenes(); } };
    const sub = el('div','sub');
    sub.innerHTML = `<span class="badge">${navN} links</span><span class="badge">${infoN} info</span>` + (n.night ? '<span class="badge night">&#9790; night</span>' : '');
    meta.appendChild(nm); meta.appendChild(sub); top.appendChild(meta);
    const star = el('div','star'+(n.id===config.startId?' on':'')); star.textContent='★'; star.title='Start scene';
    star.onclick = e => { e.stopPropagation(); setStart(n.id); };
    top.appendChild(star);
    const del = el('div','del'); del.textContent='✕'; del.title='Delete scene';
    del.onclick = e => { e.stopPropagation(); if (confirm('Delete scene "'+n.name+'"?')) removeNode(n.id); };
    top.appendChild(del);
    row.appendChild(top);

    const row2 = el('div','row2');
    const bNight = el('button','minibtn'+(n.night?' on':'')); bNight.textContent = n.night ? '🌙 Replace night' : '🌙 + night panorama';
    bNight.onclick = e => { e.stopPropagation(); nightTargetId = n.id; pickSingle('Select night panorama', a => {
      const t = byId(nightTargetId); if (t){ t.night = a.url; t.nightId = a.id; save(); engine && engine.refreshScene(); renderScenes(); toast('Night panorama added'); } nightTargetId = null;
    }); };
    row2.appendChild(bNight);
    if (n.night){
      const bDel = el('button','minibtn'); bDel.textContent = 'remove night';
      bDel.onclick = e => { e.stopPropagation(); n.night = null; n.nightId = null; save(); engine && engine.refreshScene(); renderScenes(); };
      row2.appendChild(bDel);
    }
    row.appendChild(row2);

    row.onclick = () => { if (engine){ engine.goTo(n.id); } renderScenes(); renderLinksBar(); };
    list.appendChild(row);
  });
  const tn = config.nodes.reduce((s,n)=>s+n.hotspots.filter(h=>h.type==='nav').length,0);
  const ti = config.nodes.reduce((s,n)=>s+n.hotspots.filter(h=>h.type==='info').length,0);
  $('#statHint').textContent = `${config.nodes.length} scenes · ${tn} links · ${ti} info`;
}

function renderLinksBar(){
  const bar = $('#linksBar'); bar.innerHTML = '';
  if (mode !== 'edit' || !engine) return;
  const n = byId(engine.getCurrent()); if (!n) return;
  const addNav = el('div','chip'); addNav.style.cursor='pointer'; addNav.style.borderColor='var(--accent)';
  addNav.innerHTML = '＋ <span>Link</span>'; addNav.onclick = openPicker; bar.appendChild(addNav);
  const addInfo = el('div','chip info'); addInfo.style.cursor='pointer';
  addInfo.innerHTML = '＋ <span>Info point</span>';
  addInfo.onclick = () => { if(!engine){return;} engine.setPlacement({type:'info'}); showBanner('<b>Info point:</b> click a spot on the panorama'); };
  bar.appendChild(addInfo);
  n.hotspots.forEach(h => {
    if (h.type === 'nav'){
      const t = byId(h.target); const chip = el('div','chip');
      chip.innerHTML = `<span>→</span> ${t?t.name:'?'} <span class="x">✕</span>`;
      chip.querySelector('.x').onclick = () => removeHotspot(n.id, h.id); bar.appendChild(chip);
    } else {
      const chip = el('div','chip info');
      chip.innerHTML = `<span>ⓘ</span> ${h.title||'Info'} <span class="x">✕</span>`;
      chip.querySelector('.x').onclick = () => removeHotspot(n.id, h.id); bar.appendChild(chip);
    }
  });
}

function openPicker(){
  const curId = engine.getCurrent();
  const others = config.nodes.filter(n => n.id !== curId);
  if (!others.length){ toast('Need another scene to link to'); return; }
  const p = $('#picker'), opts = $('#pickerOpts'); opts.innerHTML = '';
  others.forEach(n => {
    const o = el('div','opt'); const img = el('img'); img.src = n.day; o.appendChild(img);
    const sp = el('span'); sp.textContent = n.name; o.appendChild(sp);
    o.onclick = () => { p.classList.remove('show'); engine.setPlacement({type:'nav', target:n.id}); showBanner('<b>Link → '+n.name+'.</b> Click a spot on the panorama'); };
    opts.appendChild(o);
  });
  p.style.left = '14px'; p.style.bottom = '108px'; p.classList.add('show');
}
function showBanner(html){ $('#bannerText').innerHTML = html; $('#banner').classList.add('show'); }
function hideBanner(){ $('#banner').classList.remove('show'); }

/* ---------- info modal ---------- */
function openInfoModal(){ $('#infoTitle').value=''; $('#infoText').value=''; infoImg=null; $('#infoImgName').textContent='none'; $('#infoModal').classList.add('show'); }
$('#infoImgBtn').onclick = () => pickSingle('Select info image', a => { infoImg = { url:a.url, id:a.id }; $('#infoImgName').textContent = a.title || a.filename || 'selected'; });
$('#infoCancel').onclick = $('#infoClose').onclick = () => { $('#infoModal').classList.remove('show'); pendingInfoPos = null; };
$('#infoSave').onclick = () => {
  if (!pendingInfoPos) { $('#infoModal').classList.remove('show'); return; }
  const node = byId(engine.getCurrent());
  node.hotspots.push({ id: uid(), type:'info', title: $('#infoTitle').value || 'Info', text: $('#infoText').value || '',
    image: infoImg ? infoImg.url : null, imageId: infoImg ? infoImg.id : null, position: pendingInfoPos });
  pendingInfoPos = null; $('#infoModal').classList.remove('show');
  save(); engine.refreshScene(); renderScenes(); renderLinksBar(); toast('Info point added');
};

/* ---------- mode ---------- */
function setMode(m){
  mode = m; hideBanner(); $('#picker').classList.remove('show');
  $('#modeSeg').querySelectorAll('button').forEach(b => b.classList.toggle('on', b.dataset.mode===m));
  if (engine) engine.setEditable(m === 'edit');
  renderLinksBar();
}

/* ---------- persistence: CPT via REST + local draft ---------- */
function exportConfig(){
  return { startId: config.startId, nodes: config.nodes.map(n => ({
    id:n.id, name:n.name, day:n.day, dayId:n.dayId||null, night:n.night||null, nightId:n.nightId||null, hotspots:n.hotspots })) };
}
let saveTimer = null, saving = false;
function setStat(cls, txt){ const s = $('#saveStat'); s.className = 'savestat ' + cls; s.textContent = txt; }
function persistLocal(){ try { localStorage.setItem('mf_tour_' + (tourId || 'new'), JSON.stringify(exportConfig())); } catch(e){} }
function save(){ persistLocal(); setStat('dirty','Unsaved…'); clearTimeout(saveTimer); saveTimer = setTimeout(serverSave, 1500); }
async function serverSave(){
  if (saving) { clearTimeout(saveTimer); saveTimer = setTimeout(serverSave, 800); return; }
  if (!TB.restUrl) { setStat('dirty','Local draft only'); return; }
  saving = true; setStat('dirty','Saving…');
  try {
    const url = TB.restUrl + 'tour' + (tourId ? '/' + tourId : '');
    const r = await fetch(url, { method:'POST', credentials:'same-origin',
      headers:{ 'Content-Type':'application/json', 'X-WP-Nonce': TB.nonce || '' },
      body: JSON.stringify({ title: ($('#tourTitle').value || 'Untitled tour'), config: exportConfig() }) });
    const j = await r.json();
    if (j && j.id){
      if (!tourId){ tourId = j.id; const u = new URL(window.location.href); u.searchParams.set('tour', tourId); history.replaceState(null,'',u); $('#shortcodeOut') && ($('#shortcodeOut').value = '[pano_tour id="'+tourId+'"]'); }
      setStat('saved','Saved ✓');
    } else { setStat('dirty','Save failed'); }
  } catch(e){ setStat('dirty','Save failed (offline?)'); }
  saving = false;
}

/* ---------- export ---------- */
function exportHtml(cfg){
  const ENGINE = createEngine.toString().replace(/^export\s+/, '');
  return `<!doctype html><html lang="en"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>${cfg.nodes[0]?cfg.nodes[0].name:'Tour'} — Maverickframe</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core@5.14.1/index.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5.14.1/index.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/compass-plugin@5.14.1/index.css">
<style>html,body{margin:0;height:100%;background:#000}#tour{width:100%;height:100vh}</style></head>
<body><div id="tour"></div>
<script type="importmap">{"imports":{
"three":"https://cdn.jsdelivr.net/npm/three@0.169.0/build/three.module.js",
"@photo-sphere-viewer/core":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core@5.14.1/index.module.js",
"@photo-sphere-viewer/markers-plugin":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/markers-plugin@5.14.1/index.module.js",
"@photo-sphere-viewer/autorotate-plugin":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/autorotate-plugin@5.14.1/index.module.js",
"@photo-sphere-viewer/gyroscope-plugin":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/gyroscope-plugin@5.14.1/index.module.js",
"@photo-sphere-viewer/stereo-plugin":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/stereo-plugin@5.14.1/index.module.js",
"@photo-sphere-viewer/compass-plugin":"https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/compass-plugin@5.14.1/index.module.js"
}}<\/script>
<script type="module">
import {Viewer} from '@photo-sphere-viewer/core';
import {MarkersPlugin} from '@photo-sphere-viewer/markers-plugin';
import {AutorotatePlugin} from '@photo-sphere-viewer/autorotate-plugin';
import {GyroscopePlugin} from '@photo-sphere-viewer/gyroscope-plugin';
import {StereoPlugin} from '@photo-sphere-viewer/stereo-plugin';
import {CompassPlugin} from '@photo-sphere-viewer/compass-plugin';
const createEngine = ${ENGINE};
const CFG = ${JSON.stringify(cfg)};
createEngine({Viewer,MarkersPlugin,AutorotatePlugin,GyroscopePlugin,StereoPlugin,CompassPlugin}, document.getElementById('tour'), CFG, {editable:false});
<\/script></body></html>`;
}
function openExport(){
  const cfg = exportConfig();
  $('#jsonOut').value = JSON.stringify(cfg, null, 2);
  $('#shortcodeOut').value = tourId ? '[pano_tour id="'+tourId+'"]' : 'Save the tour first to get a shortcode';
  $('#exportModal').classList.add('show');
}
function dl(name, content, type){ const b = new Blob([content], {type}); const u = URL.createObjectURL(b); const a = el('a'); a.href=u; a.download=name; a.click(); URL.revokeObjectURL(u); }

/* ---------- toast ---------- */
let tt; function toast(m){ const t=$('#toast'); t.textContent=m; t.classList.add('show'); clearTimeout(tt); tt=setTimeout(()=>t.classList.remove('show'),1800); }

/* ---------- wire ---------- */
$('#addBtn').onclick = pickPanoramas;
$('#dropAddBtn').onclick = pickPanoramas;
$('#saveBtn').onclick = () => { clearTimeout(saveTimer); serverSave(); };
$('#tourTitle').oninput = () => save();
$('#clearBtn').onclick = () => { if (confirm('Delete all scenes?')){ if (engine){engine.destroy(); engine=null;} config={startId:null,nodes:[]}; seq=1; save(); renderScenes(); renderLinksBar(); $('#dropzone').classList.remove('hide'); $('#exportBtn').disabled=true; $('#clearBtn').disabled=true; } };
$('#exportBtn').onclick = openExport;
$('#exportClose').onclick = () => $('#exportModal').classList.remove('show');
$('#bannerCancel').onclick = () => { if (engine) engine.setPlacement(null); hideBanner(); };
$('#modeSeg').querySelectorAll('button').forEach(b => b.onclick = () => setMode(b.dataset.mode));
$('#copyShortcode').onclick = () => { navigator.clipboard.writeText($('#shortcodeOut').value); toast('Shortcode copied'); };
$('#copyJson').onclick = () => { navigator.clipboard.writeText($('#jsonOut').value); toast('JSON copied'); };
$('#downloadJson').onclick = () => dl('tour.json', $('#jsonOut').value, 'application/json');
$('#downloadHtml').onclick = () => dl('tour.html', exportHtml(exportConfig()), 'text/html');

/* ---------- init ---------- */
if (config.nodes.length){ $('#exportBtn').disabled=false; $('#clearBtn').disabled=false; }
renderScenes();
if (config.nodes.length){ ensureEngine(); renderLinksBar(); }
setStat('', tourId ? ('Tour #'+tourId) : 'New tour');
