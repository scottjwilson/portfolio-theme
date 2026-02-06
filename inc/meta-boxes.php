<?php
/**
 * Meta Boxes
 * Handles custom meta boxes for projects and skills
 */

/**
 * Add custom fields support for projects
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

    $tags = get_post_meta($post->ID, "_project_tags", true);
    $url = get_post_meta($post->ID, "_project_url", true);

    echo "<p><label>Technologies (comma-separated):</label><br>";
    echo '<input type="text" name="project_tags" value="' .
        esc_attr($tags) .
        '" style="width:100%"></p>';

    echo "<p><label>Project URL:</label><br>";
    echo '<input type="text" name="project_url" value="' .
        esc_attr($url) .
        '" style="width:100%"></p>';

    $challenges_json = get_post_meta($post->ID, "_project_challenges", true);
    $challenges = $challenges_json ? json_decode($challenges_json, true) : [];
    if (!is_array($challenges)) {
        $challenges = [];
    }

    echo '<hr style="margin: 20px 0;">';
    echo '<h4 style="margin-bottom: 10px;">Challenges &amp; Solutions</h4>';
    echo '<div id="project-challenges-wrap">';

    foreach ($challenges as $i => $challenge) {
        $problem = isset($challenge["problem"]) ? $challenge["problem"] : "";
        $solution = isset($challenge["solution"]) ? $challenge["solution"] : "";
        echo '<div class="challenge-entry" style="background: #f9f9f9; padding: 12px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px;">';
        echo "<p><label>Problem:</label><br>";
        echo '<textarea name="project_challenges[' .
            $i .
            '][problem]" rows="3" style="width:100%">' .
            esc_textarea($problem) .
            "</textarea></p>";
        echo "<p><label>Solution:</label><br>";
        echo '<textarea name="project_challenges[' .
            $i .
            '][solution]" rows="3" style="width:100%">' .
            esc_textarea($solution) .
            "</textarea></p>";
        echo '<button type="button" class="button challenge-remove" onclick="this.parentElement.remove()">Remove</button>';
        echo "</div>";
    }

    echo "</div>";
    echo '<button type="button" class="button" id="add-challenge">Add Challenge</button>';

    echo '<script>
    document.getElementById("add-challenge").addEventListener("click", function() {
        var wrap = document.getElementById("project-challenges-wrap");
        var index = wrap.querySelectorAll(".challenge-entry").length;
        var div = document.createElement("div");
        div.className = "challenge-entry";
        div.style = "background: #f9f9f9; padding: 12px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px;";
        div.innerHTML = \'<p><label>Problem:</label><br><textarea name="project_challenges[\' + index + \'][problem]" rows="3" style="width:100%"></textarea></p>\' +
            \'<p><label>Solution:</label><br><textarea name="project_challenges[\' + index + \'][solution]" rows="3" style="width:100%"></textarea></p>\' +
            \'<button type="button" class="button challenge-remove" onclick="this.parentElement.remove()">Remove</button>\';
        wrap.appendChild(div);
    });
    </script>';
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

    if (isset($_POST["project_tags"])) {
        update_post_meta(
            $post_id,
            "_project_tags",
            sanitize_text_field($_POST["project_tags"]),
        );
    }

    if (isset($_POST["project_url"])) {
        update_post_meta(
            $post_id,
            "_project_url",
            esc_url_raw($_POST["project_url"]),
        );
    }

    if (
        isset($_POST["project_challenges"]) &&
        is_array($_POST["project_challenges"])
    ) {
        $clean = [];
        foreach ($_POST["project_challenges"] as $entry) {
            $problem = isset($entry["problem"])
                ? sanitize_textarea_field($entry["problem"])
                : "";
            $solution = isset($entry["solution"])
                ? sanitize_textarea_field($entry["solution"])
                : "";
            if ($problem || $solution) {
                $clean[] = ["problem" => $problem, "solution" => $solution];
            }
        }
        update_post_meta(
            $post_id,
            "_project_challenges",
            wp_json_encode($clean, JSON_UNESCAPED_UNICODE),
        );
    } else {
        delete_post_meta($post_id, "_project_challenges");
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
