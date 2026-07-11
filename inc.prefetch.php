<?php
/**
 * Link prefetch tuning + non-Chromium fallback.
 *
 * WordPress core 6.8 ships native "Speculative Loading" (the Speculation Rules
 * API), on by default: it emits a <script type="speculationrules"> prefetch
 * block for Chromium with `conservative` eagerness. That already covers ~86% of
 * our desktop traffic — so we do NOT re-emit our own rules block (that would be
 * a redundant duplicate). Instead this file does two small things:
 *
 *   1. Bump core's eagerness conservative -> moderate via the official
 *      `wp_speculation_rules_configuration` filter, so prefetch fires on hover
 *      (~200ms intent) instead of only on pointerdown. Mode stays `prefetch`,
 *      NOT prerender: prerender executes the target page early and could
 *      double-fire GTM/HubSpot events. Prefetch only warms the browser cache.
 *
 *   2. Add a tiny hover-prefetch fallback for non-Chromium desktop (Firefox
 *      ~7.5%, Safari ~5.6%), which the native API does not reach. It bails on
 *      any engine that supports Speculation Rules (core already handles those)
 *      and on any device without a fine hover pointer (mobile gets nothing).
 *
 * This replaces WP Rocket "Preload Links" (disabled separately), which was a
 * third, overlapping prefetch mechanism.
 *
 * Kill-switch: ?mfs_prefetch=0 disables BOTH core speculative loading and the
 * fallback for that request. Global off: define('MFS_PREFETCH', false).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * True when prefetch should be suppressed (global constant or per-request kill).
 */
function mfs_prefetch_disabled() {
    if (defined('MFS_PREFETCH') && MFS_PREFETCH === false) {
        return true;
    }
    if (isset($_GET['mfs_prefetch']) && $_GET['mfs_prefetch'] === '0') {
        return true;
    }
    return false;
}

// --- Tier 1: tune WordPress core native Speculative Loading -----------------
// Return null to fully disable when the kill-switch is active; otherwise bump
// eagerness to "moderate" (hover) while keeping the safe "prefetch" mode.
add_filter('wp_speculation_rules_configuration', function ($config) {
    if (mfs_prefetch_disabled()) {
        return null; // disables core's speculation rules output entirely
    }
    if (is_array($config)) {
        $config['mode']      = 'prefetch';   // never prerender (keeps tracking clean)
        $config['eagerness'] = 'moderate';   // hover intent, not just pointerdown
    }
    return $config;
});

// --- Tier 2: hover-prefetch fallback for non-Chromium desktop ---------------
add_action('wp_footer', function () {

    if (mfs_prefetch_disabled()) {
        return;
    }
    // Never in wp-admin, customizer preview, feeds, or for logged-in editors.
    if (is_admin() || is_feed() || is_preview() || is_user_logged_in()) {
        return;
    }
    ?>
<script id="mfs-prefetch-fallback">
(function(){
  try{
    if('HTMLScriptElement' in window && HTMLScriptElement.supports
       && HTMLScriptElement.supports('speculationrules')) return; // Chromium: core handles it
    if(!window.matchMedia || !matchMedia('(hover:hover) and (pointer:fine)').matches) return; // desktop only
  }catch(e){ return; }

  var start = function(){
    var done = {}, timer;
    var skip = /\/wp-admin\/|\/wp-login|\/wp-json\/|\/feed\/|\/embed\/|\/go\/|\/refer\/|\/recommend/;
    function prefetch(href){
      if(done[href]) return; done[href]=1;
      var l=document.createElement('link');
      l.rel='prefetch'; l.href=href; l.as='document';
      document.head.appendChild(l);
    }
    document.addEventListener('pointerover', function(e){
      var a=e.target && e.target.closest ? e.target.closest('a[href]') : null;
      if(!a) return;
      var u; try{ u=new URL(a.href, location.href); }catch(_){ return; }
      if(u.origin!==location.origin) return;      // same-origin only
      if(u.search) return;                        // no query strings
      if(u.href.split('#')[0]===location.href.split('#')[0]) return; // not current page
      if(skip.test(u.pathname)) return;
      if(a.rel && /nofollow/.test(a.rel)) return;
      if(a.hasAttribute('data-no-prefetch')) return;
      clearTimeout(timer);
      timer=setTimeout(function(){ prefetch(u.href); }, 180); // intent delay
    }, {passive:true});
    document.addEventListener('pointerout', function(){ clearTimeout(timer); }, {passive:true});
  };

  if('requestIdleCallback' in window){ requestIdleCallback(start); }
  else { setTimeout(start, 1200); }
})();
</script>
    <?php
}, 25);
