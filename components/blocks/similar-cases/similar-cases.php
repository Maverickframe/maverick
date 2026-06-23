<?php
$cases = get_field('cases');
if( $cases ): ?>
<section class="similar-cases">
    <div class="container">
        <?php if ( is_singular('solutions') ) : ?>
            <div class="similar-cases__head">
                <div class="similar-cases__head-main">
                    <p class="section-subtitle similar-cases__kicker"><?php echo mfs_t('Related cases', 'Casos relacionados'); ?></p>
                    <h2 class="similar-cases__title"><?php echo mfs_t('See how agencies scale creative production with Maverick Frame', 'Mira cómo las agencias escalan su producción creativa con Maverick Frame'); ?></h2>
                </div>
                <p class="similar-cases__desc"><?php echo mfs_t('Explore campaigns where our team delivered product rendering, 3D animation, and digital content production for marketing launches.', 'Explora campañas donde nuestro equipo entregó render de producto, animación 3D y producción de contenido digital para lanzamientos de marketing.'); ?></p>
            </div>
        <?php else : ?>
            <p class="section-subtitle">Explore Similar Real Estate Cases</p>
        <?php endif; ?>

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