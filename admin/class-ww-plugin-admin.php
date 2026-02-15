<?php
/**
 * Admin Class: Handles all admin-side menus, hooks, and form rendering.
 * Delegates customer CRUD to WW_Booking_Customers class.
 */

if (! class_exists('WW_Plugin_V2_Admin')) {
  class WW_Plugin_V2_Admin {
    protected static $instance = null;
    protected $db;
    protected $table_prefix;
    protected $subscriptions;
    protected $posts;

    private $active_modules = [];

    /**
     * Singleton pattern with dependency injection for $wpdb.
     */
    public static function get_instance($db, $table_prefix) {
      if (is_null(self::$instance)) {
        self::$instance = new self($db, $table_prefix);
      }
      return self::$instance;
    }

    private function __construct($db, $table_prefix) {
      $this->db           = $db;
      $this->table_prefix = $table_prefix;

      $this->load_active_modules();

      // Load plugin functions
      require_once WW_PLUGIN_V2_DIR . 'includes/ww-plugin-functions.php';

      // SUBSCRIPTIONS
      if (! empty($this->active_modules['subscriptions'])) {
        require_once plugin_dir_path(__FILE__) . 'includes/subscriptions/class-ww-plugin-subscriptions.php';
        require_once plugin_dir_path(__FILE__) . 'includes/subscriptions/class-ww-plugin-subscription-controller.php';

        $this->subscriptions = new WW_Plugin_V2_Subscriptions($db, $table_prefix);

        $subscription_controller = new WW_Subscription_Controller($this->subscriptions);
      }

      // POSTS
      if (! empty($this->active_modules['posts'])) {
        require_once plugin_dir_path(__FILE__) . 'includes/posts/class-ww-plugin-posts.php';
        require_once plugin_dir_path(__FILE__) . 'includes/posts/class-ww-plugin-post-controller.php';

        $this->posts = new WW_Plugin_V2_Posts($db, $table_prefix);

        $post_controller = new WW_Post_Controller($this->posts);
      }

      add_action('admin_menu', [$this, 'add_plugin_menu']);
      add_action('admin_init', [$this, 'setup_admin_hooks']);

      add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function enqueue_admin_scripts( $hook ) {
      wp_enqueue_media();
    }

    private function load_active_modules() {
      $defaults = [
        'subscriptions'   => true,
        'posts'           => true
      ];

      $saved = get_option('ww_plugin_v2_active_modules', []);

      $this->active_modules = wp_parse_args($saved, $defaults);
    }

    public function setup_admin_hooks() {
      // Subscription admin hooks
      if (! empty($this->active_modules['subscriptions'])) {
        $controller = new WW_Subscription_Controller($this->subscriptions);

        add_action('admin_post_ww_plugin_v2_add_subscription', [$controller, 'handle_save']);
        add_action('admin_post_ww_plugin_v2_update_subscription', [$controller, 'handle_save']);
        add_action('admin_post_ww_plugin_v2_delete_subscription', [$controller, 'handle_delete']);
      }

      // Post admin hooks
      if (! empty($this->active_modules['posts'])) {
        $controller = new WW_Post_Controller($this->posts);

        add_action('admin_post_ww_plugin_v2_add_post', [$controller, 'handle_save']);
        add_action('admin_post_ww_plugin_v2_update_post', [$controller, 'handle_save']);
        add_action('admin_post_ww_plugin_v2_delete_post', [$controller, 'handle_delete']);
      }
    }

    // --- ADMIN MENU SETUP ---

    public function add_plugin_menu() {
      add_menu_page(
        'WW Plugin V2 menu',
        'WW Plugin',
        'manage_options',
        'ww-plugin-v2-main',
        [$this, 'render_dashboard_page'],
        'dashicons-calendar-alt',
        6
      );
      if (! empty($this->active_modules['subscriptions'])) {
        add_submenu_page(
          'ww-plugin-v2-main',
          'Subscriptions',
          'Subscriptions',
          'manage_options',
          'ww-plugin-v2-subscriptions',
          [$this, 'render_subscriptions_page']
        );
        add_submenu_page(
          null,
          'Edit Subscription',
          'Edit Subscription',
          'manage_options',
          'ww-plugin-v2-edit-subscription',
          [$this, 'render_edit_subscription_page']
        );
      };
      if (! empty($this->active_modules['posts'])) {
        add_submenu_page(
          'ww-plugin-v2-main',
          'Posts',
          'Posts',
          'manage_options',
          'ww-plugin-v2-posts',
          [$this, 'render_posts_page']
        );
        add_submenu_page(
          null,
          'Edit Post',
          'Edit Post',
          'manage_options',
          'ww-plugin-v2-edit-post',
          [$this, 'render_edit_post_page']
        );
      }
    }

// DASHBOARD
    public function render_dashboard_page() {
      $table = $this->db->prefix . $this->table_prefix;
      // Get counts for dashboard
      $subscriptions_count = $this->db->get_var("SELECT COUNT(*) FROM {$table}subscriptions");

      require_once plugin_dir_path( __FILE__ ) . 'views/dashboard.php';
    }

// SUBSCRIPTIONS
    public function render_subscriptions_page() {
      $subscriptions = $this->subscriptions->get_subscriptions();
      require_once plugin_dir_path(__FILE__) . 'views/subscriptions/subscriptions-list.php';
    }

    public function render_edit_subscription_page() {
      $subscription_id   = isset($_GET['id']) ? intval($_GET['id']) : 0;
      $subscription_data = [];
      $title             = 'Add New Subscription';

      if ($subscription_id > 0) {
        $subscription_data = $this->subscriptions->get_subscription($subscription_id);
        if ($subscription_data) {
          $title = 'Edit Subscription: ' . esc_html($subscription_data['plan_name']);
        }
      }
      require_once plugin_dir_path(__FILE__) . 'views/subscriptions/subscription-form.php';
    }

// POSTS
    public function render_posts_page() {
      $posts = $this->posts->get_posts();
      require_once plugin_dir_path(__FILE__) . 'views/posts/posts-list.php';
    }

    public function render_edit_post_page() {
      $post_id      = isset($_GET['id']) ? intval($_GET['id']) : 0;
      $data    = [];
      $title        = 'Add New Post';

      if ($post_id > 0) {
        $data = $this->posts->get_post($post_id);
        if ($data) {
          $title = 'Edit Post: ' . esc_html($data['post_title']);
        }
      }
      require_once plugin_dir_path(__FILE__) . 'views/posts/post-form.php';
    }
  }
}
