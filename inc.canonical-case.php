<?php
/**
 * Lowercase URL canonicalization (301).
 *
 * PROBLEM
 * MySQL collation is case-insensitive, so a request for
 *   /solutions/3D-rendering-for-manufacturing/
 * matches the row whose post_name is `3d-rendering-for-manufacturing`,
 * WP_Query resolves it, and the page renders 200 at the mixed-case address.
 * The <link rel=canonical> points at the lowercase URL, but the mixed-case
 * address stays live — a real duplicate of every page on the site.
 *
 * WHY WP CORE DOES NOT FIX THIS
 * redirect_canonical() is NOT suppressed here (verified on prod 30.07.2026:
 * it sits on template_redirect prio 10; the only theme-side suppression is
 * the narrow `parse_query` one for the /blog/ load-more listing, and Rank Math
 * only filters it inside the sitemap module). It simply never rebuilds the
 * permalink for a *successful* singular request: wp-includes/canonical.php
 * line ~121 gates that branch on `$wp_query->post_count < 1`. Everything after
 * it only normalizes slashes, pagination, feeds and punctuation — case is
 * never touched. So there is nothing to "turn back on"; the case class has to
 * be handled explicitly.
 *
 * WHAT THIS DOES
 * Early (init, prio 0 — before Rank Math Redirections) 301s any front-end
 * GET/HEAD whose *path* contains an ASCII uppercase letter to the lowercase
 * path. The query string is re-attached VERBATIM: UTM/gclid/fbclid values are
 * case-significant and lowercasing them would corrupt attribution.
 *
 * Replaces the manual exact-301 rows in wp_rank_math_redirections (which stay
 * in place as belt-and-braces + hit statistics).
 *
 * Kill-switch: ?mfs_lcurl=0 per request. Global off: define('MFS_LOWERCASE_URLS', false).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Paths that must never be case-folded.
 *
 * - wp-admin / wp-login / wp-cron / xmlrpc / wp-json: back end and APIs.
 * - wp-content / wp-includes: static assets. Case IS significant on disk
 *   (IMG_1234.jpg). nginx serves these without touching PHP, but the guard
 *   stays in case a request ever falls through to WordPress.
 * - feed / sitemap / robots / ads / .well-known: machine endpoints.
 * - utm_: malformed crawler URLs with query args pasted into the path. The
 *   mfs-utm-trap mu-plugin logs those as 404s; redirecting first would blind it.
 */
function mfs_lcurl_is_excluded_path($path) {
    static $prefixes = array(
        '/wp-admin',
        '/wp-includes',
        '/wp-content',
        '/wp-json',
        '/wp-login.php',
        '/wp-cron.php',
        '/wp-signup.php',
        '/wp-activate.php',
        '/wp-trackback.php',
        '/wp-comments-post.php',
        '/xmlrpc.php',
        '/.well-known',
    );

    foreach ($prefixes as $prefix) {
        if (0 === strpos($path, $prefix)) {
            return true;
        }
    }

    // Feeds, sitemaps and anything crawler-facing, anywhere in the path.
    if (preg_match('#(^|/)feed/?$|sitemap|robots\.txt|ads\.txt|favicon\.#i', $path)) {
        return true;
    }

    // Query args pasted into the path — leave them to the UTM trap.
    if (false !== stripos($path, 'utm_')) {
        return true;
    }

    // Anything that looks like a file (dot in the last segment): extensions and
    // upload filenames are case-significant.
    $last = substr($path, strrpos($path, '/') + 1);
    if ('' !== $last && false !== strpos($last, '.')) {
        return true;
    }

    return false;
}

/**
 * ASCII-only lowercase.
 *
 * strtr() with an explicit map instead of strtolower(): it is locale-proof and
 * byte-safe, so multibyte UTF-8 sequences (all bytes >= 0x80) pass through
 * untouched. Percent-encoded octets get their hex digits lowered, which is
 * lossless — RFC 3986 defines hex escapes as case-insensitive and core does the
 * same normalization in redirect_canonical().
 */
function mfs_lcurl_lower($path) {
    return strtr($path, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
}

function mfs_lcurl_disabled() {
    if (defined('MFS_LOWERCASE_URLS') && MFS_LOWERCASE_URLS === false) {
        return true;
    }
    if (isset($_GET['mfs_lcurl']) && $_GET['mfs_lcurl'] === '0') {
        return true;
    }
    return false;
}

/**
 * 301 mixed-case front-end URLs to their lowercase twin.
 */
function mfs_lcurl_redirect() {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (defined('WP_CLI') && WP_CLI) {
        return;
    }
    // REST_REQUEST is not defined yet at `init`; catch both routing styles.
    if ((defined('REST_REQUEST') && REST_REQUEST) || isset($_GET['rest_route'])) {
        return;
    }
    if (mfs_lcurl_disabled()) {
        return;
    }

    $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper((string) $_SERVER['REQUEST_METHOD']) : '';
    if ('GET' !== $method && 'HEAD' !== $method) {
        return;
    }

    $uri = isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '';
    if ('' === $uri) {
        return;
    }

    // Some proxies send the absolute form (http://host/path?q).
    if (false !== strpos($uri, '://')) {
        $parts = wp_parse_url($uri);
        $uri   = (isset($parts['path']) ? $parts['path'] : '/')
               . (isset($parts['query']) ? '?' . $parts['query'] : '');
    }

    $qpos  = strpos($uri, '?');
    $path  = (false === $qpos) ? $uri : substr($uri, 0, $qpos);
    $query = (false === $qpos) ? '' : substr($uri, $qpos); // keeps the '?', verbatim

    // Nothing to do — and this is also the loop guard.
    if (!preg_match('/[A-Z]/', $path)) {
        return;
    }
    if (mfs_lcurl_is_excluded_path($path)) {
        return;
    }

    $target = mfs_lcurl_lower($path);

    // Never emit a protocol-relative location.
    $target = '/' . ltrim($target, '/');

    // Permalink structure is /%postname%/, so core would add the trailing slash
    // on the next request anyway. Doing it here keeps every hit to a single hop
    // instead of 301 -> 301. Self-disables if the structure ever loses its slash.
    $structure = get_option('permalink_structure');
    if (is_string($structure) && '/' === substr($structure, -1) && '/' !== substr($target, -1)) {
        $target .= '/';
    }

    if ($target === $path) {
        return;
    }

    wp_safe_redirect($target . $query, 301);
    exit;
}
add_action('init', 'mfs_lcurl_redirect', 0);
