<section class="stack-section">
    <div class="container">
        <h2 class="section-title section-title_developers"><?php the_field('stack_title'); ?></h2>
        <div class="section-desc"><?php the_field('stack_desc'); ?></div>
    </div>

    <div class="stack-section__slider">
        <div class="mfs-marquee" role="group" aria-label="<?php the_field('stack_title'); ?>">
            <ul class="mfs-marquee__track">
                <?php
                    // Rendered twice for a seamless pure-CSS marquee loop (no JS/Splide).
                    // The 2nd pass is a visual duplicate — hidden from assistive tech.
                    for ( $mfs_dup = 0; $mfs_dup < 2; $mfs_dup++ ) :
                        while ( have_rows('stack_items') ) : the_row();
                            $img = get_sub_field('img');
                ?>
                    <li class="mfs-marquee__item stack-section__slider-item"<?php echo $mfs_dup ? ' aria-hidden="true"' : ''; ?>>
                        <?php lazy_attachment($img, 'large'); ?>
                    </li>
                <?php
                        endwhile;
                    endfor;
                ?>
            </ul>
        </div>
    </div>
</section>