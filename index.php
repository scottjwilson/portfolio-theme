<?php get_header(); ?>
        <section class="hero">
            <h1>
                <?php
                $hero_title = get_theme_mod('hero_title', 'Building digital experiences that matter');
                $parts = explode(' ', $hero_title);
                $last_two = array_slice($parts, -2);
                $first_parts = array_slice($parts, 0, -2);
                
                echo implode(' ', $first_parts) . ' <span class="gradient-text">' . implode(' ', $last_two) . '</span>';
                ?>
            </h1>
            <p class="subtitle">
                <?php echo esc_html(get_theme_mod('hero_subtitle', 'Full-stack web developer specializing in modern frameworks and creating performant, accessible applications.')); ?>
            </p>
            <div class="cta-buttons">
                <a href="#projects" class="btn btn-primary">View Projects</a>
                <a href="#contact" class="btn btn-secondary">Get in Touch</a>
            </div>
        </section>

        <section id="projects">
            <h2>Featured Projects</h2>
            <div class="projects-grid">
                <?php
                $projects = new WP_Query(array(
                    'post_type' => 'project',
                    'posts_per_page' => 6,
                ));
                
                if ($projects->have_posts()) :
                    while ($projects->have_posts()) : $projects->the_post();
                        $tags = get_post_meta(get_the_ID(), '_project_tags', true);
                        $url = get_post_meta(get_the_ID(), '_project_url', true);
                        ?>
                        <div class="project-card" <?php if ($url) echo 'onclick="window.open(\'' . esc_url($url) . '\', \'_blank\')" style="cursor: pointer;"'; ?>>
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo wp_trim_words(get_the_content(), 20); ?></p>
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
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                else :
                    // Default projects if none exist
                    ?>
                    <div class="project-card">
                        <h3>E-Commerce Platform</h3>
                        <p>A full-stack e-commerce solution with real-time inventory management and payment processing.</p>
                        <div class="tags">
                            <span class="tag">SvelteKit</span>
                            <span class="tag">PostgreSQL</span>
                            <span class="tag">Stripe</span>
                        </div>
                    </div>
                    <?php
                endif;
                ?>
            </div>
        </section>

        <section id="skills">
            <h2>Skills & Technologies</h2>
            <div class="skills-grid">
                <?php
                $skills = new WP_Query(array(
                    'post_type' => 'skill',
                    'posts_per_page' => -1,
                ));
                
                $skills_by_category = array(
                    'Frontend' => array(),
                    'Backend' => array(),
                    'Tools' => array(),
                );
                
                if ($skills->have_posts()) :
                    while ($skills->have_posts()) : $skills->the_post();
                        $category = get_post_meta(get_the_ID(), '_skill_category', true);
                        if (isset($skills_by_category[$category])) {
                            $skills_by_category[$category][] = get_the_title();
                        }
                    endwhile;
                    wp_reset_postdata();
                    
                    foreach ($skills_by_category as $category => $skill_list) :
                        if (empty($skill_list)) continue;
                        ?>
                        <div class="skill-category">
                            <h3><?php echo esc_html($category); ?></h3>
                            <ul class="skill-list">
                                <?php foreach ($skill_list as $skill) : ?>
                                    <li><?php echo esc_html($skill); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php
                    endforeach;
                else :
                    // Default skills if none exist
                    ?>
                    <div class="skill-category">
                        <h3>Frontend</h3>
                        <ul class="skill-list">
                            <li>SvelteKit / Svelte</li>
                            <li>React / Next.js</li>
                            <li>TypeScript</li>
                            <li>Tailwind CSS / Bootstrap</li>
                        </ul>
                    </div>
                    <?php
                endif;
                ?>
            </div>
        </section>

        <section id="contact" class="contact">
            <h2>Let's Work Together</h2>
            <p>I'm currently available for freelance projects and full-time opportunities.</p>
            <div class="cta-buttons">
                <a href="mailto:<?php echo esc_attr(get_theme_mod('contact_email', 'your.email@example.com')); ?>" class="btn btn-primary">Send Email</a>
                <a href="<?php echo esc_url(get_theme_mod('github_url', 'https://github.com/yourusername')); ?>" target="_blank" class="btn btn-secondary">GitHub</a>
            </div>
        </section>

<?php get_footer(); ?>

<?php