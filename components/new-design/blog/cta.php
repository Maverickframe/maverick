<?php
$mfs_not_en = ( mfs_lang() !== 'en' );
$ctaImageDefault = get_field('blog_cta_img_default', 2055); // From Blog Page
$ctaImage = get_field('blog_cta_image') ? get_field('blog_cta_image') : $ctaImageDefault;
// On non-English pages ignore the English ACF defaults/global values and use the localized strings.
$ctaTitle = ($mfs_not_en ? '' : get_field('blog_cta_title')) ?: mfs_t('Turn Ideas Into Visual Stories', 'Convierte ideas en historias visuales', 'Ideen in visuelle Geschichten verwandeln');
$ctaText = ($mfs_not_en ? '' : get_field('blog_cta_text')) ?: mfs_t('Whether you’re planning a product launch, marketing campaign, or real estate project, high-quality CGI helps communicate ideas clearly and accelerate decision-making. Our team creates photorealistic visuals used in marketing, presentations, and pre-sales worldwide.', 'Ya sea que planifiques el lanzamiento de un producto, una campaña de marketing o un proyecto inmobiliario, el CGI de alta calidad ayuda a comunicar ideas con claridad y acelerar la toma de decisiones. Nuestro equipo crea visuales fotorrealistas usados en marketing, presentaciones y preventas en todo el mundo.', 'Ob Sie einen Produktlaunch, eine Marketingkampagne oder ein Immobilienprojekt planen — hochwertiges CGI hilft, Ideen klar zu vermitteln und Entscheidungen zu beschleunigen. Unser Team erstellt fotorealistische Visuals, die weltweit in Marketing, Präsentationen und im Pre-Sales eingesetzt werden.');
$ctaButton = get_field('blog_cta_btn');
$ctaBtnLink = (isset($ctaButton['link']) && $ctaButton['link']) ? $ctaButton['link'] : home_url('/contacts/');
$ctaBtnTitle = (!$mfs_not_en && isset($ctaButton['title']) && $ctaButton['title']) ? $ctaButton['title'] : mfs_t('Book a call', 'Reserva una llamada', 'Beratung buchen');
$ctaBtnVariant = isset($ctaButton['color']) ? $ctaButton['color'] : 'btn-cta';
?>

<section class="article-page__cta">
    <img class="article-page__cta-img" src="<?= $ctaImage; ?>"
        alt="<?= esc_attr(mfs_t('Luxury Car Steering Wheel Interior Detail With Premium Lighting', 'Detalle interior del volante de un coche de lujo con iluminación premium', 'Innendetail eines Luxusauto-Lenkrads mit Premium-Beleuchtung')); ?>">

    <div class="article-page__cta-content">
        <h2 class="article-page__cta-title"><?= $ctaTitle; ?></h2>

        <div class="article-page__cta-text"><?= $ctaText; ?></div>

        <a href="<?= $ctaBtnLink; ?>" class="article-page__cta-button black-color <?= $ctaBtnVariant; ?>"><?= $ctaBtnTitle; ?></a>
    </div>
</section>