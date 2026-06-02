<section class="hero-section <?php echo $args['classes'] ?? null; ?>">

    <?php if (isset($args['images'])): ?>
        <?php if (count($args['images']) > 1): ?>
            <div class="hero-section__slider hero-slider">
                <div class="js-hero-slider splide" role="group" aria-label="Hero Slider Images">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php foreach( $args['images'] as $key => $img ): ?>

                            <?php
                                $desktop_id = $img['desktop_image'];
                                $mobile_id  = $img['mobile_image'];

                                $desktop_url = wp_get_attachment_image_url($desktop_id, 'full');

                                if ($mobile_id) {
                                    $mobile_url  = wp_get_attachment_image_url($mobile_id, 'large');
                                } else {
                                    $mobile_url = $desktop_url;
                                }

                                $desktop_data = wp_get_attachment_image_src($desktop_id, 'full');
                            ?>

                            <li class="splide__slide">
                                <img
                                    <?php if ($key === 0): ?>
                                        src="<?php echo $desktop_url; ?>"
                                        srcset="<?php echo $mobile_url; ?> 799w, <?php echo $desktop_url; ?> 800w"
                                        sizes="100vw"
                                        width="<?php echo $desktop_data[1]; ?>"
                                        height="<?php echo $desktop_data[2]; ?>"
                                        fetchpriority="high"
                                    <?php else: ?>
                                        data-splide-lazy-srcset="<?php echo $mobile_url; ?> 799w, <?php echo $desktop_url; ?> 800w"
                                        sizes="100vw"
                                        data-splide-lazy="<?php echo $desktop_url; ?>"
                                        width="<?php echo $desktop_data[1]; ?>"
                                        height="<?php echo $desktop_data[2]; ?>"
                                        decoding="async"
                                    <?php endif; ?>
                                    alt="Slide <?php echo $key + 1; ?>"
                                >
                            </li>

                        <?php endforeach; ?>

                        </ul>
                    </div>

                    <div class="hero-slider__nav">
                        <div class="hero-slider__counter">
                            <span class="js-hero-slider-current">1</span>
                            <span class="js-hero-slider-total"><?php echo count($args['images']); ?></span>
                        </div>
                        
                        <ul class="splide__pagination is-custom"></ul>

                        <div class="splide__arrows">
                            <button class="splide__arrow splide__arrow--prev">
                                <span>prev slide</span>
                            </button>
                            <button class="btn hero-slider__next-btn splide__arrow splide__arrow--next">
                                <span>
                                    Next slide
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="hero-section__img">
                <div class="mobile">
                    <?php echo wp_get_attachment_image($args['images'][0]['mobile_image'], 'full'); ?>
                </div>
                <div class="desktop">
                    <?php echo wp_get_attachment_image($args['images'][0]['desktop_image'], 'full'); ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="hero-section__main">
        <?php if(!empty($args['title'])): ?>
        <div class="hero-section__title hero-title"><?php echo $args['title']; ?></div>
        <?php endif; ?>

        <?php if(!empty($args['subtitle'])): ?>
        <div class="hero-section__subtitle"><?php echo $args['subtitle']; ?></div>
        <?php endif; ?>

        <?php if(!empty($args['links'])): ?>
        <div class="hero-section__links"><?php echo $args['links']; ?></div>
        <?php endif; ?>
    </div>
</section>