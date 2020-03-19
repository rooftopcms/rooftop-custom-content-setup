<?php

/**
 * The Content_Type specific functionality of the plugin.
 *
 * @link       http://error.agency
 * @since      1.0.0
 *
 * @package    Rooftop_Content_Type_Manager
 * @subpackage Rooftop_Content_Type_Manager/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Rooftop_Content_Type_Manager
 * @subpackage Rooftop_Content_Type_Manager/admin
 * @author     Error <info@error.agency>
 */

use Respect\Validation\Validator as validator;

class Content_Type {

	/**
	 * Model validator
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $validator;
    public $errors = [];

    /**
     * Taxonomies associated with this content type
     *
     * @since 1.0.0
     * @var array
     */
    public $id, $name;
    public $taxonomies = [];
    public $after_save = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $id_or_name ) {
        global $wpdb;
        $this->table = $wpdb->prefix . "custom_content_data";

        if( is_int($id_or_name) ) {
            $this->id = $id_or_name;
            $ctype_row = $wpdb->get_row($wpdb->prepare("SELECT id, name FROM ".$this->table." WHERE id = %d AND type = %s", $id_or_name, 'ContentType'));

            if( $ctype_row ) {
                $this->name = $ctype_row->name;
                $taxonomy_rows = $wpdb->get_results($wpdb->prepare("SELECT id, name, type FROM ".$this->table." WHERE parent_id = %d AND type = %s", $id_or_name, 'Taxonomy'));

                foreach($taxonomy_rows as $tax) {
                    $this->taxonomies[] = new Taxonomy($tax->name, $this, (integer)$tax->id);
                }
            }else {
                throw new Exception('Not found');
            }
        }else {
            $this->name = $id_or_name;
        }
	}

    function validate() {
        global $wpdb;

        // validate that the name is present
        if( strlen($this->name) < 1 ) {
            $this->errors[]['name'][] = "not present";
        }

        // validate the name is unique
        if( !$this->id && $wpdb->get_row($wpdb->prepare("SELECT id, UCASE(name) FROM ".$this->table." WHERE name = %s AND type = %s LIMIT 1", strtoupper($this->name), 'ContentType'), ARRAY_A) !== null ) {
            $this->errors['name'][] = "already taken";
        }

        foreach($this->taxonomies as $taxonomy) {
            if( ! $taxonomy->validate() ) {
                $this->errors['taxonomies'][] = $taxonomy->errors;
            }
        }

        return count($this->errors)==0 ? true : false;
    }

    function persisted() {
        return $this->id ? true : false;
    }

    function save() {
        if( $this->validate() ) {
            global $wpdb;

            $wpdb->query('START TRANSACTION');

            if( $this->id ) {
                $success = $this->_update();
            }else {
                $success = $this->_create();
                $this->id = $wpdb->insert_id;
            }

            // call any registered after_save hooks
            try {
                if( count( $this->after_save ) ) {
                    foreach( $this->after_save as $callback => $object ) {
                        call_user_func($object);
                    }
                }
            }catch (Exception $e) {
                $success = false;
            }

            if( $success ) {
                $wpdb->query('COMMIT');
            }else {
                $wpdb->query('ROLLBACK');
            }

            return $success;
        }

        return false;
    }

    function delete() {
        return self::delete($this->id);
    }

    private function _update() {
        global $wpdb;

        return $wpdb->update($this->table, array('name' => $this->name), array('id' => $this->id)) !== false;
    }
    private function _create() {
        global $wpdb;

        return $wpdb->insert($this->table, array('name' => $this->name, 'type' => 'ContentType')) !== false;
    }

    static function getAll() {
        global $wpdb;

        $table = $wpdb->prefix . "custom_content_data";

        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ${table} WHERE !(type IN (%s, %s) AND parent_id IS NULL)", 'Taxonomy', 'PageTemplate'));
        
        $types = array_filter($rows, function($row) {return $row->parent_id===null;});
        $taxonomies = array_filter($rows, function($row) {return $row->parent_id!==null;});

        foreach($taxonomies as $taxonomy) {
            $found_type = array_filter($types, function($type) use($taxonomy) {return $type->id == $taxonomy->parent_id;});
            $found_type = count($found_type) ? array_values($found_type)[0] : null;

            if($found_type) {
                $found_type->taxonomies[] = $taxonomy;
            }
        }

        return $types;
    }

    static function deleteContentType($id) {
        global $wpdb;
        $table = $wpdb->prefix . "custom_content_data";

        $removed_type = $wpdb->delete($table, array('id' => $id, 'type' => 'ContentType')) !== false;
        $removed_taxonomies = $wpdb->delete($table, array('parent_id' => $id)) !== false;

        return ($removed_type & $removed_taxonomies);
    }
}
