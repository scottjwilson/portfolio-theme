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

/**
 * Format challenge text: converts bullet-point lines into a <ul> list,
 * otherwise returns paragraphs with line breaks.
 */
function portfolio_format_challenge_text($text)
{
    $lines = preg_split('/\r\n|\r|\n/', $text);
    $bullet_pattern =
        '/^[\x{2022}\x{2023}\x{25E6}\x{2043}\x{2219}•\-\*]\s*\t?/u';
    $has_bullets = false;

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === "") {
            continue;
        }
        if (preg_match($bullet_pattern, $line)) {
            $has_bullets = true;
            break;
        }
    }

    if ($has_bullets) {
        $output = "<ul>";
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === "") {
                continue;
            }
            $clean = preg_replace($bullet_pattern, "", $line);
            $clean = trim($clean);
            if ($clean === "") {
                continue;
            }
            $output .= "<li>" . esc_html($clean) . "</li>";
        }
        $output .= "</ul>";
        return $output;
    }

    return nl2br(esc_html($text));
}
