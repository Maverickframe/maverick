<section class="services-3d-section">
    <div class="container">
        <h2 class="section-title section-title_services-3d"><?php the_field($args['title'] ?? null); ?></h2>

        <div class="services-3d-section__desc">
            <?php the_field($args['description'] ?? null); ?>
        </div>

        <div class="services-3d-section__items">
            <div class="js-services-3d-slider splide" role="group" aria-label="<?php the_field($args['title']) ?? null; ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows($args['items'] ?? null)) : the_row();
                                $title = get_sub_field('title');
                                $description = get_sub_field('description');
                                $icon = get_sub_field('icon');
                                $btn_title = get_sub_field('btn_title');
                                $calendly = get_sub_field('calendly');
                                $btn_link = get_sub_field('btn_link');
                        ?>
                            <li class="splide__slide">
                                <?php echo get_template_part( 'components/services-3d-item', null, array( 
                                        'title' => $title,
                                        'description' => $description,
                                        'icon' => $icon,
                                        'btn_title' => $btn_title,
                                        'calendly' => $calendly,
                                        'btn_link' => $btn_link,
                                        'download' => $args['download'] ?? false,
                                    )
                                ); ?>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>