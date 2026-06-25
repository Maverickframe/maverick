<section class="hero-front">
    <div class="container container_small">
        <?php // Services hub hero uses an eyebrow chip in the same spot — the absolute-positioned
              // breadcrumb overlapped it, so skip breadcrumbs on the services-hub template.
              if (!is_front_page() && !is_page_template('templates/template-services-hub.php')) :
            $hf_crumbs = [1 => ['link' => esc_url(home_url('/')), 'name' => mfs_t('Home', 'Inicio', 'Startseite')]];
            $hf_pos = 2;
            // Solutions CPT singles have no page parent — add the section crumb manually.
            if (is_singular('solutions')) {
                $hf_crumbs[$hf_pos++] = ['link' => esc_url(home_url('/solutions/')), 'name' => mfs_t('Solutions', 'Soluciones', 'Lösungen')];
            }
            foreach (array_reverse(get_post_ancestors(get_the_ID())) as $hf_anc) {
                $hf_crumbs[$hf_pos++] = ['link' => get_permalink($hf_anc), 'name' => get_the_title($hf_anc)];
            }
        ?>
            <?php echo get_template_part('components/new-design/breadcrumbs', null, ['breadcrumbs' => $hf_crumbs]); ?>
        <?php endif; ?>
        <div class="hero__main hero-front__main js-reveal">
            <div class="hero-front__titles">
                <div class="hero-front__subtitle"><?php the_field('subtitle'); ?></div>
                <h1 class="hero__title js-highlight text-highlight"><?php the_field('title'); ?></h1>
            </div>
            <div class="hero-front__desc"><?php the_field('description'); ?></div>
    
            <?php // todo: common ?>
            <div class="hero__reviews">
                <div class="review-item">
                    <?php echo inline_svg('icons/google.svg'); ?>
                    <span>4.8</span>
                    <?php echo inline_svg('icons/star.svg'); ?>
                </div>
                <div class="review-item">
                    <?php echo inline_svg('icons/trustpilot-white.svg'); ?>
                    <span>4.9</span>
                    <?php echo inline_svg('icons/star.svg'); ?>
                </div>
            </div>

            <div class="hero__btns">
                <button class="btn-main fill js-modal-open" data-modal="book" type="button"><?php echo mfs_t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?></button>
                <button class="btn-secondary fill js-modal-open" data-modal="download" type="button"><?php echo mfs_t('Explore our work', 'Ver nuestro trabajo', 'Arbeiten ansehen'); ?></button>
            </div>
        </div>

        <div class="hero-front__sliders">
            <div class="hero-front__slider js-reveal">
                <div class="js-hero-hover-slider-left splide" role="group" aria-label="<?php the_title(); ?>">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                                $i = 0;
                                while( have_rows('cases_left')) : the_row();
                                    $link = get_sub_field('link');
                                    $image = get_sub_field('image');
                                    $hover_image = get_sub_field('hover_image');
                            ?>
                                <li class="splide__slide">
                                    <?php if($link): ?>
                                        <a class="hero-front__slider-item" href="<?php echo esc_url($link); ?>">
                                    <?php else: ?>
                                        <div class="hero-front__slider-item">
                                    <?php endif; ?>
                                        <?php eager_attachment($image, 'full', '(max-width: 767px) 45vw, 288px', $i === 0); ?>
                                        <?php lazy_attachment($hover_image, 'full'); ?>
                                        <?php $i++; ?>
                                    <?php if($link): ?>
                                        </a>
                                    <?php else: ?>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php
                                endwhile; 
                            ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="hero-front__slider js-reveal">
                <div class="js-hero-hover-slider-right splide" role="group" aria-label="<?php the_title(); ?>">
                    <div class="splide__track">
                        <ul class="splide__list">
                            <?php
                                $i = 0;
                                while( have_rows('cases_right')) : the_row();
                                    $link = get_sub_field('link');
                                    $image = get_sub_field('image');
                                    $hover_image = get_sub_field('hover_image');
                            ?>
                                <li class="splide__slide">
                                    <?php if($link): ?>
                                        <a class="hero-front__slider-item" href="<?php echo esc_url($link); ?>">
                                    <?php else: ?>
                                        <div class="hero-front__slider-item">
                                    <?php endif; ?>
                                        <?php eager_attachment($image, 'full', '(max-width: 767px) 45vw, 288px', $i === 0); ?>
                                        <?php lazy_attachment($hover_image, 'full'); ?>
                                        <?php $i++; ?>
                                    <?php if($link): ?>
                                        </a>
                                    <?php else: ?>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php
                                endwhile; 
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>