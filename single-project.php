<?php get_header(); ?>

<?php if (have_posts()):
    while (have_posts()):

        the_post();
        // ACF fields
        $overview = get_field("project_overview");
        $role = get_field("role");
        $project_type = get_field("project_type");
        $stack = get_field("stack");
        $focus = get_field("focus");
        $url = get_field("project_url");
        $responsibilities = get_field("project_responsibilities");
        $problems = get_field("project_problems");
        $solutions = get_field("project_solutions");

        $has_quick_facts = $role || $project_type || $stack || $focus;
        $tags = $stack ? array_map("trim", explode(",", $stack)) : [];
        ?>

<article class="project-single">
    <a href="<?php echo home_url(
        "/#projects",
    ); ?>" class="back-link">&larr; Back to Projects</a>

    <header class="project-header">
        <?php if ($tags): ?>
            <div class="tags">
                <?php foreach ($tags as $tag): ?>
                    <span class="tag"><?php echo esc_html($tag); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if ($url): ?>
            <a href="<?php echo esc_url(
                $url,
            ); ?>" target="_blank" rel="noopener noreferrer" class="project-link">
                View Live Project &rarr;
            </a>
        <?php endif; ?>
    </header>

    <?php if (has_post_thumbnail()): ?>
        <div class="project-hero-image">
            <?php the_post_thumbnail("large", ["loading" => "eager"]); ?>
        </div>
    <?php endif; ?>

    <?php if ($overview || $has_quick_facts): ?>
        <div class="project-body">
            <?php if ($overview): ?>
                <section class="project-overview">
                    <h2>Overview</h2>
                    <div class="project-content">
                        <?php echo $overview; ?>
                    </div>
                </section>
            <?php endif; ?>

    <div class="project-footer">
        <a href="<?php echo home_url(
            "/#projects",
        ); ?>" class="btn btn-secondary">&larr; Back to Projects</a>
        <?php if ($url): ?>
            <a href="<?php echo esc_url(
                $url,
            ); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">View Live Project &rarr;</a>
        <?php endif; ?>
    </div>
</article>

<?php
    endwhile;
endif; ?>

<?php get_footer(); ?>
