<?php
$cases = get_field('cases');
if( $cases ): ?>
<section class="similar-cases">
    <div class="container">
        <p class="section-subtitle">Explore Similar Real Estate Cases</p>

        <div class="similar-cases__items js-reveal">
                <?php foreach( $cases as $case ): 
                    $permalink = get_permalink( $case->ID );

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