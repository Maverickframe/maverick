<section class="trusted">
    <div class="container container_small trusted__container">
        <h2><?php echo ( is_front_page() || ! is_singular() ) ? mfs_t( 'Trusted by leading teams worldwide', 'Equipos y marcas líderes de todo el mundo confían en nosotros', 'Führende Teams weltweit vertrauen uns' ) : mfs_t( 'Trusted by Leading Teams: ', 'Confían en nosotros: ', 'Vertrauen führender Teams: ' ) . esc_html( get_the_title() ); ?></h2>
        
        <div class="trusted__slider one js-reveal">
            <div class="mfs-marquee" role="group" aria-label="<?php echo esc_attr( mfs_t( 'Trusted by leading teams', 'Equipos líderes confían en nosotros', 'Führende Teams vertrauen uns' ) ); ?>">
                <ul class="mfs-marquee__track">
                    <?php
                        // rendered twice for a seamless pure-CSS marquee loop; 2nd copy hidden from AT
                        for ( $mfs_dup = 0; $mfs_dup < 2; $mfs_dup++ ) :
                            while ( have_rows('trusted_1', 9755) ) : the_row();
                                $image = get_sub_field('image');
                    ?>
                        <li class="mfs-marquee__item trusted__slider-item"<?php echo $mfs_dup ? ' aria-hidden="true"' : ''; ?>>
                            <?php lazy_attachment($image, 'full'); ?>
                        </li>
                    <?php
                            endwhile;
                        endfor;
                    ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="trusted__slider two js-reveal">
        <div class="mfs-marquee mfs-marquee--reverse" role="group" aria-label="<?php echo esc_attr( mfs_t( 'Trusted by leading teams worldwide', 'Equipos y marcas líderes de todo el mundo confían en nosotros', 'Führende Teams weltweit vertrauen uns' ) ); ?>">
            <ul class="mfs-marquee__track">
                <?php
                    // rendered twice for a seamless pure-CSS marquee loop; 2nd copy hidden from AT
                    for ( $mfs_dup = 0; $mfs_dup < 2; $mfs_dup++ ) :
                        while ( have_rows('trusted_2', 9755) ) : the_row();
                            $image = get_sub_field('image');
                ?>
                    <li class="mfs-marquee__item trusted__slider-item"<?php echo $mfs_dup ? ' aria-hidden="true"' : ''; ?>>
                        <?php lazy_attachment($image, 'full'); ?>
                    </li>
                <?php
                        endwhile;
                    endfor;
                ?>
            </ul>
        </div>
    </div>
</section>