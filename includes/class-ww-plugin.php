<?php
/**
 * Core Plugin Class: Handles database, activation, and class loading.
 */

// Admin
require_once WW_PLUGIN_V2_DIR . 'admin/class-ww-plugin-admin.php';

// Frontend
require_once WW_PLUGIN_V2_DIR . 'frontend/class-ww-plugin-frontend.php';


if (! class_exists('WW_Plugin_V2')) {
  class WW_Plugin_V2 {
    protected static $instance = null;
    protected $db;
    protected $table_prefix = 'ww_plugin_db_';
    protected $admin;
    protected $frontend;

    /**
     * Singleton pattern ensures only one instance of the class exists.
     */
    public static function get_instance() {
      if (is_null(self::$instance)) {
        self::$instance = new self();
      }
      return self::$instance;
    }

    private function __construct() {
      global $wpdb;
      $this->db = $wpdb;

      // Activation hook (must remain here)
      register_activation_hook(WW_PLUGIN_V2_DIR . 'ww-plugin.php', [$this, 'WW_Plugin_v2_activate']);

      // Core WordPress Hooks
      //add_action('rest_api_init', [$this, 'init_rest_api']);

      // ADMIN
      $this->admin = WW_Plugin_V2_Admin::get_instance( $this->db, $this->table_prefix );

      // FRONTEND
      $this->frontend = new WW_Plugin_V2_Frontend( $this->db, $this->table_prefix );
    }

    /**
     * Get the full database table name with prefix.
     */
    public function get_table_name($name) {
      return $this->db->prefix . $this->table_prefix . $name;
    }

    // --- ACTIVATION AND DB SETUP (Keep activation logic here) ---

    /**
     * Creates custom database tables upon plugin activation.
     */
    public function WW_Plugin_v2_activate() {
      global $wpdb;
      require_once ABSPATH . 'wp-admin/includes/upgrade.php';

      $charset_collate = $this->db->get_charset_collate();
      $table_prefix    = $this->table_prefix;

      // All table creation SQL remains here for activation
      $tables = [
       'posts' => "CREATE TABLE " . $this->get_table_name('posts') . " (
          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          post_id BIGINT NOT NULL,
          post_status enum('draft','published','cancelled') NOT NULL DEFAULT 'draft',
          post_title text NULL,
          post_excerpt text NULL,
          post_content text NULL,
          post_author text NULL,
          created_at datetime NOT NULL,
          updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          KEY post_id (post_id)
        ) $charset_collate;",
        'authors'       => "CREATE TABLE " . $this->get_table_name('authors') . " (
          iid BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          first_name varchar(100) NOT NULL,
          last_name varchar(100) NOT NULL,
          email varchar(150) NOT NULL,
          primary_phone varchar(50) NULL,
          secondary_phone varchar(50) NULL,
          membership_status varchar(50) NULL,
          subscriptions varchar(50) NULL,
          address_locality varchar(50) NULL,
          address_town text NULL,
          address_postcode varchar(50) NULL,
          address_country varchar(50) NULL,
          created_at datetime NOT NULL,
          updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          UNIQUE KEY email (email)
        ) $charset_collate;",
        'subscriptions' => "CREATE TABLE " . $this->get_table_name('subscriptions') . " (
          id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
          plan_name varchar(100) NOT NULL,
          plan_id varchar(100) NOT NULL,
          status varchar(20) DEFAULT 'active' NOT NULL,
          tenure varchar(20) DEFAULT '1 year' NOT NULL,
          price int(11) DEFAULT NULL,
          payment_gateway_id varchar(255) NULL,
          created_at datetime NOT NULL,
          updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (id),
          UNIQUE KEY plan_id (plan_id)
        ) $charset_collate;",
      ];

      foreach ($tables as $sql) {
        dbDelta($sql);
      }

      add_option('ww_db_version', WW_DB_VERSION);
    }
  }
}
