<?php get_header(); ?>

<?php if (have_posts()):
    while (have_posts()):

        the_post();
        $tags = get_post_meta(get_the_ID(), "_project_tags", true);
        $url = get_post_meta(get_the_ID(), "_project_url", true);

        // ACF fields
        $overview = get_field("project_overview");
        $role = get_field("role");
        $project_type = get_field("project_type");
        $stack = get_field("stack");
        $focus = get_field("focus");
        $responsibilities = get_field("project_responsibilities");
        $problems = get_field("project_problems");
        $solutions = get_field("project_solutions");

        $has_quick_facts = $role || $project_type || $stack || $focus;
        ?>

<article class="project-single">
    <a href="<?php echo home_url(
        "/#projects",
    ); ?>" class="back-link">&larr; Back to Projects</a>

    <header class="project-header">
        <?php if ($tags): ?>
            <div class="tags">
                <?php
                $tag_array = array_map("trim", explode(",", $tags));
                foreach ($tag_array as $tag) {
                    echo '<span class="tag">' . esc_html($tag) . "</span>";
                }
                ?>
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

            <?php if ($has_quick_facts): ?>
                <aside class="project-quick-facts">
                    <h3>Quick Facts</h3>
                    <?php if ($role): ?>
                        <div class="quick-fact">
                            <span class="quick-fact__label">Role</span>
                            <span class="quick-fact__value"><?php echo esc_html(
                                $role,
                            ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($project_type): ?>
                        <div class="quick-fact">
                            <span class="quick-fact__label">Type</span>
                            <span class="quick-fact__value"><?php echo esc_html(
                                $project_type,
                            ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($stack): ?>
                        <div class="quick-fact">
                            <span class="quick-fact__label">Stack</span>
                            <span class="quick-fact__value"><?php echo nl2br(
                                esc_html($stack),
                            ); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($focus): ?>
                        <div class="quick-fact">
                            <span class="quick-fact__label">Focus</span>
                            <span class="quick-fact__value"><?php echo nl2br(
                                esc_html($focus),
                            ); ?></span>
                        </div>
                    <?php endif; ?>
                </aside>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($responsibilities): ?>
        <section class="project-responsibilities">
            <h2>What I Did</h2>
            <div class="project-content">
                <?php echo $responsibilities; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($problems || $solutions): ?>
        <section class="project-challenges">
            <h2>Challenges & Solutions</h2>
            <div class="challenge-card">
                <?php if ($problems): ?>
                    <div class="challenge-card__problem">
                        <span class="challenge-label">The Problem</span>
                        <?php echo $problems; ?>
                    </div>
                <?php endif; ?>
                <?php if ($solutions): ?>
                    <div class="challenge-card__solution">
                        <span class="challenge-label">The Solution</span>
                        <?php echo $solutions; ?>
                    </div>
                <?php endif; ?>
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
