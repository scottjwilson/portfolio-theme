<?php
/**
 * Customizer Settings
 * Handles theme customizer options
 */

/**
 * Register customizer settings
 */
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