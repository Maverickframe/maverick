<?php
/*
* Template Name: LP (bare)
* Template Post Type: page
*/

/**
 * Bare landing-page shell for /lp/ advertising landing pages.
 *
 * Renders the page body with NO mega-menu, NO footer navigation, NO sticky-CTA
 * and NO modals — only the essential <head> (get_header), Google Tag Manager,
 * the page content, a minimal Privacy-only footer, and wp_footer().
 *
 * Wiring done elsewhere by template detection:
 *   - inc.vite.php  : no per-type SCSS block bundle (flat assets/lp/lp.css instead)
 *   - inc.lp.php    : enqueues assets/lp/lp.css + forces Rank Math robots noindex,follow
 *
 * The LP body lives in the page's post_content as raw HTML; wpautop is removed
 * below so the markup passes through untouched. footer.php is intentionally NOT
 * used here (its modals + sticky-CTA must not render on an ad landing page), so
 * the GTM snippet + the deferred-menu buffer flush are mirrored inline.
 */
?>
<?php get_header(); ?>

<main class="mfs-lp">
    <?php remove_filter( 'the_content', 'wpautop' ); ?>
    <?php the_content(); ?>
</main>

<footer class="mfs-lp__foot">
    <p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> Maverick Frame Studio &middot;
        <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>" target="_blank" rel="noopener">Privacy Policy</a>
    </p>
</footer>

<?php // GTM + HubSpot now load via inc.delay.php on wp_footer() below
      // (self-hosted delayed loader) — no eager inline GTM mirror on LP anymore. ?>

<?php mfs_defer_menu_flush(); // close the output buffer header.php opened (inert if disabled) ?>
<?php wp_footer(); ?>
</body>
</html>
