<section class="service-page-contacts contacts-section">
    <div class="container contacts-section__container">
        <div class="contacts-section__main">
            <div>
                <h2 class="section-title section-title_contacts">Get in touch</h2>
    
                <?php echo get_template_part( 'components/contacts-form' ); ?>
            </div>

            <div>
                <h2 class="section-title section-title_contacts">Contacts</h2>
    
                <div class="contacts-section__items">
                    <?php echo get_template_part( 'components/contacts-item', null, array( 
                            'ico' => 'address',
                            'title' => 'Address',
                            'link' => 'https://goo.gl/maps/kzR3frcsGSS1DuPq6',
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
                            'class' => 'contacts-item_desktop  contacts-item_tgwa'
                        )
                        ); 
                    ?>
                </div>
            </div>
        </div>

        <div class="contacts-section__img">
            <h2 class="contacts-section__title">Contact us</h2>
            <img src="<?php echo get_template_directory_uri_vite(); ?>/img/sp-contact.jpg" alt="Contact us" width="437" height="525" class="lazyload">
        </div>
    </div>
</section>