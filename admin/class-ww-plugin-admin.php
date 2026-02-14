<?php
/**
 * Admin Class: Handles all admin-side menus, hooks, and form rendering.
 * Delegates customer CRUD to WW_Booking_Customers class.
 */

if (! class_exists('WW_Plugin_V2_Admin')) {

    class WW_Plugin_V2_Admin
    {

        protected static $instance = null;
        protected $db;
        protected $table_prefix;
        protected $subscriptions;

        private $active_modules = [];

        /**
         * Singleton pattern with dependency injection for $wpdb.
         */
        public static function get_instance($db, $table_prefix)
        {
            if (is_null(self::$instance)) {
                self::$instance = new self($db, $table_prefix);
            }
            return self::$instance;
        }

        private function __construct($db, $table_prefix)
        {
            $this->db           = $db;
            $this->table_prefix = $table_prefix;

            $this->load_active_modules();

            // Load plugin functions
            require_once WW_PLUGIN_V2_DIR . 'includes/ww-plugin-functions.php';

            // Load Subscription Functions
            if (! empty($this->active_modules['subscriptions'])) {
                require_once plugin_dir_path(__FILE__) . 'includes/subscriptions/class-ww-plugin-subscriptions.php';
                $this->subscriptions = new WW_Plugin_V2_Subscriptions($db, $table_prefix);
            }

            // Core WordPress Hooks
            add_action('admin_menu', [$this, 'add_plugin_menu']);
            add_action('admin_init', [$this, 'setup_admin_hooks']);

            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        }

        public function enqueue_admin_scripts( $hook ) {
            // Only load scripts on our lake edit page
            if ( ! isset( $_GET['page'] ) || 'my-booking-edit-lake' !== $_GET['page'] ) {
                return;
            }
            wp_enqueue_media();
        }

        private function load_active_modules()
        {
            $defaults = [
                'subscriptions' => true,

            ];

            $saved = get_option('ww_plugin_v2_active_modules', []);

            // Merge user preferences with defaults
            $this->active_modules = wp_parse_args($saved, $defaults);
        }

        public function setup_admin_hooks()
        {
            // Subscription admin hooks
            if (! empty($this->active_modules['subscriptions'])) {
                add_action('admin_post_ww_plugin_v2_add_subscription', [$this, 'process_subscription_actions']);
                add_action('admin_post_ww_plugin_v2_update_subscription', [$this, 'process_subscription_actions']);
                add_action('admin_post_ww_plugin_v2_delete_subscription', [$this, 'process_delete_subscription']);
            }
        }

        // --- ADMIN MENU SETUP ---

        public function add_plugin_menu()
        {
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

        public function process_subscription_actions()
        {
            if (! current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions to access this page.');
            }

            if (! isset($_POST['_wpnonce']) || ! wp_verify_nonce($_POST['_wpnonce'], 'ww_plugin_v2_subscription_nonce')) {
                wp_die('Security check failed.');
            }

            $subscription_id = isset($_POST['subscription_id']) ? intval($_POST['subscription_id']) : 0;

            $data = [
                'plan_name'          => $_POST['plan_name'],
                'plan_id'            => sanitize_title($_POST['plan_name']),
                'status'             => $_POST['status'],
                'tenure'             => $_POST['tenure'],
                'price'              => $_POST['price'],
                'payment_gateway_id' => $_POST['payment_gateway_id'],
            ];

            $result  = $this->subscriptions->save_subscription($data, $subscription_id);
            $message = $result ? 1 : 2; // 1=success, 2=error

            wp_redirect(admin_url('admin.php?page=ww-plugin-v2-subscriptions&message=' . $message));
            exit;
        }

        public function process_delete_subscription()
        {
            if (! current_user_can('manage_options')) {
                wp_die('You do not have sufficient permissions to access this page.');
            }

            if (! isset($_GET['_wpnonce']) || ! wp_verify_nonce($_GET['_wpnonce'], 'ww_plugin_v2_delete_subscription')) {
                wp_die('Security check failed.');
            }

            $subscription_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $result          = $this->subscriptions->delete_subscription($subscription_id);
            $message         = $result ? 3 : 4; // 3=success, 4=error

            wp_redirect(admin_url('admin.php?page=ww-plugin-v2-subscriptions&message=' . $message));
            exit;
        }

        public function render_subscriptions_page()
        {
            $subscriptions = $this->subscriptions->get_subscriptions();
            require_once plugin_dir_path(__FILE__) . 'views/subscriptions/subscriptions-list.php';
        }

        public function render_edit_subscription_page()
        {
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
    }
}
