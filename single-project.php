<?php get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post();
    $tags = get_post_meta(get_the_ID(), '_project_tags', true);
    $url = get_post_meta(get_the_ID(), '_project_url', true);
    $challenges_json = get_post_meta(get_the_ID(), '_project_challenges', true);
    $challenges = $challenges_json ? json_decode($challenges_json, true) : array();
    if (!is_array($challenges)) {
        $challenges = array();
    }
?>

<article class="project-single">
    <a href="<?php echo home_url('/#projects'); ?>" class="back-link">&larr; Back to Projects</a>

    <header class="project-header">
        <?php if ($tags) : ?>
            <div class="tags">
                <?php
                $tag_array = array_map('trim', explode(',', $tags));
                foreach ($tag_array as $tag) {
                    echo '<span class="tag">' . esc_html($tag) . '</span>';
                }
                ?>
            </div>
        <?php endif; ?>
        <h1><?php the_title(); ?></h1>
        <?php if ($url) : ?>
            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="project-link">
                View Live Project &rarr;
            </a>
        <?php endif; ?>
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <div class="project-hero-image">
            <?php the_post_thumbnail('large', array('loading' => 'eager')); ?>
        </div>
    <?php endif; ?>

    <section class="project-overview">
        <h2>Overview</h2>
        <div class="project-content">
            <?php the_content(); ?>
        </div>
    </section>

    <?php if (!empty($challenges)) : ?>
        <section class="project-challenges">
            <h2>Challenges & Solutions</h2>
            <?php foreach ($challenges as $challenge) : ?>
                <div class="challenge-card">
                    <?php if (!empty($challenge['problem'])) : ?>
                        <div class="challenge-card__problem">
                            <span class="challenge-label">The Problem</span>
                            <p><?php echo nl2br(esc_html($challenge['problem'])); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($challenge['solution'])) : ?>
                        <div class="challenge-card__solution">
                            <span class="challenge-label">The Solution</span>
                            <p><?php echo nl2br(esc_html($challenge['solution'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <div class="project-footer">
        <a href="<?php echo home_url('/#projects'); ?>" class="btn btn-secondary">&larr; Back to Projects</a>
        <?php if ($url) : ?>
            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">View Live Project &rarr;</a>
        <?php endif; ?>
    </div>
</article>

<?php endwhile; endif; ?>

<?php get_footer(); ?>
