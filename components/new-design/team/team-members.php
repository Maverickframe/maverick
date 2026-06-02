<section class="team-members">
    <div class="container">
        <p class="section-subtitle"><?php the_field('team_subtitle'); ?></p>
        <h2 class="team-members__title"><?php the_field('team_title'); ?></h2>
        <p class="team-members__description"><?php the_field('team_description'); ?></p>
        
        <div class="team-members__items">
            <?php 
                $args = array(
                    'post_type' => 'team',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'orderby' => array(
                        'date' => 'ASC',
                    )
                );
            
                $query = new WP_Query( $args );

                $extra_images = [
                    7  => get_field('team_image_1'),
                    12 => get_field('team_image_2'),
                    17 => get_field('team_image_3'),
                    19 => get_field('team_image_4'),
                ];

                $counter = 1;
            
                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $name = get_the_title();
                        $img = get_post_thumbnail_id();
                        $color_photo = get_field( 'color_bg_photo' );
                        $position = get_field( 'position' );
                        $link = get_permalink();

                        if ( array_key_exists($counter, $extra_images) && $extra_images[$counter] ) {
                            echo '<div class="team-members__items-extra js-reveal">';
                            echo lazy_attachment($extra_images[$counter], 'large');
                            echo '</div>';
                        }
                        
                        get_template_part( 'components/common/team-item', null, [ 
                                'name' => $name,
                                'img' => $img,
                                'color_photo' => $color_photo,
                                'position' => $position,
                                'link' => $link,
                            ]
                        );

                        $counter++;
                    }
                } wp_reset_postdata();
            ?>
        </div>
    </div>
</section>