<section class="contacts-front-section" id="contacts">
    <div class="container contacts-front-section__container">
        <div class="contacts-front-section__main">
            <h3 class="contacts-front-section__title"><?php the_field($args['contacts_title'] ?? null, $args['contacts_from'] ?? null); ?></h3>
            <div class="contacts-front-section__desc"><?php the_field($args['contacts_desc'] ?? null, $args['contacts_from'] ?? null); ?></div>

            <div class="contacts-front-section__items">
                <?php echo get_template_part( 'components/contacts-item', null, array( 
                        'ico' => 'phone',
                        'link' => 'tel:'.get_field('footer_phone', 'options'),
                        'link_title' => get_field('footer_phone', 'options'),
                    )
                ); ?>
                
                <?php 
                    echo get_template_part( 'components/contacts-item', null, array( 
                        'ico' => 'wa',
                        'link' => 'https://wa.me/'.get_field('footer_whatsapp', 'options'),
                        'link_title' => get_field('footer_whatsapp', 'options'),
                    )
                ); 
                ?>
            
                <?php echo get_template_part( 'components/contacts-item', null, array( 
                        'ico' => 'email',
                        'link' => 'mailto:'.get_field('footer_email', 'options'),
                        'link_title' => get_field('footer_email', 'options'),
                        'class' => 'contacts-item_email'
                    )
                ); ?>
            </div>

            <div class="contacts-front-section__clients">
                <?php
                    $currentPage = isset($args['current_companies']) ? get_the_ID() : 6; 
                    while( have_rows('contacts_companies', $currentPage)) : the_row();
                        $img = get_sub_field('img');
                ?>
                    <div>
                        <?php lazy_attachment($img, 'full'); ?>
                    </div>
                <?php
                    endwhile; 
                ?>
            </div>
        </div>
        
        <div class="contacts-front-section__book">
            <h2 class="contacts-front-section__book-title"><?php the_field($args['contacts_book_title'] ?? null, $args['contacts_from'] ?? null); ?></h2>

            <form action="" method="POST" class="js-contacts-form contacts-form" data-link="<?php echo $args['download'] ?? null; ?>">
                <input type="hidden" name="title" value="<?php the_title(); ?> / Get in touch">

                <label class="contacts-form__input">
                    <input type="text" name="Name" placeholder="Name">

                    <span class="contacts-form__label">
                        Name
                    </span>

                    <hr />
                </label>

                <label class="contacts-form__input">
                    <input type="email" name="Email" placeholder="Email">

                    <span class="contacts-form__label">
                        Email
                    </span>

                    <span class="contacts-form__error">
                        It is not email
                    </span>

                    <hr />
                </label>

                <label class="contacts-form__input">
                    <input type="tel" name="Phone" placeholder="Phone">

                    <span class="contacts-form__label">
                        Phone
                    </span>

                    <span class="contacts-form__error">
                        It is required
                    </span>

                    <hr />
                </label>

                <label class="contacts-form__input message">
                    <input type="text" name="Message" placeholder="Message">

                    <span class="contacts-form__label">
                        Message
                    </span>

                    <hr />
                </label>

                <button class="btn department-section__link" type="submit">
                    <svg width="14.5625rem" height="3rem" viewBox="0 0 233 48" class="border">
                        <polyline points="232,1 232,47 1,47 1,1 232,1" class="bg-line" />
                        <polyline points="232,1 232,47 1,47 1,1 232,1" class="hl-line" />
                    </svg>
                    <?php the_field($args['contacts_book_btn_title'] ?? null, $args['contacts_from'] ?? null); ?>
                </button>

                <div class="contacts-form__success">
                    Thank you! We will contact you soon.
                </div>
            </form>
        </div>
    </div>
</section>
