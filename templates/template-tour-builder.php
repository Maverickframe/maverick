<?php
/**
 * Template Name: 3D Tour Builder
 *
 * Internal, gated full-screen tool. Bare layout (no theme header/footer):
 * the page bundle + theme chrome are dequeued in inc.tour.php; the builder
 * assets, Media Library picker and importmap are wired there too.
 */
if ( ! defined('ABSPATH') ) exit;

if ( ! is_user_logged_in() || ! current_user_can('edit_posts') ) {
    auth_redirect();
    exit;
}
nocache_headers();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>3D Tour Builder — Maverickframe</title>
<?php wp_head(); ?>
</head>
<body <?php body_class('mfs-tour-builder'); ?>>
<div class="mfs-tb">
  <div class="app">
    <header>
      <div class="brand"><span class="dot"></span> Maverickframe · 3D Tour Builder</div>
      <input type="text" id="tourTitle" class="titlebox" placeholder="Tour name" value="<?php
        $tid = isset($_GET['tour']) ? (int) $_GET['tour'] : 0;
        echo $tid && get_post_type($tid) === 'pano_tour' ? esc_attr(get_the_title($tid)) : '';
      ?>">
      <div class="spacer"></div>
      <span class="savestat" id="saveStat">New tour</span>
      <div class="seg" id="modeSeg">
        <button data-mode="edit" class="on">Editor</button>
        <button data-mode="preview">Preview</button>
      </div>
      <button class="btn" id="addBtn">＋ Add panorama</button>
      <button class="btn primary" id="saveBtn">Save</button>
      <button class="btn ghost" id="exportBtn" disabled>Export</button>
    </header>

    <div class="body">
      <aside>
        <div class="aside-head">
          <h2>Tour scenes</h2>
          <p>Pick 360° equirectangular images (2:1) from the Media Library. Each scene can have a <b>night</b> panorama plus navigation and info points.</p>
        </div>
        <div class="scenes" id="sceneList">
          <div class="empty" id="sceneEmpty">No scenes yet.<br>Click “Add panorama” to choose images from the Media Library.</div>
        </div>
        <div class="aside-foot">
          <div class="hint" id="statHint">0 scenes · 0 links · 0 info</div>
          <button class="btn ghost" id="clearBtn" disabled>Clear all</button>
        </div>
      </aside>

      <div class="stage">
        <div id="viewer"></div>
        <div class="dropzone" id="dropzone">
          <div class="ic">🌐</div>
          <h3>Build a virtual tour</h3>
          <p>Choose spherical 360° panoramas (2:1) from the Media Library. Add two or more scenes, drop navigation arrows between them, attach a night panorama to any scene — and you get an interactive day/night tour.</p>
          <button class="btn primary" id="dropAddBtn">Choose from Media Library</button>
        </div>
        <div class="banner" id="banner"><span id="bannerText"></span><button id="bannerCancel">Cancel</button></div>
        <div class="picker" id="picker"><h4>Where does this link go?</h4><div id="pickerOpts"></div></div>
        <div class="links-bar" id="linksBar"></div>
      </div>
    </div>
  </div>

  <!-- Info hotspot editor -->
  <div class="modal-wrap" id="infoModal">
    <div class="modal">
      <header><h3>Info point</h3><button class="btn ghost" id="infoClose">✕</button></header>
      <div class="mbody">
        <label>Title</label>
        <input type="text" id="infoTitle" placeholder="e.g. Natural stone fireplace">
        <label>Text</label>
        <textarea id="infoText" placeholder="Short description shown to the viewer in a popup"></textarea>
        <label>Image (optional)</label>
        <div style="display:flex; gap:8px; align-items:center">
          <button class="btn" id="infoImgBtn">Choose image</button>
          <span class="hint" id="infoImgName">none</span>
        </div>
      </div>
      <div class="modal-foot">
        <button class="btn ghost" id="infoCancel">Cancel</button>
        <button class="btn primary" id="infoSave">Place point</button>
      </div>
    </div>
  </div>

  <!-- Export / embed -->
  <div class="modal-wrap" id="exportModal">
    <div class="modal">
      <header><h3>Export &amp; embed</h3><button class="btn ghost" id="exportClose">✕</button></header>
      <div class="mbody">
        <label>Embed shortcode (paste into any page or case study)</label>
        <input type="text" id="shortcodeOut" readonly>
        <label>Tour config (JSON)</label>
        <textarea id="jsonOut" class="tall" readonly></textarea>
      </div>
      <div class="modal-foot">
        <button class="btn" id="copyShortcode">Copy shortcode</button>
        <button class="btn" id="copyJson">Copy JSON</button>
        <button class="btn" id="downloadJson">Download tour.json</button>
        <button class="btn primary" id="downloadHtml">Download standalone .html</button>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"></div>
</div>
<?php wp_footer(); ?>
</body>
</html>
