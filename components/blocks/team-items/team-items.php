<section class="team-items">
    <div class="container">
        <?php if (get_field('description')): ?>
            <div class="team-items__info">
                <h2><?php the_field('title'); ?></h2>
                
                <p><?php the_field('description'); ?></p>
            </div>
        <?php else: ?>
            <h2 class="section-subtitle"><?php the_field('title'); ?></h2>
        <?php endif; ?>

        <?php
            $team = get_field('items');
            if( $team ): 
        ?> 
            <div class="team-items__items js-reveal">
                <div class="js-team-new-slider splide" role="group" aria-label="<?php the_field('title'); ?>">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php foreach( $team as $member ): 
                                $name = get_the_title( $member->ID );
                                $img = get_post_thumbnail_id( $member->ID );
                                $color_photo = get_field( 'color_bg_photo', $member->ID );
                                $position = get_field( 'position', $member->ID );
                                $link = get_permalink( $member->ID );
                            ?>
                            <li class="splide__slide">
                                <?php echo get_template_part( 'components/common/team-item', null, [ 
                                        'name' => $name,
                                        'img' => $img,
                                        'color_photo' => $color_photo,
                                        'position' => $position,
                                        'link' => $link,
                                    ]
                                ); ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>