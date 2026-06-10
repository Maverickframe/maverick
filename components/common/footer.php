<?php
// Multilingual helpers: on /es/ prefer the `_es` variant of an ACF Options field (fallback to base),
// and translate hardcoded UI strings inline.
$is_es = function_exists('pll_current_language') && pll_current_language() === 'es';
$opt = function ($name) use ($is_es) {
    if ( $is_es ) {
        $es = get_field($name . '_es', 'options');
        if ( $es !== '' && $es !== null && $es !== false ) {
            return $es;
        }
    }
    return get_field($name, 'options');
};
$t = function ($en, $es) use ($is_es) {
    return $is_es ? $es : $en;
};
?>
<footer class="footer">
    <div class="container">
        <div class="footer__top">
            <div class="footer__cta js-reveal">
                <h2><?php echo $args['footer_title'] ?? $opt('footer_title'); ?></h2>
                <p><?php echo $args['footer_description'] ?? $opt('footer_description'); ?></p>

                <button class="btn-main js-modal-open" data-modal="book" type="button">
                    <?php echo $t('Book a call', 'Reservar una llamada'); ?>
                </button>
            </div>
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
                    <?php echo $t('Contact us', 'Contáctanos'); ?>
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
                    <?php echo $t('Review Us', 'Déjanos tu reseña'); ?>
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
                <span><?php echo $t('MAVERICK FRAME STUDIO. ALL RIGHTS RESERVED', 'MAVERICK FRAME STUDIO. TODOS LOS DERECHOS RESERVADOS'); ?></span>
            </div>

            <ul class="footer__bottom-links">
                <li>
                    <a href="<?php the_field('service_agreement_file', 'options'); ?>" target="_blank"><?php echo $t('Service Agreement', 'Contrato de servicio'); ?></a>
                </li>
                <li>
                    <a href="<?= get_permalink(2092) ?>" target="_blank"><?php echo $t('Editorial Policy', 'Política editorial'); ?></a>
                </li>
                <li>
                    <a href="<?= get_permalink(6397) ?>" target="_blank"><?php echo $t('Privacy Policy', 'Política de privacidad'); ?></a>
                </li>
            </ul>

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
