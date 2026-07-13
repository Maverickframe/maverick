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
	// ON. The blocker (edge CSP lacked `blob:` for media-src/worker-src) is fixed:
	// the CSP is set in the site .htaccess (LiteSpeed `Header set`, which beats the
	// PHP header override), and `blob:` was added to default-src + media-src +
	// worker-src there on 2026-07-05, so hls.js MSE playback works. Staged rollout
	// continues via MFS_VIDEO_ONLY_IDS below; set it to '' to go site-wide.
	define( 'MFS_VIDEO_HLS', true );
}
if ( ! defined( 'MFS_VIDEO_ONLY_IDS' ) ) {
	// Comma-separated singular post IDs to scope the conversion to (staged rollout).
	// Empty string = SITE-WIDE: convert every Bunny embed on every view. Verified on
	// case 15462 first, now rolled out site-wide (2026-07-05) — no Bunny iframe reaches
	// the browser anywhere. Set to specific IDs again to re-scope if ever needed.
	define( 'MFS_VIDEO_ONLY_IDS', '' );
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

		// Loop: on by default; a Bunny embed with loop=false (or &mfsloop=0) plays
		// once to the end and freezes. Only bg/hover actually loop — click never does.
		$loop = ! isset( $q['loop'] ) || filter_var( $q['loop'], FILTER_VALIDATE_BOOLEAN );
		if ( isset( $q['mfsloop'] ) ) {
			$loop = filter_var( $q['mfsloop'], FILTER_VALIDATE_BOOLEAN );
		}

		// Title (a11y + GA4 label) from the iframe's own title attribute, if any.
		$title = '';
		if ( preg_match( '#title\s*=\s*(["\'])(.*?)\1#i', $attrs, $t ) ) {
			$title = trim( $t[2] );
		}

		// Poster override: only a CLICK hero borrows the page's featured image
		// (crisp, hand-picked). For hover/bg — and especially the gallery, where
		// many videos share one page — each keeps its OWN thumbnail (the helper
		// defaults to the static thumbnail_1.jpg), else every tile shows the cover.
		$poster = '';
		if ( $mode === 'click' && is_singular() ) {
			$qid = get_queried_object_id();
			if ( $qid && has_post_thumbnail( $qid ) ) {
				$feat = get_the_post_thumbnail_url( $qid, 'large' );
				if ( $feat ) {
					$poster = $feat;
				}
			}
		}

		return mfs_video_placeholder(
			$guid,
			array(
				'mode'   => $mode,
				'loop'   => $loop,
				'title'  => $title,
				'poster' => $poster,
			)
		);
	}
}

if ( ! function_exists( 'mfs_video_placeholder' ) ) {
	/**
	 * Build the <div class="mfs-video"> placeholder that mfs-video.js hydrates —
	 * directly from a Bunny GUID, so a component can render our native player
	 * WITHOUT ever printing a Bunny <iframe> for the output-buffer to catch back.
	 * Same markup the iframe converter emits, single source of truth for both.
	 *
	 * @param string $guid Bunny video GUID (36-char).
	 * @param array  $args {
	 *     @type string $mode   bg|click|hover. Default bg.
	 *     @type bool   $loop   Loop playback. Default true. bg/hover only.
	 *     @type string $title  a11y + GA4 label. Default ''.
	 *     @type string $poster Poster URL override. Default static thumbnail_1.jpg.
	 * }
	 * @return string Placeholder HTML, or '' if the GUID is unusable.
	 */
	function mfs_video_placeholder( $guid, $args = array() ) {
		$guid = strtolower( preg_replace( '/[^0-9a-fA-F-]/', '', (string) $guid ) );
		if ( strlen( $guid ) !== 36 ) {
			return '';
		}

		$mode  = ( isset( $args['mode'] ) && in_array( $args['mode'], array( 'bg', 'click', 'hover' ), true ) ) ? $args['mode'] : 'bg';
		$loop  = ! array_key_exists( 'loop', $args ) || (bool) $args['loop'];
		$title = isset( $args['title'] ) ? trim( (string) $args['title'] ) : '';

		$pz              = MFS_BUNNY_PULLZONE;
		$src             = 'https://' . $pz . '/' . $guid . '/playlist.m3u8';
		$poster_fallback = 'https://' . $pz . '/' . $guid . '/preview.webp';
		// Static thumbnail_1.jpg by default (~250 KB); the animated preview.webp
		// (1.5-3 MB) stays only as the canvas-frozen fallback in setPoster().
		$poster = ( ! empty( $args['poster'] ) ) ? $args['poster'] : 'https://' . $pz . '/' . $guid . '/thumbnail_1.jpg';

		return sprintf(
			'<div class="mfs-video js-mfs-video mfs-video--%1$s" data-guid="%2$s" data-src="%3$s" data-poster="%4$s" data-poster-fallback="%5$s" data-mode="%1$s" data-loop="%6$s"%7$s></div>',
			esc_attr( $mode ),
			esc_attr( $guid ),
			esc_url( $src ),
			esc_url( $poster ),
			esc_url( $poster_fallback ),
			$loop ? '1' : '0',
			$title !== '' ? ' data-title="' . esc_attr( $title ) . '"' : ''
		);
	}
}
