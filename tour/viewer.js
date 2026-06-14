/* =========================================================================
   Maverickframe 3D Tour — public VIEWER (shortcode [pano_tour id=N]).
   Finds every .mfs-tour container on the page, reads its embedded config
   from data-tour, and boots the shared engine in read-only mode.
   ========================================================================= */
import { Viewer } from '@photo-sphere-viewer/core';
import { MarkersPlugin } from '@photo-sphere-viewer/markers-plugin';
import { AutorotatePlugin } from '@photo-sphere-viewer/autorotate-plugin';
import { GyroscopePlugin } from '@photo-sphere-viewer/gyroscope-plugin';
import { StereoPlugin } from '@photo-sphere-viewer/stereo-plugin';
import { CompassPlugin } from '@photo-sphere-viewer/compass-plugin';
import { createEngine } from './engine.js';

const deps = { Viewer, MarkersPlugin, AutorotatePlugin, GyroscopePlugin, StereoPlugin, CompassPlugin };

function boot(host){
  if (host.dataset.mfsBooted) return;
  host.dataset.mfsBooted = '1';
  let cfg;
  try { cfg = JSON.parse(host.getAttribute('data-tour') || 'null'); } catch(e){ cfg = null; }
  if (!cfg || !Array.isArray(cfg.nodes) || !cfg.nodes.length){
    host.innerHTML = '<div class="mfs-tour-empty">This tour has no scenes yet.</div>';
    return;
  }
  const stage = document.createElement('div');
  stage.className = 'mfs-tour-stage';
  host.appendChild(stage);
  createEngine(deps, stage, cfg, { editable: false });
}

function init(){ document.querySelectorAll('.mfs-tour').forEach(boot); }
if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
else init();
