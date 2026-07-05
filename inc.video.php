<?php
/**
 * inc.video.php — replace Bunny Stream iframe embeds with a native <video>+hls.js
 * player theme-side, in a single output-buffer pass. Player JS: src/js/components/mfs-video.js.
 *
 * WHY: the Bunny iframe ships its own player whose console logs (fatal HLS retries,
 * 500b.jpg preview sprites — third-party code in Bunny's iframe) count against PSI
 * Best-Practices, force its default controls, and can't do hover-to-play. Bunny's
 * HLS manifest is directly reachable from our domain (CORS open, no token — verified
 * 2026-07-05), so we point our own <video> at the same manifest and drop the iframe.
 * The video stays hosted on Bunny (we don't touch hosting/traffic).
 *
 * SCOPE: one preg_replace_callback over the whole page HTML catches EVERY Bunny
 * embed — post_content (~63 posts), ACF-rendered blocks (showreel, solution-intro,
 * sticky-cta) and any future paste — so "no iframe reaches the browser" holds
 * literally, site-wide, without editing 63+ records by hand. Schema JSON-LD
 * embedUrl is a plain string (not an <iframe>), so it is left untouched and the
 * VideoObject stays valid.
 *
 * MODE (playback behaviour) is inferred from the Bunny params, or forced with an
 * explicit &mfsmode=bg|click|hover in the embed URL:
 *   autoplay=true & muted=true  -> bg    (muted autoplay loop, no controls)
 *   otherwise                   -> click (poster + click-to-play w/ sound + controls)
 *   &mfsmode=hover              -> hover (muted loop preview on hover) — opt-in
 *
 * Kill switch / A-B on any URL: ?mfs_video=0 forces OFF (original iframe), ?mfs_video=1 ON.
 * Global off: set MFS_VIDEO_HLS to false and redeploy.
 *
 * NOTE (Referrer-Policy): Bunny "Block direct URL file access" is ON with our domain
 * in allowed referrers, so the browser must send a Referer to b-cdn.net. Keep the
 * site Referrer-Policy at strict-origin-when-cross-origin (default) — NOT no-referrer.
 * Add any new video domain to Bunny's allowed referrers.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'MFS_VIDEO_HLS' ) ) {
	define( 'MFS_VIDEO_HLS', true );
}
if ( ! defined( 'MFS_VIDEO_ONLY_IDS' ) ) {
	// Comma-separated singular post IDs to scope the conversion to (staged rollout).
	// Empty string = site-wide (convert every Bunny embed everywhere). While rolling
	// out we point this at ONE case/service page; the rest of the site keeps its
	// original Bunny iframes untouched. Flip to '' to go site-wide.
	define( 'MFS_VIDEO_ONLY_IDS', '15462' );
}
if ( ! defined( 'MFS_BUNNY_LIBRARY' ) ) {
	define( 'MFS_BUNNY_LIBRARY', '655216' );
}
if ( ! defined( 'MFS_BUNNY_PULLZONE' ) ) {
	// Bunny Stream CDN hostname for library 655216 (HLS manifest + thumbnail live here).
	define( 'MFS_BUNNY_PULLZONE', 'vz-2d099666-772.b-cdn.net' );
}

if ( ! function_exists( 'mfs_video_enabled' ) ) {
	function mfs_video_enabled() {
		// Per-request override / kill switch: ?mfs_video=0 forces OFF, ?mfs_video=1 ON.
		if ( isset( $_GET['mfs_video'] ) ) {
			return $_GET['mfs_video'] === '1';
		}
		return (bool) MFS_VIDEO_HLS;
	}
}

/**
 * Open a page-wide output buffer after routing. `template_redirect` never fires
 * for admin, REST or admin-ajax, so only front-end HTML is captured. Feeds, robots
 * and sitemaps carry no <iframe>, so the callback no-ops on them regardless.
 */
if ( ! function_exists( 'mfs_video_active_here' ) ) {
	/**
	 * Whether the player conversion should run for the CURRENT front-end request.
	 * Shared by the output buffer and the CSP override so they stay in lock-step.
	 */
	function mfs_video_active_here() {
		if ( is_admin() || is_feed() || is_robots() ) {
			return false;
		}
		if ( ! mfs_video_enabled() ) {
			return false;
		}
		// Staged rollout: when MFS_VIDEO_ONLY_IDS is set, only the listed singular
		// page(s) convert. Everywhere else the original Bunny iframe is served.
		$only = array_filter( array_map( 'trim', explode( ',', (string) MFS_VIDEO_ONLY_IDS ) ) );
		if ( ! empty( $only ) ) {
			if ( ! is_singular() || ! in_array( (string) get_queried_object_id(), $only, true ) ) {
				return false;
			}
		}
		return true;
	}
}

