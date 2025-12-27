<?php
function minimal_portfolio_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    
    // Register navigation menu
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'minimal-portfolio'),
    ));
}
add_action('after_setup_theme', 'minimal_portfolio_setup');

// Enqueue styles
function minimal_portfolio_scripts() {
    wp_enqueue_style('minimal-portfolio-style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'minimal_portfolio_scripts');

// Register Custom Post Types
function register_portfolio_post_types() {
    // Projects Post Type
    register_post_type('project', array(
        'labels' => array(
            'name' => 'Projects',
            'singular_name' => 'Project',
            'add_new' => 'Add New Project',
            'edit_item' => 'Edit Project',
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array('title', 'editor', 'thumbnail'),
        'menu_icon' => 'dashicons-portfolio',
        'show_in_rest' => true,
    ));
    
    // Skills Post Type
    register_post_type('skill', array(
        'labels' => array(
            'name' => 'Skills',
            'singular_name' => 'Skill',
            'add_new' => 'Add New Skill',
            'edit_item' => 'Edit Skill',
        ),
        'public' => true,
        'supports' => array('title'),
        'menu_icon' => 'dashicons-awards',
        'show_in_rest' => true,
    ));
}
add_action('init', 'register_portfolio_post_types');

// Add custom fields support
function add_project_meta_boxes() {
    add_meta_box(
        'project_details',
        'Project Details',
        'render_project_meta_box',
        'project',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_project_meta_boxes');

function render_project_meta_box($post) {
    wp_nonce_field('project_meta_box', 'project_meta_box_nonce');
    
    $tags = get_post_meta($post->ID, '_project_tags', true);
    $url = get_post_meta($post->ID, '_project_url', true);
    
    echo '<p><label>Technologies (comma-separated):</label><br>';
    echo '<input type="text" name="project_tags" value="' . esc_attr($tags) . '" style="width:100%"></p>';
    
    echo '<p><label>Project URL:</label><br>';
    echo '<input type="text" name="project_url" value="' . esc_attr($url) . '" style="width:100%"></p>';
}

function save_project_meta($post_id) {
    if (!isset($_POST['project_meta_box_nonce'])) return;
    if (!wp_verify_nonce($_POST['project_meta_box_nonce'], 'project_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    if (isset($_POST['project_tags'])) {
        update_post_meta($post_id, '_project_tags', sanitize_text_field($_POST['project_tags']));
    }
    
    if (isset($_POST['project_url'])) {
        update_post_meta($post_id, '_project_url', esc_url_raw($_POST['project_url']));
    }
}
add_action('save_post_project', 'save_project_meta');

// Add skill category meta box
function add_skill_meta_boxes() {
    add_meta_box(
        'skill_details',
        'Skill Details',
        'render_skill_meta_box',
        'skill',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_skill_meta_boxes');

function render_skill_meta_box($post) {
    wp_nonce_field('skill_meta_box', 'skill_meta_box_nonce');
    
    $category = get_post_meta($post->ID, '_skill_category', true);
    
    echo '<p><label>Category:</label><br>';
    echo '<select name="skill_category" style="width:100%">';
    echo '<option value="Frontend" ' . selected($category, 'Frontend', false) . '>Frontend</option>';
    echo '<option value="Backend" ' . selected($category, 'Backend', false) . '>Backend</option>';
    echo '<option value="Tools" ' . selected($category, 'Tools', false) . '>Tools & Others</option>';
    echo '</select></p>';
}

function save_skill_meta($post_id) {
    if (!isset($_POST['skill_meta_box_nonce'])) return;
    if (!wp_verify_nonce($_POST['skill_meta_box_nonce'], 'skill_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    
    if (isset($_POST['skill_category'])) {
        update_post_meta($post_id, '_skill_category', sanitize_text_field($_POST['skill_category']));
    }
}
add_action('save_post_skill', 'save_skill_meta');

// Customizer settings
function minimal_portfolio_customize_register($wp_customize) {
    // Hero Section
    $wp_customize->add_section('hero_section', array(
        'title' => 'Hero Section',
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('hero_title', array(
        'default' => 'Building digital experiences that matter',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('hero_title', array(
        'label' => 'Hero Title',
        'section' => 'hero_section',
        'type' => 'text',
    ));
    
    $wp_customize->add_setting('hero_subtitle', array(
        'default' => 'Full-stack web developer specializing in modern frameworks and creating performant, accessible applications.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    
    $wp_customize->add_control('hero_subtitle', array(
        'label' => 'Hero Subtitle',
        'section' => 'hero_section',
        'type' => 'textarea',
    ));
    
    // Contact Section
    $wp_customize->add_section('contact_section', array(
        'title' => 'Contact Section',
        'priority' => 31,
    ));
    
    $wp_customize->add_setting('contact_email', array(
        'default' => 'your.email@example.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('contact_email', array(
        'label' => 'Contact Email',
        'section' => 'contact_section',
        'type' => 'email',
    ));
    
    $wp_customize->add_setting('github_url', array(
        'default' => 'https://github.com/yourusername',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('github_url', array(
        'label' => 'GitHub URL',
        'section' => 'contact_section',
        'type' => 'url',
    ));
}
add_action('customize_register', 'minimal_portfolio_customize_register');

