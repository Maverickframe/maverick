<?php 
$add = '';

if (isset($args['num']) && $args['num'] !== 1) {
    $add = '_' . $args['num'];
}
?>
<section class="dev-case-section js-dev-case">
    <div class="container">
        <h2 class="section-title section-title_developers"><?php the_field('case_title' . $add); ?></h2>

        <div class="dev-case-section__info">
            <div class="dev-case-section__images">
                <?php
                    while( have_rows('case_images' . $add)) : the_row();
                        $img = get_sub_field('img'); 
                ?>
                    <?php lazy_attachment($img, 'large'); ?>
                <?php
                    endwhile; 
                ?>
            </div>
            <div class="dev-case-section__desc"><?php the_field('case_desc' . $add); ?></div>
        </div>

        <ul class="dev-case-section__items">
            <?php
                while( have_rows('case_items' . $add)) : the_row();
                    $title = get_sub_field('title'); 
                    $description = get_sub_field('description'); 
            ?>
                <li class="dev-case-section__item">
                    <h3 class="dev-case-section__item-title">
                        <?php echo $title; ?>
                    </h3>
                    <div class="dev-case-section__item-desc">
                        <?php echo $description; ?>
                    </div>
                </li>
            <?php
                endwhile; 
            ?>
        </ul>

        <button class="btn dev-case-section__more js-dev-case-more" type="button">READ MORE</button>

        <div class="dev-case-section__btn">
            <button type="button" class="btn hero-section__link js-modal-open" data-modal="download">
                <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                    <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                </svg>
                DOWNLOAD FULL CASE
            </button>
        </div>
    </div>
</section>