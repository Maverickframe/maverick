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
            // Solutions CPT singles have no page parent AND there is no Solutions hub page in any
            // language: EN /solutions/ is a Rank Math 301 to the homepage, /es/soluciones/ and
            // /de/loesungen/ do not exist. The old section crumb therefore linked every solutions
            // page to a redirect — and on ES it leaked to the EN URL (cross-language) and carried
            // the redirecting URL inside the BreadcrumbList schema. Crumb dropped until a real hub
            // exists; breadcrumb is Home → page title. If a hub is ever built, re-add it here with
            // a per-language URL.
            foreach (array_reverse(get_post_ancestors(get_the_ID())) as $hf_anc) {
                $hf_crumbs[$hf_pos++] = ['link' => get_permalink($hf_anc), 'name' => get_the_title($hf_anc)];
            }
        ?>
            <?php echo get_template_part('components/new-design/breadcrumbs', null, ['breadcrumbs' => $hf_crumbs]); ?>
        <?php endif; ?>
        <div class="hero__main hero-front__main <?php echo $hf_reveal; ?>">
            <div class="hero-front__titles">
                <div class="hero-front__subtitle"><?php the_field('subtitle'); ?></div>
                <?php
                    // On the front page and on /solutions/ this block IS the page
                    // hero, so it owns the <h1>. The services hub template prints
                    // its own <h1> above the content and then drops this block in
                    // mid-page, which gave /services/ and /es/servicios/ two H1s.
                    // First heading on the page wins; later ones degrade to <h2>.
                    $hf_h_tag = empty($GLOBALS['mfs_h1_printed']) ? 'h1' : 'h2';
                    $GLOBALS['mfs_h1_printed'] = true;
                ?>
                <<?php echo $hf_h_tag; ?> class="hero__title h1 js-highlight text-highlight"><?php the_field('title'); ?></<?php echo $hf_h_tag; ?>>
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
                            for ( $pass = 0; $pass < 1; $pass++ ) : // single pass — ping-pong needs no seamless dupe
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
                                            // Dupe (pass-1) marquee clones are visually part of the seamless loop and
                                            // can be the LCP too — a right-column dupe (slide 0, the speaker) was the PSI
                                            // LCP with loading=lazy. Give dupes the SAME eager window as real slides: it's
                                            // the identical image file (browser dedupes the URL → no extra bytes), it just
                                            // drops the lazy attr PSI flags. Keep fetchpriority=high on exactly the real
                                            // slide 0 only (one LCP hint per column).
                                            if ($i < 4 || $i >= $hf_total - 2) {
                                                eager_attachment($image, 'large', '(max-width: 1270px) 123px, 300px', !$dupe && $i === 0);
                                            } else {
                                                echo wp_get_attachment_image($image, 'large', false, [
                                                    'loading' => 'lazy',
                                                    'sizes'   => '(max-width: 1270px) 123px, 300px',
                                                ]);
                                            }
                                        ?>
                                        <?php // Explicit class so the CSS overlay targets this image by class, not by
                                              // position — robust against sibling nodes WP Rocket injects for logged-out. ?>
                                        <?php // hover image removed — one render per slide ?>
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
                            for ( $pass = 0; $pass < 1; $pass++ ) : // single pass — ping-pong needs no seamless dupe
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
                                            // Dupe (pass-1) marquee clones are visually part of the seamless loop and
                                            // can be the LCP too — a right-column dupe (slide 0, the speaker) was the PSI
                                            // LCP with loading=lazy. Give dupes the SAME eager window as real slides: it's
                                            // the identical image file (browser dedupes the URL → no extra bytes), it just
                                            // drops the lazy attr PSI flags. Keep fetchpriority=high on exactly the real
                                            // slide 0 only (one LCP hint per column).
                                            if ($i < 4 || $i >= $hf_total - 2) {
                                                eager_attachment($image, 'large', '(max-width: 1270px) 123px, 300px', !$dupe && $i === 0);
                                            } else {
                                                echo wp_get_attachment_image($image, 'large', false, [
                                                    'loading' => 'lazy',
                                                    'sizes'   => '(max-width: 1270px) 123px, 300px',
                                                ]);
                                            }
                                        ?>
                                        <?php // Explicit class so the CSS overlay targets this image by class, not by
                                              // position — robust against sibling nodes WP Rocket injects for logged-out. ?>
                                        <?php // hover image removed — one render per slide ?>
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
