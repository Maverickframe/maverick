<?php
$heroTitle = get_field('hero_title') ? get_field('hero_title') : get_the_title();
$heroDesc = get_field('hero_desc') ? get_field('hero_desc') : get_the_excerpt();
$heroPosts = get_field('hero_posts');
?>

<section class="hero-blog">
	<div class="container">
		<div class="hero-blog__head">
			<h1 class="hero-blog__title hero__title reveal-css"><?= $heroTitle; ?></h1>
			<div class="hero-blog__text hero__text reveal-css"><?= $heroDesc; ?></div>
		</div>

		<?php if ($heroPosts): ?>
			<div class="hero-blog__grid cards cards--3 reveal-css">
				<?php foreach ($heroPosts as $i => $heroPost) {
					echo get_template_part('components/new-design/blog/articles-item', null, [
						'id' => $heroPost->ID,
						'class' => $i == 0 ? ' --hero-blog --featured' : ' --hero-blog',
						'title_tag' => 'p', // hero cards sit above the first H2 — keep heading order sequential
					]);
				} ?>
			</div>
		<?php endif; ?>
	</div>
</section>