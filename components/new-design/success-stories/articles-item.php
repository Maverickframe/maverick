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
if (isset($args['date'])) {
	$articleDate = $args['date'];
} elseif (mfs_is('es')) {
	$mfs_ts = get_post_timestamp($args['id']);
	$mfs_meses = ['1' => 'enero', '2' => 'febrero', '3' => 'marzo', '4' => 'abril', '5' => 'mayo', '6' => 'junio', '7' => 'julio', '8' => 'agosto', '9' => 'septiembre', '10' => 'octubre', '11' => 'noviembre', '12' => 'diciembre'];
	$articleDate = (int) wp_date('j', $mfs_ts) . ' de ' . $mfs_meses[(string) (int) wp_date('n', $mfs_ts)] . ' de ' . wp_date('Y', $mfs_ts);
} elseif (mfs_is('de')) {
	$mfs_ts = get_post_timestamp($args['id']);
	$mfs_monate = ['1' => 'Januar', '2' => 'Februar', '3' => 'März', '4' => 'April', '5' => 'Mai', '6' => 'Juni', '7' => 'Juli', '8' => 'August', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Dezember'];
	$articleDate = (int) wp_date('j', $mfs_ts) . '. ' . $mfs_monate[(string) (int) wp_date('n', $mfs_ts)] . ' ' . wp_date('Y', $mfs_ts);
} else {
	$articleDate = get_the_date('F j, Y', $args['id']);
}
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