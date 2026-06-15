<?php
$post_type = $args['post_type'];

$cat = isset($_POST['cat']) && $_POST['cat'] != 'all' ? urlencode($_POST['cat']) : null;
$subcat = isset($_POST['subcat']) && $_POST['subcat'] != 'all' ? urlencode($_POST['subcat']) : null;
$tag = isset($_POST['tag']) && $_POST['tag'] != 'all' ? urlencode($_POST['tag']) : null;
$paged = $_POST['current_page'] ?? 1;
$tags = get_field('tags');

$categories = get_field('categories');
$categoriesArr = [];
$subCategoriesArr = [];
if ($categories && is_array($categories)) {
	foreach ($categories as $term) {
		$t = is_object($term) ? $term : get_term($term, 'category');

		if ($t && !is_wp_error($t)) {
			if ((int) $t->parent === 0) {
				$categoriesArr[] = $t;
			} else {
				$subCategoriesArr[] = $t;
			}
		}
	}
}

$args = [
	'post_type' => $post_type,
	'posts_per_page' => 6,
	'post_status' => 'publish',
];

$query = new WP_Query($args);

wp_localize_script(
	'main',
	'params',
	[
		'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php',
		'max_page' => $query->max_num_pages,
		'current_page' => $paged,
		'post_type' => $post_type
	]
);
?>

<section class="section section--articles" id="<?= $post_type; ?>">
	<div class="container">
		<div class="section__head section__head--row">
			<?php if ($categoriesArr): ?>
				<button class="section__toggle section__toggle--primary js-articles-category-toggle"
					aria-label="Toggle category filter" data-category="primary" type="button">Category</button>
			<?php endif; ?>

			<form action="#" method="POST" class="section__toggle section__toggle--search js-articles-search"
				aria-label="Search posts">
				<button type="button" class="search-icon js-articles-search-icon svg-icon" aria-label="Toggle search">
					<?= inline_svg("icons/search.svg"); ?>
				</button>

				<input type="search" name="ajax-search" class="js-ajax-search" placeholder="<?php echo esc_attr(mfs_t('Search', 'Buscar')); ?>" aria-hidden="true">

				<button type="button" class="js-articles-search-reset svg-icon" aria-label="Reset posts search"
					aria-hidden="true">
					<?= inline_svg("icons/close.svg"); ?>
				</button>
			</form>

			<?php if ($tags): ?>
				<div class="section__categories section__categories--tags js-articles-tags is-active">
					<button class="pill<?php if ($tag == 'all'): ?> is-active<?php endif; ?>" type="button" data-tag="all"
						aria-label="All posts tags">All</button>

					<?php foreach ($tags as $tagTerm): ?>
						<?php $t = is_object($tagTerm) ? $tagTerm : get_term($tagTerm, 'post_tag'); ?>
						<?php if ($t && !is_wp_error($t)): ?>
							<button class="pill<?php if ((string) $tag === (string) $t->term_id): ?> is-active<?php endif; ?>"
								type="button" data-tag="<?= esc_attr($t->term_id); ?>" aria-label="Tag: <?= esc_html($t->name); ?>">
								<?= esc_html($t->name); ?>
							</button>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<div class="section__toggle section__toggle--sort">
				<div class="--label"><?php echo mfs_t('Sort by:', 'Ordenar por:'); ?></div>

				<div class="--options">
					<select name="orderby" class="my-select js-articles-sort" aria-label="Sort by">
						<option value="latest"><?php echo mfs_t('Latest', 'Más recientes'); ?></option>
						<option value="popular"><?php echo mfs_t('Popular', 'Populares'); ?></option>
					</select>
				</div>
			</div>

			<?php if ($categoriesArr): ?>
				<div class="section__categories section__categories--primary js-articles-category" data-category="primary"
					aria-hidden="true">
					<button class="pill<?php if ($cat == 'all'): ?> is-active<?php endif; ?>" type="button" data-cat="all"
						aria-label="All posts categories">All</button>
					<?php foreach ($categoriesArr as $category): ?>
						<button class="pill<?php if ($cat == $category->term_id): ?> is-active<?php endif; ?>" type="button"
							data-cat="<?= esc_html($category->term_id); ?>"
							aria-label="Category: <?= esc_html($category->name); ?>">
							<?= esc_html($category->name); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<?php if ($subCategoriesArr): ?>
					<div class="section__categories section__categories--secondary js-articles-subcategory"
						data-category="secondary" aria-hidden="true">
						<button type="button" class="section__filters-prev align-center js-articles-category-back"
							aria-label="Back to categories">
							<?= inline_svg("icons/arrow-left.svg"); ?>
							Category
						</button>

						<button class="pill<?php if ($subcat == 'all'): ?> is-active<?php endif; ?>" type="button"
							data-subcat="all" aria-label="All posts subcategories">
							All
						</button>
						<?php foreach ($subCategoriesArr as $subCategory): ?>
							<button class="pill<?php if ($subcat == $subCategory->term_id): ?> is-active<?php endif; ?>"
								type="button" data-parent="<?= esc_attr($subCategory->parent); ?>"
								data-subcat="<?= esc_attr($subCategory->term_id); ?>"
								aria-label="Subcategory: <?= esc_html($subCategory->name); ?>">
								<?= esc_html($subCategory->name); ?>
							</button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>

		<div class="section__notfound not-found js-articles-notfound" aria-hidden="true" role="alert">
			<div class="not-found__title h2"><?php echo mfs_t('Nothing Found', 'No se encontró nada'); ?></div>
			<div class="not-found__text"><?php echo mfs_t("Oops! We couldn't find any results matching your search. Please try different keywords or check back later!", '¡Vaya! No encontramos resultados para tu búsqueda. Prueba con otras palabras o vuelve más tarde.'); ?></div>
			<button class="btn-main js-articles-search-reset" type="button" aria-label="<?php echo esc_attr(mfs_t('Reset posts filters', 'Restablecer filtros')); ?>"><?php echo mfs_t('Reset filters', 'Restablecer filtros'); ?></button>
		</div>

		<div class="cards cards--3 js-articles-items">
			<?php
			if ($query->have_posts()) {
				while ($query->have_posts()) {
					$query->the_post();

					echo get_template_part("components/new-design/{$post_type}/articles-item", null, [
						'id' => get_the_ID(),
						'class' => " --{$post_type}"
					]);
				}
			}

			wp_reset_postdata();
			?>
		</div>

		<div class="section__btn">
			<button class="btn-secondary-black js-articles-more" type="button" aria-label="<?php echo esc_attr(mfs_t('Load more posts', 'Cargar más entradas')); ?>" <?php if ($query->max_num_pages == $paged): ?> disabled <?php endif; ?>>
				<?php echo mfs_t('Load more', 'Cargar más'); ?>
			</button>
		</div>
	</div>
</section>