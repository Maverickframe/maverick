<?php
/**
 * Global sticky CTA card (desktop only).
 *
 * A small video window (Bunny embed) + "Get In Touch" button that stays pinned
 * bottom-right while scrolling. Rendered site-wide from footer.php.
 *
 * Suppressed automatically on pages that already render the in-hero `.hero__cta`
 * card (e.g. service pages) — those set $GLOBALS['mfs_hero_cta_rendered'] = true
 * in components/blocks/hero-services/hero-services.php, so we never show two.
 *
 * Content (Cycle 2): values live in ACF Options — "Sticky CTA (Global)".
 *   sticky_cta_enabled (true_false) · sticky_cta_video (textarea, raw Bunny embed)
 *   sticky_cta_label / sticky_cta_label_es (text).
 */

if ( ! empty( $GLOBALS['mfs_hero_cta_rendered'] ) ) {
    return;
}

if ( ! function_exists( 'get_field' ) ) {
    return;
}

// Only render on new-design pages — that's where the base CSS bundle (which
// styles .sticky-cta) is loaded. Legacy pages load main.scss and would show
// the card unstyled.
if ( function_exists( 'isNewDesign' ) && ! isNewDesign() ) {
    return;
}

$sc_enabled = get_field( 'sticky_cta_enabled', 'options' );
// If the field was never saved, default to ON.
if ( $sc_enabled === null ) {
    $sc_enabled = true;
}

$sc_video = trim( (string) get_field( 'sticky_cta_video', 'options' ) );

if ( ! $sc_enabled || $sc_video === '' ) {
    return;
}

$sc_is_es = function_exists( 'pll_current_language' ) && pll_current_language() === 'es';
$sc_label = get_field( 'sticky_cta_label', 'options' );
if ( $sc_is_es ) {
    $sc_label_es = get_field( 'sticky_cta_label_es', 'options' );
    if ( $sc_label_es ) {
        $sc_label = $sc_label_es;
    }
}
if ( ! $sc_label ) {
    $sc_label = mfs_t( 'Get In Touch', 'Contáctanos' );
}
?>
<div class="sticky-cta js-sticky-cta">
    <div class="sticky-cta__media">
        <?php echo $sc_video; // phpcs:ignore — trusted admin-entered Bunny embed ?>
    </div>
    <button class="sticky-cta__btn btn-main fill js-modal-open" data-modal="book" type="button">
        <span><?php echo esc_html( $sc_label ); ?></span>
        <?php echo inline_svg( 'icons/arrow-up.svg' ); ?>
    </button>
</div>
