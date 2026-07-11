<?php
/**
 * Link prefetch — self-hosted replacement for WP Rocket "Preload Links".
 *
 * Goal: make internal navigation feel instant without shipping Rocket's inline
 * helper. Two tiers, chosen by the browser itself:
 *
 *   1. Chromium (~86% of our desktop traffic) → native Speculation Rules API.
 *      A declarative <script type="speculationrules"> JSON block. Zero JS runs;
 *      the browser prefetches same-origin links on hover/pointerdown (moderate
 *      eagerness). We use `prefetch`, NOT `prerender`: prerender executes the
 *      target page early and could double-fire GTM/HubSpot events. Prefetch only
 *      warms the browser cache — tracking stays clean. Upgrade to prerender later
 *      only after verifying analytics doesn't double-count.
 *
 *   2. Non-Chromium desktop (Firefox ~7.5%, Safari ~5.6%) → tiny hover-prefetch
 *      fallback that injects <link rel=prefetch> on hover. Gated so Chromium
 *      never runs it (it has the declarative block) and touch devices never do
 *      (no hover / pointer:fine).
 *
 *   3. Mobile → nothing. Both tiers are gated out.
 *
 * Kill-switch: append ?mfs_prefetch=0 to any URL to disable on that request.
 * Global off: define('MFS_PREFETCH', false).
 *
 * Exclusions mirror the old Rocket Preload Links config (feeds, wp-json, embeds,
 * /go/ /refer/ /recommend redirects) plus wp-admin, login, query-string URLs,
 * rel=nofollow and any element tagged data-no-prefetch.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_footer', function () {

    // Global off-switch + per-request kill-switch.
    if (defined('MFS_PREFETCH') && MFS_PREFETCH === false) {
        return;
    }
    if (isset($_GET['mfs_prefetch']) && $_GET['mfs_prefetch'] === '0') {
        return;
    }

    // Never in wp-admin, customizer preview, feeds, or for logged-in editors
    // (they get the un-optimized page anyway and don't need speculative loads).
    if (is_admin() || is_feed() || is_preview() || is_user_logged_in()) {
        return;
    }

    // --- Tier 1: declarative Speculation Rules (Chromium) --------------------
    // source:"document" = watch the page's own links; moderate eagerness fires
    // on hover / pointerdown. href_matches "/*" restricts to same-origin paths.
    $rules = array(
        'prefetch' => array(
            array(
                'source'    => 'document',
                'eagerness' => 'moderate',
                'where'     => array(
                    'and' => array(
                        array('href_matches' => '/*'),
                        array('not' => array('href_matches' => '/wp-admin/*')),
                        array('not' => array('href_matches' => '/wp-login.php*')),
                        array('not' => array('href_matches' => '/wp-json/*')),
                        array('not' => array('href_matches' => '/feed/*')),
                        array('not' => array('href_matches' => '/*/feed/*')),
                        array('not' => array('href_matches' => '/*/embed/*')),
                        array('not' => array('href_matches' => '/go/*')),
                        array('not' => array('href_matches' => '/refer/*')),
                        array('not' => array('href_matches' => '/recommend*')),
                        // No query-string URLs (calculator/quiz state, tracking).
                        array('not' => array('href_matches' => '/*\?*')),
                        array('not' => array('selector_matches' => '[rel~="nofollow"]')),
                        array('not' => array('selector_matches' => '[data-no-prefetch]')),
                    ),
                ),
            ),
        ),
    );
    echo '<script type="speculationrules">'
        . wp_json_encode($rules)
        . '</script>' . "\n";

    // --- Tier 2: hover-prefetch fallback (non-Chromium desktop only) ---------
    // Bails immediately on any engine that supports Speculation Rules (Tier 1
    // already covers it) and on any device without a fine hover pointer.
    ?>
<script id="mfs-prefetch-fallback">
(function(){
  try{
    if('HTMLScriptElement' in window && HTMLScriptElement.supports
       && HTMLScriptElement.supports('speculationrules')) return; // Chromium: declarative
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

  // Attach off the critical path.
  if('requestIdleCallback' in window){ requestIdleCallback(start); }
  else { setTimeout(start, 1200); }
})();
</script>
    <?php
}, 25);
