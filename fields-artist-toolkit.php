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