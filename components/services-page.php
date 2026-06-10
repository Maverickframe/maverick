<section class="inner-page services-page">
    <div class="container services-page__container">
        <aside class="services-page__sidebar">
            <h1 class="section-title section-title_services-page"><?php the_title(); ?></h1>

            <div class="services-page__nav-mobile">
                <?php
                    while( have_rows('services')) : the_row();
                        $link_title = get_sub_field('link_title');
                        $link_icon = get_sub_field('link_icon');
                ?>
                    <?php echo get_template_part( 'components/services-item', null, array( 
                            'num' => get_row_index(),
                            'title' => $link_title,
                            'class' => 'services-item_page',
                            'link' => get_page_link( 10 ) . '#'.preg_replace('/\s+/', '_', $link_title),
                            'icon' => $link_icon,
                        )
                    ); ?>
                <?php
                    endwhile; 
                ?>
            </div>
            
            <div class="services-page__nav-desktop">
                <ul class="page-nav">
                    <?php
                        while( have_rows('services')) : the_row();
                            $link_title = get_sub_field('link_title');
                    ?>
                        <li class="page-nav__item">
                            <a href="#<?php echo preg_replace('/\s+/', '_', $link_title); ?>" class="btn page-nav__btn <?php if(get_row_index() === 1): ?>active <?php endif;?>js-services-link"><?php echo $link_title; ?></a>
                        </li>
                    <?php
                        endwhile; 
                    ?>
                </ul>
            </div>
        </aside>

        <div class="services-page__main">
            <div class="services-page__items">
                <?php
                    while( have_rows('services')) : the_row();
                        $title = get_sub_field('title');
                        $description = get_sub_field('description');
                        $img = get_sub_field('img');
                        $link = get_sub_field('read_more_link');
                        $link_title = get_sub_field('link_title');
                ?>
                    <?php echo get_template_part( 'components/sp-item', null, array( 
                        'num' => get_row_index(),
                            'title' => $title,
                            'desc' => $description,
                            'img' => $img,
                            'link' => $link,
                            'id' => preg_replace('/\s+/', '_', $link_title),
                        )
                    ); ?>
                <?php
                    endwhile; 
                ?>
            </div>

            <div class="services-page__form sp-form">
                <div class="sp-form__img">
                    <h2 class="sp-form__title"><?php echo mfs_t('Contact us', 'Contáctanos'); ?></h2>
                    <img src="<?php echo get_template_directory_uri_vite(); ?>/img/services-contacts.jpg" alt="Contact us" width="437" height="238" class="lazyload">
                </div>
                
                <div class="sp-form__desc">
                    If you would like to obtain more information about our services or discuss a potential project, please either contact us directly or use the quick form below.
                </div>

                <div class="sp-form__form">
                    <?php echo get_template_part( 'components/contacts-form' ); ?>
                </div>
            </div>
        </div>
    </div>
</section>