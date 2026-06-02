<section class="service-page-team">
    <header class="service-page-team__header">
        <h2 class="service-page-team__title"><?php the_field('team_title'); ?></h2>

        <a href="<?php the_field('team_link'); ?>" class="btn service-page-team__link"><span>read more</span></a>
    </header>

    <div class="service-page-team__desc">
        <?php the_field('team_desc'); ?>
    </div>

    <div class="service-page-team__slider">
        <div class="js-team-slider splide" role="group" aria-label="<?php the_field('team_title'); ?>">
            <div class="splide__track">
                <ul class="splide__list">
                    <?php
                        $team = get_field('team');
                        if( $team ): 
                    ?>
                    <?php foreach( $team as $member ): 
                        $title = get_the_title( $member->ID );
                        $img = get_field( 'black_photo', $member->ID );
                        $position = get_field( 'position', $member->ID );
                    ?>
                        <li class="splide__slide">
                            <div class="service-page-team__item">
                                <?php lazy_attachment($img, 'large'); ?>

                                <div class="service-page-team__item-overlay">
                                    <p class="service-page-team__item-name">
                                        <?php echo $title; ?>
                                    </p>

                                    <p class="service-page-team__item-position">
                                        <?php echo $position; ?>
                                    </p>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="splide-scrollbar js-scrollbar">
                <div class="splide-scrollbar__bar js-scrollbar-bar"></div>
            </div>
        </div>
    </div>
</section>