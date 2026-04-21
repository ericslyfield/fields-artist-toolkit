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

// Register meta fields for work
function register_work_meta() {
    register_post_meta('work', 'work_year', array(
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('work', 'work_duration', array(
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'description'       => 'Duration for time-based work (e.g. "12:30", "45 minutes")',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('work', 'work_dimensions', array(
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'description'       => 'Physical dimensions (e.g. "24 x 36 in", "variable")',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('work', 'work_materials', array(
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'description'       => 'Materials or processes used',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('work', 'work_edition', array(
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'description'       => 'Edition (e.g. "1/10", "AP 2/3", "Unique")',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback'     => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('work', 'work_embed_url', array(
        'show_in_rest'      => true,
        'single'            => true,
        'type'              => 'string',
        'description'       => 'Embed URL for time-based work (Vimeo, SoundCloud, etc.)',
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback'     => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('work', 'work_credits', array(
        'show_in_rest' => array(
            'schema' => array(
                'type'  => 'array',
                'items' => array(
                    'type'       => 'object',
                    'properties' => array(
                        'name' => array('type' => 'string'),
                        'role' => array('type' => 'string'),
                    ),
                ),
            ),
        ),
        'single'        => true,
        'type'          => 'array',
        'description'   => 'Credited contributors and their roles on this work',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
    ));
}
add_action('init', 'Nonarchival\FieldsToolkit\register_work_meta');

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
        'show_admin_column' => false,
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
        'Design',
        'Drawing',
        'Digital',
        'Painting',
        'Sculpture',
        'Installation',
        'Photography',
        'Performance',
        'Concept',
        'Collage'
    );

    foreach ($default_mediums as $medium) {
        if (!term_exists($medium, 'medium')) {
            wp_insert_term($medium, 'medium');
        }
    }

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'Nonarchival\FieldsToolkit\activate');

// Register Technique Taxonomy
function register_taxonomy_technique() {
    $labels = array(
        'name'              => __('Techniques', 'nonarchival'),
        'singular_name'     => __('Technique', 'nonarchival'),
        'search_items'      => __('Search Techniques', 'nonarchival'),
        'all_items'         => __('All Techniques', 'nonarchival'),
        'edit_item'         => __('Edit Technique', 'nonarchival'),
        'update_item'       => __('Update Technique', 'nonarchival'),
        'add_new_item'      => __('Add New Technique', 'nonarchival'),
        'new_item_name'     => __('New Technique Name', 'nonarchival'),
        'menu_name'         => __('Techniques', 'nonarchival'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'work/technique'),
    );

    register_taxonomy('technique', 'work', $args);
}
add_action('init', 'Nonarchival\FieldsToolkit\register_taxonomy_technique');

// Register Collaborator Taxonomy
function register_taxonomy_collaborator() {
    $labels = array(
        'name'              => __('Collaborators', 'nonarchival'),
        'singular_name'     => __('Collaborator', 'nonarchival'),
        'search_items'      => __('Search Collaborators', 'nonarchival'),
        'all_items'         => __('All Collaborators', 'nonarchival'),
        'edit_item'         => __('Edit Collaborator', 'nonarchival'),
        'update_item'       => __('Update Collaborator', 'nonarchival'),
        'add_new_item'      => __('Add New Collaborator', 'nonarchival'),
        'new_item_name'     => __('New Collaborator Name', 'nonarchival'),
        'menu_name'         => __('Collaborators', 'nonarchival'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'work/collaborator'),
    );

    register_taxonomy('collaborator', 'work', $args);
}
add_action('init', 'Nonarchival\FieldsToolkit\register_taxonomy_collaborator');

// Medium to fields mapping
function get_fields_for_medium($medium_slug) {
    $field_map = array(
        'film'         => array('year', 'duration', 'embed_url', 'credits'),
        'video'        => array('year', 'duration', 'embed_url', 'credits'),
        'sound'        => array('year', 'duration', 'embed_url', 'credits'),
        'photography'  => array('year', 'dimensions', 'materials', 'edition'),
        'drawing'      => array('year', 'dimensions', 'materials'),
        'painting'     => array('year', 'dimensions', 'materials'),
        'collage'      => array('year', 'dimensions', 'materials'),
        'installation' => array('year', 'dimensions', 'materials', 'credits'),
        'sculpture'    => array('year', 'dimensions', 'materials', 'credits'),
        'performance'  => array('year', 'duration', 'credits'),
        'design'       => array('year', 'materials', 'edition'),
        'digital'      => array('year', 'materials', 'credits'),
        'concept'      => array('year'),
    );

    return $field_map[strtolower($medium_slug)] ?? array('year');
}

// Register meta box
function add_work_meta_box() {
    add_meta_box(
        'work_details',
        __('Work Details', 'nonarchival'),
        'Nonarchival\FieldsToolkit\render_work_meta_box',
        'work',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'Nonarchival\FieldsToolkit\add_work_meta_box');

// Render meta box
function render_work_meta_box($post) {
    wp_nonce_field('work_meta_box', 'work_meta_box_nonce');

    $medium_terms  = wp_get_post_terms($post->ID, 'medium', array('fields' => 'slugs'));
    $medium_slug   = !empty($medium_terms) ? $medium_terms[0] : '';

    $year      = get_post_meta($post->ID, 'work_year', true);
    $duration  = get_post_meta($post->ID, 'work_duration', true);
    $dims      = get_post_meta($post->ID, 'work_dimensions', true);
    $edition   = get_post_meta($post->ID, 'work_edition', true);
    $materials = get_post_meta($post->ID, 'work_materials', true);
    $embed_url = get_post_meta($post->ID, 'work_embed_url', true);
    $credits   = get_post_meta($post->ID, 'work_credits', true);

    if (!is_array($credits)) {
        $credits = array();
    }

    $field_map = array(
        'film'         => array('year', 'duration', 'embed_url', 'credits'),
        'video'        => array('year', 'duration', 'embed_url', 'credits'),
        'sound'        => array('year', 'duration', 'embed_url', 'credits'),
        'photography'  => array('year', 'dimensions', 'materials', 'edition'),
        'drawing'      => array('year', 'dimensions', 'materials'),
        'painting'     => array('year', 'dimensions', 'materials'),
        'collage'      => array('year', 'dimensions', 'materials'),
        'installation' => array('year', 'dimensions', 'materials', 'credits'),
        'sculpture'    => array('year', 'dimensions', 'materials', 'credits'),
        'performance'  => array('year', 'duration', 'credits'),
        'design'       => array('year', 'materials', 'edition'),
        'digital'      => array('year', 'materials', 'credits'),
        'concept'      => array('year'),
    );
    ?>
    <div class="work-meta-box">

        <p class="no-medium-notice" style="background:#fff3cd;padding:10px;border-left:4px solid #ffc107;margin-bottom:12px;<?php echo !empty($medium_slug) ? 'display:none;' : ''; ?>">
            <?php _e('Assign a medium in the sidebar to see relevant fields.', 'nonarchival'); ?>
        </p>

        <div class="meta-field" data-field="year">
            <p>
                <label for="work_year"><strong><?php _e('Year', 'nonarchival'); ?></strong></label><br>
                <input type="text" id="work_year" name="work_year" value="<?php echo esc_attr($year); ?>" pattern="\d{4}" placeholder="2024" style="width:120px;">
            </p>
        </div>

        <div class="meta-field" data-field="duration">
            <p>
                <label for="work_duration"><strong><?php _e('Duration', 'nonarchival'); ?></strong></label><br>
                <input type="text" id="work_duration" name="work_duration" value="<?php echo esc_attr($duration); ?>" placeholder="e.g. 12:30, 45 minutes" style="width:200px;">
            </p>
        </div>

        <div class="meta-field" data-field="dimensions">
            <p>
                <label for="work_dimensions"><strong><?php _e('Dimensions', 'nonarchival'); ?></strong></label><br>
                <input type="text" id="work_dimensions" name="work_dimensions" value="<?php echo esc_attr($dims); ?>" placeholder="e.g. 24 x 36 in, variable" style="width:200px;">
            </p>
        </div>

        <div class="meta-field" data-field="materials">
            <p>
                <label for="work_materials"><strong><?php _e('Materials', 'nonarchival'); ?></strong></label><br>
                <input type="text" id="work_materials" name="work_materials" value="<?php echo esc_attr($materials); ?>" placeholder="e.g. silver gelatin print, oil on linen" style="width:100%;">
            </p>
        </div>

        <div class="meta-field" data-field="edition">
            <p>
                <label for="work_edition"><strong><?php _e('Edition', 'nonarchival'); ?></strong></label><br>
                <input type="text" id="work_edition" name="work_edition" value="<?php echo esc_attr($edition); ?>" placeholder="e.g. 1/10, AP 2/3, Unique" style="width:200px;">
            </p>
        </div>

        <div class="meta-field" data-field="embed_url">
            <p>
                <label for="work_embed_url"><strong><?php _e('Embed URL', 'nonarchival'); ?></strong></label><br>
                <input type="url" id="work_embed_url" name="work_embed_url" value="<?php echo esc_attr($embed_url); ?>" placeholder="https://vimeo.com/..." style="width:100%;">
                <span class="description"><?php _e('Vimeo, SoundCloud, YouTube, etc.', 'nonarchival'); ?></span>
            </p>
        </div>

        <div class="meta-field" data-field="credits">
            <h4 style="margin-bottom:8px;"><?php _e('Credits', 'nonarchival'); ?></h4>
            <div id="work-credits-container">
                <?php foreach ($credits as $index => $credit) : ?>
                    <div class="credit-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                        <input type="text" name="work_credits[<?php echo $index; ?>][name]" value="<?php echo esc_attr($credit['name'] ?? ''); ?>" placeholder="<?php _e('Name', 'nonarchival'); ?>" style="flex:1;">
                        <input type="text" name="work_credits[<?php echo $index; ?>][role]" value="<?php echo esc_attr($credit['role'] ?? ''); ?>" placeholder="<?php _e('Role', 'nonarchival'); ?>" style="flex:1;">
                        <button type="button" class="button remove-credit">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="add-credit"><?php _e('+ Add Credit', 'nonarchival'); ?></button>
        </div>

    </div>

    <script>
    jQuery(document).ready(function($) {
        const fieldMap  = <?php echo json_encode($field_map); ?>;
        <?php
        $all_medium_terms = get_terms(array('taxonomy' => 'medium', 'hide_empty' => false));
        $term_slug_map    = array();
        if (!is_wp_error($all_medium_terms)) {
            foreach ($all_medium_terms as $t) {
                $term_slug_map[$t->term_id] = $t->slug;
            }
        }
        ?>
        const termSlugs = <?php echo json_encode($term_slug_map); ?>;
        let creditIndex = <?php echo count($credits); ?>;

        function applyFields(slugs) {
            if (!slugs || slugs.length === 0) {
                $('.meta-field').hide();
                $('.no-medium-notice').show();
                return;
            }
            $('.no-medium-notice').hide();
            const active = new Set();
            slugs.forEach(function(slug) {
                (fieldMap[slug] || ['year']).forEach(function(f) { active.add(f); });
            });
            $('.meta-field').each(function() {
                active.has($(this).data('field')) ? $(this).show() : $(this).hide();
            });
        }

        // Initial state from PHP
        applyFields(<?php echo json_encode($medium_terms); ?>);

        // Classic editor
        $(document).on('change', '[name="tax_input[medium][]"]', function() {
            const slugs = $('[name="tax_input[medium][]"]:checked').map(function() {
                return $(this).val();
            }).get();
            applyFields(slugs);
        });

        // Block editor (Gutenberg)
        if (typeof wp !== 'undefined' && wp.data) {
            let prevKey = null;
            wp.data.subscribe(function() {
                const editor = wp.data.select('core/editor');
                if (!editor) return;
                const ids  = editor.getEditedPostAttribute('medium') || [];
                const key  = ids.join(',');
                if (key === prevKey) return;
                prevKey = key;
                applyFields(ids.map(function(id) { return termSlugs[id] || ''; }).filter(Boolean));
            });
        }

        $('#add-credit').on('click', function() {
            const row = `<div class="credit-row" style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                <input type="text" name="work_credits[${creditIndex}][name]" placeholder="<?php _e('Name', 'nonarchival'); ?>" style="flex:1;">
                <input type="text" name="work_credits[${creditIndex}][role]" placeholder="<?php _e('Role', 'nonarchival'); ?>" style="flex:1;">
                <button type="button" class="button remove-credit">&times;</button>
            </div>`;
            $('#work-credits-container').append(row);
            creditIndex++;
        });

        $(document).on('click', '.remove-credit', function() {
            $(this).closest('.credit-row').remove();
        });
    });
    </script>
    <?php
}

// Save meta box data
function save_work_meta($post_id) {
    if (!isset($_POST['work_meta_box_nonce'])) return;
    if (!wp_verify_nonce($_POST['work_meta_box_nonce'], 'work_meta_box')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // Year — validated to four digits
    if (isset($_POST['work_year'])) {
        $year = sanitize_text_field($_POST['work_year']);
        $year = preg_match('/^\d{4}$/', $year) ? $year : '';
        $year ? update_post_meta($post_id, 'work_year', $year) : delete_post_meta($post_id, 'work_year');
    }

    // Simple text fields
    foreach (array('work_duration', 'work_dimensions', 'work_edition', 'work_materials') as $field) {
        if (isset($_POST[$field])) {
            $value = sanitize_text_field($_POST[$field]);
            $value ? update_post_meta($post_id, $field, $value) : delete_post_meta($post_id, $field);
        }
    }

    // Embed URL
    if (isset($_POST['work_embed_url'])) {
        $url = esc_url_raw($_POST['work_embed_url']);
        ($url && filter_var($url, FILTER_VALIDATE_URL))
            ? update_post_meta($post_id, 'work_embed_url', $url)
            : delete_post_meta($post_id, 'work_embed_url');
    }

    // Credits — save meta and sync collaborator taxonomy
    if (isset($_POST['work_credits']) && is_array($_POST['work_credits'])) {
        $credits            = array();
        $collaborator_names = array();

        foreach ($_POST['work_credits'] as $credit) {
            $name = sanitize_text_field($credit['name'] ?? '');
            $role = sanitize_text_field($credit['role'] ?? '');
            if ($name || $role) {
                $credits[] = array('name' => $name, 'role' => $role);
                if ($name) $collaborator_names[] = $name;
            }
        }

        if (!empty($credits)) {
            update_post_meta($post_id, 'work_credits', $credits);
            wp_set_object_terms($post_id, $collaborator_names, 'collaborator', false);
        } else {
            delete_post_meta($post_id, 'work_credits');
            wp_set_object_terms($post_id, array(), 'collaborator', false);
        }
    } else {
        delete_post_meta($post_id, 'work_credits');
        wp_set_object_terms($post_id, array(), 'collaborator', false);
    }
}
add_action('save_post_work', 'Nonarchival\FieldsToolkit\save_work_meta');

// Year and Medium columns for Work list
function add_work_columns($columns) {
    $new = array();
    foreach ($columns as $key => $value) {
        $new[$key] = $value;
        if ($key === 'title') {
            $new['work_year']   = __('Year', 'nonarchival');
            $new['work_medium'] = __('Medium', 'nonarchival');
        }
    }
    return $new;
}
add_filter('manage_work_posts_columns', 'Nonarchival\FieldsToolkit\add_work_columns');

// Populate Year and Medium columns
function populate_work_columns($column, $post_id) {
    switch ($column) {
        case 'work_year':
            $year = get_post_meta($post_id, 'work_year', true);
            echo $year ? esc_html($year) : '—';
            break;
        case 'work_medium':
            $terms = get_the_terms($post_id, 'medium');
            if ($terms && !is_wp_error($terms)) {
                echo esc_html(implode(', ', wp_list_pluck($terms, 'name')));
            } else {
                echo '—';
            }
            break;
    }
}
add_action('manage_work_posts_custom_column', 'Nonarchival\FieldsToolkit\populate_work_columns', 10, 2);

// Make Year column sortable
function make_work_columns_sortable($columns) {
    $columns['work_year'] = 'work_year';
    return $columns;
}
add_filter('manage_edit-work_sortable_columns', 'Nonarchival\FieldsToolkit\make_work_columns_sortable');

// Sort by work_year meta when orderby is set
function sort_work_by_year($query) {
    if (!is_admin() || !$query->is_main_query()) return;
    if ($query->get('orderby') === 'work_year') {
        $query->set('meta_key', 'work_year');
        $query->set('orderby', 'meta_value');
    }
}
add_action('pre_get_posts', 'Nonarchival\FieldsToolkit\sort_work_by_year');

// Register Client Taxonomy
function register_taxonomy_client() {
    $labels = array(
        'name'              => __('Clients', 'nonarchival'),
        'singular_name'     => __('Client', 'nonarchival'),
        'search_items'      => __('Search Clients', 'nonarchival'),
        'all_items'         => __('All Clients', 'nonarchival'),
        'edit_item'         => __('Edit Client', 'nonarchival'),
        'update_item'       => __('Update Client', 'nonarchival'),
        'add_new_item'      => __('Add New Client', 'nonarchival'),
        'new_item_name'     => __('New Client Name', 'nonarchival'),
        'menu_name'         => __('Clients', 'nonarchival'),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_rest'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'work/client'),
    );

    register_taxonomy('client', 'work', $args);
}
add_action('init', 'Nonarchival\FieldsToolkit\register_taxonomy_client');

// Relabel 'Excerpt' to 'Project Description' for the work post type
function relabel_excerpt($translated, $original, $context, $domain) {
    if (get_post_type() !== 'work') return $translated;
    $map = array(
        'Excerpt'                                                => 'Project Description',
        'Write an excerpt (optional)'                           => 'Write a short project description',
        'Excerpts are optional hand-crafted summaries of your content that can be used in your theme. <a href="%s">Learn more about manual excerpts</a>.' => 'A short description of this work, used in listings and archive pages.',
    );
    return $map[$original] ?? $translated;
}
add_filter('gettext_with_context', 'Nonarchival\FieldsToolkit\relabel_excerpt', 10, 4);

// Flush rewrite rules on deactivation
function deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'Nonarchival\FieldsToolkit\deactivate');