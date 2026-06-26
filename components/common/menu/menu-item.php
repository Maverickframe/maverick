<?php
/**
 * One top-level menu item (shared by the ACF-driven EN/ES menu and the code-defined DE menu).
 * Receives a normalized row via get_template_part $args:
 *   keyname, label, desktop_label, permalink, groups_links[]
 * The markup is the original menu.php per-item markup, extracted verbatim so EN/ES output is
 * unchanged. The only language-aware tweak: the catalog CTA card is suppressed on /de/ because
 * there is no localized DE catalog asset (showing the English one would leak English).
 */
$keyname       = $args['keyname'] ?? '';
$label         = $args['label'] ?? '';
$desktop_label = $args['desktop_label'] ?? '';
$permalink     = $args['permalink'] ?? null;
$groups_links  = $args['groups_links'] ?? array();

$has_group_links = ! empty( $groups_links );
$has_submenu     = $has_group_links || $keyname === 'our_works' || $keyname === 'resources';
$with_cta        = $keyname === 'services' || $keyname === 'solutions';
$show_cta        = $with_cta; // catalog CTA on services/solutions (all languages; DE label localized)
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

        <div class="menu__submenu <?php echo $keyname; ?> <?php if($show_cta): ?>with-cta<?php endif; ?>">
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
                                        </ul>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if($show_cta): ?>
                        <?php echo get_template_part('components/common/menu/menu-catalog'); ?>
                    <?php endif;?>
                </li>

                <?php if($has_group_links && $keyname != 'company' && $permalink && $desktop_label): ?>
                    <li class="menu__submenu-footer">
                        <a href="<?php echo $permalink; ?>" class="menu__submenu-alllink"><?php echo $desktop_label; ?></a>
                    </li>
                <?php endif; ?>

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
