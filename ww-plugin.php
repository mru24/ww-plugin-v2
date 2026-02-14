<?php
/**
 * Plugin Name: WW Plugin
 * Description: Basic plugin with database data store
 * Version: 1.1.0
 * Author: Val Wroblewski
 * License: GPL2
 */

// Exit if accessed directly (security measure)
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
if ( ! defined( 'WW_PLUGIN_DIR' ) ) {
    // Defines the full path to the plugin directory (e.g., /wp-content/plugins/ww-booking-system/)
    define( 'WW_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WW_DB_VERSION' ) ) {
    // Version used for checking and running database migrations/updates
    define( 'WW_DB_VERSION', '1.0' );
}

/**
 * Core Plugin Loader
 */
if ( ! class_exists( 'WW_Plugin' ) ) {
    // Include the main plugin class which holds all the logic and handles loading the admin/public components.
    require_once WW_PLUGIN_DIR . 'includes/class-ww-plugin.php';

    /**
     * Instantiates the main plugin class and assigns it to a global variable.
     */
    function WW_Plugin_run() {
        // We use a global variable to make the plugin instance accessible throughout WordPress.
        $GLOBALS['ww-system'] = WW_Booking_Plugin::get_instance();
    }

    // Start the plugin
    WW_Plugin_run();
}