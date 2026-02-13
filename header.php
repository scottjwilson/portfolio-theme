<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo("charset"); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="container">
        <nav>
            <a href="<?php echo home_url(); ?>" class="logo">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.png" alt="<?php bloginfo(
    "name",
); ?>" class="logo-img">
                <span class="logo-text"><?php bloginfo("name"); ?></span>
            </a>
            <?php wp_nav_menu([
                "theme_location" => "primary",
                "container" => false,
                "menu_class" => "nav-links",
                "fallback_cb" => function () {
                    $base = esc_url(home_url("/"));
                    echo '<ul class="nav-links">';
                    echo '<li><a href="' . $base . '#projects">Work</a></li>';
                    echo '<li><a href="' . $base . '#skills">Skills</a></li>';
                    echo '<li><a href="' .
                        $base .
                        '#services">Services</a></li>';
                    echo '<li><a href="' .
                        $base .
                        '#final-cta">Contact</a></li>';
                    echo "</ul>";
                },
            ]); ?>
        </nav>
        <main id="main-content">
