<?php
add_action ( 'init', function() {
    new Wyzi_Claim_Application();
}, 5 );

class Wyzi_Claim_Application {

    private $post_type;
    public $dir;
    public $file;

    public function __construct() {
        $this->post_type = 'wyzi_claimrequest';
        $this->register_post_type();
        
        add_filter('manage_wyzi_claimrequest_posts_columns', array(&$this, 'wyzi_claimrequest_columns'));
        add_action('manage_wyzi_claimrequest_posts_custom_column', array(&$this, 'custom_wyzi_claimrequest_column'), 10, 2);
        add_filter('post_row_actions', array(&$this, 'modify_wyzi_claimrequest_row_actions'), 10, 2);
        add_filter('bulk_actions-edit-wyzi_claimrequest', array(&$this, 'wyzi_claimrequest_bulk_actions'));
        add_action('admin_menu', array(&$this, 'remove_wyzi_claimrequest_meta_boxes'));
        add_action('add_meta_boxes', array(&$this, 'adding_wyzi_claimrequest_meta_boxes'), 10, 2);  
    }
    
     function adding_wyzi_claimrequest_meta_boxes($post_type, $post) {
        
        add_meta_box(
                'claim-form-data', __('Claim Form Data', 'wyzi-business-finder'), array(&$this,'render_claim_meta_box'), 'wyzi_claimrequest', 'normal', 'default'
        );
    }
    
    
       function render_claim_meta_box($post, $metabox) {
        $post_id = $post->ID;
        $form_data = get_post_meta($post_id, 'wyzi_claim_fields', true);
        if (!empty($form_data) && is_array($form_data)) {
            foreach ($form_data as $key => $value) {
                echo '<div class="wyzi-form-field">';
                echo '<label>' . html_entity_decode($value['label']) . ':</label>';
                if ($value['type'] == 'file') {
                    if(!empty($value['value']) && is_array($value['value'])){
                        foreach ($value['value'] as $attacment_id) {
                            echo '<span> <a href="' . wp_get_attachment_url($attacment_id) . '" download>' . get_the_title($attacment_id) . '</a> </span>';
                        }
                    }
                } else {
                    if(is_array($value['value'])){
                        echo '<span> ' . implode(', ', $value['value']) . '</span>';
                    } else{
                        echo '<span> ' . $value['value'] . '</span>';
                    }
                }
                echo '</div>';
            }
        }
    }
    
    
     function remove_wyzi_claimrequest_meta_boxes() {
        if (current_user_can('manage_options')) {
            remove_meta_box('submitdiv', 'wyzi_claimrequest', 'side');
        }
    }
    
      function wyzi_claimrequest_bulk_actions($actions) {
        unset($actions['edit']);
        return $actions;
    }
    
    function modify_wyzi_claimrequest_row_actions($actions, $post) {
        
        if ($post->post_type == "wyzi_claimrequest") {
            unset($actions['view']);
            unset($actions['edit']);
            unset($actions['inline hide-if-no-js']);
            unset($actions['trash']);
            $user_id = get_post_meta($post->ID, 'user_id', true);
            $user = new WP_User($user_id);
            $user_data = get_userdata($user_id);
            $actions['view'] = '<a href="' . get_edit_post_link($post->ID,'display') . '" title="" rel="permalink">' . __('View', 'wyzi-business-finder') . '</a>';
        // Here we can add approve or reject for later progress of this feature
        }
        return $actions;
    }
    function custom_wyzi_claimrequest_column($column, $post_id) {
        switch ($column) {
            case 'userid' :
                echo get_post_meta($post_id, 'username', true);
                break;
            case 'email' :
                echo get_post_meta($post_id, 'email', true);
                break;
        }
    }

    function wyzi_claimrequest_columns($columns) {
       
        unset($columns['title'], $columns['date']);
        $new_columns = array(
            'userid' => __('Username', 'wyzi-business-finder'),
            'email' => __('Email', 'wyzi-business-finder'),
            'date' => __('Date', 'wyzi-business-finder')
        );
        return array_merge($columns, $new_columns);
    }


    function register_post_type() {

        if (post_type_exists($this->post_type))
            return;
        $post_type_visibility = false;
        if(is_super_admin(get_current_user_id())){
            $post_type_visibility = true;
        }
        $app_name = esc_html__( 'Application', 'wyzi-business-finder' );
        $labels = array(
            'name' => _x('Claim Application', 'wyzi-business-finder'),
            'singular_name' => _x('Claim Application', 'wyzi-business-finder'),
            'add_new' => _x('Add New', $this->post_type, 'wyzi-business-finder'),
            'add_new_item' => sprintf(__('Add New %s', 'wyzi-business-finder'), $app_name),
            'edit_item' => sprintf(__('View %s', 'wyzi-business-finder'), $app_name),
            'new_item' => sprintf(__('New %s', 'wyzi-business-finder'), $app_name),
            'all_items' => sprintf(__('All %s', 'wyzi-business-finder'), $app_name),
            'view_item' => sprintf(__('View %s', 'wyzi-business-finder'), $app_name),
            'search_items' => sprintf(__('Search %a', 'wyzi-business-finder'), $app_name),
            'not_found' => sprintf(__('No %s found', 'wyzi-business-finder'), $app_name),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'wyzi-business-finder'), $app_name),
            'parent_item_colon' => '',
            'all_items' => __('Claim Application', 'wyzi-business-finder'),
            'menu_name' => __('Claim Application', 'wyzi-business-finder')
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => 'users.php',
            'show_in_nav_menus' => false,
            'query_var' => false,
            'rewrite' => true,
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false,
                'delete_posts' => false,
                'publish_posts' => 'publish_businesses',
            'edit_posts' => 'edit_businesses',
            'edit_others_posts' => 'edit_others_businesses',
            'delete_published_posts' => 'delete_published_businesses',
            'edit_published_posts' => 'edit_published_businesses',
            'delete_others_posts' => 'delete_others_businesses',
            'read_private_posts' => 'read_private_businesses',
            'read_post' => 'read_business',
            ),
            'map_meta_cap' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'supports' => array('')
        );
        register_post_type($this->post_type, $args);
    }

}
