<?php
    // On the front page the above-the-fold hero must not stay at opacity:0 waiting for
    // the deferred theme bundle (GSAP reveals .js-reveal on intersect). That JS-gated
    // reveal pushed the LCP paint past Lighthouse's measurement window → NO_LCP / very
    // late LCP. Use a CSS-animated reveal class on the front page so the hero (H1 +
    // images) paints immediately and GSAP never touches it. Inner-page heroes keep the
    // JS .js-reveal path (and the .single-solutions slider hack in hero-front.scss).
    $hf_reveal = is_front_page() ? 'hero-front__reveal' : 'js-reveal';
?>
<section class="hero-front">
    <div class="container container_small">
        <?php // Services hub hero uses an eyebrow chip in the same spot — the absolute-positioned
              // breadcrumb overlapped it, so skip breadcrumbs on the services-hub template.
              if (!is_front_page() && !is_page_template('templates/template-services-hub.php')) :
            $hf_lang = function_exists('mfs_lang') ? mfs_lang() : 'en';
            $hf_home = $hf_lang === 'es' ? home_url('/es/') : ( $hf_lang === 'de' ? home_url('/de/') : home_url('/') );
            $hf_crumbs = [1 => ['link' => esc_url($hf_home), 'name' => mfs_t('Home', 'Inicio', 'Startseite')]];
            $hf_pos = 2;
            // Solutions CPT singles have no page parent — add the section crumb manually.
            // The `solutions` CPT has no archive index; on /de/ (no Lösungen landing either) the
            // section crumb points at the DE hub instead of a 404 /de/loesungen/.
            if (is_singular('solutions')) {
                $hf_sol = $hf_lang === 'de' ? home_url('/de/') : home_url('/solutions/');
                $hf_crumbs[$hf_pos++] = ['link' => esc_url($hf_sol), 'name' => mfs_t('Solutions', 'Soluciones', 'Lösungen')];
            }
            foreach (array_reverse(get_post_ancestors(get_the_ID())) as $hf_anc) {
                $hf_crumbs[$hf_pos++] = ['link' => get_permalink($hf_anc), 'name' => get_the_title($hf_anc)];
            }
        ?>
            <?php echo get_template_part('components/new-design/breadcrumbs', null, ['breadcrumbs' => $hf_crumbs]); ?>
        <?php endif; ?>
        <div class="hero__main hero-front__main <?php echo $hf_reveal; ?>">
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
            <div class="hero-front__slider <?php echo $hf_reveal; ?>">
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
                                        <?php
                                            // Only the first slide of each column is eager + high priority (LCP candidate).
                                            // The rest use native loading=lazy: the browser still loads any slide that is
                                            // in the viewport on load (incl. Splide loop clones), but defers off-screen
                                            // slides — cutting the hero from ~20 eager images to a couple. Size stays
                                            // 'large' so retina quality is unchanged (images render ~409px @ up to DPR 2).
                                            if ($i === 0) {
                                                eager_attachment($image, 'large', '(max-width: 767px) 45vw, 288px', true);
                                            } else {
                                                echo wp_get_attachment_image($image, 'large', false, [
                                                    'loading' => 'lazy',
                                                    'sizes'   => '(max-width: 767px) 45vw, 288px',
                                                ]);
                                            }
                                        ?>
                                        <?php lazy_attachment($hover_image, 'large'); ?>
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

            <div class="hero-front__slider <?php echo $hf_reveal; ?>">
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
                                        <?php
                                            // Only the first slide of each column is eager + high priority (LCP candidate).
                                            // The rest use native loading=lazy: the browser still loads any slide that is
                                            // in the viewport on load (incl. Splide loop clones), but defers off-screen
                                            // slides — cutting the hero from ~20 eager images to a couple. Size stays
                                            // 'large' so retina quality is unchanged (images render ~409px @ up to DPR 2).
                                            if ($i === 0) {
                                                eager_attachment($image, 'large', '(max-width: 767px) 45vw, 288px', true);
                                            } else {
                                                echo wp_get_attachment_image($image, 'large', false, [
                                                    'loading' => 'lazy',
                                                    'sizes'   => '(max-width: 767px) 45vw, 288px',
                                                ]);
                                            }
                                        ?>
                                        <?php lazy_attachment($hover_image, 'large'); ?>
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