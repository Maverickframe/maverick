<?php
/*
* Template Name: Contacts (new design)
* Template Post Type: page
*/
?>

<?php get_header(); ?>
<?php echo get_template_part('components/common/header', null, [
    'class' => 'header_white'
]); ?>

<main class="main contacts-new-page">
    <section class="contacts-top">
        <div class="container container_small">
            <div class="contacts-top__grid">
                <?php echo get_template_part('components/new-design/contacts/contacts-hero'); ?>
                <?php echo get_template_part('components/new-design/contacts/contacts-form'); ?>
            </div>
        </div>
    </section>

    <?php
    // Reuse the homepage "trusted" logos block (language-appropriate front page).
    $front_id = (int) get_option('page_on_front');
    if ($front_id && function_exists('pll_get_post')) {
        $tr = pll_get_post($front_id);
        if ($tr) {
            $front_id = $tr;
        }
    }
    if ($front_id) {
        $front = get_post($front_id);
        if ($front && has_blocks($front->post_content)) {
            foreach (parse_blocks($front->post_content) as $block) {
                if (($block['blockName'] ?? '') === 'acf/trusted') {
                    echo render_block($block);
                    break;
                }
            }
        }
    }
    ?>

    <?php echo get_template_part('components/new-design/contacts/connect'); ?>

    <?php echo get_template_part('components/new-design/contacts/contacts-faq'); ?>
</main>

<?php echo get_template_part('components/common/footer'); ?>
<?php get_footer(); ?>
