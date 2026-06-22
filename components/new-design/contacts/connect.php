<?php
/**
 * Contacts — "Connect with us" section (new design).
 * Contact data comes from the global ACF Options (same source as the footer).
 * Map is a keyless Google Maps embed built from the address.
 */
$c_address  = get_field('footer_address', 'options');
$c_phone    = get_field('footer_phone', 'options');
$c_email    = get_field('footer_email', 'options');
$c_whatsapp = get_field('footer_whatsapp', 'options');
$c_map      = get_field('footer_map', 'options');

$map_src = $c_address
    ? 'https://www.google.com/maps?q=' . rawurlencode(wp_strip_all_tags($c_address)) . '&output=embed'
    : '';

$socials = [
    'instagram' => 'Instagram',
    'linkedin'  => 'LinkedIn',
    'behance'   => 'Behance',
    'facebook'  => 'Facebook',
];

// Uniform inline line-icons (24x24, stroke 1.6) so all four contact rows match.
$ico_phone = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6.5 3.5h3l1.2 4-2 1.3a12 12 0 005.5 5.5l1.3-2 4 1.2v3a2 2 0 01-2.2 2A16 16 0 014.5 5.7 2 2 0 016.5 3.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>';
$ico_mail  = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.6"/><path d="M4 7l8 6 8-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$ico_wa    = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 3.5a8.5 8.5 0 00-7.3 12.8L3.5 20.5l4.4-1.1A8.5 8.5 0 1012 3.5z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><path d="M9.2 8.3c.15-.4.3-.4.55-.4h.4c.15 0 .35 0 .5.4l.55 1.3c.07.16 0 .3-.08.4l-.4.45c-.08.1-.16.23-.08.4a4 4 0 001.9 1.8c.16.07.3 0 .4-.1l.4-.45c.1-.13.25-.16.4-.08l1.25.6c.16.08.25.23.25.4v.4c0 .65-.55 1.15-1.2 1.15a5.6 5.6 0 01-5.3-5.4c0-.4.18-.8.3-1.1z" fill="currentColor"/></svg>';
$ico_pin   = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 21s7-5.4 7-11a7 7 0 10-14 0c0 5.6 7 11 7 11z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/><circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.6"/></svg>';
?>
<section class="connect">
    <div class="container container_small">
        <h2 class="connect__title"><?php echo mfs_t('Connect with us', 'Conecta con nosotros', 'Mit uns vernetzen'); ?></h2>

        <div class="connect__items">
            <?php if ($c_phone): ?>
                <a class="connect__item" href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $c_phone)); ?>">
                    <?php echo $ico_phone; ?>
                    <span><?php echo esc_html($c_phone); ?></span>
                </a>
            <?php endif; ?>

            <?php if ($c_email): ?>
                <a class="connect__item" href="mailto:<?php echo esc_attr($c_email); ?>">
                    <?php echo $ico_mail; ?>
                    <span><?php echo esc_html($c_email); ?></span>
                </a>
            <?php endif; ?>

            <?php if ($c_whatsapp): ?>
                <a class="connect__item" href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $c_whatsapp)); ?>" target="_blank" rel="noopener">
                    <?php echo $ico_wa; ?>
                    <span><?php echo esc_html($c_whatsapp); ?> (WA)</span>
                </a>
            <?php endif; ?>

            <?php if ($c_address): ?>
                <a class="connect__item" href="<?php echo esc_url($c_map ?: $map_src); ?>" target="_blank" rel="noopener">
                    <?php echo $ico_pin; ?>
                    <span><?php echo esc_html($c_address); ?></span>
                </a>
            <?php endif; ?>
        </div>

        <?php if ($map_src): ?>
            <div class="connect__map">
                <iframe
                    src="<?php echo esc_url($map_src); ?>"
                    title="<?php echo esc_attr(mfs_t('Maverick Frame Studio location', 'Ubicación de Maverick Frame Studio', 'Standort von Maverick Frame Studio')); ?>"
                    width="100%" height="100%" style="border:0;"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        <?php endif; ?>

        <ul class="connect__socials socials">
            <?php foreach ($socials as $key => $label):
                $url = get_field($key, 'options');
                if (!$url) continue; ?>
                <li>
                    <a href="<?php echo esc_url($url); ?>" rel="nofollow noopener" target="_blank"
                        aria-label="<?php echo esc_attr(mfs_t('Open', 'Abrir', 'Öffnen') . ' ' . $label); ?>" class="connect__social-link">
                        <?php echo inline_svg('icons/' . $key . '.svg'); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