if ( ! function_exists( 'mfs_video_ob_start' ) ) {
	function mfs_video_ob_start() {
		if ( ! mfs_video_active_here() ) {
			return;
		}
		// CSP: our <video> plays through hls.js, which uses Media Source Extensions
		// — a `blob:` object URL as the media source and a `blob:`-created Web
		// Worker for demuxing. The site's base CSP allows `blob:` for frame-src
		// (the old Bunny iframe) but NOT for media-src/worker-src, so MSE playback
		// is refused (video error 4 "no supported sources"). Re-send a CSP that
		// adds `blob:` to default-src + media-src + worker-src, same pattern as the
		// tour builder's override (inc.tour.php). Done here at template_redirect —
		// after the main query is parsed (conditionals reliable) but before any
		// template output, so headers can still be sent.
		// NOTE: if the base CSP is enforced at the edge (Cloudflare/Nginx) and wins
		// the intersection, this PHP override won't take effect — then `blob:` must
		// be added to media-src + worker-src at the host level.
		if ( ! headers_sent() ) {
			header( "Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; media-src 'self' https: data: blob:; worker-src 'self' blob:; frame-src 'self' https: blob:;" );
		}
		ob_start( 'mfs_video_convert_html' );
	}
	add_action( 'template_redirect', 'mfs_video_ob_start', 0 );
}

if ( ! function_exists( 'mfs_video_convert_html' ) ) {
	function mfs_video_convert_html( $html ) {
		// Fast bail: nothing to do unless a Bunny embed is present in this buffer.
		if ( strpos( $html, 'mediadelivery.net/embed/' ) === false ) {
			return $html;
		}

		// Match a whole <iframe …>…</iframe> (or self-terminated). The attribute
		// blob is inspected in the callback, so attribute order is irrelevant and
		// any non-Bunny iframe is returned byte-for-byte unchanged.
		$pattern = '#<iframe\b([^>]*)>\s*(?:</iframe>)?#i';

		$out = preg_replace_callback( $pattern, 'mfs_video_replace_iframe', $html );

		// preg_replace_callback returns null on failure (e.g. PCRE backtrack limit);
		// never emit a blank page — fall back to the original buffer.
		return ( $out === null ) ? $html : $out;
	}
}

if ( ! function_exists( 'mfs_video_replace_iframe' ) ) {
	function mfs_video_replace_iframe( $m ) {
		$attrs = $m[1];
		$lib   = preg_quote( MFS_BUNNY_LIBRARY, '#' );

		// Only touch Bunny Stream embeds for our library; otherwise pass through.
		if ( ! preg_match(
			'#src\s*=\s*(["\'])\s*(?:https?:)?//(?:iframe|player)\.mediadelivery\.net/embed/' . $lib . '/([0-9a-fA-F-]{36})([^"\']*)\1#i',
			$attrs,
			$s
		) ) {
			return $m[0];
		}

		$guid  = strtolower( $s[2] );
		$query = html_entity_decode( $s[3], ENT_QUOTES ); // &amp; -> &

		parse_str( ltrim( $query, '?' ), $q );

		// Mode: explicit &mfsmode= wins, else infer from Bunny playback params.
		$mode = '';
		if ( ! empty( $q['mfsmode'] ) && in_array( $q['mfsmode'], array( 'bg', 'click', 'hover' ), true ) ) {
			$mode = $q['mfsmode'];
		} else {
			$autoplay = isset( $q['autoplay'] ) && filter_var( $q['autoplay'], FILTER_VALIDATE_BOOLEAN );
			$muted    = isset( $q['muted'] ) && filter_var( $q['muted'], FILTER_VALIDATE_BOOLEAN );
			$mode     = ( $autoplay && $muted ) ? 'bg' : 'click';
		}

		// Title (a11y + GA4 label) from the iframe's own title attribute, if any.
		$title = '';
		if ( preg_match( '#title\s*=\s*(["\'])(.*?)\1#i', $attrs, $t ) ) {
			$title = trim( $t[2] );
		}

		$pz  = MFS_BUNNY_PULLZONE;
		$src = 'https://' . $pz . '/' . $guid . '/playlist.m3u8';

		// Poster: Bunny's default thumbnail.jpg is NOT generated for this library
		// (404); only the animated preview.webp exists, and it's a soft 320x180.
		// So prefer the page's own featured image (crisp, static, hand-picked) on
		// singular views, and fall back to Bunny's preview.webp elsewhere. The JS
		// swaps to the preview if the featured image ever fails to load.
		$poster_fallback = 'https://' . $pz . '/' . $guid . '/preview.webp';
		$poster          = $poster_fallback;
		if ( is_singular() ) {
			$qid = get_queried_object_id();
			if ( $qid && has_post_thumbnail( $qid ) ) {
				$feat = get_the_post_thumbnail_url( $qid, 'large' );
				if ( $feat ) {
					$poster = $feat;
				}
			}
		}

		return sprintf(
			'<div class="mfs-video js-mfs-video mfs-video--%1$s" data-guid="%2$s" data-src="%3$s" data-poster="%4$s" data-poster-fallback="%5$s" data-mode="%1$s"%6$s></div>',
			esc_attr( $mode ),
			esc_attr( $guid ),
			esc_url( $src ),
			esc_url( $poster ),
			esc_url( $poster_fallback ),
			$title !== '' ? ' data-title="' . esc_attr( $title ) . '"' : ''
		);
	}
}
