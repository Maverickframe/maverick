<section class="section-related js-reveal">
    <div class="container">
        <h2 class="section__title">Related Articles</h2>

        <div class="cards cards--3">
            <?php
            $args = array(
                'post_type' => $args['post_type'],
                'posts_per_page' => 3,
                'post_status' => 'publish',
                'post__not_in' => [$post->ID]
            );

            $related_query = new WP_Query($args);

            if ($related_query->have_posts()) {
                while ($related_query->have_posts()) {
                    $related_query->the_post();
                    echo get_template_part('components/new-design/blog/articles-item', null, [
                        'id' => get_the_ID(),
                        'class' => ' --related'
                    ]);
                }
            }

            wp_reset_query();
            $wp_query->rewind_posts();
            $post_id = $post->ID;
            ?>
        </div>
    </div>
</section>