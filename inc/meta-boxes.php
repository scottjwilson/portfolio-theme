<?php
/**
 * Meta Boxes
 * Handles custom meta boxes for projects and skills
 */

/**
 * Add project URL meta box
 */
function add_project_meta_boxes()
{
    add_meta_box(
        "project_details",
        "Project Details",
        "render_project_meta_box",
        "project",
        "normal",
        "high",
    );
}
add_action("add_meta_boxes", "add_project_meta_boxes");

/**
 * Render project meta box
 */
function render_project_meta_box($post)
{
    wp_nonce_field("project_meta_box", "project_meta_box_nonce");

    $url = get_post_meta($post->ID, "_project_url", true);

    echo "<p><label>Project URL:</label><br>";
    echo '<input type="text" name="project_url" value="' .
        esc_attr($url) .
        '" style="width:100%"></p>';
}

/**
 * Save project meta data
 */
function save_project_meta($post_id)
{
    if (!isset($_POST["project_meta_box_nonce"])) {
        return;
    }
    if (
        !wp_verify_nonce($_POST["project_meta_box_nonce"], "project_meta_box")
    ) {
        return;
    }
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST["project_url"])) {
        update_post_meta(
            $post_id,
            "_project_url",
            esc_url_raw($_POST["project_url"]),
        );
    }
}
add_action("save_post_project", "save_project_meta");

/**
 * Add skill category meta box
 */
function add_skill_meta_boxes()
{
    add_meta_box(
        "skill_details",
        "Skill Details",
        "render_skill_meta_box",
        "skill",
        "normal",
        "high",
    );
}
add_action("add_meta_boxes", "add_skill_meta_boxes");

/**
 * Render skill meta box
 */
function render_skill_meta_box($post)
{
    wp_nonce_field("skill_meta_box", "skill_meta_box_nonce");

    $category = get_post_meta($post->ID, "_skill_category", true);

    echo "<p><label>Category:</label><br>";
    echo '<select name="skill_category" style="width:100%">';
    echo '<option value="Frontend" ' .
        selected($category, "Frontend", false) .
        ">Frontend</option>";
    echo '<option value="Backend" ' .
        selected($category, "Backend", false) .
        ">Backend</option>";
    echo '<option value="Tools" ' .
        selected($category, "Tools", false) .
        ">Tools & Others</option>";
    echo "</select></p>";
}

/**
 * Save skill meta data
 */
function save_skill_meta($post_id)
{
    if (!isset($_POST["skill_meta_box_nonce"])) {
        return;
    }
    if (!wp_verify_nonce($_POST["skill_meta_box_nonce"], "skill_meta_box")) {
        return;
    }
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE) {
        return;
    }

    if (isset($_POST["skill_category"])) {
        update_post_meta(
            $post_id,
            "_skill_category",
            sanitize_text_field($_POST["skill_category"]),
        );
    }
}
add_action("save_post_skill", "save_skill_meta");
