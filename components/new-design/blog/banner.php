<?php
$bannerImageDefault = get_field('sidebar_banner_img_default', 2055); // From Blog Page
$bannerImage = get_field('sidebar_banner_img') ? get_field('sidebar_banner_img') : $bannerImageDefault;
$bannerTitle = get_field('sidebar_banner_title') ?? 'Professional 3D Rendering Services for Complex Visual Projects';
$bannerText = get_field('sidebar_banner_text') ?? '<ul class="list"><li>Work with Maverick Frame to outsource visualization and creative production to a team of experienced specialists.</li><li>From architectural renders to product visuals and animations, everything you need to present projects with impact.</li></ul>';
$bannerButton = get_field('sidebar_banner_btn');
$bannerBtnTitle = isset($bannerButton['title']) ? $bannerButton['title'] : 'Book a call';
$bannerBtnVariant = isset($bannerButton['color']) ? $bannerButton['color'] : 'btn-cta';
?>

<div class="article-page__banner">
    <img class="article-page__banner-img" src="<?= $bannerImage; ?>"
        alt="<?php echo esc_attr( mfs_t('Architectural CGI Treehouse Resort In Forest Landscape', 'Resort de casa del árbol CGI arquitectónico en un paisaje boscoso', 'Architektonisches CGI-Baumhaus-Resort in Waldlandschaft') ); ?>">

    <div class="article-page__banner-content">
        <h3 class="article-page__banner-title"><?= $bannerTitle; ?></h3>

        <div class="article-page__banner-description"><?= $bannerText; ?></div>

        <button type="button" class="js-modal-open article-page__banner-button black-color <?= $bannerBtnVariant; ?>" data-modal="book"><?= $bannerBtnTitle; ?></button>
    </div>
</div>