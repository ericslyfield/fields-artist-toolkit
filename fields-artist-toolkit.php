<?php
/*
Plugin Name: FIELDS* Artist Toolkit
Description: A Portfolio plugin for multi-disciplinary artists.
Version: 0.0.1
Author: Eric Slyfield
*/

namespace Nonarchival\FieldsToolkit;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Register Custom Post Type for Artwork
function register_post_type_work() {
    // Labels and Headings
    $labels = array(
        'name'               => __('Work', 'nonarchival'),
        'singular_name'      => __('Work', 'nonarchival'),
        'menu_name'          => __('Work', 'nonarchival'),
        'add_new'            => __('Add New Work', 'nonarchival'),
        'add_new_item'       => __('Add New Work', 'nonarchival'),
        'edit_item'          => __('Edit Work Entry', 'nonarchival'),
        'new_item'           => __('New Work Entry', 'nonarchival'),
        'view_item'          => __('View Work', 'nonarchival'),
        'search_items'       => __('Search Works', 'nonarchival'),
        'not_found'          => __('No works found', 'nonarchival'),
        'not_found_in_trash' => __('No works found in trash', 'nonarchival'),
    );

    // Admin Settings
    $args = array(
        'menu_position'       => 4,
        'menu_icon'           => 'dashicons-art',
        'description'         => __('A portfolio plugin for multi-disciplinary artists.', 'nonarchival'),
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
        'rewrite'             => array('slug' => 'work'),
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'show_in_rest'        => true,
    );

    // Registers Settings
    register_post_type('work', $args);
}
add_action('init', 'Nonarchival\FieldsToolkit\register_post_type_work');

// Register Medium Taxonomy
function register_taxonomy_medium() {
    $labels = array(
        'name'              => __('Mediums', 'nonarchival'),
        'singular_name'     => __('Medium', 'nonarchival'),
        'search_items'      => __('Search Mediums', 'nonarchival'),
        'all_items'         => __('All Mediums', 'nonarchival'),
        'edit_item'         => __('Edit Medium', 'nonarchival'),
        'update_item'       => __('Update Medium', 'nonarchival'),
        'add_new_item'      => __('Add New Medium', 'nonarchival'),
        'new_item_name'     => __('New Medium Name', 'nonarchival'),
        'menu_name'         => __('Mediums', 'nonarchival'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'work/medium'),
    );

    register_taxonomy('medium', 'work', $args);
}
add_action('init', 'Nonarchival\FieldsToolkit\register_taxonomy_medium');

// Seed default medium terms on plugin activation
function activate() {
    register_post_type_work();
    register_taxonomy_medium();

    $default_mediums = array(
        'Film',
        'Video',
        'Sound',
        'Photography',
        'Drawing',
        'Painting',
        'Installation',
        'Sculpture',
        'Performance',
        'Digital',
        'Design'
    );

    foreach ($default_mediums as $medium) {
        if (!term_exists($medium, 'medium')) {
            wp_insert_term($medium, 'medium');
        }
    }

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'Nonarchival\FieldsToolkit\activate');