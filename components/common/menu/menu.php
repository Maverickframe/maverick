<nav class="js-menu menu">
    <ul class="menu__list">
        <?php
            while( have_rows('menu_items', 'options')) : the_row();
                $keyname = get_sub_field('keyname');
                $label = get_sub_field('label');
                $desktop_label = get_sub_field('desktop_label');
                $link = get_sub_field('link');
                if ($link) {
                    $permalink = get_permalink($link->ID);
                } else {
                    $permalink = null;
                }

                $groups_links = get_sub_field('groups_links');
                $has_group_links = !empty($groups_links);
                $has_submenu = $has_group_links || $keyname === 'our_works' || $keyname === 'resources';
                $with_cta = $keyname === 'services' || $keyname === 'solutions';
        ?>
            <li <?php if($has_submenu): ?>class="menu-item-has-children"<?php endif; ?>>
                <?php if(!$has_submenu): ?>
                    <?php if($permalink): ?>
                        <a href="<?php echo $permalink; ?>"><?php echo $label; ?></a>
                    <?php else: ?>
                        <span><?php echo $label; ?></span>
                    <?php endif; ?>
                <?php else: ?>
                    <span><?php echo $label; ?></span>

                    <div class="menu__submenu <?php echo $keyname; ?> <?php if($with_cta): ?>with-cta<?php endif; ?>">
                        <ul>
                            <li>
                                <?php echo get_template_part('components/common/menu/menu-big-links', null, [
                                    'keyname' => $keyname,
                                    'label' => $label,
                                    'permalink' => $permalink,
                                    'links' => $keyname == 'company' ? $groups_links : null,
                                ]); ?>

                                <?php if($has_group_links && $keyname != 'company'): ?>
                                    <ul class="<?php echo $keyname; ?>">
                                        <?php foreach($groups_links as $group): ?>
                                            <li>
                                                <?php if(!empty($group['link'])): ?>
                                                    <strong><a href="<?php echo get_permalink($group['link']); ?>"><?php echo $group['title']; ?></a></strong>
                                                <?php else: ?>
                                                    <strong><?php echo $group['title']; ?></strong>
                                                <?php endif; ?>
        
                                                <?php if(!empty($group['links'])): ?>
                                                    <ul>
                                                        <?php foreach($group['links'] as $link): ?>
                                                            <li>
                                                                <?php if(isset($link['link'])): ?>
                                                                    <a href="<?php echo get_permalink($link['link']); ?>"><?php echo $link['title']; ?></a>
                                                                <?php else: ?>
                                                                    <span><?php echo $link['title']; ?></span>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>

                                                        <?php
                                                            if (
                                                                isset($desktop_label) && 
                                                                isset($permalink) &&
                                                                $group === end($groups_links)
                                                            ):
                                                        ?>
                                                        <li class="menu__desktop-container">
                                                            <a href="<?php echo $permalink; ?>" class="menu__desktop-link"><?php echo $desktop_label; ?></a>
                                                        </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>

                                <?php if($with_cta): ?>
                                    <?php echo get_template_part('components/common/menu/menu-catalog'); ?>
                                <?php endif;?>
                            </li>

                            <?php if($keyname == 'our_works'): ?>
                                <?php echo get_template_part('components/common/menu/menu-our-works'); ?>
                            <?php endif; ?>

                            <?php if($keyname == 'resources'): ?>
                                <?php echo get_template_part('components/common/menu/menu-resources', null, [
                                    'desktop_label' => $desktop_label,
                                    'permalink' => $permalink,
                                ]); ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </li>
        <?php
            endwhile; 
        ?>
    </ul>

    <button class="menu__cta btn-main js-modal-open" data-modal="book" type="button">
        Book a call
    </button>
</nav>