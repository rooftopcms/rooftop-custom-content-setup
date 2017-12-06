<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://error.agency
 * @since             1.0.0
 * @package           Rooftop_Custom_Content_Setup
 *
 * @wordpress-plugin
 * Plugin Name:       Rooftop Custom Content Setup
 * Plugin URI:        http://github.com/rooftopcms/rooftop-custom-content-setup
 * Description:       Custom content types, taxonomies, and page templates in Rooftop.
 * Version:           1.2.2.1
 * Author:            Rooftop CMS
 * Author URI:        http://rooftopcms.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       rooftop-custom-content-setup
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-rooftop-custom-content-setup-activator.php
 */
function activate_rooftop_custom_content_setup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-custom-content-setup-activator.php';
	Rooftop_Custom_Content_Setup_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-rooftop-custom-content-setup-deactivator.php
 */
function deactivate_rooftop_custom_content_setup() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-custom-content-setup-deactivator.php';
	Rooftop_Custom_Content_Setup_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_rooftop_custom_content_setup' );
register_deactivation_hook( __FILE__, 'deactivate_rooftop_custom_content_setup' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-rooftop-custom-content-setup.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_rooftop_custom_content_setup() {

	$plugin = new Rooftop_Custom_Content_Setup();
	$plugin->run();

}
run_rooftop_custom_content_setup();
