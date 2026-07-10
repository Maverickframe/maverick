<?php
    // On the front page the above-the-fold hero must not stay at opacity:0 waiting for
    // the deferred theme bundle (GSAP reveals .js-reveal on intersect). That JS-gated
    // reveal pushed the LCP paint past Lighthouse's measurement window → NO_LCP / very
    // late LCP. Use a CSS-animated reveal class on the front page so the hero (H1 +
    // images) paints immediately and GSAP never touches it. Inner-page heroes keep the
    // JS .js-reveal path (and the .single-solutions slider hack in hero-front.scss).
    $hf_reveal = is_front_page() ? 'hero-front__reveal' : 'reveal-css';
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
                <div class="mfs-marquee hero-front__marquee" role="group" aria-label="<?php the_title(); ?>">
                    <ul class="mfs-marquee__track">
                        <?php
                            // Rendered twice for a seamless pure-CSS marquee loop; 2nd pass hidden from AT.
                            // LCP eager/lazy runs on the 1st pass only (the visible originals).
                            for ( $pass = 0; $pass < 2; $pass++ ) :
                                $dupe = ( $pass === 1 );
                                $i = 0;
                                $hf_col = get_field('cases_left');
                                $hf_total = is_array($hf_col) ? count($hf_col) : 0;
                                while( have_rows('cases_left')) : the_row();
                                    $link = get_sub_field('link');
                                    $image = get_sub_field('image');
                                    $hover_image = get_sub_field('hover_image');
                            ?>
                                <li class="mfs-marquee__item"<?php echo $dupe ? ' aria-hidden="true"' : ''; ?>>
                                    <?php if($link): ?>
                                        <a class="hero-front__slider-item" href="<?php echo esc_url($link); ?>"<?php echo $dupe ? ' tabindex="-1"' : ''; ?>>
                                    <?php else: ?>
                                        <div class="hero-front__slider-item">
                                    <?php endif; ?>
                                        <?php
                                            // First 4 AND last 2 slides of each column are eager; the rest lazy.
                                            // Last 2 too: one column's AutoScroll runs backwards (speed -0.5), so
                                            // Splide's loop CLONES of the TAIL slides are visible within seconds —
                                            // PSI mobile LCP was the 10th (last) slide of the right column, still
                                            // lazy → 3s load delay (report ox2ewd5hxl, 07-02).
                                            // The hero is an auto-scrolling Splide marquee, so the LCP element is
                                            // whichever slide is largest when LCP fires — NOT deterministically slide 0.
                                            // Making only slide 0 eager left the real LCP slide lazy+JS-gated → mobile
                                            // LCP ~7.7s. Eager-ing the first few visible slides makes the LCP image load
                                            // immediately. 4 (not 3): the marquee auto-scrolls ~30px/s, so on mobile the
                                            // 4th slide enters the viewport within the LCP window (~6s) and became the
                                            // lazy-loaded LCP element (PSI 07-02: load delay 3.3s). fetchpriority=high
                                            // only on the very first (one LCP hint).
                                            // sizes match Splide breakpoints: ≤1270px the slider is fixedWidth 145 → mobile
                                            // picks ~400w (not 768) — keeps retina quality, cuts mobile bytes.
                                            // >1270px the vertical (ttb) slide column renders ~288px CSS (PSI-measured),
                                            // so desktop = 300px (NOT 420): DPR1 picks 400w instead of 768w (~80KB/slide),
                                            // DPR2 retina still picks 768w. 420px over-declared → forced the 768w file.
                                            if (!$dupe && ($i < 4 || $i >= $hf_total - 2)) {
                                                eager_attachment($image, 'large', '(max-width: 1270px) 150px, 300px', $i === 0);
                                            } else {
                                                echo wp_get_attachment_image($image, 'large', false, [
                                                    'loading' => 'lazy',
                                                    'sizes'   => '(max-width: 1270px) 150px, 300px',
                                                ]);
                                            }
                                        ?>
                                        <?php // Explicit class so the CSS overlay targets this image by class, not by
                                              // position — robust against sibling nodes WP Rocket injects for logged-out. ?>
                                        <?php lazy_attachment($hover_image, 'large', 'lazy', 'hero-front__slider-hover', '(max-width: 1270px) 150px, 300px'); ?>
                                        <?php $i++; ?>
                                    <?php if($link): ?>
                                        </a>
                                    <?php else: ?>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php
                                endwhile;
                            endfor;
                            ?>
                    </ul>
                </div>
            </div>

            <div class="hero-front__slider <?php echo $hf_reveal; ?>">
                <div class="mfs-marquee mfs-marquee--reverse hero-front__marquee" role="group" aria-label="<?php the_title(); ?>">
                    <ul class="mfs-marquee__track">
                        <?php
                            // Rendered twice for a seamless pure-CSS marquee loop; 2nd pass hidden from AT.
                            for ( $pass = 0; $pass < 2; $pass++ ) :
                                $dupe = ( $pass === 1 );
                                $i = 0;
                                $hf_col = get_field('cases_right');
                                $hf_total = is_array($hf_col) ? count($hf_col) : 0;
                                while( have_rows('cases_right')) : the_row();
                                    $link = get_sub_field('link');
                                    $image = get_sub_field('image');
                                    $hover_image = get_sub_field('hover_image');
                            ?>
                                <li class="mfs-marquee__item"<?php echo $dupe ? ' aria-hidden="true"' : ''; ?>>
                                    <?php if($link): ?>
                                        <a class="hero-front__slider-item" href="<?php echo esc_url($link); ?>"<?php echo $dupe ? ' tabindex="-1"' : ''; ?>>
                                    <?php else: ?>
                                        <div class="hero-front__slider-item">
                                    <?php endif; ?>
                                        <?php
                                            // First 4 AND last 2 slides of each column are eager; the rest lazy.
                                            // Last 2 too: one column's AutoScroll runs backwards (speed -0.5), so
                                            // Splide's loop CLONES of the TAIL slides are visible within seconds —
                                            // PSI mobile LCP was the 10th (last) slide of the right column, still
                                            // lazy → 3s load delay (report ox2ewd5hxl, 07-02).
                                            // The hero is an auto-scrolling Splide marquee, so the LCP element is
                                            // whichever slide is largest when LCP fires — NOT deterministically slide 0.
                                            // Making only slide 0 eager left the real LCP slide lazy+JS-gated → mobile
                                            // LCP ~7.7s. Eager-ing the first few visible slides makes the LCP image load
                                            // immediately. 4 (not 3): the marquee auto-scrolls ~30px/s, so on mobile the
                                            // 4th slide enters the viewport within the LCP window (~6s) and became the
                                            // lazy-loaded LCP element (PSI 07-02: load delay 3.3s). fetchpriority=high
                                            // only on the very first (one LCP hint).
                                            // sizes match Splide breakpoints: ≤1270px the slider is fixedWidth 145 → mobile
                                            // picks ~400w (not 768) — keeps retina quality, cuts mobile bytes.
                                            // >1270px the vertical (ttb) slide column renders ~288px CSS (PSI-measured),
                                            // so desktop = 300px (NOT 420): DPR1 picks 400w instead of 768w (~80KB/slide),
                                            // DPR2 retina still picks 768w. 420px over-declared → forced the 768w file.
                                            if (!$dupe && ($i < 4 || $i >= $hf_total - 2)) {
                                                eager_attachment($image, 'large', '(max-width: 1270px) 150px, 300px', $i === 0);
                                            } else {
                                                echo wp_get_attachment_image($image, 'large', false, [
                                                    'loading' => 'lazy',
                                                    'sizes'   => '(max-width: 1270px) 150px, 300px',
                                                ]);
                                            }
                                        ?>
                                        <?php // Explicit class so the CSS overlay targets this image by class, not by
                                              // position — robust against sibling nodes WP Rocket injects for logged-out. ?>
                                        <?php lazy_attachment($hover_image, 'large', 'lazy', 'hero-front__slider-hover', '(max-width: 1270px) 150px, 300px'); ?>
                                        <?php $i++; ?>
                                    <?php if($link): ?>
                                        </a>
                                    <?php else: ?>
                                        </div>
                                    <?php endif; ?>
                                </li>
                            <?php
                                endwhile;
                            endfor;
                            ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>