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

class Taxonomy {

    /**
     * Taxonomies associated with this content type
     *
     * @since 1.0.0
     * @var array
     */
    public $id, $name;
    public $content_type;
    public $parent_id = null;

    public $errors = [];

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     */
    public function __construct( $name, $content_type, $id = null ) {
        global $wpdb;
        $this->table = $table_name = $wpdb->prefix . "custom_content_data";

        $this->id = $id;
        $this->name = $name;
        $this->content_type = $content_type;

        if( !$this->id ) {
            $content_type->taxonomies[] = $this;
            $this->object_id = microtime(true); // needed so we can find the index of a taxonomy object in an array if un-saved taxonomies
        }

        $content_type->after_save[] = array($this, 'validate_and_save');
    }

    function validate() {
        global $wpdb;

        // validate that the name is present
        if( strlen($this->name) < 1 ) {
            $this->errors[]['name'][] = "not present";
        }

        // validate the name is unique in this content type
        if( $this->content_type ) {
            $previous_taxonomies = array_slice($this->content_type->taxonomies, 0, array_search($this, $this->content_type->taxonomies));
            $unsaved_matched_with_name = array_filter($previous_taxonomies, function($tax) {
                return $tax->name == $this->name;
            });

            if( count($unsaved_matched_with_name) || !$this->content_type->id && $wpdb->get_row($wpdb->prepare("SELECT id, UCASE(name) FROM ".$this->table." WHERE name = %s AND parent_id = %d LIMIT 1", strtoupper($this->name), $this->content_type->id), ARRAY_A) !== null ) {
                $this->errors['name'][] = $this->name . " already taken";
            }
        }else {
            if( $this->id ) {
                $count = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) AS count FROM $this->table WHERE id != %d AND name = %s AND type = %s AND parent_id IS NULL", $this->id, $this->name, 'Taxonomy'));
            }else {
                $count = (int)$wpdb->get_var($wpdb->prepare("SELECT COUNT(*) AS count FROM $this->table WHERE name = %s AND type = %s AND parent_id IS NULL", $this->name, 'Taxonomy'));
            }
            if( $count > 0 ) {
                $this->errors['name'][] = $this->name . " already taken";
            }
        }

        return count($this->errors)==0 ? true : false;
    }

    function save() {
        global $wpdb;

        if( $this->validate() ) {
            if( $this->id ) {
                $success = $this->_update();
            }else {
                $success = $this->_create();
                $this->id = $wpdb->insert_id;
            }

            if( !$success ) {
                throw new Exception('Rooftop Taxonomy Error');
            }
        }else {
            $success = false;
        }

        return $success;
    }

    function validate_and_save() {
        if( $this->validate() ) {
            return $this->save();
        }

        return true;
    }

    function delete() {
        $this->_delete();
    }

    private function _delete() {
        $removed_type = self::deleteTaxonomy( $this->id );

        return $removed_type !== false;
    }

    private function _update() {
        global $wpdb;

        if( !$this->parent_id ) {
            $updated = $wpdb->query( $wpdb->prepare( "UPDATE $this->table SET name = %s, parent_id = NULL WHERE id = %d AND type = %s", $this->name, $this->id, 'Taxonomy' ) );
        }else {
            $update = array( 'name' => $this->name, 'parent_id' => $this->parent_id );
            $where  = array( 'id' => $this->id, 'type' => 'Taxonomy' );

            $updated = $wpdb->update( $this->table, $update, $where ) !== false;
        }

        return $updated;
    }
    private function _create() {
        global $wpdb;

        $insert = array('name' => $this->name, 'type' => 'Taxonomy');
        if( $this->content_type ) {
            $insert['parent_id'] = $this->content_type->id;
        }
        $inserted = $wpdb->insert( $this->table, $insert );
        return $inserted !== false;
    }

    static function getAll() {
        global $wpdb;

        $table = $table_name = $wpdb->prefix . "custom_content_data";
        $taxonomies = $wpdb->get_results( $wpdb->prepare("SELECT * FROM $table WHERE type = %s AND parent_id IS NULL", 'Taxonomy') );

        return $taxonomies;
    }

    static function find($id) {
        global $wpdb;

        $table = $table_name = $wpdb->prefix . "custom_content_data";
        $taxonomy_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table WHERE type = %s AND id = %d", 'Taxonomy', $id) );

        $taxonomy = new self( $taxonomy_row->name, null, $taxonomy_row->id );
        $taxonomy->parent_id = (int)$taxonomy_row->parent_id;

        return $taxonomy;
    }

    static function deleteTaxonomy($id) {
        global $wpdb;
        $table = $wpdb->prefix . "custom_content_data";

        // remove the taxonomy from wordpress
        $taxonomy = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table WHERE type = %s AND id = %d", 'Taxonomy', $id) );
        if( $taxonomy ) {
            $taxonomy_name = str_replace( " ", "_", strtolower( $taxonomy->name ) );

            $wp_taxonomy = get_taxonomy( $taxonomy_name );
            $wp_taxonomy_terms = get_terms( $wp_taxonomy->name );

            array_map( function( $term ) use ( $taxonomy_name ) {
                wp_delete_term( $term->term_id, $taxonomy_name );
            }, $wp_taxonomy_terms );
        }

        $removed_taxonomy = $wpdb->delete( $table, array('id' => $id, 'type' => 'Taxonomy') );

        return $removed_taxonomy;
    }
}
