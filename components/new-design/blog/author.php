<?php
$author = get_field('author');
// Polylang filters the post_object relation by language; team posts exist only
// in EN/DE, so on ES the relation is empty. Fall back to the raw stored team ID.
if (!$author) {
    $rawAuthorId = (int) get_post_meta(get_the_ID(), 'author', true);
    if ($rawAuthorId) {
        $author = get_post($rawAuthorId);
    }
}
if ($author) {
    $authorAvatar = get_field('black_photo', $author->ID);
    $authorName = get_the_title($author->ID);
    $authorPosition = get_field('position', $author->ID);
    $authorText = get_field('text', $author->ID);
    $authorLinkedin = get_field('linkedin', $author->ID);
    $authorLink = get_permalink($author->ID);
}
?>

<?php if ($author): ?>
    <section class="article-page__author">
        <div class="article-page__author-header">
            <div class="article-page__author-profile">
                <?php lazy_attachment($authorAvatar, 'full', 'lazy', 'article-page__author-avatar'); ?>

                <div class="article-page__author-info">
                    <h3 class="article-page__author-name">
                        <?= $authorName; ?>
                    </h3>
                    <p class="article-page__author-role">
                        <?= $authorPosition; ?>
                    </p>
                </div>
            </div>

            <div class="article-page__author-socials socials">
                <?php if ($authorLinkedin): ?>
                    <a href="<?= $authorLinkedin; ?>" rel="nofollow noopener" target="_blank" aria-label="Open LinkedIn">
                        <?= inline_svg('icons/linkedin-white.svg'); ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php /*if (have_rows('socials', $author->ID)): ?>
                <div class="article-page__author-socials socials">
                    <?php
                    while (have_rows('socials', $author->ID)):
                        the_row();
                        $name = get_sub_field('name');
                        $link = get_sub_field('link');
                        $icon = strtolower($name) . '-white.svg';
                        ?>
                        <a href="<?= $link; ?>" rel="nofollow noopener" target="_blank" aria-label="Open <?= $name; ?>">
                            <?= inline_svg("icons/{$icon}"); ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            <?php endif; */ ?>
        </div>

        <?php if ($authorText): ?>
            <div class="article-page__author-body">
                <p class="article-page__author-text"><?= $authorText; ?></p>
            </div>
        <?php endif; ?>

        <div class="article-page__author-footer">
            <a href="<?= $authorLink; ?>" class="article-page__author-profile-link">
                <?= mfs_t('View full profile', 'Ver perfil completo', 'Vollständiges Profil ansehen'); ?>
                <span class="svg-icon">
                    <?= inline_svg("icons/arrow-right.svg"); ?>
                </span>
            </a>
        </div>
    </section>
<?php endif; ?>