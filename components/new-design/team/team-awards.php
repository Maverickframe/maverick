<?php
    // Awards: reuse the exact "Awards & recognition" block from the homepage so the
    // team page matches the front page 1:1 (badges, filter, dark logos). Pick the
    // front page of the current language so EN/ES each mirror their own homepage.
    $front_id = (int) get_option('page_on_front');
    if ( function_exists('pll_get_post') ) {
        $translated_front = pll_get_post($front_id);
        if ( $translated_front ) { $front_id = $translated_front; }
    }

    $awards_block = null;
    if ( $front_id ) {
        $stack = parse_blocks( get_post_field('post_content', $front_id) );
        while ( $stack ) {
            $b = array_shift($stack);
            if ( isset($b['blockName']) && $b['blockName'] === 'acf/team-awards' ) {
                $awards_block = $b;
                break;
            }
            if ( ! empty($b['innerBlocks']) ) {
                foreach ( $b['innerBlocks'] as $ib ) { $stack[] = $ib; }
            }
        }
    }

    if ( $awards_block ) :
        echo render_block( $awards_block );
    else : // Fallback: the team page's own awards_items repeater
?>
<section class="team-awards team-awards_block">
    <div class="container container_small">
        <div class="team-awards__info">
            <p class="section-subtitle"><?php the_field('awards_subtitle'); ?></p>
            <h2 class="team-awards__title"><?php the_field('awards_title'); ?></h2>
            <p class="team-awards__description"><?php the_field('awards_description'); ?></p>
        </div>

        <div class="team-awards__items">
            <?php
                while( have_rows('awards_items')) : the_row();
                    $years = get_sub_field('years');
                    $image = get_sub_field('image');
                    $title = get_sub_field('title');
                    $nomination = get_sub_field('nomination');
                    $description = get_sub_field('description');
            ?>
                <div class="team-award js-reveal">
                    <div class="team-award__top">
                        <?php if ($years): ?><span class="team-award__year"><?php echo $years; ?></span><?php endif; ?>
                    </div>

                    <div class="team-award__img">
                        <?php if ($image): ?>
                            <?php echo wp_get_attachment_image($image, 'full'); ?>
                        <?php elseif ($title): ?>
                            <span class="team-award__mono" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( preg_replace('/[^A-Za-z]/', '', (string) $title), 0, 1 ) ) ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ($title): ?><h3 class="team-award__title"><?php echo $title; ?></h3><?php endif; ?>
                    <?php if ($nomination): ?><p class="team-award__nomination"><?php echo $nomination; ?></p><?php endif; ?>
                    <?php if ($description): ?><p class="team-award__description"><?php echo $description; ?></p><?php endif; ?>
                </div>
            <?php
                endwhile;
            ?>
        </div>
    </div>
</section>
<?php endif; ?>
