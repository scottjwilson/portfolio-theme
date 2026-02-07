<?php
/**
 * Custom Post Types
 * Registers custom post types for the portfolio theme
 */

/**
 * Register Custom Post Types
 */
function register_portfolio_post_types()
{
    // Projects Post Type
    register_post_type("project", [
        "labels" => [
            "name" => "Projects",
            "singular_name" => "Project",
            "add_new" => "Add New Project",
            "edit_item" => "Edit Project",
        ],
        "public" => true,
        "has_archive" => true,
        "supports" => ["title", "editor", "thumbnail", "page-attributes"],
        "menu_icon" => "dashicons-portfolio",
        "show_in_rest" => true,
    ]);

    // Skills Post Type
    register_post_type("skill", [
        "labels" => [
            "name" => "Skills",
            "singular_name" => "Skill",
            "add_new" => "Add New Skill",
            "edit_item" => "Edit Skill",
        ],
        "public" => true,
        "supports" => ["title"],
        "menu_icon" => "dashicons-awards",
        "show_in_rest" => true,
    ]);
}
add_action("init", "register_portfolio_post_types");
