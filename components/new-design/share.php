<div class="share<?= $args['class'] ?? null; ?>" <?= $args['props'] ?? null; ?>>
    <a href="https://www.linkedin.com/shareArticle?title=<?= urlencode(get_the_title()); ?>&url=<?= get_the_permalink(); ?>"
        target="_blank" rel="nofollow noopener" aria-label="Linkedin">
        <?= inline_svg('icons/linkedin-white.svg'); ?>
    </a>
    <a href="https://x.com/intent/post?text=<?= urlencode(get_the_title()); ?>&url=<?= urlencode(get_the_permalink()); ?>"
        target="_blank" rel="nofollow noopener" aria-label="X">
        <?= inline_svg('icons/x-white.svg'); ?>
    </a>
    <a href="https://www.facebook.com/sharer.php?t=<?= urlencode(get_the_title()); ?>&u=<?= get_the_permalink(); ?>"
        target="_blank" rel="nofollow noopener" aria-label="Facebook">
        <?= inline_svg('icons/facebook-white.svg'); ?>
    </a>
    <a href="https://reddit.com/submit?title=<?= urlencode(get_the_title()); ?>&url=<?= urlencode(get_the_permalink()); ?>"
        target="_blank" rel="nofollow noopener" aria-label="Reddit">
        <?= inline_svg('icons/reddit-white.svg'); ?>
    </a>
    <a href="mailto:?subject=<?= urlencode(get_the_title()); ?>&body=<?= urlencode(get_the_permalink()); ?>"
        target="_blank" rel="nofollow noopener" aria-label="Mail">
        <?= inline_svg('icons/mail-white.svg'); ?>
    </a>
</div>