<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://error.agency
 * @since      1.0.0
 *
 * @package    Rooftop_Custom_Content_Setup
 * @subpackage Rooftop_Custom_Content_Setup/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Custom_Content_Setup
 * @subpackage Rooftop_Custom_Content_Setup/admin
 * @author     Rooftop CMS <info@error.agency>
 */
class Rooftop_Custom_Content_Setup_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/rooftop-custom-content-setup-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/rooftop-custom-content-setup-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function content_type_menu_links() {
        $rooftop_menu_slug = "rooftop-overview";

        /**
         * Add the Content Type item to the Rooftop CMS admin menu
         */
        add_submenu_page($rooftop_menu_slug, "Content Setup", "Content Setup", "manage_options", $this->plugin_name."-overview", array($this, 'rooftop_content_type_menu_callback') );
    }

	function rooftop_content_type_menu_callback() {
        /* figure out which request method we should be using and render the appropriate view */
        if($_POST && array_key_exists('method', $_POST)) {
            $method = strtoupper($_POST['method']);
        }elseif($_POST && array_key_exists('id', $_POST)) {
            $method = 'PATCH';
        }else {
            $method = $_SERVER['REQUEST_METHOD'];
        }

        if( $_POST ) {
            if( ! isset($_POST[$this->plugin_name.'-token']) || ! wp_verify_nonce($_POST[$this->plugin_name.'-token'], $this->plugin_name) ) {
                print '<div class="wrap"><div class="errors"><p>Form token not verified</p></div></div>';
                exit;
            }
        }

        switch( $method ) {
            case 'GET':
                if(!array_key_exists('id', $_GET) && !array_key_exists('action', $_GET)){
                    $this->content_types_admin_index();
                }elseif(array_key_exists('action', $_GET) && $_GET['action'] !== 'edit' ){
                    if( $_GET['action'] === 'new' ) {
                        $this->content_types_admin_form();
                    }elseif( $_GET['action'] === 'new-taxonomy' ) {
                        $this->content_types_admin_taxonomy_form();
                    }elseif( $_GET['action'] === 'edit-taxonomy' ) {
                        $this->content_types_admin_edit_taxonomy_form();
                    }elseif( $_GET['action'] === 'new-page-template' ) {
                        $this->content_types_admin_page_template_form();
                    }elseif( $_GET['action'] === 'edit-page-template' ) {
                        $this->content_types_admin_edit_page_template_form();
                    }
                }elseif(array_key_exists('id', $_GET)) {
                    $this->content_types_view_form((int)$_GET['id']);
                }

                break;
            case 'POST':
                if( array_key_exists('new-taxonomy', $_POST) ){
                    $this->createTaxonomy( $_POST['content-type'] );
                }elseif( array_key_exists( 'new-page-template', $_POST) ){
                    $this->createPageTemplate( $_POST['content-type'] );
                }else {
                    $this->createContentTypeAndTaxonomies($_POST['content-type']);
                    $this->content_types_admin_index();
                }
                break;
            case 'PATCH':
                if( array_key_exists('edit-taxonomy', $_POST) ){
                    echo "Edit taxonomy";
                    $this->updateTaxonomy($_POST['content-type']);

                    echo "<div class='wrap'>Saved</div>";
                    echo "<script>document.location.reload()</script>";
                }elseif( array_key_exists( 'edit-page-template', $_POST ) ) {
                    $this->updatePageTemplate($_POST['content-type']);
                }else {
                    $this->updateContentTypeAndTaxonomies($_POST['content-type']);
                    $this->content_types_view_form((int)$_POST['id']);
                    echo "<script>window.location.reload()</script>";
                }
                break;
            case 'DELETE':
                if( array_key_exists( 'delete-taxonomy', $_POST) ){
                    $this->deleteTaxonomy((int)$_POST['id']);
                }elseif( array_key_exists( 'delete-page-template', $_POST ) ) {
                    $this->deletePageTemplate((int)$_POST['id']);
                }else {
                    $this->deleteContentTypeAndTaxonomies((int)$_POST['id']);
                }
                break;
        }
    }

	/**
     * list all custom content types
     */
    private function content_types_admin_index() {
        $content_types = Content_Type::getAll();
        $taxonomies = Taxonomy::getAll();
        $page_templates = Page_Template::getAll();

        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-content-types-admin-index.php';
    }

    /**
     * form for creating new content type
     */
    private function content_types_admin_form() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-content-types-admin-new.php';
    }

    private function content_types_admin_taxonomy_form() {
        $content_types = Content_Type::getAll();
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-content-types-admin-new-taxonomy.php';
    }

    private function content_types_admin_edit_taxonomy_form() {
        $taxonomy = Taxonomy::find((int)$_GET['id']);
        $content_types = Content_Type::getAll();

        if( ! $taxonomy ) {
            echo "Taxonomy not found";
            new WP_Error(404, "Taxonomy not found");
            return;
        }

        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-content-types-admin-edit-taxonomy.php';
    }

    private function content_types_admin_page_template_form() {
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-content-types-admin-new-page-template.php';
    }

    private function content_types_admin_edit_page_template_form() {
        $page_template = new Page_Template((int)$_GET['id']);
        require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-content-types-admin-edit-page-template.php';
    }

    /**
     * Form for editing an existing content type
     */
    private function content_types_view_form($id) {
        try {
            $content_type = new Content_Type($id);
            require_once plugin_dir_path( __FILE__ ) . 'partials/rooftop-content-types-admin-show.php';
        }catch(Exception $e) {
            echo "<div class='wrap'>Not found</div>";
            $this->content_types_admin_index();
        }
    }


    private function createContentTypeAndTaxonomies($data) {
        $content_type = new Content_Type($data['name']);

        $new_taxonomies = array_filter($data['taxonomies'], function($t){return $t!=="";});
        foreach($new_taxonomies as $new_taxonomy) {
            new Taxonomy($new_taxonomy, $content_type);
        }

        if( $content_type->save() ) {
            echo "<div class='wrap'>Saved</div>";
            $this->content_types_admin_index();
        }else {
            $this->renderErrors($content_type->errors);
        }
    }

    private function createTaxonomy($data) {
        $content_type = $data['taxonomy']['content-type-id'] == "" ? null : new Content_Type((int)$data['taxonomy']['content-type-id']);
        $new_taxonomy = new Taxonomy($data['taxonomy']['name'], $content_type);

        try {
            $new_taxonomy->save();
            $this->content_types_admin_index();
        }catch(Exception $e) {
            $this->renderErrors($new_taxonomy->errors);
            $this->content_types_admin_taxonomy_form();
        }
    }

    private function updateContentTypeAndTaxonomies($data) {
        foreach($data['taxonomies'] as $taxonomy_id => $taxonomy) {
            if( array_key_exists('_destroy', $taxonomy) && $taxonomy['_destroy'] === 'on' ) {
                try {
                    Taxonomy::deleteTaxonomy((int)$taxonomy_id);
                }catch( Exception $e ) {
                    new WP_Error(500, "Couldn't delete taxonomy");
                    exit;
                }
            }elseif( array_key_exists('name', $taxonomy) && strlen($taxonomy['name']) > 0 ) {
                $content_type = new Content_Type((int)$_POST['id']);
                $new_taxonomy = new Taxonomy($taxonomy['name'], $content_type);

                try {
                    $saved = $new_taxonomy->save();
                }catch ( Exception $e ) {
                    new WP_Error(500, "Couldn't save taxonomy in content type $content_type->name");
                    exit;
                }

                if( !$saved ) {
                    $this->renderErrors($new_taxonomy->errors);
                }

                $this->content_types_admin_index();
            }
        }
    }

    private function updateTaxonomy($data) {
        $taxonomy = Taxonomy::find((int)$_POST['id']);
        $new_parent_id = (int)$data['taxonomy']['content-type-id'];

        if( ! $new_parent_id ) {
            $taxonomy->parent_id = null;
        }else {
            $taxonomy->parent_id = $new_parent_id;
        }

        try {
            $saved = $taxonomy->save();
            $this->content_types_admin_index();
        }catch(Exception $e) {
            return new WP_Error(500, "Could not update taxonomy");
        }

        return $saved;
    }

    private function deleteContentTypeAndTaxonomies($id) {
        $deleted = Content_Type::deleteContentType((int)$id);

        if( !$deleted ){
            return new WP_Error(500, "Could not delete content type");
            exit;
        }else {
            echo "<div class='wrap'>Deleted</div>";
            $this->content_types_admin_index();
        }
    }

    private function deleteTaxonomy($id) {
        $deleted = Taxonomy::deleteTaxonomy((int)$id);

        if( $deleted ) {
            echo "<div class='wrap'>Deleted</div>";
            $this->content_types_admin_index();
        }else {
            new WP_Error(500, "Couldn't delete taxonomy");
            exit;
        }
    }

    private function createPageTemplate($data) {
        $template = $data['page-template']['name'] == "" ? null : new Page_Template($data['page-template']['name']);

        $saved = $template->save();
        $this->content_types_admin_index();
    }
    private function updatePageTemplate($data) {
        $template = new Page_Template((int)$_POST['id']);
        $template->name = $data['page-template']['name'];

        $saved = $template->save();

        if( $saved ) {
            echo "Saved";
            $this->content_types_admin_index();
        }else {
            $this->renderErrors($template->errors);
        }
    }
    private function deletePageTemplate($id) {
        $deleted = Page_Template::deleteCustomPageTemplate($id);

        if( $deleted ) {
            echo "<div class='wrap'>Deleted</div>";
            $this->content_types_admin_index();
        }else {
            new WP_Error(500, "Couldn't delete template");
            exit;
        }
    }

    private function renderErrors($errors) {
        require_once plugin_dir_path( __FILE__ ) . 'partials/render-errors.php';
        return new WP_Error(500, "Could not save content type");
        exit;
    }

    function add_content_type_tables( $blog_id ) {
        global $wpdb;

        $table_name = $wpdb->prefix . "${blog_id}_custom_content_data";

        if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = <<<EOSQL
CREATE TABLE $table_name (
    id MEDIUMINT NOT NULL AUTO_INCREMENT,
    name VARCHAR(256),
    type VARCHAR(256) NOT NULL,
    parent_id INTEGER,
    PRIMARY KEY(id)
)
EOSQL;

            dbDelta($sql);
        }
    }

    /**
     * @param $blog_id
     * @return mixed
     *
     * remove the blog specific content_types tables
     */
    public function remove_content_type_tables($blog_id){
        self::drop_database_tables($blog_id);
        return $blog_id;
    }

    public static function drop_database_tables($blog_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . "custom_content_data";
        $sql = <<<EOSQL
DROP TABLE $table_name;
EOSQL;

        $wpdb->query($sql);
    }

    /**
     * register our custom templates in the wp cache, overriding the menu that wp uses
     * thanks to http://www.wpexplorer.com/wordpress-page-templates-plugin
     */

    public function register_custom_templates( $attrs ) {
        $page_templates = [];
        foreach( Page_Template::getAll() as $template ) {
            $page_templates[$template->name] = $template->name;
        }

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list. If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates with the existing templates array from the cache.
        $templates = array_merge( $templates, $page_templates );

        // Add the modified cache to allow WordPress to pick it up for listing available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $attrs;
    }
}
