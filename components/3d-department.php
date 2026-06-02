<section class="department-section">
    <div class="container">
        <h2 class="section-title section-title_department"><?php the_field('department_title'); ?></h2>

        <div class="department-section__desc">
            <?php the_field('department_desc'); ?>
        </div>

        <div class="department-section__items">
            <div class="js-department-slider splide" role="group" aria-label="<?php the_field('department_title') ?? null; ?>">
                <div class="splide__track">
                    <ul class="splide__list">
                        <?php
                            while( have_rows('department_items')) : the_row();
                                $name = get_sub_field('name');
                                $photo = get_sub_field('photo');
                                $position = get_sub_field('position');
                                $description = get_sub_field('description');
                                $department = get_sub_field('department');
                                $department_link = get_sub_field('department_link');
                        ?>
                            <li class="splide__slide">
                                <?php echo get_template_part( 'components/3d-department-item', null, array( 
                                        'name' => $name,
                                        'photo' => $photo,
                                        'position' => $position,
                                        'description' => $description,
                                        'department' => $department,
                                        'department_link' => $department_link,
                                    )
                                ); ?>
                            </li>
                        <?php
                            endwhile; 
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="department-section__cta">
            <p><?php the_field('department_subscription'); ?></p>
            <button type="button" class="btn department-section__link js-modal-open" data-modal="book">
                <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                    <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
                </svg>
                <?php the_field('department_btn'); ?>
            </button>
        </div>
    </div>
</section>