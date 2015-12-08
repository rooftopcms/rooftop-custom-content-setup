<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://error.agency
 * @since      1.0.0
 *
 * @package    Rooftop_Custom_Content_Setup
 * @subpackage Rooftop_Custom_Content_Setup/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Custom_Content_Setup
 * @subpackage Rooftop_Custom_Content_Setup/public
 * @author     Rooftop CMS <info@error.agency>
 */
class Rooftop_Custom_Content_Setup_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_Custom_Content_Setup_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Custom_Content_Setup_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-custom-content-setup-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Rooftop_Custom_Content_Setup_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Rooftop_Custom_Content_Setup_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-custom-content-setup-public.js', array( 'jquery' ), $this->version, false );

	}

    public function register_custom_content_types() {
        $content_types = Content_Type::getAll();

        foreach($content_types as $content_type) {
            $this->register_content_type($content_type->name);

            if( property_exists($content_type, 'taxonomies') ) {
                foreach($content_type->taxonomies as $taxonomy) {
                    $this->register_taxonomy($taxonomy->name, str_replace(" ","_",strtolower($content_type->name)));
                }
            }
        }

        $taxonomies = Taxonomy::getAll();
        foreach($taxonomies as $taxonomy) {
            $this->register_taxonomy($taxonomy->name, 'all');
        }
    }

    private function register_content_type($type, $args = null) {
        $inflector = ICanBoogie\Inflector::get('en');
        $sanitised = str_replace(" ","_",strtolower($type));
        $singular = $inflector->titleize($type);
        $plural = $inflector->pluralize($singular);
        $default_args = array(
            'hierarchical' => false,
            'labels' => array(
                'name' => $plural,
                'singular_name' => $singular,
                'menu_name' => $plural,
                'name_admin_bar' => $singular,
                'all_items' => "All $plural",
                'add_new' => "New $singular",
                'add_new_item' => "New $singular",
                'edit_item' => "Edit $singular",
                'new_item' => "New $singular",
                'view_item' => "View $singular",
                'search_items' => "Search $plural",
                'not_found' => "No $plural found",
                'not_found_in_trash' => "No $plural found in trash",
                'parent_item_colon' => "Parent $singular:"
            ),
            'description' => "A $type",
            'public' => true,
            'supports' => array(
                'title', 'editor'
            ),
            'show_ui' => true,
            'menu_position' => 20,
            'capability_type' => 'page',
            'has_archive' => true,
            'show_in_rest' => true,
            'rest_base' => $inflector->pluralize($sanitised),
            'include_taxonomies_in_response' => true
        );

        if(is_null($args) || !is_array($args)){
            $args = $default_args;
        }else {
            $args = array_merge($default_args, $args);
        }

        register_post_type($sanitised, $args);
    }

    private function register_taxonomy($name, $content_type, $args = null) {
        $inflector = ICanBoogie\Inflector::get('en');
        $sanitised = str_replace(" ","_",strtolower($name));
        $human = $inflector->titleize($sanitised);
        $plural = $inflector->pluralize($human);
        $singular = $inflector->singularize($human);
        $default_args = array(
            'name' => $plural,
            'singular_name' => $singular,
            'labels' => array(
                'name' => $plural,
                'singular_name' => $singular,
                'menu_name' => $plural,
                'all_items' => "All $plural",
                'edit_item' => "Edit $singular",
                'view_item' => "View $singular",
                'update_item' => "Update $singular",
                'add_new_item' => "Add new $singular",
                'new_item_name' => "New $singular name",
                'parent_item' => "Parent $singular",
                'parent_item_colon' => "Parent $singular:",
                'search_items' => "Search $plural",
                'popular_items' => "Popular $plural",
                'separate_items_with_commas' => "Separate $plural with commas",
                'add_or_remove_items' => "Add or remove $plural",
                'choose_from_most_used' => "Most used $plural",
                'not_found' => "No $plural found"
            ),
            'show_in_rest' => true,
            'query_var' => true
        );

        if(is_null($args) || !is_array($args)){
            $args = $default_args;
        }else {
            $args = array_merge($default_args, $args);
        }

        if(is_array($content_type)){
            $types = $content_type;
        }elseif($content_type == 'all') {
            $types = get_post_types(array('public' => true));
        }else {
            $types = [$content_type];
        }

        register_taxonomy($sanitised, array_values($types), $args);
    }
}
