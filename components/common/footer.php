<?php
// Multilingual helpers: on non-English pages prefer the language variant of an ACF
// Options field (`_es` / `_de`, fallback to base), and translate hardcoded UI strings inline.
$mfs_footer_lang = mfs_lang();
$opt = function ($name) use ($mfs_footer_lang) {
    if ( $mfs_footer_lang !== 'en' ) {
        $v = get_field($name . '_' . $mfs_footer_lang, 'options');
        if ( $v !== '' && $v !== null && $v !== false ) {
            return $v;
        }
    }
    return get_field($name, 'options');
};
$t = function ($en, $es = null, $de = null) {
    return mfs_t($en, $es, $de);
};
?>
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <?php if ( ! is_front_page() ) : // Home uses the Free Test Render section as its closing CTA instead. ?>
            <div class="footer__cta js-reveal">
                <h2><?php
                    $mfs_ftitle = $args['footer_title'] ?? $opt('footer_title');
                    if ( $mfs_footer_lang === 'de' && ! get_field('footer_title_de', 'options') ) {
                        $mfs_ftitle = mfs_t('Let\'s create visuals that sell', null, 'Lassen Sie uns Visuals schaffen, die verkaufen');
                    }
                    echo $mfs_ftitle;
                ?></h2>
                <p><?php
                    $mfs_fdesc = $args['footer_description'] ?? $opt('footer_description');
                    if ( $mfs_footer_lang === 'de' && ! get_field('footer_description_de', 'options') ) {
                        $mfs_fdesc = mfs_t('Book a call with our manager today to discuss the details of your 3D visualization project and start working with our professional studio at the earliest opportunity', null, 'Vereinbaren Sie noch heute ein Gespräch mit unserem Manager, um die Details Ihres 3D-Visualisierungsprojekts zu besprechen und zeitnah mit unserem professionellen Studio zu starten.');
                    }
                    echo $mfs_fdesc;
                ?></p>

                <button class="btn-main js-modal-open" data-modal="book" type="button">
                    <?php echo $t('Book a call', 'Reservar una llamada', 'Beratung buchen'); ?>
                </button>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer__links js-footer-acc">
            <?php
            $footer_menu = $opt('footer_menu');
            if ($footer_menu):
                foreach ($footer_menu as $footer_menu_item):
                    $footer_title = $footer_menu_item['title'] ?? '';
                    $footer_groups = $footer_menu_item['groups'] ?? [];
            ?>
                <div class="footer__links-item footer-links js-footer-acc-item">
                    <button class="footer-links__btn js-footer-acc-btn" type="button">
                        <?= esc_html($footer_title); ?>
                    </button>

                    <div class="footer-links__item-overflow">
                        <div class="footer-links__item">
                            <?php foreach ($footer_groups as $group): ?>
                                <?php
                                    $group_title = $group['title'] ?? '';
                                    $group_link = $group['link'] ?? 0;
                                    $group_links = $group['links'] ?? [];
                                    $has_links = !empty($group_links);
                                ?>
                                <div class="footer-links__subitem<?= !$group_title ? ' horizontal' : '' ?>">
                                    <?php if ($group_link): ?>
                                        <a href="<?= esc_url(get_permalink($group_link)); ?>" class="footer__subtitle"><?= esc_html($group_title); ?></a>
                                    <?php elseif($group_title): ?>
                                        <p class="footer__subtitle"><?= esc_html($group_title); ?></p>
                                    <?php endif; ?>

                                    <?php if ($has_links): ?>
                                        <ul>
                                            <?php foreach ($group_links as $link): ?>
                                                <?php
                                                    $link_title = $link['title'] ?? '';
                                                    $link_id = $link['link'] ?? 0;
                                                ?>
                                                <li>
                                                    <?php if ($link_id): ?>
                                                        <a href="<?= esc_url(get_permalink($link_id)); ?>"><?= esc_html($link_title); ?></a>
                                                    <?php else: ?>
                                                        <span><?= esc_html($link_title); ?></span>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php
                endforeach;
            endif;
            ?>
        </div>

        <div class="footer__contacts">
            <div class="footer__contacts-info">
                <p class="footer__subtitle">
                    <?php echo $t('Contact us', 'Contáctanos', 'Kontaktieren Sie uns'); ?>
                </p>

                <ul>
                    <li><?php the_field('footer_address', 'options'); ?></li>
                    <li class="phone">
                        <a href="tel:<?php the_field('footer_phone', 'options'); ?>"
                            target="_blank"><?php the_field('footer_phone', 'options'); ?></a>
                        <a href="https://wa.me/<?php the_field('footer_whatsapp', 'options'); ?>" target="_blank"
                            rel="nofollow noopener"><?php the_field('footer_whatsapp', 'options'); ?> (WA)</a>
                    </li>
                    <li>
                        <a href="mailto:<?php the_field('footer_email', 'options'); ?>"
                            target="_blank"><?php the_field('footer_email', 'options'); ?></a>
                    </li>
                </ul>
            </div>

            <div class="footer__reviews">
                <p class="footer__subtitle">
                    <?php echo $t('Review Us', 'Déjanos tu reseña', 'Bewerten Sie uns'); ?>
                </p>

                <div class="footer__reviews-info">
                    <a href="<?php the_field('footer_map', 'options'); ?>" target="_blank" rel="nofollow noopener"
                        aria-label="Review Us on Google">
                        <?= inline_svg('icons/google.svg'); ?>
                    </a>
                    <a href="https://uk.trustpilot.com/review/interior57.com" target="_blank" rel="nofollow noopener"
                        aria-label="Review Us on Trustpilot">
                        <?= inline_svg('icons/trustpilot-white.svg'); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <div class="footer__copy">
                <?= inline_svg('icons/copy.svg'); ?>
                <span><?php echo $t('MAVERICK FRAME STUDIO. ALL RIGHTS RESERVED', 'MAVERICK FRAME STUDIO. TODOS LOS DERECHOS RESERVADOS', 'MAVERICK FRAME STUDIO. ALLE RECHTE VORBEHALTEN'); ?></span>
            </div>

            <ul class="footer__bottom-links">
                <?php if ( $mfs_footer_lang === 'de' ) : // German legal footer: Impressum + Datenschutzerklärung (DE legal pages). ?>
                <li>
                    <a href="<?= get_permalink(20456) ?>">Impressum</a>
                </li>
                <li>
                    <a href="<?= get_permalink(20457) ?>">Datenschutzerklärung</a>
                </li>
                <?php else : ?>
                <li>
                    <a href="<?php the_field('service_agreement_file', 'options'); ?>" target="_blank"><?php echo $t('Service Agreement', 'Contrato de servicio'); ?></a>
                </li>
                <li>
                    <a href="<?= get_permalink(2092) ?>" target="_blank"><?php echo $t('Editorial Policy', 'Política editorial'); ?></a>
                </li>
                <li>
                    <a href="<?= get_permalink(6397) ?>" target="_blank"><?php echo $t('Privacy Policy', 'Política de privacidad'); ?></a>
                </li>
                <?php endif; ?>
            </ul>

            <?php
            // Language chooser. Links go to the translation of the current page;
            // Polylang falls back to the language home when no translation exists.
            if ( function_exists('pll_the_languages') ) :
                $pll_langs = pll_the_languages( array( 'raw' => 1, 'hide_if_no_translation' => 0 ) );
                if ( ! empty($pll_langs) ) :
                    $lang_meta = array(
                        'en' => array( 'region' => 'Worldwide',   'flag' => 'globe' ),
                        'es' => array( 'region' => 'España',      'flag' => 'es' ),
                        'de' => array( 'region' => 'Deutschland', 'flag' => 'de' ),
                    );
                    $globe_svg = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.6 2.7 2.6 15.3 0 18M12 3c-2.6 2.7-2.6 15.3 0 18"/></svg>';
                    $es_flag_svg = '<svg viewBox="0 0 30 20" preserveAspectRatio="none" aria-hidden="true"><rect width="30" height="20" fill="#c60b1e"/><rect y="5" width="30" height="10" fill="#ffc400"/><g transform="translate(9,10)"><rect x="-4.4" y="-2.6" width="0.95" height="6.2" fill="#9a7b13"/><rect x="3.45" y="-2.6" width="0.95" height="6.2" fill="#9a7b13"/><path d="M-3,-3 h6 v2.6 a3,4 0 0 1 -3,4 a3,4 0 0 1 -3,-4 z" fill="#ad1519" stroke="#9a7b13" stroke-width="0.4"/><path d="M-3,-1 h6" stroke="#ffc400" stroke-width="0.5"/><rect x="-2.4" y="-4.4" width="4.8" height="1.4" rx="0.3" fill="#c8961e"/></g></svg>';
                    $de_flag_svg = '<svg viewBox="0 0 30 20" preserveAspectRatio="none" aria-hidden="true"><rect width="30" height="6.667" y="0" fill="#000000"/><rect width="30" height="6.667" y="6.667" fill="#dd0000"/><rect width="30" height="6.666" y="13.333" fill="#ffce00"/></svg>';
                    $render_flag = function( $flag ) use ( $globe_svg, $es_flag_svg, $de_flag_svg ) {
                        if ( $flag === 'es' ) {
                            return '<span class="footer-lang__flag footer-lang__flag--es">' . $es_flag_svg . '</span>';
                        }
                        if ( $flag === 'de' ) {
                            return '<span class="footer-lang__flag footer-lang__flag--de">' . $de_flag_svg . '</span>';
                        }
                        return '<span class="footer-lang__flag footer-lang__flag--globe">' . $globe_svg . '</span>';
                    };
                    $check_svg = '<svg class="footer-lang__check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><path d="M5 12l5 5L20 7"/></svg>';
                    $cur_slug = pll_current_language();
                    $cur_meta = isset($lang_meta[$cur_slug]) ? $lang_meta[$cur_slug] : array( 'region' => '', 'flag' => 'globe' );
                    $cur_name = isset($pll_langs[$cur_slug]) ? $pll_langs[$cur_slug]['name'] : '';
            ?>
            <div class="footer-lang js-footer-lang">
                <button type="button" class="footer-lang__toggle js-footer-lang-btn" aria-haspopup="true" aria-expanded="false">
                    <?= $render_flag($cur_meta['flag']) ?>
                    <span class="footer-lang__current"><?= esc_html($cur_meta['region']) ?> (<?= esc_html($cur_name) ?>)</span>
                    <svg class="footer-lang__caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                </button>

                <div class="footer-lang__panel">
                    <ul>
                        <?php foreach ( $pll_langs as $l ):
                            $slug    = $l['slug'];
                            $meta    = isset($lang_meta[$slug]) ? $lang_meta[$slug] : array( 'region' => $l['name'], 'flag' => 'globe' );
                            $current = ! empty($l['current_lang']);
                        ?>
                            <li>
                                <a href="<?= esc_url($l['url']) ?>" class="footer-lang__opt<?= $current ? ' is-current' : '' ?>"<?= $current ? ' aria-current="true"' : '' ?>>
                                    <?= $render_flag($meta['flag']) ?>
                                    <span class="footer-lang__txt">
                                        <strong><?= esc_html($meta['region']) ?></strong>
                                        <span><?= esc_html($l['name']) ?></span>
                                    </span>
                                    <?= $check_svg ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <?php endif; endif; ?>

            <ul class="footer-socials socials">
                <?php if (get_field("instagram", 'options')): ?>
                    <li>
                        <a href="<?php the_field("instagram", 'options'); ?>" rel="nofollow noopener" target="_blank"
                            aria-label="Open Instagram" class="footer-socials__link">
                            <?= inline_svg('icons/instagram.svg'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (get_field("linkedin", 'options')): ?>
                    <li>
                        <a href="<?php the_field("linkedin", 'options'); ?>" rel="nofollow noopener" target="_blank"
                            aria-label="Open LinkedIn" class="footer-socials__link">
                            <?= inline_svg('icons/linkedin.svg'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (get_field("behance", 'options')): ?>
                    <li>
                        <a href="<?php the_field("behance", 'options'); ?>" rel="nofollow noopener" target="_blank"
                            aria-label="Open Behance" class="footer-socials__link">
                            <?= inline_svg('icons/behance.svg'); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (get_field("facebook", 'options')): ?>
                    <li>
                        <a href="<?php the_field("facebook", 'options'); ?>" rel="nofollow noopener" target="_blank"
                            aria-label="Open Facebook" class="footer-socials__link">
                            <?= inline_svg('icons/facebook.svg'); ?>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</footer>

<script>
(function(){
    if (window.__mfsFooterLangInit) { return; }
    window.__mfsFooterLangInit = true;
    function init(){
        var wrap = document.querySelector('.js-footer-lang');
        if (!wrap) { return; }
        var btn = wrap.querySelector('.js-footer-lang-btn');
        btn.addEventListener('click', function(e){
            e.stopPropagation();
            var open = wrap.classList.toggle('is-open');
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
        document.addEventListener('click', function(e){
            if (!wrap.contains(e.target)) {
                wrap.classList.remove('is-open');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape') { wrap.classList.remove('is-open'); btn.setAttribute('aria-expanded','false'); }
        });
    }
    if (document.readyState !== 'loading') { init(); }
    else { document.addEventListener('DOMContentLoaded', init); }
})();
</script>
