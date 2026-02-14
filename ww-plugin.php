<?php
/**
 * Plugin Name: WW Plugin V2
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
if ( ! defined( 'WW_PLUGIN_V2_DIR' ) ) {
    define( 'WW_PLUGIN_V2_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WW_DB_VERSION' ) ) {
    define( 'WW_DB_VERSION', '1.0' );
}

/**
 * Core Plugin Loader
 */
if ( ! class_exists( 'WW_Plugin_V2' ) ) {
    // Include the main plugin class which holds all the logic and handles loading the admin/public components.
    require_once WW_PLUGIN_V2_DIR . 'includes/class-ww-plugin.php';

    /**
     * Instantiates the main plugin class and assigns it to a global variable.
     */
    function WW_Plugin_V2_run() {
        // We use a global variable to make the plugin instance accessible throughout WordPress.
        $GLOBALS['ww-plugin-v2'] = WW_Plugin_V2::get_instance();
    }

    // Start the plugin
    WW_Plugin_V2_run();
}