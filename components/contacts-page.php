<section class="inner-page contacts-page">
    <div class="container">
        <div class="contacts-page__imgs">
            <div class="contacts-page__imgs-items">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-1.webp" alt="Contacts 1" width="211" height="116" fetchpriority="high">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-2.webp" alt="Contacts 2" width="211" height="116" fetchpriority="high">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-3.webp" alt="Contacts 3" width="211" height="116" fetchpriority="high">
                <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-4.webp" alt="Contacts 4" width="211" height="116" fetchpriority="high">
            </div>

            <div class="contacts-page__desc">
                Our clients get the best results when working closely with our design team. We are terrific listeners with generations’ worth of experience. But, enough about us. 
                <strong>Let’s hear from you.</strong>
            </div>
        </div>

        <div class="contacts-page__main">
            <div>
                <h1 class="section-title section-title_contacts-page">Contacts</h1>
    
                <div class="contacts-section__items contacts-section__items_page">
                    <?php echo get_template_part( 'components/contacts-item', null, array( 
                            'ico' => 'address',
                            'title' => 'Address',
                            'link' => get_field('footer_map', 'options'),
                            'link_title' => get_field('footer_address', 'options'),
                            'class' => 'contacts-item_address'
                        )
                    ); ?>
                    <?php echo get_template_part( 'components/contacts-item', null, array( 
                            'ico' => 'phone',
                            'title' => '<span>Phone</span> / Telegram / WhatsApp',
                            'link' => 'tel:'.get_field('footer_phone', 'options'),
                            'link_title' => get_field('footer_phone', 'options'),
                            'class' => 'contacts-item_mobile'
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
                    <?php echo get_template_part( 'components/contacts-item', null, array( 
                            'ico' => 'phone',
                            'title' => 'Phone',
                            'link' => 'tel:'.get_field('footer_phone', 'options'),
                            'link_title' => get_field('footer_phone', 'options'),
                            'class' => 'contacts-item_desktop'
                        )
                    ); ?>
                    <?php 
                        echo get_template_part( 'components/contacts-item', null, array( 
                            'ico' => 'tgwa',
                            'title' => 'WhatsApp',
                            'link' => 'https://wa.me/'.get_field('footer_whatsapp', 'options'),
                            'link_title' => get_field('footer_whatsapp', 'options'),
                            'class' => 'contacts-item_desktop contacts-item_tgwa'
                        )
                    ); 
                    ?>
                </div>
            </div>

            <div class="contacts-page__form">
                <div class="contacts-page__img">
                    <img src="<?php echo get_template_directory_uri_vite(); ?>/img/contacts-1.webp" alt="Get in touch" loading="lazy" width="343" height="168">
                    
                    <h2 class="contacts-page__img-title"><?php echo mfs_t('Get in touch', 'Ponte en contacto', 'Kontakt aufnehmen'); ?></h2>
                </div>

                <div class="contacts-page__mob-desc">
                    Our clients get the best results when working closely with our design team. We are terrific listeners with generations’ worth of experience. 
                    <br>But, enough about us. Let’s hear from you.
                </div>
    
                <?php echo get_template_part( 'components/contacts-form' ); ?>
            </div>


        </div>
    </div>
</section>
