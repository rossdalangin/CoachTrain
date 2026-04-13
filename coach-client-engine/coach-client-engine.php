<?php
/**
 * Plugin Name: Coach Client Engine
 * Plugin URI:  https://coachclientengine.com
 * Description: All-in-one client acquisition system for coaches and consultants.
 * Version:     1.0.0
 * Author:      Coach Client Engine Team
 * Author URI:  https://coachclientengine.com
 * Text Domain: coach-client-engine
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'CCE_VERSION', '1.0.0' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-coach-client-engine.php';

/**
 * Begins execution of the plugin.
 */
function run_coach_client_engine() {
	$plugin = new Coach_Client_Engine();
	$plugin->run();
}

/**
 * The code that runs during plugin activation.
 */
function activate_coach_client_engine() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cce-activator.php';
	CCE_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_coach_client_engine() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cce-deactivator.php';
	CCE_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_coach_client_engine' );
register_deactivation_hook( __FILE__, 'deactivate_coach_client_engine' );

/**
 * Add action links to plugins page.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
    $settings_link = '<a href="' . admin_url( 'admin.php?page=cce-settings' ) . '">Settings</a>';
    $dashboard_link = '<a href="' . admin_url( 'admin.php?page=coach-client-engine' ) . '">Dashboard</a>';
    array_unshift( $links, $settings_link, $dashboard_link );
    return $links;
} );

run_coach_client_engine();
