
<?php
    // Keys (award/nominee/featured/member) are filter logic — never translate them.
    // Plural labels drive the filter tabs; singular labels drive the per-card badge.
    $type_labels = array(
        'award'    => mfs_t('Awards', 'Premios', 'Auszeichnungen'),
        'nominee'  => mfs_t('Nominations', 'Nominaciones', 'Nominierungen'),
        'featured' => mfs_t('Featured', 'Destacados', 'Vorgestellt'),
        'member'   => mfs_t('Memberships', 'Membresías', 'Mitgliedschaften'),
    );
    $badge_labels = array(
        'award'    => mfs_t('Award', 'Premio', 'Auszeichnung'),
        'nominee'  => mfs_t('Nominee', 'Nominado', 'Nominierung'),
        'featured' => mfs_t('Featured', 'Destacado', 'Vorgestellt'),
        'member'   => mfs_t('Membership', 'Membresía', 'Mitgliedschaft'),
    );

    $rows = array();
    if ( have_rows('items') ) {
        while ( have_rows('items') ) { the_row();
            $type = get_sub_field('type');
            if ( ! isset($type_labels[$type]) ) { $type = 'award'; }
            $rows[] = array(
                'years'       => get_sub_field('years'),
                'image'       => get_sub_field('image'),
                'title'       => get_sub_field('title'),
                'nomination'  => get_sub_field('nomination'),
                'description' => get_sub_field('description'),
                'type'        => $type,
            );
        }
    }

    $present = array();
    foreach ( $rows as $r ) { $present[ $r['type'] ] = true; }
    $tabs = array();
    foreach ( $type_labels as $key => $label ) {
        if ( isset($present[$key]) ) { $tabs[$key] = $label; }
    }
    $show_filter = count($tabs) > 1;
?>

<section class="team-awards team-awards_block">
    <div class="container container_small">
        <div class="team-awards__info">
            <?php if(get_field('subtitle')): ?><p class="section-subtitle"><?php the_field('subtitle'); ?></p><?php endif; ?>
            <h2 class="team-awards__title"><?php the_field('title'); ?></h2>
            <?php if(get_field('description')): ?><p class="team-awards__description"><?php the_field('description'); ?></p><?php endif; ?>
        </div>

        <?php if ( $show_filter ): ?>
            <div class="team-awards__filter" role="tablist" aria-label="Filter recognitions by type">
                <button type="button" class="team-awards__tab is-active" data-filter="all" aria-selected="true"><?php echo esc_html( mfs_t('All', 'Todos', 'Alle') ); ?></button>
                <?php foreach ( $tabs as $key => $label ): ?>
                    <button type="button" class="team-awards__tab" data-filter="<?php echo esc_attr($key); ?>" aria-selected="false"><?php echo esc_html($label); ?></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="team-awards__items">
            <?php foreach ( $rows as $r ):
                $type = $r['type'];
                $badge = $badge_labels[$type];
            ?>
                <div class="team-award js-reveal team-award--<?php echo esc_attr($type); ?>" data-type="<?php echo esc_attr($type); ?>">
                    <div class="team-award__top">
                        <?php if ( $r['years'] ): ?><span class="team-award__year"><?php echo esc_html($r['years']); ?></span><?php endif; ?>
                        <span class="team-award__badge team-award__badge--<?php echo esc_attr($type); ?>"><?php echo esc_html($badge); ?></span>
                    </div>

                    <div class="team-award__img">
                        <?php if ( $r['image'] ): ?>
                            <?php echo lazy_attachment($r['image'], 'full'); ?>
                        <?php elseif ( $r['title'] ): ?>
                            <span class="team-award__mono" aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( preg_replace('/[^A-Za-z]/', '', (string) $r['title']), 0, 1 ) ) ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ( $r['title'] ): ?><h3 class="team-award__title"><?php echo esc_html($r['title']); ?></h3><?php endif; ?>
                    <?php if ( $r['nomination'] ): ?><p class="team-award__nomination"><?php echo esc_html($r['nomination']); ?></p><?php endif; ?>
                    <?php if ( $r['description'] ): ?><p class="team-award__description"><?php echo esc_html($r['description']); ?></p><?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ( $show_filter ): ?>
<script>
(function(){
    if ( window.__mfsTeamAwardsInit ) { return; }
    window.__mfsTeamAwardsInit = true;
    function init(){
        document.querySelectorAll('.team-awards_block').forEach(function(sec){
            var tabs  = sec.querySelectorAll('.team-awards__tab');
            var cards = sec.querySelectorAll('.team-award');
            if ( ! tabs.length ) { return; }
            tabs.forEach(function(tab){
                tab.addEventListener('click', function(){
                    var f = tab.getAttribute('data-filter');
                    tabs.forEach(function(t){
                        var on = ( t === tab );
                        t.classList.toggle('is-active', on);
                        t.setAttribute('aria-selected', on ? 'true' : 'false');
                    });
                    cards.forEach(function(c){
                        var show = ( f === 'all' || c.getAttribute('data-type') === f );
                        c.style.display = show ? '' : 'none';
                    });
                });
            });
        });
    }
    if ( document.readyState !== 'loading' ) { init(); }
    else { document.addEventListener('DOMContentLoaded', init); }
})();
</script>
<?php endif; ?>
