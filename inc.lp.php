<?php
/**
 * /lp/ advertising landing pages — support wiring for the "LP (bare)" template.
 *
 *  1. Enqueues the flat, self-contained stylesheet assets/lp/lp.css (scoped to
 *     .mfs-lp) only on pages using the bare template. No Vite rebuild needed —
 *     a plain CSS file in the theme ships as-is.
 *  2. Forces Rank Math robots to noindex,follow as a code-level safety net on top
 *     of the per-page Rank Math setting (Rank Math stays the single source of the
 *     one robots tag — we don't emit a second one).
 *
 * The bundle suppression (no page.scss / block bundle on this template) lives in
 * inc.vite.php's mfs_page_bundle_key().
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

const MFS_LP_TEMPLATE = 'templates/page-lp-bare.php';

/**
 * Flat LP stylesheet — self-contained (own reset + design system + components),
 * scoped under .mfs-lp so it can't leak into the rest of the site.
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! is_page_template( MFS_LP_TEMPLATE ) ) {
        return;
    }
    $rel  = '/assets/lp/lp.css';
    $path = get_template_directory() . $rel;
    if ( ! file_exists( $path ) ) {
        return;
    }
    wp_enqueue_style( 'mfs-lp', get_template_directory_uri() . $rel, array(), filemtime( $path ) );
}, 20 );

/**
 * Robots safety net: even if the per-page Rank Math setting is missed, any page on
 * the bare LP template is forced to noindex,follow. Modifies Rank Math's single
 * robots tag rather than printing a second one.
 */
add_filter( 'rank_math/frontend/robots', function ( $robots ) {
    if ( is_page_template( MFS_LP_TEMPLATE ) ) {
        $robots['index']  = 'noindex';
        $robots['follow'] = 'follow';
    }
    return $robots;
} );
