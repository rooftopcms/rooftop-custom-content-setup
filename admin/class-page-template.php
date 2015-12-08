<?php

/**
 * The Page_Template specific functionality of the plugin.
 *
 * @link       http://error.agency
 * @since      1.0.0
 *
 * @package    Rooftop_Content_Type_Manager
 * @subpackage Rooftop_Content_Type_Manager/admin
 */

class Page_Template {

    public $errors = [];

    /**
     * Taxonomies associated with this content type
     *
     * @since 1.0.0
     * @var array
     */
    public $id, $name;

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
            $template_row = $wpdb->get_row($wpdb->prepare("SELECT id, name FROM ".$this->table." WHERE id = %d AND type = %s", $id_or_name, 'PageTemplate'));
            $this->name = $template_row->name;
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
        if( !$this->id && $wpdb->get_row($wpdb->prepare("SELECT UCASE(name) FROM ".$this->table." WHERE name = %s AND type = %s LIMIT 1", strtoupper($this->name), 'PageTemplate'), ARRAY_A) !== null ) {
            $this->errors['name'][] = "already taken";
        }elseif ( $wpdb->get_row( $wpdb->prepare("SELECT id, UCASE(name) FROM ".$this->table." WHERE id != %d AND name = %s AND type = %s LIMIT 1", (int)$this->id, strtoupper($this->name), 'PageTemplate'), ARRAY_A ) !== null ) {
            $this->errors['name'][] = "already taken";
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
                if( property_exists( $this, 'after_save' ) &&  count( $this->after_save ) ) {
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

        return $wpdb->insert($this->table, array('name' => $this->name, 'type' => 'PageTemplate')) !== false;
    }

    static function getAll() {
        global $wpdb;

        $table = $wpdb->prefix . "custom_content_data";
        $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM ${table} WHERE type = %s", 'PageTemplate'));

        return $rows;
    }

    static function deleteCustomPageTemplate($id) {
        global $wpdb;
        $table = $wpdb->prefix . "custom_content_data";

        $removed_template = $wpdb->delete($table, array('id' => $id, 'type' => 'PageTemplate')) !== false;

        return $removed_template;
    }
}
