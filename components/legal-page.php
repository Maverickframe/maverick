<section class="inner-page contacts-page">
    <div class="container">
        <div class="contacts-page__imgs contacts-page__imgs_legal">
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-1.webp" alt="Contacts 1" width="211" height="116">
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-2.webp" alt="Contacts 2" width="211" height="116">
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-3.webp" alt="Contacts 3" width="211" height="116">
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-5.webp" alt="Contacts 5" width="211" height="116">
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-4.webp" alt="Contacts 4" width="211" height="116">
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-6.webp" alt="Contacts 6" width="211" height="116">
        </div>

        <section class="contacts-section contacts-section_legal">
            <div class="contacts-section__container contacts-section__container_legal">
                <div class="contacts-section__main">
                    <h2 class="section-title section-title_contacts">Concept Vibe Ltd</h2>

                    <div class="contacts-section__items contacts-section__items_legal">
                        <?php echo get_template_part( 'components/contacts-item', null, array( 
                                'ico' => 'address',
                                'title' => 'Address',
                                'link' => 'https://goo.gl/maps/kzR3frcsGSS1DuPq6',
                                'link_title' => get_field('footer_address', 'options'),
                                'class' => 'contacts-item_address'
                            )
                        ); ?>
                        <?php echo get_template_part( 'components/contacts-item', null, array( 
                                'ico' => 'register',
                                'title' => 'Registration number:',
                                'link_title' => '14547578',
                                'class' => 'contacts-item_register'
                            )
                        ); ?>
                        <?php echo get_template_part( 'components/contacts-item', null, array( 
                                'ico' => 'email',
                                'title' => 'Email',
                                'link' => 'mailto:'.get_field('footer_email', 'options'),
                                'link_title' => get_field('footer_email', 'options'),
                                'class' => 'contacts-item_email'
                            )
                        ); ?>
                    </div>
                </div>

                <div class="contacts-section__img contacts-section__img_legal">
                    <h2 class="contacts-section__title">Legal</h2>
                    <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contact-us.jpg" alt="Contact us" width="587" height="519" class="lazyload">
                </div>
            </div>
        </section>
    </div>
</section>
