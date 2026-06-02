<?php
$faqItems = get_field('faq_items');
?>

<?php if ($faqItems): ?>
    <section class="faq<?= $args['class'] ?? null; ?>">
        <div class="container container_small">
            <div class="faq__info">
                <h2><?= get_field('faq_title') ?? 'Maverick Frame Blog FAQ'; ?></h2>
                <?php if(get_field('faq_description')): ?>
                    <div class="faq__description">
                        <?php the_field('faq_description'); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="faq__items js-faq">
                <?php
                while (have_rows('faq_items')):
                    the_row();
                    $title = get_sub_field('title');
                    $description = get_sub_field('description');
                    ?>
                    <div class="faq-item js-faq-item js-reveal">
                        <button type="button" class="btn faq-item__btn js-faq-btn">
                            <span>
                                <?= $title; ?>
                            </span>
                        </button>

                        <div class="faq-item__answer">
                            <div class="faq-item__answer-inner">
                                <?= $description; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                ?>
            </div>
        </div>
    </section>
<?php endif; ?>