<section class="design-services">
    <div class="container">
        <div class="design-services__info">
            <h2><?php the_field('title'); ?></h2>  
            <p class="p1"><?php the_field('description'); ?></p>
        </div>

        <div class="design-services__items">
        <?php
            $services = [];

            while (have_rows('field_69ef22986cfa7')) : the_row();

                $num = get_row_index() - 1;

                $title = get_sub_field('title');
                $desc = get_sub_field('desc');
                $image = get_sub_field('image');
                $width = get_sub_field('width');
                $modal_title = get_sub_field('modal_title');

                if ($modal_title) {

                    $products = [];

                    foreach (get_sub_field('modal_products') ?: [] as $product) {

                        $video = $product['video'] ?? null;
                        $img_id = $product['image'] ?? null;

                        ob_start();

                        if ($video) {

                            $poster = wp_get_attachment_image_url(
                                $img_id ?: ($video['id'] ?? 0),
                                'large'
                            );
                            ?>

                            <video
                                playsinline
                                preload="none"
                                muted
                                loop
                                class="js-video-item-hover lazyload"
                                poster="<?php echo esc_url($poster); ?>"
                                data-src="<?php echo esc_url($video['url']); ?>"
                            ></video>

                            <?php

                        } elseif ($img_id) {

                            lazy_attachment($img_id, 'large');

                        }

                        $media_html = ob_get_clean();

                        $products[] = [
                            'title' => $product['title'] ?? '',
                            'location' => $product['location'] ?? '',
                            'client' => $product['client'] ?? '',
                            'year' => $product['year'] ?? '',
                            'link' => $product['link'] ?? '',
                            'media' => $media_html,
                        ];
                    }

                    $services[] = [
                        'title' => $title,
                        'desc' => get_sub_field('modal_desc'),
                        'how_title' => get_sub_field('modal_how_title'),
                        'how_items' => get_sub_field('modal_how_items'),
                        'products' => $products,
                    ];
                }
            ?>
                <div class="design-services-item extended js-reveal js-design-services-item" <?php if($width > 0): ?>style="--desktop-width: <?php echo $width; ?>%;"<?php endif; ?>>
                    <?php lazy_attachment($image, 'full'); ?>

                    <div class="design-services-item__info">
                        <h3>
                            <?php echo $title; ?>
                        </h3>

                        <div>
                            <div class="p1 js-desc-text design-services-item__text">
                                <?php echo $desc; ?>
                            </div>
    
                            <button class="js-desc-more design-services-item__more-btn" type="button"><?php echo mfs_t('More', 'Más', 'Mehr'); ?></button>
                        </div>
                    </div>

                    <?php if ($modal_title): ?>
                    <button class="design-services-item__btn js-modal-open" data-modal="design-service" data-services-source="design-services-json" data-service-index="<?php echo $num; ?>" type="button">
                        <span class="design-services-item__btn-icon">
                            <?php echo inline_svg('icons/expand.svg'); ?>
                        </span>
                        <span class="design-services-item__btn-title"><?php echo mfs_t('Expand', 'Ampliar', 'Vergrößern'); ?></span>
                    </button>
                    <?php endif; ?>
                </div>
            <?php
                endwhile; 
            ?>
        </div>
    </div>
</section>

<script type="application/json" id="design-services-json">
<?php echo wp_json_encode($services); ?>
</script>

<?php echo get_template_part( 'components/common/modals/modal-design-services-item' ); ?> 