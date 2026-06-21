<?php
$trendingCat = get_field('trending_cat');

if ($trendingCat) {
	$trendingPosts = get_posts(array(
		'post_type' => 'blog',
		'category' => $trendingCat->term_id,
		'numberposts' => 5
	));

	$trendingSlicedPosts = array_slice($trendingPosts, 1);

	$trendingTopPost = $trendingPosts[0];
	$trendingTopAuthor = get_field('author', $trendingTopPost->ID);
	$trendingTopReadTime = get_field('read_time', $trendingTopPost->ID);
}

// No trending posts (e.g. empty ES blog hub before any ES posts exist) — skip the whole section.
if (empty($trendingTopPost)) {
	return;
}
?>

<section class="trending section">
	<div class="container">
		<div class="section__head js-reveal">
			<p class="section__eyebrow"><?php the_field('trending_tag'); ?></p>
			<h2 class="section__title h1"><?php the_field('trending_title'); ?></h2>
			<div class="section__text"><?php the_field('trending_desc'); ?></div>
		</div>

		<?php if ($trendingTopPost): ?>
			<?php echo get_template_part('components/new-design/blog/articles-item', null, [
				'id' => $trendingTopPost->ID,
				'class' => ' --trending --featured',
			]); ?>

			<div class="cards cards--4 scroll-snap js-reveal">
				<?php foreach ($trendingSlicedPosts as $trendingPost) {
					echo get_template_part('components/new-design/blog/articles-item', null, [
						'id' => $trendingPost->ID,
						'class' => ' --trending'
					]);
				} ?>
			</div>
		<?php endif; ?>

		<div class="section__btn">
			<a href="#blog" class="btn-main js-trigger-blog">
				<?php echo mfs_t('View all rankings', 'Ver todos los artículos', 'Alle Artikel ansehen'); ?>
			</a>
		</div>
	</div>
</section>