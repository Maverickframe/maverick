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

<div class="portfolio-front-item js-portfolio-front-item" data-groups='[<?php echo implode(',', $catNamesGroups); ?>]'>
    <?php lazy_attachment(get_post_thumbnail_id(get_the_ID()), 'large'); ?>
    <a href="<?php the_permalink(); ?>" class="portfolio-front-item__link">    
        <span>   
            <?php the_title(); ?> | <?php echo implode(', ', $catNames); ?>
        </span>
    </a>
</div>