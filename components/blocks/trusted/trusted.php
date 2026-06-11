<section class="trusted">
    <div class="container container_small trusted__container">
        <h2><?php echo ( is_front_page() || ! is_singular() ) ? mfs_t( 'Trusted by leading teams worldwide', 'Equipos y marcas líderes de todo el mundo confían en nosotros' ) : mfs_t( 'Trusted by Leading Teams: ', 'Confían en nosotros: ' ) . esc_html( get_the_title() ); ?></h2>
        
        <div class="trusted__slider one js-reveal">
            <div class="js-presentation-trusted-slider-one splide" role="group" aria-label="Trusted by leading teams">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('trusted_1', 9755)) : the_row();
                                $image = get_sub_field('image');
                        ?>
                            <li class="splide__slide">
                                <div class="trusted__slider-item">
                                    <?php lazy_attachment($image, 'full'); ?>
                                </div>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="trusted__slider two js-reveal">
        <div class="js-presentation-trusted-slider-two splide" role="group" aria-label="Trusted by leading teams worldwide">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php
                        while( have_rows('trusted_2', 9755)) : the_row();
                            $image = get_sub_field('image');
                    ?>
                        <li class="splide__slide">
                            <div class="trusted__slider-item">
                                <?php lazy_attachment($image, 'full'); ?>
                            </div>
                        </li>
                    <?php
                        endwhile; 
                    ?>
                </ul>
            </div>
        </div>
    </div>
</section>