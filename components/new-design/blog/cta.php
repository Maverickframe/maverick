<?php
$ctaImageDefault = get_field('blog_cta_img_default', 2055); // From Blog Page
$ctaImage = get_field('blog_cta_image') ? get_field('blog_cta_image') : $ctaImageDefault;
$ctaTitle = get_field('blog_cta_title') ?? 'Turn Ideas Into Visual Stories';
$ctaText = get_field('blog_cta_text') ?? 'Whether you’re planning a product launch, marketing campaign, or real estate project, high-quality CGI helps communicate ideas clearly and accelerate decision-making. Our team creates photorealistic visuals used in marketing, presentations, and pre-sales worldwide.';
$ctaButton = get_field('blog_cta_btn');
$ctaBtnLink = isset($ctaButton['link']) ? $ctaButton['link'] : home_url('/contacts/');
$ctaBtnTitle = isset($ctaButton['title']) ? $ctaButton['title'] : 'Book a call';
$ctaBtnVariant = isset($ctaButton['color']) ? $ctaButton['color'] : 'btn-cta';
?>

<section class="article-page__cta">
    <img class="article-page__cta-img" src="<?= $ctaImage; ?>"
        alt="Luxury Car Steering Wheel Interior Detail With Premium Lighting">

    <div class="article-page__cta-content">
        <h2 class="article-page__cta-title"><?= $ctaTitle; ?></h2>

        <div class="article-page__cta-text"><?= $ctaText; ?></div>

        <a href="<?= $ctaBtnLink; ?>" class="article-page__cta-button black-color <?= $ctaBtnVariant; ?>"><?= $ctaBtnTitle; ?></a>
    </div>
</section>