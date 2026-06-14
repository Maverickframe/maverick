<?php
/**
 * Block: acf/pano-hero
 * Two-column hero: copy (eyebrow + highlighted title + description + button)
 * on the left, an interactive 360° tour (Photo Sphere Viewer) on the right.
 * The tour is rendered through the shared [pano_tour] mechanism in inc.tour.php,
 * so the viewer assets + blob-CSP fix auto-load. See mfs_tour_render().
 */
$eyebrow = get_field('eyebrow');
$title   = get_field('title');
$desc    = get_field('description');
$tour_id = (int) get_field('tour_id');
$btn     = get_field('button_text');
if (!$btn) { $btn = 'Expand fullscreen'; }
?>
<section class="pano-hero">
    <div class="container">
        <div class="pano-hero__main js-reveal">
            <div class="pano-hero__info">
                <?php if ($eyebrow): ?>
                    <p class="section-subtitle"><?php echo esc_html($eyebrow); ?></p>
                <?php endif; ?>
                <?php if ($title): ?>
                    <h2 class="pano-hero__title js-highlight text-highlight"><?php echo $title; ?></h2>
                <?php endif; ?>
                <?php if ($desc): ?>
                    <div class="pano-hero__desc p1"><?php echo wpautop($desc); ?></div>
                <?php endif; ?>
                <?php if ($tour_id): ?>
                    <button type="button" class="btn-main fill pano-hero__btn" data-pano-expand>
                        <?php echo esc_html($btn); ?>
                    </button>
                <?php endif; ?>
            </div>

            <div class="pano-hero__media">
                <?php
                    if ($tour_id && function_exists('mfs_tour_render')) {
                        echo mfs_tour_render($tour_id);
                    } elseif ($tour_id) {
                        echo do_shortcode('[pano_tour id="' . $tour_id . '"]');
                    } else {
                        echo '<div class="pano-hero__placeholder">Select a 3D tour in the block settings.</div>';
                    }
                ?>
            </div>
        </div>
    </div>
</section>
