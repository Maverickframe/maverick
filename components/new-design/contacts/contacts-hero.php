<?php
/**
 * Contacts hero content (new design): standard breadcrumbs + site-standard
 * highlighted heading (js-highlight / text-highlight with <em>) + subtext.
 * Rendered inside the 2-column .contacts-top grid by the template.
 */
$is_es = function_exists('pll_current_language') && pll_current_language() === 'es';
$home  = $is_es && function_exists('pll_home_url') ? pll_home_url('es') : home_url('/');

$title_html = get_post_meta(get_the_ID(), 'hero_title_html', true);
if (!$title_html) {
    $title_html = $is_es
        ? 'Trabaja con un equipo de visualización que <em>entiende lo que tu proyecto necesita</em>'
        : 'Work with a visualization team that <em>understands what your project needs</em>';
}

$subtitle = get_post_meta(get_the_ID(), 'hero_subtitle', true);
if (!$subtitle) {
    $subtitle = mfs_t(
        'Tell us what you’re working on, and our team will get back to you with the right next step.',
        'Cuéntanos en qué estás trabajando y nuestro equipo te responderá con el siguiente paso adecuado.'
    );
}
?>
<div class="contacts-hero">
    <?php echo get_template_part('components/new-design/breadcrumbs', null, [
        'breadcrumbs' => [
            ['link' => esc_url($home), 'name' => mfs_t('Home', 'Inicio')],
        ],
    ]); ?>

    <h1 class="contacts-hero__title"><?php echo wp_kses_post($title_html); ?></h1>

    <p class="contacts-hero__subtitle"><?php echo esc_html($subtitle); ?></p>
</div>
