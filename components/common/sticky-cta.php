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

// Fallback when the Options field is empty: render our native <video> player
// DIRECTLY via the placeholder helper (no iframe for inc.video.php to convert),
// homepage showreel reel, muted bg, plays ONCE to the end (no loop). If an editor
// pastes a raw Bunny embed into Options instead, inc.video.php converts it the
// usual way. Override via Options → "Sticky CTA (Global)".
if ( $sc_video === '' && function_exists( 'mfs_video_placeholder' ) ) {
    $sc_video = mfs_video_placeholder(
        'e3ddaf1f-eb40-4f5f-8b7d-5c529bf12265',
        array(
            'mode'  => 'bg',
            'loop'  => false,
            'title' => 'Maverick Frame showreel',
        )
    );
}

// Sticky-CTA video always plays once (never loops), whatever the source. The
// helper fallback already sets loop=false; if the embed came from ACF Options as
// a raw Bunny iframe instead, force &mfsloop=0 into its URL so inc.video.php emits
// data-loop="0" on the converted <video>.
if ( $sc_video !== '' && strpos( $sc_video, 'mediadelivery.net/embed/' ) !== false && strpos( $sc_video, 'mfsloop' ) === false ) {
    $sc_video = preg_replace_callback(
        '#(//(?:iframe|player)\.mediadelivery\.net/embed/[0-9]+/[0-9a-fA-F-]{36})([^"\']*)#i',
        static function ( $m ) {
            $sep = ( strpos( $m[2], '?' ) === false ) ? '?' : '&';
            return $m[1] . $m[2] . $sep . 'mfsloop=0';
        },
        $sc_video
    );
}

if ( ! $sc_enabled || $sc_video === '' ) {
    return;
}

$sc_lang  = mfs_lang();
$sc_label = get_field( 'sticky_cta_label', 'options' );
if ( $sc_lang !== 'en' ) {
    // On non-English pages prefer the localized Options field; otherwise fall back to
    // the translated UI string (NOT the English base, which would leak EN on /de/ /es/).
    $sc_label_loc = get_field( 'sticky_cta_label_' . $sc_lang, 'options' );
    $sc_label = $sc_label_loc ? $sc_label_loc : mfs_t( 'Get In Touch', 'Contáctanos', 'Kontakt aufnehmen' );
}
if ( ! $sc_label ) {
    $sc_label = mfs_t( 'Get In Touch', 'Contáctanos', 'Kontakt aufnehmen' );
}
?>
<div class="sticky-cta js-sticky-cta is-armed">
    <div class="sticky-cta__media">
        <button class="sticky-cta__close js-sticky-cta-close" type="button" aria-label="Hide video">
            <svg viewBox="0 0 12 12" width="12" height="12" aria-hidden="true" focusable="false">
                <path d="M1 1l10 10M11 1L1 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </button>
        <?php echo $sc_video; // phpcs:ignore — trusted admin-entered Bunny embed ?>
    </div>
    <button class="sticky-cta__btn btn-main fill js-modal-open" data-modal="book" type="button">
        <span><?php echo esc_html( $sc_label ); ?></span>
        <?php echo inline_svg( 'icons/arrow-up.svg' ); ?>
    </button>
</div>
