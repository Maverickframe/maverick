<?php
/**
 * Contacts hero content (new design): standard breadcrumbs + site-standard
 * highlighted heading (js-highlight / text-highlight with <em>) + subtext.
 * Rendered inside the 2-column .contacts-top grid by the template.
 */
$mfs_hero_lang = mfs_lang();
$home  = ( $mfs_hero_lang !== 'en' && function_exists('pll_home_url') ) ? pll_home_url($mfs_hero_lang) : home_url('/');

$title_html = get_post_meta(get_the_ID(), 'hero_title_html', true);
if (!$title_html) {
    $title_html = mfs_t(
        'Work with a visualization team that <em>understands what your project needs</em>',
        'Trabaja con un equipo de visualización que <em>entiende lo que tu proyecto necesita</em>',
        'Arbeiten Sie mit einem Visualisierungsteam, das <em>versteht, was Ihr Projekt braucht</em>'
    );
}

$subtitle = get_post_meta(get_the_ID(), 'hero_subtitle', true);
if (!$subtitle) {
    $subtitle = mfs_t(
        'Tell us what you’re working on, and our team will get back to you with the right next step.',
        'Cuéntanos en qué estás trabajando y nuestro equipo te responderá con el siguiente paso adecuado.', 'Erzählen Sie uns, woran Sie arbeiten, und unser Team meldet sich mit dem passenden nächsten Schritt.'
    );
}
?>
<div class="contacts-hero">
    <?php echo get_template_part('components/new-design/breadcrumbs', null, [
        'breadcrumbs' => [
            ['link' => esc_url($home), 'name' => mfs_t('Home', 'Inicio', 'Startseite')],
        ],
    ]); ?>

    <h1 class="contacts-hero__title"><?php echo wp_kses_post($title_html); ?></h1>

    <p class="contacts-hero__subtitle"><?php echo esc_html($subtitle); ?></p>
</div>
