<?php
/**
 * Self-hosted SVG upload + sanitize.
 *
 * Replaces the `svg-support` (Bodhi) plugin so it can be removed from the stack.
 * The plugin only ever did three admin-side things on this site (it enqueues
 * nothing on the front end, and the trusted-logo marquee renders svg attachments
 * as plain <img> — see components/blocks/trusted/trusted.php — which needs no
 * plugin): (1) allow .svg in the media library, (2) sanitize each upload, and
 * (3) keep the admin grid preview from breaking. This file reproduces all three.
 *
 * Sanitization uses enshrined/svg-sanitize (vendored via composer — the SAME
 * library safe-svg and svg-support use). vendor/autoload.php is already loaded
 * in inc.vite.php, so the class is available.
 *
 * Uploads are already gated by WP's `upload_files` capability, so only logged-in
 * editors/admins can trigger this — but every accepted svg is still scrubbed.
 *
 * Kill-switch: define('MFS_SVG_UPLOAD', false) in wp-config.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'MFS_SVG_UPLOAD' ) ) {
	define( 'MFS_SVG_UPLOAD', true );
}

if ( ! MFS_SVG_UPLOAD ) {
	return;
}

/**
 * 1) Allow .svg in the media library.
 */
add_filter( 'upload_mimes', function ( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
} );

/**
 * 2) Fix WP's real-mime detection so the upload isn't rejected.
 *
 * WP >= 4.7.1 runs a finfo check that returns text/plain or image/svg+xml
 * inconsistently for SVG, which can override the upload_mimes allowance and
 * block the file. When the extension is .svg and WP hasn't already resolved a
 * type, force the correct pair. We only trust the extension to SET the type
 * here; the actual bytes are scrubbed by the prefilter below before storage.
 */
add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes ) {
	if ( ! empty( $data['ext'] ) && ! empty( $data['type'] ) ) {
		return $data;
	}

	$parts = explode( '.', $filename );
	$ext   = strtolower( (string) end( $parts ) );

	if ( 'svg' === $ext ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}, 10, 4 );

/**
 * 3) Sanitize every uploaded SVG in place, before WP stores it.
 *
 * Runs on the temp file. If the payload is not valid XML / can't be sanitized,
 * the upload is rejected with a clear error rather than silently stored.
 */
add_filter( 'wp_handle_upload_prefilter', function ( $file ) {
	$name = isset( $file['name'] ) ? $file['name'] : '';
	$type = isset( $file['type'] ) ? $file['type'] : '';

	$is_svg = ( 'image/svg+xml' === $type )
		|| ( '' !== $name && 'svg' === strtolower( pathinfo( $name, PATHINFO_EXTENSION ) ) );

	if ( ! $is_svg ) {
		return $file;
	}

	if ( ! class_exists( '\enshrined\svgSanitize\Sanitizer' ) ) {
		$file['error'] = __( 'SVG could not be sanitized (sanitizer unavailable). Upload blocked for safety.', 'maverickframe' );
		return $file;
	}

	$tmp = isset( $file['tmp_name'] ) ? $file['tmp_name'] : '';
	if ( '' === $tmp || ! is_readable( $tmp ) ) {
		$file['error'] = __( 'SVG upload could not be read for sanitizing.', 'maverickframe' );
		return $file;
	}

	$dirty = file_get_contents( $tmp );
	if ( false === $dirty || '' === trim( (string) $dirty ) ) {
		$file['error'] = __( 'The SVG file appears to be empty.', 'maverickframe' );
		return $file;
	}

	$sanitizer = new \enshrined\svgSanitize\Sanitizer();
	$sanitizer->removeRemoteReferences( true ); // strip external hrefs / entity fetches
	$sanitizer->minify( true );

	$clean = $sanitizer->sanitize( $dirty );

	if ( false === $clean || '' === trim( (string) $clean ) ) {
		$file['error'] = __( 'This SVG could not be sanitized safely and was not uploaded.', 'maverickframe' );
		return $file;
	}

	// Belt-and-suspenders over enshrined: <style> contents pass through as-is, so
	// strip CSS @import rules and neutralize remote url() references (exfil /
	// remote-load vectors). Local url(#id) refs used by gradients/filters and
	// inline url(data:...) are left intact.
	$clean = preg_replace( '/@import\b[^;]*;?/i', '', (string) $clean );
	$clean = preg_replace( '#url\(\s*([\'"]?)\s*(?:https?:)?//#i', 'url($1#', $clean );

	if ( false === file_put_contents( $tmp, $clean ) ) {
		$file['error'] = __( 'The sanitized SVG could not be written.', 'maverickframe' );
		return $file;
	}

	return $file;
} );

/**
 * 4) Keep the admin media-library grid/list previews sized correctly.
 *
 * SVGs carry no intrinsic pixel dimensions in the attachment metadata, so the
 * grid thumbnail and the attachment-details preview render at 0/oversized.
 * Constrain them to the thumbnail box. Admin-only, tiny.
 */
add_action( 'admin_head', function () {
	echo '<style id="mfs-svg-admin">'
		. '.media-icon img[src$=".svg"],'
		. 'img.attachment-thumbnail[src$=".svg"],'
		. '.attachment .thumbnail img[src$=".svg"],'
		. '.media-modal .details-image[src$=".svg"]{width:100%;height:auto;max-width:100%;}'
		. '</style>';
} );
