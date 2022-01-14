<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://chebre.net
 * @since      1.0.0
 *
 * @package    Chebre_Map
 * @subpackage Chebre_Map/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Chebre_Map
 * @subpackage Chebre_Map/admin
 * @author     Helder Chebre <hchebre@gmail.com>
 */
class Chebre_Map_Admin
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;


    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;


    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Chebre_Map_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Chebre_Map_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/chebre-map-admin.css', array(), $this->version, 'all');
        wp_enqueue_style($this->plugin_name . '_leaflet_css', plugin_dir_url(__FILE__) . 'css/leaflet.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Chebre_Map_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Chebre_Map_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/chebre-map-admin.js', array('jquery'), $this->version, false);

        wp_enqueue_script($this->plugin_name . 'leafelt_js', plugin_dir_url(__FILE__) . 'js/leaflet.js', array('jquery'), $this->version, false);

        if (!did_action('wp_enqueue_media')) {
            wp_enqueue_media();
        }

//        wp_enqueue_script( 'wp.media', $this->plugin_name, plugin_dir_url(__FILE__) . 'js/chebre-map-admin.js', array( 'jquery' ) );


    }


    /**
     * Register the administration menu for this plugin into the WordPress Dashboard menu.
     *
     * @since    1.0.0
     */

    public function add_plugin_admin_menu()
    {

        /*
         * Add a settings page for this plugin to the Settings menu.
         *
         * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
         *
         *        Administration Menus: http://codex.wordpress.org/Administration_Menus
         *
         */
        add_options_page('Chebre Map Options Functions Setup', 'Chebre Map', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page')
        );
    }

    /**
     * Add settings action link to the plugins page.
     *
     * @since    1.0.0
     */

    public function add_action_links($links)
    {
        /*
        *  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
        */
        $settings_link = array(
            '<a href="' . admin_url('options-general.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',
        );
        return array_merge($settings_link, $links);

    }

    public function validate($input)
    {
        // All checkboxes inputs
        $valid = array();

        $valid['sidebar-image-url'] = esc_url($input['sidebar-image-url']);
        $valid['sidebar-avatar-url'] = esc_url($input['sidebar-avatar-url']);
        $valid['sidebar-title'] = ($input['sidebar-title']);
        return $valid;
    }

    public function options_update()
    {
        register_setting($this->plugin_name, $this->plugin_name, array($this, 'validate'));
    }

    /**
     * Render the settings page for this plugin.
     *
     * @since    1.0.0
     */

    public function display_plugin_setup_page()
    {
        include_once('partials/chebre-map-admin-display.php');
    }

    /**
     * Creates a new custom post type
     *
     * @since 1.0.0
     * @access public
     * @uses register_post_type()
     */
    public function new_poi_cpt_callback()
    {

        $this->add_poi_cpt_taxonomy();
        $this->register_cpt();

    }

    public function register_cpt()
    {
        $labels = array(
            'name' => 'Point of interests',
            'singular_name' => 'Point of interest',
            'menu_name' => 'Point of interest',
            'name_admin_bar' => 'Point of interest',
            'archives' => 'Item Archives',
            'attributes' => 'Item Attributes',
            'parent_item_colon' => 'Parent Item:',
            'all_items' => 'All Point of interests',
            'add_new_item' => 'Add New Point of interest',
            'add_new' => 'Add New Point of interest',
            'new_item' => 'New Item',
            'edit_item' => 'Edit Point of interest',
            'view_item' => 'View Item',
            'view_items' => 'View Items',
            'search_items' => 'Search Item',
            'not_found' => 'Not found',
            'not_found_in_trash' => 'Not found in Trash',
        );
        $args = array(
            'description' => 'Post Type Description',
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'query_var' => true,
            'has_archive' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
//            'rewrite' => array('slug' => 'chebre-map'),
            'capability_type' => 'post',
        );

        register_post_type('chebre_map', $args);
    }

    public function add_poi_cpt_taxonomy()
    {


        $labels = array(
            'name' => "Point of interest's category",
            'singular_name' => 'Category',
            'menu_name' => 'Category',
            'all_items' => 'All Categories',
            'parent_item' => 'Parent Item',
            'parent_item_colon' => 'Parent Item:',
            'new_item_name' => 'New Item Name',
            'add_new_item' => 'Add New Category',
            'add_new' => 'Add New Category',
            'edit_item' => 'Edit Category',
            'view_item' => 'View Item',
            'separate_items_with_commas' => 'Separate items with commas',
            'add_or_remove_items' => 'Add or remove items',
            'choose_from_most_used' => 'Choose from the most used',
            'popular_items' => 'Popular Items',
            'search_items' => 'Search Items',
            'not_found' => 'Not Found',
            'no_terms' => 'No items',
            'items_list' => 'Items list',
            'items_list_navigation' => 'Items list navigation',

        );
        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'publicly_queryable' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'rewrite' => array(
                'slug' => 'poi_category',
                'with_front' => false,
                'feeds' => true,
            ),
        );

        register_taxonomy('poi_category', array('chebre_map'), $args);
    }


    public function add_poi_cpt_metaboxes_callback()
    {

        // add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args );
        add_meta_box('new_poi_cpt_latitude_metabox', 'Location Details', array($this, 'new_poi_cpt_coordenate_metabox_callback'), 'chebre_map', 'normal', 'default');

    }

    public function new_poi_cpt_coordenate_metabox_callback($post)
    {
// Add a nonce field so we can check for it later.
        wp_nonce_field($this->plugin_name . '_meta_box_nonce', $this->plugin_name . '_meta_box_nonce');
        ?>

        <div class="post_type_field_containers">

            <div class="row">
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary" id="btn-select-coordenate">Select Coordenate</button>
                    <div class="input-group mb-3 mt-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Latitude</span>
                        </div>
                        <?php
                        $args = array(
                            'type' => 'input',
                            'subtype' => 'text',
                            'id' => $this->plugin_name . '_latitude',
                            'name' => $this->plugin_name . '_latitude',
                            'required' => '',
                            'get_options_list' => '',
                            'value_type' => 'normal',
                            'wp_data' => 'post_meta',
                            'post_id' => $post->ID,
                            'placeholder' => 'Latitude',
                            'className' => 'form-control'
                        );
                        // this gets the post_meta value and echos back the input
                        $this->plugin_name_render_settings_field($args);
                        ?>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Longitude</span>
                        </div>
                        <?php
                        $args = array(
                            'type' => 'input',
                            'subtype' => 'text',
                            'id' => $this->plugin_name . '_longitude',
                            'name' => $this->plugin_name . '_longitude',
                            'required' => '',
                            'get_options_list' => '',
                            'value_type' => 'normal',
                            'wp_data' => 'post_meta',
                            'post_id' => $post->ID,
                            'placeholder' => 'Longitude',
                            'className' => 'form-control'
                        );
                        // this gets the post_meta value and echos back the input
                        $this->plugin_name_render_settings_field($args);
                        ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal -->
        <div class="chebre-map-modal" id="chebre-map-modal">
            <div class="chebre-map-modal-header">
                <h3 class="modal-title">Select Location <a href="#" class="chebre-map-modal-close">X</a></h3>

            </div>
            <div class="chebre-map-modal-content">
                <div id="chebre-map-modal-map"></div>
            </div>
        </div>
        <?php
    }


    public function plugin_name_render_settings_field($args)
    {
        if ($args['wp_data'] == 'option') {
            $wp_data_value = get_option($args['name']);
        } elseif ($args['wp_data'] == 'post_meta') {
            $wp_data_value = get_post_meta($args['post_id'], $args['name'], true);
        }

        switch ($args['type']) {
            case 'input':
                $value = ($args['value_type'] == 'serialized') ? serialize($wp_data_value) : $wp_data_value;
                if ($args['subtype'] != 'checkbox') {
                    $prependStart = (isset($args['prepend_value'])) ? '
<div class="input-prepend"><span class="add-on">' . $args['prepend_value'] . '</span>' : '';
                    $prependEnd = (isset($args['prepend_value'])) ? '
</div>' : '';
                    $step = (isset($args['step'])) ? 'step="' . $args['step'] . '"' : '';
                    $min = (isset($args['min'])) ? 'min="' . $args['min'] . '"' : '';
                    $max = (isset($args['max'])) ? 'max="' . $args['max'] . '"' : '';
                    if (isset($args['disabled'])) {
// hide the actual input bc if it was just a disabled input the informaiton saved in the database would be wrong - bc it would pass empty values and wipe the actual information
                        echo $prependStart . '<input placeholder="' . $args['placeholder'] . '" class="' . $args['className'] . '" type="' . $args['subtype'] . '"
                             id="' . $args['id'] . '_disabled" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '_disabled" size="40" disabled value="' . esc_attr($value) . '" />
<input type="hidden"
       id="' . $args['id'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    } else {
                        echo $prependStart . '<input  placeholder="' . $args['placeholder'] . '" class="' . $args['className'] . '" type="' . $args['subtype'] . '"
                             id="' . $args['id'] . '" "' . $args['required'] . '" ' . $step . ' ' . $max . ' ' . $min . ' name="' . $args['name'] . '" size="40" value="' . esc_attr($value) . '" />' . $prependEnd;
                    }
                    /*<input
                            required="required" '.$disabled.' type="number" step="any" id="'.$this->plugin_name.'_cost2" name="'.$this->plugin_name.'_cost2" value="' . esc_attr( $cost ) . '" size="25" />
                    <input type="hidden" id="'.$this->plugin_name.'_cost" step="any" name="'.$this->plugin_name.'_cost"
                           value="' . esc_attr( $cost ) . '"/>*/

                } else {
                    $checked = ($value) ? 'checked' : '';
                    echo '<input type="' . $args['subtype'] . '" class="' . $args['className'] . '"
             id="' . $args['id'] . '" "' . $args['required'] . '" name="' . $args['name'] . '" size="40" value="1" ' . $checked . ' />';
                }
                break;
            default:
# code...
                break;
        }
    }

    public function save_cpt_meta_boxes_callback($post_id)
    {


        if (!isset($_POST[$this->plugin_name . '_meta_box_nonce'])) {
            return;
        }


        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }


        if (!current_user_can('edit_post', $post_id)) {
            return;
        }


        if (!isset($_POST[$this->plugin_name . '_latitude']) &&
            !isset($_POST[$this->plugin_name . '_longitude'])) {
            return;
        }


        $latitude = sanitize_text_field($_POST[$this->plugin_name . "_latitude"]);
        $longitude = sanitize_text_field($_POST[$this->plugin_name . "_longitude"]);

        update_post_meta($post_id, $this->plugin_name . '_latitude', $latitude);
        update_post_meta($post_id, $this->plugin_name . '_longitude', $longitude);

    }


}
