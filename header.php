<?php
$classes = str_replace(".php", "", get_page_template_slug());
$classes = str_replace("templates/", "", $classes);
$classes .= is_single() ? ' single-' . get_post_type() : '';
$classes .= (is_page() && !is_page_template() || is_404()) ? ' single-page' : '';
$classes = trim($classes);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> class="scrollbar <?= $classes; ?>">

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#2d40ae">

    <?php // Connect hints for third-party origins. GTM + HubSpot are now DELAYED
          // (WP Rocket "Delay JS" — they load on first user interaction, not at
          // paint), so eager `preconnect` for them would sit unused and PSI would
          // flag it — dropped. Keep cheap dns-prefetch (DNS only, no socket) for the
          // HubSpot/Google origins the delayed scripts eventually hit. hs-banner
          // removed: the cookie-consent banner is turned off in the HubSpot portal. ?>
    <link rel="dns-prefetch" href="https://js-eu1.hs-scripts.com">
    <link rel="dns-prefetch" href="https://js-eu1.hs-analytics.net">
    <link rel="dns-prefetch" href="https://track-eu1.hubspot.com">
    <link rel="dns-prefetch" href="https://www.google.com">

    <?php
    $portfolioType = gettype(get_field('portfolio_type')) === 'array'
        ? get_field('portfolio_type')
        : array();
    if (get_field('no_index') || $portfolioType === 'private' || (in_array('private', $portfolioType) && !in_array('common', $portfolioType))):
        ?>
        <meta name="robots" content="noindex">
    <?php endif; ?>
    <link rel="manifest" href="<?= get_template_directory_uri(); ?>/site.webmanifest">
    <link rel="icon" href="<?= get_template_directory_uri_vite(); ?>/img/favicon.ico" type="image/x-icon" sizes="any">
    <link rel="icon" href="<?= get_template_directory_uri_vite(); ?>/img/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="<?= get_template_directory_uri_vite(); ?>/img/apple-touch-icon.png">

    <?php if (!isNewDesign()): ?>
        <?php
        $img_id = (wp_is_mobile()
            ? get_field('hero_img_mobile')
            : get_field('hero_img_desktop')) ?? get_post_thumbnail_id(get_the_ID());

        $img_src = wp_get_attachment_image_url($img_id, "full");
        $img_srcset = wp_get_attachment_image_srcset($img_id, "full");
        $img_sizes = wp_get_attachment_image_sizes($img_id, "full");

        if ($img_src):
            ?>
            <link rel="preload" as="image" href="<?= $img_src; ?>" imagesrcset="<?= $img_srcset; ?>"
                imagesizes="<?= $img_sizes; ?>">
        <?php endif; ?>

        <link rel="preload" as="image" href="<?= get_template_directory_uri_vite(); ?>/img/logo.svg">
        <link rel="preload" as="font" type="font/woff2"
            href="<?= get_template_directory_uri_vite(); ?>/fonts/red-hat-display-v14-latin-300.woff2" crossorigin>
        <link rel="preload" as="font" type="font/woff2"
            href="<?= get_template_directory_uri_vite(); ?>/fonts/red-hat-display-v14-latin-500.woff2" crossorigin>
        <link rel="preload" as="font" type="font/woff2"
            href="<?= get_template_directory_uri_vite(); ?>/fonts/red-hat-display-v14-latin-regular.woff2" crossorigin>
        <link rel="preload" as="font" type="font/woff2"
            href="<?= get_template_directory_uri_vite(); ?>/fonts/red-hat-display-v14-latin-700.woff2" crossorigin>
    <?php else: ?>
        <link rel="preload" as="font" type="font/woff2"
            href="<?= get_template_directory_uri_vite(); ?>/fonts/inter-tight-v9-latin-regular.woff2" crossorigin>
        <link rel="preload" as="font" type="font/woff2"
            href="<?= get_template_directory_uri_vite(); ?>/fonts/inter-tight-v9-latin-500.woff2" crossorigin>
    <?php endif; ?>

    <?php wp_head(); ?>

    <?php if ($mfs_common_schema = get_field('common_schema', 'options')): ?>
        <?php
        // common_schema is a single global JSON-LD string (WebSite node) with a
        // hardcoded inLanguage of en-GB. Localize it to the current Polylang
        // language at render time so /de/ and /es/ emit the right language signal.
        // EN keeps en-GB (no replacement). Whitespace-tolerant regex.
        $mfs_schema_lang = mfs_t('en-GB', 'es-ES', 'de'); // EN→en-GB, ES→es-ES, DE→de
        if ($mfs_schema_lang !== 'en-GB') {
            $mfs_common_schema = preg_replace('/("inLanguage"\s*:\s*)"en-GB"/', '$1"' . $mfs_schema_lang . '"', $mfs_common_schema);
        }
        ?>
        <script type="application/ld+json"><?= $mfs_common_schema; ?></script>
    <?php endif; ?>

    <?php if (get_field('schema_org')): ?>
        <?= get_field('schema_org'); ?>
    <?php endif; ?>
</head>

<body id="top">
    <?php if (!defined('IS_VITE_DEVELOPMENT') || IS_VITE_DEVELOPMENT == false): ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T4JS5BJV" height="0" width="0"
                style="display:none;visibility:hidden" title="Google Tag Manager"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    <?php endif; ?>
    <?php // GEO readability (T13): start buffering the page body so footer.php can relocate
          // the site <header> (mega-menu) to the end of <body>. Inert unless enabled
          // (non-prod only). See mfs_defer_menu_* in functions.php. ?>
    <?php mfs_defer_menu_start(); ?>