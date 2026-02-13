<?php
/**
 * Customizer Settings
 * Handles theme customizer options
 */

/**
 * Register customizer settings
 */
function minimal_portfolio_customize_register($wp_customize)
{
    // Hero Section
    $wp_customize->add_section("hero_section", [
        "title" => "Hero Section",
        "priority" => 30,
    ]);

    $wp_customize->add_setting("hero_title", [
        "default" => "Building digital experiences that matter",
        "sanitize_callback" => "sanitize_text_field",
    ]);

    $wp_customize->add_control("hero_title", [
        "label" => "Hero Title",
        "section" => "hero_section",
        "type" => "text",
    ]);

    $wp_customize->add_setting("hero_subtitle", [
        "default" =>
            "Full-stack web developer specializing in modern frameworks and creating performant, accessible applications.",
        "sanitize_callback" => "sanitize_textarea_field",
    ]);

    $wp_customize->add_control("hero_subtitle", [
        "label" => "Hero Subtitle",
        "section" => "hero_section",
        "type" => "textarea",
    ]);

    // SEO
    $wp_customize->add_section("seo_section", [
        "title" => "SEO",
        "priority" => 29,
    ]);

    $wp_customize->add_setting("meta_description", [
        "default" => "",
        "sanitize_callback" => "sanitize_text_field",
    ]);

    $wp_customize->add_control("meta_description", [
        "label" => "Meta Description",
        "section" => "seo_section",
        "type" => "textarea",
        "description" => "Recommended 150-160 characters for search results.",
    ]);

    // Contact Section
    $wp_customize->add_section("contact_section", [
        "title" => "Contact Section",
        "priority" => 31,
    ]);

    $wp_customize->add_setting("contact_email", [
        "default" => "your.email@example.com",
        "sanitize_callback" => "sanitize_email",
    ]);

    $wp_customize->add_control("contact_email", [
        "label" => "Contact Email",
        "section" => "contact_section",
        "type" => "email",
    ]);

    $wp_customize->add_setting("github_url", [
        "default" => "https://github.com/yourusername",
        "sanitize_callback" => "esc_url_raw",
    ]);

    $wp_customize->add_control("github_url", [
        "label" => "GitHub URL",
        "section" => "contact_section",
        "type" => "url",
    ]);
}
add_action("customize_register", "minimal_portfolio_customize_register");
