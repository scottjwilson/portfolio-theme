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
 * Include required files
 */
require_once PORTFOLIO_THEME_PATH . "/inc/vite-assets.php";
require_once PORTFOLIO_THEME_PATH . "/inc/post-types.php";
require_once PORTFOLIO_THEME_PATH . "/inc/meta-boxes.php";
require_once PORTFOLIO_THEME_PATH . "/inc/customizer.php";

// Include template functions (if needed in the future)
// require_once PORTFOLIO_THEME_PATH . '/inc/template-sections.php';
// require_once PORTFOLIO_THEME_PATH . '/inc/template-functions.php';
