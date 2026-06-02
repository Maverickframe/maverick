<?php
$blocks = parse_blocks(get_post_field('post_content', $args['id']));
$articleTitle = get_the_title($args['id']);
foreach ($blocks as $block) {
	if ($block['blockName'] === 'acf/hero-block') {
		$articleTitle = $block['attrs']['data']['title'] ?? get_the_title($args['id']);
		break;
	}
}

$articleImgHoverLogo = $args['hover_logo'] ?? get_field('hover_logo', $args['id']);
$articleImgHoverText = $args['hover_text'] ?? get_field('hover_text', $args['id']);

$articleTags = $args['tags'] ?? get_the_tags($args['id']);
$articleDate = $args['date'] ?? get_the_date('F j, Y', $args['id']);
$articlePermalink = $args['link'] ?? get_permalink($args['id']);
$articleExcerpt = $args['excerpt'] ?? get_the_excerpt($args['id']);
$articleReadTime = $args['read_time'] ?? get_field('read_time', $args['id']);
$articleAuthor = $args['author'] ?? get_field('author', $args['id']);
?>

<article class="case-item<?= $args['class'] ?? null; ?>">
	<a class="case-item__link" href="<?= $articlePermalink; ?>">
		<span class="case-item__img">
			<?php lazy_attachment(get_post_thumbnail_id($args['id']), 'large'); ?>

			<?php if ($articleImgHoverLogo || $articleImgHoverText): ?>
				<span class="case-item__hover">
					<?= $articleImgHoverText; ?>
				</span>
			<?php endif; ?>
		</span>

		<div class="case-item__info">
			<time datetime="<?= get_the_date('Y-m-d'); ?>" class="case-item__date">
				<?= $articleDate; ?>
			</time>

			<h3 class="case-item__title<?= $args['class_title'] ?? null; ?>">
				<?= $articleTitle; ?>
			</h3>

			<?php if ($articleExcerpt): ?>
				<p class="case-item__excerpt">
					<?= $articleExcerpt; ?>
				</p>
			<?php endif; ?>

			<span class="case-item__arrow">
				<?= inline_svg($args['read_more_icon'] ?? 'icons/arrow-right-accent.svg'); ?>
			</span>
		</div>
	</a>
</article>