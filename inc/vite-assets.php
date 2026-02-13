<?php
/**
 * Vite Asset Management
 * Handles Vite dev server detection and asset enqueuing
 */

/**
 * Detect if Vite dev server is running and get the base path
 */
function portfolio_detect_vite_server()
{
    $vite_server = "http://localhost:3000";

    // Always check if Vite is running (don't require WP_DEBUG)
    // This allows Vite HMR to work in development even if WP_DEBUG is off

    // Try checking if main.js is accessible (more reliable than @vite/client)
    $response = @wp_remote_get($vite_server . "/js/main.js", [
        "timeout" => 1,
        "sslverify" => false,
        "redirection" => 0,
    ]);

    // If main.js is accessible, Vite is running
    if (
        !is_wp_error($response) &&
        wp_remote_retrieve_response_code($response) === 200
    ) {
        // Try @vite/client at root to determine base path
        $client_response = @wp_remote_get($vite_server . "/@vite/client", [
            "timeout" => 1,
            "sslverify" => false,
            "redirection" => 0,
        ]);

        if (
            !is_wp_error($client_response) &&
            wp_remote_retrieve_response_code($client_response) === 200
        ) {
            return ["running" => true, "base" => "/", "server" => $vite_server];
        }

        // Try with base path
        $client_response2 = @wp_remote_get(
            $vite_server . "/wp-content/themes/Portfolio-Theme/@vite/client",
            [
                "timeout" => 1,
                "sslverify" => false,
                "redirection" => 0,
            ],
        );

        if (
            !is_wp_error($client_response2) &&
            wp_remote_retrieve_response_code($client_response2) === 200
        ) {
            return [
                "running" => true,
                "base" => "/wp-content/themes/Portfolio-Theme/",
                "server" => $vite_server,
            ];
        }

        // If main.js works but @vite/client doesn't, assume root path (Vite might be starting up)
        return ["running" => true, "base" => "/", "server" => $vite_server];
    }

    return ["running" => false, "base" => "/", "server" => $vite_server];
}

/**
 * Enqueue styles and scripts
 * Falls back to direct enqueues if Vite is not available
 */
function portfolio_enqueue_assets()
{
    // Google Fonts with preconnect for faster loading
    wp_enqueue_style(
        "google-fonts",
        "https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap",
        [],
        null,
        "print",
    );

    // Deregister block library CSS (not used in this theme)
    wp_dequeue_style("wp-block-library");
    wp_dequeue_style("wp-block-library-theme");
    wp_dequeue_style("global-styles");

    // Main stylesheet (required by WordPress)
    wp_enqueue_style("portfolio-style", get_stylesheet_uri());

    // Check if Vite is being used
    $vite = portfolio_detect_vite_server();

    // Check if manifest exists (production build)
    $manifest_path = get_theme_file_path("dist/.vite/manifest.json");
    $manifest_exists = file_exists($manifest_path);

    // If Vite is running in dev OR manifest exists in production, skip direct enqueues
    // (load_vite_assets will handle production assets)
    if ($vite["running"] || $manifest_exists) {
        return; // Vite will handle assets via load_vite_assets()
    }

    // Fallback: enqueue directly if Vite is not available and no manifest exists
    // CSS Variables (depends on Google Fonts to ensure font loads first)
    wp_enqueue_style(
        "variables",
        get_template_directory_uri() . "/css/variables.css",
        ["google-fonts"],
        "1.0.0",
    );

    // Base styles
    wp_enqueue_style(
        "base",
        get_template_directory_uri() . "/css/base.css",
        ["variables"],
        "1.0.0",
    );
}
add_action("wp_enqueue_scripts", "portfolio_enqueue_assets");

/**
 * Output Vite scripts in head
 */
function portfolio_output_vite_scripts()
{
    $vite = portfolio_detect_vite_server();

    // Debug: Always output scripts if we're in a development environment
    // (Check for localhost or common dev domains)
    $is_local =
        strpos(home_url(), "localhost") !== false ||
        strpos(home_url(), "127.0.0.1") !== false ||
        strpos(home_url(), ".local") !== false ||
        strpos(home_url(), ".dev") !== false;

    if ($vite["running"] || $is_local) {
        $vite_server = $vite["server"];
        $vite_base = $vite["running"] ? $vite["base"] : "/";
        $vite_client_url = $vite_server . $vite_base . "@vite/client";
        $vite_main_url = $vite_server . $vite_base . "js/main.js";

        echo '<script type="module" src="' .
            esc_url($vite_client_url) .
            '"></script>' .
            "\n";
        echo '<script type="module" src="' .
            esc_url($vite_main_url) .
            '"></script>' .
            "\n";
    }
}
add_action("wp_head", "portfolio_output_vite_scripts", 1);

/**
 * Load Vite assets (development and production)
 */
function load_vite_assets(): void
{
    $vite = portfolio_detect_vite_server();

    if ($vite["running"]) {
        // Scripts are output in wp_head hook above
        return;
    }

    // Production mode - use manifest.json
    $manifest_path = get_theme_file_path("dist/.vite/manifest.json");

    if (!file_exists($manifest_path)) {
        return;
    }

    $manifest = json_decode(file_get_contents($manifest_path), true);

    if (!$manifest) {
        return;
    }

    // Load JS from manifest (CSS is bundled with it)
    if (isset($manifest["js/main.js"])) {
        $entry = $manifest["js/main.js"];

        // Enqueue CSS files if they exist in the manifest
        if (isset($entry["css"]) && is_array($entry["css"])) {
            foreach ($entry["css"] as $index => $css_file) {
                $css_path = get_theme_file_path("dist/" . $css_file);
                wp_enqueue_style(
                    "vite-style-" . $index,
                    get_theme_file_uri("dist/" . $css_file),
                    [],
                    file_exists($css_path) ? filemtime($css_path) : null,
                );
            }
        }

        // Enqueue JS
        $js_path = get_theme_file_path("dist/" . $entry["file"]);
        wp_enqueue_script(
            "vite-main",
            get_theme_file_uri("dist/" . $entry["file"]),
            [],
            file_exists($js_path) ? filemtime($js_path) : null,
            true,
        );
        wp_script_add_data("vite-main", "type", "module");
    }
}
add_action("wp_enqueue_scripts", "load_vite_assets", 100);

/**
 * Ensure Vite scripts have type="module" attribute
 */
function portfolio_script_loader_tag($tag, $handle, $src)
{
    // Add type="module" to Vite scripts
    if (strpos($handle, "vite-") === 0) {
        // Check if type="module" is already present
        if (
            strpos($tag, 'type="module"') === false &&
            strpos($tag, "type='module'") === false
        ) {
            $tag = str_replace("<script ", '<script type="module" ', $tag);
        }
    }
    return $tag;
}
add_filter("script_loader_tag", "portfolio_script_loader_tag", 10, 3);
