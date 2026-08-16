<?php
/**
 * inc.delay.php — self-hosted "delay third-party JS until first interaction".
 *
 * Replaces WP Rocket's "Delay JS" for the only third-party script the site still
 * loads: Google Tag Manager. Vanilla JS, ~1 KB, no plugin. Fires it on the FIRST
 * user interaction (scroll / mousemove / touch / key / pointer / click) OR after
 * a timeout fallback.
 * The gate is idempotent (fires once) and self-removes its listeners.
 *
 * 2026-08-16: the HubSpot tracking loader (js-eu1.hs-scripts.com/<portal>.js) was
 * removed from here. HubSpot is being retired — its portal stopped accepting new
 * contacts on 2026-08-15 — and the loader only pulled in hs-analytics, hs-banner
 * and the __ptq.gif beacon plus the `hubspotutk` cookie. Nothing on the site read
 * any of it: leads are delivered by forms/notify.php (journal + email), and the
 * Google Ads remarketing lists are fed by our own first-party tag, not by HubSpot.
 * ⚠️ The lead path is a different thing entirely and is very much alive:
 * forms/lead.php → forms/lead-dispatch.php → journal, studio email, our CRM.
 * Nothing in this file may be taken as licence to touch it.
 *
 * IMPORTANT — what the fallback actually costs (measured 10.08.2026):
 * a visitor who leaves BEFORE the fallback fires without ever touching the page
 * is never measured at all: no GA4 pageview at all. On mobile
 * there is no `mousemove`, so a read-and-back visit produces zero events. With
 * the default 10 s fallback this silently dropped ~35% of Google Ads clicks
 * (Ads clicks vs GA4 sessions: Display 27%, cold Search ~60%, engaged UK ~80%),
 * which repeatedly got misread as "half the clicks never reach the site".
 *
 * PAID FAST PATH: when the URL carries a paid click id (gclid / gbraid / wbraid /
 * msclkid) the fallback drops to MFS_DELAY_PAID_MS. The check is done in JS, not
 * PHP, on purpose — the served HTML stays byte-identical for every visitor, so
 * edge caching is unaffected and no per-gclid cache entries are created.
 * Lighthouse / PSI lab runs request URLs without a click id, so they keep the
 * full 10 s delay and the score is untouched.
 *
 * Why delaying loses nothing (once the script does load): GTM events pushed to
 * window.dataLayer before gtm.js evaluates are queued (dataLayer is a plain
 * array) and replayed on load. The GTM <noscript> iframe stays in header.php
 * for the JS-off case.
 *
 * GTM is loaded from edge.maverickframe.com — our own self-hosted server-side
 * GTM (sGTM), not googletagmanager.com and no longer the gw.maverickframe.com
 * Cloudflare Worker. That server also receives the GA4 hits and issues the
 * first-party FPID / FPGCLAW cookies over Set-Cookie, which is what keeps a
 * gclid alive past Safari ITP's 7-day cap on JS-written cookies.
 * Rollback: swap the URL back to https://gw.maverickframe.com/metrics/gtm.js
 * AND revert server_container_url in the GTM web container — both, or hits and
 * scripts end up on different hosts.
 *
 * Because GTM is injected here via createElement (not present in the
 * HTML buffer), WP Rocket's "Delay JS" cannot re-catch them — so once this loader
 * is live and excluded from delay, WP Rocket's Delay JS can be turned off.
 *
 * Kill-switch: ?mfs_delay=0 (per-request) or define('MFS_DELAY', false) (global)
 * → load immediately, no gating (for debugging / PSI measurement).
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'MFS_GTM_ID' ) )            define( 'MFS_GTM_ID', 'GTM-T4JS5BJV' );
if ( ! defined( 'MFS_DELAY_FALLBACK_MS' ) ) define( 'MFS_DELAY_FALLBACK_MS', 10000 );
if ( ! defined( 'MFS_DELAY_PAID_MS' ) )     define( 'MFS_DELAY_PAID_MS', 800 );

add_action( 'wp_footer', 'mfs_print_delayed_thirdparty', 20 );

function mfs_print_delayed_thirdparty() {
	// No tracking in local Vite dev (mirrors the old footer.php guards).
	if ( defined( 'IS_VITE_DEVELOPMENT' ) && IS_VITE_DEVELOPMENT == true ) {
		return;
	}

	$immediate = ( isset( $_GET['mfs_delay'] ) && $_GET['mfs_delay'] === '0' )
	          || ( defined( 'MFS_DELAY' ) && MFS_DELAY === false );

	$gtm = wp_json_encode( MFS_GTM_ID );
	$fb  = $immediate ? 0 : (int) MFS_DELAY_FALLBACK_MS;
	$pfb = $immediate ? 0 : (int) MFS_DELAY_PAID_MS;
	?>
<script id="mfs-delay">
(function(){
  var GTM=<?php echo $gtm; ?>,FB=<?php echo $fb; ?>,PFB=<?php echo $pfb; ?>,fired=false;
  // Paid click landing? Measure it almost immediately — a bounce before the
  // default fallback would otherwise never be recorded at all.
  try{
    if(PFB<FB&&/[?&](gclid|gbraid|wbraid|msclkid)=/i.test(window.location.search)){FB=PFB;}
  }catch(e){}
  var evts=['scroll','mousemove','touchstart','keydown','pointerdown','click'];
  function load(){
    if(fired){return;}
    fired=true;
    for(var i=0;i<evts.length;i++){window.removeEventListener(evts[i],load,{passive:true});}
    window.dataLayer=window.dataLayer||[];
    window.dataLayer.push({'gtm.start':Date.now(),event:'gtm.js'});
    var g=document.createElement('script');
    g.async=true;g.src='https://edge.maverickframe.com/gtm.js?id='+GTM;
    document.head.appendChild(g);
  }
  for(var i=0;i<evts.length;i++){window.addEventListener(evts[i],load,{passive:true});}
  setTimeout(load,FB);
})();
</script>
<?php
}
