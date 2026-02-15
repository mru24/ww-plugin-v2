<?php
/**
 * Posts Functions
 * Handles CRUD operations for the 'posts' table.
 */

if ( ! class_exists( 'WW_Plugin_V2_Posts' ) ) {

  class WW_Plugin_V2_Posts {

    protected $db;
    protected $table_prefix;

    public function __construct( $db, $table_prefix ) {
      $this->db = $db;
      $this->table_prefix = $table_prefix;
    }

    protected function get_table_name( $name ) {
      return $this->db->prefix . $this->table_prefix . $name;
    }

    public function get_posts() {
      $table = $this->get_table_name( 'posts' );
      $sql = "SELECT * FROM {$table} ORDER BY post_title ASC";
      return $this->db->get_results( $sql, ARRAY_A );
    }

    public function get_post( $post_id ) {
      $table = $this->get_table_name( 'posts' );
      $sql = $this->db->prepare( "SELECT * FROM {$table} WHERE id = %d", $post_id );
      return $this->db->get_row( $sql, ARRAY_A );
    }

    public function save_post( $data, $post_id = 0 ) {
      $table = $this->get_table_name( 'posts' );

      $fields = array(
        'post_title'        => sanitize_text_field( $data['post_title'] ),
        'post_excerpt'      => sanitize_text_field( $data['post_excerpt'] ),
        'post_content'      => sanitize_text_field( $data['post_content'] ),
        'post_author'       => sanitize_text_field( $data['post_author'] ),
        'post_status'       => sanitize_text_field( $data['post_status'] ),
      );

      $format = array( '%s', '%s', '%s', '%s', '%s' );

      if ( $post_id > 0 ) {
        $where        = array( 'id' => $post_id );
        $where_format = array( '%d' );
        return $this->db->update( $table, $fields, $where, $format, $where_format );
      } else {
        $fields['created_at'] = current_time( 'mysql', true );
        $format[]             = '%s';
        return $this->db->insert( $table, $fields, $format );
      }
    }
    public function delete_post( $post_id ) {
      $table = $this->get_table_name( 'posts' );
      return $this->db->delete( $table, array( 'id' => $post_id ), array( '%d' ) );
    }
  }
}