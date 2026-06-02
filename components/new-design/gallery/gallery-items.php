<?php
$categories = get_posts([
    'post_type' => 'gallery',
    'posts_per_page' => -1,
    'post_parent' => 0,
    'orderby' => 'menu_order',
]);

if($categories): ?>
<div class="container">
    <div class="gallery-items">
        <div class="gallery-items__select-mob">
            <select class="gallery-items__select-mobile js-gallery-mobile" aria-label="Select Gallery Category">
                <option value="all" selected>All</option>
                <?php foreach($categories as $cat):
                    $subcats = get_posts([
                        'post_type' => 'gallery',
                        'posts_per_page' => -1,
                        'post_parent' => $cat->ID,
                    ]);
                    ?>
                    <?php if($subcats): ?>
                        <?php foreach($subcats as $sub): ?>
                            <option value="<?php echo sanitize_title($cat->post_title . '-' . $sub->post_title); ?>">
                                <?php echo esc_html($sub->post_title . ' (' . $cat->post_title . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="<?php echo sanitize_title($cat->post_title); ?>">
                            <?php echo esc_html($cat->post_title); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <span class="js-gallery-select-title"></span>
        </div>

        <ul class="gallery-items__tabs">
            <li>
                <button class="js-gallery-tab-btn" data-tab="all">All</button>
            </li>
            <?php foreach($categories as $cat): ?>
                <li class="js-category">
                    <?php
                    $subcats = get_posts(['post_type'=>'gallery','posts_per_page'=>-1,'post_parent'=>$cat->ID]);
                    $has_sub = !empty($subcats);
                    ?>
                    <button class="js-gallery-tab-btn js-category-btn <?php echo $has_sub?'has-sub':''; ?>" <?php echo $has_sub?'':'data-tab="'.sanitize_title($cat->post_title).'"'; ?>>
                        <?php echo esc_html($cat->post_title); ?>
                    </button>
                    <?php if($has_sub): ?>
                        <ul>
                            <?php foreach($subcats as $sub): ?>
                                <li>
                                    <button class="js-gallery-tab-btn" data-tab="<?php echo sanitize_title($cat->post_title . '-' . $sub->post_title); ?>">
                                        <?php echo esc_html($sub->post_title); ?>
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="gallery-items__tabs-content">
            <div class="gallery-items__items js-gallery-tab-content js-gallery" id="all">
                <?php
                foreach($categories as $cat):
                    $items = get_field('items', $cat->ID);
                    if($items):
                        foreach($items as $item):
                            $class = 'col-' . str_replace([' ', '/'], ['', '-'], $item['size']);
                            $class .= ' mob-col-' . str_replace([' ', '/'], ['', '-'], $item['size_mob']);
                            echo get_template_part('components/new-design/gallery/gallery-item', null, array_merge($item, ['gallery_id'=>'all','class'=>$class]));
                        endforeach;
                    endif;

                    $subcats = get_posts(['post_type'=>'gallery','posts_per_page'=>-1,'post_parent'=>$cat->ID]);
                    foreach($subcats as $sub):
                        $sub_items = get_field('items', $sub->ID);
                        if($sub_items):
                            foreach($sub_items as $item):
                                $class = 'col-' . str_replace([' ', '/'], ['', '-'], $item['size']);
                                $class .= ' mob-col-' . str_replace([' ', '/'], ['', '-'], $item['size_mob']);
                                echo get_template_part('components/new-design/gallery/gallery-item', null, array_merge($item, ['gallery_id'=>'all','class'=>$class]));
                            endforeach;
                        endif;
                    endforeach;
                endforeach;
                ?>
            </div>

            <?php foreach($categories as $cat):
                $cat_id = sanitize_title($cat->post_title);
                $items = get_field('items', $cat->ID);
            ?>
                <div class="gallery-items__items js-gallery-tab-content js-gallery" id="<?php echo $cat_id; ?>">
                    <?php if($items):
                        foreach($items as $item):
                            $class = 'col-' . str_replace([' ', '/'], ['', '-'], $item['size']);
                            $class .= ' mob-col-' . str_replace([' ', '/'], ['', '-'], $item['size_mob']);
                            echo get_template_part('components/new-design/gallery/gallery-item', null, array_merge($item, ['gallery_id'=>$cat_id,'class'=>$class]));
                        endforeach;
                    endif; ?>
                </div>

                <?php
                    $subcats = get_posts(['post_type'=>'gallery','posts_per_page'=>-1,'post_parent'=>$cat->ID]);
                    foreach($subcats as $sub):
                        $sub_id = sanitize_title($cat->post_title . '-' . $sub->post_title);
                        $sub_items = get_field('items', $sub->ID);
                ?>
                    <div class="gallery-items__items js-gallery-tab-content js-gallery" id="<?php echo $sub_id; ?>">
                        <?php if($sub_items):
                            foreach($sub_items as $item):
                                $class = 'col-' . str_replace([' ', '/'], ['', '-'], $item['size']);
                                $class .= ' mob-col-' . str_replace([' ', '/'], ['', '-'], $item['size_mob']);
                                echo get_template_part('components/new-design/gallery/gallery-item', null, array_merge($item, ['gallery_id'=>$sub_id,'class'=>$class]));
                            endforeach;
                        endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>