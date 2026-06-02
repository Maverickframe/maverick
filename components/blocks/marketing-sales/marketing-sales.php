<section class="marketing-sales js-reveal">
    <div class="container">
        <div class="marketing-sales__img desktop">
            <?php lazy_attachment(get_field('img'), 'full'); ?>
        </div>

        <div class="marketing-sales__info">
            <p class="section-subtitle">Marketing & Sales Usage</p>
            <h2><?php the_field('title'); ?></h2>

            <?php
                $blocks = parse_blocks(get_post_field('post_content'));
                    $isPresentation = false;
                    foreach ($blocks as $block) {
                        if ($block['blockName'] === 'acf/visual-results') {
                            $isPresentation = $block['attrs']['data']['is_presentation'] ?? '';
                            break;
                        }
                    }
            ?>
            <div class="marketing-sales__img mobile <?php echo $isPresentation ? 'is-presentation' : ''; ?>">
                <?php lazy_attachment(get_field('img'), 'full'); ?>
            </div>

            <div class="p1">
                <?php the_field('description'); ?>
            </div>

            <div class="marketing-sales__quote"><?php the_field('quote'); ?></div>
        </div>
    </div>
</section>