<?php
if ( ! function_exists( 'mfs_case_url' ) ) {
    /**
     * Portfolio CPT is migrated to success-stories (all /portfolio/* are 301).
     * Resolve the card link straight to the Rank Math redirect target
     * so we never print internal links that redirect. Fallback: /gallery/.
     */
    function mfs_case_url( $post_id = null ) {
        global $wpdb;
        $post_id = $post_id ?: get_the_ID();
        if ( get_post_type( $post_id ) !== 'portfolio' ) {
            return get_permalink( $post_id );
        }
        $pattern = 'portfolio/' . get_post_field( 'post_name', $post_id ) . '/';
        $url_to  = $wpdb->get_var( $wpdb->prepare(
            "SELECT url_to FROM {$wpdb->prefix}rank_math_redirections WHERE status = 'active' AND sources LIKE %s LIMIT 1",
            '%' . $wpdb->esc_like( '"' . $pattern . '"' ) . '%'
        ) );
        return $url_to ?: home_url( '/gallery/' );
    }
}


    $category = get_the_category(); 
    $catNames = array();
    $catNamesGroups = array();

    foreach ($category as $cat) {
        if($cat->term_id != 1 ) {
            array_push($catNames, $cat->name);
            array_push($catNamesGroups, '"'.$cat->name.'"');
        }
    }
?>

<div class="portfolio-front-item js-portfolio-front-item" data-groups='[<?php echo implode(',', $catNamesGroups); ?>]'>
    <?php lazy_attachment(get_post_thumbnail_id(get_the_ID()), 'large'); ?>
    <a href="<?php echo esc_url( mfs_case_url() ); ?>" class="portfolio-front-item__link">    
        <span>   
            <?php the_title(); ?> | <?php echo implode(', ', $catNames); ?>
        </span>
    </a>
</div>