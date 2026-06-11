<section class="section-related js-reveal">
    <div class="container">
        <h2 class="section__title">Related Articles</h2>

        <div class="cards cards--3">
            <?php
            $relatedPostType = $args['post_type'] ?? get_post_type($post->ID);
            $currentId       = $post->ID;
            $catIds          = wp_get_post_terms($currentId, 'category', ['fields' => 'ids']);

            $relatedIds = [];

            // 1) Topical: posts sharing a category with the current one
            if (!empty($catIds) && !is_wp_error($catIds)) {
                $topicalQuery = new WP_Query([
                    'post_type'           => $relatedPostType,
                    'posts_per_page'      => 3,
                    'post_status'         => 'publish',
                    'post__not_in'        => [$currentId],
                    'fields'              => 'ids',
                    'no_found_rows'       => true,
                    'ignore_sticky_posts' => true,
                    'tax_query'           => [
                        [
                            'taxonomy' => 'category',
                            'field'    => 'term_id',
                            'terms'    => $catIds,
                        ],
                    ],
                ]);
                $relatedIds = $topicalQuery->posts;
            }

            // 2) Fallback: fill remaining slots with the most recent posts
            if (count($relatedIds) < 3) {
                $fallbackQuery = new WP_Query([
                    'post_type'           => $relatedPostType,
                    'posts_per_page'      => 3 - count($relatedIds),
                    'post_status'         => 'publish',
                    'post__not_in'        => array_merge([$currentId], $relatedIds),
                    'fields'              => 'ids',
                    'no_found_rows'       => true,
                    'ignore_sticky_posts' => true,
                ]);
                $relatedIds = array_merge($relatedIds, $fallbackQuery->posts);
            }

            if (!empty($relatedIds)) {
                $related_query = new WP_Query([
                    'post_type'           => $relatedPostType,
                    'post__in'            => $relatedIds,
                    'orderby'             => 'post__in',
                    'posts_per_page'      => count($relatedIds),
                    'post_status'         => 'publish',
                    'ignore_sticky_posts' => true,
                ]);

                while ($related_query->have_posts()) {
                    $related_query->the_post();
                    echo get_template_part('components/new-design/blog/articles-item', null, [
                        'id'    => get_the_ID(),
                        'class' => ' --related',
                    ]);
                }

                wp_reset_postdata();
            }
            ?>
        </div>
    </div>
</section>
