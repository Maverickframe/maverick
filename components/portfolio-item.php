<?php 
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

<div class="portfolio-item <?php echo $args['class']; ?> js-portfolio-item" data-groups='[<?php echo implode(',', $catNamesGroups); ?>]'>
    <a href="<?php the_permalink(); ?>" class="portfolio-item__link">    
        <?php 
            if (isset($args['index']) && $args['index'] < (wp_is_mobile() ? 8 : 10)) {
                echo wp_get_attachment_image(get_post_thumbnail_id(get_the_ID()), 'large'); 
            } else {
                lazy_attachment(get_post_thumbnail_id(get_the_ID()), 'large'); 
            }
        ?>
        <span>
        
            <?php the_title(); ?> | <?php echo implode(', ', $catNames); ?>
            <br>+open
        </span>
    </a>
</div>