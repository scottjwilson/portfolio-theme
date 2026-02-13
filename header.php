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
                <?php bloginfo("name"); ?>
            </a>
            <?php wp_nav_menu([
                "theme_location" => "primary",
                "container" => false,
                "menu_class" => "nav-links",
                "fallback_cb" => function () {
                    $base = esc_url(home_url("/"));
                    echo '<ul class="nav-links">';
                    echo '<li><a href="' .
                        $base .
                        '#projects">Projects</a></li>';
                    echo '<li><a href="' . $base . '#skills">Skills</a></li>';
                    echo '<li><a href="' . $base . '#contact">Contact</a></li>';
                    echo "</ul>";
                },
            ]); ?>
        </nav>
        <main id="main-content">
