<?php
    // Hide the Team section only when it has no members to show. Polylang filters
    // the ACF 'items' field by language, so on /de/ case pages it now returns the
    // German team members (the DE team exists as of 2026-06-25). The previous
    // blanket drop on German success-stories is removed; the empty() guard still
    // covers any page/language whose selection filters down to nothing.
    if ( empty( get_field('items') ) ) {
        return;
    }
?>
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
                <div class="js-team-new-slider mfs-snap" role="group" aria-label="<?php the_field('title'); ?>">
                    <ul class="mfs-snap__track">
                            <?php foreach( $team as $member ): 
                                $name = get_the_title( $member->ID );
                                $img = get_post_thumbnail_id( $member->ID );
                                $color_photo = get_field( 'color_bg_photo', $member->ID );
                                $position = get_field( 'position', $member->ID );
                                $link = get_permalink( $member->ID );
                            ?>
                            <li class="mfs-snap__item">
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
        <?php endif; ?>
    </div>
</section>