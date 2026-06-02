<section class="audit-section">
    <div class="container">
        <div class="audit-section__container">
            <?php lazy_attachment(get_field('audit_img'), 'large'); ?>
            <div class="audit-section__info">
                <h2 class="audit-section__title"><?php the_field('audit_title'); ?></h2>
                <div class="audit-section__desc"><?php the_field('audit_desc'); ?></div>
                <button type="button" class="btn hero-section__link js-modal-open" data-modal="download">
                    <svg width="13.125rem" height="3rem" viewBox="0 0 210 48" class="border">
                        <polyline points="209,1 209,47 1,47 1,1 209,1" class="bg-line" />
                        <polyline points="209,1 209,47 1,47 1,1 209,1" class="hl-line" />
                    </svg>
                    SEND
                </button>
            </div>
        </div>
    </div>
</section>