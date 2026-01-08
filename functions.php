<?php





/**
 * Detect if Vite dev server is running and get the base path
 */
function portfolio_detect_vite_server() {
    $vite_server = 'http://localhost:3000';
    
    // Always check if Vite is running (don't require WP_DEBUG)
    // This allows Vite HMR to work in development even if WP_DEBUG is off
    
    // Try checking if main.js is accessible (more reliable than @vite/client)
    $response = @wp_remote_get($vite_server . '/js/main.js', array(
        'timeout' => 1,
        'sslverify' => false,
        'redirection' => 0
    ));
    
    // If main.js is accessible, Vite is running
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        // Try @vite/client at root to determine base path
        $client_response = @wp_remote_get($vite_server . '/@vite/client', array(
            'timeout' => 1,
            'sslverify' => false,
            'redirection' => 0
        ));
        
        if (!is_wp_error($client_response) && wp_remote_retrieve_response_code($client_response) === 200) {
            return array('running' => true, 'base' => '/', 'server' => $vite_server);
        }
        
        // Try with base path
        $client_response2 = @wp_remote_get($vite_server . '/wp-content/themes/Portfolio-Theme/@vite/client', array(
            'timeout' => 1,
            'sslverify' => false,
            'redirection' => 0
        ));
        
        if (!is_wp_error($client_response2) && wp_remote_retrieve_response_code($client_response2) === 200) {
            return array('running' => true, 'base' => '/wp-content/themes/Portfolio-Theme/', 'server' => $vite_server);
        }
        
        // If main.js works but @vite/client doesn't, assume root path (Vite might be starting up)
        return array('running' => true, 'base' => '/', 'server' => $vite_server);
    }
    
    return array('running' => false, 'base' => '/', 'server' => $vite_server);
}

/**
 * Enqueue styles and scripts
 * Falls back to direct enqueues if Vite is not available
 */
function portfolio_enqueue_assets() {
    // Always enqueue Google Fonts
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap', array(), null);
    
    // Main stylesheet (required by WordPress)
    wp_enqueue_style('portfolio-style', get_stylesheet_uri());
    
    // Check if Vite is being used
    $vite = portfolio_detect_vite_server();
    
    // If Vite is running in dev or manifest exists in production, skip direct enqueues
    if ($vite['running'] || (!defined('WP_DEBUG') || !WP_DEBUG) && file_exists(get_theme_file_path('dist/.vite/manifest.json'))) {
        return; // Vite will handle assets via load_vite_assets()
    }
    
    // Fallback: enqueue directly if Vite is not available
    // CSS Variables (depends on Google Fonts to ensure font loads first)
    wp_enqueue_style('variables', get_template_directory_uri() . '/css/variables.css', array('google-fonts'), '1.0.0');
    
    // Base styles
    wp_enqueue_style('base', get_template_directory_uri() . '/css/base.css', array('variables'), '1.0.0');
    
    
}
add_action('wp_enqueue_scripts', 'portfolio_enqueue_assets');

// // Include template functions
// require_once get_template_directory() . '/inc/template-sections.php';
// require_once get_template_directory() . '/inc/template-functions.php';

/**
 * Output Vite scripts in head
 */
function portfolio_output_vite_scripts() {
    $vite = portfolio_detect_vite_server();
    
    // Debug: Always output scripts if we're in a development environment
    // (Check for localhost or common dev domains)
    $is_local = strpos(home_url(), 'localhost') !== false || 
                strpos(home_url(), '127.0.0.1') !== false ||
                strpos(home_url(), '.local') !== false ||
                strpos(home_url(), '.dev') !== false;
    
    if ($vite['running'] || $is_local) {
        $vite_server = $vite['server'];
        $vite_base = $vite['running'] ? $vite['base'] : '/';
        $vite_client_url = $vite_server . $vite_base . '@vite/client';
        $vite_main_url = $vite_server . $vite_base . 'js/main.js';
        
        echo '<script type="module" src="' . esc_url($vite_client_url) . '"></script>' . "\n";
        echo '<script type="module" src="' . esc_url($vite_main_url) . '"></script>' . "\n";
    }
}
add_action('wp_head', 'portfolio_output_vite_scripts', 1);

/**
 * Load Vite assets (development and production)
 */
function load_vite_assets(): void {
    $vite = portfolio_detect_vite_server();
    
    if ($vite['running']) {
        // Scripts are output in wp_head hook above
        return;
    }
    
    // Production mode - use manifest.json
    $manifest_path = get_theme_file_path('dist/.vite/manifest.json');
    
    if (!file_exists($manifest_path)) {
        return;
    }
    
    $manifest = json_decode(file_get_contents($manifest_path), true);
    
    if (!$manifest) {
        return;
    }
    
    // Load JS from manifest (CSS is bundled with it)
    if (isset($manifest['js/main.js'])) {
        $entry = $manifest['js/main.js'];
        
        // Enqueue CSS files if they exist in the manifest
        if (isset($entry['css']) && is_array($entry['css'])) {
            foreach ($entry['css'] as $index => $css_file) {
                $css_path = get_theme_file_path('dist/' . $css_file);
                wp_enqueue_style(
                    'vite-style-' . $index,
                    get_theme_file_uri('dist/' . $css_file),
                    array(),
                    file_exists($css_path) ? filemtime($css_path) : null
                );
            }
        }
        
        // Enqueue JS
        $js_path = get_theme_file_path('dist/' . $entry['file']);
        wp_enqueue_script(
            'vite-main',
            get_theme_file_uri('dist/' . $entry['file']),
            array(),
            file_exists($js_path) ? filemtime($js_path) : null,
            true
        );
        wp_script_add_data('vite-main', 'type', 'module');
    }
}
add_action('wp_enqueue_scripts', 'load_vite_assets', 100);

/**
 * Ensure Vite scripts have type="module" attribute
 */
function portfolio_script_loader_tag($tag, $handle, $src) {
    // Add type="module" to Vite scripts
    if (strpos($handle, 'vite-') === 0) {
        // Check if type="module" is already present
        if (strpos($tag, 'type="module"') === false && strpos($tag, "type='module'") === false) {
            $tag = str_replace('<script ', '<script type="module" ', $tag);
        }
    }
    return $tag;
}
add_filter('script_loader_tag', 'portfolio_script_loader_tag', 10, 3);


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
