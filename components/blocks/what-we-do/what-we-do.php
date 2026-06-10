<?php

$services = [];
$service_index = 0;
$categories = [];

if (have_rows('categories')) :

    while (have_rows('categories')) : the_row();

        $category = [
            'title' => get_sub_field('title'),
            'items' => [],
        ];

        foreach (get_sub_field('items') ?: [] as $item) {

            $current_service_index = null;

            if (!empty($item['modal_title'])) {

                $current_service_index = $service_index;

                $media = [];

                if (!empty($item['modal_images'])) {
                    foreach ($item['modal_images'] as $image_item) {
                        $img_id = $image_item['image'] ?? null;
                        $iframe = $image_item['video_iframe'] ?? null;

                        ob_start();
                        if ($iframe) {
                            echo $iframe;
                        } elseif ($img_id) {
                            lazy_attachment($img_id, 'large');
                        }
                        $html = trim(ob_get_clean());
                        if ($html !== '') {
                            $media[] = $html;
                        }
                    }
                }

                $case = $item['case'] ?? null;
                $case_id = null;

                if ($case instanceof WP_Post) {
                    $case_id = $case->ID;
                } elseif (is_array($case) && !empty($case['ID'])) {
                    $case_id = (int) $case['ID'];
                } elseif (is_numeric($case)) {
                    $case_id = (int) $case;
                }

                $services[] = [
                    'title' => $item['modal_title'],
                    'desc' => $item['modal_desc'],
                    'how_items' => $item['modal_how_items'] ?? [],
                    'media' => $media,
                    'numbers' => $item['modal_numbers'] ?? [],
                    'case_url' => $case_id ? get_permalink($case_id) : '',
                ];

                $service_index++;
            }

            $item['service_index'] = $current_service_index;

            $category['items'][] = $item;
        }

        $categories[] = $category;

    endwhile;

endif;
?>
<section class="what-we-do">
    <div class="container container_small">
        <div class="what-we-do__info">
            <div>
                <p class="section-subtitle"><?php the_field('subtitle'); ?></p>
                <h2><?php echo get_field('title'); ?></h2>
            </div>
    
            <div class="what-we-do__desc">
                <?php echo get_field('description'); ?>
            </div>
        </div>

        <div class="js-tabs-container what-we-do__items">
            <ul class="what-we-do__tabs">
                <?php
                    foreach ($categories as $category):
                        $title = $category['title'];
                ?>
                    <li>
                        <button class="js-tab-btn" data-tab="tab-<?php echo sanitize_title($title); ?>">
                            <span>
                                <?php echo $title; ?>
                            </span>
                        </button>
                    </li>
                <?php
                    endforeach; 
                ?>
            </ul>

           <div class="what-we-do__tabs-content">
               <?php
                   foreach ($categories as $category):
                       $title = $category['title'];
                       $items = $category['items'];
               ?>
                   <div class="what-we-do__tab-content js-tab-content" id="tab-<?php echo sanitize_title($title); ?>">
                       <?php if ($items): ?>
                           <div class="js-what-we-do-slider splide" role="group" aria-label="<?php the_title(); ?>">
                               <div class="splide__track">
                                   <ul class="splide__list">
                                       <?php 
                                           foreach($items as $item): 
                                       ?>
                                           <li class="splide__slide size-<?php echo sanitize_title($item['size']); ?> row-<?php echo sanitize_title($item['row']); ?>">
                                               <?php get_template_part('components/blocks/what-we-do/what-we-do-item', null, [
                                                   'item' => $item, 
                                                   'service_index' => $item['service_index']
                                               ]); ?>
                                           </li>
                                       <?php
                                           endforeach; 
                                       ?>
                                   </ul>
                               </div>
                           </div>
                       <?php endif; ?>
                   </div>
               <?php
                   endforeach; 
               ?>
           </div>

           <div class="what-we-do__cta">
                <?php echo inline_svg('icons/messages.svg'); ?>

                <?php
                    $cta = get_field('cta');
                    if ($cta) :
                        $cta_title = $cta['title'] ?? null;
                        $cta_desc = $cta['description'] ?? null;
                        $cta_advantages = $cta['advantages'] ?? null;
                ?>
                    <div class="what-we-do__cta-info">
                        <?php if ($cta_title) : ?>
                            <h3><?php echo $cta_title; ?></h3>
                        <?php endif; ?>

                        <?php if ($cta_desc) : ?>
                            <p><?php echo $cta_desc; ?></p>
                        <?php endif; ?>

                        <?php if ($cta_advantages) : ?>
                            <ul class="what-we-do__cta-advantages">
                                <?php foreach ($cta_advantages as $advantage) : ?>
                                    <li>
                                        <?php echo inline_svg('icons/check.svg'); ?>
                                        <?php echo $advantage['advantage']; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <button class="btn-main js-modal-open" data-modal="book" type="button">
                    <?php echo mfs_t('Book a call', 'Reservar una llamada'); ?>
                    <?php echo inline_svg('icons/arrow-open.svg'); ?>
                </button>
            </div>
        </div>
    </div>
</section>


<script type="application/json" id="what-we-do-json">
<?php echo wp_json_encode($services); ?>
</script>

<?php echo get_template_part( 'components/common/modals/modal-what-we-do' ); ?> 