<?php
// Only run when WordPress is uninstalling this plugin
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete all work posts and their meta
$works = get_posts(array(
    'post_type'      => 'work',
    'post_status'    => 'any',
    'numberposts'    => -1,
    'fields'         => 'ids',
));

foreach ($works as $post_id) {
    wp_delete_post($post_id, true);
}

// Delete all terms and taxonomy data
$taxonomies = array('medium', 'technique', 'collaborator', 'client');

foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'fields'     => 'ids',
    ));

    if (!is_wp_error($terms)) {
        foreach ($terms as $term_id) {
            wp_delete_term($term_id, $taxonomy);
        }
    }
}

// Flush rewrite rules
flush_rewrite_rules();
