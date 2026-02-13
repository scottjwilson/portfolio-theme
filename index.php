<?php get_header(); ?>
        <section class="hero">
            <span class="hero-label">WordPress Developer</span>
            <h1>
                <?php
                $hero_title = get_theme_mod(
                    "hero_title",
                    "Building digital experiences that matter",
                );
                $parts = explode(" ", $hero_title);
                $last_two = array_slice($parts, -2);
                $first_parts = array_slice($parts, 0, -2);

                echo implode(" ", $first_parts) .
                    ' <span class="gradient-text">' .
                    implode(" ", $last_two) .
                    "</span>";
                ?>
            </h1>
            <p class="subtitle">
                <?php echo esc_html(
                    get_theme_mod(
                        "hero_subtitle",
                        "Full-stack web developer specializing in modern frameworks and creating performant, accessible applications.",
                    ),
                ); ?>
            </p>
            <div class="cta-buttons">
                <a href="#projects" class="btn btn-primary"><?php echo esc_html(
                    get_theme_mod("hero_btn_primary_text", "View Projects"),
                ); ?></a>
                <a href="#contact" class="btn btn-secondary"><?php echo esc_html(
                    get_theme_mod("hero_btn_secondary_text", "Get in Touch"),
                ); ?></a>
            </div>
        </section>

        <section id="what-i-do" class="value-prop animate-in">
            <h2>I Help Businesses Build WordPress Systems That Actually Scale</h2>
            <div class="value-prop__body">
                <div class="value-prop__text">
                    <p>Most WordPress sites slow down over time. They accumulate plugins, performance issues, and fragile updates.</p>
                    <p>I focus on:</p>
                </div>
                <ul class="value-prop__list">
                    <li>Clean architecture</li>
                    <li>Structured data modeling</li>
                    <li>Core Web Vitals optimization</li>
                    <li>Long-term maintainability</li>
                </ul>
                <p class="value-prop__closing">Your website should be a business asset — not a liability.</p>
            </div>
        </section>

        <section id="services" class="animate-in">
            <h2>Services</h2>
            <div class="services-grid">
                <div class="service-card">
                    <h3>Custom WordPress Development</h3>
                    <ul>
                        <li>Custom themes built from scratch</li>
                        <li>Advanced Custom Fields (ACF)</li>
                        <li>Custom Post Types & relational data structures</li>
                        <li>Structured CMS architecture</li>
                    </ul>
                </div>
                <div class="service-card">
                    <h3>Performance Optimization</h3>
                    <ul>
                        <li>Lighthouse 90+ performance builds</li>
                        <li>Asset optimization & lazy loading</li>
                        <li>Server configuration & caching strategy</li>
                        <li>CDN implementation</li>
                    </ul>
                </div>
                <div class="service-card">
                    <h3>WooCommerce & E-commerce</h3>
                    <ul>
                        <li>Clean checkout flows</li>
                        <li>Performance-optimized product pages</li>
                        <li>Payment gateway integrations</li>
                        <li>Technical SEO foundations</li>
                    </ul>
                </div>
                <div class="service-card">
                    <h3>Maintenance & Technical Support</h3>
                    <ul>
                        <li>Plugin conflict resolution</li>
                        <li>Security hardening</li>
                        <li>Hosting migrations</li>
                        <li>Ongoing update strategy</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="performance" class="value-prop animate-in">
            <h2>Performance Isn't an Afterthought</h2>
            <div class="value-prop__body">
                <p class="value-prop__text">Every site I build or optimize is engineered to:</p>
                <ul class="value-prop__list">
                    <li>Achieve high 90s Lighthouse scores</li>
                    <li>Load quickly on mobile and desktop</li>
                    <li>Maintain clean code structure</li>
                    <li>Remain stable through updates</li>
                </ul>
                <p class="value-prop__closing">Speed impacts conversions, SEO, and user trust. I treat performance as infrastructure — not a cosmetic metric.</p>
            </div>
        </section>

        <section id="projects" class="animate-in">
            <h2>Featured Projects</h2>
            <div class="projects-grid">
                <?php
                $projects = new WP_Query([
                    "post_type" => "project",
                    "posts_per_page" => 6,
                    "orderby" => "menu_order",
                    "order" => "ASC",
                ]);

                $project_index = 0;
                if ($projects->have_posts()):
                    while ($projects->have_posts()):

                        $projects->the_post();
                        $stack = get_field("stack");
                        $tags = $stack
                            ? array_map("trim", explode(",", $stack))
                            : [];
                        ?>
                        <div class="project-card" onclick="window.location.href='<?php echo esc_url(
                            get_permalink(),
                        ); ?>'" style="cursor: pointer;">
                            <div class="project-card__image">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail("project-card", [
                                        "class" => "project-card__img",
                                        "loading" =>
                                            $project_index === 0
                                                ? "eager"
                                                : "lazy",
                                        "fetchpriority" =>
                                            $project_index === 0
                                                ? "high"
                                                : false,
                                    ]); ?>
                                <?php else: ?>
                                    <div class="project-card__placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                            <line x1="8" y1="21" x2="16" y2="21"></line>
                                            <line x1="12" y1="17" x2="12" y2="21"></line>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="project-card__content">
                                <h3><?php the_title(); ?></h3>
                                <p><?php echo wp_trim_words(
                                    get_field("project_overview") ?:
                                    get_the_content(),
                                    20,
                                ); ?></p>
                                <?php if ($tags): ?>
                                    <div class="tags">
                                        <?php foreach ($tags as $tag): ?>
                                            <span class="tag"><?php echo esc_html(
                                                $tag,
                                            ); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php $project_index++;
                    endwhile;
                    wp_reset_postdata();
                else:
                     ?>
                    <div class="project-card">
                        <div class="project-card__image">
                            <div class="project-card__placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="project-card__content">
                            <h3>E-Commerce Platform</h3>
                            <p>A full-stack e-commerce solution with real-time inventory management and payment processing.</p>
                            <div class="tags">
                                <span class="tag">SvelteKit</span>
                                <span class="tag">PostgreSQL</span>
                                <span class="tag">Stripe</span>
                            </div>
                        </div>
                    </div>
                    <?php
                endif;
                ?>
            </div>
        </section>

        <section id="skills" class="animate-in">
            <h2>Skills & Technologies</h2>
            <div class="skills-grid">
                <?php
                $skills = new WP_Query([
                    "post_type" => "skill",
                    "posts_per_page" => -1,
                ]);

                $skills_by_category = [
                    "Frontend" => [],
                    "Backend" => [],
                    "Tools" => [],
                ];

                if ($skills->have_posts()):
                    while ($skills->have_posts()):
                        $skills->the_post();
                        $category = get_post_meta(
                            get_the_ID(),
                            "_skill_category",
                            true,
                        );
                        if (isset($skills_by_category[$category])) {
                            $skills_by_category[$category][] = get_the_title();
                        }
                    endwhile;
                    wp_reset_postdata();

                    foreach ($skills_by_category as $category => $skill_list):
                        if (empty($skill_list)) {
                            continue;
                        } ?>
                        <div class="skill-category">
                            <h3><?php echo esc_html($category); ?></h3>
                            <ul class="skill-list">
                                <?php foreach ($skill_list as $skill): ?>
                                    <li><?php echo esc_html($skill); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php
                    endforeach;
                else:
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

        <section id="why-me" class="why-me animate-in">
            <h2>Why Clients Choose Me</h2>
            <ul class="why-me__list">
                <li>I prioritize long-term stability over quick fixes</li>
                <li>I communicate clearly and meet deadlines</li>
                <li>I solve performance issues without unnecessary complexity</li>
                <li>I treat websites like revenue assets</li>
            </ul>
        </section>

<?php get_footer(); ?>
