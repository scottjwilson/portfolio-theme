<?php
/**
 * Portfolio Theme Functions
 * Main functions file that loads theme functionality
 */

// Define theme constants
if (!defined("PORTFOLIO_THEME_VERSION")) {
    define("PORTFOLIO_THEME_VERSION", "1.0.0");
}

if (!defined("PORTFOLIO_THEME_PATH")) {
    define("PORTFOLIO_THEME_PATH", get_template_directory());
}

if (!defined("PORTFOLIO_THEME_URI")) {
    define("PORTFOLIO_THEME_URI", get_template_directory_uri());
}

/**
 * Theme Setup
 */
function portfolio_theme_setup()
{
    // Add theme support for title tag (WordPress 4.1+)
    add_theme_support("title-tag");

    // Add other theme supports as needed
    add_theme_support("post-thumbnails");
    add_image_size("project-card", 800, 450, true);
    add_theme_support("html5", [
        "search-form",
        "comment-form",
        "comment-list",
        "gallery",
        "caption",
    ]);
}
add_action("after_setup_theme", "portfolio_theme_setup");

/**
 * Add preconnect for Google Fonts and make font CSS non-render-blocking
 */
function portfolio_resource_hints()
{
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' .
        "\n";
}
add_action("wp_head", "portfolio_resource_hints", 0);

function portfolio_font_loader_tag($html, $handle)
{
    if ($handle === "google-fonts") {
        $noscript = str_replace("media='print'", "media='all'", $html);
        $html = str_replace(
            "media='print'",
            "media='print' onload=\"this.media='all'\"",
            $html,
        );
        $html .= "<noscript>" . $noscript . "</noscript>";
    }
    return $html;
}
add_filter("style_loader_tag", "portfolio_font_loader_tag", 10, 2);

/**
 * Output meta description tag
 */
function portfolio_meta_description()
{
    if (is_front_page()) {
        $description = get_theme_mod("meta_description", "");
        if (empty($description)) {
            $description = get_bloginfo("description");
        }
    } elseif (is_singular("project")) {
        $overview = get_field("project_overview");
        $description = $overview
            ? wp_strip_all_tags($overview)
            : get_the_excerpt();
        $description = wp_trim_words($description, 25, "");
    } else {
        $description = get_bloginfo("description");
    }

    if ($description) {
        echo '<meta name="description" content="' .
            esc_attr($description) .
            '">' .
            "\n";
    }
}
add_action("wp_head", "portfolio_meta_description", 1);

/**
 * Include required files
 */
require_once PORTFOLIO_THEME_PATH . "/inc/vite-assets.php";
require_once PORTFOLIO_THEME_PATH . "/inc/post-types.php";
require_once PORTFOLIO_THEME_PATH . "/inc/meta-boxes.php";
require_once PORTFOLIO_THEME_PATH . "/inc/customizer.php";
