<section class="team-members">
    <div class="container">
        <p class="section-subtitle"><?php the_field('team_subtitle'); ?></p>
        <h2 class="team-members__title"><?php the_field('team_title'); ?></h2>
        <p class="team-members__description"><?php the_field('team_description'); ?></p>
        
        <div class="team-members__items">
            <?php
                $tm_is_es = function_exists('pll_current_language') && pll_current_language() === 'es';
                $tm_pos_es = [
                    'Company Owner'             => 'Propietario de la empresa',
                    'Co-Founder & CEO'          => 'Cofundador y CEO',
                    'UX/UI & Graphic Designer'  => 'Diseñador UX/UI y gráfico',
                    'Art Director'              => 'Director de arte',
                    'Project Manager'           => 'Gestor de proyectos',
                    'Storyboard Artist'         => 'Artista de storyboard',
                    '3D Generalist'             => 'Generalista 3D',
                    '3D Artist (Blender)'       => 'Artista 3D (Blender)',
                    '3D Modeller'               => 'Modelador 3D',
                    'AI Artist'                 => 'Artista de IA',
                    '3D Artist (Twinmotion)'    => 'Artista 3D (Twinmotion)',
                    '2D Motion Graphic Artist'  => 'Artista de motion graphics 2D',
                    '3D Artist (3Ds Max)'       => 'Artista 3D (3Ds Max)',
                    '3D Artist (Cinema 4D)'     => 'Artista 3D (Cinema 4D)',
                    '3D Cinematic Artist'       => 'Artista 3D cinematográfico',
                    'Interior Designer'         => 'Diseñador de interiores',
                    'Senior Architect'          => 'Arquitecto sénior',
                    'BIM Specialist'            => 'Especialista BIM',
                    '3D Artist (Unreal Engine)' => 'Artista 3D (Unreal Engine)',
                    'VFX Artist'                => 'Artista VFX',
                ];
                $args = array(
                    'post_type' => 'team',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'lang' => '', // show team members on all languages (team CPT is EN-only)
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
                        if ( $tm_is_es && isset($tm_pos_es[$position]) ) {
                            $position = $tm_pos_es[$position];
                        }
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