<?php
$cases = get_field('cases');
$no_stretch = get_field('no_stretch');
if( $cases ): ?>
<section class="our-work js-reveal">
    <div class="container">
        <div class="our-work__info">
            <div>
                <p class="section-subtitle"><?php echo mfs_t('Our work', 'Nuestro trabajo', 'Unsere Arbeiten'); ?></p>

                <h2><?php the_field('title'); ?></h2>
            </div>
    
            <p><?php the_field('description'); ?></p>
        </div>

        <div class="our-work__items <?php if($no_stretch):?>no-stretch<?php endif; ?>">
                <?php foreach( $cases as $case ): 
                    $blocks = parse_blocks(get_post_field('post_content', $case->ID));
                    $hero_title = '';
                    foreach ($blocks as $block) {
                        if ($block['blockName'] === 'acf/hero-block') {
                            $hero_title = $block['attrs']['data']['title'] ?? '';
                            break;
                        }
                    }
                ?>
                    <?php echo get_template_part('components/common/case-item', null, [
                        'link' => get_permalink( $case->ID ),
                        'title' => $hero_title ?: get_the_title($case->ID),
                        'id' => $case->ID
                    ]); ?>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
</section>
<?php endif; ?>